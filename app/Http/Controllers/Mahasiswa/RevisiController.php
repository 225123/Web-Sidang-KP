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
        $periodeId = session('selected_periode_id') ?? \App\Models\TahunAjaran::aktif()?->id ?? null;

        $query = PendaftaranSidang::with(['pendaftaranKp.pembimbing.dosen', 'penguji1.dosen', 'penguji2.dosen'])
            ->where('mahasiswa_id', $mahasiswaId);

        if ($periodeId) {
            $query->whereHas('pendaftaranKp', function($q) use ($periodeId) {
                $q->withoutGlobalScope('periode')->where('tahun_ajaran_id', $periodeId);
            });
        }

        $sidang = $query->latest()->first();

        if ($sidang) {
            $sidangScore = ((float) ($sidang->nilai_penguji_1 ?? 0) * 0.5) + ((float) ($sidang->nilai_penguji_2 ?? 0) * 0.5);
            $originalGrade = $this->getGradeFromScore($sidangScore);
            
            $revisiVerified = ($sidang->status_revisi === 'Disahkan' || $sidang->status_revisi === 'Diterima');
            $finalGrade = $originalGrade;
            
            if ($sidang->status_kelulusan === 'Lulus Dengan Revisi' && !$revisiVerified) {
                $finalGrade = $this->getPenalizedGrade($originalGrade);
            }
            
            $sidang->nilai_akhir_display = $sidangScore;
            $sidang->grade_display = $finalGrade;
        }

        $isPastPeriod = $periodeId && $periodeId != \App\Models\TahunAjaran::aktif()?->id;

        return view('mahasiswa.revisi', compact('sidang', 'isPastPeriod'));
    }

    private function getGradeFromScore($nilai)
    {
        if ($nilai >= 86) return 'A';
        if ($nilai >= 81) return 'A-';
        if ($nilai >= 76) return 'B+';
        if ($nilai >= 71) return 'B';
        if ($nilai >= 66) return 'B-';
        if ($nilai >= 61) return 'C+';
        if ($nilai >= 56) return 'C';
        if ($nilai >= 46) return 'D';
        return 'E';
    }

    private function getPenalizedGrade($grade)
    {
        $grades = ['A', 'A-', 'B+', 'B', 'B-', 'C+', 'C', 'D', 'E'];
        $index = array_search($grade, $grades);
        $newIndex = min($index + 1, count($grades) - 1);
        return $grades[$newIndex];
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

        // Cek jika status sudah disahkan
        if (in_array(strtolower($sidang->status_revisi), ['disetujui', 'verified', 'disahkan'])) {
            return back()->with('error', 'Anda tidak dapat mengunggah revisi ulang karena berkas telah disahkan.');
        }

        if ($request->hasFile('file_revisi')) {
            $path = $request->file('file_revisi')->store('revisi_sidang', upload_disk());
            $sidang->file_revisi = $path;
            $sidang->link_revisi = null;
        } elseif ($request->filled('link_revisi')) {
            $sidang->link_revisi = $request->link_revisi;
            $sidang->file_revisi = null;
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

    public function destroy($id)
    {
        $mahasiswaId = Auth::user()->id;
        $sidang = PendaftaranSidang::where('id', $id)
            ->where('mahasiswa_id', $mahasiswaId)
            ->first();

        if (! $sidang) {
            return back()->with('error', 'Data revisi tidak ditemukan atau tidak valid.');
        }

        if (in_array(strtolower($sidang->status_revisi), ['disetujui', 'verified', 'disahkan', 'diterima'])) {
            return back()->with('error', 'Anda tidak dapat menghapus revisi karena berkas telah disahkan.');
        }

        if ($sidang->file_revisi) {
            \Illuminate\Support\Facades\Storage::disk(upload_disk())->delete($sidang->file_revisi);
            $sidang->file_revisi = null;
        }

        $sidang->link_revisi = null;
        $sidang->status_revisi = 'Belum mengumpulkan';
        $sidang->tanggal_revisi = null;
        $sidang->save();

        // Optional: Notifikasi ke Dosen bahwa dihapus bisa ditambah jika perlu, tapi karena akan upload ulang mungkin tidak perlu.
        return back()->with('success', 'Berkas revisi berhasil dihapus. Silakan unggah kembali berkas yang baru.');
    }
}
