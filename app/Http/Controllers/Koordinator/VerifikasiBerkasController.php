<?php

namespace App\Http\Controllers\Koordinator;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PendaftaranSidang;
use App\Models\RiwayatPenolakanSidang;

class VerifikasiBerkasController extends Controller
{
    public function index()
    {
        // Ambil semua data sidang yang sudah diajukan ke Koordinator (status_koordinator != 'unsubmitted')
        // Dan hanya ambil yang status_verifikasi (Dosen) = 'verified' karena itu prasyarat
        $semuaPengajuan = PendaftaranSidang::with(['mahasiswa.user', 'pendaftaranKp'])
            ->where('status_verifikasi', 'verified')
            ->where('status_koordinator', '!=', 'unsubmitted')
            ->get();

        // 1. Yang ada di tabel utama (Pending, Verified)
        $pengajuans = $semuaPengajuan->whereIn('status_koordinator', ['pending', 'verified'])->values();
        
        // 2. Yang ada di tabel bawah (Ditolak) -> Sekarang dari tabel Riwayat independen
        $ditolaks = RiwayatPenolakanSidang::with(['pendaftaranSidang.mahasiswa.user', 'pendaftaranSidang.pendaftaranKp', 'mahasiswa.user'])->latest()->get();

        // 3. Yang belum submit sama sekali (Tapi sudah ACC dosen)
        $belumKumpuls = PendaftaranSidang::with(['mahasiswa.user', 'pendaftaranKp'])
            ->where('status_verifikasi', 'verified')
            ->where('status_koordinator', 'unsubmitted')
            ->get();

        // Rekap widget
        $statDisahkan = $semuaPengajuan->where('status_koordinator', 'verified')->count();
        $statBelum = $semuaPengajuan->where('status_koordinator', 'pending')->count();
        $statDitolak = $semuaPengajuan->where('status_koordinator', 'rejected')->count();

        return view('koordinator.verifikasi-berkas-sidang', compact(
            'pengajuans', 'ditolaks', 'belumKumpuls',
            'statDisahkan', 'statBelum', 'statDitolak'
        ));
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status_koordinator' => 'required|in:verified,rejected',
            'feedback' => 'nullable|string'
        ]);

        $pengajuan = PendaftaranSidang::findOrFail($id);

        $pengajuan->update([
            'status_koordinator' => $request->status_koordinator,
            'koordinator_feedback' => $request->feedback ?? null
        ]);

        if ($request->status_koordinator === 'rejected') {
            RiwayatPenolakanSidang::create([
                'pendaftaran_sidang_id' => $pengajuan->id,
                'mahasiswa_id' => $pengajuan->mahasiswa_id,
                'feedback' => $request->feedback,
                'ditolak_oleh' => 'koordinator'
            ]);
        }

        if ($request->ajax()) {
            // Re-calculate stats for real-time update
            $semua = PendaftaranSidang::where('status_verifikasi', 'verified')
                ->where('status_koordinator', '!=', 'unsubmitted')
                ->get();
            
            return response()->json([
                'success' => true,
                'status' => $request->status_koordinator,
                'feedback' => $request->feedback,
                'stats' => [
                    'disahkan' => $semua->where('status_koordinator', 'verified')->count(),
                    'belum' => $semua->where('status_koordinator', 'pending')->count(),
                    'ditolak' => $semua->where('status_koordinator', 'rejected')->count(),
                ]
            ]);
        }

        $message = $request->status_koordinator == 'verified' 
            ? 'Berkas mahasiswa berhasil diverifikasi dan disahkan.'
            : 'Berkas mahasiswa dikembalikan karena ditolak.';

        return back()->with('success', $message);
    }
}
