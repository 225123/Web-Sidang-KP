<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use App\Models\LogBimbingan;
use App\Models\PendaftaranKp;
use App\Models\PendaftaranSidang;
use App\Models\TimelineKegiatan;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $userId = Auth::id();

        // 1. Status Kerja Praktik
        $latestKp = PendaftaranKp::with(['supervisorInstansi', 'pembimbing'])
            ->where('mahasiswa_id', $userId)
            ->latest()
            ->first();

        $kpStatus = [
            'judul' => ($latestKp && $latestKp->judul_kp) ? $latestKp->judul_kp : '-',
            'instansi' => ($latestKp && $latestKp->instansi_nama) ? $latestKp->instansi_nama : '-',
            'jenis_instansi' => ($latestKp && $latestKp->jenis_instansi) ? $latestKp->jenis_instansi : '-',
            'supervisor' => ($latestKp && $latestKp->supervisorInstansi && $latestKp->supervisorInstansi->nama_supervisor) ? $latestKp->supervisorInstansi->nama_supervisor : '-',
            'pembimbing' => ($latestKp && $latestKp->pembimbing && $latestKp->pembimbing->name) ? $latestKp->pembimbing->name : '-',
            'status_teks' => $latestKp ? ($latestKp->status_kp === 'approved' ? 'On Progress' : ($latestKp->status_kp === 'pending' ? 'Pending Approval' : 'Belum Mendaftar')) : 'Belum Mendaftar',
            'status_raw' => $latestKp->status_kp ?? 'none',
        ];

        $bimbinganDosenCount = 0;
        $bimbinganSupervisorCount = 0;
        if ($latestKp) {
            $bimbinganDosenCount = LogBimbingan::where('pendaftaran_kp_id', $latestKp->id)
                ->where('mahasiswa_id', $userId)
                ->where('is_supervisor', false)
                ->where('status_approval', 'approved')
                ->count();

            $bimbinganSupervisorCount = LogBimbingan::where('pendaftaran_kp_id', $latestKp->id)
                ->where('mahasiswa_id', $userId)
                ->where('is_supervisor', true)
                ->where('status_approval', 'approved')
                ->count();
        }

        // Progress Calculation (Example: 12 lecturer meetings = 100%)
        $targetDosen = 12;
        $targetSupervisor = 6;
        $progress = ($latestKp && $targetDosen > 0) ? min(100, round(($bimbinganDosenCount / $targetDosen) * 100)) : 0;

        // 2. Timeline Terdekat (Mahasiswa)
        $timeline = TimelineKegiatan::where('kategori', 'mahasiswa')
            ->where('tanggal', '>=', now()->toDateString())
            ->orderBy('tanggal', 'asc')
            ->orderBy('waktu', 'asc')
            ->first();

        // 3. Status Sidang
        $sidang = PendaftaranSidang::with(['penguji1', 'penguji2'])
            ->where('mahasiswa_id', $userId)
            ->latest()
            ->first();

        // 4. Notifikasi (Dynamic)
        $notifikasi = \App\Models\NotifikasiLog::where(function ($query) use ($userId) {
                $query->where('receiver_id', $userId)
                    ->orWhere('target_role', 'mahasiswa');
            })
            ->where('is_read', false)
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();
            
        $notifikasiCount = \App\Models\NotifikasiLog::where(function ($query) use ($userId) {
                $query->where('receiver_id', $userId)
                    ->orWhere('target_role', 'mahasiswa');
            })
            ->where('is_read', false)
            ->count();

        return view('mahasiswa.dashboard', [
            'active' => 'dashboard',
            'kp' => $kpStatus,
            'bimbinganDosen' => ['current' => $bimbinganDosenCount, 'target' => $targetDosen],
            'bimbinganSupervisor' => ['current' => $bimbinganSupervisorCount, 'target' => $targetSupervisor],
            'progress' => $progress,
            'timeline' => $timeline,
            'sidang' => $sidang,
            'notifikasi' => $notifikasi,
            'notifikasiCount' => $notifikasiCount,
        ]);
    }
}
