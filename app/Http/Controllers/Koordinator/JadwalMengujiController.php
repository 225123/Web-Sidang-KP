<?php

namespace App\Http\Controllers\Koordinator;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\PendaftaranSidang;

class JadwalMengujiController extends Controller
{
    public function index()
    {
        app()->setLocale('id');
        $userId = Auth::user()->id;

        // Ambil mahasiswa di mana koordinator ini (bertindak sebagai dosen) menjadi:
        // 1. Penguji 1
        // 2. Penguji 2
        // 3. Pembimbing (Supervisor Internal)
        
        $sidangs = PendaftaranSidang::with(['mahasiswa.user', 'pendaftaranKp.supervisorInternal'])
            ->whereNotNull('tanggal_sidang')
            ->where(function($query) use ($userId) {
                $query->where('penguji_1_id', $userId)
                      ->orWhere('penguji_2_id', $userId)
                      ->orWhereHas('pendaftaranKp', function($q) use ($userId) {
                          $q->where('supervisor_internal_id', $userId);
                      });
            })
            ->get();

        return view('koordinator.Jadwal-menguji', compact('sidangs'));
    }
}
