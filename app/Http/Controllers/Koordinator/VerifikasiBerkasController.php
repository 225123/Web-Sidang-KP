<?php

namespace App\Http\Controllers\Koordinator;

use App\Http\Controllers\Controller;
use App\Models\PendaftaranSidang;
use App\Models\RiwayatPenolakanSidang;
use Illuminate\Http\Request;

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

        // 1. Yang ada di tabel utama (Pending, Verified, Rejected) -> Semua yang sudah diajukan
        $pengajuans = $semuaPengajuan->values();

        // 2. Riwayat Penolakan (semua history penolakan)
        $ditolaks = RiwayatPenolakanSidang::with(['mahasiswa.user'])
            ->orderBy('created_at', 'desc')
            ->get();

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
            'feedback' => 'nullable|string',
        ]);

        $pengajuan = PendaftaranSidang::findOrFail($id);

        $pengajuan->update([
            'status_koordinator' => $request->status_koordinator,
            'koordinator_feedback' => $request->feedback ?? null,
        ]);

        if ($request->status_koordinator === 'rejected') {
            RiwayatPenolakanSidang::create([
                'pendaftaran_sidang_id' => $pengajuan->id,
                'mahasiswa_id' => $pengajuan->mahasiswa_id,
                'feedback' => $request->feedback,
                'ditolak_oleh' => 'koordinator',
            ]);
        } elseif ($request->status_koordinator === 'verified') {
            // Jika verified dan External, generate token dan kirim email
            $pengajuan->loadMissing('pendaftaranKp.supervisorInstansi');
            
            if ($pengajuan->pendaftaranKp && $pengajuan->pendaftaranKp->jenis_instansi === 'External') {
                $supervisor = $pengajuan->pendaftaranKp->supervisorInstansi;
                
                if ($supervisor && !empty($supervisor->email_supervisor)) {
                    // Generate Unique Token
                    if (empty($pengajuan->token_penilaian_supervisor)) {
                        $token = \Illuminate\Support\Str::random(64);
                        $pengajuan->update([
                            'token_penilaian_supervisor' => $token
                        ]);
                    } else {
                        $token = $pengajuan->token_penilaian_supervisor;
                    }
                    
                    $url_penilaian = url('/penilaian-supervisor/' . $token);
                    
                    try {
                        \Illuminate\Support\Facades\Mail::to($supervisor->email_supervisor)
                            ->send(new \App\Mail\SupervisorPenilaianMail($pengajuan, $url_penilaian));
                    } catch (\Exception $e) {
                        // Log error but don't stop the verification process
                        \Illuminate\Support\Facades\Log::error('Failed to send supervisor email: ' . $e->getMessage());
                    }
                }
            }
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
                ],
            ]);
        }

        $message = $request->status_koordinator == 'verified'
            ? 'Berkas mahasiswa berhasil diverifikasi dan disahkan.'
            : 'Berkas mahasiswa dikembalikan karena ditolak.';

        return back()->with('success', $message);
    }
}
