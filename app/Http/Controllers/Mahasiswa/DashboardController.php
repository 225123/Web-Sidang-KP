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

        $periodeId = session('selected_periode_id') ?? \App\Models\TahunAjaran::aktif()?->id ?? null;

        // 1. Status Kerja Praktik
        $queryKp = PendaftaranKp::withoutGlobalScope('periode')
            ->with(['supervisorInstansi', 'pembimbing'])
            ->where(function ($query) use ($userId) {
                $query->where('mahasiswa_id', $userId)
                      ->orWhereJsonContains('anggota_kelompok_ids', $userId)
                      ->orWhereJsonContains('anggota_kelompok_ids', (string) $userId);
            });

        if ($periodeId) {
            $queryKp->where('tahun_ajaran_id', $periodeId);
        }

        // Prioritize approved > verified > pending > draft > rejected
        $latestKp = (clone $queryKp)->orderByRaw("
            CASE 
                WHEN status_kp = 'approved' THEN 1
                WHEN status_kp = 'verified' THEN 2
                WHEN status_kp = 'pending' THEN 3
                WHEN status_kp IS NULL THEN 4
                WHEN status_kp = 'rejected' THEN 5
                ELSE 6
            END
        ")->latest()->first();

        // Fetch individual KP specifically for the user to accurately check their own status
        $individualKpQuery = PendaftaranKp::withoutGlobalScope('periode')
            ->where('mahasiswa_id', $userId);
        if ($periodeId) {
            $individualKpQuery->where('tahun_ajaran_id', $periodeId);
        }
        $individualKp = (clone $individualKpQuery)->orderByRaw("
            CASE 
                WHEN status_kp = 'approved' THEN 1
                WHEN status_kp = 'verified' THEN 2
                WHEN status_kp = 'pending' THEN 3
                WHEN status_kp IS NULL THEN 4
                WHEN status_kp = 'rejected' THEN 5
                ELSE 6
            END
        ")->latest()->first();

        $isRegistered = $individualKp && $individualKp->status_kp !== 'rejected';
        
        $statusTeks = 'Belum Mendaftar';
        if ($individualKp) {
            if ($individualKp->status_kp === 'approved') {
                $statusTeks = 'On Progress';
            } elseif ($individualKp->status_kp === 'verified') {
                $statusTeks = 'Verified';
            } elseif ($individualKp->status_kp === 'pending') {
                $statusTeks = 'Pending Approval';
            } elseif ($individualKp->status_kp === 'rejected') {
                $statusTeks = 'Belum Mendaftar';
            }
        }

        $kpStatus = [
            'judul' => ($isRegistered && $individualKp->judul_kp) ? $individualKp->judul_kp : '-',
            'instansi' => ($isRegistered && $latestKp && $latestKp->instansi_nama) ? $latestKp->instansi_nama : '-',
            'jenis_instansi' => ($isRegistered && $latestKp && $latestKp->jenis_instansi) ? $latestKp->jenis_instansi : '-',
            'supervisor' => ($isRegistered && $latestKp && $latestKp->supervisorInstansi && $latestKp->supervisorInstansi->nama_supervisor) ? $latestKp->supervisorInstansi->nama_supervisor : '-',
            'pembimbing' => ($isRegistered && $latestKp && $latestKp->pembimbing && $latestKp->pembimbing->name) ? $latestKp->pembimbing->name : '-',
            'status_teks' => $statusTeks,
            'status_raw' => $individualKp->status_kp ?? 'none',
            'is_lanjutan' => (Auth::user()->mahasiswa && strtolower(Auth::user()->mahasiswa->status_mahasiswa) === 'lanjut'),
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
            ->where('periode_id', $periodeId)
            ->where('tanggal', '>=', now()->toDateString())
            ->orderBy('tanggal', 'asc')
            ->orderBy('waktu', 'asc')
            ->first();

        // 3. Status Sidang
        $sidangQuery = PendaftaranSidang::with(['penguji1', 'penguji2'])
            ->where('mahasiswa_id', $userId);
        
        if ($latestKp) {
            $sidangQuery->where('pendaftaran_kp_id', $latestKp->id);
        } elseif ($periodeId) {
            // fallback if no KP but period is selected, though unlikely to have sidang without KP
            $sidangQuery->whereHas('pendaftaranKp', function($q) use ($periodeId) {
                $q->where('tahun_ajaran_id', $periodeId);
            });
        }
            
        $sidang = $sidangQuery->latest()->first();

        // Calculate dynamic status for Sidang
        if ($sidang) {
            if ($sidang->pelaksanaan === 'Selesai' || $sidang->pelaksanaan === 'Dibatalkan') {
                $sidang->calculated_status = $sidang->pelaksanaan;
            } elseif (!empty($sidang->tanggal_sidang) && !empty($sidang->waktu_mulai_sidang)) {
                $sidang->calculated_status = 'Terjadwal';
            } else {
                $sidang->calculated_status = $sidang->status_jadwal ?? 'Submitted';
            }
        }

        // 1b. Override Status Kerja Praktik jika finalisasi nilai sudah dilakukan (nilai_dipublikasi = true)
        if ($sidang && $sidang->nilai_dipublikasi) {
            $kpStatus['status_teks'] = 'Selesai';
            $kpStatus['status_raw'] = 'approved'; // Tetap hijau
        }

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
