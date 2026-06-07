<?php

namespace App\Http\Controllers\Koordinator;

use App\Http\Controllers\Controller;
use App\Models\Dosen;
use App\Models\PendaftaranKp;
use App\Models\User;
use App\Models\NotifikasiLog;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\DB;

class PenugasanPembimbingController extends Controller
{
    private function getDosenWorkloadMap()
    {
        // Get all active pendaftaran_kp clusters that have a supervisor
        $pendaftarans = PendaftaranKp::whereNotNull('pembimbing_id')->get();
        
        $dosenBebanMap = [];
        foreach ($pendaftarans as $kp) {
            $dId = $kp->pembimbing_id;
            
            // Count unique students in this KP (handling groups)
            $studentCount = 1;
            if ($kp->pengerjaan_kp === 'kelompok' && !empty($kp->anggota_kelompok_ids)) {
                $memberIds = is_string($kp->anggota_kelompok_ids) ? json_decode($kp->anggota_kelompok_ids, true) : $kp->anggota_kelompok_ids;
                if (is_array($memberIds)) {
                    $studentCount = count($memberIds);
                    if (!in_array($kp->mahasiswa_id, $memberIds)) {
                        $studentCount++;
                    }
                }
            }
            
            if (! isset($dosenBebanMap[$dId])) {
                $dosenBebanMap[$dId] = 0;
            }
            $dosenBebanMap[$dId] += $studentCount;
        }

        return $dosenBebanMap;
    }

    private function syncAllMahasiswa()
    {
        // Cleanup corrupt data created by previous bug
        PendaftaranKp::withoutGlobalScopes()->whereNull('tahun_ajaran_id')->whereNull('status_kp')->delete();

        $periodeId = session('selected_periode_id');
        $activeId  = $periodeId ?? \App\Models\TahunAjaran::where('is_active', true)->value('id');

        if (! $activeId) return; // Tidak ada periode aktif, skip

        // Ambil mahasiswas yang aktif di periode ini
        $mahasiswas = User::where('role', 'mahasiswa')
            ->whereHas('mahasiswa', function($q) use ($activeId) {
                $q->where('tahun_ajaran_id', $activeId)
                  ->orWhereHas('pendaftaranKps', function($sq) use ($activeId) {
                      $sq->withoutGlobalScope('periode')
                         ->where('tahun_ajaran_id', $activeId)
                         ->where(function($q2) {
                             $q2->whereNotNull('status_kp')
                                ->orWhereRaw('id = (SELECT MIN(id) FROM pendaftaran_kp AS pkp2 WHERE pkp2.mahasiswa_id = pendaftaran_kp.mahasiswa_id)');
                         });
                  });
            })->get();

        foreach ($mahasiswas as $mhs) {
            // Cek apakah mahasiswa sudah punya record untuk PERIODE INI khususnya
            $existsForPeriod = PendaftaranKp::where('tahun_ajaran_id', $activeId)
                ->where(function ($q) use ($mhs) {
                    $q->where('mahasiswa_id', $mhs->id)
                      ->orWhereJsonContains('anggota_kelompok_ids', $mhs->id)
                      ->orWhereJsonContains('anggota_kelompok_ids', (string) $mhs->id);
                })->exists();

            if (! $existsForPeriod) {
                // Buat draft untuk periode aktif agar mahasiswa muncul di tabel penugasan
                PendaftaranKp::firstOrCreate(
                    [
                        'mahasiswa_id'    => $mhs->id,
                        'tahun_ajaran_id' => $activeId,
                    ],
                    [
                        'pengerjaan_kp'  => 'individu',
                        'status_kp'      => null,
                        'judul_kp'       => '-',
                        'jenis_proyek'   => '-',
                        'instansi_nama'  => '-',
                        'jenis_instansi' => 'Internal',
                        'tipe_kp'        => 'internal',
                    ]
                );
            }
        }
    }



    public function index(Request $request)
    {
        try {
            $this->syncAllMahasiswa();


        $dosens = Dosen::with('user')->where('is_aktif', true)->get();

        // 1. Fetch all User Mahasiswa to enforce precisely 1 row per student structure
        $query = User::with(['mahasiswa'])->where('role', 'mahasiswa');
        if (session()->has('selected_periode_id')) {
            $periodeId = session('selected_periode_id');
            $query->whereHas('mahasiswa', function($sq) use ($periodeId) {
                $sq->where('tahun_ajaran_id', $periodeId)
                   ->orWhereHas('pendaftaranKps', function($q2) use ($periodeId) {
                       $q2->withoutGlobalScope('periode')
                          ->where('tahun_ajaran_id', $periodeId)
                          ->where(function($q3) {
                              $q3->whereNotNull('status_kp')
                                 ->orWhereRaw('id = (SELECT MIN(id) FROM pendaftaran_kp AS pkp2 WHERE pkp2.mahasiswa_id = pendaftaran_kp.mahasiswa_id)');
                          });
                   });
            });
        }

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

        $formattedPendaftarans = $this->formatClusters($allMahasiswas);

        // --- Calculate Dosen Beban strictly from these Active Clusters ---
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

        $formattedPendaftarans = $this->applyFilters($formattedPendaftarans, $request);

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

        $totalQuery = User::where('role', 'mahasiswa')->has('mahasiswa');
        if (session()->has('selected_periode_id')) {
            $periodeId = session('selected_periode_id');
            $totalQuery->whereHas('mahasiswa', function($sq) use ($periodeId) {
                $sq->where('tahun_ajaran_id', $periodeId)
                   ->orWhereHas('pendaftaranKps', function($q2) use ($periodeId) {
                       $q2->withoutGlobalScope('periode')
                          ->where('tahun_ajaran_id', $periodeId)
                          ->where(function($q3) {
                              $q3->whereNotNull('status_kp')
                                 ->orWhereRaw('id = (SELECT MIN(id) FROM pendaftaran_kp AS pkp2 WHERE pkp2.mahasiswa_id = pendaftaran_kp.mahasiswa_id)');
                          });
                   });
            });
        }
        $totalAllMahasiswa = $totalQuery->count();

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

        $latestPeriode = \App\Models\TahunAjaran::terbaru()->first();
        $isReadOnly = (session('selected_periode_id') && $latestPeriode && session('selected_periode_id') != $latestPeriode->id);

        return view('koordinator.Penugasan-Pembimbing', [
            'dosenList' => $dosenList,
            'pendaftarans'           => $paginatedItems,
            'paginator'              => $pendaftarans,
            'startNumber'            => $startNumber,
            'endNumber'              => $endNumber,
            'totalMahasiswa'         => $totalAllMahasiswa,
            'filteredMahasiswaCount' => $filteredMahasiswaCount,
            'ditugaskanCount'        => $ditugaskanCount,
            'menungguCount'          => $menungguCount,
            'allGroupSizes'          => $allGroupSizes,
            'isReadOnly'             => $isReadOnly,
        ]);
        } catch (\Throwable $e) {
            error_log('[PenugasanPembimbing ERROR] ' . $e->getMessage() . ' | File: ' . $e->getFile() . ':' . $e->getLine());
            throw $e;
        }
    }


    private function formatClusters($allMahasiswas)
    {
        $clusters = [];
        foreach ($allMahasiswas as $m) {
            if (! $m->mahasiswa) {
                continue;
            }

            $latestKp = PendaftaranKp::with('supervisorInstansi')
                ->where(function ($q) use ($m) {
                    $q->where('mahasiswa_id', $m->id)
                        ->orWhereJsonContains('anggota_kelompok_ids', $m->id)
                        ->orWhereJsonContains('anggota_kelompok_ids', (string) $m->id);
                })
                ->orderByRaw("
                    CASE 
                        WHEN status_kp = 'approved' THEN 1
                        WHEN status_kp = 'verified' THEN 2
                        WHEN status_kp = 'pending' THEN 3
                        WHEN status_kp IS NULL THEN 4
                        WHEN status_kp = 'rejected' THEN 5
                        ELSE 6
                    END
                ")->latest()->first();

            // Cluster by KP ID if it exists, so group members are combined even if pending
            $clusterId = $latestKp ? 'kp_'.$latestKp->id : 'mhs_'.$m->id;

            if (! isset($clusters[$clusterId])) {
                $clusters[$clusterId] = [
                    'id' => $clusterId,
                    'kp' => $latestKp,
                    'mahasiswas' => [],
                ];
            }

            $individualKp = PendaftaranKp::where('mahasiswa_id', $m->id)
                ->orderByRaw("
                    CASE 
                        WHEN status_kp = 'approved' THEN 1
                        WHEN status_kp = 'verified' THEN 2
                        WHEN status_kp = 'pending' THEN 3
                        WHEN status_kp IS NULL THEN 4
                        WHEN status_kp = 'rejected' THEN 5
                        ELSE 6
                    END
                ")->latest()->first();

            $maskedInstansi = $individualKp && $individualKp->status_kp !== 'rejected' ? ($individualKp->instansi_nama ?? '-') : '-';
            $maskedSupervisor = ($individualKp && $individualKp->status_kp !== 'rejected' && $individualKp->supervisorInstansi) ? $individualKp->supervisorInstansi->nama_supervisor : '-';
            $maskedJenisInstansi = $individualKp && $individualKp->status_kp !== 'rejected' ? ucfirst($individualKp->jenis_instansi ?? 'Eksternal') : '-';

            $clusters[$clusterId]['mahasiswas'][] = [
                'user_id' => $m->id,
                'nama' => $m->name,
                'nim' => $m->mahasiswa->nim,
                'judul_kp' => ($individualKp && $individualKp->status_kp !== 'rejected') ? ($individualKp->judul_kp ?? '-') : '-',
                'instansi' => $maskedInstansi,
                'supervisor' => $maskedSupervisor,
                'jenis_kp' => $maskedJenisInstansi,
                'dosen_id' => $latestKp ? $latestKp->pembimbing_id : null,
                'slug' => $individualKp ? \Str::slug($individualKp->judul_kp ?? 'kp').'-'.$m->mahasiswa->nim : 'kp-'.$m->mahasiswa->nim,
            ];
        }

        $formattedPendaftarans = [];
        foreach ($clusters as $cluster) {
            $kp = $cluster['kp'];


            $pengerjaanFormat = ($kp && in_array(strtolower($kp->pengerjaan_kp ?? ''), ['kelompok', 'berkelompok'])) ? 'Kelompok' : '-';

            $formattedPendaftarans[] = [
                'id' => $cluster['id'],
                'slug' => \Str::slug($kp ? $kp->judul_kp : 'kp').'-'.($cluster['mahasiswas'][0]['nim'] ?? '12345'),
                'mahasiswas' => $cluster['mahasiswas'],
                'pengerjaan' => $pengerjaanFormat,
                'dosen_id' => $cluster['mahasiswas'][0]['dosen_id'] ?? null,
                'supervisor_id' => $kp ? $kp->supervisor_internal_id : null,
            ];
        }
        
        return $formattedPendaftarans;
    }

    private function applyFilters($formattedPendaftarans, Request $request)
    {
        if ($request->has('status_filter') && $request->status_filter != 'All') {
            $formattedPendaftarans = array_filter($formattedPendaftarans, function ($group) use ($request) {
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

        return $formattedPendaftarans;
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
                        if ($dosenUserId && $kp->supervisor_internal_id == $dosenUserId) {
                            throw new \Exception("Dosen pembimbing tidak boleh sama dengan supervisor internal untuk mahasiswa di KP: " . ($kp->judul_kp ?? '-'));
                        }
                        $kp->update(['pembimbing_id' => empty($dosenUserId) ? null : $dosenUserId]);
                        
                        // Sinkronisasikan pembimbing_id ke seluruh anggota kelompok
                        $mhsIdsToSync = [];
                        if (!empty($kp->anggota_kelompok_ids)) {
                            $decoded = is_string($kp->anggota_kelompok_ids) ? json_decode($kp->anggota_kelompok_ids, true) : $kp->anggota_kelompok_ids;
                            if (is_array($decoded)) {
                                $mhsIdsToSync = $decoded;
                            }
                        }
                        if (!empty($mhsIdsToSync)) {
                            PendaftaranKp::whereIn('mahasiswa_id', $mhsIdsToSync)
                                ->where(function($q) {
                                    $q->whereIn('status_kp', ['pending', 'approved', 'verified'])
                                      ->orWhereNull('status_kp');
                                })
                                ->update(['pembimbing_id' => empty($dosenUserId) ? null : $dosenUserId]);
                        }

                        if ($dosenUserId) {
                            $this->sendAssignmentNotification($kp, $dosenUserId);
                        }
                    }
                } elseif (str_starts_with($clusterId, 'mhs_')) {
                    $mhsId = str_replace('mhs_', '', $clusterId);
                    
                    $kp = PendaftaranKp::where('mahasiswa_id', $mhsId)
                        ->orWhereJsonContains('anggota_kelompok_ids', $mhsId)
                        ->orWhereJsonContains('anggota_kelompok_ids', (string) $mhsId)
                        ->orderByRaw("
                            CASE 
                                WHEN status_kp = 'approved' THEN 1
                                WHEN status_kp = 'verified' THEN 2
                                WHEN status_kp = 'pending' THEN 3
                                WHEN status_kp IS NULL THEN 4
                                WHEN status_kp = 'rejected' THEN 5
                                ELSE 6
                            END
                        ")->latest()->first();

                    if ($kp) {
                        if ($dosenUserId && $kp->supervisor_internal_id == $dosenUserId) {
                            throw new \Exception("Dosen pembimbing tidak boleh sama dengan supervisor internal untuk mahasiswa: " . ($mhsId));
                        }
                        $kp->update(['pembimbing_id' => empty($dosenUserId) ? null : $dosenUserId]);

                        // Sinkronisasikan pembimbing_id ke seluruh anggota kelompok
                        $mhsIdsToSync = [];
                        if (!empty($kp->anggota_kelompok_ids)) {
                            $decoded = is_string($kp->anggota_kelompok_ids) ? json_decode($kp->anggota_kelompok_ids, true) : $kp->anggota_kelompok_ids;
                            if (is_array($decoded)) {
                                $mhsIdsToSync = $decoded;
                            }
                        }
                        if (!empty($mhsIdsToSync)) {
                            PendaftaranKp::whereIn('mahasiswa_id', $mhsIdsToSync)
                                ->where(function($q) {
                                    $q->whereIn('status_kp', ['pending', 'approved', 'verified'])
                                      ->orWhereNull('status_kp');
                                })
                                ->update(['pembimbing_id' => empty($dosenUserId) ? null : $dosenUserId]);
                        }

                        if ($dosenUserId) {
                            $this->sendAssignmentNotification($kp, $dosenUserId);
                        }
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

            $allMhsQuery = User::with('mahasiswa')->where('role', 'mahasiswa');
            if (session()->has('selected_periode_id')) {
                $periodeId = session('selected_periode_id');
                $allMhsQuery->whereHas('mahasiswa', function($sq) use ($periodeId) {
                    $sq->where('tahun_ajaran_id', $periodeId)
                       ->orWhereHas('pendaftaranKps', function($q2) use ($periodeId) {
                           $q2->withoutGlobalScope('periode')
                              ->where('tahun_ajaran_id', $periodeId)
                              ->where(function($q3) {
                                  $q3->whereNotNull('status_kp')
                                     ->orWhereRaw('id = (SELECT MIN(id) FROM pendaftaran_kp AS pkp2 WHERE pkp2.mahasiswa_id = pendaftaran_kp.mahasiswa_id)');
                              });
                       });
                });
            }
            $allMahasiswas = $allMhsQuery->get();
            $clusters = [];

            foreach ($allMahasiswas as $m) {
                if (! $m->mahasiswa) {
                    continue;
                }

                $latestKp = PendaftaranKp::where(function ($q) use ($m) {
                    $q->where('mahasiswa_id', $m->id)
                        ->orWhereJsonContains('anggota_kelompok_ids', $m->id)
                        ->orWhereJsonContains('anggota_kelompok_ids', (string) $m->id);
                })
                ->orderByRaw("
                    CASE 
                        WHEN status_kp = 'approved' THEN 1
                        WHEN status_kp = 'verified' THEN 2
                        WHEN status_kp = 'pending' THEN 3
                        WHEN status_kp IS NULL THEN 4
                        WHEN status_kp = 'rejected' THEN 5
                        ELSE 6
                    END
                ")->latest()->first();

                // Skip mahasiswa yang sudah punya dosen pembimbing (cek dari latest KP)
                if ($latestKp && ! empty($latestKp->pembimbing_id)) {
                    continue;
                }

                $approvedKp = ($latestKp && $latestKp->status_kp === 'approved') ? $latestKp : null;

                $clusterId = $approvedKp ? 'kp_'.$approvedKp->id : 'mhs_'.$m->id;

                if (! isset($clusters[$clusterId])) {
                    $clusters[$clusterId] = [
                        'id' => $clusterId,
                        'is_group' => $approvedKp ? true : false,
                        'members' => [],
                        'supervisor_id' => $approvedKp ? $approvedKp->supervisor_internal_id : null,
                    ];
                }

                $clusters[$clusterId]['members'][] = $m->id;
            }

            $groups = [];
            $individuals = [];
            foreach ($clusters as $cid => $cData) {
                $size = count($cData['members']);
                if ($cData['is_group'] && $size > 1) {
                    $groups[] = ['id' => $cid, 'size' => $size, 'members' => $cData['members'], 'supervisor_id' => $cData['supervisor_id']];
                } else {
                    $individuals[] = ['id' => $cid, 'size' => $size, 'members' => $cData['members'], 'supervisor_id' => $cData['supervisor_id']];
                }
            }

            usort($groups, function ($a, $b) {
                return $b['size'] <=> $a['size'];
            });

            $toProcess = array_merge($groups, $individuals);

            $assignmentsDraft = [];

            foreach ($toProcess as $item) {
                $size = $item['size'];
                $supervisorId = $item['supervisor_id'] ?? null;

                usort($dosenStats, function ($a, $b) {
                    return $a['beban'] <=> $b['beban'];
                });

                // Find a dosen with the smallest workload who is NOT the supervisor
                $foundDosenIdx = -1;
                foreach ($dosenStats as $idx => $d) {
                    if ($supervisorId && $d['id'] == $supervisorId) {
                        continue;
                    }
                    $foundDosenIdx = $idx;
                    break;
                }

                if ($foundDosenIdx !== -1) {
                    $assignmentsDraft[$item['id']] = $dosenStats[$foundDosenIdx]['id'];
                    $dosenStats[$foundDosenIdx]['beban'] += $size;
                }
            }

            $groupSizesDraft = [];
            foreach ($toProcess as $item) {
                $groupSizesDraft[$item['id']] = $item['size'];
            }

            return response()->json([
                'success' => true,
                'assignments' => $assignmentsDraft,
                'groupSizes' => $groupSizesDraft,
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

        $kp = PendaftaranKp::with('supervisorInstansi')
            ->where(function ($q) use ($mhsUser) {
                $q->where('mahasiswa_id', $mhsUser->id)
                    ->orWhereJsonContains('anggota_kelompok_ids', $mhsUser->id)
                    ->orWhereJsonContains('anggota_kelompok_ids', (string) $mhsUser->id);
            })
            ->orderByRaw("
                CASE 
                    WHEN status_kp = 'approved' THEN 1
                    WHEN status_kp = 'verified' THEN 2
                    WHEN status_kp = 'pending' THEN 3
                    WHEN status_kp IS NULL THEN 4
                    WHEN status_kp = 'rejected' THEN 5
                    ELSE 6
                END
            ")->latest()->first();

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

        $individualKp = PendaftaranKp::where('mahasiswa_id', $mhsUser->id)
            ->orderByRaw("
                CASE 
                    WHEN status_kp = 'approved' THEN 1
                    WHEN status_kp = 'verified' THEN 2
                    WHEN status_kp = 'pending' THEN 3
                    WHEN status_kp IS NULL THEN 4
                    WHEN status_kp = 'rejected' THEN 5
                    ELSE 6
                END
            ")->latest()->first();
            
        $periodeId = session('selected_periode_id');
        $isReadOnly = $periodeId && $periodeId != (\App\Models\TahunAjaran::aktif()?->id);

        return view('koordinator.Penugasan-Pembimbing-Detail', compact('kp', 'individualKp', 'dosenList', 'clusterId', 'isReadOnly'));
    }

    private function sendAssignmentNotification($kp, $dosenUserId)
    {
        $dosen = User::find($dosenUserId);
        if (!$dosen) return;

        // --- Kirim Notifikasi ke Mahasiswa (Ketua & Anggota) ---
        $mhsIds = [$kp->mahasiswa_id];
        if ($kp->anggota_kelompok_ids) {
            $decoded = is_string($kp->anggota_kelompok_ids) ? json_decode($kp->anggota_kelompok_ids, true) : $kp->anggota_kelompok_ids;
            if (is_array($decoded)) $mhsIds = array_unique(array_merge($mhsIds, $decoded));
        }

        foreach ($mhsIds as $mid) {
            $u = User::where('id', $mid)->orWhereHas('mahasiswa', fn($q) => $q->where('nim', $mid))->first();
            if ($u) {
                NotifikasiLog::create([
                    'sender_id' => null,
                    'receiver_id' => $u->id,
                    'judul' => "Dosen Pembimbing Ditugaskan",
                    'pesan' => "Anda telah ditugaskan pembimbing baru: " . ($dosen->name ?? 'Dosen') . ". Silakan hubungi pembimbing Anda untuk memulai bimbingan.",
                    'target_url' => route('mahasiswa.bimbingan-dosen'),
                ]);
            }
        }

        // --- Kirim Notifikasi ke Dosen ---
        NotifikasiLog::create([
            'sender_id' => null,
            'receiver_id' => $dosenUserId,
            'judul' => "Penugasan Bimbingan Baru",
            'pesan' => "Anda telah ditugaskan sebagai pembimbing untuk mahasiswa " . ($kp->user->name ?? 'Grup') . ". Silakan cek daftar bimbingan Anda.",
            'target_url' => route('dosen.daftar-mahasiswa'),
        ]);
    }
}
