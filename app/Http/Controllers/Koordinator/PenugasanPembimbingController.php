<?php

namespace App\Http\Controllers\Koordinator;

use App\Http\Controllers\Controller;
use App\Models\Dosen;
use App\Models\PendaftaranKp;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\DB;

class PenugasanPembimbingController extends Controller
{
    private function getDosenWorkloadMap()
    {
        $mahasiswas = \App\Models\Mahasiswa::whereNotNull('pembimbing_id')->get();
        
        $dosenBebanMap = [];
        foreach ($mahasiswas as $m) {
            $dId = $m->pembimbing_id;
            if (! isset($dosenBebanMap[$dId])) {
                $dosenBebanMap[$dId] = 0;
            }
            $dosenBebanMap[$dId]++;
        }

        return $dosenBebanMap;
    }

    private function syncAllMahasiswa()
    {
        $mahasiswas = User::where('role', 'mahasiswa')->get();
        foreach ($mahasiswas as $mhs) {
            $exists = PendaftaranKp::where('mahasiswa_id', $mhs->id)
                ->orWhere(function ($q) use ($mhs) {
                    $q->whereJsonContains('anggota_kelompok_ids', $mhs->id)
                        ->orWhereJsonContains('anggota_kelompok_ids', (string) $mhs->id);
                })->exists();

            if (! $exists) {
                // Buat data draft untuk mahasiswa yang belum mendaftar sama sekali agar masuk tabel
                PendaftaranKp::firstOrCreate(
                    ['mahasiswa_id' => $mhs->id],
                    [
                        'pengerjaan_kp' => 'individu',
                        'status_kp' => null,
                        'judul_kp' => '-',
                        'jenis_proyek' => '-',
                        'instansi_nama' => '-',
                        'jenis_instansi' => 'Internal',
                        'tipe_kp' => 'internal',
                    ]
                );
            }
        }
    }

    public function index(Request $request)
    {
        $this->syncAllMahasiswa();

        $dosens = Dosen::with('user')->where('is_aktif', true)->get();
        // Beban akan dihitung setelah klaster mahasiswa terbentuk agar terhindar dari duplikasi PendaftaranKP (rejected vs approved)

        // 1. Fetch all User Mahasiswa to enforce precisely 1 row per student structure
        $query = User::with(['mahasiswa'])->where('role', 'mahasiswa');

        // Apply advanced filters targeting user or their assigned projects
        if ($request->has('search') && $request->search != '') {
            $search = strtolower($request->search);
            $query->where(function ($q) use ($search) {
                $q->whereRaw('LOWER(name) LIKE ?', ['%'.$search.'%'])
                    ->orWhereHas('mahasiswa', function ($mq) use ($search) {
                        $mq->whereRaw('LOWER(nim) LIKE ?', ['%'.$search.'%']);
                    });
            });
        }

        $allMahasiswas = $query->get();

        // 3. Cluster them: If they share an APPROVED Group PendaftaranKp, group them. Otherwise STANDALONE.
        $clusters = [];
        foreach ($allMahasiswas as $m) {
            if (! $m->mahasiswa) {
                continue;
            }

            // Find the absolute latest KP for this mahasiswa/group
            $latestKp = PendaftaranKp::with('supervisorInstansi')
                ->where(function ($q) use ($m) {
                    $q->where('mahasiswa_id', $m->id)
                        ->orWhereJsonContains('anggota_kelompok_ids', $m->id)
                        ->orWhereJsonContains('anggota_kelompok_ids', (string) $m->id);
                })->latest()->first();

            // Only recognize their membership for UI clustering if it is officially approved.
            // If they are pending or rejected, they must be stripped back to isolated states.
            $approvedKp = ($latestKp && $latestKp->status_kp === 'approved') ? $latestKp : null;

            $clusterId = $approvedKp ? 'kp_'.$approvedKp->id : 'mhs_'.$m->id;

            if (! isset($clusters[$clusterId])) {
                $clusters[$clusterId] = [
                    'id' => $clusterId, // Unique cluster identifier
                    'kp' => $approvedKp,
                    'mahasiswas' => [],
                ];
            }

            $clusters[$clusterId]['mahasiswas'][] = [
                'user_id' => $m->id,
                'nama' => $m->name,
                'nim' => $m->mahasiswa->nim,
                'judul_kp' => $approvedKp ? ($approvedKp->judul_kp ?? '-') : '-',
                'dosen_id' => $m->mahasiswa->pembimbing_id,
            ];
        }

        // Apply remaining structural filters on the formed clusters (e.g. status)
        $formattedPendaftarans = [];
        foreach ($clusters as $cluster) {
            $kp = $cluster['kp'];

            // Reconcile shared metrics for the UI row
            $isApproved = ! is_null($kp);
            $maskedInstansi = $isApproved ? ($kp->instansi_nama ?? '-') : '-';
            $maskedSupervisor = $isApproved && $kp->supervisorInstansi ? $kp->supervisorInstansi->nama_supervisor : '-';
            $maskedJenisInstansi = $isApproved ? ucfirst($kp->jenis_instansi ?? 'Eksternal') : '-';
            $pengerjaanFormat = $isApproved && in_array(strtolower($kp->pengerjaan_kp ?? ''), ['kelompok', 'berkelompok']) ? 'Kelompok' : '-';

            $formattedPendaftarans[] = [
                'id' => $cluster['id'],
                'slug' => \Str::slug($kp->judul_kp ?? 'kp').'-'.($cluster['mahasiswas'][0]['nim'] ?? '12345'),
                'mahasiswas' => $cluster['mahasiswas'],
                'jenis_kp' => $maskedJenisInstansi,
                'instansi' => $maskedInstansi,
                'supervisor' => $maskedSupervisor,
                'pengerjaan' => $pengerjaanFormat,
                'dosen_id' => $cluster['mahasiswas'][0]['dosen_id'] ?? null,
            ];
        }

        // --- NEW: Calculate Dosen Beban strictly from these Active Clusters ---
        $dosenBebanMap = [];
        foreach ($formattedPendaftarans as $group) {
            $dId = $group['dosen_id'];
            if (! empty($dId)) {
                if (! isset($dosenBebanMap[$dId])) {
                    $dosenBebanMap[$dId] = 0;
                }
                $dosenBebanMap[$dId] += count($group['mahasiswas']);
            }
        }

        $dosenList = [];
        foreach ($dosens as $d) {
            if (! $d->user) {
                continue;
            }
            $dosenList[] = [
                'id' => $d->user_id,
                'nama' => $d->user->name,
                'beban' => $dosenBebanMap[$d->user_id] ?? 0,
                'kuota' => $d->kuota_bimbingan,
            ];
        }

        usort($dosenList, function ($a, $b) {
            return $a['beban'] <=> $b['beban'];
        });
        // ----------------------------------------------------------------------

        // Apply Status & Pengerjaan filters onto clusters!
        if ($request->has('status_filter') && $request->status_filter != 'All') {
            $formattedPendaftarans = array_filter($formattedPendaftarans, function ($group) use ($request) {
                // If it's grouped, we check if all are assigned.
                $allAssigned = collect($group['mahasiswas'])->every(fn ($m) => ! is_null($m['dosen_id']));
                $noneAssigned = collect($group['mahasiswas'])->every(fn ($m) => is_null($m['dosen_id']));

                if ($request->status_filter == 'Menunggu') {
                    return $noneAssigned;
                }
                if ($request->status_filter == 'Ditugaskan') {
                    return $allAssigned;
                }

                return true;
            });
        }

        if ($request->has('pengerjaan') && $request->pengerjaan != 'All') {
            $formattedPendaftarans = array_filter($formattedPendaftarans, function ($group) use ($request) {
                if ($request->pengerjaan == 'Individu') {
                    return $group['pengerjaan'] === 'Individu' || $group['pengerjaan'] === '-';
                }
                if ($request->pengerjaan == 'Berkelompok') {
                    return $group['pengerjaan'] === 'Kelompok';
                }

                return true;
            });
        }

        if ($request->has('dosen_pembimbing') && $request->dosen_pembimbing != 'All') {
            $formattedPendaftarans = array_filter($formattedPendaftarans, function ($group) use ($request) {
                $dosenId = $group['dosen_id'];
                if ($request->dosen_pembimbing === 'Belum Ditugaskan') {
                    return empty($dosenId);
                }

                return $dosenId == $request->dosen_pembimbing;
            });
        }

        // Pagination Collection conversion so 'paginator' exists for View
        $perPage = 20;
        $page = Paginator::resolveCurrentPage() ?: 1;
        $collection = collect($formattedPendaftarans);
        $paginatedItems = $collection->slice(($page - 1) * $perPage, $perPage)->values()->all();
        $pendaftarans = new LengthAwarePaginator(
            $paginatedItems, $collection->count(), $perPage, $page,
            ['path' => Paginator::resolveCurrentPath(), 'query' => $request->query()]
        );

        $allGroupSizes = [];
        foreach ($formattedPendaftarans as $p) {
            $allGroupSizes[$p['id']] = count($p['mahasiswas']);
        }

        $totalAllMahasiswa = User::where('role', 'mahasiswa')->has('mahasiswa')->count();

        $ditugaskanCount = 0;
        foreach ($formattedPendaftarans as $group) {
            if (! empty($group['dosen_id'])) {
                $ditugaskanCount += count($group['mahasiswas']);
            }
        }
        $menungguCount = max(0, $totalAllMahasiswa - $ditugaskanCount);

        $filteredMahasiswaCount = collect($formattedPendaftarans)->sum(function ($group) {
            return count($group['mahasiswas']);
        });

        $startNumber = 1;
        $previousGroups = $collection->slice(0, ($page - 1) * $perPage);
        foreach ($previousGroups as $g) {
            $startNumber += count($g['mahasiswas']);
        }

        $countOnPage = 0;
        foreach ($paginatedItems as $g) {
            $countOnPage += count($g['mahasiswas']);
        }
        $endNumber = $countOnPage > 0 ? ($startNumber + $countOnPage - 1) : 0;

        $filteredMahasiswaCount = collect($formattedPendaftarans)->sum(function ($group) {
            return count($group['mahasiswas']);
        });

        return view('koordinator.Penugasan-Pembimbing', [
            'dosenList' => $dosenList,
            'pendaftarans' => $paginatedItems,
            'paginator' => $pendaftarans,
            'startNumber' => $startNumber,
            'endNumber' => $endNumber,
            'totalMahasiswa' => $totalAllMahasiswa,
            'filteredMahasiswaCount' => $filteredMahasiswaCount,
            'ditugaskanCount' => $ditugaskanCount,
            'menungguCount' => $menungguCount,
            'allGroupSizes' => $allGroupSizes,
        ]);
    }

    public function storePlotting(Request $request)
    {
        $this->syncAllMahasiswa();

        $assignments = $request->input('assignments', []);

        if (empty($assignments)) {
            return redirect()->back()->with('error', 'Tidak ada plotting baru yang ditarik.');
        }

        DB::beginTransaction();
        try {
            foreach ($assignments as $clusterId => $dosenUserId) {
                if (str_starts_with($clusterId, 'kp_')) {
                    $kpId = str_replace('kp_', '', $clusterId);
                    $kp = PendaftaranKp::find($kpId);
                    if ($kp) {
                        $kp->update(['pembimbing_id' => empty($dosenUserId) ? null : $dosenUserId]);
                        $memberIds = is_string($kp->anggota_kelompok_ids) ? json_decode($kp->anggota_kelompok_ids, true) : $kp->anggota_kelompok_ids;
                        if (!is_array($memberIds)) $memberIds = [];
                        if (!in_array($kp->mahasiswa_id, $memberIds)) $memberIds[] = $kp->mahasiswa_id;
                        
                        \App\Models\Mahasiswa::whereIn('user_id', $memberIds)
                            ->update(['pembimbing_id' => empty($dosenUserId) ? null : $dosenUserId]);
                    }
                } elseif (str_starts_with($clusterId, 'mhs_')) {
                    $mhsId = str_replace('mhs_', '', $clusterId);
                    \App\Models\Mahasiswa::where('user_id', $mhsId)
                        ->update(['pembimbing_id' => empty($dosenUserId) ? null : $dosenUserId]);
                        
                    $kp = PendaftaranKp::where('mahasiswa_id', $mhsId)
                        ->orWhereJsonContains('anggota_kelompok_ids', $mhsId)
                        ->orWhereJsonContains('anggota_kelompok_ids', (string) $mhsId)
                        ->latest()->first();
                    if ($kp) {
                        $kp->update(['pembimbing_id' => empty($dosenUserId) ? null : $dosenUserId]);
                    }
                }
            }
            DB::commit();

            return redirect()->back()->with('success', 'Plotting dosen pembimbing berhasil disimpan!');
        } catch (\Exception $e) {
            DB::rollback();

            return redirect()->back()->with('error', 'Terjadi kesalahan sistem: '.$e->getMessage());
        }
    }

    public function autoAssign()
    {
        $this->syncAllMahasiswa();

        try {
            $dosens = Dosen::with('user')->where('is_aktif', true)->get();
            $dosenStats = [];

            $dosenBebanMap = $this->getDosenWorkloadMap();

            foreach ($dosens as $d) {
                if (! $d->user) {
                    continue;
                }
                $dosenStats[] = [
                    'id' => $d->user_id,
                    'kuota' => $d->kuota_bimbingan,
                    'beban' => $dosenBebanMap[$d->user_id] ?? 0,
                ];
            }

            $allMahasiswas = User::with('mahasiswa')->where('role', 'mahasiswa')->get();
            $clusters = [];

            foreach ($allMahasiswas as $m) {
                if (! $m->mahasiswa) {
                    continue;
                }

                $latestKp = PendaftaranKp::where(function ($q) use ($m) {
                    $q->where('mahasiswa_id', $m->id)
                        ->orWhereJsonContains('anggota_kelompok_ids', $m->id)
                        ->orWhereJsonContains('anggota_kelompok_ids', (string) $m->id);
                })->latest()->first();

                // Skip mahasiswa yang sudah punya dosen pembimbing
                if (! empty($m->mahasiswa->pembimbing_id)) {
                    continue;
                }

                $approvedKp = ($latestKp && $latestKp->status_kp === 'approved') ? $latestKp : null;

                $clusterId = $approvedKp ? 'kp_'.$approvedKp->id : 'mhs_'.$m->id;

                if (! isset($clusters[$clusterId])) {
                    $clusters[$clusterId] = [
                        'id' => $clusterId,
                        'is_group' => $approvedKp ? true : false,
                        'members' => [],
                    ];
                }

                $clusters[$clusterId]['members'][] = $m->id;
            }

            $groups = [];
            $individuals = [];
            foreach ($clusters as $cid => $cData) {
                $size = count($cData['members']);
                if ($cData['is_group'] && $size > 1) {
                    $groups[] = ['id' => $cid, 'size' => $size, 'members' => $cData['members']];
                } else {
                    $individuals[] = ['id' => $cid, 'size' => $size, 'members' => $cData['members']];
                }
            }

            usort($groups, function ($a, $b) {
                return $b['size'] <=> $a['size'];
            });

            $toProcess = array_merge($groups, $individuals);

            $assignmentsDraft = [];

            foreach ($toProcess as $item) {
                $size = $item['size'];

                usort($dosenStats, function ($a, $b) {
                    return $a['beban'] <=> $b['beban'];
                });

                if (count($dosenStats) > 0) {
                    $dData = &$dosenStats[0]; // Pilih dosen dengan beban terkecil

                    if (str_starts_with($item['id'], 'kp_')) {
                        $assignmentsDraft[$item['id']] = $dData['id'];
                    } elseif (str_starts_with($item['id'], 'mhs_')) {
                        $assignmentsDraft[$item['id']] = $dData['id'];
                    }
                    $dData['beban'] += $size;
                }
            }

            return response()->json([
                'success' => true,
                'assignments' => $assignmentsDraft,
                'message' => 'Berhasil membuat draft auto-plotting.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal Auto-Plotting: '.$e->getMessage(),
            ], 500);
        }
    }

    public function resetPlotting()
    {
        try {
            DB::beginTransaction();
            \App\Models\Mahasiswa::whereNotNull('pembimbing_id')->update(['pembimbing_id' => null]);
            PendaftaranKp::whereNotNull('pembimbing_id')->update(['pembimbing_id' => null]);
            DB::commit();

            return redirect()->back()->with('success', 'Berhasil mengosongkan semua penugasan dosen pembimbing. Beban dosen telah direset.');
        } catch (\Exception $e) {
            DB::rollback();

            return redirect()->back()->with('error', 'Terjadi kesalahan saat mereset penugasan: '.$e->getMessage());
        }
    }

    public function show($slug)
    {
        $parts = explode('-', $slug);
        $nim = end($parts);

        $mhsUser = User::with(['mahasiswa.pembimbing'])->whereHas('mahasiswa', function ($q) use ($nim) {
            $q->where('nim', $nim);
        })->firstOrFail();

        // Cari active KP fallback into empty
        $kp = PendaftaranKp::with('supervisorInstansi')
            ->where('status_kp', 'approved')
            ->where(function ($q) use ($mhsUser) {
                $q->where('mahasiswa_id', $mhsUser->id)
                    ->orWhereJsonContains('anggota_kelompok_ids', $mhsUser->id)
                    ->orWhereJsonContains('anggota_kelompok_ids', (string) $mhsUser->id);
            })->latest()->first();

        // Mock KP object for individual display if none
        if (! $kp) {
            $kp = new PendaftaranKp;
            $kp->mahasiswa_id = $mhsUser->id;
            $kp->status_kp = null;
            $kp->pengerjaan_kp = 'individu';
        }

        $kp->user = $mhsUser;
        $mahasiswasDetail = [];
        if ($kp->status_kp === 'approved' && $kp->pengerjaan_kp === 'kelompok' && ! empty($kp->anggota_kelompok_ids)) {
            $anggotaIds = is_string($kp->anggota_kelompok_ids) ? json_decode($kp->anggota_kelompok_ids, true) : $kp->anggota_kelompok_ids;
            if (is_array($anggotaIds)) {
                $members = User::whereIn('id', $anggotaIds)
                    ->orWhereHas('mahasiswa', function ($q) use ($anggotaIds) {
                        $q->whereIn('nim', $anggotaIds);
                    })->with('mahasiswa')->get();

                foreach ($members as $member) {
                    if ($member->id === $mhsUser->id) {
                        continue;
                    }
                    $mahasiswasDetail[] = [
                        'nama' => $member->name,
                        'nim' => $member->mahasiswa ? $member->mahasiswa->nim : '-',
                    ];
                }
            }
        }

        usort($mahasiswasDetail, function ($a, $b) {
            return strcmp($a['nim'], $b['nim']);
        });

        $kp->anggotaLainList = $mahasiswasDetail;

        $dosens = Dosen::with('user')->where('is_aktif', true)->get();
        $dosenList = [];

        $dosenBebanMap = $this->getDosenWorkloadMap();

        foreach ($dosens as $d) {
            if (! $d->user) {
                continue;
            }

            $dosenList[] = [
                'id' => $d->user_id,
                'nama' => $d->user->name,
                'beban' => $dosenBebanMap[$d->user_id] ?? 0,
                'kuota' => $d->kuota_bimbingan,
            ];
        }

        usort($dosenList, function ($a, $b) {
            return strcasecmp($a['nama'], $b['nama']);
        });

        // The ID of the item in the array that determines assignment is 'mhs_X' or 'kp_X'
        $clusterId = ($kp->status_kp === 'approved' && $kp->id) ? 'kp_'.$kp->id : 'mhs_'.$mhsUser->id;

        return view('koordinator.Penugasan-Pembimbing-Detail', compact('kp', 'dosenList', 'clusterId'));
    }
}
