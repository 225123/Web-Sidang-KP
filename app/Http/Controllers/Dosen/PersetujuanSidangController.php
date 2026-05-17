<?php

namespace App\Http\Controllers\Dosen;

use App\Http\Controllers\Controller;
use App\Models\PendaftaranSidang;
use App\Models\NotifikasiLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PersetujuanSidangController extends Controller
{
    // Menampilkan halaman persetujuan khusus mahasiswa bimbingan dosen yang sedang login
    public function index()
    {
        $dosenId = Auth::user()->id;

        // 1. Ambil data Persetujuan Sidang berdasarkan pendaftaran_kp yang dibimbing dosen ini
        $pengajuans = PendaftaranSidang::whereHas('pendaftaranKp', function ($q) use ($dosenId) {
            $q->where('pembimbing_id', $dosenId);
        })
            ->with(['mahasiswa.user', 'pendaftaranKp.logBimbingans'])
            ->get();

        // 3. Filter Manual untuk menghitung Total Bimbingan (Agar tidak bocor antar anggota kelompok)
        // Kita lakukan di level Collection agar tidak merusak Query SQL
        foreach ($pengajuans as $pengajuan) {
            $ownerId = $pengajuan->mahasiswa_id;

            // Filter log bimbingan agar hanya menghitung yang DISETUJUI oleh pemilik pengajuan ini
            $pengajuan->total_bimbingan_count = $pengajuan->pendaftaranKp ? $pengajuan->pendaftaranKp->logBimbingans
                ->where('mahasiswa_id', $ownerId)
                ->where('status_approval', 'approved')
                ->count() : 0;
        }

        // Statistik Badge (Gunakan status sesuai skema DB: verified, pending, rejected)
        $jumlahDisetujui = $pengajuans->where('status_verifikasi', 'verified')->count();
        $jumlahBelum = $pengajuans->where('status_verifikasi', 'pending')->count();
        $jumlahDitolak = $pengajuans->where('status_verifikasi', 'rejected')->count();

        return view('dosen.persetujuan-sidang', compact('pengajuans', 'jumlahDisetujui', 'jumlahBelum', 'jumlahDitolak'));
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
            'pesan' => "Pengajuan persetujuan sidang Anda telah disetujui oleh Dosen Pembimbing.",
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
        $mahasiswaId = $pengajuan->mahasiswa_id;

        // Fitur notifikasi di-bypass karena belum ada tabel/Model NotifikasiLog
        // NotifikasiLog::create([...]);

        if ($pengajuan->file_laporan) {
            \Illuminate\Support\Facades\Storage::disk(upload_disk())->delete($pengajuan->file_laporan);
        }

        $pengajuan->update([
            'status_verifikasi' => 'rejected',
            'dosen_feedback' => $request->feedback,
            'file_laporan' => null,
            'link_drive' => null,
        ]);

        // --- Kirim Notifikasi Sistem ---
        NotifikasiLog::create([
            'sender_id' => null,
            'receiver_id' => $mahasiswaId,
            'judul' => "Persetujuan Sidang: DITOLAK",
            'pesan' => "Pengajuan persetujuan sidang Anda telah ditolak oleh Dosen Pembimbing. Feedback: " . $request->feedback,
            'target_url' => route('mahasiswa.persetujuan-sidang.index'),
        ]);

        return back()->with('success', 'Pengajuan mahasiswa telah dikembalikan sebagai ditolak. Feedback telah disimpan dalam riwayat.');
    }
}
