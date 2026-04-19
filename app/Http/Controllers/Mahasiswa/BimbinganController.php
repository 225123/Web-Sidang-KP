<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PendaftaranKp;
use App\Models\LogBimbingan;
use Illuminate\Support\Facades\Auth;

class BimbinganController extends Controller
{
    public function index(Request $request)
    {
        $userId = Auth::id();
        $pendaftaran = PendaftaranKp::where('mahasiswa_id', $userId)
            ->whereNotIn('status_kp', ['rejected', 'pending'])
            ->first();

        if (!$pendaftaran) {
            $pendaftaran = PendaftaranKp::where('mahasiswa_id', $userId)->latest()->first();
        }

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
            'logs' => $logs
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'tanggal' => 'required|date',
            'detail' => 'required|string',
            'bukti' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        $userId = Auth::id();
        $pendaftaran = PendaftaranKp::where('mahasiswa_id', $userId)->latest()->first();

        if (!$pendaftaran) {
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
            $filePath = $request->file('bukti')->store('log_bimbingan_bukti', 'public');
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
        return redirect()->route('mahasiswa.bimbingan-dosen')->with('success', 'Bimbingan berhasil ditambahkan.');
    }

    public function exportPdf()
    {
        $userId = Auth::id();
        $pendaftaran = PendaftaranKp::with(['pembimbing.dosen', 'user.mahasiswa'])
            ->where('mahasiswa_id', $userId)
            ->whereNotIn('status_kp', ['rejected', 'pending'])
            ->first();

        if (!$pendaftaran) {
            $pendaftaran = PendaftaranKp::with(['pembimbing.dosen', 'user.mahasiswa'])
                ->where('mahasiswa_id', $userId)->latest()->first();
        }

        if (!$pendaftaran) {
            return back()->with('error', 'Pendaftaran KP tidak ditemukan.');
        }

        // Ambil bimbingan HANYA yang 'approved' (disetujui pembimbing)
        $logs = LogBimbingan::where('pendaftaran_kp_id', $pendaftaran->id)
            ->where('mahasiswa_id', $userId)
            ->where('status_approval', 'approved')
            ->orderBy('tanggal', 'asc')
            ->get();

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.bimbingan_log', [
            'pendaftaran' => $pendaftaran,
            'logs' => $logs
        ]);

        return $pdf->download('Log_Bimbingan_'.($pendaftaran->user->mahasiswa->nim ?? 'MHS').'.pdf');
    }
}
