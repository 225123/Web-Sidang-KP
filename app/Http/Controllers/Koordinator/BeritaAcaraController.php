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

        return view('koordinator.berita-acara', compact(
            'sudahSidang',
            'belumSidang',
            'totalSidang',
            'totalSudah',
            'totalBelum',
            'percentage'
        ));
    }

    public function previewPdf()
    {
        // Load the view into dompdf and stream it directly
        $pdf = Pdf::loadView('koordinator.berita-acara-pdf-template');

        return $pdf->stream('preview_berita_acara.pdf');
    }
}
