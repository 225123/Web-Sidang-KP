<?php

namespace App\Http\Controllers\Koordinator;

use App\Http\Controllers\Controller;
use App\Models\Mahasiswa;
use App\Models\PendaftaranKp;
use App\Models\User;
use Illuminate\Http\Request;

class ProgressUmumController extends Controller
{
    public function index()
    {
        // Ambil SEMUA Mahasiswa agar tampil secara individu, difilter per periode aktif
        $mahasiswaQuery = Mahasiswa::with('user');
        if (session()->has('selected_periode_id')) {
            $periodeId = session('selected_periode_id');
            $mahasiswaQuery->where(function($q) use ($periodeId) {
                $q->where('tahun_ajaran_id', $periodeId)
                  ->orWhereIn('user_id', function($sub) use ($periodeId) {
                      $sub->select('mahasiswa_id')->from('pendaftaran_kp')->where('tahun_ajaran_id', $periodeId);
                  });
            });
        }
        $mahasiswas = $mahasiswaQuery->get();

        $pendaftarans = collect();

        foreach ($mahasiswas as $mhs) {
            // Cari pendaftaran KP di mana mahasiswa ini terlibat (sebagai ketua atau anggota)
            $kp = PendaftaranKp::with(['supervisorInstansi', 'logBimbingans', 'pembimbing'])
                ->where(function ($q) use ($mhs) {
                    $q->where('mahasiswa_id', $mhs->user_id)
                        ->orWhereJsonContains('anggota_kelompok_ids', $mhs->user_id)
                        ->orWhereJsonContains('anggota_kelompok_ids', (string) $mhs->user_id);
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

            if ($kp) {
                // Mahasiswa sudah terdaftar/terlibat KP
                $myLogs = $kp->logBimbingans->where('mahasiswa_id', $mhs->user_id);
                $myApprovedLogs = $myLogs->where('status_approval', 'approved');
                $adaPending = $myLogs->where('status_approval', 'pending')->count() > 0;

                $ownKp = PendaftaranKp::where('mahasiswa_id', $mhs->user_id)
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

                $kpToUse = $ownKp ?: $kp;

                $pendaftarans->push([
                    'id' => $kpToUse->id,
                    'display_mahasiswa' => $mhs,
                    'display_judul_kp' => $kpToUse->judul_kp ?? '-',
                    'display_instansi' => $kpToUse->instansi_nama ?? '-',
                    'display_supervisor' => ($kpToUse->supervisorInstansi) ? $kpToUse->supervisorInstansi->nama_supervisor : '-',
                    'display_pembimbing' => $kpToUse->pembimbing->name ?? ($kpToUse->pembimbing_id ? 'Dosen ID: '.$kpToUse->pembimbing_id : '-'),
                    'total_log' => $myApprovedLogs->count(),
                    'status_label' => $myLogs->count() > 0 ? ($adaPending ? 'Menunggu pengecekan' : 'Diperiksa') : '-',
                ]);
            } else {
                // Mahasiswa belum terdaftar KP
                $pendaftarans->push([
                    'id' => null,
                    'display_mahasiswa' => $mhs,
                    'display_judul_kp' => '-',
                    'display_instansi' => '-',
                    'display_supervisor' => '-',
                    'display_pembimbing' => '-',
                    'total_log' => 0,
                    'status_label' => '-',
                ]);
            }
        }

        // Ambil daftar dosen untuk filter
        $dosens = User::whereIn('role', ['dosen', 'koordinator_kp'])->orderBy('name')->get();

        return view('koordinator.progress-umum', [
            'active' => 'progress-umum',
            'pendaftarans' => $pendaftarans,
            'dosens' => $dosens,
        ]);
    }


}
