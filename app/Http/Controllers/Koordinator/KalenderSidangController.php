<?php

namespace App\Http\Controllers\Koordinator;

use App\Http\Controllers\Controller;
use App\Models\PendaftaranSidang;

class KalenderSidangController extends Controller
{
    public function index()
    {
        // Ambil semua yang sudah ada jadwalnya
        $periodeId = session('selected_periode_id');
        $sidangs = PendaftaranSidang::with(['mahasiswa.user', 'penguji1', 'penguji2', 'pendaftaranKp.supervisorInternal'])
            ->whereNotNull('tanggal_sidang')
            ->whereHas('pendaftaranKp', function ($q) use ($periodeId) {
                $q->withoutGlobalScope('periode')->where('tahun_ajaran_id', $periodeId);
            })
            ->get();

        $allEvents = $sidangs->map(function ($s) {
            return [
                'id' => $s->id,
                'nama' => strtoupper($s->mahasiswa->user->name ?? 'Mahasiswa'),
                'nim' => $s->mahasiswa->nim ?? '-',
                'penguji1' => $s->penguji1->name ?? '-',
                'penguji2' => $s->penguji2->name ?? '-',
                'penguji' => [
                    $s->penguji1->name ?? '-',
                    $s->penguji2->name ?? '-',
                ],
                'tanggal' => $s->tanggal_sidang,
                'jadwal' => [
                    'tanggal' => date('d/m/Y', strtotime($s->tanggal_sidang)),
                    'waktu' => date('H:i', strtotime($s->waktu_mulai_sidang)).'-'.date('H:i', strtotime($s->waktu_selesai_sidang)),
                    'ruang' => $s->ruang_sidang ?? '-',
                ],
                'waktu_mulai' => $s->waktu_mulai_sidang,
                'ruangan' => $s->ruang_sidang ?? '-',
                'status' => $s->status_jadwal === 'submitted' ? 'Terbit' : 'Terjadwal',
                'status_raw' => $s->status_jadwal,
                'pelaksanaan' => $s->pelaksanaan ?? '-',
                'tanggal_sidang' => $s->tanggal_sidang,
                'waktu_mulai_sidang' => $s->waktu_mulai_sidang,
                'waktu_selesai_sidang' => $s->waktu_selesai_sidang,
            ];
        });

        return view('koordinator.kalender-sidang', [
            'events' => $allEvents,
        ]);
    }
}
