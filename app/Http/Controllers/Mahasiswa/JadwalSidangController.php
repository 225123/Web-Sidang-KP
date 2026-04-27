<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use App\Models\PendaftaranSidang;

class JadwalSidangController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        // Cari pendaftaran sidang mahasiswa bersangkutan yang status koordinatornya verified
        $sidang = PendaftaranSidang::with(['pendaftaranKp', 'penguji1', 'penguji2'])
            ->where('mahasiswa_id', $user->id)
            ->where('status_koordinator', 'verified')
            ->first();

        return view('mahasiswa.jadwal-sidang', compact('sidang', 'user'));
    }
}
