<?php

namespace App\Http\Controllers\Dosen;

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

        // Ambil mahasiswa di mana dosen ini memiliki peran apapun (Penguji, Pembimbing, atau Supervisior)
        $sidangs = PendaftaranSidang::with(['mahasiswa.user'])
            ->where(function ($query) use ($currentUserId, $currentUserName) {
                // Peran sebagai Penguji
                $query->where('penguji_1_id', $currentUserId)
                    ->orWhere('penguji_2_id', $currentUserId)
                    // Peran sebagai Pembimbing atau Supervisor (dari tabel pendaftaran_kp)
                    ->orWhereHas('pendaftaranKp', function ($q) use ($currentUserId, $currentUserName) {
                    $q->where('pembimbing_id', $currentUserId)
                        ->orWhere('supervisor_internal_id', $currentUserId)
                        ->orWhereExists(function ($sq) use ($currentUserName) {
                            $sq->select(DB::raw(1))
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

                // Jika tidak ada (sangat jarang), fallback ke pencarian berdasarkan mahasiswa_id
                if (!$kp) {
                    $kp = PendaftaranKp::with(['supervisorInternal', 'supervisorInstansi'])
                        ->where('mahasiswa_id', $sidang->mahasiswa_id)
                        ->where('status_kp', 'approved')
                        ->first();
                }

                // Set relasi agar view menggunakan data KP yang benar
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

                $roles = [];
                if ($sidang->penguji_1_id == $currentUserId) {
                    $roles[] = 'PENGUJI 1';
                }
                if ($sidang->penguji_2_id == $currentUserId) {
                    $roles[] = 'PENGUJI 2';
                }

                if ($kp) {
                    if ($kp->pembimbing_id == $currentUserId) {
                        $roles[] = 'PEMBIMBING';
                    }
                    if ($kp->supervisor_internal_id == $currentUserId) {
                        $roles[] = 'SUPERVISIOR';
                    }
                    if ($kp->supervisorInstansi && $kp->supervisorInstansi->nama_supervisor === $currentUserName) {
                        $roles[] = 'SUPERVISIOR';
                    }
                }

                // Cleaned up fallback, relation handles it perfectly.
    
                $sidang->user_roles = array_unique($roles);
                $sidang->is_penguji_1 = ($sidang->penguji_1_id == $currentUserId);

                return $sidang;
            });

        return view('dosen.input-nilai', compact('sidangs'));
    }

    public function detail($id, $role)
    {
        $userId = Auth::id();
        $userName = Auth::user()->name;
        $sidang = PendaftaranSidang::with(['mahasiswa.user'])->findOrFail($id);

        // FORCE: Prioritaskan mencari data pendaftaran KP dari foreign key pendaftaran_sidang agar data Anggota tidak nyasar ke KP individu lama.
        $kp = PendaftaranKp::with(['supervisorInternal', 'supervisorInstansi'])->find($sidang->pendaftaran_kp_id);

        // Jika tidak ada (sangat jarang), fallback ke pencarian berdasarkan mahasiswa_id
        if (!$kp) {
            $kp = PendaftaranKp::with(['supervisorInternal', 'supervisorInstansi'])
                ->where('mahasiswa_id', $sidang->mahasiswa_id)
                ->where('status_kp', 'approved')
                ->first();
        }

        // Set relasi agar view menggunakan data KP yang benar
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

        // Security Check yang lebih kuat
        $isAuthorized = false;
        if ($role === 'penguji1' && $sidang->penguji_1_id == $userId) {
            $isAuthorized = true;
        } elseif ($role === 'penguji2' && $sidang->penguji_2_id == $userId) {
            $isAuthorized = true;
        } elseif ($kp) {
            if ($role === 'pembimbing' && $kp->pembimbing_id == $userId) {
                $isAuthorized = true;
            } elseif ($role === 'supervisior') {
                if ($kp->supervisor_internal_id == $userId) {
                    $isAuthorized = true;
                }
                if ($kp->supervisorInstansi && $kp->supervisorInstansi->nama_supervisor === $userName) {
                    $isAuthorized = true;
                }
            }
        }

        // Fallback for students handled by kp relations only

        if (!$isAuthorized) {
            return redirect()->route('dosen.input-nilai.index')->with('error', 'Anda tidak memiliki otoritas untuk peran ini.');
        }

        return view('dosen.input-nilai-detail', compact('sidang', 'role'));
    }

    public function store(Request $request, $id, $role)
    {
        $sidang = PendaftaranSidang::findOrFail($id);
        $userId = Auth::id();
        $userName = Auth::user()->name;

        // Validasi authority yang sama dengan detail()
        $kp = PendaftaranKp::find($sidang->pendaftaran_kp_id);
        if (!$kp) {
            $kp = PendaftaranKp::where('mahasiswa_id', $sidang->mahasiswa_id)->where('status_kp', 'approved')->first();
        }

        $isAuthorized = false;
        if ($role === 'penguji1' && $sidang->penguji_1_id == $userId) {
            $isAuthorized = true;
        } elseif ($role === 'penguji2' && $sidang->penguji_2_id == $userId) {
            $isAuthorized = true;
        } elseif ($kp) {
            if ($role === 'pembimbing' && $kp->pembimbing_id == $userId) {
                $isAuthorized = true;
            } elseif ($role === 'supervisior') {
                if ($kp->supervisor_internal_id == $userId) {
                    $isAuthorized = true;
                }
                if ($kp->supervisorInstansi && $kp->supervisorInstansi->nama_supervisor === $userName) {
                    $isAuthorized = true;
                }
            }
        }

        // Authorized check completed array

        if (!$isAuthorized) {
            return redirect()->route('dosen.input-nilai.index')->with('error', 'Akses ditolak.');
        }

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

        if ($request->filled('catatan')) {
            $timestamp = now()->format('d/m/Y H:i');
            $roleLabel = strtoupper($role);
            $newNote = "[{$timestamp} - {$roleLabel}]: {$request->catatan}";
            $sidang->catatan_sidang = $sidang->catatan_sidang ? $sidang->catatan_sidang . "\n" . $newNote : $newNote;
            $sidang->save();
        }

        $this->calculateFinalGrade($sidang);

        return redirect()->route('dosen.input-nilai.index')->with('success', 'Nilai berhasil disimpan.');
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

        // Ensure all 4 sections have BEEN GRADED (not null)
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
