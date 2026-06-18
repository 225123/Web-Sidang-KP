<?php

namespace App\Http\Controllers\Koordinator;

use App\Http\Controllers\Controller;
use App\Models\TahunAjaran;
use App\Models\PendaftaranKp;
use App\Models\User;
use App\Models\Mahasiswa;
use App\Models\Dosen;
use Illuminate\Http\Request;

class PeriodeKpController extends Controller
{
    public function index()
    {
        $periodes = TahunAjaran::withTrashed()->terbaru()->get();

        $last = $periodes->whereNull('deleted_at')->first();
        $nextPeriod = $this->generateNext($last);

        // Count pendaftaran per period
        $stats = [];
        $dosenStats = [];
        $userStats = [];
        foreach ($periodes as $periode) {
            if (!$periode->is_active || $periode->trashed() || $periode->total_mahasiswa !== null) {
                // Gunakan static history (baku/frozen)
                $stats[$periode->id] = $periode->total_mahasiswa ?? 0;
                $dosenStats[$periode->id] = $periode->total_dosen ?? 0;
                $userStats[$periode->id] = $periode->total_user ?? 0;
            } else {
                // Hitung fresh dari database untuk periode aktif
                $stats[$periode->id] = Mahasiswa::where('tahun_ajaran_id', $periode->id)->count();
                
                // Dosen aktif (hanya yang is_aktif = true di tabel dosen)
                $dosenStats[$periode->id] = Dosen::where('is_aktif', true)->count();
                $userStats[$periode->id] = $stats[$periode->id] + $dosenStats[$periode->id];
            }
        }

        // Active period counts
        $aktif = $periodes->firstWhere('is_active', true);
        $aktifStats = ['mahasiswa' => 0, 'dosen' => 0, 'total' => 0];

        if ($aktif) {
            $aktifStats['mahasiswa'] = $stats[$aktif->id] ?? 0;
            $aktifStats['dosen'] = $dosenStats[$aktif->id] ?? 0;
            $aktifStats['total'] = $userStats[$aktif->id] ?? 0;
        }

        return view('koordinator.periode-kp', compact('periodes', 'nextPeriod', 'stats', 'dosenStats', 'userStats', 'aktif', 'aktifStats'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'semester' => 'required|in:Ganjil,Genap',
            'tahun'    => 'required|regex:/^\d{4}\/\d{4}$/',
        ]);

        $isSisipan = $request->input('is_sisipan') == '1';
        $oldActive = TahunAjaran::where('is_active', true)->first();

        if ($isSisipan) {
            if (!$oldActive) {
                return back()->with('error', "Tidak ada periode aktif untuk dijadikan periode sisipan.");
            }
            $label = $oldActive->label_tahun_ajaran . ' - Sisipan';
            $semester = $oldActive->semester;
            $tahun = $oldActive->tahun;
        } else {
            $label = $request->semester . ' ' . $request->tahun;
            $semester = $request->semester;
            $tahun = $request->tahun;
        }

        if (TahunAjaran::where('label_tahun_ajaran', $label)->exists()) {
            return back()->with('error', "Periode \"$label\" sudah ada.");
        }

        // Auto-close current active period and freeze its stats
        if ($oldActive) {
            $mhsCount = Mahasiswa::where('tahun_ajaran_id', $oldActive->id)->count();

            $dsnCount = Dosen::where('is_aktif', true)->count();

            $oldActive->update([
                'is_active' => false,
                'total_mahasiswa' => $mhsCount,
                'total_dosen' => $dsnCount,
                'total_user' => $mhsCount + $dsnCount
            ]);
        }

        $newPeriod = TahunAjaran::create([
            'semester'           => $semester,
            'tahun'              => $tahun,
            'label_tahun_ajaran' => $label,
            'is_active'          => true,
            'koordinator_id'     => auth()->id(),
        ]);

        // Berpusat di periode yang baru dibuka
        session(['selected_periode_id' => $newPeriod->id]);

        if ($oldActive) {
            if ($isSisipan) {
                $this->carryOverSisipanStudents($oldActive->id, $newPeriod->id);
            } else {
                $this->carryOverLanjutStudents($oldActive->id, $newPeriod->id);
            }
        }

        return back()->with('success', "Periode KP \"$label\" berhasil dibuka dan kini menjadi periode aktif.");
    }

    private function carryOverLanjutStudents($oldPeriodeId, $newPeriodeId)
    {
        // PENTING: Gunakan withoutGlobalScope karena session sudah di-set ke $newPeriodeId
        // sebelum fungsi ini dipanggil. Tanpa ini, Global Scope memblokir query ke periode lama.
        $sidangs = \App\Models\PendaftaranSidang::withoutGlobalScope('periode')
            ->with(['pendaftaranKp' => function ($q) {
                $q->withoutGlobalScope('periode')->with('supervisorInstansi');
            }])
            ->whereHas('pendaftaranKp', function ($q) use ($oldPeriodeId) {
                $q->withoutGlobalScope('periode')->where('tahun_ajaran_id', $oldPeriodeId);
            })
            ->whereIn('status_kelulusan', ['Lanjut', 'Tidak Lulus'])
            ->get();

        foreach ($sidangs as $sidang) {
            $oldKp = $sidang->pendaftaranKp;
            if (!$oldKp) continue;

            // Bypass global scope — kita sedang mengecek di periode BARU
            $exists = PendaftaranKp::withoutGlobalScope('periode')
                ->where('mahasiswa_id', $oldKp->mahasiswa_id)
                ->where('tahun_ajaran_id', $newPeriodeId)
                ->where('is_lanjutan', true)
                ->exists();

            if (!$exists) {
                $newKp = PendaftaranKp::create([
                    'mahasiswa_id' => $oldKp->mahasiswa_id,
                    'tahun_ajaran_id' => $newPeriodeId,
                    'judul_kp' => $oldKp->judul_kp,
                    'jenis_proyek' => $oldKp->jenis_proyek,
                    'instansi_nama' => $oldKp->instansi_nama,
                    'instansi_alamat' => $oldKp->instansi_alamat,
                    'jenis_instansi' => $oldKp->jenis_instansi,
                    'pembimbing_id' => $oldKp->pembimbing_id,
                    'supervisor_internal_id' => $oldKp->supervisor_internal_id,
                    'tipe_kp' => $oldKp->tipe_kp,
                    'pengerjaan_kp' => $oldKp->pengerjaan_kp,
                    'anggota_kelompok_ids' => $oldKp->anggota_kelompok_ids,
                    'status_kp' => 'approved', // Langsung disetujui tanpa verifikasi ulang
                    'is_lanjutan' => true,
                    'pendaftaran_asal_id' => $oldKp->id,
                ]);

                if ($oldKp->supervisorInstansi) {
                    \App\Models\SupervisorInstansi::create([
                        'pendaftaran_kp_id' => $newKp->id,
                        'nama_supervisor' => $oldKp->supervisorInstansi->nama_supervisor,
                        'kontak_supervisor' => $oldKp->supervisorInstansi->kontak_supervisor,
                        'no_hp_supervisor' => $oldKp->supervisorInstansi->no_hp_supervisor,
                        'email_supervisor' => $oldKp->supervisorInstansi->email_supervisor,
                        'jabatan_supervisor' => $oldKp->supervisorInstansi->jabatan_supervisor,
                    ]);
                }
            }
        }
    }

    private function carryOverSisipanStudents($oldPeriodeId, $newPeriodeId)
    {
        // 1. Fetch all PendaftaranSidang from old period where status_kelulusan == 'Lanjut'
        $sidangs = \App\Models\PendaftaranSidang::withoutGlobalScope('periode')
            ->with(['pendaftaranKp' => function ($q) {
                $q->withoutGlobalScope('periode')->with(['supervisorInstansi', 'logBimbingans']);
            }])
            ->whereHas('pendaftaranKp', function ($q) use ($oldPeriodeId) {
                $q->withoutGlobalScope('periode')->where('tahun_ajaran_id', $oldPeriodeId);
            })
            ->where('status_kelulusan', 'Lanjut')
            ->get();

        foreach ($sidangs as $sidang) {
            $oldKp = $sidang->pendaftaranKp;
            if (!$oldKp) continue;

            // 2. Update Mahasiswa table so they automatically appear in the new period
            \App\Models\Mahasiswa::where('user_id', $sidang->mahasiswa_id)->update(['tahun_ajaran_id' => $newPeriodeId]);

            // 3. Duplicate PendaftaranKp if not already done for this group
            $newKp = \App\Models\PendaftaranKp::withoutGlobalScope('periode')
                ->where('pendaftaran_asal_id', $oldKp->id)
                ->where('tahun_ajaran_id', $newPeriodeId)
                ->where('is_lanjutan', true)
                ->first();

            if (!$newKp) {
                $newKp = \App\Models\PendaftaranKp::create([
                    'mahasiswa_id' => $oldKp->mahasiswa_id,
                    'tahun_ajaran_id' => $newPeriodeId,
                    'judul_kp' => $oldKp->judul_kp,
                    'jenis_proyek' => $oldKp->jenis_proyek,
                    'instansi_nama' => $oldKp->instansi_nama,
                    'instansi_alamat' => $oldKp->instansi_alamat,
                    'jenis_instansi' => $oldKp->jenis_instansi,
                    'pembimbing_id' => $oldKp->pembimbing_id,
                    'supervisor_internal_id' => $oldKp->supervisor_internal_id,
                    'tipe_kp' => $oldKp->tipe_kp,
                    'pengerjaan_kp' => $oldKp->pengerjaan_kp,
                    'anggota_kelompok_ids' => $oldKp->anggota_kelompok_ids,
                    'status_kp' => 'approved', // Automatically approved
                    'is_lanjutan' => true,
                    'pendaftaran_asal_id' => $oldKp->id,
                ]);

                // 4. Duplicate SupervisorInstansi
                if ($oldKp->supervisorInstansi) {
                    \App\Models\SupervisorInstansi::create([
                        'pendaftaran_kp_id' => $newKp->id,
                        'nama_supervisor' => $oldKp->supervisorInstansi->nama_supervisor,
                        'kontak_supervisor' => $oldKp->supervisorInstansi->kontak_supervisor,
                        'no_hp_supervisor' => $oldKp->supervisorInstansi->no_hp_supervisor,
                        'email_supervisor' => $oldKp->supervisorInstansi->email_supervisor,
                        'jabatan_supervisor' => $oldKp->supervisorInstansi->jabatan_supervisor,
                    ]);
                }

                // 5. Duplicate LogBimbingan
                foreach ($oldKp->logBimbingans as $log) {
                    \App\Models\LogBimbingan::create([
                        'pendaftaran_kp_id' => $newKp->id,
                        'mahasiswa_id' => $log->mahasiswa_id,
                        'tanggal' => $log->tanggal,
                        'materi_bahasan' => $log->materi_bahasan,
                        'file_progress' => $log->file_progress,
                        'status_approval' => $log->status_approval,
                        'komentar_dosen' => $log->komentar_dosen,
                        'is_supervisor' => $log->is_supervisor,
                    ]);
                }
            }

            // 6. Duplicate PendaftaranSidang for this specific member
            $existsSidang = \App\Models\PendaftaranSidang::withoutGlobalScope('periode')
                ->where('pendaftaran_kp_id', $newKp->id)
                ->where('mahasiswa_id', $sidang->mahasiswa_id)
                ->exists();

            if (!$existsSidang) {
                \App\Models\PendaftaranSidang::create([
                    'pendaftaran_kp_id' => $newKp->id,
                    'mahasiswa_id' => $sidang->mahasiswa_id,
                    
                    // Retain files and verification
                    'file_laporan' => $sidang->file_laporan,
                    'file_log_bimbingan' => $sidang->file_log_bimbingan,
                    'file_persetujuan_pembimbing' => $sidang->file_persetujuan_pembimbing,
                    'file_nilai_supervisor' => $sidang->file_nilai_supervisor,
                    'file_berkas_lainnya' => $sidang->file_berkas_lainnya,
                    'link_github' => $sidang->link_github,
                    'link_drive' => $sidang->link_drive,
                    'link_deploy' => $sidang->link_deploy,
                    
                    'status_verifikasi' => $sidang->status_verifikasi, // retain verified status
                    'status_koordinator' => $sidang->status_koordinator, // retain approved by koor
                    'koordinator_feedback' => $sidang->koordinator_feedback,
                    'dosen_feedback' => $sidang->dosen_feedback,
                    'pelaksanaan' => $sidang->pelaksanaan,

                    // Retain Scores from Supervisor and Pembimbing
                    'nilai_pembimbing' => $sidang->nilai_pembimbing,
                    'nilai_supervisor' => $sidang->nilai_supervisor,
                    'nb_laporan' => $sidang->nb_laporan,
                    'nb_produk' => $sidang->nb_produk,
                    'nb_sikap' => $sidang->nb_sikap,
                    'ns_motivasi' => $sidang->ns_motivasi,
                    'ns_kualitas' => $sidang->ns_kualitas,
                    'ns_inisiatif' => $sidang->ns_inisiatif,
                    'ns_sikap' => $sidang->ns_sikap,
                    
                    'token_penilaian_supervisor' => $sidang->token_penilaian_supervisor,
                    'is_penilaian_supervisor_submitted' => $sidang->is_penilaian_supervisor_submitted,

                    // Reset Penguji, Schedule, Nilai Akhir, and Revision
                    'tanggal_sidang' => null,
                    'waktu_mulai_sidang' => null,
                    'waktu_selesai_sidang' => null,
                    'ruang_sidang' => null,
                    'status_jadwal' => 'pending',
                    'penguji_1_id' => null,
                    'penguji_2_id' => null,
                    'nilai_penguji_1' => null,
                    'nilai_penguji_2' => null,
                    'nilai_akhir' => null,
                    'grade' => null,
                    'catatan_sidang' => null,
                    'status_kelulusan' => null,
                    
                    'n1_laporan' => null,
                    'n1_produk' => null,
                    'n1_presentasi' => null,
                    'n2_laporan' => null,
                    'n2_produk' => null,
                    'n2_presentasi' => null,
                    
                    'original_n1_laporan' => null,
                    'original_n1_produk' => null,
                    'original_n1_presentasi' => null,
                    'original_nilai_penguji_1' => null,
                    
                    'status_revisi' => null,
                    'file_revisi' => null,
                    'link_revisi' => null,
                    'tanggal_revisi' => null,
                    'catatan_revisi' => null,
                    
                    'berita_acara_disubmit' => false,
                    'nilai_dipublikasi' => false,
                ]);
            }
        }
    }

    public function setActive(Request $request, $id)
    {
        $periode = TahunAjaran::findOrFail($id);
        
        // Auto-close current active period and freeze its stats
        $oldActive = TahunAjaran::where('is_active', true)->first();
        if ($oldActive && $oldActive->id !== $periode->id) {
            $mhsCount = Mahasiswa::where('tahun_ajaran_id', $oldActive->id)->count();

            $dsnCount = Dosen::where('is_aktif', true)->count();

            $oldActive->update([
                'is_active' => false,
                'total_mahasiswa' => $mhsCount,
                'total_dosen' => $dsnCount,
                'total_user' => $mhsCount + $dsnCount
            ]);
        }

        $periode->update(['is_active' => true]);

        // Berpusat di periode yang dipilih
        session(['selected_periode_id' => $periode->id]);

        return back()->with('success', "Periode \"{$periode->label_tahun_ajaran}\" sekarang menjadi periode aktif.");
    }

    public function destroy($id)
    {
        $periode = TahunAjaran::findOrFail($id);

        if ($periode->is_active) {
            return back()->with('error', 'Tidak dapat menghapus periode yang sedang aktif.');
        }

        if (PendaftaranKp::where('tahun_ajaran_id', $id)->exists()) {
            return back()->with('error', 'Tidak dapat menghapus periode yang sudah memiliki data pendaftaran.');
        }

        $label = $periode->label_tahun_ajaran;
        $periode->delete();

        return back()->with('success', "Periode \"$label\" berhasil dihapus.");
    }

    /**
     * Ganjil X/Y  → Genap X/Y  (same academic year)
     * Genap X/Y   → Ganjil Y/Y+1 (next academic year)
     */
    private function generateNext(?TahunAjaran $last): array
    {
        if (!$last) {
            $semester  = 'Ganjil';
            $year      = (int) now()->format('Y');
            $tahun     = $year . '/' . ($year + 1);
        } elseif ($last->semester === 'Ganjil') {
            $semester = 'Genap';
            $tahun    = $last->tahun; // e.g. 2025/2026
        } else {
            // Genap → advance year
            $semester = 'Ganjil';
            $parts    = explode('/', $last->tahun);
            $endYear  = (int)($parts[1] ?? $parts[0]);
            $tahun    = $endYear . '/' . ($endYear + 1); // e.g. 2026/2027
        }

        return ['semester' => $semester, 'tahun' => $tahun, 'label' => "$semester $tahun"];
    }
}
