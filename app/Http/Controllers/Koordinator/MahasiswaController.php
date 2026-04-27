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
            ->latest()
            ->get();

        return view('koordinator.data-mahasiswa', compact('pendaftarans'));
    }

    public function show($id)
    {
        $pendaftaran = PendaftaranKp::with(['mahasiswa.user', 'pembimbing', 'supervisorInstansi', 'logBimbingans'])
            ->findOrFail($id);

        $jumlahDiterima = $pendaftaran->logBimbingans->where('status_approval', 'approved')->count();
        $jumlahBelumDiperiksa = $pendaftaran->logBimbingans->where('status_approval', 'pending')->count();
        $jumlahDitolak = $pendaftaran->logBimbingans->where('status_approval', 'rejected')->count();

        return view('koordinator.data-mahasiswa-detail', compact('pendaftaran', 'jumlahDiterima', 'jumlahBelumDiperiksa', 'jumlahDitolak'));
    }
}
