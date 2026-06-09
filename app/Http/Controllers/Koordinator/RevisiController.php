<?php

namespace App\Http\Controllers\Koordinator;

use App\Http\Controllers\Controller;
use App\Models\NotifikasiLog;
use App\Models\PendaftaranSidang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RevisiController extends Controller
{
    public function index()
    {
        $activePeriodId = session('selected_periode_id') ?? \App\Models\TahunAjaran::aktif()?->id;
        $activePeriode = \App\Models\TahunAjaran::aktif();
        $isReadOnly = $activePeriode && $activePeriodId != $activePeriode->id;

        $user = Auth::user();
        if (($user->hasRole('dosen') || $user->hasRole('koordinator')) && $user->dosen && !$user->dosen->is_aktif) {
            $isReadOnly = true;
        }

        $sidangs = PendaftaranSidang::with(['mahasiswa.user'])
            ->whereHas('pendaftaranKp', function($q) use ($activePeriodId) {
                if ($activePeriodId) {
                    $q->where('tahun_ajaran_id', $activePeriodId);
                }
            })
            ->where('penguji_1_id', Auth::user()->id)
            ->where('status_kelulusan', 'Lulus Dengan Revisi')
            ->orderBy('id', 'desc')
            ->get();

        return view('koordinator.revisi', compact('sidangs', 'isReadOnly'));
    }

    public function terima(Request $request, $id)
    {
        $dosenId = Auth::user()->id;
        $sidang = PendaftaranSidang::where('id', $id)
            ->where('penguji_1_id', $dosenId)
            ->firstOrFail();

        if ($sidang->status_revisi !== 'Menunggu') {
            return back()->with('error', 'Status revisi belum bisa diproses.');
        }

        $request->validate([
            'n1_laporan' => 'required|numeric|min:0|max:100',
            'n1_produk' => 'required|numeric|min:0|max:100',
            'n1_presentasi' => 'required|numeric|min:0|max:100',
        ]);

        if ($sidang->original_nilai_penguji_1 === null) {
            $sidang->original_n1_laporan = $sidang->n1_laporan;
            $sidang->original_n1_produk = $sidang->n1_produk;
            $sidang->original_n1_presentasi = $sidang->n1_presentasi;
            $sidang->original_nilai_penguji_1 = $sidang->nilai_penguji_1;
        }

        $sidang->n1_laporan = $request->n1_laporan;
        $sidang->n1_produk = $request->n1_produk;
        $sidang->n1_presentasi = $request->n1_presentasi;
        $sidang->nilai_penguji_1 = ($request->n1_laporan * 0.4) + ($request->n1_produk * 0.4) + ($request->n1_presentasi * 0.2);
        
        $sidang->status_revisi = 'Disahkan';
        $sidang->save();

        // Recalculate Final Grade
        $pembimbing = (float) $sidang->nilai_pembimbing * 0.40;
        $supervisor = (float) $sidang->nilai_supervisor * 0.10;
        $penguji1 = (float) $sidang->nilai_penguji_1 * 0.25;
        $penguji2 = (float) $sidang->nilai_penguji_2 * 0.25;
        $avg = $pembimbing + $supervisor + $penguji1 + $penguji2;
        $sidang->nilai_akhir = round($avg, 3);
        
        if ($avg >= 86) {
            $sidang->grade = 'A';
        } elseif ($avg >= 81) {
            $sidang->grade = 'A-';
        } elseif ($avg >= 76) {
            $sidang->grade = 'B+';
        } elseif ($avg >= 71) {
            $sidang->grade = 'B';
        } elseif ($avg >= 66) {
            $sidang->grade = 'B-';
        } elseif ($avg >= 61) {
            $sidang->grade = 'C+';
        } elseif ($avg >= 56) {
            $sidang->grade = 'C';
        } elseif ($avg >= 46) {
            $sidang->grade = 'D';
        } else {
            $sidang->grade = 'E';
        }
        $sidang->save();

        // Notifikasi ke mahasiswa
        NotifikasiLog::create([
            'sender_id' => Auth::user()->id,
            'receiver_id' => $sidang->mahasiswa->user_id,
            'target_role' => 'mahasiswa',
            'judul' => 'Revisi Disahkan',
            'pesan' => 'Berkas revisi sidang Anda telah disahkan oleh koordinator (penguji).',
            'target_url' => '/mahasiswa/revisi',
            'is_read' => false,
        ]);

        return back()->with('success', 'Revisi berhasil DISAHKAN dan nilai telah diperbarui.');
    }

    public function tolak(Request $request, $id)
    {
        $dosenId = Auth::user()->id;
        $sidang = PendaftaranSidang::where('id', $id)
            ->where('penguji_1_id', $dosenId)
            ->firstOrFail();

        if ($sidang->status_revisi !== 'Menunggu') {
            return back()->with('error', 'Status revisi belum bisa diproses.');
        }

        $request->validate([
            'catatan_revisi' => 'required|string',
        ]);

        $sidang->status_revisi = 'Ditolak';
        $sidang->catatan_revisi = $request->catatan_revisi;
        $sidang->save();

        // Notifikasi ke mahasiswa
        NotifikasiLog::create([
            'sender_id' => Auth::user()->id,
            'receiver_id' => $sidang->mahasiswa->user_id,
            'target_role' => 'mahasiswa',
            'judul' => 'Revisi Ditolak',
            'pesan' => "Berkas revisi sidang Anda ditolak.\n\nCatatan: " . $request->catatan_revisi,
            'target_url' => '/mahasiswa/revisi',
            'is_read' => false,
        ]);

        return back()->with('success', 'Revisi mahasiswa berhasil DITOLAK.');
    }
}
