<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\PendaftaranKp;
use App\Models\PendaftaranSidang;
use Barryvdh\DomPDF\Facade\Pdf;

class PersetujuanSidangController extends Controller
{
    // 1. Menampilkan Halaman Persetujuan (Sisi Mahasiswa)
    public function index()
    {
        $mahasiswaId = Auth::user()->id;

        // Ambil data Pendaftaran KP yang sudah APPROVED milik mahasiswa ini
        $pendaftaran = PendaftaranKp::with(['mahasiswa.user', 'pembimbing'])
            ->where('mahasiswa_id', $mahasiswaId)
            ->where('status_kp', 'approved') // Pastikan hanya KP yang disetujui
            ->latest()
            ->first();

        $totalBimbingan = 0;
        $persetujuan = null;

        if ($pendaftaran) {
            $totalBimbingan = $pendaftaran->logBimbingans()->count();
            // Ambil data pengajuan sidang berdasarkan pendaftaran_kp_id dan mahasiswa_id
            $persetujuan = PendaftaranSidang::where('pendaftaran_kp_id', $pendaftaran->id)
                ->where('mahasiswa_id', $mahasiswaId)
                ->first();
        }

        return view('mahasiswa.persetujuan-sidang-kp', compact('pendaftaran', 'totalBimbingan', 'persetujuan'));
    }

    // 2. Menyimpan/Mengajukan Laporan ke Dosen Pembimbing
    public function store(Request $request)
    {
        $request->validate([
            'file_laporan' => 'nullable|mimes:pdf|max:5120',
            'link_drive' => 'nullable|url'
        ]);

        $mahasiswaId = Auth::user()->id;

        // Cari ID KP yang aktif (ID 28 atau 26 seperti di hasil debug Anda)
        $pendaftaran = PendaftaranKp::where('mahasiswa_id', $mahasiswaId)
            ->where('status_kp', 'approved')
            ->latest()
            ->first();

        if (!$pendaftaran) {
            return back()->with('error', 'Anda belum memiliki pendaftaran KP yang disetujui oleh Koordinator.');
        }

        $filePath = null;
        if ($request->hasFile('file_laporan')) {
            $filePath = $request->file('file_laporan')->store('laporan_kp', 'public');
        }

        // Simpan atau update jika sudah ada (mencegah pendobelan row)
        PendaftaranSidang::updateOrCreate(
            ['pendaftaran_kp_id' => $pendaftaran->id, 'mahasiswa_id' => $mahasiswaId], 
            [
                'file_laporan' => $filePath,
                'link_github' => $request->link_drive,
                'status_verifikasi' => 'pending', // Sesuai constraint DB ('pending', 'verified', 'rejected')
                'tahun_ajaran_id' => $pendaftaran->tahun_ajaran_id
            ]
        );

        return back()->with('success', 'Laporan berhasil diajukan. Silakan tunggu verifikasi dari Dosen Pembimbing.');
    }

    // 3. Menampilkan / Download PDF Surat Persetujuan
    public function cetakPersetujuan(Request $request, $id)
    {
        $persetujuan = PendaftaranSidang::with(['mahasiswa.user', 'mahasiswa.pembimbing.dosen'])->findOrFail($id);

        $pdf = Pdf::loadView('pdf.surat-persetujuan-sidang', compact('persetujuan'));

        // Jika parameter download=true ada di URL, maka file akan terdownload
        if ($request->has('download') && $request->download == 'true') {
            return $pdf->download('Surat_Persetujuan_Sidang_' . $persetujuan->mahasiswa->nim . '.pdf');
        }

        // Stream untuk menampilkan preview di dalam iframe (Gambar 3)
        return $pdf->stream('Surat_Persetujuan_Sidang.pdf');
    }
}