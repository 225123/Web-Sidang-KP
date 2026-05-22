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
        $periodeId = session('selected_periode_id') ?? \App\Models\TahunAjaran::aktif()?->id;

        // Cek apakah finalisasi sudah pernah dilakukan di periode ini
        $isFinalized = PendaftaranSidang::whereHas('pendaftaranKp', function($q) use ($periodeId) {
            $q->withoutGlobalScope('periode')->where('tahun_ajaran_id', $periodeId);
        })->where('nilai_dipublikasi', true)->exists();

        // Ambil semua mahasiswa di periode tersebut
        $mahasiswas = \App\Models\Mahasiswa::with(['user'])
            ->where('tahun_ajaran_id', $periodeId)
            ->get()
            ->map(function ($mhs) use ($isFinalized, $periodeId) {
                // Cari pendaftaran Sidang yang nilainya sudah dipublikasi untuk mahasiswa ini di periode tersebut
                $sidang = PendaftaranSidang::whereHas('pendaftaranKp', function($q) use ($mhs, $periodeId) {
                        $q->withoutGlobalScope('periode')
                          ->where('mahasiswa_id', $mhs->user_id)
                          ->where('tahun_ajaran_id', $periodeId);
                    })
                    ->latest()
                    ->first();

                // Cari pendaftaran KP yang terkait dengan sidang tersebut, atau KP terbaru jika tidak ada sidang
                $kp = $sidang 
                    ? \App\Models\PendaftaranKp::withoutGlobalScope('periode')->find($sidang->pendaftaran_kp_id)
                    : \App\Models\PendaftaranKp::withoutGlobalScope('periode')
                        ->where('mahasiswa_id', $mhs->user_id)
                        ->where('tahun_ajaran_id', $periodeId)
                        ->latest()
                        ->first();

                $mhs->judul_kp_display = $kp ? $kp->judul_kp : '-';
                $mhs->instansi_display = $kp ? $kp->instansi_nama : '-';

                // Jika sudah punya nilai yang dipublikasi
                if ($sidang && $sidang->nilai_dipublikasi && $mhs->is_aktif) {
                    $logic = $this->calculateFinalLogic($sidang);
                    $mhs->nilai_akhir_display = $logic['nilai'];
                    $mhs->grade_display = $logic['grade'];
                    // Standarisasi Tidak Lulus menjadi Lanjut
                    $mhs->status_kelulusan_display = $sidang->status_kelulusan === 'Tidak Lulus' ? 'Lanjut' : $sidang->status_kelulusan;
                } else {
                    // Jika belum punya nilai, atau sidang belum selesai, atau tidak aktif
                    if ($isFinalized) {
                        $mhs->nilai_akhir_display = 0;
                        $mhs->grade_display = 'E';
                        $mhs->status_kelulusan_display = 'Lanjut';
                    } else {
                        $mhs->nilai_akhir_display = '-';
                        $mhs->grade_display = '-';
                        $mhs->status_kelulusan_display = 'Belum Finalisasi';
                    }
                }

                return $mhs;
            })
            ->sortBy('nim')
            ->values();

        return view('koordinator.laporan-arsip', compact('mahasiswas', 'isFinalized'));
    }

    public function downloadPdf()
    {
        $periodeId = session('selected_periode_id') ?? \App\Models\TahunAjaran::aktif()?->id;

        $isFinalized = PendaftaranSidang::whereHas('pendaftaranKp', function($q) use ($periodeId) {
            $q->withoutGlobalScope('periode')->where('tahun_ajaran_id', $periodeId);
        })->where('nilai_dipublikasi', true)->exists();

        $mahasiswas = \App\Models\Mahasiswa::with(['user'])
            ->where('tahun_ajaran_id', $periodeId)
            ->get()
            ->map(function ($mhs) use ($isFinalized, $periodeId) {
                $sidang = PendaftaranSidang::whereHas('pendaftaranKp', function($q) use ($mhs, $periodeId) {
                        $q->withoutGlobalScope('periode')
                          ->where('mahasiswa_id', $mhs->user_id)
                          ->where('tahun_ajaran_id', $periodeId);
                    })
                    ->latest()
                    ->first();

                $kp = $sidang 
                    ? \App\Models\PendaftaranKp::withoutGlobalScope('periode')->find($sidang->pendaftaran_kp_id)
                    : \App\Models\PendaftaranKp::withoutGlobalScope('periode')
                        ->where('mahasiswa_id', $mhs->user_id)
                        ->where('tahun_ajaran_id', $periodeId)
                        ->latest()
                        ->first();

                $mhs->judul_kp_display = $kp ? $kp->judul_kp : '-';
                $mhs->instansi_display = $kp ? $kp->instansi_nama : '-';

                if ($sidang && $sidang->nilai_dipublikasi && $mhs->is_aktif) {
                    $logic = $this->calculateFinalLogic($sidang);
                    $mhs->nilai_akhir_display = $logic['nilai'];
                    $mhs->grade_display = $logic['grade'];
                    $mhs->status_kelulusan_display = $sidang->status_kelulusan === 'Tidak Lulus' ? 'Lanjut' : $sidang->status_kelulusan;
                } else {
                    if ($isFinalized) {
                        $mhs->nilai_akhir_display = 0;
                        $mhs->grade_display = 'E';
                        $mhs->status_kelulusan_display = 'Lanjut';
                    } else {
                        $mhs->nilai_akhir_display = '-';
                        $mhs->grade_display = '-';
                        $mhs->status_kelulusan_display = 'Belum Finalisasi';
                    }
                }

                return $mhs;
            })
            ->sortBy('nim')
            ->values();

        $koordinator = User::with('dosen')->whereIn('role', [1, 'koordinator_kp'])->first();

        if (!$koordinator && auth()->user()->role == 'koordinator_kp') {
            $koordinator = auth()->user()->load('dosen');
        }

        $pdf = Pdf::loadView('exports.laporan-arsip-pdf', compact('mahasiswas', 'koordinator'))
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
