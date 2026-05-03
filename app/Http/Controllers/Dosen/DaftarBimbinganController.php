<?php

namespace App\Http\Controllers\Dosen;

use App\Http\Controllers\Controller;
use App\Models\LogBimbingan;
use App\Models\Mahasiswa;
use App\Models\PendaftaranKp;
use App\Models\NotifikasiLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DaftarBimbinganController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $isKoordinator = ($user->role === 'koordinator');
        $dosenId = $user->id;

        // 1. Ambil pendaftaran KP. Jika koordinator, ambil SEMUA yang sudah mendaftar (untuk memantau).
        // Jika dosen, ambil yang dibimbing.
        $query = PendaftaranKp::with(['supervisorInstansi', 'logBimbingans', 'mahasiswa.user', 'pembimbing']);

        if (! $isKoordinator) {
            $query->where('pembimbing_id', $dosenId);
        }

        $kps = $query->get();

        $pendaftarans = collect();
        $jumlahSelesai = 0;
        $jumlahBelumDiperiksa = 0;

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
                $mhs = Mahasiswa::with('user')->where('user_id', $uid)->first();
                if ($mhs) {
                    // Cek log bimbingan untuk mahasiswa spesifik ini
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
                        'status_approval_semua' => $myLogs->count() > 0 ? ($adaPending ? 'Menunggu pengecekan' : 'Diperiksa') : '-',
                    ]);

                    // Statistik Global (Disesuaikan dengan konteks login)
                    if (! $isKoordinator) {
                        foreach ($myLogs as $log) {
                            if ($log->status_approval == 'approved') {
                                $jumlahSelesai++;
                            } elseif ($log->status_approval == 'pending') {
                                $jumlahBelumDiperiksa++;
                            }
                        }
                    }
                }
            }
        }

        // Statistik global untuk koordinator: Total dari seluruh sistem
        if ($isKoordinator) {
            $jumlahSelesai = LogBimbingan::where('status_approval', 'approved')->count();
            $jumlahBelumDiperiksa = LogBimbingan::where('status_approval', 'pending')->count();
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
            },
        ])->where('id', $id)->firstOrFail();

        if ($pendaftaran->pembimbing_id != $dosenId && Auth::user()->role != 'koordinator') {
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
            'status_approval' => 'required|in:approved,rejected',
        ]);

        $log = LogBimbingan::findOrFail($log_id);
        $log->update([
            'status_approval' => $request->status_approval,
        ]);

        // --- Kirim Notifikasi Sistem ---
        $statusText = $request->status_approval === 'approved' ? 'DISETUJUI' : 'DITOLAK';
        NotifikasiLog::create([
            'sender_id' => null,
            'receiver_id' => $log->mahasiswa_id,
            'judul' => "Log Bimbingan: $statusText",
            'pesan' => "Log bimbingan Anda tanggal " . date('d/m/Y', strtotime($log->tanggal)) . " telah $statusText oleh Pembimbing.",
            'target_url' => route('mahasiswa.bimbingan-dosen'),
        ]);

        return back()->with('success', 'Status bimbingan berhasil diperbarui.');
    }
}
