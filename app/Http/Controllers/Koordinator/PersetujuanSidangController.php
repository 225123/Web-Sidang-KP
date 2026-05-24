<?php

namespace App\Http\Controllers\Koordinator;

use App\Http\Controllers\Controller;
use App\Models\PendaftaranSidang;
use App\Models\NotifikasiLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PersetujuanSidangController extends Controller
{
    // Menampilkan halaman persetujuan khusus mahasiswa bimbingan koordinator (sebagai agen dosen)
    public function index()
    {
        $dosenId = Auth::user()->id;

        $periodeId = session('selected_periode_id');
        $pengajuans = PendaftaranSidang::whereHas('pendaftaranKp', function ($q) use ($dosenId, $periodeId) {
            $q->where('pembimbing_id', $dosenId);
            if ($periodeId) {
                $q->where('tahun_ajaran_id', $periodeId);
            }
        })
            ->with(['mahasiswa.user', 'pendaftaranKp.logBimbingans'])
            ->get();

        $riwayatPenolakan = \App\Models\RiwayatPenolakanSidang::whereHas('pendaftaranSidang.pendaftaranKp', function ($q) use ($dosenId, $periodeId) {
            $q->where('pembimbing_id', $dosenId);
            if ($periodeId) {
                $q->where('tahun_ajaran_id', $periodeId);
            }
        })
        ->with(['pendaftaranSidang.mahasiswa.user', 'pendaftaranSidang.pendaftaranKp'])
        ->orderBy('created_at', 'desc')
        ->get();

        // 3. Filter Manual untuk menghitung Total Bimbingan (Agar tidak bocor antar anggota kelompok)
        foreach ($pengajuans as $pengajuan) {
            $ownerId = $pengajuan->mahasiswa_id;

            // Filter log bimbingan agar hanya menghitung yang DISETUJUI oleh pemilik pengajuan ini
            $pengajuan->total_bimbingan_count = $pengajuan->pendaftaranKp ? $pengajuan->pendaftaranKp->logBimbingans
                ->where('mahasiswa_id', $ownerId)
                ->where('status_approval', 'approved')
                ->count() : 0;
        }

        // Statistik Badge
        $jumlahDisetujui = $pengajuans->where('status_verifikasi', 'verified')->count();
        $jumlahBelum = $pengajuans->where('status_verifikasi', 'pending')->count();
        $jumlahDitolak = $pengajuans->where('status_verifikasi', 'rejected')->count();

        return view('koordinator.persetujuan-sidang', compact('pengajuans', 'riwayatPenolakan', 'jumlahDisetujui', 'jumlahBelum', 'jumlahDitolak'));
    }

    // Mengesahkan / Menyetujui Pengajuan Sidang
    public function update(Request $request, $id)
    {
        $pengajuan = PendaftaranSidang::findOrFail($id);

        $pengajuan->update([
            'status_verifikasi' => 'verified',
        ]);

        // --- Kirim Notifikasi Sistem ---
        NotifikasiLog::create([
            'sender_id' => null,
            'receiver_id' => $pengajuan->mahasiswa_id,
            'judul' => "Persetujuan Sidang: DISETUJUI",
            'pesan' => "Pengajuan persetujuan sidang Anda telah disetujui oleh Koordinator (sebagai Pembimbing).",
            'target_url' => route('mahasiswa.persetujuan-sidang.index'),
        ]);

        return back()->with('success', 'Pengajuan mahasiswa berhasil disahkan.');
    }

    // Menolak Pengajuan (Hapus Row & Kirim Feedback)
    public function tolak(Request $request, $id)
    {
        $request->validate([
            'feedback' => 'required|string',
        ]);

        $pengajuan = PendaftaranSidang::findOrFail($id);

        $pengajuan->update([
            'status_verifikasi' => 'rejected',
            'dosen_feedback' => $request->feedback,
        ]);

        \App\Models\RiwayatPenolakanSidang::create([
            'pendaftaran_sidang_id' => $pengajuan->id,
            'alasan_penolakan' => $request->feedback,
            'ditolak_oleh' => 'Koordinator',
        ]);

        // --- Kirim Notifikasi Sistem ---
        NotifikasiLog::create([
            'sender_id' => null,
            'receiver_id' => $pengajuan->mahasiswa_id,
            'judul' => "Persetujuan Sidang: DITOLAK",
            'pesan' => "Pengajuan persetujuan sidang Anda telah ditolak oleh Koordinator (sebagai Pembimbing). Feedback: " . $request->feedback,
            'target_url' => route('mahasiswa.persetujuan-sidang.index'),
        ]);

        return back()->with('success', 'Pengajuan berhasil ditolak dan dipindahkan ke riwayat penolakan.');
    }
}
