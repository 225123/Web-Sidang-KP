<?php

use App\Http\Controllers\ProfileController;
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

// SIMULASI UI DASHBOARD KOORDINATOR
Route::prefix('koordinator')->name('koordinator.')->middleware('auth')->group(function() {
    Route::get('/dashboard', function() {
        return view('koordinator.dashboard', ['active' => 'dashboard']);
    })->name('dashboard');
    
    // Catch-all for dummy routes on sidebar
    Route::get('/{page}', function($page) {
        $titles = [
            'timeline' => 'Timeline KP', 'pendaftaran' => 'Pendaftaran KP',
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

// SIMULASI UI DASHBOARD MAHASISWA
Route::prefix('mahasiswa')->name('mahasiswa.')->middleware('auth')->group(function() {
    Route::get('/dashboard', function() {
        return view('mahasiswa.dashboard', ['active' => 'dashboard']);
    })->name('dashboard');
    
    Route::get('/{page}', function($page) {
        $titles = [
            'pendaftaran-kp' => 'Pendaftaran KP', 'data-kp' => 'Data KP Saya',
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

// SIMULASI UI DASHBOARD DOSEN
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
