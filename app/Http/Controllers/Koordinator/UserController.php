<?php

namespace App\Http\Controllers\Koordinator;

use App\Exports\UsersTemplateExport;
use App\Http\Controllers\Controller;
use App\Imports\UsersImport;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Facades\Excel;

class UserController extends Controller
{
    /**
     * Mengecek ketersediaan ID (NIM/NIDN) melalui AJAX untuk form tambah user
     */
    public function checkId(Request $request)
    {
        $id = $request->input('id_user');
        if (!$id) {
            return response()->json(['exists' => false]);
        }

        // Cek di tabel mahasiswa
        $mhs = DB::table('mahasiswa')
            ->join('users', 'mahasiswa.user_id', '=', 'users.id')
            ->where('mahasiswa.nim', $id)
            ->select('users.id as user_id', 'users.name', 'users.email', 'users.role')
            ->first();

        if ($mhs) {
            $isAllowed = false;
            $notAllowedMessage = '';
            
            $activePeriodId = \App\Models\TahunAjaran::where('is_active', true)->value('id');
            
            // Cek apakah mahasiswa MAHASISWA ini SEDANG AKTIF di periode saat ini
            $mhsRecord = DB::table('mahasiswa')->where('user_id', $mhs->user_id)->first();
            if ($mhsRecord && $mhsRecord->tahun_ajaran_id == $activePeriodId) {
                $isAllowed = false;
                $notAllowedMessage = 'Mahasiswa dengan NIM ini sudah terdaftar dan aktif di periode saat ini. Penambahan pengguna diblokir.';
            } else {
                $latestSidang = DB::table('pendaftaran_sidang')
                    ->where('mahasiswa_id', $mhs->user_id)
                    ->orderBy('id', 'desc')
                    ->first();

                if (!$latestSidang) {
                    $isAllowed = true;
                } else {
                    if (in_array($latestSidang->status_kelulusan, ['Lulus', 'Lulus Dengan Revisi']) && !in_array($latestSidang->grade, ['D', 'E'])) {
                        $isAllowed = false;
                    } else {
                        $isAllowed = true;
                    }
                }
                if (!$isAllowed && !$notAllowedMessage) {
                    $notAllowedMessage = 'Mahasiswa ini sudah dinyatakan Lulus / Lulus Dengan Revisi di periode sebelumnya dan tidak bisa didaftarkan ulang. Penambahan pengguna diblokir.';
                }
            }

            return response()->json([
                'exists' => true,
                'role_type' => 'mahasiswa',
                'name' => $mhs->name,
                'email' => $mhs->email,
                'role' => $mhs->role == 'mahasiswa' ? 'Mahasiswa' : ucfirst($mhs->role),
                'not_allowed' => !$isAllowed,
                'not_allowed_message' => $notAllowedMessage,
                'user_id' => $mhs->user_id
            ]);
        }

        // Cek di tabel dosen
        $dosen = DB::table('dosen')
            ->join('users', 'dosen.user_id', '=', 'users.id')
            ->where('dosen.nidn', $id)
            ->select('users.name', 'users.email', 'users.role')
            ->first();

        if ($dosen) {
            return response()->json([
                'exists' => true,
                'role_type' => 'dosen',
                'name' => $dosen->name,
                'email' => $dosen->email,
                'role' => $dosen->role == 'dosen' ? 'Dosen' : 'Koordinator KP'
            ]);
        }

        return response()->json(['exists' => false]);
    }

    /**
     * Menampilkan halaman Manajemen Akses
     */
    public function index(Request $request)
    {
        if (!\Illuminate\Support\Facades\Schema::hasColumn('mahasiswa', 'tahun_ajaran_id')) {
            \Illuminate\Support\Facades\Schema::table('mahasiswa', function (\Illuminate\Database\Schema\Blueprint $table) {
                $table->unsignedBigInteger('tahun_ajaran_id')->nullable();
            });
            $activeId = \App\Models\TahunAjaran::where('is_active', true)->value('id');
            if ($activeId) {
                DB::table('mahasiswa')->update(['tahun_ajaran_id' => $activeId]);
            }
        }

        if (!\Illuminate\Support\Facades\Schema::hasColumn('mahasiswa', 'status_mahasiswa')) {
            \Illuminate\Support\Facades\Schema::table('mahasiswa', function (\Illuminate\Database\Schema\Blueprint $table) {
                $table->string('status_mahasiswa', 10)->default('baru');
            });
        }

        if (!\Illuminate\Support\Facades\Schema::hasColumn('mahasiswa', 'is_aktif')) {
            \Illuminate\Support\Facades\Schema::table('mahasiswa', function (\Illuminate\Database\Schema\Blueprint $table) {
                $table->boolean('is_aktif')->default(true);
            });
        }

        $tab = $request->input('tab', 'dosen');

        $periodeIdForStatus = session('selected_periode_id') ? (int) session('selected_periode_id') : null;
        $statusMahasiswaSql = $periodeIdForStatus 
            ? "CASE 
                 WHEN mahasiswa.tahun_ajaran_id = {$periodeIdForStatus} THEN mahasiswa.status_mahasiswa
                 ELSE COALESCE((
                     SELECT CASE WHEN is_lanjutan = true THEN 'lanjut' ELSE 'baru' END
                     FROM pendaftaran_kp 
                     WHERE pendaftaran_kp.mahasiswa_id = users.id 
                       AND pendaftaran_kp.tahun_ajaran_id = {$periodeIdForStatus}
                     LIMIT 1
                 ), 'baru')
               END"
            : "mahasiswa.status_mahasiswa";

        $query = User::leftJoin('mahasiswa', 'users.id', '=', 'mahasiswa.user_id')
            ->leftJoin('dosen', 'users.id', '=', 'dosen.user_id')
            ->select('users.*', DB::raw('COALESCE(mahasiswa.nim, dosen.nidn) as identifier_id'), DB::raw('COALESCE(dosen.is_aktif, mahasiswa.is_aktif) as is_aktif'), DB::raw("($statusMahasiswaSql) as status_mahasiswa"));

        if ($tab === 'dosen') {
            $query->whereIn('users.role', ['dosen', 'koordinator_kp']);
        } else {
            $query->where('users.role', 'mahasiswa');
            // Filter Mahasiswa by selected period
            if (session()->has('selected_periode_id')) {
                $periodeId = session('selected_periode_id');
                $query->where(function($q) use ($periodeId) {
                    $q->where('mahasiswa.tahun_ajaran_id', $periodeId)
                      ->orWhereExists(function ($sub) use ($periodeId) {
                          $sub->select(DB::raw(1))
                              ->from('pendaftaran_kp')
                              ->whereColumn('pendaftaran_kp.mahasiswa_id', 'users.id')
                              ->where('pendaftaran_kp.tahun_ajaran_id', $periodeId)
                              ->where(function($q) {
                                  $q->whereNotNull('pendaftaran_kp.status_kp')
                                    ->orWhereRaw('pendaftaran_kp.id = (SELECT MIN(id) FROM pendaftaran_kp AS pkp2 WHERE pkp2.mahasiswa_id = users.id)');
                              });
                      });
                });
            }
        }

        if ($request->filled('search')) {
            $search = strtolower($request->search);
            $query->where(function ($q) use ($search) {
                $q->whereRaw('LOWER(users.name) LIKE ?', ["%{$search}%"])
                    ->orWhereRaw('LOWER(users.email) LIKE ?', ["%{$search}%"])
                    ->orWhereRaw('LOWER(mahasiswa.nim) LIKE ?', ["%{$search}%"])
                    ->orWhereRaw('LOWER(dosen.nidn) LIKE ?', ["%{$search}%"]);
            });
        }

        if ($request->filled('role_filter')) {
            $query->where('users.role', $request->role_filter);
        }

        if ($request->filled('status')) {
            if ($request->status === 'Aktif') {
                $query->where('dosen.is_aktif', true);
            } elseif ($request->status === 'Tidak Aktif') {
                $query->where('dosen.is_aktif', false);
            }
        }

        if ($tab === 'mahasiswa' && $request->filled('status_mahasiswa')) {
            $query->whereRaw("($statusMahasiswaSql) = ?", [$request->status_mahasiswa]);
        }

        $users = $query->orderBy('users.created_at', 'desc')->paginate(10);

        if ($request->ajax()) {
            return response()->json([
                'users' => $users->items(),
                'pagination' => [
                    'current_page' => $users->currentPage(),
                    'last_page' => $users->lastPage(),
                    'per_page' => $users->perPage(),
                    'total' => $users->total(),
                    'from' => $users->firstItem(),
                    'to' => $users->lastItem(),
                ],
            ]);
        }

        $users->withQueryString();

        $activePeriode = \App\Models\TahunAjaran::aktif();
        $selectedPeriodeId = session('selected_periode_id') ?? $activePeriode?->id;
        $isReadOnly = $activePeriode && $selectedPeriodeId != $activePeriode->id;
        
        $user = auth()->user();
        if (in_array($user->role, ['dosen', 'koordinator_kp']) && $user->dosen && !$user->dosen->is_aktif) {
            $isReadOnly = true;
        }

        return view('koordinator.manajemen-user', compact('users', 'tab', 'isReadOnly'));
    }

    /**
     * Menambah user baru ke database PostgreSQL
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'id_user' => 'required|string|min:9',
            'email' => 'required|email',
            'role' => 'required|in:mahasiswa,dosen,koordinator_kp',
        ], [
            'id_user.min' => 'ID (NIM/NIDN/NIDK) minimal harus 9 digit/karakter.',
        ]);

        // Pastikan pendaftaran manual selalu masuk ke periode AKTIF secara global, bukan bergantung pada filter dropdown
        $activePeriodId = \App\Models\TahunAjaran::aktif()?->id;

        // Validasi dan Pengecekan Duplikat ID
        if ($request->role === 'mahasiswa') {
            $existingMahasiswa = DB::table('mahasiswa')->where('nim', $request->id_user)->first();
            
            if ($existingMahasiswa) {
                // Jika sudah ada di periode yang sama
                if ($existingMahasiswa->tahun_ajaran_id == $activePeriodId) {
                    return back()->withErrors(['id_user' => 'Mahasiswa dengan NIM ini sudah terdaftar di periode saat ini.'])->withInput();
                }

                $latestSidang = DB::table('pendaftaran_sidang')
                    ->where('mahasiswa_id', $existingMahasiswa->user_id)
                    ->orderBy('id', 'desc')
                    ->first();

                $isAllowed = false;

                if (!$latestSidang) {
                    $isAllowed = true;
                } else {
                    if (in_array($latestSidang->status_kelulusan, ['Lulus', 'Lulus Dengan Revisi']) && !in_array($latestSidang->grade, ['D', 'E'])) {
                        $isAllowed = false;
                    } else {
                        $isAllowed = true;
                    }
                }

                if (!$isAllowed) {
                    return back()->withErrors(['id_user' => 'Penambahan ditolak: Mahasiswa ini sudah dinyatakan Lulus / Lulus Dengan Revisi di periode sebelumnya dan tidak memenuhi syarat mengulang (Lanjut/Tidak ikut sidang/Nilai D/E).'])->withInput();
                }

                // Cek apakah email baru ini bentrok dengan user LAIN
                $emailConflict = User::where('email', $request->email)->where('id', '!=', $existingMahasiswa->user_id)->first();
                if ($emailConflict) {
                    return back()->withErrors(['email' => 'Email ini sudah digunakan oleh pengguna lain.'])->withInput();
                }

                // Lolos pengecekan, perbarui data mahasiswa ini ke periode saat ini (status = lanjut)
                DB::transaction(function () use ($request, $existingMahasiswa, $activePeriodId) {
                    User::where('id', $existingMahasiswa->user_id)->update([
                        'name' => $request->name,
                        'email' => $request->email,
                    ]);
                    DB::table('mahasiswa')->where('nim', $request->id_user)->update([
                        'email' => $request->email,
                        'tahun_ajaran_id' => $activePeriodId,
                        'status_mahasiswa' => 'lanjut',
                    ]);
                });

                return back()->with('success', 'Mahasiswa (Lanjut) berhasil diperbarui ke periode saat ini.');
            }
        } elseif (in_array($request->role, ['dosen', 'koordinator_kp'])) {
            $existingDosen = DB::table('dosen')->where('nidn', $request->id_user)->first();
            if ($existingDosen) {
                return back()->withErrors(['id_user' => 'Dosen/Koordinator dengan ID ini sudah terdaftar.'])->withInput();
            }
        }

        // Cek duplikasi email untuk user baru
        $existingEmail = User::where('email', $request->email)->first();
        if ($existingEmail) {
            return back()->withErrors(['email' => 'Email ini sudah digunakan oleh pengguna lain.'])->withInput();
        }

        // Eksekusi Pembuatan User Baru
        DB::transaction(function () use ($request, $activePeriodId) {
            $user = User::create([
                'name'     => $request->name,
                'email'    => $request->email,
                'password' => bcrypt($request->id_user), // Password default disamakan dengan ID (NIM/NIDN/NIDK)
                'role'     => $request->role,
            ]);

            if ($request->role === 'mahasiswa') {
                DB::table('mahasiswa')->insert([
                    'user_id' => $user->id,
                    'nim' => $request->id_user,
                    'email' => $request->email,
                    'prodi' => 'Informatika',
                    'tahun_ajaran_id' => $activePeriodId,
                    'status_mahasiswa' => 'baru',
                ]);
            } elseif (in_array($request->role, ['dosen', 'koordinator_kp'])) {
                DB::table('dosen')->insert([
                    'user_id' => $user->id,
                    'nidn' => $request->id_user,
                    'is_aktif' => true,
                ]);
            }
        });

        return back()->with('success', 'User berhasil ditambahkan.');
    }

    /**
     * Menampilkan halaman Edit Akses User
     */
    public function edit($id)
    {
        $user = User::findOrFail($id);

        $detail = null;
        if (in_array($user->role, ['dosen', 'koordinator_kp'])) {
            $detail = DB::table('dosen')->where('user_id', $user->id)->first();
        } elseif ($user->role === 'mahasiswa') {
            $detail = DB::table('mahasiswa')->where('user_id', $user->id)->first();
        }

        // Nama file view disesuaikan: manajemen-user-edit-user.blade.php
        return view('koordinator.manajemen-user-edit-user', compact('user', 'detail'));
    }

    /**
     * Menyimpan perubahan ke database dan kembali ke route manajemen-akses
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,'.$id,
            'role' => 'required|in:mahasiswa,dosen,koordinator_kp',
        ], [
            'name.required' => 'Nama tidak boleh kosong.',
            'email.required' => 'Email tidak boleh kosong.',
            'email.unique' => 'Email ini sudah digunakan oleh pengguna lain.',
            'role.required' => 'Role tidak boleh kosong.',
        ]);

        $user = User::findOrFail($id);

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
        ]);

        if (in_array($user->role, ['dosen', 'koordinator_kp'])) {
            DB::table('dosen')->where('user_id', $id)->update([
                'is_aktif' => $request->status === 'Aktif',
            ]);
        } elseif ($user->role === 'mahasiswa') {
            DB::table('mahasiswa')->where('user_id', $id)->update([
                'is_aktif' => $request->status === 'Aktif',
            ]);
        }

        return redirect()->route('koordinator.manajemen-akses')->with('success', 'Data akses berhasil diperbarui.');
    }

    /**
     * Memperbarui hanya status aktif/tidak aktif dosen langsung dari tabel utama
     */
    public function updateStatus(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $success = false;

        if (in_array($user->role, ['dosen', 'koordinator_kp'])) {
            $success = (bool) DB::table('dosen')->where('user_id', $id)->update([
                'is_aktif' => $request->status === 'Aktif',
            ]);
        } elseif ($user->role === 'mahasiswa') {
            $success = (bool) DB::table('mahasiswa')->where('user_id', $id)->update([
                'is_aktif' => $request->status === 'Aktif',
            ]);
        }

        if ($request->ajax()) {
            return response()->json([
                'success' => $success,
                'message' => 'Status dosen '.$user->name.' berhasil diperbarui.',
            ]);
        }

        return back()->with('success', 'Status dosen '.$user->name.' berhasil diperbarui.');
    }

    public function import(Request $request)
    {
        if ($request->isMethod('get')) {
            $validRows = session('import_users_preview', []);
            $duplicateRows = session('duplicateRows', []);
            if (empty($validRows) && empty($duplicateRows)) {
                return redirect()->route('koordinator.manajemen-akses')->with('error', 'Sesi pratinjau tidak ditemukan atau sudah kadaluarsa. Silakan unggah ulang file Excel Anda.');
            }
            return view('koordinator.manajemen-user-preview-import-user', compact('validRows'));
        }

        $request->validate([
            'file_import' => 'required|mimes:xlsx,xls',
        ], [
            'file_import.required' => 'Gagal mengunggah: Silakan pilih file Excel terlebih dahulu.',
            'file_import.mimes' => 'Format file tidak valid! Pastikan file yang diunggah hanyalah berformat Excel (.xlsx atau .xls) berbahasa Indonesia.',
        ]);

        $data = Excel::toArray(new UsersImport, $request->file('file_import'));
        $rows = $data[0] ?? []; 

        $validRows = [];
        $duplikatAtauDitolak = [];

        // Ambil data eksisting untuk cek di memory (efisiensi)
        $existingUsers = User::with(['mahasiswa'])->get();
        $eksisEmails = $existingUsers->pluck('email')->toArray();
        
        $eksisNids = DB::table('dosen')->pluck('nidn')->toArray();
        $eksisNims = DB::table('mahasiswa')->pluck('nim')->toArray();
        $eksisIds = array_merge($eksisNids, $eksisNims);

        foreach ($rows as $row) {
            if (!empty($row['nama']) && !empty($row['email'])) {
                $role = strtolower(str_replace(' ', '_', $row['role'] ?? ''));
                $rowId = (string)$row['id'];
                $rowEmail = strtolower($row['email']);

                if (strlen($rowId) < 9) {
                    $duplikatAtauDitolak[] = [
                        'nama' => $row['nama'],
                        'id' => $rowId,
                        'email' => $rowEmail,
                        'role' => $role,
                        'keterangan' => 'Ditolak: ID kurang dari 9 karakter',
                        'existing' => ['nama' => '-', 'email' => '-', 'status' => '-']
                    ];
                    continue;
                }

                // Cek apakah mahasiswa ini sudah ada
                if ($role === 'mahasiswa' && in_array($rowId, $eksisNims)) {
                    $userRecord = $existingUsers->where('email', $rowEmail)->first();
                    // Jika beda email, mungkin konflik
                    if (!$userRecord) {
                        $mhsRecord = DB::table('mahasiswa')->where('nim', $rowId)->first();
                        $userRecord = $existingUsers->where('id', $mhsRecord->user_id)->first();
                    }

                    // Cek kelayakan mahasiswa eksisting untuk mengulang
                    $isAllowed = false;
                    $notAllowedMessage = '';
                    $activePeriodId = session('selected_periode_id') ?? \App\Models\TahunAjaran::aktif()?->id;
                    $mhsRecord = DB::table('mahasiswa')->where('user_id', $userRecord->id)->first();
                    
                    $latestKp = null;
                    $latestSidang = null;
                    
                    if ($mhsRecord && $mhsRecord->tahun_ajaran_id == $activePeriodId) {
                        $isAllowed = false;
                        $notAllowedMessage = 'Ditolak: Mahasiswa dengan NIM ini sudah terdaftar dan aktif di periode saat ini. Penambahan pengguna diblokir.';
                    } else {
                        $latestSidang = DB::table('pendaftaran_sidang')
                            ->where('mahasiswa_id', $userRecord->id)
                            ->orderBy('id', 'desc')
                            ->first();

                        if (!$latestSidang) {
                            $isAllowed = true;
                        } else {
                            if (in_array($latestSidang->status_kelulusan, ['Lulus', 'Lulus Dengan Revisi']) && !in_array($latestSidang->grade, ['D', 'E'])) {
                                $isAllowed = false;
                            } else {
                                $isAllowed = true;
                            }
                        }
                        
                        $latestKp = DB::table('pendaftaran_kp')
                            ->where(function($query) use ($userRecord) {
                                $query->where('mahasiswa_id', $userRecord->id)
                                      ->orWhereJsonContains('anggota_kelompok_ids', (string) $userRecord->id)
                                      ->orWhereJsonContains('anggota_kelompok_ids', $userRecord->id);
                            })
                            ->orderBy('id', 'desc')
                            ->first();
                        if (!$isAllowed && !$notAllowedMessage) {
                            $notAllowedMessage = 'Ditolak: Mahasiswa sudah Lulus / Lulus Dengan Revisi. Penambahan pengguna diblokir.';
                        }
                    }

                    $statusLama = 'Belum KP';
                    if ($latestKp && isset($latestSidang) && $latestSidang) {
                        $statusLama = $latestSidang->status_kelulusan;
                        if (!$statusLama) $statusLama = 'Sedang KP';
                    } elseif ($latestKp) {
                        $statusLama = 'Sedang KP';
                    }

                    $existingData = [
                        'nama' => $userRecord->name,
                        'email' => $userRecord->email,
                        'status' => $statusLama
                    ];

                    if (!$isAllowed) {
                        $duplikatAtauDitolak[] = [
                            'nama' => $row['nama'],
                            'id' => $rowId,
                            'email' => $rowEmail,
                            'role' => $role,
                            'keterangan' => $notAllowedMessage,
                            'existing' => $existingData
                        ];
                        continue;
                    }

                    $duplikatAtauDitolak[] = [
                        'nama' => $row['nama'],
                        'id' => $rowId,
                        'email' => $rowEmail,
                        'role' => $role,
                        'keterangan' => 'Diterima: Lanjut',
                        'existing' => $existingData
                    ];

                    // Jika diizinkan mengulang, masukkan ke validRows tapi dengan tanda 'is_update'
                    $validRows[] = [
                        'nama' => $row['nama'],
                        'id' => $rowId,
                        'email' => $rowEmail,
                        'role' => $role,
                        'is_update' => true, // Flag untuk update
                        'user_id' => $userRecord->id
                    ];
                    continue;
                }

                // Cek Dosen/Koordinator atau konflik email bagi user baru
                $isDuplicateEmail = in_array($rowEmail, array_map('strtolower', $eksisEmails));
                $isDuplicateId = in_array($rowId, $eksisIds);

                if ($isDuplicateEmail || $isDuplicateId) {
                    $existingDosen = null;
                    if ($isDuplicateId) {
                        $existingMhs = DB::table('mahasiswa')->where('nim', $rowId)->first();
                        if ($existingMhs) {
                            $u = DB::table('users')->where('id', $existingMhs->user_id)->first();
                            $existingDosen = ['nama' => $u->name, 'email' => $u->email, 'status' => 'Mahasiswa'];
                        } else {
                            $existingDsn = DB::table('dosen')->where('nidn', $rowId)->first();
                            if ($existingDsn) {
                                $u = DB::table('users')->where('id', $existingDsn->user_id)->first();
                                $existingDosen = ['nama' => $u->name, 'email' => $u->email, 'status' => 'Dosen/Koordinator'];
                            }
                        }
                    } else {
                        $u = DB::table('users')->where('email', $rowEmail)->first();
                        if ($u) {
                            $existingDosen = ['nama' => $u->name, 'email' => $u->email, 'status' => ucfirst($u->role)];
                        }
                    }

                    $duplikatAtauDitolak[] = [
                        'nama' => $row['nama'],
                        'id' => $rowId,
                        'email' => $rowEmail,
                        'role' => $role,
                        'keterangan' => 'Ditolak: Duplikat ID/Email',
                        'existing' => $existingDosen ?? ['nama' => '-', 'email' => '-', 'status' => '-']
                    ];
                    continue;
                }

                $validRows[] = [
                    'nama' => $row['nama'],
                    'id' => $rowId,
                    'email' => $rowEmail,
                    'role' => $role,
                    'is_update' => false
                ];

                $eksisEmails[] = $rowEmail;
                $eksisIds[] = $rowId;
            }
        }

        if (empty($validRows) && empty($duplikatAtauDitolak)) {
            return back()->with('error', 'File tidak memiliki baris data yang valid! Pastikan Anda mengisi kolom nama dan email.');
        }

        if (count($duplikatAtauDitolak) > 0) {
            session(['duplicateRows' => $duplikatAtauDitolak]);
        } else {
            session()->forget('duplicateRows');
        }

        session(['import_users_preview' => $validRows]);

        return view('koordinator.manajemen-user-preview-import-user', compact('validRows'));
    }

    /**
     * Konfirmasi Import Massal dan Masukkan Database
     */
    public function confirmImport(Request $request)
    {
        $rows = $request->input('users', []);

        if (empty($rows)) {
            return redirect()->route('koordinator.manajemen-akses')->with('error', 'Tidak ada data user yang dikirim untuk diimport.');
        }

        $activePeriodId = session('selected_periode_id') ?? \App\Models\TahunAjaran::aktif()?->id;

        DB::transaction(function () use ($rows, $activePeriodId) {
            foreach ($rows as $row) {
                if (empty($row['nama']) || empty($row['email'])) {
                    continue;
                }

                if (empty($row['id']) || strlen((string)$row['id']) < 9) {
                    continue;
                }

                $isUpdate = isset($row['is_update']) && $row['is_update'] == '1';

                if ($isUpdate && isset($row['user_id'])) {
                    // Update user eksisting (Mahasiswa Mengulang)
                    User::where('id', $row['user_id'])->update([
                        'name' => $row['nama'],
                        'email' => $row['email'],
                    ]);
                    DB::table('mahasiswa')->where('user_id', $row['user_id'])->update([
                        'email' => $row['email'],
                        'tahun_ajaran_id' => $activePeriodId,
                        'status_mahasiswa' => 'lanjut',
                    ]);
                } else {
                    // Create user baru
                    $user = User::create([
                        'name'     => $row['nama'],
                        'email'    => $row['email'],
                        'password' => bcrypt($row['id']),
                        'role'     => strtolower(str_replace(' ', '_', $row['role'])),
                    ]);

                    if ($user->role === 'mahasiswa') {
                        DB::table('mahasiswa')->insert([
                            'user_id' => $user->id,
                            'nim' => $row['id'],
                            'email' => $row['email'],
                            'prodi' => 'Informatika',
                            'tahun_ajaran_id' => $activePeriodId,
                            'status_mahasiswa' => 'baru',
                        ]);
                    } elseif (in_array($user->role, ['dosen', 'koordinator_kp'])) {
                        DB::table('dosen')->insert([
                            'user_id' => $user->id,
                            'nidn' => $row['id'],
                            'is_aktif' => true,
                        ]);
                    }
                }
            }
        });

        session()->forget(['import_users_preview', 'duplicateRows']);

        return redirect()->route('koordinator.manajemen-akses')->with('success', 'Pendaftaran dan pembaruan '.count($rows).' data user berhasil dieksekusi secara penuh!');
    }

    /**
     * Download Template Import Excel
     */
    public function downloadTemplate()
    {
        return Excel::download(new UsersTemplateExport, 'Template_Data_User.xlsx');
    }

    /**
     * Menghapus user beserta data spesifik per role
     */
    public function destroy($id)
    {
        $user = User::find($id);

        if (!$user) {
            if (request()->ajax()) {
                return response()->json(['success' => false, 'message' => 'User tidak ditemukan atau sudah dihapus sebelumnya.']);
            }
            return back()->with('error', 'User tidak ditemukan atau sudah dihapus sebelumnya.');
        }

        // Prevent self deletion
        if ($user->id === auth()->id()) {
            if (request()->ajax()) {
                return response()->json(['success' => false, 'message' => 'Akses ditolak: Anda tidak dapat menghapus akun Anda sendiri.']);
            }

            return back()->with('error', 'Akses ditolak: Anda tidak dapat menghapus akun Anda sendiri.');
        }

        // Prevent deleting the only Koordinator? Optional, but self-deletion covers the immediate case.

        DB::transaction(function () use ($user) {
            if (in_array($user->role, ['dosen', 'koordinator_kp'])) {
                DB::table('dosen')->where('user_id', $user->id)->delete();
                $user->delete();
            } elseif ($user->role === 'mahasiswa') {
                $activePeriodId = session('selected_periode_id') ?? \App\Models\TahunAjaran::aktif()?->id;

                // Cek apakah punya riwayat KP di periode selain periode saat ini
                $pastKps = DB::table('pendaftaran_kp')
                    ->where('mahasiswa_id', $user->id)
                    ->where('tahun_ajaran_id', '!=', $activePeriodId)
                    ->whereNotNull('tahun_ajaran_id')
                    ->orderBy('tahun_ajaran_id', 'desc')
                    ->get();

                if ($pastKps->count() > 0) {
                    // Hanya "keluarkan" dari periode saat ini, rollback ke periode KP terakhirnya
                    $lastPeriod = $pastKps->first()->tahun_ajaran_id;
                    DB::table('mahasiswa')->where('user_id', $user->id)->update([
                        'tahun_ajaran_id' => $lastPeriod,
                    ]);
                    
                    // Hapus draft pendaftaran KP milik dia di periode saat ini (jika ada) agar tidak tertinggal
                    DB::table('pendaftaran_kp')
                        ->where('mahasiswa_id', $user->id)
                        ->where('tahun_ajaran_id', $activePeriodId)
                        ->delete();
                } else {
                    // Jika memang tidak ada riwayat KP di periode lain, hapus permanen ke akarnya
                    DB::table('mahasiswa')->where('user_id', $user->id)->delete();
                    $user->delete();
                }
            }
        });

        if (request()->ajax()) {
            return response()->json(['success' => true, 'message' => 'User berhasil dihapus.']);
        }

        return back()->with('success', 'User berhasil dihapus.');
    }

    /**
     * Export Data User ke PDF
     */
    public function exportPdf(Request $request)
    {
        $type = $request->query('type', 'semua');

        $periodeIdForStatus = session('selected_periode_id') ? (int) session('selected_periode_id') : null;
        $statusMahasiswaSql = $periodeIdForStatus 
            ? "CASE 
                 WHEN mahasiswa.tahun_ajaran_id = {$periodeIdForStatus} THEN mahasiswa.status_mahasiswa
                 ELSE COALESCE((
                     SELECT CASE WHEN is_lanjutan = true THEN 'lanjut' ELSE 'baru' END
                     FROM pendaftaran_kp 
                     WHERE pendaftaran_kp.mahasiswa_id = users.id 
                       AND pendaftaran_kp.tahun_ajaran_id = {$periodeIdForStatus}
                     LIMIT 1
                 ), 'baru')
               END"
            : "mahasiswa.status_mahasiswa";

        $query = User::leftJoin('mahasiswa', 'users.id', '=', 'mahasiswa.user_id')
            ->leftJoin('dosen', 'users.id', '=', 'dosen.user_id')
            ->select('users.*', DB::raw('COALESCE(mahasiswa.nim, dosen.nidn) as identifier_id'), 'dosen.is_aktif', DB::raw("($statusMahasiswaSql) as status_mahasiswa"));

        $title = 'DAFTAR SELURUH PENGGUNA SISTEM';

        if ($type === 'dosen') {
            $query->whereIn('users.role', ['dosen', 'koordinator_kp']);
            $title = 'DAFTAR DATA DOSEN DAN KOORDINATOR KP';
        } elseif ($type === 'mahasiswa') {
            $query->where('users.role', 'mahasiswa');
            if (session()->has('selected_periode_id')) {
                $periodeId = session('selected_periode_id');
                $query->where(function($q) use ($periodeId) {
                    $q->where('mahasiswa.tahun_ajaran_id', $periodeId)
                      ->orWhereExists(function ($sub) use ($periodeId) {
                          $sub->select(DB::raw(1))
                              ->from('pendaftaran_kp')
                              ->whereColumn('pendaftaran_kp.mahasiswa_id', 'users.id')
                              ->where('pendaftaran_kp.tahun_ajaran_id', $periodeId);
                      });
                });
            }
            $title = 'DAFTAR DATA MAHASISWA';
        }

        $users = $query->orderBy('users.role', 'desc')
            ->orderBy('users.name', 'asc')
            ->get();

        $pdf = Pdf::loadView('koordinator.pdf.users', compact('users', 'title', 'type'));

        // Setup paper size
        $pdf->setPaper('A4', 'portrait');

        $filename = 'Laporan_Data_User_'.ucfirst($type).'_'.date('Ymd_His').'.pdf';

        return $pdf->stream($filename);
    }
}
