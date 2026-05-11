<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use App\Models\LogBimbingan;
use App\Models\NotifikasiLog;
use App\Models\PendaftaranKp;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BimbinganController extends Controller
{
    public function index(Request $request)
    {
        $periodeId = session('selected_periode_id') ?? \App\Models\TahunAjaran::aktif()->id ?? null;

        $userId = Auth::id();
        $query = PendaftaranKp::withoutGlobalScope('periode')
            ->where(function ($query) use ($userId) {
                $query->where('mahasiswa_id', $userId)
                      ->orWhereJsonContains('anggota_kelompok_ids', $userId)
                      ->orWhereJsonContains('anggota_kelompok_ids', (string) $userId);
            });
            
        if ($periodeId) {
            $query->where('tahun_ajaran_id', $periodeId);
        }

        $pendaftaran = (clone $query)->orderByRaw("
            CASE 
                WHEN status_kp = 'approved' THEN 1
                WHEN status_kp = 'verified' THEN 2
                WHEN status_kp = 'pending' THEN 3
                WHEN status_kp IS NULL THEN 4
                WHEN status_kp = 'rejected' THEN 5
                ELSE 6
            END
        ")->latest()->first();

        $logs = [];
        if ($pendaftaran) {
            // PERBAIKAN: Hanya ambil log milik SAYA (userId), bukan satu kelompok
            $logs = LogBimbingan::where('pendaftaran_kp_id', $pendaftaran->id)
                ->where('mahasiswa_id', $userId)
                ->orderBy('tanggal', 'desc')
                ->get();
        }

        return view('mahasiswa.bimbingan-dosen', [
            'active' => 'bimbingan-dosen',
            'pendaftaran' => $pendaftaran,
            'logs' => $logs,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'tanggal' => 'required|date',
            'detail' => 'required|string',
            'bukti' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $periodeId = session('selected_periode_id') ?? \App\Models\TahunAjaran::aktif()->id ?? null;

        $userId = Auth::id();
        $query = PendaftaranKp::withoutGlobalScope('periode')
            ->where(function ($query) use ($userId) {
                $query->where('mahasiswa_id', $userId)
                      ->orWhereJsonContains('anggota_kelompok_ids', $userId)
                      ->orWhereJsonContains('anggota_kelompok_ids', (string) $userId);
            });
            
        if ($periodeId) {
            $query->where('tahun_ajaran_id', $periodeId);
        }

        $pendaftaran = (clone $query)->whereNotIn('status_kp', ['rejected', 'pending'])->latest()->first();

        if (! $pendaftaran) {
            $pendaftaran = $query->latest()->first();
        }

        if (! $pendaftaran) {
            return back()->with('error', 'Pendaftaran KP tidak ditemukan.');
        }

        $materiBahasan = [
            'waktuMulai' => $request->waktuMulai,
            'waktuSelesai' => $request->waktuSelesai,
            'tempat' => $request->tempat,
            'topik' => $request->topik,
            'detail' => $request->detail,
        ];

        $filePath = null;
        if ($request->hasFile('bukti')) {
            $filePath = $request->file('bukti')->store('log_bimbingan_bukti', upload_disk());
        }

        LogBimbingan::create([
            'pendaftaran_kp_id' => $pendaftaran->id,
            'mahasiswa_id' => $userId, // Pastikan kolom ini ada di database!
            'tanggal' => $request->tanggal,
            'materi_bahasan' => json_encode([
                'waktuMulai' => $request->waktuMulai,
                'waktuSelesai' => $request->waktuSelesai,
                'tempat' => $request->tempat,
                'topik' => $request->topik,
                'detail' => $request->detail,
            ]),
            'file_progress' => $filePath,
            'status_approval' => 'pending',
            'is_supervisor' => false,
        ]);

        // Notifikasi ke Pembimbing
        if ($pendaftaran->pembimbing_id) {
            NotifikasiLog::create([
                'sender_id' => null, // Sistem
                'receiver_id' => $pendaftaran->pembimbing_id,
                'judul' => 'Update Bimbingan Mahasiswa',
                'pesan' => auth()->user()->name . ' (' . (auth()->user()->mahasiswa->nim ?? '-') . ') telah menginput log bimbingan baru.',
                'target_url' => route('dosen.daftar-mahasiswa.detail', $pendaftaran->id),
            ]);
        }



        return redirect()->route('mahasiswa.bimbingan-dosen')->with('success', 'Bimbingan berhasil ditambahkan.');
    }

    public function exportPdf()
    {
        $periodeId = session('selected_periode_id') ?? \App\Models\TahunAjaran::aktif()->id ?? null;

        $userId = Auth::id();
        $query = PendaftaranKp::withoutGlobalScope('periode')
            ->with(['pembimbing.dosen', 'user.mahasiswa'])
            ->where(function ($query) use ($userId) {
                $query->where('mahasiswa_id', $userId)
                      ->orWhereJsonContains('anggota_kelompok_ids', $userId)
                      ->orWhereJsonContains('anggota_kelompok_ids', (string) $userId);
            });
            
        if ($periodeId) {
            $query->where('tahun_ajaran_id', $periodeId);
        }

        $pendaftaran = (clone $query)->whereNotIn('status_kp', ['rejected', 'pending'])->latest()->first();

        if (! $pendaftaran) {
            $pendaftaran = $query->latest()->first();
        }

        if (! $pendaftaran) {
            return back()->with('error', 'Pendaftaran KP tidak ditemukan.');
        }

        // Ambil bimbingan HANYA yang 'approved' (disetujui pembimbing)
        $logs = LogBimbingan::where('pendaftaran_kp_id', $pendaftaran->id)
            ->where('mahasiswa_id', $userId)
            ->where('status_approval', 'approved')
            ->orderBy('tanggal', 'asc')
            ->get();

        $pdf = Pdf::loadView('pdf.bimbingan_log', [
            'pendaftaran' => $pendaftaran,
            'logs' => $logs,
        ]);

        return $pdf->download('Log_Bimbingan_'.($pendaftaran->user->mahasiswa->nim ?? 'MHS').'.pdf');
    }
}
