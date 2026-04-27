<?php

namespace App\Http\Controllers\Koordinator;

use App\Http\Controllers\Controller;
use App\Models\PendaftaranSidang;

class RekapRevisiController extends Controller
{
    public function index()
    {
        // Ambil semua mahasiswa yang berstatus 'Lulus Dengan Revisi'
        $sidangs = PendaftaranSidang::with(['mahasiswa.user'])
            ->where('status_kelulusan', 'Lulus Dengan Revisi')
            ->orderBy('id', 'desc')
            ->get();

        return view('koordinator.rekap-revisi', compact('sidangs'));
    }
}
