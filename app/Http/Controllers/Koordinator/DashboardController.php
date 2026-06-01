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
                    $sq->where('tahun_ajaran_id', $periodeId);
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
            $belumKumpulBerkas = PendaftaranSidang::where('status_koordinator', 'pending')->count();

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

            // 5. Notifikasi
            $userId = auth()->id();
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
            ]);

        } catch (\Throwable $e) {
            error_log('[KOORDINATOR_DASHBOARD_ERROR] ' . get_class($e) . ': ' . $e->getMessage() . ' | File: ' . $e->getFile() . ' Line: ' . $e->getLine());
            throw $e;
        }
    }
}
