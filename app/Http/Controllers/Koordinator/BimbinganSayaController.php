<?php

namespace App\Http\Controllers\Koordinator;

use App\Http\Controllers\Controller;
use App\Models\LogBimbingan;
use App\Models\Mahasiswa;
use App\Models\PendaftaranKp;
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
                $mhs = Mahasiswa::with('user')->where('user_id', $uid)->first();
                if ($mhs) {
                    $myLogs = $kp->logBimbingans->where('mahasiswa_id', $mhs->user_id);
                    $myApprovedLogs = $myLogs->where('status_approval', 'approved');
                    $adaPending = $myLogs->where('status_approval', 'pending')->count() > 0;

                    $pendaftarans->push([
                        'id' => $kp->id,
                        'display_mahasiswa' => $mhs,
                        'display_judul_kp' => $kp->judul_kp ?? '-',
                        'display_instansi' => $kp->instansi_nama ?? '-',
                        'display_supervisor' => ($kp->supervisorInstansi) ? $kp->supervisorInstansi->nama_supervisor : '-',
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
            'status_approval' => 'required|in:approved,rejected',
        ]);

        $log = LogBimbingan::findOrFail($log_id);
        $log->update([
            'status_approval' => $request->status_approval,
        ]);

        return back()->with('success', 'Status bimbingan berhasil diperbarui.');
    }
}
