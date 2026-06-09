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

        $periodeId = session('selected_periode_id') ?? \App\Models\TahunAjaran::aktif()?->id ?? null;

        $query = PendaftaranSidang::with(['mahasiswa.user', 'penguji1', 'penguji2', 'pendaftaranKp.pembimbing', 'pendaftaranKp.supervisorInstansi'])
            ->whereHas('mahasiswa', function ($query) use ($userId) {
                $query->where('user_id', $userId);
            });
            
        if ($periodeId) {
            $query->whereHas('pendaftaranKp', function($q) use ($periodeId) {
                $q->withoutGlobalScope('periode')->where('tahun_ajaran_id', $periodeId);
            });
        }

        $isPastPeriod = $periodeId && $periodeId != \App\Models\TahunAjaran::aktif()?->id;
        $isFinalized = $isPastPeriod || PendaftaranSidang::whereHas('pendaftaranKp', function($q) use ($periodeId) {
            $q->withoutGlobalScope('periode')->where('tahun_ajaran_id', $periodeId);
        })->where('nilai_dipublikasi', true)->exists();

        $sidang = $query->latest()->first();

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
        } elseif ($isFinalized) {
            // Jika sudah difinalisasi namun mahasiswa tidak mendaftar sidang, berikan nilai 0 otomatis
            $mhs = \App\Models\Mahasiswa::with('user')->where('user_id', $userId)->first();
            $kp = \App\Models\PendaftaranKp::withoutGlobalScope('periode')
                ->where('mahasiswa_id', $userId)
                ->where('tahun_ajaran_id', $periodeId)
                ->latest()
                ->first();

            $sidang = new PendaftaranSidang([
                'mahasiswa_id' => $userId,
                'pendaftaran_kp_id' => $kp ? $kp->id : null,
                'status_kelulusan' => 'Lanjut',
                'nilai_dipublikasi' => true,
                'nilai_pembimbing' => 0,
                'nilai_supervisor' => 0,
                'nilai_penguji_1' => 0,
                'nilai_penguji_2' => 0,
                'nilai_akhir' => 0,
                'n1_laporan' => 0,
                'n1_produk' => 0,
                'n1_presentasi' => 0,
                'n2_laporan' => 0,
                'n2_produk' => 0,
                'n2_presentasi' => 0,
                'nb_laporan' => 0,
                'nb_produk' => 0,
                'nb_sikap' => 0,
                'ns_motivasi' => 0,
                'ns_kualitas' => 0,
                'ns_inisiatif' => 0,
                'ns_sikap' => 0,
            ]);

            $sidang->setRelation('mahasiswa', $mhs);
            if ($kp) {
                $sidang->setRelation('pendaftaranKp', $kp->load('pembimbing', 'supervisorInstansi'));
            }

            $sidang->nilai_akhir_display = 0;
            $sidang->grade_display = 'E';
            $sidang->original_grade = 'E';
            $sidang->is_penalized = false;
        }

        return view('mahasiswa.nilai-akhir', compact('sidang', 'isPastPeriod'));
    }

    public function hasilSidang()
    {
        $userId = Auth::id();

        $periodeId = session('selected_periode_id') ?? \App\Models\TahunAjaran::aktif()?->id ?? null;

        $query = PendaftaranSidang::with(['mahasiswa.user', 'penguji1', 'penguji2'])
            ->whereHas('mahasiswa', function ($query) use ($userId) {
                $query->where('user_id', $userId);
            });
            
        if ($periodeId) {
            $query->whereHas('pendaftaranKp', function($q) use ($periodeId) {
                $q->withoutGlobalScope('periode')->where('tahun_ajaran_id', $periodeId);
            });
        }

        $isPastPeriod = $periodeId && $periodeId != \App\Models\TahunAjaran::aktif()?->id;
        $isFinalized = $isPastPeriod || PendaftaranSidang::whereHas('pendaftaranKp', function($q) use ($periodeId) {
            $q->withoutGlobalScope('periode')->where('tahun_ajaran_id', $periodeId);
        })->where('nilai_dipublikasi', true)->exists();

        $sidang = $query->latest()->first();

        if ($sidang) {
            // Standarisasi status
            if ($sidang->status_kelulusan === 'Tidak Lulus') {
                $sidang->status_kelulusan = 'Lanjut';
            }

            $logic = $this->calculateFinalLogic($sidang);
            $sidang->nilai_akhir_display = $logic['nilai'];
            $sidang->grade_display = $logic['grade'];
        } elseif ($isFinalized) {
            $mhs = \App\Models\Mahasiswa::with('user')->where('user_id', $userId)->first();
            $kp = \App\Models\PendaftaranKp::withoutGlobalScope('periode')
                ->where('mahasiswa_id', $userId)
                ->where('tahun_ajaran_id', $periodeId)
                ->latest()
                ->first();

            $sidang = new PendaftaranSidang([
                'mahasiswa_id' => $userId,
                'pendaftaran_kp_id' => $kp ? $kp->id : null,
                'status_kelulusan' => 'Lanjut',
                'nilai_dipublikasi' => true,
                'nilai_pembimbing' => 0,
                'nilai_supervisor' => 0,
                'nilai_penguji_1' => 0,
                'nilai_penguji_2' => 0,
                'nilai_akhir' => 0,
                'n1_laporan' => 0,
                'n1_produk' => 0,
                'n1_presentasi' => 0,
                'n2_laporan' => 0,
                'n2_produk' => 0,
                'n2_presentasi' => 0,
            ]);

            $sidang->setRelation('mahasiswa', $mhs);
            if ($kp) {
                $sidang->setRelation('pendaftaranKp', $kp->load('pembimbing', 'supervisorInstansi'));
            }

            $sidang->nilai_akhir_display = 0;
            $sidang->grade_display = 'E';
        }

        return view('mahasiswa.hasil-sidang', compact('sidang', 'isPastPeriod'));
    }

    public function downloadNilai()
    {
        $userId = Auth::id();
        $periodeId = session('selected_periode_id') ?? \App\Models\TahunAjaran::aktif()?->id ?? null;

        $query = PendaftaranSidang::with(['mahasiswa.user', 'penguji1', 'penguji2', 'pendaftaranKp.pembimbing', 'pendaftaranKp.supervisorInstansi'])
            ->whereHas('mahasiswa', function ($query) use ($userId) {
                $query->where('user_id', $userId);
            });
            
        if ($periodeId) {
            $query->whereHas('pendaftaranKp', function($q) use ($periodeId) {
                $q->withoutGlobalScope('periode')->where('tahun_ajaran_id', $periodeId);
            });
        }

        $sidang = $query->latest()->firstOrFail();

        if (!$sidang->nilai_dipublikasi) {
            abort(403, 'Nilai belum dipublikasi oleh koordinator.');
        }

        $logic = $this->calculateFinalLogic($sidang);
        $sidang->nilai_akhir_display = $logic['nilai'];
        $sidang->grade_display = $logic['grade'];
        $sidang->original_grade = $logic['original_grade'];
        $sidang->is_penalized = $logic['is_penalized'];

        $ownKp = \App\Models\PendaftaranKp::withoutGlobalScope('periode')
            ->where('mahasiswa_id', $sidang->mahasiswa_id)
            ->whereIn('status_kp', ['pending', 'approved'])
            ->latest()
            ->first();
        $sidang->judul_kp_display = $ownKp ? $ownKp->judul_kp : ($sidang->pendaftaranKp->judul_kp ?? '-');

        // Encode logo as base64 to ensure it loads in DomPDF on Vercel
        $logoSrc = '';
        if (file_exists(public_path('images/logo.png'))) {
            $logoData = base64_encode(file_get_contents(public_path('images/logo.png')));
            $logoSrc = 'data:image/png;base64,' . $logoData;
        }

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::setOptions(['isRemoteEnabled' => true])
            ->loadView('exports.nilai-akhir-pdf', compact('sidang', 'logoSrc'));
        return $pdf->download('Nilai_Akhir_' . ($sidang->mahasiswa->nim ?? 'Mahasiswa') . '.pdf');
    }

    public function downloadBeritaAcara()
    {
        $userId = Auth::id();
        $periodeId = session('selected_periode_id') ?? \App\Models\TahunAjaran::aktif()?->id ?? null;

        $query = PendaftaranSidang::with(['mahasiswa.user', 'penguji1.dosen', 'penguji2.dosen', 'pendaftaranKp.pembimbing.dosen', 'pendaftaranKp.supervisorInstansi'])
            ->whereHas('mahasiswa', function ($query) use ($userId) {
                $query->where('user_id', $userId);
            });
            
        if ($periodeId) {
            $query->whereHas('pendaftaranKp', function($q) use ($periodeId) {
                $q->withoutGlobalScope('periode')->where('tahun_ajaran_id', $periodeId);
            });
        }

        $sidang = $query->latest()->firstOrFail();

        if (!$sidang->nilai_dipublikasi || $sidang->pelaksanaan !== 'Selesai') {
            abort(403, 'Berita acara belum tersedia. Pastikan sidang telah selesai dan nilai telah dipublikasi oleh koordinator.');
        }

        $ownKp = \App\Models\PendaftaranKp::withoutGlobalScope('periode')
            ->where('mahasiswa_id', $sidang->mahasiswa_id)
            ->whereIn('status_kp', ['pending', 'approved'])
            ->latest()
            ->first();
        $sidang->judul_kp_display = $ownKp ? $ownKp->judul_kp : ($sidang->pendaftaranKp->judul_kp ?? '-');

        // Get Koordinator
        $koordinator = \App\Models\User::where('role', 'koordinator_kp')->with('dosen')->first();

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
                'sidang_score' => 0,
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
            'nilai' => $nilaiFinal,
            'sidang_score' => $sidangScore,
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
        $newIndex = min($index + 1, count($grades) - 1);
        return $grades[$newIndex];
    }
}
