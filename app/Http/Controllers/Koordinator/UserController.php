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
            ->select('users.name', 'users.email', 'users.role')
            ->first();

        if ($mhs) {
            return response()->json([
                'exists' => true,
                'role_type' => 'mahasiswa',
                'name' => $mhs->name,
                'email' => $mhs->email,
                'role' => $mhs->role == 'mahasiswa' ? 'Mahasiswa' : ucfirst($mhs->role)
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

        $query = User::leftJoin('mahasiswa', 'users.id', '=', 'mahasiswa.user_id')
            ->leftJoin('dosen', 'users.id', '=', 'dosen.user_id')
            ->select('users.*', DB::raw('COALESCE(mahasiswa.nim, dosen.nidn) as identifier_id'), DB::raw('COALESCE(dosen.is_aktif, mahasiswa.is_aktif) as is_aktif'), 'mahasiswa.status_mahasiswa');

        if ($tab === 'dosen') {
            $query->whereIn('users.role', ['dosen', 'koordinator_kp']);
        } else {
            $query->where('users.role', 'mahasiswa');
            // Filter Mahasiswa by selected period
            if (session()->has('selected_periode_id')) {
                $periodeId = session('selected_periode_id');
                $query->where(function($q) use ($periodeId) {
                    $q->where('mahasiswa.tahun_ajaran_id', $periodeId)
                      ->orWhereIn('users.id', function($sub) use ($periodeId) {
                          $sub->select('mahasiswa_id')->from('pendaftaran_kp')->where('tahun_ajaran_id', $periodeId);
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
            $query->where('mahasiswa.status_mahasiswa', $request->status_mahasiswa);
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

        return view('koordinator.manajemen-user', compact('users', 'tab'));
    }

    /**
     * Menambah user baru ke database PostgreSQL
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'id_user' => 'required|string',
            'email' => 'required|email',
            'role' => 'required|in:mahasiswa,dosen,koordinator_kp',
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

                // Cek data KP terakhir
                $latestKp = DB::table('pendaftaran_kp')
                    ->where('mahasiswa_id', $existingMahasiswa->user_id)
                    ->orderBy('id', 'desc')
                    ->first();

                $isAllowed = false;

                if (!$latestKp) {
                    $isAllowed = true; // Belum pernah mendaftar KP sama sekali
                } else {
                    $latestSidang = DB::table('pendaftaran_sidang')
                        ->where('pendaftaran_kp_id', $latestKp->id)
                        ->first();

                    if (!$latestSidang) {
                        $isAllowed = true; // Tidak mengikuti sidang
                    } else {
                        if (in_array($latestSidang->status_kelulusan, ['Lanjut', 'Tidak Lulus'])) {
                            $isAllowed = true;
                        } elseif (in_array($latestSidang->grade, ['D', 'E'])) {
                            $isAllowed = true;
                        }
                    }
                }

                if (!$isAllowed) {
                    return back()->withErrors(['id_user' => 'Penambahan ditolak: Mahasiswa ini sudah dinyatakan Lulus di periode sebelumnya dan tidak memenuhi syarat mengulang (Lanjut/Tidak ikut sidang/Nilai D/E).'])->withInput();
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
        $request->validate([
            'file_import' => 'required|mimes:xlsx,xls',
        ], [
            'file_import.required' => 'Gagal mengunggah: Silakan pilih file Excel terlebih dahulu.',
            'file_import.mimes' => 'Format file tidak valid! Pastikan file yang diunggah hanyalah berformat Excel (.xlsx atau .xls) berbahasa Indonesia.',
        ]);

        // Mengambil data sebagai Array
        $data = Excel::toArray(new UsersImport, $request->file('file_import'));

        $rows = $data[0] ?? []; // Ambil worksheet pertama
        $validRows = [];
        $duplikatData = [];

        $eksisEmails = User::pluck('email')->toArray();
        $eksisNids = DB::table('dosen')->pluck('nidn')->toArray();
        $eksisNims = DB::table('mahasiswa')->pluck('nim')->toArray();
        $eksisIds = array_merge($eksisNids, $eksisNims);

        foreach ($rows as $row) {
            // Evaluasi baris kosong
            if (! empty($row['nama']) && ! empty($row['email'])) {
                $role = strtolower(str_replace(' ', '_', $row['role'] ?? ''));
                $isDuplicateEmail = in_array(strtolower($row['email']), array_map('strtolower', $eksisEmails));
                $isDuplicateId = in_array($row['id'], $eksisIds);

                if ($isDuplicateEmail || $isDuplicateId) {
                    $duplikatData[] = [
                        'nama' => $row['nama'],
                        'id' => $row['id'],
                        'email' => $row['email'],
                        'role' => $role,
                        'is_duplicate_email' => $isDuplicateEmail,
                        'is_duplicate_id' => $isDuplicateId,
                    ];

                    continue;
                }

                $validRows[] = [
                    'nama' => $row['nama'],
                    'id' => $row['id'],
                    'email' => $row['email'],
                    'role' => $role,
                ];

                $eksisEmails[] = $row['email'];
                $eksisIds[] = $row['id'];
            }
        }

        if (empty($validRows) && empty($duplikatData)) {
            return back()->with('error', 'File tidak memiliki baris data yang valid! Pastikan Anda mengisi kolom nama dan email.');
        }

        if (count($duplikatData) > 0) {
            session()->now('duplicateRows', $duplikatData);
        }

        // Menyimpan array ke dalam session untuk ditampilkan di halaman preview
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

        $eksisEmails = User::pluck('email')->toArray();
        $eksisNids = DB::table('dosen')->pluck('nidn')->toArray();
        $eksisNims = DB::table('mahasiswa')->pluck('nim')->toArray();
        $eksisIds = array_merge($eksisNids, $eksisNims);

        $duplikatData = [];
        $validRowsFinal = [];

        foreach ($rows as $row) {
            if (empty($row['nama']) || empty($row['email'])) {
                continue; // skip if somehow empty
            }

            if (in_array(strtolower($row['email']), array_map('strtolower', $eksisEmails)) || in_array($row['id'], $eksisIds)) {
                $duplikatData[] = [
                    'nama' => $row['nama'],
                    'id' => $row['id'],
                    'email' => $row['email'],
                    'role' => $row['role'],
                ];

                continue; // Skip the insertion for this row if duplicate
            }

            $validRowsFinal[] = $row;
            // Add to simulated existing arrays so we catch duplicates WITHIN the imported file itself
            $eksisEmails[] = $row['email'];
            $eksisIds[] = $row['id'];
        }

        if (empty($validRowsFinal) && count($duplikatData) > 0) {
            return redirect()->route('koordinator.manajemen-akses')->with('error', 'Semua data gagal diimport karena Duplikat email atau ID.');
        }

        DB::transaction(function () use ($validRowsFinal) {
            foreach ($validRowsFinal as $row) {
                // 1. Simpan ke tabel users
                $user = User::create([
                    'name'     => $row['nama'],
                    'email'    => $row['email'],
                    'password' => bcrypt($row['id']), // Password default disamakan dengan ID (NIM/NIDN/NIDK)
                    'role'     => strtolower(str_replace(' ', '_', $row['role'])),
                ]);

                // 2. Simpan ke tabel detail
                if ($user->role === 'mahasiswa') {
                    DB::table('mahasiswa')->insert([
                        'user_id' => $user->id,
                        'nim' => $row['id'],
                        'email' => $row['email'],
                        'prodi' => 'Informatika',
                        'tahun_ajaran_id' => session('selected_periode_id') ?? \App\Models\TahunAjaran::aktif()?->id,
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
        });

        session()->forget('import_users_preview');

        if (count($duplikatData) > 0) {
            return redirect()->route('koordinator.manajemen-akses')->with('success', count($validRowsFinal).' user berhasil didaftarkan. Beberapa dilewati karena duplikat.');
        }

        return redirect()->route('koordinator.manajemen-akses')->with('success', 'Seluruh data user ('.count($validRowsFinal).') berhasil diimport dan didaftarkan.');
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

        $query = User::leftJoin('mahasiswa', 'users.id', '=', 'mahasiswa.user_id')
            ->leftJoin('dosen', 'users.id', '=', 'dosen.user_id')
            ->select('users.*', DB::raw('COALESCE(mahasiswa.nim, dosen.nidn) as identifier_id'), 'dosen.is_aktif', 'mahasiswa.status_mahasiswa');

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
                      ->orWhereIn('users.id', function($sub) use ($periodeId) {
                          $sub->select('mahasiswa_id')->from('pendaftaran_kp')->where('tahun_ajaran_id', $periodeId);
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
