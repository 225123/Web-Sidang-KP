<?php

namespace App\Http\Controllers\Dosen;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\PendaftaranSidang;
use App\Models\PendaftaranKp;


class PersetujuanSidangController extends Controller
{
    // Menampilkan halaman persetujuan khusus mahasiswa bimbingan dosen yang sedang login
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
        // Kita lakukan di level Collection agar tidak merusak Query SQL
        foreach ($pengajuans as $pengajuan) {
            $ownerId = $pengajuan->mahasiswa_id;

            // Filter log bimbingan agar hanya menghitung yang diupload oleh pemilik pengajuan ini
            $pengajuan->total_bimbingan_count = $pengajuan->pendaftaranKp ? $pengajuan->pendaftaranKp->logBimbingans
                ->where('mahasiswa_id', $ownerId)
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
            'status_verifikasi' => 'verified'
        ]);

        // Fitur notifikasi di-bypass karena belum ada tabel/Model NotifikasiLog
        // NotifikasiLog::create([...]);

        return back()->with('success', 'Pengajuan mahasiswa berhasil disahkan.');
    }

    // Menolak Pengajuan (Hapus Row & Kirim Feedback)
    public function tolak(Request $request, $id)
    {
        $request->validate([
            'feedback' => 'required|string'
        ]);

        $pengajuan = PendaftaranSidang::findOrFail($id);
        $mahasiswaId = $pengajuan->mahasiswa_id;

        // Fitur notifikasi di-bypass karena belum ada tabel/Model NotifikasiLog
        // NotifikasiLog::create([...]);

        // 2. Ubah status menjadi rejected dan simpan alasan penolakan
        $pengajuan->update([
            'status_verifikasi' => 'rejected',
            'dosen_feedback' => $request->feedback
        ]);

        return back()->with('success', 'Pengajuan mahasiswa telah dikembalikan sebagai ditolak. Feedback telah disimpan dalam riwayat.');
    }
}