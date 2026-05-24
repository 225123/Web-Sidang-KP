<?php

namespace App\Http\Controllers\Koordinator;

use App\Http\Controllers\Controller;
use App\Models\PendaftaranSidang;
use App\Models\NotifikasiLog;

class FinalisasiNilaiController extends Controller
{
    public function index()
    {
        $activePeriodId = session('selected_periode_id') ?? \App\Models\TahunAjaran::aktif()?->id;

        // Mendukung status Lulus, Lulus Dengan Revisi, dan Lanjut (pengganti Tidak Lulus)
        $sidangs = PendaftaranSidang::with(['mahasiswa.user', 'pendaftaranKp.pembimbing', 'pendaftaranKp.supervisorInstansi'])
            ->whereHas('pendaftaranKp', function($q) use ($activePeriodId) {
                if ($activePeriodId) {
                    $q->where('tahun_ajaran_id', $activePeriodId);
                }
            })
            ->whereIn('status_kelulusan', ['Lulus', 'Lulus Dengan Revisi', 'Lanjut', 'Tidak Lulus'])
            ->get()
            ->sortBy(function ($sidang) {
                return $sidang->mahasiswa->nim;
            })
            ->values()
            ->map(function ($sidang) {
                // Standarisasi status: Jika di DB masih 'Tidak Lulus', tampilkan sebagai 'Lanjut'
                if ($sidang->status_kelulusan === 'Tidak Lulus') {
                    $sidang->status_kelulusan = 'Lanjut';
                }

                $logic = $this->calculateFinalLogic($sidang);
                $sidang->nilai_akhir_display = $logic['nilai'];
                $sidang->grade_display = $logic['grade'];
                $sidang->original_grade = $logic['original_grade'];
                $sidang->is_penalized = $logic['is_penalized'];

                // Dapatkan Judul KP spesifik milik mahasiswa (karena anggota kelompok bisa beda judul)
                $ownKp = \App\Models\PendaftaranKp::where('mahasiswa_id', $sidang->mahasiswa_id)
                    ->where('status_kp', 'approved')
                    ->latest()
                    ->first();
                $sidang->judul_kp_display = $ownKp ? $ownKp->judul_kp : ($sidang->pendaftaranKp->judul_kp ?? '-');

                return $sidang;
            });

        // Berita Acara is now automatically available to students who finished defense
        $allBeritaAcaraSubmitted = true;

        // Check if all valid sidangs are already dipublikasi
        $hasSidangToSah = $sidangs->where('nilai_dipublikasi', false)->count() > 0;
        $hasValidSidangs = $sidangs->count() > 0;
        $isAllNilaiDisahkan = !$hasSidangToSah && $hasValidSidangs;

        return view('koordinator.finalisasi-nilai', compact('sidangs', 'allBeritaAcaraSubmitted', 'isAllNilaiDisahkan', 'hasValidSidangs'));
    }

    public function sahkan()
    {
        $activePeriodId = session('selected_periode_id') ?? \App\Models\TahunAjaran::aktif()?->id;

        $sidangs = PendaftaranSidang::whereHas('pendaftaranKp', function($q) use ($activePeriodId) {
                if ($activePeriodId) {
                    $q->where('tahun_ajaran_id', $activePeriodId);
                }
            })
            ->whereIn('status_kelulusan', ['Lulus', 'Lulus Dengan Revisi', 'Lanjut', 'Tidak Lulus'])
            ->where('nilai_dipublikasi', false)
            ->get();

        foreach ($sidangs as $sidang) {
            $sidang->nilai_dipublikasi = true;
            $sidang->save();

            NotifikasiLog::create([
                'sender_id' => null, // Sistem
                'receiver_id' => $sidang->mahasiswa->user_id,
                'judul' => 'Nilai Sidang Terbit',
                'pesan' => 'Koordinator telah mempublikasikan Nilai Akhir Sidang KP Anda. Silakan cek halaman Nilai Akhir untuk mengunduh dokumen terkait.',
                'target_url' => route('mahasiswa.nilai-akhir'),
            ]);
        }

        return back()->with('success', 'Finalisasi Nilai berhasil disahkan dan diterbitkan ke mahasiswa.');
    }

    public function show($id)
    {
        $sidang = PendaftaranSidang::with(['mahasiswa.user', 'penguji1', 'penguji2', 'pendaftaranKp.pembimbing', 'pendaftaranKp.supervisorInstansi'])
            ->findOrFail($id);

        // Standarisasi status
        if ($sidang->status_kelulusan === 'Tidak Lulus') {
            $sidang->status_kelulusan = 'Lanjut';
        }

        $logic = $this->calculateFinalLogic($sidang);
        $sidang->nilai_akhir_display = $logic['nilai'];
        $sidang->grade_display = $logic['grade'];
        $sidang->original_grade = $logic['original_grade'];
        $sidang->is_penalized = $logic['is_penalized'];

        // Dapatkan Judul KP spesifik milik mahasiswa (karena anggota kelompok bisa beda judul)
        $ownKp = \App\Models\PendaftaranKp::where('mahasiswa_id', $sidang->mahasiswa_id)
            ->where('status_kp', 'approved')
            ->latest()
            ->first();
        $sidang->judul_kp_display = $ownKp ? $ownKp->judul_kp : ($sidang->pendaftaranKp->judul_kp ?? '-');

        return view('koordinator.finalisasi-nilai-detail', compact('sidang'));
    }

    public function downloadPdf($id)
    {
        $sidang = PendaftaranSidang::with(['mahasiswa.user', 'penguji1', 'penguji2', 'pendaftaranKp.pembimbing', 'pendaftaranKp.supervisorInstansi'])
            ->findOrFail($id);

        $logic = $this->calculateFinalLogic($sidang);
        $sidang->nilai_akhir_display = $logic['nilai'];
        $sidang->grade_display = $logic['grade'];
        $sidang->original_grade = $logic['original_grade'];
        $sidang->is_penalized = $logic['is_penalized'];

        // Dapatkan Judul KP spesifik milik mahasiswa
        $ownKp = \App\Models\PendaftaranKp::withoutGlobalScope('periode')
            ->where('mahasiswa_id', $sidang->mahasiswa_id)
            ->whereIn('status_kp', ['pending', 'approved'])
            ->latest()
            ->first();
        $sidang->judul_kp_display = $ownKp ? $ownKp->judul_kp : ($sidang->pendaftaranKp->judul_kp ?? '-');

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('exports.nilai-akhir-pdf', compact('sidang'));
        return $pdf->download('Nilai_Akhir_' . ($sidang->mahasiswa->nim ?? 'Mahasiswa') . '.pdf');
    }

    public function downloadBeritaAcara($id)
    {
        $sidang = PendaftaranSidang::with(['mahasiswa.user', 'penguji1.dosen', 'penguji2.dosen', 'pendaftaranKp.pembimbing.dosen', 'pendaftaranKp.supervisorInstansi'])
            ->findOrFail($id);

        if ($sidang->pelaksanaan !== 'Selesai') {
            abort(403, 'Berita acara belum tersedia karena sidang belum selesai.');
        }

        // Dapatkan Judul KP spesifik milik mahasiswa
        $ownKp = \App\Models\PendaftaranKp::withoutGlobalScope('periode')
            ->where('mahasiswa_id', $sidang->mahasiswa_id)
            ->whereIn('status_kp', ['pending', 'approved'])
            ->latest()
            ->first();
        $sidang->judul_kp_display = $ownKp ? $ownKp->judul_kp : ($sidang->pendaftaranKp->judul_kp ?? '-');

        // Get Koordinator (Official role)
        $periode = \App\Models\TahunAjaran::aktif();
        $koordinator = null;
        if ($periode && $periode->koordinator_id) {
            $koordinator = \App\Models\User::with('dosen')->find($periode->koordinator_id);
        }
        if (!$koordinator) {
            $koordinator = \App\Models\User::where('role', 'koordinator_kp')->with('dosen')->first();
        }

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('koordinator.berita-acara-pdf-template', compact('sidang', 'koordinator'));
        return $pdf->download('Berita_Acara_' . ($sidang->mahasiswa->nim ?? 'Mahasiswa') . '.pdf');
    }

    private function calculateFinalLogic($sidang)
    {
        $status = $sidang->status_kelulusan;

        // Case: Lanjut / Tidak Lulus (Nilai 0, Grade E)
        if ($status === 'Lanjut' || $status === 'Tidak Lulus') {
            return [
                'nilai' => 0,
                'grade' => 'E',
                'original_grade' => 'E',
                'is_penalized' => false,
            ];
        }

        $sidangScore = ((float) ($sidang->nilai_penguji_1 ?? 0) * 0.5) + ((float) ($sidang->nilai_penguji_2 ?? 0) * 0.5);

        $revisiVerified = ($sidang->status_revisi === 'Disahkan' || $sidang->status_revisi === 'Diterima');
        $originalGrade = $this->getGradeFromScore($sidangScore);
        $finalGrade = $originalGrade;
        $isPenalized = false;

        if ($status === 'Lulus Dengan Revisi' && ! $revisiVerified) {
            $finalGrade = $this->getPenalizedGrade($originalGrade);
            $isPenalized = true;
        }

        return [
            'nilai' => $sidangScore,
            'grade' => $finalGrade,
            'original_grade' => $originalGrade,
            'is_penalized' => $isPenalized,
        ];
    }

    private function getGradeFromScore($nilai)
    {
        if ($nilai >= 86) {
            return 'A';
        }
        if ($nilai >= 81) {
            return 'A-';
        }
        if ($nilai >= 76) {
            return 'B+';
        }
        if ($nilai >= 71) {
            return 'B';
        }
        if ($nilai >= 66) {
            return 'B-';
        }
        if ($nilai >= 61) {
            return 'C+';
        }
        if ($nilai >= 56) {
            return 'C';
        }
        if ($nilai >= 46) {
            return 'D';
        }

        return 'E';
    }

    private function getPenalizedGrade($grade)
    {
        $grades = ['A', 'A-', 'B+', 'B', 'B-', 'C+', 'C', 'D', 'E'];
        $index = array_search($grade, $grades);
        $newIndex = min($index + 1, count($grades) - 1);

        return $grades[$newIndex];
    }
}
