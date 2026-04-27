<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use App\Models\NotifikasiLog;
use App\Models\PendaftaranSidang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RevisiController extends Controller
{
    public function index()
    {
        $mahasiswaId = Auth::user()->id;

        $sidang = PendaftaranSidang::with(['pendaftaranKp.pembimbing.dosen', 'penguji1.dosen', 'penguji2.dosen'])
            ->where('mahasiswa_id', $mahasiswaId)
            ->latest()
            ->first();

        return view('mahasiswa.revisi', compact('sidang'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'file_revisi' => 'nullable|mimes:pdf|max:5120',
            'link_revisi' => 'nullable|url',
        ]);

        if (! $request->hasFile('file_revisi') && ! $request->filled('link_revisi')) {
            return back()->with('error', 'Silakan unggah file PDF atau masukkan link Drive.');
        }

        $mahasiswaId = Auth::user()->id;
        $sidang = PendaftaranSidang::where('mahasiswa_id', $mahasiswaId)
            ->where('status_kelulusan', 'Lulus Dengan Revisi')
            ->first();

        if (! $sidang) {
            return back()->with('error', 'Data revisi tidak valid.');
        }

        // Cek jika status sudah selain Belum mengumpulkan
        if ($sidang->status_revisi !== 'Belum mengumpulkan') {
            return back()->with('error', 'Anda tidak dapat mengunggah revisi ulang.');
        }

        if ($request->hasFile('file_revisi')) {
            $path = $request->file('file_revisi')->store('revisi_sidang', 'public');
            $sidang->file_revisi = $path;
        }

        if ($request->filled('link_revisi')) {
            $sidang->link_revisi = $request->link_revisi;
        }

        $sidang->status_revisi = 'Menunggu';
        $sidang->tanggal_revisi = now();
        $sidang->save();

        // Notifikasi ke Koordinator
        NotifikasiLog::create([
            'sender_id' => null, // Sistem
            'target_role' => 'koordinator',
            'judul' => 'Pengumpulan Revisi Sidang',
            'pesan' => auth()->user()->name.' ('.(auth()->user()->mahasiswa->nim ?? '-').') telah mengumpulkan berkas revisi sidang.',
            'target_url' => route('koordinator.revisi.index'),
            'is_read' => false,
        ]);

        return back()->with('success', 'Berkas revisi berhasil diunggah. Menunggu pemeriksaan.');
    }
}
