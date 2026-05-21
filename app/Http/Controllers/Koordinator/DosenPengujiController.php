<?php

namespace App\Http\Controllers\Koordinator;

use App\Http\Controllers\Controller;
use App\Models\PendaftaranSidang;
use App\Models\User;
use App\Models\NotifikasiLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use App\Mail\SupervisorPenilaianMail;

class DosenPengujiController extends Controller
{
    public function index()
    {
        // 1. Ambil pendaftaran yang sudah diverifikasi berkasnya
        $allPendaftaran = PendaftaranSidang::with(['mahasiswa.user', 'pendaftaranKp.pembimbing', 'penguji1', 'penguji2'])
            ->where('status_koordinator', 'verified')
            ->get();

        // 2. Deteksi Konflik (Warnings)
        $warnings = $this->detectConflicts($allPendaftaran);

        // Bagi menjadi Daftar Tunggu dan Sudah Terjadwal
        $daftarTunggu = $allPendaftaran->filter(function ($p) {
            return is_null($p->penguji_1_id) || is_null($p->penguji_2_id);
        })->sortBy('mahasiswa.nim')->values();

        $terjadwal = $allPendaftaran->filter(function ($p) {
            return ! is_null($p->penguji_1_id) && ! is_null($p->penguji_2_id);
        })->sortBy('mahasiswa.nim')->values();

        // 3. Ambil daftar dosen aktif
        $dosenAktif = User::whereHas('dosen', function ($query) {
            $query->where('is_aktif', 1);
        })
            ->get();

        // 4. Hitung beban menguji saat ini
        $bebanPenguji = $this->calculateLoads();

        $dosenList = $dosenAktif->map(function ($d) use ($bebanPenguji) {
            return [
                'id' => $d->id,
                'nama' => $d->name,
                'beban' => $bebanPenguji->get($d->id, 0),
            ];
        })->sortBy('nama')->values();

        return view('koordinator.dosen-penguji', [
            'daftarTunggu' => $this->mapPendaftaran($daftarTunggu),
            'terjadwal' => $this->mapPendaftaran($terjadwal),
            'dosenList' => $dosenList,
            'totalMahasiswa' => $allPendaftaran->count(),
            'warnings' => $warnings,
        ]);
    }

    private function calculateLoads()
    {
        return DB::table('pendaftaran_sidang')
            ->select('penguji_1_id as user_id', DB::raw('count(*) as total'))
            ->whereNotNull('penguji_1_id')
            ->groupBy('penguji_1_id')
            ->unionAll(
                DB::table('pendaftaran_sidang')
                    ->select('penguji_2_id as user_id', DB::raw('count(*) as total'))
                    ->whereNotNull('penguji_2_id')
                    ->groupBy('penguji_2_id')
            )
            ->get()
            ->groupBy('user_id')
            ->map(function ($group) {
                return $group->sum('total');
            });
    }

    private function detectConflicts($allPendaftaran)
    {
        $warnings = [];
        $lecturerSchedules = [];

        foreach ($allPendaftaran as $p) {
            $pembimbingId = $p->pendaftaranKp->pembimbing_id ?? null;

            // 1. Conflict: Examiner is Pembimbing
            if ($p->penguji_1_id && $p->penguji_1_id == $pembimbingId) {
                $warnings[] = "Mahasiswa {$p->mahasiswa->user->name} memiliki Penguji 1 yang juga Pembimbingnya.";
            }
            if ($p->penguji_2_id && $p->penguji_2_id == $pembimbingId) {
                $warnings[] = "Mahasiswa {$p->mahasiswa->user->name} memiliki Penguji 2 yang juga Pembimbingnya.";
            }

            // 2. Conflict: Schedule Overlap
            if ($p->tanggal_sidang && $p->waktu_mulai_sidang) {
                $examiners = array_filter([$p->penguji_1_id, $p->penguji_2_id]);
                foreach ($examiners as $exId) {
                    $lecturerSchedules[$exId][] = [
                        'name' => $p->mahasiswa->user->name,
                        'tanggal' => $p->tanggal_sidang,
                        'mulai' => $p->waktu_mulai_sidang,
                        'selesai' => $p->waktu_selesai_sidang,
                    ];
                }
            }
        }

        // Check for overlaps in lecturer schedules
        foreach ($lecturerSchedules as $exId => $sessions) {
            $count = count($sessions);
            for ($i = 0; $i < $count; $i++) {
                for ($j = $i + 1; $j < $count; $j++) {
                    $s1 = $sessions[$i];
                    $s2 = $sessions[$j];

                    if ($s1['tanggal'] == $s2['tanggal']) {
                        // Check overlap
                        if (max($s1['mulai'], $s2['mulai']) < min($s1['selesai'], $s2['selesai'])) {
                            $lecturer = User::find($exId)->name ?? 'Dosen';
                            $warnings[] = "Dosen {$lecturer} memiliki jadwal bentrok antara mahasiswa {$s1['name']} dan {$s2['name']} pada tanggal {$s1['tanggal']}.";
                        }
                    }
                }
            }
        }

        return array_unique($warnings);
    }

    private function mapPendaftaran($collection)
    {
        return $collection->map(function ($p) {
            $ownKp = \App\Models\PendaftaranKp::where('mahasiswa_id', $p->mahasiswa_id)
                ->where('status_kp', 'approved')
                ->latest()
                ->first();
            $judul = $ownKp ? $ownKp->judul_kp : ($p->pendaftaranKp->judul_kp ?? '-');

            return [
                'id' => $p->id,
                'nim' => $p->mahasiswa->nim ?? '-',
                'name' => strtolower($p->mahasiswa->user->name ?? '-'),
                'judul' => strtolower($judul),
                'pembimbing_id' => $p->pendaftaranKp->pembimbing_id ?? null,
                'pembimbing_name' => strtolower($p->pendaftaranKp->pembimbing->name ?? '-'),
                'tanggal' => $p->tanggal_sidang,
                'mulai' => $p->waktu_mulai_sidang,
                'selesai' => $p->waktu_selesai_sidang,
                'ruang' => $p->ruang_sidang,
                'penguji_1_id' => $p->penguji_1_id,
                'penguji_2_id' => $p->penguji_2_id,
                'status_jadwal' => $p->status_jadwal,
            ];
        });
    }

    public function autoPlot()
    {
        $dosenList = User::whereHas('dosen', function ($query) {
            $query->where('is_aktif', 1);
        })->get();

        if ($dosenList->isEmpty()) {
            return response()->json(['success' => false, 'message' => 'Tidak ada dosen aktif untuk diplot.'], 422);
        }

        $daftarTunggu = PendaftaranSidang::with(['pendaftaranKp'])
            ->where('status_koordinator', 'verified')
            ->where(function ($q) {
                $q->whereNull('penguji_1_id')->orWhereNull('penguji_2_id');
            })->get();

        if ($daftarTunggu->isEmpty()) {
            return response()->json(['success' => false, 'message' => 'Daftar tunggu penguji kosong.'], 422);
        }

        // Initialize loads
        $loads = $this->calculateLoads();
        $dosenData = $dosenList->map(function ($d) use ($loads) {
            return [
                'id' => $d->id,
                'name' => $d->name,
                'load' => $loads->get($d->id, 0),
            ];
        })->toArray();

        // Get all current assignments for overlap checking
        $currentAssignments = PendaftaranSidang::whereNotNull('tanggal_sidang')
            ->get(['id', 'tanggal_sidang', 'waktu_mulai_sidang', 'waktu_selesai_sidang', 'penguji_1_id', 'penguji_2_id'])
            ->toArray();

        $successCount = 0;

        DB::beginTransaction();
        try {
            foreach ($daftarTunggu as $sidang) {
                $tanggal = $sidang->tanggal_sidang;
                $mulai = $sidang->waktu_mulai_sidang;
                $selesai = $sidang->waktu_selesai_sidang;
                $pembimbingId = $sidang->pendaftaranKp->pembimbing_id;

                // Sort dosen by load to ensure fair distribution
                usort($dosenData, function ($a, $b) {
                    return $a['load'] <=> $b['load'];
                });

                $selected = [];
                foreach ($dosenData as &$d) {
                    if (count($selected) >= 2) {
                        break;
                    }

                    // Constraint 1: Not Pembimbing
                    if ($d['id'] == $pembimbingId) {
                        continue;
                    }

                    // Constraint 2: No Overlap
                    if ($tanggal && $mulai && $selesai) {
                        $hasConflict = false;
                        foreach ($currentAssignments as $ca) {
                            if ($ca['tanggal_sidang'] == $tanggal) {
                                // If examiner is in this session
                                if ($ca['penguji_1_id'] == $d['id'] || $ca['penguji_2_id'] == $d['id']) {
                                    // Overlap check
                                    if (max($mulai, $ca['waktu_mulai_sidang']) < min($selesai, $ca['waktu_selesai_sidang'])) {
                                        $hasConflict = true;
                                        break;
                                    }
                                }
                            }
                        }
                        if ($hasConflict) {
                            continue;
                        }
                    }

                    $selected[] = $d['id'];
                    $d['load']++; // Increment load for fairness in this loop
                }

                if (count($selected) == 2) {
                    $sidang->update([
                        'penguji_1_id' => $selected[0],
                        'penguji_2_id' => $selected[1],
                    ]);

                    // Update currentAssignments for next iteration
                    $currentAssignments[] = [
                        'id' => $sidang->id,
                        'tanggal_sidang' => $tanggal,
                        'waktu_mulai_sidang' => $mulai,
                        'waktu_selesai_sidang' => $selesai,
                        'penguji_1_id' => $selected[0],
                        'penguji_2_id' => $selected[1],
                    ];
                    $successCount++;
                }
            }
            DB::commit();

            return response()->json(['success' => true, 'message' => "Auto Plotting berhasil untuk {$successCount} mahasiswa."]);
        } catch (\Exception $e) {
            DB::rollback();

            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan saat auto plotting: '.$e->getMessage()], 500);
        }
    }

    public function store(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:pendaftaran_sidang,id',
            'penguji_1_id' => 'required|exists:users,id',
            'penguji_2_id' => 'required|exists:users,id',
        ]);

        if ($request->penguji_1_id == $request->penguji_2_id) {
            return response()->json(['success' => false, 'message' => 'Penguji 1 dan Penguji 2 tidak boleh orang yang sama.'], 422);
        }

        $sidang = PendaftaranSidang::findOrFail($request->id);
        $pembimbingId = $sidang->pendaftaranKp->pembimbing_id;

        if ($request->penguji_1_id == $pembimbingId || $request->penguji_2_id == $pembimbingId) {
            return response()->json(['success' => false, 'message' => 'Dosen Pembimbing tidak boleh menjadi penguji untuk mahasiswanya.'], 422);
        }

        $sidang->update([
            'penguji_1_id' => $request->penguji_1_id,
            'penguji_2_id' => $request->penguji_2_id,
            'status_jadwal' => 'draft',
        ]);

        return response()->json(['success' => true, 'message' => 'Penugasan dosen penguji berhasil disimpan.']);
    }

    public function bulkDestroy(Request $request)
    {
        $ids = $request->input('ids', []);
        if (empty($ids)) {
            return response()->json(['success' => false, 'message' => 'Tidak ada mahasiswa yang dipilih.'], 422);
        }

        PendaftaranSidang::whereIn('id', $ids)->update([
            'penguji_1_id' => null,
            'penguji_2_id' => null,
            'status_jadwal' => 'draft',
        ]);

        return response()->json(['success' => true, 'message' => count($ids).' penugasan penguji telah dibatalkan.']);
    }

    public function destroy($id)
    {
        $sidang = PendaftaranSidang::findOrFail($id);
        $sidang->update([
            'penguji_1_id' => null, 
            'penguji_2_id' => null,
            'status_jadwal' => 'draft'
        ]);

        return response()->json(['success' => true, 'message' => 'Penugasan penguji dibatalkan.']);
    }

    public function submit(Request $request)
    {
        // Ubah semua yang sudah ada pengujinya tapi masih 'draft' menjadi 'submitted'
        $sidangs = PendaftaranSidang::where('status_koordinator', 'verified')
            ->whereNotNull('penguji_1_id')
            ->whereNotNull('penguji_2_id')
            ->where('status_jadwal', 'draft')
            ->get();

        foreach ($sidangs as $sidang) {
            $updateData = ['status_jadwal' => 'submitted'];
            
            // Generate token if not exists
            if (empty($sidang->token_penilaian_supervisor)) {
                $sidang->token_penilaian_supervisor = Str::random(60);
                $updateData['token_penilaian_supervisor'] = $sidang->token_penilaian_supervisor;
            }
            
            $sidang->update($updateData);

            // --- Kirim Notifikasi ke Mahasiswa ---
            NotifikasiLog::create([
                'sender_id' => null,
                'receiver_id' => $sidang->mahasiswa->user_id,
                'judul' => "Jadwal Sidang Diterbitkan",
                'pesan' => "Jadwal sidang KP Anda telah ditentukan. Silakan cek halaman Jadwal Sidang untuk melihat detail waktu, tempat, dan dosen penguji.",
                'target_url' => route('mahasiswa.jadwal-sidang'),
            ]);

            // --- Kirim Notifikasi ke Dosen Penguji 1 ---
            NotifikasiLog::create([
                'sender_id' => null,
                'receiver_id' => $sidang->penguji_1_id,
                'judul' => "Tugas Menguji Baru",
                'pesan' => "Anda telah ditugaskan sebagai Penguji 1 untuk sidang mahasiswa " . ($sidang->mahasiswa->user->name ?? '') . ". Silakan cek Jadwal Menguji.",
                'target_url' => route('dosen.jadwal-menguji'),
            ]);

            // --- Kirim Notifikasi ke Dosen Penguji 2 ---
            NotifikasiLog::create([
                'sender_id' => null,
                'receiver_id' => $sidang->penguji_2_id,
                'judul' => "Tugas Menguji Baru",
                'pesan' => "Anda telah ditugaskan sebagai Penguji 2 untuk sidang mahasiswa " . ($sidang->mahasiswa->user->name ?? '') . ". Silakan cek Jadwal Menguji.",
                'target_url' => route('dosen.jadwal-menguji'),
            ]);

            // --- Kirim Email ke Supervisor Instansi ---
            $sidang->loadMissing('pendaftaranKp.supervisorInstansi');
            $supervisorEmail = $sidang->pendaftaranKp->supervisorInstansi->email_supervisor ?? null;
            if ($supervisorEmail) {
                $urlPenilaian = route('supervisor.penilaian.form', ['token' => $sidang->token_penilaian_supervisor]);
                Mail::to($supervisorEmail)->send(new SupervisorPenilaianMail($sidang, $urlPenilaian));
            }
        }

        return redirect()->back()->with('success', 'Penugasan penguji telah berhasil disubmit.');
    }

    public function cancelSubmit(Request $request)
    {
        PendaftaranSidang::where('status_koordinator', 'verified')
            ->whereNotNull('penguji_1_id')
            ->whereNotNull('penguji_2_id')
            ->where('status_jadwal', 'submitted')
            ->update(['status_jadwal' => 'draft']);

        return redirect()->back()->with('success', 'Status submit penugasan dibatalkan.');
    }
}
