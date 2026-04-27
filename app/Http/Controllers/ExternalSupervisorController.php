<?php

namespace App\Http\Controllers;

use App\Models\PendaftaranSidang;
use Illuminate\Http\Request;

class ExternalSupervisorController extends Controller
{
    /**
     * Show the public grading form for the supervisor.
     */
    public function showForm($token)
    {
        $sidang = PendaftaranSidang::with(['mahasiswa.user', 'pendaftaranKp.supervisorInstansi'])
            ->where('token_penilaian_supervisor', $token)
            ->first();

        // 1. Validasi Token Expired / Tidak Ditemukan
        if (!$sidang) {
            return abort(404, 'Tautan tidak valid atau sudah kadaluarsa.');
        }

        // 2. Validasi Jika Sudah Diisi
        if ($sidang->is_penilaian_supervisor_submitted) {
            return view('supervisor.sudah_dinilai', compact('sidang'));
        }

        return view('supervisor.penilaian', compact('sidang', 'token'));
    }

    /**
     * Submit the grade.
     */
    public function submitNilai(Request $request, $token)
    {
        $sidang = PendaftaranSidang::where('token_penilaian_supervisor', $token)->first();

        if (!$sidang || $sidang->is_penilaian_supervisor_submitted) {
            return abort(404, 'Tautan tidak valid atau penilaian sudah dikirim sebelumnya.');
        }

        $request->validate([
            'nilai_motivasi' => 'required|numeric|min:0|max:100',
            'nilai_kualitas' => 'required|numeric|min:0|max:100',
            'nilai_inisiatif' => 'required|numeric|min:0|max:100',
            'nilai_sikap' => 'required|numeric|min:0|max:100',
            'file_nilai_supervisor' => 'required|mimes:pdf|max:5120', // Wajib upload form PDF ber-cap
        ]);

        // Calculate average grade (25% each as per standard grading criteria)
        $nilaiSupervisor = ($request->nilai_motivasi * 0.25) +
                           ($request->nilai_kualitas * 0.25) +
                           ($request->nilai_inisiatif * 0.25) +
                           ($request->nilai_sikap * 0.25);

        // Upload PDF
        $filePath = null;
        if ($request->hasFile('file_nilai_supervisor')) {
            $filePath = $request->file('file_nilai_supervisor')->store('sidang_berkas', 'public');
        }

        $sidang->update([
            'nilai_supervisor' => $nilaiSupervisor,
            'file_nilai_supervisor' => $filePath,
            'is_penilaian_supervisor_submitted' => true,
        ]);

        // Automatically calculate final grade if all other components are complete
        $this->calculateFinalGrade($sidang);

        return redirect()->route('supervisor.penilaian.success')->with('success', 'Penilaian berhasil disimpan. Terima kasih atas kontribusi Anda.');
    }

    private function calculateFinalGrade($sidang)
    {
        $scores = [
            $sidang->nilai_pembimbing,
            $sidang->nilai_penguji_1,
            $sidang->nilai_penguji_2,
            $sidang->nilai_supervisor,
        ];

        $isComplete = true;
        foreach ($scores as $score) {
            if (is_null($score)) {
                $isComplete = false;
                break;
            }
        }

        if ($isComplete) {
            $pembimbing = (float) $sidang->nilai_pembimbing * 0.40;
            $supervisor = (float) $sidang->nilai_supervisor * 0.10;
            $penguji1   = (float) $sidang->nilai_penguji_1 * 0.25;
            $penguji2   = (float) $sidang->nilai_penguji_2 * 0.25;
            
            $avg = $pembimbing + $supervisor + $penguji1 + $penguji2;
            $sidang->nilai_akhir = round($avg, 3); // 3 decimals

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

            $sidang->status_kelulusan = ($avg >= 60) ? 'Lulus' : 'Tidak Lulus';
            $sidang->save();
        }
    }

    public function success()
    {
        return view('supervisor.success');
    }
}
