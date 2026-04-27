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
        $totalMahasiswa = User::where('role', 'mahasiswa')->count();
        $kpBerjalan = PendaftaranKp::where('status_kp', 'approved')->count();
        $kpSelesai = PendaftaranSidang::where('status_kelulusan', 'Lulus')->count();
        $sidangTerjadwal = PendaftaranSidang::where('status_jadwal', 'Dijadwalkan')->count();
        $sudahKumpulBerkas = PendaftaranSidang::where('status_verifikasi', 'Approved')->count();
        $belumKumpulBerkas = PendaftaranSidang::where('status_verifikasi', 'Pending')->count();

        $stats = [
            'total_mahasiswa' => $totalMahasiswa,
            'kp_berjalan' => $kpBerjalan,
            'kp_selesai' => $kpSelesai,
            'sidang_terjadwal' => $sidangTerjadwal,
            'sudah_berkas' => $sudahKumpulBerkas,
            'belum_berkas' => $belumKumpulBerkas,
        ];

        // 2. Timeline (Closest for Dosen)
        $timelineDosen = TimelineKegiatan::where('kategori', 'dosen')
            ->where('tanggal', '>=', now()->toDateString())
            ->orderBy('tanggal', 'asc')
            ->orderBy('waktu', 'asc')
            ->take(1)
            ->first();

        // 3. Chart Data: Monthly Registrations (for "Statistik Jadwal Sidang" chart as a proxy or actual scheduling)
        // Let's use PendaftaranSidang to show scheduling trends per week/day as in the UI
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
        $totalApprovedKp = PendaftaranKp::where('status_kp', 'approved')->count();
        $sudahSidang = PendaftaranSidang::whereNotNull('nilai_akhir')->count();
        $belumSidang = max(0, $totalApprovedKp - $sudahSidang);

        $progressSidang = [
            'sudah' => $totalApprovedKp > 0 ? round(($sudahSidang / $totalApprovedKp) * 100) : 0,
            'belum' => $totalApprovedKp > 0 ? round(($belumSidang / $totalApprovedKp) * 100) : 100,
        ];

        return view('koordinator.dashboard', [
            'active' => 'dashboard',
            'stats' => $stats,
            'timeline' => $timelineDosen,
            'weeklySidangStats' => $weeklySidangStats,
            'progressSidang' => $progressSidang,
            'sudahSidangCount' => $sudahSidang,
            'belumSidangCount' => $belumSidang,
        ]);
    }
}
