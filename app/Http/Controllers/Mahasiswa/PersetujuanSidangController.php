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
            $persetujuan = PendaftaranSidang::where('pendaftaran_kp_id', $pendaftaran->id)
                ->where('mahasiswa_id', $mahasiswaId)
                ->first();
        }

        $ownKp = PendaftaranKp::where('mahasiswa_id', $mahasiswaId)->latest()->first();

        $mahasiswaData = Auth::user()->mahasiswa;

        $isReadOnly = $periodeId && $periodeId != \App\Models\TahunAjaran::aktif()?->id;

        return view('mahasiswa.persetujuan-sidang-kp', compact('pendaftaran', 'totalBimbingan', 'persetujuan', 'mahasiswaData', 'ownKp', 'isReadOnly'));
    }

    // 2. Menyimpan/Mengajukan Laporan ke Dosen Pembimbing
    public function store(Request $request)
    {
        $request->validate([
            'file_laporan' => 'nullable|required_without:link_drive|mimes:pdf|max:5120',
            'link_drive' => 'nullable|required_without:file_laporan|url',
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

        $dataToUpdate = [
            'link_drive' => $request->link_drive,
            'status_verifikasi' => 'pending', // Sesuai constraint DB ('pending', 'verified', 'rejected')
            'status_koordinator' => 'unsubmitted', // Belum diajukan ke koordinator — hanya persetujuan dosen
        ];

        if ($request->hasFile('file_laporan')) {
            $dataToUpdate['file_laporan'] = $request->file('file_laporan')->store('laporan_kp', upload_disk());
            $dataToUpdate['link_drive'] = null;
        } elseif ($request->filled('link_drive')) {
            $dataToUpdate['link_drive'] = $request->link_drive;
            $dataToUpdate['file_laporan'] = null;
        }

        // Simpan atau update jika sudah ada (mencegah pendobelan row)
        // PENTING: status_koordinator HARUS 'unsubmitted' saat tahap ini agar record
        // tidak muncul di tabel Verifikasi Berkas Koordinator sebelum mahasiswa submit berkas final.
        PendaftaranSidang::updateOrCreate(
            ['pendaftaran_kp_id' => $pendaftaran->id, 'mahasiswa_id' => $mahasiswaId],
            $dataToUpdate
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

        $ownKp = \App\Models\PendaftaranKp::where('mahasiswa_id', $persetujuan->mahasiswa_id)
            ->where('status_kp', 'approved')
            ->latest()
            ->first();
        $persetujuan->judul_kp_display = $ownKp ? $ownKp->judul_kp : ($persetujuan->pendaftaranKp->judul_kp ?? '-');

        $pdf = Pdf::loadView('pdf.surat-persetujuan-sidang', compact('persetujuan'));

        // Jika parameter download=true ada di URL, maka file akan terdownload
        if ($request->has('download') && $request->download == 'true') {
            return $pdf->download('Surat_Persetujuan_Sidang_'.$persetujuan->mahasiswa->nim.'.pdf');
        }

        // Stream untuk menampilkan preview di dalam iframe (Gambar 3)
        return $pdf->stream('Surat_Persetujuan_Sidang.pdf');
    }

    // 4. Menghapus / Membatalkan Laporan yang Telah Diajukan
    public function destroy($id)
    {
        $mahasiswaId = Auth::user()->id;
        $persetujuan = PendaftaranSidang::where('id', $id)
            ->where('mahasiswa_id', $mahasiswaId)
            ->first();

        if (! $persetujuan) {
            return back()->with('error', 'Data tidak ditemukan atau tidak valid.');
        }

        // Hanya bisa menghapus jika status masih pending/Menunggu
        if (in_array(strtolower($persetujuan->status_verifikasi), ['verified', 'disetujui'])) {
            return back()->with('error', 'Anda tidak dapat membatalkan pengajuan karena telah disetujui oleh Dosen Pembimbing.');
        }

        if ($persetujuan->file_laporan) {
            \Illuminate\Support\Facades\Storage::disk(upload_disk())->delete($persetujuan->file_laporan);
            $persetujuan->file_laporan = null;
        }

        $persetujuan->link_drive = null;
        $persetujuan->status_verifikasi = null; 
        $persetujuan->save();

        return back()->with('success', 'Pengajuan berhasil dibatalkan. Silakan unggah kembali berkas yang baru.');
    }
}
