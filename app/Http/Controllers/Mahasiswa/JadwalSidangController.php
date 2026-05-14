<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use App\Models\PendaftaranSidang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\KirimKalenderMail;

class JadwalSidangController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        $periodeId = session('selected_periode_id') ?? \App\Models\TahunAjaran::aktif()?->id ?? null;

        $query = PendaftaranSidang::with(['pendaftaranKp', 'penguji1', 'penguji2'])
            ->where('mahasiswa_id', $user->id)
            ->where('status_koordinator', 'verified');
            
        if ($periodeId) {
            $query->whereHas('pendaftaranKp', function($q) use ($periodeId) {
                $q->withoutGlobalScope('periode')->where('tahun_ajaran_id', $periodeId);
            });
        }

        $sidang = $query->latest()->first();

        return view('mahasiswa.jadwal-sidang', compact('sidang', 'user'));
    }

    public function kirimEmailKalender(Request $request)
    {
        $user = auth()->user();

        $periodeId = session('selected_periode_id') ?? \App\Models\TahunAjaran::aktif()?->id ?? null;

        $query = PendaftaranSidang::with(['pendaftaranKp', 'penguji1', 'penguji2'])
            ->where('mahasiswa_id', $user->id)
            ->where('status_koordinator', 'verified');
            
        if ($periodeId) {
            $query->whereHas('pendaftaranKp', function($q) use ($periodeId) {
                $q->withoutGlobalScope('periode')->where('tahun_ajaran_id', $periodeId);
            });
        }

        $sidang = $query->latest()->first();

        if (!$sidang || $sidang->status_jadwal !== 'submitted') {
            return response()->json(['success' => false, 'message' => 'Jadwal belum tersedia.'], 400);
        }

        $startDateTime = \Carbon\Carbon::parse($sidang->tanggal_sidang . ' ' . $sidang->waktu_mulai_sidang, 'Asia/Jakarta')->setTimezone('UTC')->format('Ymd\THis\Z');
        $endDateTime = \Carbon\Carbon::parse($sidang->tanggal_sidang . ' ' . $sidang->waktu_selesai_sidang, 'Asia/Jakarta')->setTimezone('UTC')->format('Ymd\THis\Z');
        
        $gcalUrl = "https://calendar.google.com/calendar/render?action=TEMPLATE" . 
                   "&text=" . urlencode("Sidang KP: " . ($sidang->pendaftaranKp->judul_kp ?? '')) .
                   "&dates=" . $startDateTime . "/" . $endDateTime .
                   "&details=" . urlencode("Jadwal Sidang Kerja Praktik\nMahasiswa: " . $user->name . " (" . $user->mahasiswa->nim . ")\nPenguji 1: " . ($sidang->penguji1->name ?? '-') . "\nPenguji 2: " . ($sidang->penguji2->name ?? '-')) .
                   "&location=" . urlencode($sidang->ruang_sidang ?? '');

        try {
            Mail::to($user->email)->send(new KirimKalenderMail($sidang, $gcalUrl));
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}
