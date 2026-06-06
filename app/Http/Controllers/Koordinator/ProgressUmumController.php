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
                  ->orWhereHas('pendaftaranKps', function($sq) use ($periodeId) {
                      $sq->withoutGlobalScope('periode')
                         ->where('tahun_ajaran_id', $periodeId)
                         ->where(function($q2) {
                             $q2->whereNotNull('status_kp')
                                ->orWhereRaw('id = (SELECT MIN(id) FROM pendaftaran_kp AS pkp2 WHERE pkp2.mahasiswa_id = pendaftaran_kp.mahasiswa_id)');
                         });
                  });
            });
        }
        $mahasiswas = $mahasiswaQuery->get();

        // PRELOAD ALL KP for the period to avoid N+1 queries
        $kpQuery = PendaftaranKp::withoutGlobalScope('periode')->with(['supervisorInstansi', 'logBimbingans', 'pembimbing']);
        if (session()->has('selected_periode_id')) {
            $kpQuery->where('tahun_ajaran_id', session('selected_periode_id'));
        }
        $allKps = $kpQuery->get();

        $pendaftarans = collect();

        foreach ($mahasiswas as $mhs) {
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
                // Sorting logic in memory
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

            if ($kpToUse) {
                // Mahasiswa sudah terdaftar/terlibat KP
                $myLogs = $kpToUse->logBimbingans->where('mahasiswa_id', $mhs->user_id);
                $myApprovedLogs = $myLogs->where('status_approval', 'approved');
                $adaPending = $myLogs->where('status_approval', 'pending')->count() > 0;

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
