<?php

namespace App\Http\Controllers\Koordinator;

use App\Http\Controllers\Controller;
use App\Models\LogBimbingan;
use App\Models\Mahasiswa;
use App\Models\PendaftaranKp;
use App\Models\NotifikasiLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BimbinganSayaController extends Controller
{
    public function index()
    {
        $dosenId = Auth::id();

        // Ambil pendaftaran KP di mana koordinator ini (sebagai dosen) adalah pembimbingnya
        $kps = PendaftaranKp::with(['supervisorInstansi', 'logBimbingans', 'mahasiswa.user'])
            ->where('pembimbing_id', $dosenId)
            ->get();

        $pendaftarans = collect();
        $jumlahSelesai = 0;
        $jumlahBelumDiperiksa = 0;
        $processedUserIds = [];

        foreach ($kps as $kp) {
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

                $mhs = Mahasiswa::with('user')->where('user_id', $uid)->first();
                if ($mhs) {
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
                        'total_log' => $myApprovedLogs->count(),
                        'status_approval_semua' => $myLogs->count() > 0 ? ($adaPending ? 'Menunggu pengecekan' : 'Diperiksa') : '-',
                    ]);

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
            },
        ])->where('id', $id)->firstOrFail();

        // Cek authorization: Dosen berhak akses jika ia pembimbing mahasiswa ini 
        // (baik sebagai ketua maupun sebagai anggota kelompok di pendaftaran yang dibimbingnya)
        $isAuthorized = false;
        if (Auth::user()->role == 'koordinator' || Auth::user()->role == 'koordinator_kp') {
            $isAuthorized = true;
        } else {
            $mhsId = $pendaftaran->mahasiswa_id;
            $isAuthorized = PendaftaranKp::where('pembimbing_id', $dosenId)
                ->where(function($q) use ($mhsId) {
                    $q->where('mahasiswa_id', $mhsId)
                      ->orWhere('anggota_kelompok_ids', 'LIKE', '%"'.$mhsId.'"%')
                      ->orWhere('anggota_kelompok_ids', 'LIKE', '%'.$mhsId.'%');
                })->exists();
        }

        if (!$isAuthorized) {
            abort(403, 'Unauthorized access. Anda bukan dosen pembimbing mahasiswa ini.');
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
