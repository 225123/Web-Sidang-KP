<?php

namespace App\Http\Controllers\Dosen;

use App\Http\Controllers\Controller;
use App\Models\PendaftaranSidang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class JadwalMengujiController extends Controller
{
    public function index()
    {
        app()->setLocale('id');
        $userId = Auth::user()->id;

        // Ambil mahasiswa di mana dosen ini menjadi:
        // 1. Penguji 1
        // 2. Penguji 2
        // 3. Pembimbing (Supervisor Internal)

        $sidangs = PendaftaranSidang::with(['mahasiswa.user', 'pendaftaranKp.supervisorInternal'])
            ->whereNotNull('tanggal_sidang') // Hanya yang sudah dijadwal
            ->where(function ($query) use ($userId) {
                $query->where('penguji_1_id', $userId)
                    ->orWhere('penguji_2_id', $userId)
                    ->orWhereHas('pendaftaranKp', function ($q) use ($userId) {
                        $q->where('supervisor_internal_id', $userId);
                    });
            })
            ->get();

        return view('dosen.jadwal-menguji', compact('sidangs'));
    }

    public function inputNilai($id)
    {
        $userId = Auth::user()->id;
        $sidang = PendaftaranSidang::with(['mahasiswa.user', 'pendaftaranKp.supervisorInternal'])->findOrFail($id);

        // Tentukan role dosen untuk mahasiswa ini
        $role = null;
        if ($sidang->penguji_1_id == $userId) {
            $role = 'penguji1';
        } elseif ($sidang->penguji_2_id == $userId) {
            $role = 'penguji2';
        } elseif ($sidang->pendaftaranKp->supervisor_internal_id == $userId) {
            $role = 'pembimbing';
        }

        if (! $role) {
            return redirect()->route('dosen.jadwal-menguji')->with('error', 'Anda tidak memiliki otoritas penilaian untuk mahasiswa ini.');
        }

        return view('dosen.input-nilai', compact('sidang', 'role'));
    }

    public function storeNilai(Request $request, $id)
    {
        $userId = Auth::user()->id;
        $sidang = PendaftaranSidang::findOrFail($id);

        $request->validate([
            'nilai' => 'required|numeric|min:0|max:100',
            'catatan' => 'nullable|string',
        ]);

        $role = null;
        if ($sidang->penguji_1_id == $userId) {
            $role = 'penguji1';
        } elseif ($sidang->penguji_2_id == $userId) {
            $role = 'penguji2';
        } elseif ($sidang->pendaftaranKp->supervisor_internal_id == $userId) {
            $role = 'pembimbing';
        }

        if (! $role) {
            abort(403);
        }

        // Update nilai sesuai role
        if ($role == 'penguji1') {
            $sidang->nilai_penguji_1 = $request->nilai;
        } elseif ($role == 'penguji2') {
            $sidang->nilai_penguji_2 = $request->nilai;
        } elseif ($role == 'pembimbing') {
            $sidang->nilai_pembimbing = $request->nilai;
        }

        // Jika catatan diisi, tambahkan ke catatan_sidang (tulis bertumpuk atau pisah field)
        // Untuk sederhana, kita timpa atau append dengan label
        $timestamp = now()->format('d/m/Y H:i');
        $roleLabel = strtoupper($role);
        $newCatatan = "[{$timestamp} - {$roleLabel}]: {$request->catatan}";
        $sidang->catatan_sidang = $sidang->catatan_sidang ? $sidang->catatan_sidang."\n".$newCatatan : $newCatatan;

        $sidang->save();

        // Hitung Nilai Akhir Otomatis jika semua sudah input
        $this->calculateFinalGrade($sidang);

        return redirect()->route('dosen.jadwal-menguji')->with('success', 'Nilai berhasil disimpan.');
    }

    private function calculateFinalGrade($sidang)
    {
        if ($sidang->nilai_pembimbing && $sidang->nilai_penguji_1 && $sidang->nilai_penguji_2) {
            $avg = ($sidang->nilai_pembimbing + $sidang->nilai_penguji_1 + $sidang->nilai_penguji_2) / 3;
            $sidang->nilai_akhir = round($avg, 2);

            // Konversi Grade (Standar Umum)
            if ($avg >= 85) {
                $sidang->grade = 'A';
            } elseif ($avg >= 80) {
                $sidang->grade = 'A-';
            } elseif ($avg >= 75) {
                $sidang->grade = 'B+';
            } elseif ($avg >= 70) {
                $sidang->grade = 'B';
            } elseif ($avg >= 65) {
                $sidang->grade = 'B-';
            } elseif ($avg >= 60) {
                $sidang->grade = 'C+';
            } elseif ($avg >= 55) {
                $sidang->grade = 'C';
            } else {
                $sidang->grade = 'D/E';
            }

            // Status Kelulusan
            $sidang->status_kelulusan = ($avg >= 60) ? 'lulus' : 'tidak_lulus';

            $sidang->save();
        }
    }
}
