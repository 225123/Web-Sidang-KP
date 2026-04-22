<?php

namespace App\Http\Controllers\Koordinator;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\PendaftaranSidang;
use App\Models\PendaftaranKp;

class PersetujuanSidangController extends Controller
{
    // Menampilkan halaman persetujuan khusus mahasiswa bimbingan koordinator (sebagai agen dosen)
    public function index()
    {
        $dosenId = Auth::user()->id;

        // 1. Ambil data Persetujuan Sidang berdasarkan pendaftaran_kp yang dibimbing dosen ini
        $pengajuans = PendaftaranSidang::whereHas('pendaftaranKp', function($q) use ($dosenId) {
                $q->where('pembimbing_id', $dosenId);
            })
            ->with(['mahasiswa.user', 'pendaftaranKp.logBimbingans'])
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

        return view('koordinator.persetujuan-sidang', compact('pengajuans', 'jumlahDisetujui', 'jumlahBelum', 'jumlahDitolak'));
    }

    // Mengesahkan / Menyetujui Pengajuan Sidang
    public function update(Request $request, $id)
    {
        $pengajuan = PendaftaranSidang::findOrFail($id);

        $pengajuan->update([
            'status_verifikasi' => 'verified'
        ]);

        return back()->with('success', 'Pengajuan mahasiswa berhasil disahkan.');
    }

    // Menolak Pengajuan (Hapus Row & Kirim Feedback)
    public function tolak(Request $request, $id)
    {
        $request->validate([
            'feedback' => 'required|string'
        ]);

        $pengajuan = PendaftaranSidang::findOrFail($id);
        
        $pengajuan->update([
            'status_verifikasi' => 'rejected',
            'dosen_feedback' => $request->feedback
        ]);

        return back()->with('success', 'Pengajuan berhasil ditolak dan dipindahkan ke riwayat penolakan.');
    }
}
