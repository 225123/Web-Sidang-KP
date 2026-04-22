<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Koordinator\UserController;
use App\Http\Controllers\Koordinator\PendaftaranKpController as KoordinatorPendaftaranKpController;
use App\Http\Controllers\Mahasiswa\PendaftaranKpController as MahasiswaPendaftaranKpController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Mahasiswa\PersetujuanSidangController;
use App\Http\Controllers\Dosen\PersetujuanSidangController as DosenPersetujuanSidangController;
use App\Http\Controllers\Mahasiswa\PendaftaranSidangController;
use App\Http\Controllers\Koordinator\VerifikasiBerkasController;

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

    // New Custom Profile Page Routes
    Route::get('/profil', [\App\Http\Controllers\UserProfileController::class, 'index'])->name('profil.index');
    Route::post('/profil/info', [\App\Http\Controllers\UserProfileController::class, 'updateInfo'])->name('profil.updateInfo');
    Route::post('/profil/avatar', [\App\Http\Controllers\UserProfileController::class, 'updateAvatar'])->name('profil.updateAvatar');
    Route::post('/profil/signature/upload', [\App\Http\Controllers\UserProfileController::class, 'updateSignatureUpload'])->name('profil.updateSignatureUpload');
    Route::post('/profil/signature/draw', [\App\Http\Controllers\UserProfileController::class, 'updateSignatureDraw'])->name('profil.updateSignatureDraw');
});

// ==========================================
// ROUTE KOORDINATOR (Sudah Disatukan & Dirapihkan)
// ==========================================
Route::prefix('koordinator')->name('koordinator.')->middleware(['auth', 'verified'])->group(function () {

    // 1. Dashboard Koordinator
    Route::get('/dashboard', function () {
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

    // Penugasan Pembimbing
    Route::get('/penugasan-pembimbing', [\App\Http\Controllers\Koordinator\PenugasanPembimbingController::class, 'index'])->name('penugasan-pembimbing');
    Route::post('/penugasan-pembimbing/store', [\App\Http\Controllers\Koordinator\PenugasanPembimbingController::class, 'storePlotting'])->name('penugasan-pembimbing.store');
    Route::post('/penugasan-pembimbing/auto', [\App\Http\Controllers\Koordinator\PenugasanPembimbingController::class, 'autoAssign'])->name('penugasan-pembimbing.auto');

    Route::get('/penugasan-pembimbing/detail/{slug}', [\App\Http\Controllers\Koordinator\PenugasanPembimbingController::class, 'show'])->name('penugasan-pembimbing.detail');

    // Bimbingan Saya (Koordinator)
    Route::get('/bimbingan-saya', [\App\Http\Controllers\Koordinator\BimbinganSayaController::class, 'index'])->name('bimbingan-saya');
    Route::get('/bimbingan-saya/{id}/detail-log-bimbingan', [\App\Http\Controllers\Koordinator\BimbinganSayaController::class, 'detail'])->name('bimbingan-saya.detail');
    Route::put('/bimbingan-saya/log/{id}/status', [\App\Http\Controllers\Koordinator\BimbinganSayaController::class, 'updateStatus'])->name('bimbingan-saya.updateStatus');

    // 4. Persetujuan Sidang (Koordinator)
    Route::get('/persetujuan-sidang', [\App\Http\Controllers\Koordinator\PersetujuanSidangController::class, 'index'])->name('persetujuan-sidang.index');
    Route::put('/persetujuan-sidang/{id}/update', [\App\Http\Controllers\Koordinator\PersetujuanSidangController::class, 'update'])->name('persetujuan-sidang.update');
    Route::delete('/persetujuan-sidang/{id}/tolak', [\App\Http\Controllers\Koordinator\PersetujuanSidangController::class, 'tolak'])->name('persetujuan-sidang.tolak');

    // 5. Verifikasi Berkas Sidang (Koordinator)
    Route::get('/verifikasi', [VerifikasiBerkasController::class, 'index'])->name('verifikasi-berkas');
    Route::put('/verifikasi/{id}/status', [VerifikasiBerkasController::class, 'updateStatus'])->name('verifikasi-berkas.status');

    // 6. Penjadwalan Sidang
    Route::get('/penjadwalan', [\App\Http\Controllers\Koordinator\PenjadwalanSidangController::class, 'index'])->name('penjadwalan.index');
    Route::post('/penjadwalan/store', [\App\Http\Controllers\Koordinator\PenjadwalanSidangController::class, 'store'])->name('penjadwalan.store');
    Route::delete('/penjadwalan/{id}', [\App\Http\Controllers\Koordinator\PenjadwalanSidangController::class, 'destroySchedule'])->name('penjadwalan.destroy');
    Route::post('/penjadwalan/bulk-destroy', [\App\Http\Controllers\Koordinator\PenjadwalanSidangController::class, 'bulkDestroy'])->name('penjadwalan.bulk-destroy');
    Route::post('/penjadwalan/auto', [\App\Http\Controllers\Koordinator\PenjadwalanSidangController::class, 'autoSchedule'])->name('penjadwalan.auto');

    // 7. Dosen Penguji
    Route::get('/dosen-penguji', [\App\Http\Controllers\Koordinator\DosenPengujiController::class, 'index'])->name('dosen-penguji');
    Route::post('/dosen-penguji/store', [\App\Http\Controllers\Koordinator\DosenPengujiController::class, 'store'])->name('dosen-penguji.store');
    Route::post('/dosen-penguji/auto', [\App\Http\Controllers\Koordinator\DosenPengujiController::class, 'autoPlot'])->name('dosen-penguji.auto');
    Route::post('/dosen-penguji/bulk-destroy', [\App\Http\Controllers\Koordinator\DosenPengujiController::class, 'bulkDestroy'])->name('dosen-penguji.bulk-destroy');
    Route::delete('/dosen-penguji/{id}/cancel', [\App\Http\Controllers\Koordinator\DosenPengujiController::class, 'destroy'])->name('dosen-penguji.cancel');
    Route::post('/dosen-penguji/submit', [\App\Http\Controllers\Koordinator\DosenPengujiController::class, 'submit'])->name('dosen-penguji.submit');
    Route::post('/dosen-penguji/cancel-submit', [\App\Http\Controllers\Koordinator\DosenPengujiController::class, 'cancelSubmit'])->name('dosen-penguji.cancel-submit');
    Route::get('/kalender-sidang', [\App\Http\Controllers\Koordinator\KalenderSidangController::class, 'index'])->name('kalender-sidang');
    Route::get('/jadwal-menguji', [\App\Http\Controllers\Koordinator\JadwalMengujiController::class, 'index'])->name('jadwal-menguji');

    // 8. Input Nilai Sidang (Koordinator)
    Route::get('/input-nilai', [\App\Http\Controllers\Koordinator\InputNilaiController::class, 'index'])->name('input-nilai.index');
    Route::get('/input-nilai/{id}/{role}', [\App\Http\Controllers\Koordinator\InputNilaiController::class, 'detail'])->name('input-nilai.detail');
    Route::post('/input-nilai/{id}/{role}', [\App\Http\Controllers\Koordinator\InputNilaiController::class, 'store'])->name('input-nilai.store');
    Route::get('/input-nilai/{id}/{role}/download', [\App\Http\Controllers\Koordinator\InputNilaiController::class, 'downloadPdf'])->name('input-nilai.download');
    Route::post('/input-nilai/{id}/status', [\App\Http\Controllers\Koordinator\InputNilaiController::class, 'updateStatus'])->name('input-nilai.status');

    // 5. Catch-all for dummy routes on sidebar (Harus paling bawah di grup ini)
    Route::get('/{page}', function ($page) {
        $titles = [
            'timeline' => 'Timeline KP',
            'data-mhs' => 'Data Mahasiswa KP',
            'pembimbing' => 'Pembimbing',
            'penjadwalan' => 'Penjadwalan Sidang',
            'penguji' => 'Dosen Penguji',
            'kalender' => 'Kalender Sidang',
            'revisi' => 'Revisi',
            'nilai-akhir' => 'Nilai Akhir',
            'berita-acara' => 'Berita Acara',
            'laporan' => 'Laporan KP',
            'sistem' => 'Sistem',
            'pengumuman' => 'Pengumuman',
            'audit-log' => 'Audit Log',
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
Route::prefix('mahasiswa')->name('mahasiswa.')->middleware('auth')->group(function () {
    Route::get('/dashboard', function () {
        return view('mahasiswa.dashboard', ['active' => 'dashboard']);
    })->name('dashboard');

    Route::get('/pendaftaran-kp', [MahasiswaPendaftaranKpController::class, 'create'])->name('pendaftaran-kp.create');
    Route::post('/pendaftaran-kp', [MahasiswaPendaftaranKpController::class, 'store'])->name('pendaftaran-kp.store');

    Route::get('/status-pendaftaran', [MahasiswaPendaftaranKpController::class, 'dataKpSaya'])->name('status-pendaftaran');

    // Bimbingan Dosen (Mahasiswa)
    Route::get('/bimbingan-dosen', [\App\Http\Controllers\Mahasiswa\BimbinganController::class, 'index'])->name('bimbingan-dosen');
    Route::post('/bimbingan-dosen', [\App\Http\Controllers\Mahasiswa\BimbinganController::class, 'store'])->name('bimbingan-dosen.store');
    Route::get('/bimbingan-dosen/export-pdf', [\App\Http\Controllers\Mahasiswa\BimbinganController::class, 'exportPdf'])->name('bimbingan-dosen.export-pdf');

    // Persetujuan Sidang (Mahasiswa)
    Route::get('/persetujuan-sidang', [PersetujuanSidangController::class, 'index'])->name('persetujuan-sidang.index');
    Route::post('/persetujuan-sidang', [PersetujuanSidangController::class, 'store'])->name('persetujuan-sidang.store');
    Route::get('/persetujuan-sidang/{id}/cetak', [PersetujuanSidangController::class, 'cetakPersetujuan'])->name('persetujuan-sidang.cetak');

    // Pendaftaran Sidang (Mahasiswa)
    Route::get('/pendaftaran-sidang', [PendaftaranSidangController::class, 'index'])->name('pendaftaran-sidang.index');
    Route::post('/pendaftaran-sidang', [PendaftaranSidangController::class, 'store'])->name('pendaftaran-sidang.store');
    Route::get('/pendaftaran-sidang/template-supervisor', [PendaftaranSidangController::class, 'downloadTemplateSupervisor'])->name('pendaftaran-sidang.template-supervisor');

    // Jadwal Sidang Mahasiswa
    Route::get('/jadwal-sidang', [\App\Http\Controllers\Mahasiswa\JadwalSidangController::class, 'index'])->name('jadwal-sidang');

    Route::get('/{page}', function ($page) {
        $titles = [
            'bimbingan-dosen' => 'Bimbingan Dosen',
            'bimbingan-supervisor' => 'Bimbingan Supervisor',
            'persetujuan-sidang' => 'Persetujuan Sidang KP',
            'jadwal-sidang' => 'Jadwal Sidang',
            'hasil-sidang' => 'Hasil Sidang',
            'revisi' => 'Revisi',
            'nilai-akhir' => 'Nilai Akhir KP',
            'notifikasi' => 'Notifikasi',
            'panduan' => 'Panduan Website'
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
Route::prefix('dosen')->name('dosen.')->middleware('auth')->group(function () {
    Route::get('/dashboard', function () {
        return view('dosen.dashboard', ['active' => 'dashboard']);
    })->name('dashboard');

    // Daftar Mahasiswa (Dosen)
    Route::get('/daftar-mahasiswa', [\App\Http\Controllers\Dosen\DaftarBimbinganController::class, 'index'])->name('daftar-mahasiswa');
    Route::get('/daftar-mahasiswa/{id}/detail-log-bimbingan', [\App\Http\Controllers\Dosen\DaftarBimbinganController::class, 'detail'])->name('daftar-mahasiswa.detail');
    Route::put('/daftar-mahasiswa/log/{id}/status', [\App\Http\Controllers\Dosen\DaftarBimbinganController::class, 'updateStatus'])->name('daftar-mahasiswa.updateStatus');

    // Sidang Lifecycle (Dosen) - New Input Nilai Routes
    Route::get('/jadwal-menguji', [\App\Http\Controllers\Dosen\JadwalMengujiController::class, 'index'])->name('jadwal-menguji');
    Route::get('/input-nilai', [\App\Http\Controllers\Dosen\InputNilaiController::class, 'index'])->name('input-nilai.index');
    Route::get('/input-nilai/{id}/{role}', [\App\Http\Controllers\Dosen\InputNilaiController::class, 'detail'])->name('input-nilai.detail');
    Route::post('/input-nilai/{id}/{role}', [\App\Http\Controllers\Dosen\InputNilaiController::class, 'store'])->name('input-nilai.store');
    Route::get('/input-nilai/{id}/{role}/download', [\App\Http\Controllers\Dosen\InputNilaiController::class, 'downloadPdf'])->name('input-nilai.download');
    Route::post('/input-nilai/{id}/status', [\App\Http\Controllers\Dosen\InputNilaiController::class, 'updateStatus'])->name('input-nilai.status');

    // Halaman Persetujuan Sidang Dosen
    Route::get('/persetujuan-sidang', [DosenPersetujuanSidangController::class, 'index'])->name('persetujuan-sidang.index');
    Route::put('/persetujuan-sidang/{id}/update', [DosenPersetujuanSidangController::class, 'update'])->name('persetujuan-sidang.update');
    Route::delete('/persetujuan-sidang/{id}/tolak', [DosenPersetujuanSidangController::class, 'tolak'])->name('persetujuan-sidang.tolak');
    Route::get('/{page}', function ($page) {
        $titles = [
            'daftar-mahasiswa' => 'Daftar Mahasiswa',
            'persetujuan-sidang' => 'Persetujuan Sidang',
            'jadwal-sidang' => 'Jadwal Sidang',
            'input-nilai' => 'Input Nilai',
            'akumulasi-penilaian' => 'Akumulasi Penilaian',
            'berita-acara' => 'Berita Acara',
            'revisi' => 'Revisi',
            'panduan' => 'Panduan Website'
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

require __DIR__ . '/auth.php';