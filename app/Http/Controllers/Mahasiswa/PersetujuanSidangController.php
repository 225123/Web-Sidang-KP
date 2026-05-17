<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use App\Models\PendaftaranKp;
use App\Models\PendaftaranSidang;
use App\Models\NotifikasiLog;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PersetujuanSidangController extends Controller
{
    // 1. Menampilkan Halaman Persetujuan (Sisi Mahasiswa)
    public function index()
    {
        $mahasiswaId = Auth::user()->id;

        $periodeId = session('selected_periode_id') ?? \App\Models\TahunAjaran::aktif()?->id ?? null;

        // Ambil data Pendaftaran KP yang sudah APPROVED milik mahasiswa ini (termasuk kelompook)
        $query = PendaftaranKp::withoutGlobalScope('periode')
            ->with(['mahasiswa.user', 'pembimbing'])
            ->where(function ($q) use ($mahasiswaId) {
                $q->where('mahasiswa_id', $mahasiswaId)
                    ->orWhereJsonContains('anggota_kelompok_ids', $mahasiswaId)
                    ->orWhereJsonContains('anggota_kelompok_ids', (string) $mahasiswaId);
            })
            ->where('status_kp', 'approved'); // Pastikan hanya KP yang disetujui
            
        if ($periodeId) {
            $query->where('tahun_ajaran_id', $periodeId);
        }

        $pendaftaran = $query->latest()->first();

        $totalBimbingan = 0;
        $persetujuan = null;

        if ($pendaftaran) {
            $totalBimbingan = $pendaftaran->logBimbingans()
                ->where('mahasiswa_id', $mahasiswaId)
                ->where('status_approval', 'approved')
                ->count();
            // Ambil data pengajuan sidang berdasarkan pendaftaran_kp_id dan mahasiswa_id
            $persetujuan = PendaftaranSidang::where('pendaftaran_kp_id', $pendaftaran->id)
                ->where('mahasiswa_id', $mahasiswaId)
                ->first();
        }

        $mahasiswaData = Auth::user()->mahasiswa;

        return view('mahasiswa.persetujuan-sidang-kp', compact('pendaftaran', 'totalBimbingan', 'persetujuan', 'mahasiswaData'));
    }

    // 2. Menyimpan/Mengajukan Laporan ke Dosen Pembimbing
    public function store(Request $request)
    {
        $request->validate([
            'file_laporan' => 'nullable|mimes:pdf|max:5120',
            'link_drive' => 'nullable|url',
        ]);

        $mahasiswaId = Auth::user()->id;

        $periodeId = session('selected_periode_id') ?? \App\Models\TahunAjaran::aktif()?->id ?? null;

        // Cari ID KP yang aktif untuk mahasiswa (termasuk kelompok)
        $query = PendaftaranKp::withoutGlobalScope('periode')
            ->where(function ($q) use ($mahasiswaId) {
                $q->where('mahasiswa_id', $mahasiswaId)
                    ->orWhereJsonContains('anggota_kelompok_ids', $mahasiswaId)
                    ->orWhereJsonContains('anggota_kelompok_ids', (string) $mahasiswaId);
            })
            ->where('status_kp', 'approved');
            
        if ($periodeId) {
            $query->where('tahun_ajaran_id', $periodeId);
        }

        $pendaftaran = $query->latest()->first();

        if (! $pendaftaran) {
            return back()->with('error', 'Anda belum memiliki pendaftaran KP yang disetujui oleh Koordinator.');
        }

        $filePath = null;
        if ($request->hasFile('file_laporan')) {
            $filePath = $request->file('file_laporan')->store('laporan_kp', upload_disk());
        }

        // Simpan atau update jika sudah ada (mencegah pendobelan row)
        // PENTING: status_koordinator HARUS 'unsubmitted' saat tahap ini agar record
        // tidak muncul di tabel Verifikasi Berkas Koordinator sebelum mahasiswa submit berkas final.
        PendaftaranSidang::updateOrCreate(
            ['pendaftaran_kp_id' => $pendaftaran->id, 'mahasiswa_id' => $mahasiswaId],
            [
                'file_laporan' => $filePath,
                'link_github' => $request->link_drive,
                'status_verifikasi' => 'pending', // Sesuai constraint DB ('pending', 'verified', 'rejected')
                'status_koordinator' => 'unsubmitted', // Belum diajukan ke koordinator — hanya persetujuan dosen
            ]
        );

        // --- Kirim Notifikasi ke Pembimbing ---
        if ($pendaftaran->pembimbing_id) {
            NotifikasiLog::create([
                'sender_id' => null,
                'receiver_id' => $pendaftaran->pembimbing_id,
                'judul' => "Permohonan Persetujuan Sidang",
                'pesan' => auth()->user()->name . " (" . (auth()->user()->mahasiswa->nim ?? '-') . ") telah mengajukan laporan untuk persetujuan sidang.",
                'target_url' => route('dosen.persetujuan-sidang.index'),
            ]);
        }

        return back()->with('success', 'Laporan berhasil diajukan. Silakan tunggu verifikasi dari Dosen Pembimbing.');
    }

    // 3. Menampilkan / Download PDF Surat Persetujuan
    public function cetakPersetujuan(Request $request, $id)
    {
        $persetujuan = PendaftaranSidang::with(['mahasiswa.user', 'pendaftaranKp.pembimbing.dosen'])->findOrFail($id);

        $pdf = Pdf::loadView('pdf.surat-persetujuan-sidang', compact('persetujuan'));

        // Jika parameter download=true ada di URL, maka file akan terdownload
        if ($request->has('download') && $request->download == 'true') {
            return $pdf->download('Surat_Persetujuan_Sidang_'.$persetujuan->mahasiswa->nim.'.pdf');
        }

        // Stream untuk menampilkan preview di dalam iframe (Gambar 3)
        return $pdf->stream('Surat_Persetujuan_Sidang.pdf');
    }
}
