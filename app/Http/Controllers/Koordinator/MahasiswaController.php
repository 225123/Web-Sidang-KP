<?php

namespace App\Http\Controllers\Koordinator;

use App\Http\Controllers\Controller;
use App\Models\Mahasiswa;
use App\Models\PendaftaranKp;
use App\Models\User;
use Illuminate\Http\Request;

class MahasiswaController extends Controller
{
    public function index()
    {
        // Ambil data mahasiswa yang sudah memiliki pendaftaran KP
        $pendaftarans = PendaftaranKp::with(['mahasiswa.user', 'pembimbing', 'supervisorInstansi'])
            ->get()
            ->sortBy(fn($p) => $p->mahasiswa->nim ?? '')
            ->values();

        return view('koordinator.data-mahasiswa', compact('pendaftarans'));
    }
}
