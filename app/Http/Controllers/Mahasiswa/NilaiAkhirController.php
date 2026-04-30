<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use App\Models\PendaftaranSidang;
use Illuminate\Support\Facades\Auth;

class NilaiAkhirController extends Controller
{
    public function index()
    {
        $userId = Auth::id();

        $sidang = PendaftaranSidang::with(['mahasiswa.user', 'penguji1', 'penguji2', 'pendaftaranKp.pembimbing', 'pendaftaranKp.supervisorInstansi'])
            ->whereHas('mahasiswa', function ($query) use ($userId) {
                $query->where('user_id', $userId);
            })
            ->latest()
            ->first();

        if ($sidang) {
            // Standarisasi status
            if ($sidang->status_kelulusan === 'Tidak Lulus') {
                $sidang->status_kelulusan = 'Lanjut';
            }

            $logic = $this->calculateFinalLogic($sidang);
            $sidang->nilai_akhir_display = $logic['nilai'];
            $sidang->grade_display = $logic['grade'];
            $sidang->original_grade = $logic['original_grade'];
            $sidang->is_penalized = $logic['is_penalized'];
        }

        return view('mahasiswa.nilai-akhir', compact('sidang'));
    }

    public function hasilSidang()
    {
        $userId = Auth::id();

        $sidang = PendaftaranSidang::with(['mahasiswa.user', 'penguji1', 'penguji2'])
            ->whereHas('mahasiswa', function ($query) use ($userId) {
                $query->where('user_id', $userId);
            })
            ->latest()
            ->first();

        if ($sidang) {
            // Standarisasi status
            if ($sidang->status_kelulusan === 'Tidak Lulus') {
                $sidang->status_kelulusan = 'Lanjut';
            }

            $logic = $this->calculateFinalLogic($sidang);
            $sidang->nilai_akhir_display = $logic['nilai'];
            $sidang->grade_display = $logic['grade'];
        }

        return view('mahasiswa.hasil-sidang', compact('sidang'));
    }

    public function downloadNilai()
    {
        $userId = Auth::id();
        $sidang = PendaftaranSidang::with(['mahasiswa.user', 'penguji1', 'penguji2', 'pendaftaranKp.pembimbing', 'pendaftaranKp.supervisorInstansi'])
            ->whereHas('mahasiswa', function ($query) use ($userId) {
                $query->where('user_id', $userId);
            })
            ->latest()
            ->firstOrFail();

        if (!$sidang->nilai_dipublikasi) {
            abort(403, 'Nilai belum dipublikasi oleh koordinator.');
        }

        $logic = $this->calculateFinalLogic($sidang);
        $sidang->nilai_akhir_display = $logic['nilai'];
        $sidang->grade_display = $logic['grade'];
        $sidang->original_grade = $logic['original_grade'];
        $sidang->is_penalized = $logic['is_penalized'];

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('exports.nilai-akhir-pdf', compact('sidang'));
        return $pdf->download('Nilai_Akhir_' . ($sidang->mahasiswa->nim ?? 'Mahasiswa') . '.pdf');
    }

    public function downloadBeritaAcara()
    {
        $userId = Auth::id();
        $sidang = PendaftaranSidang::with(['mahasiswa.user', 'penguji1.dosen', 'penguji2.dosen', 'pendaftaranKp.pembimbing.dosen', 'pendaftaranKp.supervisorInstansi'])
            ->whereHas('mahasiswa', function ($query) use ($userId) {
                $query->where('user_id', $userId);
            })
            ->latest()
            ->firstOrFail();

        if (!$sidang->nilai_dipublikasi || !$sidang->berita_acara_disubmit) {
            abort(403, 'Berita acara belum diterbitkan atau disubmit oleh koordinator.');
        }

        // Get Koordinator (Role 1)
        $koordinator = \App\Models\User::where('role', 1)->first();

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

        $nilaiFinal = (float) $sidang->nilai_akhir;

        if ($nilaiFinal <= 0) {
            $pembimbing = (float) ($sidang->nilai_pembimbing ?? 0) * 0.4;
            $supervisor = (float) ($sidang->nilai_supervisor ?? 0) * 0.1;
            $penguji1 = (float) ($sidang->nilai_penguji_1 ?? 0) * 0.25;
            $penguji2 = (float) ($sidang->nilai_penguji_2 ?? 0) * 0.25;
            $nilaiFinal = $pembimbing + $supervisor + $penguji1 + $penguji2;
        }

        $revisiVerified = ($sidang->status_revisi === 'Disahkan' || $sidang->status_revisi === 'Diterima');
        $originalGrade = $this->getGradeFromScore($nilaiFinal);
        $finalGrade = $originalGrade;
        $isPenalized = false;

        if ($status === 'Lulus Dengan Revisi' && ! $revisiVerified) {
            $finalGrade = $this->getPenalizedGrade($originalGrade);
            $isPenalized = true;
        }

        return [
            'nilai' => $nilaiFinal,
            'grade' => $finalGrade,
            'original_grade' => $originalGrade,
            'is_penalized' => $isPenalized,
        ];
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
        $newIndex = min($index + 3, count($grades) - 1);
        return $grades[$newIndex];
    }
}
