<?php

namespace App\Http\Controllers\Koordinator;

use App\Http\Controllers\Controller;
use App\Models\PendaftaranSidang;

class FinalisasiNilaiController extends Controller
{
    public function index()
    {
        // Mendukung status Lulus, Lulus Dengan Revisi, dan Lanjut (pengganti Tidak Lulus)
        $sidangs = PendaftaranSidang::with(['mahasiswa.user', 'pendaftaranKp.pembimbing', 'pendaftaranKp.supervisorInstansi'])
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

                return $sidang;
            });

        return view('koordinator.finalisasi-nilai', compact('sidangs'));
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

        return view('koordinator.finalisasi-nilai-detail', compact('sidang'));
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
        $newIndex = min($index + 3, count($grades) - 1);

        return $grades[$newIndex];
    }
}
