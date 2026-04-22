<?php

namespace App\Http\Controllers\Dosen;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PendaftaranKp;
use App\Models\LogBimbingan;
use App\Models\Mahasiswa;
use Illuminate\Support\Facades\Auth;

class DaftarBimbinganController extends Controller
{
    public function index()
    {
        $dosenId = Auth::id();

        $kps = PendaftaranKp::where('pembimbing_id', $dosenId)->get();
        $mahasiswaIds = [];
        foreach ($kps as $kp) {
            $mahasiswaIds[] = $kp->mahasiswa_id;
            if (!empty($kp->anggota_kelompok_ids)) {
                $anggota = is_string($kp->anggota_kelompok_ids) ? json_decode($kp->anggota_kelompok_ids, true) : $kp->anggota_kelompok_ids;
                if (is_array($anggota)) {
                    foreach ($anggota as $aid) {
                        $mahasiswaIds[] = $aid;
                    }
                }
            }
        }
        $mahasiswaIds = array_unique($mahasiswaIds);
        
        // 1. Ambil mahasiswa bimbingan
        $mahasiswas = Mahasiswa::with(['user'])->whereIn('user_id', $mahasiswaIds)->get();

        $pendaftarans = collect();
        $jumlahSelesai = 0;
        $jumlahBelumDiperiksa = 0;

        foreach ($mahasiswas as $mhs) {
            // Ambil pendaftaran KP tanpa filter 'mahasiswa_id' di level SQL untuk menghindari error
            $kpLog = PendaftaranKp::with(['logBimbingans', 'supervisorInstansi'])
                ->where(function ($q) use ($mhs) {
                    $q->where('mahasiswa_id', $mhs->user_id)
                        ->orWhereJsonContains('anggota_kelompok_ids', $mhs->user_id)
                        ->orWhereJsonContains('anggota_kelompok_ids', (string) $mhs->user_id);
                })
                ->where('status_kp', 'approved')
                ->latest()
                ->first();

            if (!$kpLog && $mhs->user) {
                $kpLog = PendaftaranKp::with(['logBimbingans', 'supervisorInstansi'])
                    ->where('mahasiswa_id', $mhs->user_id)
                    ->latest()
                    ->first();
            }

            $displayKp = new \stdClass();
            $displayKp->id = $kpLog->id ?? null;
            $displayKp->display_mahasiswa = $mhs;
            $displayKp->display_judul_kp = $kpLog->judul_kp ?? '-';
            $displayKp->display_instansi = $kpLog->instansi_nama ?? '-';
            $displayKp->display_supervisor = $kpLog->supervisorInstansi->nama_supervisor ?? '-';
            $displayKp->total_log = 0;
            $displayKp->status_approval_semua = '-';

            if ($kpLog) {
                // filter log bimbingan di level PHP agar hanya milik mahasiswa ini saja
                $myLogs = $kpLog->logBimbingans->where('mahasiswa_id', $mhs->user_id);
                $myApprovedLogs = $myLogs->where('status_approval', 'approved');

                $displayKp->total_log = $myApprovedLogs->count();
                $adaPending = $myLogs->where('status_approval', 'pending')->count() > 0;

                if ($myLogs->count() > 0) {
                    $displayKp->status_approval_semua = $adaPending ? 'Menunggu pengecekan' : 'Diperiksa';
                }

                // Hitung statistik untuk Dashboard
                foreach ($myLogs as $log) {
                    if ($log->status_approval == 'approved') {
                        $jumlahSelesai++;
                    } elseif ($log->status_approval == 'pending') {
                        $jumlahBelumDiperiksa++;
                    }
                }
            }
            
            $pendaftarans->push($displayKp);
        }

        return view('dosen.daftar-mahasiswa', [
            'active' => 'daftar-mahasiswa',
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

        if ($pendaftaran->pembimbing_id != $dosenId && Auth::user()->role != 'koordinator_kp') {
            abort(403, 'Unauthorized access.');
        }

        $pendaftaranLoad->display_mahasiswa = $mhs;
        $pendaftaranLoad->display_judul_kp = $pendaftaranLoad->judul_kp;

        // Summary Badges (Sudah privat)
        $jumlahDiterima = $pendaftaranLoad->logBimbingans->where('status_approval', 'approved')->count();
        $jumlahBelumDiperiksa = $pendaftaranLoad->logBimbingans->where('status_approval', 'pending')->count();
        $jumlahDitolak = $pendaftaranLoad->logBimbingans->where('status_approval', 'rejected')->count();

        return view('dosen.daftar-mahasiswa-detail-log-bimbingan', [
            'active' => 'daftar-mahasiswa',
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
