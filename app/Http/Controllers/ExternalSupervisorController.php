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
            'nilai_motivasi' => 'required|numeric|min:1|max:100|regex:/^\d+(\.\d{1,3})?$/',
            'nilai_kualitas' => 'required|numeric|min:1|max:100|regex:/^\d+(\.\d{1,3})?$/',
            'nilai_inisiatif' => 'required|numeric|min:1|max:100|regex:/^\d+(\.\d{1,3})?$/',
            'nilai_sikap' => 'required|numeric|min:1|max:100|regex:/^\d+(\.\d{1,3})?$/',
            'file_nilai_supervisor' => 'required|string', // Wajib base64 string tanda tangan
        ], [
            'regex' => 'Kolom :attribute maksimal memiliki 3 angka desimal.',
            'min' => 'Kolom :attribute minimal bernilai 1.',
            'max' => 'Kolom :attribute maksimal bernilai 100.'
        ]);

        // Calculate average grade (25% each as per standard grading criteria)
        $nilaiSupervisor = ($request->nilai_motivasi * 0.25) +
                           ($request->nilai_kualitas * 0.25) +
                           ($request->nilai_inisiatif * 0.25) +
                           ($request->nilai_sikap * 0.25);

        // Konversi Base64 Tanda Tangan ke file gambar dan simpan ke Storj/Lokal
        $filePath = null;
        if ($request->filled('file_nilai_supervisor')) {
            $base64Data = $request->file_nilai_supervisor;
            if (preg_match('/^data:image\/(\w+);base64,/', $base64Data, $type)) {
                $base64Data = substr($base64Data, strpos($base64Data, ',') + 1);
                $type = strtolower($type[1]);
                
                if (in_array($type, ['png', 'jpg', 'jpeg', 'webp'])) {
                    $base64Data = base64_decode($base64Data);
                    if ($base64Data !== false) {
                        $fileName = 'sidang_berkas/supervisor_sig_' . uniqid() . '_' . time() . '.' . $type;
                        \Illuminate\Support\Facades\Storage::disk(upload_disk())->put($fileName, $base64Data);
                        $filePath = $fileName;
                    }
                }
            }
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

            $sidang->save();
        }
    }

    public function success()
    {
        return view('supervisor.success');
    }
}
