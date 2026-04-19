<?php

namespace App\Http\Controllers\Koordinator;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PendaftaranKp;
use App\Models\LogBimbingan;
use App\Models\Mahasiswa;
use Illuminate\Support\Facades\Auth;

class BimbinganSayaController extends Controller
{
    public function index()
    {
        $dosenId = Auth::id();

        // 1. Ambil mahasiswa bimbingan
        $mahasiswas = Mahasiswa::with(['user'])->where('pembimbing_id', $dosenId)->get();

        $pendaftarans = collect();
        $jumlahSelesai = 0;
        $jumlahBelumDiperiksa = 0;

        foreach ($mahasiswas as $mhs) {
            // Ambil pendaftaran KP tanpa filter 'mahasiswa_id' di level SQL untuk menghindari error
            $kpLog = PendaftaranKp::with('logBimbingans')
                ->where(function ($q) use ($mhs) {
                    $q->where('mahasiswa_id', $mhs->user_id)
                        ->orWhereJsonContains('anggota_kelompok_ids', $mhs->user_id)
                        ->orWhereJsonContains('anggota_kelompok_ids', (string) $mhs->user_id);
                })
                ->where('status_kp', 'approved')
                ->latest()
                ->first();

            if (!$kpLog && $mhs->user) {
                $kpLog = PendaftaranKp::with('logBimbingans')
                    ->where('mahasiswa_id', $mhs->user_id)
                    ->latest()
                    ->first();
            }

            if ($kpLog) {
                $displayKp = clone $kpLog;

                // --- PERBAIKAN LOGIKA PRIVASI DI SINI ---
                $myLogs = $kpLog->logBimbingans->where('mahasiswa_id', $mhs->user_id);

                $displayKp->total_log = $myLogs->count();
                $adaPending = $myLogs->where('status_approval', 'pending')->count() > 0;

                if ($displayKp->total_log == 0) {
                    $displayKp->status_approval_semua = '-';
                } else {
                    $displayKp->status_approval_semua = $adaPending ? 'Menunggu pengecekan' : 'Diperiksa';
                }

                $displayKp->display_mahasiswa = $mhs;
                $displayKp->display_judul_kp = $kpLog->judul_kp;
                $displayKp->display_instansi = $kpLog->instansi_nama;

                $pendaftarans->push($displayKp);

                // Hitung statistik untuk Dashboard
                foreach ($myLogs as $log) {
                    if ($log->status_approval == 'approved') {
                        $jumlahSelesai++;
                    } elseif ($log->status_approval == 'pending') {
                        $jumlahBelumDiperiksa++;
                    }
                }
            }
        }

        return view('koordinator.bimbingan-saya', [
            'active' => 'bimbingan-saya',
            'pendaftarans' => $pendaftarans,
            'jumlahSelesai' => $jumlahSelesai,
            'jumlahBelumDiperiksa' => $jumlahBelumDiperiksa,
        ]);
    }

    public function detail($id)
    {
        $dosenId = Auth::id();

        // Ambil data pendaftaran
        $pendaftaran = PendaftaranKp::findOrFail($id);

        // PERBAIKAN DI SINI: Ambil data mahasiswa dari pendaftaran ini
        $mhs = Mahasiswa::with('user')->where('user_id', $pendaftaran->mahasiswa_id)->firstOrFail();

        // Re-load pendaftaran dengan log yang sudah DIFILTER berdasarkan pemiliknya
        $pendaftaranLoad = PendaftaranKp::with([
            'logBimbingans' => function ($q) use ($mhs) {
                $q->where('mahasiswa_id', $mhs->user_id) // <-- Filter agar hanya log milik mahasiswa ini
                    ->orderBy('tanggal', 'desc');
            }
        ])->where('id', $id)->firstOrFail();

        if ($mhs->pembimbing_id != $dosenId && Auth::user()->role != 'koordinator_kp') {
            abort(403, 'Unauthorized access.');
        }

        $pendaftaranLoad->display_mahasiswa = $mhs;
        $pendaftaranLoad->display_judul_kp = $pendaftaranLoad->judul_kp;

        // Summary Badges (Sudah privat)
        $jumlahDiterima = $pendaftaranLoad->logBimbingans->where('status_approval', 'approved')->count();
        $jumlahBelumDiperiksa = $pendaftaranLoad->logBimbingans->where('status_approval', 'pending')->count();
        $jumlahDitolak = $pendaftaranLoad->logBimbingans->where('status_approval', 'rejected')->count();

        return view('koordinator.bimbingan-saya-detail-log-bimbingan', [
            'active' => 'bimbingan-saya',
            'pendaftaran' => $pendaftaranLoad,
            'jumlahDiterima' => $jumlahDiterima,
            'jumlahBelumDiperiksa' => $jumlahBelumDiperiksa,
            'jumlahDitolak' => $jumlahDitolak,
        ]);
    }

    public function updateStatus(Request $request, $log_id)
    {
        $request->validate([
            'status_approval' => 'required|in:approved,rejected'
        ]);

        $log = LogBimbingan::findOrFail($log_id);
        $log->update([
            'status_approval' => $request->status_approval
        ]);

        return back()->with('success', 'Status bimbingan berhasil diperbarui.');
    }
}
