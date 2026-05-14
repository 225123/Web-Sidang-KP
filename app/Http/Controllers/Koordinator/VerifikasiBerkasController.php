<?php

namespace App\Http\Controllers\Koordinator;

use App\Http\Controllers\Controller;
use App\Models\PendaftaranSidang;
use App\Models\RiwayatPenolakanSidang;
use App\Models\NotifikasiLog;
use Illuminate\Http\Request;

class VerifikasiBerkasController extends Controller
{
    public function index()
    {
        $activePeriodId = session('selected_periode_id') ?? \App\Models\TahunAjaran::aktif()?->id;

        // Ambil semua data sidang yang sudah diajukan ke Koordinator (status_koordinator != 'unsubmitted')
        // Dan hanya ambil yang status_verifikasi (Dosen) = 'verified' karena itu prasyarat
        // Serta filter berdasarkan periode aktif
        $semuaPengajuan = PendaftaranSidang::with(['mahasiswa.user', 'pendaftaranKp'])
            ->whereHas('pendaftaranKp', function($query) use ($activePeriodId) {
                $query->where('tahun_ajaran_id', $activePeriodId);
            })
            ->where('status_verifikasi', 'verified')
            ->where(function($q) {
                // Hanya ambil yang sudah benar-benar diajukan ke koordinator (bukan NULL dan bukan unsubmitted)
                $q->where('status_koordinator', '!=', 'unsubmitted')
                  ->whereNotNull('status_koordinator');
            })
            ->get();

        // 1. Yang ada di tabel utama (Pending, Verified, Rejected) -> Semua yang sudah diajukan
        $pengajuans = $semuaPengajuan->values();

        // 2. Riwayat Penolakan (semua history penolakan) - Filter by period
        $ditolaks = RiwayatPenolakanSidang::with(['mahasiswa.user'])
            ->whereHas('pendaftaranSidang.pendaftaranKp', function($query) use ($activePeriodId) {
                $query->where('tahun_ajaran_id', $activePeriodId);
            })
            ->orderBy('created_at', 'desc')
            ->get();

        // 3. Yang belum submit sama sekali (Tapi sudah ACC dosen) - Filter by period
        // Juga tangkap status_koordinator = NULL (data legacy yang tidak ter-set dengan benar)
        $belumKumpuls = PendaftaranSidang::with(['mahasiswa.user', 'pendaftaranKp'])
            ->whereHas('pendaftaranKp', function($query) use ($activePeriodId) {
                $query->where('tahun_ajaran_id', $activePeriodId);
            })
            ->where('status_verifikasi', 'verified')
            ->where(function($q) {
                $q->where('status_koordinator', 'unsubmitted')
                  ->orWhereNull('status_koordinator');
            })
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

            // --- Kirim Notifikasi Sistem ---
            NotifikasiLog::create([
                'sender_id' => null,
                'receiver_id' => $pengajuan->mahasiswa_id,
                'judul' => "Verifikasi Berkas Sidang: DITOLAK",
                'pesan' => "Berkas pendaftaran sidang Anda ditolak oleh Koordinator. Feedback: " . $request->feedback,
                'target_url' => route('mahasiswa.pendaftaran-sidang.index'),
            ]);
        } elseif ($request->status_koordinator === 'verified') {
            // --- Kirim Notifikasi Sistem ---
            NotifikasiLog::create([
                'sender_id' => null,
                'receiver_id' => $pengajuan->mahasiswa_id,
                'judul' => "Verifikasi Berkas Sidang: DISETUJUI",
                'pesan' => "Berkas pendaftaran sidang Anda telah diverifikasi dan disetujui oleh Koordinator. Tunggu penjadwalan sidang Anda.",
                'target_url' => route('mahasiswa.pendaftaran-sidang.index'),
            ]);
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
            $activePeriodId = session('selected_periode_id') ?? \App\Models\TahunAjaran::aktif()?->id;
            
            $semua = PendaftaranSidang::whereHas('pendaftaranKp', function($query) use ($activePeriodId) {
                    $query->where('tahun_ajaran_id', $activePeriodId);
                })
                ->where('status_verifikasi', 'verified')
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
