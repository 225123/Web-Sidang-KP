<?php

namespace App\Http\Controllers\Koordinator;

use App\Http\Controllers\Controller;
use App\Models\PendaftaranSidang;
use Barryvdh\DomPDF\Facade\Pdf;

class BeritaAcaraController extends Controller
{
    public function index()
    {
        // Ambil semua sidang yang memiliki jadwal
        $sidangs = PendaftaranSidang::with(['mahasiswa.user', 'penguji1', 'penguji2'])->whereNotNull('tanggal_sidang')->get();

        $sudahSidang = [];
        $belumSidang = [];

        foreach ($sidangs as $sidang) {
            $mhsData = [
                'id' => $sidang->id,
                'name' => strtoupper($sidang->mahasiswa->user->name ?? 'MAHASISWA'),
                'nim' => $sidang->mahasiswa->nim ?? '-',
                'jadwal' => ($sidang->tanggal_sidang ? date('d/m/Y', strtotime($sidang->tanggal_sidang)) : '-').' '.
                            ($sidang->waktu_mulai_sidang ? date('H:i', strtotime($sidang->waktu_mulai_sidang)).'-'.date('H:i', strtotime($sidang->waktu_selesai_sidang)) : '-'),
                'ruang' => $sidang->ruang_sidang ?? '-',
                'p1' => $sidang->penguji1->name ?? 'Belum Diplot',
                'p2' => $sidang->penguji2->name ?? 'Belum Diplot',
            ];

            if ($sidang->pelaksanaan === 'Selesai') {
                $sudahSidang[] = $mhsData;
            } else {
                $belumSidang[] = $mhsData;
            }
        }

        $totalSidang = count($sidangs);
        $totalSudah = count($sudahSidang);
        $totalBelum = count($belumSidang);

        $percentage = $totalSidang > 0 ? round(($totalSudah / $totalSidang) * 100) : 0;

        $hasSelesai = count($sudahSidang) > 0;
        $isAllSubmitted = false;
        if ($hasSelesai) {
            $unsubmittedCount = PendaftaranSidang::where('pelaksanaan', 'Selesai')
                ->where('berita_acara_disubmit', false)
                ->count();
            $isAllSubmitted = $unsubmittedCount === 0;
        }

        return view('koordinator.berita-acara', compact(
            'sudahSidang',
            'belumSidang',
            'totalSidang',
            'totalSudah',
            'totalBelum',
            'percentage',
            'isAllSubmitted',
            'hasSelesai'
        ));
    }

    public function previewPdf()
    {
        // Get an example sidang for preview
        $sidang = PendaftaranSidang::with(['mahasiswa.user', 'pendaftaranKp', 'penguji1.dosen', 'penguji2.dosen'])
            ->whereNotNull('tanggal_sidang')
            ->first();

        // If no sidang exists, we use a dummy object to prevent errors
        if (!$sidang) {
            $sidang = new PendaftaranSidang([
                'tanggal_sidang' => now(),
                'waktu_mulai_sidang' => '08:00',
                'ruang_sidang' => 'Ruang Preview',
            ]);
        }

        $koordinator = \App\Models\User::with('dosen')->where('role', 'koordinator')->first();

        // Load the view into dompdf and stream it directly
        $pdf = Pdf::loadView('koordinator.berita-acara-pdf-template', compact('sidang', 'koordinator'));

        return $pdf->stream('preview_berita_acara.pdf');
    }

    public function submit()
    {
        PendaftaranSidang::where('pelaksanaan', 'Selesai')
            ->update(['berita_acara_disubmit' => true]);

        return back()->with('success', 'Berita Acara berhasil disubmit dan dikunci untuk mahasiswa yang telah selesai sidang.');
    }
}
