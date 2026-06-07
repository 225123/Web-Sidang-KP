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
        $userId = Auth::id();
        $periodeId = session('selected_periode_id');

        // Ambil semua sidang yang dosen tsb menjadi penguji 1 atau 2,
        // dan filter secara eksplisit sesuai periode aktif.
        $sidangs = PendaftaranSidang::withoutGlobalScope('periode')->with(['mahasiswa.user', 'penguji1', 'penguji2', 'pendaftaranKp.supervisorInternal'])
            ->whereNotNull('tanggal_sidang')
            ->where(function ($query) use ($userId) {
                $query->where('penguji_1_id', $userId)
                      ->orWhere('penguji_2_id', $userId);
            })
            ->get();

        $allEvents = $sidangs->map(function ($s) {
            return [
                'id' => $s->id,
                'nama' => strtoupper($s->mahasiswa->user->name ?? 'Mahasiswa'),
                'nim' => $s->mahasiswa->nim ?? '-',
                'penguji1' => $s->penguji1->name ?? '-',
                'penguji2' => $s->penguji2->name ?? '-',
                'penguji' => [
                    $s->penguji1->name ?? '-',
                    $s->penguji2->name ?? '-',
                ],
                'tanggal' => $s->tanggal_sidang,
                'jadwal' => [
                    'tanggal' => date('d/m/Y', strtotime($s->tanggal_sidang)),
                    'waktu' => date('H:i', strtotime($s->waktu_mulai_sidang)).'-'.date('H:i', strtotime($s->waktu_selesai_sidang)),
                    'ruang' => $s->ruang_sidang ?? '-',
                ],
                'waktu_mulai' => $s->waktu_mulai_sidang,
                'ruangan' => $s->ruang_sidang ?? '-',
                'status' => 'Terjadwal',
                'status_raw' => $s->status_jadwal,
                'pelaksanaan' => $s->pelaksanaan ?? '-',
                'tanggal_sidang' => $s->tanggal_sidang,
                'waktu_mulai_sidang' => $s->waktu_mulai_sidang,
                'waktu_selesai_sidang' => $s->waktu_selesai_sidang,
            ];
        });

        return view('dosen.jadwal-menguji', [
            'events' => $allEvents,
        ]);
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

            $sidang->save();
        }
    }
}
