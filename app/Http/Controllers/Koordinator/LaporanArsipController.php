<?php

namespace App\Http\Controllers\Koordinator;

use App\Http\Controllers\Controller;
use App\Models\PendaftaranSidang;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Mahasiswa;
use App\Models\PendaftaranKp;
use Barryvdh\DomPDF\Facade\Pdf;

class LaporanArsipController extends Controller
{
    public function index()
    {
        $periodeId = session('selected_periode_id') ?? \App\Models\TahunAjaran::aktif()?->id;

        // Ambil semua sidang di periode ini (persis seperti Finalisasi Nilai)
        $sidangRows = PendaftaranSidang::withoutGlobalScope('periode')
            ->with(['mahasiswa.user', 'pendaftaranKp'])
            ->whereHas('pendaftaranKp', function($q) use ($periodeId) {
                $q->withoutGlobalScope('periode')->where('tahun_ajaran_id', $periodeId);
            })
            ->whereIn('status_kelulusan', ['Lulus', 'Lulus Dengan Revisi', 'Lanjut', 'Tidak Lulus'])
            ->get();

        // Kumpulkan user_id mahasiswa yang sudah ada data sidangnya
        $mahasiswaDenganSidang = $sidangRows->pluck('mahasiswa_id')->unique()->toArray();

        // Ambil Mahasiswa di periode ini yang TIDAK punya sidang (termasuk yang pindah periode tapi punya draft/riwayat KP di sini)
        $mahasiswaTanpaSidang = Mahasiswa::with('user')
            ->where(function($q) use ($periodeId) {
                $q->where('tahun_ajaran_id', $periodeId)
                  ->orWhereHas('pendaftaranKps', function($sq) use ($periodeId) {
                      $sq->withoutGlobalScope('periode')
                         ->where('tahun_ajaran_id', $periodeId)
                         ->where(function($q2) {
                             $q2->whereNotNull('status_kp')
                                ->orWhereRaw('id = (SELECT MIN(id) FROM pendaftaran_kp AS pkp2 WHERE pkp2.mahasiswa_id = pendaftaran_kp.mahasiswa_id)');
                         });
                  });
            })
            ->whereNotIn('user_id', $mahasiswaDenganSidang)
            ->get();

        $hasil = collect();

        // Tambahkan baris dari data sidang
        foreach ($sidangRows as $sidang) {
            $logic = $this->calculateFinalLogic($sidang);
            $statusKelulusan = $sidang->status_kelulusan === 'Tidak Lulus' ? 'Lanjut' : $sidang->status_kelulusan;

            $ownKp = PendaftaranKp::withoutGlobalScope('periode')
                ->where('mahasiswa_id', $sidang->mahasiswa_id)
                ->where('tahun_ajaran_id', $periodeId)
                ->latest()->first();

            $hasil->push([
                'nim'                    => $sidang->mahasiswa->nim ?? '-',
                'nama'                   => $sidang->mahasiswa->user->name ?? '-',
                'judul_kp'               => $ownKp ? $ownKp->judul_kp : '-',
                'nilai_akhir_display'    => $logic['nilai'],
                'grade_display'          => $logic['grade'],
                'status_kelulusan_display' => $statusKelulusan,
            ]);
        }

        // Tambahkan baris mahasiswa tanpa sidang -> otomatis Lanjut
        foreach ($mahasiswaTanpaSidang as $mhs) {
            $hasil->push([
                'nim'                    => $mhs->nim ?? '-',
                'nama'                   => $mhs->user->name ?? '-',
                'judul_kp'               => '-',
                'nilai_akhir_display'    => 0,
                'grade_display'          => 'E',
                'status_kelulusan_display' => 'Lanjut',
            ]);
        }

        // Urutkan berdasarkan NIM
        $mahasiswas = $hasil->sortBy('nim')->values();

        // Pastikan variabel objek, bukan array assoc, karena di view dipanggil menggunakan panah ->
        $mahasiswas = $mahasiswas->map(function($item) {
            return (object) $item;
        });

        $hasSidangToSah = $sidangRows->where('nilai_dipublikasi', false)->count() > 0;
        $hasValidSidangs = $sidangRows->count() > 0;
        $isAllNilaiDisahkan = !$hasSidangToSah && $hasValidSidangs;

        return view('koordinator.laporan-arsip', compact('mahasiswas', 'isAllNilaiDisahkan'));
    }

    public function downloadPdf()
    {
        $periodeId = session('selected_periode_id') ?? \App\Models\TahunAjaran::aktif()?->id;

        $sidangRows = PendaftaranSidang::withoutGlobalScope('periode')
            ->with(['mahasiswa.user', 'pendaftaranKp'])
            ->whereHas('pendaftaranKp', function($q) use ($periodeId) {
                $q->withoutGlobalScope('periode')->where('tahun_ajaran_id', $periodeId);
            })
            ->whereIn('status_kelulusan', ['Lulus', 'Lulus Dengan Revisi', 'Lanjut', 'Tidak Lulus'])
            ->get();

        $mahasiswaDenganSidang = $sidangRows->pluck('mahasiswa_id')->unique()->toArray();

        $mahasiswaTanpaSidang = Mahasiswa::with('user')
            ->where(function($q) use ($periodeId) {
                $q->where('tahun_ajaran_id', $periodeId)
                  ->orWhereHas('pendaftaranKps', function($sq) use ($periodeId) {
                      $sq->withoutGlobalScope('periode')
                         ->where('tahun_ajaran_id', $periodeId)
                         ->where(function($q2) {
                             $q2->whereNotNull('status_kp')
                                ->orWhereRaw('id = (SELECT MIN(id) FROM pendaftaran_kp AS pkp2 WHERE pkp2.mahasiswa_id = pendaftaran_kp.mahasiswa_id)');
                         });
                  });
            })
            ->whereNotIn('user_id', $mahasiswaDenganSidang)
            ->get();

        $hasil = collect();

        foreach ($sidangRows as $sidang) {
            $logic = $this->calculateFinalLogic($sidang);
            $statusKelulusan = $sidang->status_kelulusan === 'Tidak Lulus' ? 'Lanjut' : $sidang->status_kelulusan;

            $ownKp = PendaftaranKp::withoutGlobalScope('periode')
                ->where('mahasiswa_id', $sidang->mahasiswa_id)
                ->where('tahun_ajaran_id', $periodeId)
                ->latest()->first();

            $hasil->push([
                'nim'                    => $sidang->mahasiswa->nim ?? '-',
                'nama'                   => $sidang->mahasiswa->user->name ?? '-',
                'judul_kp'               => $ownKp ? $ownKp->judul_kp : '-',
                'nilai_akhir_display'    => $logic['nilai'],
                'grade_display'          => $logic['grade'],
                'status_kelulusan_display' => $statusKelulusan,
            ]);
        }

        foreach ($mahasiswaTanpaSidang as $mhs) {
            $hasil->push([
                'nim'                    => $mhs->nim ?? '-',
                'nama'                   => $mhs->user->name ?? '-',
                'judul_kp'               => '-',
                'nilai_akhir_display'    => 0,
                'grade_display'          => 'E',
                'status_kelulusan_display' => 'Lanjut',
            ]);
        }

        $mahasiswas = $hasil->sortBy('nim')->values();
        $mahasiswas = $mahasiswas->map(function($item) {
            return (object) $item;
        });

        $periode = \App\Models\TahunAjaran::aktif();
        $koordinator = null;
        if ($periode && $periode->koordinator_id) {
            $koordinator = \App\Models\User::with('dosen')->find($periode->koordinator_id);
        }
        
        if (!$koordinator && auth()->user()->role == 'koordinator_kp') {
            $koordinator = auth()->user()->load('dosen');
        } elseif (!$koordinator) {
            $koordinator = \App\Models\User::with('dosen')->whereIn('role', [1, 'koordinator_kp'])->first();
        }

        // Encode logo as base64 to ensure it loads in DomPDF on Vercel
        $logoSrc = '';
        if (file_exists(public_path('images/logo.png'))) {
            $logoData = base64_encode(file_get_contents(public_path('images/logo.png')));
            $logoSrc = 'data:image/png;base64,' . $logoData;
        }

        // Encode signature as base64
        $signatureSrc = null;
        if ($koordinator) {
            if ($koordinator->signature && strpos($koordinator->signature, 'data:image') === 0) {
                $signatureSrc = $koordinator->signature;
            } elseif ($koordinator->signature_path) {
                try {
                    $disk = upload_disk();
                    if (\Illuminate\Support\Facades\Storage::disk($disk)->exists($koordinator->signature_path)) {
                        $imgData = \Illuminate\Support\Facades\Storage::disk($disk)->get($koordinator->signature_path);
                        $base64 = base64_encode($imgData);
                        $ext = pathinfo($koordinator->signature_path, PATHINFO_EXTENSION) ?: 'png';
                        $signatureSrc = 'data:image/' . $ext . ';base64,' . $base64;
                    }
                } catch (\Exception $e) {
                    $signatureSrc = null;
                }
            }
        }

        $pdf = Pdf::setOptions(['isRemoteEnabled' => true])
            ->loadView('exports.laporan-arsip-pdf', compact('mahasiswas', 'koordinator', 'logoSrc', 'signatureSrc'))
            ->setPaper('a4', 'portrait');

        return $pdf->download('Laporan_Kelulusan_Mahasiswa.pdf');
    }

    private function calculateFinalLogic($sidang)
    {
        $status = $sidang->status_kelulusan;

        if ($status === 'Lanjut' || $status === 'Tidak Lulus') {
            return ['nilai' => 0, 'grade' => 'E'];
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

        if ($status === 'Lulus Dengan Revisi' && !$revisiVerified) {
            $finalGrade = $this->getPenalizedGrade($originalGrade);
        }

        return ['nilai' => $nilaiFinal, 'grade' => $finalGrade];
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
        return $grades[min($index + 3, count($grades) - 1)];
    }
}
