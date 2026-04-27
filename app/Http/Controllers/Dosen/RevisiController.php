<?php

namespace App\Http\Controllers\Dosen;

use App\Http\Controllers\Controller;
use App\Models\PendaftaranSidang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RevisiController extends Controller
{
    public function index()
    {
        $dosenId = Auth::user()->id;

        // Ambil mahasiswa yang sidang dan dosen ini adalah Penguji 1, dan status_kelulusan = 'Lulus Dengan Revisi'
        $sidangs = PendaftaranSidang::with(['mahasiswa.user'])
            ->where('penguji_1_id', $dosenId)
            ->where('status_kelulusan', 'Lulus Dengan Revisi')
            ->orderBy('id', 'desc')
            ->get();

        return view('dosen.revisi', compact('sidangs'));
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

        $sidang->status_revisi = 'Ditolak';
        $sidang->save();

        return back()->with('success', 'Revisi mahasiswa berhasil DITOLAK.');
    }
}
