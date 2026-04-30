<?php

namespace App\Http\Controllers\Koordinator;

use App\Http\Controllers\Controller;
use App\Models\PendaftaranKp;
use App\Models\User;
use Illuminate\Http\Request;

class PendaftaranKpController extends Controller
{
    public function index(Request $request)
    {
        $queryMain = PendaftaranKp::with(['supervisorInstansi', 'user.mahasiswa'])->where('status_kp', '!=', 'rejected')->latest();
        $queryRejected = PendaftaranKp::with(['supervisorInstansi', 'user.mahasiswa'])->where('status_kp', 'rejected')->latest();

        $mainReq = new Request($request->input('main', []));
        $rejectedReq = new Request($request->input('rejected', []));

        $this->applyFilters($queryMain, $mainReq);
        if ($mainReq->has('status_approval') && $mainReq->status_approval != 'All') {
            if ($mainReq->status_approval == 'Disetujui') {
                $queryMain->where('status_kp', 'approved');
            } elseif ($mainReq->status_approval == 'Belum Diperiksa') {
                $queryMain->where('status_kp', 'pending');
            }
        }

        $this->applyFilters($queryRejected, $rejectedReq);

        $pendaftarans = $queryMain->paginate(500, ['*'], 'page')->withQueryString();
        $rejectedPendaftarans = $queryRejected->paginate(500, ['*'], 'req_page')->withQueryString();

        $this->mapMahasiswaList($pendaftarans);
        $this->mapMahasiswaList($rejectedPendaftarans);

        $disetujuiCount = 0;
        $pendingCount = 0;
        $ditolakCount = 0;

        $usersMahasiswa = User::where('role', 'mahasiswa')->get();
        $totalMahasiswa = $usersMahasiswa->count();

        foreach ($usersMahasiswa as $u) {

            $latestKp = PendaftaranKp::where('mahasiswa_id', $u->id)
                ->orWhereJsonContains('anggota_kelompok_ids', $u->id)
                ->orWhereJsonContains('anggota_kelompok_ids', (string) $u->id)
                ->orderBy('created_at', 'desc')
                ->first();

            if ($latestKp) {
                if ($latestKp->status_kp === 'approved') {
                    $disetujuiCount++;
                } elseif ($latestKp->status_kp === 'pending') {
                    $pendingCount++;
                }
                // Jika ingin menghitung ditolak secara strict berdasarkan pendaftaran terakhir:
                elseif ($latestKp->status_kp === 'rejected') {
                    $ditolakCount++;
                }
            }
        }

        $dapatProjek = $disetujuiCount;
        $totalMahasiswa = $usersMahasiswa->count();

        $allStatusRows = $usersMahasiswa->map(function ($u) {
            $ownKp = PendaftaranKp::where('mahasiswa_id', $u->id)
                ->orderBy('created_at', 'desc')
                ->first();

            $isSudah = false;
            if ($ownKp && in_array($ownKp->status_kp, ['pending', 'approved'])) {
                $isSudah = true;
            }

            $invitedKp = PendaftaranKp::where(function ($q) use ($u) {
                $q->whereJsonContains('anggota_kelompok_ids', $u->id)
                    ->orWhereJsonContains('anggota_kelompok_ids', (string) $u->id);
            })
                ->whereIn('status_kp', ['pending', 'approved'])
                ->latest()
                ->first();

            $statusText = 'Belum Mendaftar';
            if ($isSudah) {
                $statusText = 'Sudah Mendaftar';
            } elseif ($invitedKp) {
                $statusText = 'Belum Mendaftar (Proyek Ada)';
            }

            return [
                'nim' => $u->mahasiswa->nim ?? '-',
                'name' => $u->name ?? '-',
                'status' => $statusText,
            ];
        })->sortBy('nim')->values();

        $stats = [
            'disetujui' => $disetujuiCount,
            'belum_diperiksa' => $pendingCount,
            'ditolak' => $ditolakCount,
            'total_mahasiswa' => $totalMahasiswa,
            'dapat_projek' => $dapatProjek,
            'belum_dapat_projek' => max(0, $totalMahasiswa - $dapatProjek),
        ];

        return view('koordinator.Pendaftaran-KP', compact('pendaftarans', 'rejectedPendaftarans', 'stats', 'allStatusRows'));
    }

    private function mapMahasiswaList($paginator)
    {
        $processedGroupHashes = [];
        $filteredItems = [];

        foreach ($paginator as $p) {
            // Check for group duplicates
            $groupMembers = [$p->mahasiswa_id];
            if (in_array(strtolower($p->pengerjaan_kp), ['kelompok', 'berkelompok']) && ! empty($p->anggota_kelompok_ids)) {
                $anggotaIds = is_string($p->anggota_kelompok_ids) ? json_decode($p->anggota_kelompok_ids, true) : $p->anggota_kelompok_ids;
                if (is_array($anggotaIds)) {
                    foreach ($anggotaIds as $aid) {
                        // Sometimes nim is saved in anggota_kelompok_ids instead of ID, handle it if needed
                        $groupMembers[] = (string) $aid;
                    }
                }
            }
            sort($groupMembers);
            $groupHash = implode('_', $groupMembers);

            if (in_array($groupHash, $processedGroupHashes)) {
                $p->is_duplicate = true;

                continue;
            }
            $processedGroupHashes[] = $groupHash;
            $p->is_duplicate = false;

            $mahasiswas = [];
            if ($p->user && $p->user->mahasiswa) {
                $mahasiswas[] = [
                    'nama' => $p->user->name,
                    'nim' => $p->user->mahasiswa->nim,
                    'has_registered' => true,
                    'is_leader' => true,
                    'judul_kp' => $p->judul_kp,
                    'instansi_nama' => $p->instansi_nama,
                ];
            }

            if (in_array(strtolower($p->pengerjaan_kp), ['kelompok', 'berkelompok']) && ! empty($p->anggota_kelompok_ids)) {
                $anggotaIds = is_string($p->anggota_kelompok_ids) ? json_decode($p->anggota_kelompok_ids, true) : $p->anggota_kelompok_ids;
                if (is_array($anggotaIds)) {
                    $members = User::whereIn('id', $anggotaIds)
                        ->orWhereHas('mahasiswa', function ($q) use ($anggotaIds) {
                            $q->whereIn('nim', $anggotaIds);
                        })->with('mahasiswa')->get();

                    foreach ($members as $member) {
                        if ($member->id === $p->mahasiswa_id) {
                            continue;
                        }

                        $memberKp = PendaftaranKp::where('mahasiswa_id', $member->id)
                            ->whereIn('status_kp', ['pending', 'approved'])
                            ->latest()
                            ->first();

                        $mahasiswas[] = [
                            'nama' => $member->name,
                            'nim' => $member->mahasiswa ? $member->mahasiswa->nim : '-',
                            'has_registered' => $memberKp ? true : false,
                            'is_leader' => false,
                            'judul_kp' => $memberKp ? $memberKp->judul_kp : null,
                            'instansi_nama' => $memberKp ? $memberKp->instansi_nama : null,
                        ];
                    }
                }
            }

            // Sort by NIM ascending
            usort($mahasiswas, function ($a, $b) {
                return strcmp($a['nim'], $b['nim']);
            });

            $p->mahasiswaList = $mahasiswas;
        }
    }

    public function show($slug)
    {
        $parts = explode('-', $slug);
        $nim = end($parts);

        $kp = PendaftaranKp::with(['supervisorInstansi', 'user.mahasiswa'])
            ->whereHas('user.mahasiswa', function ($q) use ($nim) {
                $q->where('nim', $nim);
            })->latest()->firstOrFail();

        // Process details sorting
        $mahasiswasDetail = [];
        if (in_array(strtolower($kp->pengerjaan_kp), ['kelompok', 'berkelompok']) && ! empty($kp->anggota_kelompok_ids)) {
            $anggotaIds = is_string($kp->anggota_kelompok_ids) ? json_decode($kp->anggota_kelompok_ids, true) : $kp->anggota_kelompok_ids;
            if (is_array($anggotaIds)) {
                $members = User::whereIn('id', $anggotaIds)
                    ->orWhereHas('mahasiswa', function ($q) use ($anggotaIds) {
                        $q->whereIn('nim', $anggotaIds);
                    })->with('mahasiswa')->get();

                foreach ($members as $member) {
                    if ($member->id === $kp->mahasiswa_id) {
                        continue;
                    }
                    $mahasiswasDetail[] = [
                        'nama' => $member->name,
                        'nim' => $member->mahasiswa ? $member->mahasiswa->nim : '-',
                    ];
                }
            }
        }

        // Sort ascending by NIM
        usort($mahasiswasDetail, function ($a, $b) {
            return strcmp($a['nim'], $b['nim']);
        });

        $kp->anggotaLainList = $mahasiswasDetail;

        return view('koordinator.pendaftaran-kp-detail', compact('kp'));
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:approved,rejected',
            'catatan' => 'nullable|string',
        ]);

        $kp = PendaftaranKp::findOrFail($id);
        $kp->status_kp = $request->status;

        // Preserve group configuration so history tab displays properly.

        // Memastikan catatan akan tertimpa menjadi null (kosong) jika form dikirim tanpa text
        $catatan = $request->catatan ?? '';
        $kp->catatan = empty(trim($catatan)) ? null : trim($catatan);

        $kp->save();

        return redirect()->back()->with('success', 'Status pendaftaran berhasil diperbarui.');
    }

    private function applyFilters($query, Request $request)
    {
        if ($request->has('jenis_kp') && $request->jenis_kp != 'All') {
            $query->where('jenis_instansi', $request->jenis_kp);
        }

        if ($request->has('search') && $request->search != '') {
            $search = strtolower($request->search);

            $matchingUsers = User::whereRaw('LOWER(name) LIKE ?', ['%'.$search.'%'])
                ->orWhereHas('mahasiswa', function ($q) use ($search) {
                    $q->whereRaw('LOWER(nim) LIKE ?', ['%'.$search.'%']);
                })->get();

            $matchingUserIds = $matchingUsers->pluck('id')->toArray();
            $matchingNims = $matchingUsers->map(function ($u) {
                return $u->mahasiswa ? $u->mahasiswa->nim : null;
            })->filter()->toArray();

            $query->where(function ($q) use ($search, $matchingUserIds, $matchingNims) {
                $q->whereHas('user', function ($uq) use ($search) {
                    $uq->whereRaw('LOWER(name) LIKE ?', ['%'.$search.'%'])
                        ->orWhereHas('mahasiswa', function ($mq) use ($search) {
                            $mq->whereRaw('LOWER(nim) LIKE ?', ['%'.$search.'%']);
                        });
                });

                foreach ($matchingUserIds as $id) {
                    $q->orWhereJsonContains('anggota_kelompok_ids', $id);
                    $q->orWhereJsonContains('anggota_kelompok_ids', (string) $id);
                }
                foreach ($matchingNims as $nim) {
                    $q->orWhereJsonContains('anggota_kelompok_ids', $nim);
                }

                $q->orWhereRaw('LOWER(judul_kp) LIKE ?', ['%'.$search.'%']);
            });
        }

        if ($request->has('pengerjaan') && $request->pengerjaan != 'All') {
            $pengerjaan = strtolower($request->pengerjaan);
            if ($pengerjaan === 'sendiri') {
                $pengerjaan = 'individu';
            } elseif ($pengerjaan === 'kelompok') {
                $pengerjaan = 'berkelompok';
            }
            $query->where('pengerjaan_kp', $pengerjaan);
        }

        if ($request->has('status_baru_lanjut') && $request->status_baru_lanjut != 'All') {
            if ($request->status_baru_lanjut === 'Baru') {
                $query->where(function ($q) {
                    $q->where('is_lanjutan', false)->orWhereNull('is_lanjutan');
                });
            } elseif ($request->status_baru_lanjut === 'Lanjut') {
                $query->where('is_lanjutan', true);
            }
        }
    }
}
