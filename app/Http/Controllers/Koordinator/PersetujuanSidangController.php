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

        // 1. Ambil ID Mahasiswa yang dibimbing oleh dosen ini secara langsung
        $mhsIds = \App\Models\Mahasiswa::where('pembimbing_id', $dosenId)->pluck('user_id');

        // 2. Ambil data Persetujuan Sidang berdasarkan Mahasiswa tersebut
        $pengajuans = PendaftaranSidang::whereIn('mahasiswa_id', $mhsIds)
            ->with(['mahasiswa.user', 'pendaftaranKp.logBimbingans'])
            ->get();

        // 3. Filter Manual untuk menghitung Total Bimbingan (Agar tidak bocor antar anggota kelompok)
        foreach ($pengajuans as $pengajuan) {
            $ownerId = $pengajuan->mahasiswa_id;

            $pengajuan->total_bimbingan_count = $pengajuan->pendaftaranKp ? $pengajuan->pendaftaranKp->logBimbingans
                ->where('mahasiswa_id', $ownerId)
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
        $pengajuan->delete();

        return back()->with('success', 'Pengajuan berhasil ditolak dan dihapus dari antrean. Feedback telah dikirim ke mahasiswa.');
    }
}
