<?php

namespace App\Http\Controllers\Koordinator;

use App\Http\Controllers\Controller;
use App\Models\PendaftaranKp;
use App\Models\PendaftaranSidang;
use App\Models\TimelineKegiatan;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // 1. Statistics for Cards
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

        // The following counts automatically use the global scope from PendaftaranKp and PendaftaranSidang
        // KP Berjalan: Mahasiswa yang sudah approved KP tapi BELUM selesai sidang
        $kpBerjalan = PendaftaranKp::where('status_kp', 'approved')
            ->whereDoesntHave('pendaftaranSidang', function($q) {
                $q->where('pelaksanaan', 'Selesai');
            })->count();
        // KP Selesai: Mahasiswa yang sudah melakukan sidang (pelaksanaan = Selesai), apapun hasilnya
        $kpSelesai = PendaftaranSidang::where('pelaksanaan', 'Selesai')->count();
        
        // Sidang Terjadwal: Sudah dipublikasikan ke kalender (submitted)
        $sidangTerjadwal = PendaftaranSidang::where('status_jadwal', 'submitted')->count();
        
        // Berkas Sidang (Verifikasi Koordinator)
        $sudahKumpulBerkas = PendaftaranSidang::where('status_koordinator', 'verified')->count();
        $belumKumpulBerkas = PendaftaranSidang::where('status_koordinator', 'pending')->count();

        $stats = [
            'total_mahasiswa' => $totalMahasiswa,
            'kp_berjalan' => $kpBerjalan,
            'kp_selesai' => $kpSelesai,
            'sidang_terjadwal' => $sidangTerjadwal,
            'sudah_berkas' => $sudahKumpulBerkas,
            'belum_berkas' => $belumKumpulBerkas,
        ];

        $periodeId = session('selected_periode_id') ?? \App\Models\TahunAjaran::aktif()->id ?? null;

        // 2. Timeline (Closest for Dosen)
        $timelineDosen = TimelineKegiatan::where('kategori', 'dosen')
            ->where('periode_id', $periodeId)
            ->where('tanggal', '>=', now()->toDateString())
            ->orderBy('tanggal', 'asc')
            ->orderBy('waktu', 'asc')
            ->take(1)
            ->first();

        // 3. Chart Data: Monthly Registrations
        $currentWeekSidangs = PendaftaranSidang::whereNotNull('tanggal_sidang')
            ->whereBetween('tanggal_sidang', [now()->startOfWeek(), now()->endOfWeek()])
            ->select(DB::raw('strftime("%w", tanggal_sidang) as day'), DB::raw('count(*) as count'))
            ->groupBy('day')
            ->pluck('count', 'day')
            ->toArray();

        // Weekly labels: Sun (0) to Sat (6)
        $weeklySidangStats = [];
        for ($i = 0; $i < 7; $i++) {
            $weeklySidangStats[] = $currentWeekSidangs[$i] ?? 0;
        }

        // 4. Progress Sidang: sudah vs belum
        // Progress based on students who have approved KP in this period
        $totalApprovedKp = PendaftaranKp::where('status_kp', 'approved')->count();
        $sudahSidang = PendaftaranSidang::where('pelaksanaan', 'Selesai')->count();
        $belumSidang = max(0, $totalApprovedKp - $sudahSidang);

        $progressSidang = [
            'sudah' => $totalApprovedKp > 0 ? round(($sudahSidang / $totalApprovedKp) * 100) : 0,
            'belum' => $totalApprovedKp > 0 ? round(($belumSidang / $totalApprovedKp) * 100) : 100,
        ];

        // 5. Notifikasi (Dynamic)
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
            'active' => 'dashboard',
            'stats' => $stats,
            'timeline' => $timelineDosen,
            'weeklySidangStats' => $weeklySidangStats,
            'progressSidang' => $progressSidang,
            'sudahSidangCount' => $sudahSidang,
            'belumSidangCount' => $belumSidang,
            'notifikasi' => $notifikasi,
            'notifikasiCount' => $notifikasiCount,
        ]);
    }
}
