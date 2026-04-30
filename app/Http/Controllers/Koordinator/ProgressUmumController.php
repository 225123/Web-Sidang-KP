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
        // Ambil SEMUA Mahasiswa agar tampil secara individu
        $mahasiswas = Mahasiswa::with('user')->get();

        $pendaftarans = collect();

        foreach ($mahasiswas as $mhs) {
            // Cari pendaftaran KP di mana mahasiswa ini terlibat (sebagai ketua atau anggota)
            $kp = PendaftaranKp::with(['supervisorInstansi', 'logBimbingans', 'pembimbing'])
                ->where(function ($q) use ($mhs) {
                    $q->where('mahasiswa_id', $mhs->user_id)
                        ->orWhereJsonContains('anggota_kelompok_ids', $mhs->user_id)
                        ->orWhereJsonContains('anggota_kelompok_ids', (string) $mhs->user_id);
                })
                ->latest()
                ->first();

            if ($kp) {
                // Mahasiswa sudah terdaftar/terlibat KP
                $myLogs = $kp->logBimbingans->where('mahasiswa_id', $mhs->user_id);
                $myApprovedLogs = $myLogs->where('status_approval', 'approved');
                $adaPending = $myLogs->where('status_approval', 'pending')->count() > 0;

                $ownKp = PendaftaranKp::where('mahasiswa_id', $mhs->user_id)->latest()->first();

                $pendaftarans->push([
                    'id' => $kp->id,
                    'display_mahasiswa' => $mhs,
                    'display_judul_kp' => $ownKp ? ($ownKp->judul_kp ?? '-') : '-',
                    'display_instansi' => $ownKp ? ($ownKp->instansi_nama ?? '-') : '-',
                    'display_supervisor' => ($kp->supervisorInstansi) ? $kp->supervisorInstansi->nama_supervisor : '-',
                    'display_pembimbing' => $kp->pembimbing->name ?? ($kp->pembimbing_id ? 'Dosen ID: '.$kp->pembimbing_id : '-'),
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

    public function detail($id, Request $request)
    {
        // Detail log bimbingan untuk mahasiswa spesifik (dari perspektif pemantauan koordinator)
        $pendaftaran = PendaftaranKp::findOrFail($id);
        $mhsId = $request->query('mhs_id', $pendaftaran->mahasiswa_id);

        $mhs = Mahasiswa::with('user')->where('user_id', $mhsId)->firstOrFail();

        $pendaftaranLoad = PendaftaranKp::with([
            'logBimbingans' => function ($q) use ($mhsId) {
                $q->where('mahasiswa_id', $mhsId)->orderBy('tanggal', 'desc');
            },
            'pembimbing',
        ])->where('id', $id)->firstOrFail();

        $pendaftaranLoad->display_mahasiswa = $mhs;

        $jumlahDiterima = $pendaftaranLoad->logBimbingans->where('status_approval', 'approved')->count();
        $jumlahBelumDiperiksa = $pendaftaranLoad->logBimbingans->where('status_approval', 'pending')->count();
        $jumlahDitolak = $pendaftaranLoad->logBimbingans->where('status_approval', 'rejected')->count();

        return view('koordinator.progress-umum-detail', [
            'active' => 'progress-umum',
            'pendaftaran' => $pendaftaranLoad,
            'jumlahDiterima' => $jumlahDiterima,
            'jumlahBelumDiperiksa' => $jumlahBelumDiperiksa,
            'jumlahDitolak' => $jumlahDitolak,
        ]);
    }
}
