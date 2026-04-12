<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Koordinator\UserController;
use App\Http\Controllers\Koordinator\PendaftaranKpController as KoordinatorPendaftaranKpController;
use App\Http\Controllers\Mahasiswa\PendaftaranKpController as MahasiswaPendaftaranKpController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// ==========================================
// ROUTE KOORDINATOR (Sudah Disatukan & Dirapihkan)
// ==========================================
Route::prefix('koordinator')->name('koordinator.')->middleware(['auth', 'verified'])->group(function() {
    
    // 1. Dashboard Koordinator
    Route::get('/dashboard', function() {
        return view('koordinator.dashboard', ['active' => 'dashboard']);
    })->name('dashboard');

    // 2. Route Asli Manajemen Akses (Harus di atas route dummy)
    Route::get('/manajemen-akses', [UserController::class, 'index'])->name('manajemen-akses');
    Route::post('/manajemen-akses/store', [UserController::class, 'store'])->name('user.store');
    Route::get('/manajemen-akses/export-pdf', [UserController::class, 'exportPdf'])->name('user.export-pdf');
    Route::get('/manajemen-akses/{id}/edit', [UserController::class, 'edit'])->name('user.edit');
    Route::put('/manajemen-akses/{id}/update', [UserController::class, 'update'])->name('user.update');
    Route::put('/manajemen-akses/{id}/status', [UserController::class, 'updateStatus'])->name('user.update-status');
    Route::delete('/manajemen-akses/{id}/destroy', [UserController::class, 'destroy'])->name('user.destroy');
    Route::post('/manajemen-akses/import', [UserController::class, 'import'])->name('user.import');
    Route::post('/manajemen-akses/import/confirm', [UserController::class, 'confirmImport'])->name('user.import.confirm');
    Route::get('/manajemen-akses/template/download', [UserController::class, 'downloadTemplate'])->name('user.template.download');
    
    // 3. Pendaftaran KP Koordinator
    Route::get('/pendaftaran-kp', [KoordinatorPendaftaranKpController::class, 'index'])->name('pendaftaran-kp');
    Route::get('/pendaftaran-kp/detail/{slug}', [KoordinatorPendaftaranKpController::class, 'show'])->name('pendaftaran-kp.show');
    Route::put('/pendaftaran-kp/{id}/status', [KoordinatorPendaftaranKpController::class, 'updateStatus'])->name('pendaftaran-kp.status');

    // 4. Catch-all for dummy routes on sidebar (Harus paling bawah di grup ini)
    Route::get('/{page}', function($page) {
        $titles = [
            'timeline' => 'Timeline KP',
            'data-mhs' => 'Data Mahasiswa KP', 'pembimbing' => 'Pembimbing',
            'pelaksanaan' => 'Pelaksanaan KP', 'verifikasi' => 'Verifikasi Berkas',
            'penjadwalan' => 'Penjadwalan Sidang', 'penguji' => 'Dosen Penguji',
            'kalender' => 'Kalender Sidang', 'revisi' => 'Revisi',
            'nilai-akhir' => 'Nilai Akhir', 'berita-acara' => 'Berita Acara',
            'laporan' => 'Laporan KP', 'sistem' => 'Sistem',
            'pengumuman' => 'Pengumuman', 'audit-log' => 'Audit Log',
            'panduan' => 'Panduan Website'
        ];
        return view('dummy', [
            'role' => 'koordinator',
            'active' => $page,
            'title' => $titles[$page] ?? ucwords(str_replace('-', ' ', $page)),
            'userName' => auth()->user()->name,
            'roleName' => 'KOORDINATOR KP'
        ]);
    })->name('dummy');
});

// ==========================================
// SIMULASI UI DASHBOARD MAHASISWA
// ==========================================
Route::prefix('mahasiswa')->name('mahasiswa.')->middleware('auth')->group(function() {
    Route::get('/dashboard', function() {
        return view('mahasiswa.dashboard', ['active' => 'dashboard']);
    })->name('dashboard');
    
    Route::get('/pendaftaran-kp', [MahasiswaPendaftaranKpController::class, 'create'])->name('pendaftaran-kp.create');
    Route::post('/pendaftaran-kp', [MahasiswaPendaftaranKpController::class, 'store'])->name('pendaftaran-kp.store');
    
    Route::get('/status-pendaftaran', [MahasiswaPendaftaranKpController::class, 'dataKpSaya'])->name('status-pendaftaran');

    Route::get('/{page}', function($page) {
        $titles = [
            'bimbingan-dosen' => 'Bimbingan Dosen', 'bimbingan-supervisor' => 'Bimbingan Supervisor',
            'persetujuan-sidang' => 'Persetujuan Sidang KP', 'pendaftaran-sidang' => 'Pendaftaran Sidang',
            'jadwal-sidang' => 'Jadwal Sidang', 'hasil-sidang' => 'Hasil Sidang',
            'revisi' => 'Revisi', 'nilai-akhir' => 'Nilai Akhir KP',
            'notifikasi' => 'Notifikasi', 'profil' => 'Profil', 'panduan' => 'Panduan Website'
        ];
        return view('dummy', [
            'role' => 'mahasiswa',
            'active' => $page,
            'title' => $titles[$page] ?? ucwords(str_replace('-', ' ', $page)),
            'userName' => auth()->user()->name,
            'roleName' => 'MAHASISWA'
        ]);
    })->name('dummy');
});

// ==========================================
// SIMULASI UI DASHBOARD DOSEN
// ==========================================
Route::prefix('dosen')->name('dosen.')->middleware('auth')->group(function() {
    Route::get('/dashboard', function() {
        return view('dosen.dashboard', ['active' => 'dashboard']);
    })->name('dashboard');
    
    Route::get('/{page}', function($page) {
        $titles = [
            'daftar-mahasiswa' => 'Daftar Mahasiswa', 'persetujuan-sidang' => 'Persetujuan Sidang',
            'jadwal-sidang' => 'Jadwal Sidang', 'input-nilai' => 'Input Nilai',
            'akumulasi-penilaian' => 'Akumulasi Penilaian', 'berita-acara' => 'Berita Acara',
            'revisi' => 'Revisi', 'profil' => 'Profil', 'panduan' => 'Panduan Website'
        ];
        return view('dummy', [
            'role' => 'dosen',
            'active' => $page,
            'title' => $titles[$page] ?? ucwords(str_replace('-', ' ', $page)),
            'userName' => auth()->user()->name,
            'roleName' => 'DOSEN'
        ]);
    })->name('dummy');
});

require __DIR__.'/auth.php';