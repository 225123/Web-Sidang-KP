<?php

namespace App\Http\Controllers\Koordinator;

use App\Http\Controllers\Controller;
use App\Models\PendaftaranSidang;
use Illuminate\Http\Request;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;

class LaporanArsipController extends Controller
{
    public function index()
    {
        // Ambil data mahasiswa yang sudah dipublikasi nilainya (Hasil Finalisasi)
        $sidangs = PendaftaranSidang::with(['mahasiswa.user', 'pendaftaranKp.pembimbing', 'pendaftaranKp.supervisorInstansi'])
            ->where('nilai_dipublikasi', true)
            ->orderBy('id', 'desc')
            ->get()
            ->map(function ($sidang) {
                $logic = $this->calculateFinalLogic($sidang);
                $sidang->nilai_akhir_display = $logic['nilai'];
                $sidang->grade_display = $logic['grade'];
                return $sidang;
            });

        return view('koordinator.laporan-arsip', compact('sidangs'));
    }

    public function downloadPdf()
    {
        $sidangs = PendaftaranSidang::with(['mahasiswa.user', 'pendaftaranKp.pembimbing', 'pendaftaranKp.supervisorInstansi'])
            ->where('nilai_dipublikasi', true)
            ->orderBy('id', 'desc')
            ->get()
            ->map(function ($sidang) {
                $logic = $this->calculateFinalLogic($sidang);
                $sidang->nilai_akhir_display = $logic['nilai'];
                $sidang->grade_display = $logic['grade'];
                return $sidang;
            });

        $koordinator = User::with('dosen')->whereIn('role', [1, 'koordinator_kp'])->first();

        // Fallback if not found by role (use current user if they are koordinator)
        if (!$koordinator && auth()->user()->role == 'koordinator_kp') {
            $koordinator = auth()->user()->load('dosen');
        }

        $pdf = Pdf::loadView('exports.laporan-arsip-pdf', compact('sidangs', 'koordinator'))
            ->setPaper('a4', 'portrait');

        return $pdf->download('Laporan_Kelulusan_Mahasiswa.pdf');
    }

    private function calculateFinalLogic($sidang)
    {
        $status = $sidang->status_kelulusan;

        if ($status === 'Lanjut' || $status === 'Tidak Lulus') {
            return [
                'nilai' => 0,
                'grade' => 'E',
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

        if ($status === 'Lulus Dengan Revisi' && ! $revisiVerified) {
            $finalGrade = $this->getPenalizedGrade($originalGrade);
        }

        return [
            'nilai' => $nilaiFinal,
            'grade' => $finalGrade,
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
