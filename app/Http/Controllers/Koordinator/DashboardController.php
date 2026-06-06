<?php

namespace App\Http\Controllers\Koordinator;

use App\Http\Controllers\Controller;
use App\Models\PendaftaranKp;
use App\Models\PendaftaranSidang;
use App\Models\TimelineKegiatan;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class DashboardController extends Controller
{
    public function index()
    {
        try {
            // 1. Statistics for Cards
            // Peserta KP: Users who are mahasiswa AND have registration in the selected period
            $totalMahasiswaQuery = User::where('role', 'mahasiswa');
            if (session()->has('selected_periode_id')) {
                $periodeId = session('selected_periode_id');
                $totalMahasiswaQuery->whereHas('mahasiswa', function($sq) use ($periodeId) {
                    $sq->where('tahun_ajaran_id', $periodeId)
                       ->orWhereHas('pendaftaranKps', function($q) use ($periodeId) {
                           $q->withoutGlobalScope('periode')->where('tahun_ajaran_id', $periodeId);
                       });
                });
            }
            $totalMahasiswa = $totalMahasiswaQuery->count();

            // KP Berjalan: Mahasiswa yang sudah approved KP tapi BELUM selesai sidang
            $kpBerjalan = PendaftaranKp::where('status_kp', 'approved')
                ->whereDoesntHave('pendaftaranSidang', function($q) {
                    $q->where('pelaksanaan', 'Selesai');
                })->count();

            // KP Selesai
            $kpSelesai = PendaftaranSidang::where('pelaksanaan', 'Selesai')->count();

            // Sidang Terjadwal
            $sidangTerjadwal = PendaftaranSidang::where('status_jadwal', 'submitted')->count();

            // Berkas Sidang
            $sudahKumpulBerkas = PendaftaranSidang::where('status_koordinator', 'verified')->count();
            // Belum kumpul berkas: KP yang sudah di-approve tapi belum memiliki berkas sidang yang tervalidasi
            $belumKumpulBerkas = PendaftaranKp::where('status_kp', 'approved')
                ->whereDoesntHave('pendaftaranSidang', function($q) {
                    $q->where('status_koordinator', 'verified');
                })->count();

            $stats = [
                'total_mahasiswa' => $totalMahasiswa,
                'kp_berjalan'     => $kpBerjalan,
                'kp_selesai'      => $kpSelesai,
                'sidang_terjadwal' => $sidangTerjadwal,
                'sudah_berkas'    => $sudahKumpulBerkas,
                'belum_berkas'    => $belumKumpulBerkas,
            ];

            $periodeId = session('selected_periode_id') ?? \App\Models\TahunAjaran::aktif()?->id ?? null;

            // 2. Timeline terdekat
            $timelineDosen = TimelineKegiatan::where('kategori', 'dosen')
                ->where('periode_id', $periodeId)
                ->where('tanggal', '>=', now()->toDateString())
                ->orderBy('tanggal', 'asc')
                ->orderBy('waktu', 'asc')
                ->take(1)
                ->first();

            // 3. Chart Data: Group sidangs by week
            $sidangQuery = PendaftaranSidang::whereNotNull('tanggal_sidang');
            
            if ($periodeId) {
                $sidangQuery->whereHas('pendaftaranKp', function($q) use ($periodeId) {
                    $q->where('tahun_ajaran_id', $periodeId);
                });
            }
            
            $sidangsThisPeriod = $sidangQuery->orderBy('tanggal_sidang', 'asc')->pluck('tanggal_sidang');

            $weeksData = [];
            foreach ($sidangsThisPeriod as $tanggal) {
                $date = \Carbon\Carbon::parse($tanggal);
                $startOfWeek = $date->copy()->startOfWeek(\Carbon\Carbon::SUNDAY);
                $endOfWeek = $date->copy()->endOfWeek(\Carbon\Carbon::SATURDAY);
                
                $weekKey = $startOfWeek->format('Y-m-d');
                
                if (!isset($weeksData[$weekKey])) {
                    $startFormat = $startOfWeek->format('d');
                    if ($startOfWeek->month !== $endOfWeek->month) {
                        $startFormat = $startOfWeek->translatedFormat('d M');
                    }
                    $endFormat = $endOfWeek->translatedFormat('d M Y');
                    $label = $startFormat . ' - ' . $endFormat;
                    
                    $weeksData[$weekKey] = [
                        'label' => $label,
                        'start' => $startOfWeek->format('Y-m-d'),
                        'stats' => array_fill(0, 7, 0)
                    ];
                }
                
                $dayOfWeek = $date->dayOfWeek; // 0 to 6
                $weeksData[$weekKey]['stats'][$dayOfWeek]++;
            }

            $chartWeeks = array_values($weeksData);
            
            // 4. Progress Sidang
            $totalApprovedKp = PendaftaranKp::where('status_kp', 'approved')->count();
            $sudahSidang     = PendaftaranSidang::where('pelaksanaan', 'Selesai')->count();
            $belumSidang     = max(0, $totalApprovedKp - $sudahSidang);

            $progressSidang = [
                'sudah' => $totalApprovedKp > 0 ? round(($sudahSidang / $totalApprovedKp) * 100) : 0,
                'belum' => $totalApprovedKp > 0 ? round(($belumSidang / $totalApprovedKp) * 100) : 100,
            ];

            // Progress Bimbingan (Koordinator as Pembimbing)
            $kpsQuery = PendaftaranKp::with(['logBimbingans', 'mahasiswa.user'])
                ->where('pembimbing_id', auth()->id())
                ->whereIn('status_kp', ['pending', 'approved', 'verified']);

            if ($periodeId) {
                $kpsQuery->where('tahun_ajaran_id', $periodeId);
            }
            $kps = $kpsQuery->get();

            $mahasiswaBimbingan = 0;
            $totalBimbinganSelesai = 0;
            $listBimbinganMahasiswa = collect();
            $processedUserIds = [];

            foreach ($kps as $kp) {
                $userIds = [$kp->mahasiswa_id];
                if (in_array(strtolower($kp->pengerjaan_kp ?? ''), ['kelompok', 'berkelompok']) && !empty($kp->anggota_kelompok_ids)) {
                    $decoded = is_string($kp->anggota_kelompok_ids) ? json_decode($kp->anggota_kelompok_ids, true) : $kp->anggota_kelompok_ids;
                    if (is_array($decoded)) {
                        foreach ($decoded as $id) {
                            if (!empty($id)) $userIds[] = $id;
                        }
                    }
                }
                $userIds = array_unique($userIds);

                foreach ($userIds as $uid) {
                    if (in_array($uid, $processedUserIds)) continue;
                    $processedUserIds[] = $uid;

                    $mhs = \App\Models\Mahasiswa::with('user')->where('user_id', $uid)->first();
                    if ($mhs) {
                        $mahasiswaBimbingan++;

                        $myApprovedLogs = \App\Models\LogBimbingan::where('pendaftaran_kp_id', $kp->id)
                            ->where('mahasiswa_id', $uid)
                            ->where('is_supervisor', false)
                            ->where('status_approval', 'approved')
                            ->count();

                        $totalBimbinganSelesai += min($myApprovedLogs, 12);

                        $listBimbinganMahasiswa->push((object)[
                            'nama' => $mhs->user->name ?? 'Unknown',
                            'nim' => $mhs->nim ?? 'Unknown',
                            'count' => $myApprovedLogs,
                            'target' => 12
                        ]);
                    }
                }
            }
            $totalBimbinganTarget = $mahasiswaBimbingan * 12;
            $progressBimbinganKoordinator = $totalBimbinganTarget > 0 ? round(($totalBimbinganSelesai / $totalBimbinganTarget) * 100) : 0;

            // Progress Bimbingan Umum (Rekap Seluruh Mahasiswa)
            $mahasiswaQueryUmum = \App\Models\Mahasiswa::query();
            if ($periodeId) {
                $mahasiswaQueryUmum->where(function($q) use ($periodeId) {
                    $q->where('tahun_ajaran_id', $periodeId)
                      ->orWhereHas('pendaftaranKps', function($sq) use ($periodeId) {
                          $sq->withoutGlobalScope('periode')->where('tahun_ajaran_id', $periodeId);
                      });
                });
            }
            $mahasiswasUmum = $mahasiswaQueryUmum->get();

            // PRELOAD ALL KP for the period to avoid N+1 queries
            $kpQuery = PendaftaranKp::with(['logBimbingans' => function($q) {
                $q->where('status_approval', 'approved');
            }]);
            if ($periodeId) {
                $kpQuery->where('tahun_ajaran_id', $periodeId);
            }
            $allKps = $kpQuery->get();

            $sumRatiosUmum = 0;
            $countBelumUmum = 0;
            $countDimulaiUmum = 0;
            $countMemenuhiUmum = 0;
            $totalMhsUmum = $mahasiswasUmum->count();

            foreach ($mahasiswasUmum as $mhs) {
                // Find KP for this student in memory
                $mhsKps = $allKps->filter(function($kp) use ($mhs) {
                    if ($kp->mahasiswa_id == $mhs->user_id) return true;
                    
                    $anggota = is_string($kp->anggota_kelompok_ids) ? json_decode($kp->anggota_kelompok_ids, true) : $kp->anggota_kelompok_ids;
                    if (is_array($anggota) && (in_array($mhs->user_id, $anggota) || in_array((string)$mhs->user_id, $anggota))) {
                        return true;
                    }
                    return false;
                });

                $kpToUse = null;
                if ($mhsKps->count() > 0) {
                    // Sorting logic in memory (simulating the SQL CASE statement)
                    $mhsKps = $mhsKps->sortBy(function($kp) {
                        switch($kp->status_kp) {
                            case 'approved': return 1;
                            case 'verified': return 2;
                            case 'pending': return 3;
                            case null: return 4;
                            case 'rejected': return 5;
                            default: return 6;
                        }
                    });

                    // Prefer own KP if exists
                    $ownKps = $mhsKps->where('mahasiswa_id', $mhs->user_id);
                    $kpToUse = $ownKps->first() ?? $mhsKps->first();
                }

                $totalLog = 0;
                if ($kpToUse) {
                    $totalLog = $kpToUse->logBimbingans->where('mahasiswa_id', $mhs->user_id)->count();
                }

                if ($totalLog == 0) {
                    $countBelumUmum++;
                } elseif ($totalLog >= 12) {
                    $countMemenuhiUmum++;
                } else {
                    $countDimulaiUmum++;
                }

                $ratio = min($totalLog / 12, 1.0);
                $sumRatiosUmum += $ratio;
            }

            $overallPercentUmum = $totalMhsUmum > 0 ? ($sumRatiosUmum / $totalMhsUmum) * 100 : 0;
            $displayPercentUmum = number_format($overallPercentUmum, 1);

            // Persetujuan Menunggu
            $menungguPersetujuan = collect();
            $userId = auth()->id();
            
            // 1. Pendaftaran KP Baru (Role: Koordinator)
            $kpMenungguQuery = PendaftaranKp::with(['mahasiswa.user'])
                ->where('status_kp', 'pending')
                ->latest();
            if ($periodeId) $kpMenungguQuery->where('tahun_ajaran_id', $periodeId);
            $kpMenunggu = $kpMenungguQuery->take(2)->get();
            
            foreach ($kpMenunggu as $kp) {
                $menungguPersetujuan->push((object)[
                    'mahasiswa' => $kp->mahasiswa->user->name ?? 'Unknown',
                    'jenis' => 'Pendaftaran KP',
                    'route' => route('koordinator.persetujuan-kp.index'),
                    'color' => 'bg-[#E3F2FD] text-[#1565C0] border-[#2196F3]',
                ]);
            }

            // 2. Log Bimbingan (Role: Pembimbing)
            $logsMenungguQuery = \App\Models\LogBimbingan::with(['pendaftaranKp.mahasiswa.user'])
                ->whereHas('pendaftaranKp', function ($q) use ($userId, $periodeId) {
                    $q->where('pembimbing_id', $userId);
                    if ($periodeId) $q->where('tahun_ajaran_id', $periodeId);
                })
                ->where('is_supervisor', false)
                ->where('status_approval', 'pending')
                ->latest();
            $logsMenunggu = $logsMenungguQuery->take(2)->get();

            foreach ($logsMenunggu as $log) {
                $submitterName = \App\Models\User::find($log->mahasiswa_id)->name ?? 'Unknown';
                $menungguPersetujuan->push((object)[
                    'mahasiswa' => $submitterName,
                    'jenis' => 'Log Bimbingan',
                    'route' => route('dosen.daftar-mahasiswa.detail', $log->mahasiswa_id),
                    'color' => 'bg-[#FFF9C4] text-[#827717] border-[#FBC02D]',
                ]);
            }

            // 3. Pendaftaran Sidang (Role: Pembimbing)
            $sidangMenungguQuery = PendaftaranSidang::with(['mahasiswa.user'])
                ->whereHas('pendaftaranKp', function ($q) use ($userId, $periodeId) {
                    $q->where('pembimbing_id', $userId);
                    if ($periodeId) $q->where('tahun_ajaran_id', $periodeId);
                })
                ->where('status_verifikasi', 'pending')
                ->latest('pendaftaran_kp_id');
            $sidangMenunggu = $sidangMenungguQuery->take(2)->get();

            foreach ($sidangMenunggu as $sidang) {
                $menungguPersetujuan->push((object)[
                    'mahasiswa' => $sidang->mahasiswa->user->name ?? 'Unknown',
                    'jenis' => 'Persetujuan Sidang',
                    'route' => route('dosen.persetujuan-sidang.index'),
                    'color' => 'bg-[#C8E6C9] text-[#1B5E20] border-[#4CAF50]',
                ]);
            }

            // 4. Verifikasi Berkas Sidang (Role: Koordinator)
            $berkasMenungguQuery = PendaftaranSidang::with(['mahasiswa.user'])
                ->where('status_verifikasi', 'verified')
                ->where('status_koordinator', 'pending')
                ->latest('id');
            if ($periodeId) {
                $berkasMenungguQuery->whereHas('pendaftaranKp', function($q) use ($periodeId) {
                    $q->where('tahun_ajaran_id', $periodeId);
                });
            }
            $berkasMenunggu = $berkasMenungguQuery->take(2)->get();

            foreach ($berkasMenunggu as $berkas) {
                $menungguPersetujuan->push((object)[
                    'mahasiswa' => $berkas->mahasiswa->user->name ?? 'Unknown',
                    'jenis' => 'Verifikasi Berkas',
                    'route' => route('koordinator.verifikasi-berkas'),
                    'color' => 'bg-[#E1BEE7] text-[#4A148C] border-[#9C27B0]',
                ]);
            }

            // 5. Notifikasi
            $notifikasi = \App\Models\NotifikasiLog::where(function ($query) use ($userId) {
                    $query->where('receiver_id', $userId)
                          ->orWhere('target_role', 'koordinator');
                })
                ->where('is_read', false)
                ->orderBy('created_at', 'desc')
                ->take(5)
                ->get();

            $notifikasiCount = \App\Models\NotifikasiLog::where(function ($query) use ($userId) {
                    $query->where('receiver_id', $userId)
                          ->orWhere('target_role', 'koordinator');
                })
                ->where('is_read', false)
                ->count();

            // 6. Jadwal Sidang Terdekat (sebagai penguji)
            $jadwalTerdekat = PendaftaranSidang::with(['mahasiswa.user', 'mahasiswa'])
                ->where(function ($q) use ($userId) {
                    $q->where('penguji_1_id', $userId)
                      ->orWhere('penguji_2_id', $userId);
                })
                ->where('status_jadwal', 'submitted')
                ->where('pelaksanaan', '!=', 'Selesai')
                ->whereDate('tanggal_sidang', '>=', now())
                ->orderBy('tanggal_sidang', 'asc')
                ->orderBy('waktu_mulai_sidang', 'asc')
                ->take(2)
                ->get();

            return view('koordinator.dashboard', [
                'active'          => 'dashboard',
                'stats'           => $stats,
                'timeline'        => $timelineDosen,
                'chartWeeks'      => $chartWeeks,
                'progressSidang'  => $progressSidang,
                'sudahSidangCount' => $sudahSidang,
                'belumSidangCount' => $belumSidang,
                'notifikasi'      => $notifikasi,
                'notifikasiCount' => $notifikasiCount,
                'progressBimbinganKoordinator' => $progressBimbinganKoordinator,
                'listBimbinganMahasiswa' => $listBimbinganMahasiswa,
                'menungguPersetujuan' => $menungguPersetujuan,
                'jadwalTerdekat' => $jadwalTerdekat,
                'countBelumUmum' => $countBelumUmum,
                'countDimulaiUmum' => $countDimulaiUmum,
                'countMemenuhiUmum' => $countMemenuhiUmum,
                'displayPercentUmum' => $displayPercentUmum,
                'overallPercentUmum' => $overallPercentUmum,
            ]);

        } catch (\Throwable $e) {
            error_log('[KOORDINATOR_DASHBOARD_ERROR] ' . get_class($e) . ': ' . $e->getMessage() . ' | File: ' . $e->getFile() . ' Line: ' . $e->getLine());
            throw $e;
        }
    }
}
