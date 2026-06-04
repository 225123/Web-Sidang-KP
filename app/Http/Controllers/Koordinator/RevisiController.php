<?php

namespace App\Http\Controllers\Koordinator;

use App\Http\Controllers\Controller;
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

        $sidangs = PendaftaranSidang::with(['mahasiswa.user'])
            ->whereHas('pendaftaranKp', function($q) use ($activePeriodId) {
                if ($activePeriodId) {
                    $q->where('tahun_ajaran_id', $activePeriodId);
                }
            })
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

        $sidang->status_revisi = 'Disahkan';
        $sidang->save();

        return back()->with('success', 'Revisi mahasiswa berhasil DISAHKAN.');
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

        if ($sidang->file_revisi) {
            \Illuminate\Support\Facades\Storage::disk(upload_disk())->delete($sidang->file_revisi);
        }

        $sidang->status_revisi = 'Ditolak';
        $sidang->file_revisi = null;
        $sidang->link_revisi = null;
        $sidang->save();

        return back()->with('success', 'Revisi mahasiswa berhasil DITOLAK.');
    }
}
