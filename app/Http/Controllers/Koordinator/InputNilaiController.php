<?php

namespace App\Http\Controllers\Koordinator;

use App\Http\Controllers\Controller;
use App\Models\PendaftaranKp;
use App\Models\PendaftaranSidang;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class InputNilaiController extends Controller
{
    public function index()
    {
        app()->setLocale('id');
        $currentUserId = Auth::id();
        $currentUserName = Auth::user()->name;

        $activePeriodId = session('selected_periode_id') ?? \App\Models\TahunAjaran::aktif()?->id;

        // Kita menggunakan pencocokan berbasis Mahasiswa ID untuk mengatasi data pendaftaran sidang yang mungkin link ke KP ID yang salah (lama/rejected)
        $sidangs = PendaftaranSidang::with(['mahasiswa.user', 'pendaftaranKp.supervisorInternal', 'pendaftaranKp.supervisorInstansi'])
            ->whereHas('pendaftaranKp', function($q) use ($activePeriodId) {
                if ($activePeriodId) {
                    $q->where('tahun_ajaran_id', $activePeriodId);
                }
            })
            ->where('status_jadwal', 'submitted')
            ->where(function ($query) use ($currentUserId, $currentUserName) {
                $query->where('penguji_1_id', $currentUserId)
                    ->orWhere('penguji_2_id', $currentUserId)
                    // Cari pendaftaran sidang milik mahasiswa yang memiliki KP approved besutan dosen ini
                    ->orWhereHas('pendaftaranKp', function ($sq) use ($currentUserId, $currentUserName) {
                        $sq->where('pembimbing_id', $currentUserId)
                            ->orWhere('supervisor_internal_id', $currentUserId)
                            ->orWhereExists(function ($ssq) use ($currentUserName) {
                                $ssq->select(DB::raw(1))
                                    ->from('supervisor_instansi')
                                    ->whereColumn('supervisor_instansi.pendaftaran_kp_id', 'pendaftaran_kp.id')
                                    ->where('nama_supervisor', $currentUserName);
                            });
                    });
            })
            ->get()
            ->map(function (PendaftaranSidang $sidang) use ($currentUserId, $currentUserName) {
                // FORCE: Prioritaskan mencari data pendaftaran KP dari foreign key pendaftaran_sidang agar data Anggota tidak nyasar ke KP individu lama.
                $kp = PendaftaranKp::with(['supervisorInternal', 'supervisorInstansi'])->find($sidang->pendaftaran_kp_id);

                if (!$kp) {
                    $kp = PendaftaranKp::with(['supervisorInternal', 'supervisorInstansi'])
                        ->where('mahasiswa_id', $sidang->mahasiswa_id)
                        ->where('status_kp', 'approved')
                        ->first();
                }

                if ($kp) {
                    // Resolve student's own approved KP record to get their actual individual title
                    $ownKp = PendaftaranKp::where('mahasiswa_id', $sidang->mahasiswa_id)
                        ->where('status_kp', 'approved')
                        ->first();
                    if ($ownKp && $kp->id !== $ownKp->id) {
                        $kp->judul_kp = $ownKp->judul_kp;
                    }
                    $sidang->setRelation('pendaftaranKp', $kp);
                }

                $userRoles = [];
                if ($sidang->penguji_1_id == $currentUserId) {
                    $userRoles[] = 'PENGUJI 1';
                }
                if ($sidang->penguji_2_id == $currentUserId) {
                    $userRoles[] = 'PENGUJI 2';
                }

                if ($kp) {
                    if ($kp->pembimbing_id == $currentUserId) {
                        $userRoles[] = 'PEMBIMBING';
                    }
                    if ($kp->supervisor_internal_id == $currentUserId) {
                        $userRoles[] = 'SUPERVISIOR';
                    }
                    if ($kp->supervisorInstansi && $kp->supervisorInstansi->nama_supervisor === $currentUserName) {
                        $userRoles[] = 'SUPERVISIOR';
                    }
                }
                // Fallback pembimbing dari tabel mahasiswa telah dihapus
                $sidang->user_roles = array_unique($userRoles);
                $sidang->is_penguji_1 = ($sidang->penguji_1_id == $currentUserId);

                return $sidang;
            });

        return view('koordinator.input-nilai', compact('sidangs'));
    }

    public function detail($id, $role)
    {
        $sidang = PendaftaranSidang::with(['mahasiswa.user'])->findOrFail($id);

        // FORCE: Prioritaskan mencari data pendaftaran KP dari foreign key pendaftaran_sidang agar data Anggota tidak nyasar ke KP individu lama.
        $kp = PendaftaranKp::with(['supervisorInternal', 'supervisorInstansi'])->find($sidang->pendaftaran_kp_id);

        if (!$kp) {
            $kp = PendaftaranKp::with(['supervisorInternal', 'supervisorInstansi'])
                ->where('mahasiswa_id', $sidang->mahasiswa_id)
                ->where('status_kp', 'approved')
                ->first();
        }

        if ($kp) {
            // Resolve student's own approved KP record to get their actual individual title
            $ownKp = PendaftaranKp::where('mahasiswa_id', $sidang->mahasiswa_id)
                ->where('status_kp', 'approved')
                ->first();
            if ($ownKp && $kp->id !== $ownKp->id) {
                $kp->judul_kp = $ownKp->judul_kp;
            }
            $sidang->setRelation('pendaftaranKp', $kp);
        }

        return view('koordinator.input-nilai-detail', compact('sidang', 'role'));
    }

    public function store(Request $request, $id, $role)
    {
        $sidang = PendaftaranSidang::findOrFail($id);

        $rules = [];
        if ($role === 'pembimbing') {
            $rules = [
                'nb_laporan' => 'required|numeric|min:1|max:100',
                'nb_produk' => 'required|numeric|min:1|max:100',
                'nb_sikap' => 'required|numeric|min:1|max:100',
            ];
        } elseif ($role === 'penguji1' || $role === 'penguji2') {
            $rules = [
                'n_laporan' => 'required|numeric|min:1|max:100',
                'n_produk' => 'required|numeric|min:1|max:100',
                'n_presentasi' => 'required|numeric|min:1|max:100',
            ];
            if ($role === 'penguji1') {
                $rules['status_kelulusan'] = 'required|in:Lulus,Lulus Dengan Revisi,Lanjut';
            }
        } elseif ($role === 'supervisior') {
            $rules = [
                'ns_motivasi' => 'required|numeric|min:1|max:100',
                'ns_kualitas' => 'required|numeric|min:1|max:100',
                'ns_inisiatif' => 'required|numeric|min:1|max:100',
                'ns_sikap' => 'required|numeric|min:1|max:100',
            ];
        }

        $input = $request->all();
        $this->sanitizeNumeric($input);

        $request->merge($input);
        $request->validate($rules);

        if ($role === 'pembimbing') {
            $sidang->nb_laporan = $request->nb_laporan;
            $sidang->nb_produk = $request->nb_produk;
            $sidang->nb_sikap = $request->nb_sikap;
            $sidang->nilai_pembimbing = ($request->nb_laporan * 0.4) + ($request->nb_produk * 0.4) + ($request->nb_sikap * 0.2);
        } elseif ($role === 'penguji1') {
            if ($sidang->original_nilai_penguji_1 === null) {
                $sidang->original_n1_laporan = $request->n_laporan;
                $sidang->original_n1_produk = $request->n_produk;
                $sidang->original_n1_presentasi = $request->n_presentasi;
                $sidang->original_nilai_penguji_1 = ($request->n_laporan * 0.4) + ($request->n_produk * 0.4) + ($request->n_presentasi * 0.2);
            }

            $sidang->n1_laporan = $request->n_laporan;
            $sidang->n1_produk = $request->n_produk;
            $sidang->n1_presentasi = $request->n_presentasi;
            $sidang->nilai_penguji_1 = ($request->n_laporan * 0.4) + ($request->n_produk * 0.4) + ($request->n_presentasi * 0.2);
            $sidang->status_kelulusan = $request->status_kelulusan;
        } elseif ($role === 'penguji2') {
            $sidang->n2_laporan = $request->n_laporan;
            $sidang->n2_produk = $request->n_produk;
            $sidang->n2_presentasi = $request->n_presentasi;
            $sidang->nilai_penguji_2 = ($request->n_laporan * 0.4) + ($request->n_produk * 0.4) + ($request->n_presentasi * 0.2);
        } elseif ($role === 'supervisior') {
            $sidang->ns_motivasi = $request->ns_motivasi;
            $sidang->ns_kualitas = $request->ns_kualitas;
            $sidang->ns_inisiatif = $request->ns_inisiatif;
            $sidang->ns_sikap = $request->ns_sikap;
            $sidang->nilai_supervisor = ($request->ns_motivasi * 0.25) + ($request->ns_kualitas * 0.25) + ($request->ns_inisiatif * 0.25) + ($request->ns_sikap * 0.25);
        }

        $sidang->save();

        if ($request->has('catatan')) {
            $sidang->catatan_sidang = $request->catatan;
            $sidang->save();
        }

        $this->calculateFinalGrade($sidang);

        return redirect()->route('koordinator.input-nilai.index')->with('success', 'Nilai berhasil disimpan sebagai Koordinator.');
    }

    public function downloadPdf($id, $role)
    {
        $sidang = PendaftaranSidang::with(['mahasiswa.user', 'pendaftaranKp.supervisorInternal'])->findOrFail($id);
        
        $kp = $sidang->pendaftaranKp;
        if ($kp) {
            // Resolve student's own approved KP record to get their actual individual title
            $ownKp = PendaftaranKp::where('mahasiswa_id', $sidang->mahasiswa_id)
                ->where('status_kp', 'approved')
                ->first();
            if ($ownKp && $kp->id !== $ownKp->id) {
                $kp->judul_kp = $ownKp->judul_kp;
            }
        }
        
        $pdf = Pdf::loadView('pdf.grading-summary', compact('sidang', 'role'));

        return $pdf->stream('Nilai_Sidang_' . $role . '_' . $sidang->mahasiswa->nim . '.pdf');
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate(['pelaksanaan' => 'required|in:Menunggu,Berjalan,Selesai,Dibatalkan']);
        $sidang = PendaftaranSidang::findOrFail($id);

        // Strict enforcement: only Penguji 1 can change to Selesai/Dibatalkan
        if ($sidang->penguji_1_id != Auth::id()) {
            return response()->json(['success' => false, 'message' => 'Hanya Penguji 1 yang berwenang mengubah status pelaksanaan.'], 403);
        }

        $sidang->pelaksanaan = $request->pelaksanaan;
        $sidang->save();

        return response()->json(['success' => true, 'message' => 'Status pelaksanaan diperbarui.']);
    }

    private function sanitizeNumeric(&$input)
    {
        $fields = [
            'nb_laporan',
            'nb_produk',
            'nb_sikap',
            'n_laporan',
            'n_produk',
            'n_presentasi',
            'ns_motivasi',
            'ns_kualitas',
            'ns_inisiatif',
            'ns_sikap',
        ];
        foreach ($fields as $field) {
            if (isset($input[$field]) && is_string($input[$field])) {
                $input[$field] = str_replace(',', '.', $input[$field]);
            }
        }
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
            // Bobot resmi: Pembimbing 40%, Supervisor 10%, Penguji1 25%, Penguji2 25%
            $pembimbing = (float) $sidang->nilai_pembimbing * 0.40;
            $supervisor = (float) $sidang->nilai_supervisor * 0.10;
            $penguji1 = (float) $sidang->nilai_penguji_1 * 0.25;
            $penguji2 = (float) $sidang->nilai_penguji_2 * 0.25;

            $avg = $pembimbing + $supervisor + $penguji1 + $penguji2;
            $sidang->nilai_akhir = round($avg, 3); // 3 decimals

            if ($avg >= 86) {
                $sidang->grade = 'A';
            } elseif ($avg >= 81) {
                $sidang->grade = 'A-';
            } elseif ($avg >= 76) {
                $sidang->grade = 'B+';
            } elseif ($avg >= 71) {
                $sidang->grade = 'B';
            } elseif ($avg >= 66) {
                $sidang->grade = 'B-';
            } elseif ($avg >= 61) {
                $sidang->grade = 'C+';
            } elseif ($avg >= 56) {
                $sidang->grade = 'C';
            } elseif ($avg >= 46) {
                $sidang->grade = 'D';
            } else {
                $sidang->grade = 'E';
            }

            $sidang->save();
        }
    }
}
