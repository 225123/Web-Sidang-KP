<?php

namespace App\Http\Controllers\Dosen;

use App\Http\Controllers\Controller;
use App\Models\LogBimbingan;
use App\Models\PendaftaranKp;
use App\Models\PendaftaranSidang;
use App\Models\TimelineKegiatan;
use App\Models\NotifikasiLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $dosenId = Auth::id();

        // Fallback for non-logged in users accessing this route
        if (!$dosenId) {
            return view('dosen.dashboard', [
                'active' => 'dashboard',
                'stats' => ['bimbingan' => 0, 'belum_sidang' => 0, 'telah_sidang' => 0, 'sidang_terjadwal' => 0],
                'menungguPersetujuan' => collect(),
                'jadwalTerdekat' => collect(),
                'beritaAcaraTertunda' => 0,
                'progressBimbingan' => 0,
                'listBimbinganMahasiswa' => collect(),
            ]);
        }

        // Ambil pendaftaran KP dimana dosen menjadi pembimbing
        $periodeId = session('selected_periode_id') ?? \App\Models\TahunAjaran::aktif()->id ?? null;

        $kpsQuery = PendaftaranKp::with(['logBimbingans', 'mahasiswa.user', 'pembimbing'])
            ->where('pembimbing_id', $dosenId);

        if ($periodeId) {
            $kpsQuery->withoutGlobalScope('periode')->where('tahun_ajaran_id', $periodeId);
        }

        $kps = $kpsQuery->get();

        $mahasiswaBimbingan = 0;
        $belumSidang = 0;
        $telahSidang = 0;
        $totalBimbinganSelesai = 0;
        $listBimbinganMahasiswa = collect();
        $processedUserIds = [];

        foreach ($kps as $kp) {
            // Dapatkan semua user_id yang terlibat (Ketua + Anggota)
            $userIds = [$kp->mahasiswa_id];
            if (! empty($kp->anggota_kelompok_ids)) {
                $decoded = is_string($kp->anggota_kelompok_ids) ? json_decode($kp->anggota_kelompok_ids, true) : $kp->anggota_kelompok_ids;
                if (is_array($decoded)) {
                    foreach ($decoded as $id) {
                        if (! empty($id)) {
                            $userIds[] = $id;
                        }
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

                    // Cek status sidang untuk mahasiswa ini
                    $sidang = PendaftaranSidang::where('pendaftaran_kp_id', $kp->id)
                        ->where('mahasiswa_id', $uid)
                        ->first();

                    if ($sidang && $sidang->pelaksanaan === 'Selesai') {
                        $telahSidang++;
                    } else {
                        $belumSidang++;
                    }

                    // Hitung jumlah bimbingan mahasiswa ini yang approved
                    $myApprovedLogs = LogBimbingan::where('pendaftaran_kp_id', $kp->id)
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

        // Sidang Terjadwal: Sidang di mana dosen menjadi penguji yang BELUM selesai
        $sidangTerjadwal = PendaftaranSidang::where(function ($q) use ($dosenId) {
                $q->where('penguji_1_id', $dosenId)
                  ->orWhere('penguji_2_id', $dosenId);
            })
            ->where('status_jadwal', 'Dijadwalkan')
            ->where('pelaksanaan', '!=', 'Selesai')
            ->count();

        $stats = [
            'bimbingan' => $mahasiswaBimbingan,
            'belum_sidang' => $belumSidang,
            'telah_sidang' => $telahSidang,
            'sidang_terjadwal' => $sidangTerjadwal,
        ];

        // 2. Persetujuan Menunggu
        // Log Bimbingan yang belum disetujui (status_approval = 'pending')
        $logsMenunggu = LogBimbingan::with(['pendaftaranKp.mahasiswa.user'])
            ->whereHas('pendaftaranKp', function ($q) use ($dosenId) {
                $q->where('pembimbing_id', $dosenId);
            })
            ->where('is_supervisor', false)
            ->where('status_approval', 'pending')
            ->latest()
            ->take(3)
            ->get();

        // Pendaftaran Sidang yang butuh persetujuan pembimbing (status_verifikasi masih pending)
        $sidangMenunggu = PendaftaranSidang::with(['mahasiswa.user'])
            ->whereHas('pendaftaranKp', function ($q) use ($dosenId) {
                $q->where('pembimbing_id', $dosenId);
            })
            ->where('status_verifikasi', 'pending')
            ->latest('pendaftaran_kp_id')
            ->take(2)
            ->get();

        $menungguPersetujuan = collect();
        foreach ($logsMenunggu as $log) {
            $menungguPersetujuan->push((object)[
                'mahasiswa' => $log->pendaftaranKp->mahasiswa->user->name ?? 'Unknown',
                'jenis' => 'Log Bimbingan',
                'id' => $log->id,
                'route' => route('dosen.daftar-mahasiswa.detail', $log->pendaftaran_kp_id),
                'color' => 'bg-[#FFF9C4] text-[#827717] border-[#FBC02D]',
            ]);
        }
        foreach ($sidangMenunggu as $sidang) {
            $menungguPersetujuan->push((object)[
                'mahasiswa' => $sidang->mahasiswa->user->name ?? 'Unknown',
                'jenis' => 'Daftar Sidang',
                'id' => $sidang->id,
                'route' => route('dosen.persetujuan-sidang.index'),
                'color' => 'bg-[#C8E6C9] text-[#1B5E20] border-[#4CAF50]',
            ]);
        }

        // 3. Jadwal Sidang Terdekat: Hanya yang BELUM selesai
        $jadwalTerdekat = PendaftaranSidang::with(['mahasiswa.user', 'mahasiswa'])
            ->where(function ($q) use ($dosenId) {
                $q->where('penguji_1_id', $dosenId)
                  ->orWhere('penguji_2_id', $dosenId);
            })
            ->where('status_jadwal', 'Dijadwalkan')
            ->where('pelaksanaan', '!=', 'Selesai')
            ->whereDate('tanggal_sidang', '>=', now())
            ->orderBy('tanggal_sidang', 'asc')
            ->orderBy('waktu_mulai_sidang', 'asc')
            ->take(2)
            ->get();



        $totalBimbinganTarget = $mahasiswaBimbingan * 12;
        $progressBimbingan = $totalBimbinganTarget > 0 ? round(($totalBimbinganSelesai / $totalBimbinganTarget) * 100) : 0;

        // 6. Timeline
        $timeline = TimelineKegiatan::where('kategori', 'dosen')
            ->where('periode_id', $periodeId)
            ->where('tanggal', '>=', now()->toDateString())
            ->orderBy('tanggal', 'asc')
            ->orderBy('waktu', 'asc')
            ->first();

        // 7. Notifikasi (Unread)
        $notifikasi = NotifikasiLog::where(function ($query) use ($dosenId) {
                $query->where('receiver_id', $dosenId)
                    ->orWhere('target_role', 'dosen');
            })
            ->where('is_read', false)
            ->orderBy('created_at', 'desc')
            ->take(3)
            ->get();
            
        $notifikasiCount = NotifikasiLog::where(function ($query) use ($dosenId) {
                $query->where('receiver_id', $dosenId)
                    ->orWhere('target_role', 'dosen');
            })
            ->where('is_read', false)
            ->count();

        return view('dosen.dashboard', [
            'active' => 'dashboard',
            'stats' => $stats,
            'menungguPersetujuan' => $menungguPersetujuan,
            'jadwalTerdekat' => $jadwalTerdekat,
            'progressBimbingan' => $progressBimbingan,
            'listBimbinganMahasiswa' => $listBimbinganMahasiswa,
            'timeline' => $timeline,
            'notifikasi' => $notifikasi,
            'notifikasiCount' => $notifikasiCount,
        ]);
    }
}
