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
        $pendaftarans = PendaftaranKp::with(['mahasiswa.user', 'pembimbing', 'supervisorInstansi'])
            ->orderByRaw("
                CASE 
                    WHEN status_kp = 'approved' THEN 1
                    WHEN status_kp = 'verified' THEN 2
                    WHEN status_kp = 'pending' THEN 3
                    WHEN status_kp IS NULL THEN 4
                    WHEN status_kp = 'rejected' THEN 5
                    ELSE 6
                END
            ")
            ->latest('updated_at')
            ->get()
            ->unique('mahasiswa_id')
            ->filter(function ($p) {
                return in_array($p->status_kp, ['approved', 'pending', 'verified']);
            })
            ->map(function ($p) {
                $anggotaList = [];
                if (!empty($p->anggota_kelompok_ids) && is_array($p->anggota_kelompok_ids)) {
                    $anggotas = Mahasiswa::with('user')->whereIn('user_id', $p->anggota_kelompok_ids)->get();
                    foreach ($anggotas as $mhs) {
                        $anggotaList[] = [
                            'nim' => $mhs->nim,
                            'nama' => $mhs->user->name ?? 'Unknown',
                        ];
                    }
                }
                $p->anggotaLainList = $anggotaList;
                return $p;
            })
            ->sortBy(fn($p) => $p->mahasiswa->nim ?? '')
            ->values();

        return view('koordinator.data-mahasiswa', compact('pendaftarans'));
    }
}
