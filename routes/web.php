<?php

use App\Http\Controllers\Dosen\DaftarBimbinganController;
use App\Http\Controllers\Dosen\PersetujuanSidangController as DosenPersetujuanSidangController;
use App\Http\Controllers\Koordinator\BeritaAcaraController;
use App\Http\Controllers\Koordinator\BimbinganSayaController;
use App\Http\Controllers\Koordinator\DashboardController;
use App\Http\Controllers\Koordinator\DosenPengujiController;
use App\Http\Controllers\Koordinator\FinalisasiNilaiController;
use App\Http\Controllers\Koordinator\InputNilaiController;
use App\Http\Controllers\Koordinator\JadwalMengujiController;
use App\Http\Controllers\Koordinator\KalenderSidangController;
use App\Http\Controllers\Koordinator\NotifikasiController;
use App\Http\Controllers\Koordinator\PendaftaranKpController as KoordinatorPendaftaranKpController;
use App\Http\Controllers\Koordinator\PengumumanController;
use App\Http\Controllers\Koordinator\PenjadwalanSidangController;
use App\Http\Controllers\Koordinator\PenugasanPembimbingController;
use App\Http\Controllers\Koordinator\ProgressUmumController;
use App\Http\Controllers\Koordinator\RekapRevisiController;
use App\Http\Controllers\Koordinator\RevisiController;
use App\Http\Controllers\Koordinator\TimelineController;
use App\Http\Controllers\Koordinator\UserController;
use App\Http\Controllers\Koordinator\VerifikasiBerkasController;
use App\Http\Controllers\Mahasiswa\BimbinganController;
use App\Http\Controllers\Mahasiswa\JadwalSidangController;
use App\Http\Controllers\Mahasiswa\PendaftaranKpController as MahasiswaPendaftaranKpController;
use App\Http\Controllers\Mahasiswa\PendaftaranSidangController;
use App\Http\Controllers\Mahasiswa\PersetujuanSidangController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// ==========================================
// PUBLIC ROUTES (External Supervisor)
// ==========================================
Route::get('/penilaian-supervisor/success', [\App\Http\Controllers\ExternalSupervisorController::class, 'success'])->name('supervisor.penilaian.success');
Route::get('/penilaian-supervisor/{token}', [\App\Http\Controllers\ExternalSupervisorController::class, 'showForm'])->name('supervisor.penilaian.form');
Route::post('/penilaian-supervisor/{token}', [\App\Http\Controllers\ExternalSupervisorController::class, 'submitNilai'])->name('supervisor.penilaian.submit');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // New Custom Profile Page Routes
    Route::get('/profil', [UserProfileController::class, 'index'])->name('profil.index');
    Route::post('/profil/info', [UserProfileController::class, 'updateInfo'])->name('profil.updateInfo');
    Route::post('/profil/avatar', [UserProfileController::class, 'updateAvatar'])->name('profil.updateAvatar');
    Route::post('/profil/signature/upload', [UserProfileController::class, 'updateSignatureUpload'])->name('profil.updateSignatureUpload');
    Route::post('/profil/signature/draw', [UserProfileController::class, 'updateSignatureDraw'])->name('profil.updateSignatureDraw');

    Route::post('/set-periode', [\App\Http\Controllers\PeriodeSessionController::class, 'setPeriode'])->name('set-periode');
});

// ==========================================
// ROUTE KOORDINATOR (Sudah Disatukan & Dirapihkan)
// ==========================================
Route::prefix('koordinator')->name('koordinator.')->middleware(['auth', 'verified', 'role:koordinator'])->group(function () {
    // Periode KP
    Route::get('/periode-kp', [App\Http\Controllers\Koordinator\PeriodeKpController::class, 'index'])->name('periode-kp.index');
    Route::post('/periode-kp', [App\Http\Controllers\Koordinator\PeriodeKpController::class, 'store'])->name('periode-kp.store');
    Route::put('/periode-kp/{id}/aktif', [App\Http\Controllers\Koordinator\PeriodeKpController::class, 'setActive'])->name('periode-kp.aktif');
    Route::delete('/periode-kp/{id}', [App\Http\Controllers\Koordinator\PeriodeKpController::class, 'destroy'])->name('periode-kp.destroy');

    // 1. Dashboard Koordinator
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/manajemen-akses', [UserController::class, 'index'])->name('manajemen-akses');
    Route::get('/manajemen-akses/check-id', [UserController::class, 'checkId'])->name('user.check-id');
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

    // Data Mahasiswa KP
    Route::get('/data-mahasiswa', [App\Http\Controllers\Koordinator\MahasiswaController::class, 'index'])->name('data-mahasiswa.index');

    // Penugasan Pembimbing
    Route::get('/penugasan-pembimbing', [PenugasanPembimbingController::class, 'index'])->name('penugasan-pembimbing');
    Route::post('/penugasan-pembimbing/store', [PenugasanPembimbingController::class, 'storePlotting'])->name('penugasan-pembimbing.store');
    Route::post('/penugasan-pembimbing/auto', [PenugasanPembimbingController::class, 'autoAssign'])->name('penugasan-pembimbing.auto');
    Route::post('/penugasan-pembimbing/reset', [PenugasanPembimbingController::class, 'resetPlotting'])->name('penugasan-pembimbing.reset');

    Route::get('/penugasan-pembimbing/detail/{slug}', [PenugasanPembimbingController::class, 'show'])->name('penugasan-pembimbing.detail');

    // Bimbingan Saya (Koordinator)
    Route::get('/bimbingan-saya', [BimbinganSayaController::class, 'index'])->name('bimbingan-saya');
    Route::get('/bimbingan-saya/{id}/detail-log-bimbingan', [BimbinganSayaController::class, 'detail'])->name('bimbingan-saya.detail');
    Route::put('/bimbingan-saya/log/{id}/status', [BimbinganSayaController::class, 'updateStatus'])->name('bimbingan-saya.updateStatus');

    // Progress Umum (Koordinator)
    Route::get('/progress-umum', [ProgressUmumController::class, 'index'])->name('progress-umum');


    // 4. Persetujuan Sidang (Koordinator)
    Route::get('/persetujuan-sidang', [App\Http\Controllers\Koordinator\PersetujuanSidangController::class, 'index'])->name('persetujuan-sidang.index');
    Route::put('/persetujuan-sidang/{id}/update', [App\Http\Controllers\Koordinator\PersetujuanSidangController::class, 'update'])->name('persetujuan-sidang.update');
    Route::delete('/persetujuan-sidang/{id}/tolak', [App\Http\Controllers\Koordinator\PersetujuanSidangController::class, 'tolak'])->name('persetujuan-sidang.tolak');

    // 5. Verifikasi Berkas Sidang (Koordinator)
    Route::get('/verifikasi', [VerifikasiBerkasController::class, 'index'])->name('verifikasi-berkas');
    Route::put('/verifikasi/{id}/status', [VerifikasiBerkasController::class, 'updateStatus'])->name('verifikasi-berkas.status');

    // 6. Penjadwalan Sidang
    Route::get('/penjadwalan', [PenjadwalanSidangController::class, 'index'])->name('penjadwalan.index');
    Route::post('/penjadwalan/store', [PenjadwalanSidangController::class, 'store'])->name('penjadwalan.store');
    Route::delete('/penjadwalan/{id}', [PenjadwalanSidangController::class, 'destroySchedule'])->name('penjadwalan.destroy');
    Route::post('/penjadwalan/bulk-destroy', [PenjadwalanSidangController::class, 'bulkDestroy'])->name('penjadwalan.bulk-destroy');
    Route::post('/penjadwalan/auto', [PenjadwalanSidangController::class, 'autoSchedule'])->name('penjadwalan.auto');

    // 7. Dosen Penguji
    Route::get('/dosen-penguji', [DosenPengujiController::class, 'index'])->name('dosen-penguji');
    Route::post('/dosen-penguji/store', [DosenPengujiController::class, 'store'])->name('dosen-penguji.store');
    Route::post('/dosen-penguji/auto', [DosenPengujiController::class, 'autoPlot'])->name('dosen-penguji.auto');
    Route::post('/dosen-penguji/bulk-destroy', [DosenPengujiController::class, 'bulkDestroy'])->name('dosen-penguji.bulk-destroy');
    Route::delete('/dosen-penguji/{id}/cancel', [DosenPengujiController::class, 'destroy'])->name('dosen-penguji.cancel');
    Route::post('/dosen-penguji/submit', [DosenPengujiController::class, 'submit'])->name('dosen-penguji.submit');
    Route::post('/dosen-penguji/cancel-submit', [DosenPengujiController::class, 'cancelSubmit'])->name('dosen-penguji.cancel-submit');
    Route::get('/kalender-sidang', [KalenderSidangController::class, 'index'])->name('kalender-sidang');
    Route::get('/jadwal-menguji', [JadwalMengujiController::class, 'index'])->name('jadwal-menguji');

    // 8. Input Nilai Sidang (Koordinator)
    Route::get('/input-nilai', [InputNilaiController::class, 'index'])->name('input-nilai.index');
    Route::post('/input-nilai/{id}/status', [InputNilaiController::class, 'updateStatus'])->name('input-nilai.status');
    Route::get('/input-nilai/{id}/{role}', [InputNilaiController::class, 'detail'])->name('input-nilai.detail');
    Route::post('/input-nilai/{id}/{role}', [InputNilaiController::class, 'store'])->name('input-nilai.store');
    Route::get('/input-nilai/{id}/{role}/download', [InputNilaiController::class, 'downloadPdf'])->name('input-nilai.download');

    // 9. Berita Acara (Koordinator) - REMOVED AS REQUESTED
    // Route::get('/berita-acara', [BeritaAcaraController::class, 'index'])->name('berita-acara.index');
    // Route::post('/berita-acara/submit', [BeritaAcaraController::class, 'submit'])->name('berita-acara.submit');
    // Route::get('/berita-acara/preview-pdf', [BeritaAcaraController::class, 'previewPdf'])->name('berita-acara.preview-pdf');

    // Revisi (Jika Koordinator bertindak sebagai Penguji 1)
    Route::get('/revisi', [RevisiController::class, 'index'])->name('revisi.index');
    Route::post('/revisi/{id}/terima', [RevisiController::class, 'terima'])->name('revisi.terima');
    Route::post('/revisi/{id}/tolak', [RevisiController::class, 'tolak'])->name('revisi.tolak');

    // Rekap Revisi (Seluruh Mahasiswa Lulus Dengan Revisi)
    Route::get('/rekap-revisi', [RekapRevisiController::class, 'index'])->name('rekap-revisi');

    // 10. Finalisasi Nilai (Rekap Nilai Akhir & Grade)
    Route::get('/finalisasi-nilai', [FinalisasiNilaiController::class, 'index'])->name('finalisasi-nilai.index');
    Route::post('/finalisasi-nilai/sahkan', [FinalisasiNilaiController::class, 'sahkan'])->name('finalisasi-nilai.sahkan');
    Route::get('/finalisasi-nilai/{id}', [FinalisasiNilaiController::class, 'show'])->name('finalisasi-nilai.show');
    Route::get('/finalisasi-nilai/{id}/download', [FinalisasiNilaiController::class, 'downloadPdf'])->name('finalisasi-nilai.download');
    Route::get('/finalisasi-nilai/{id}/download-berita-acara', [FinalisasiNilaiController::class, 'downloadBeritaAcara'])->name('finalisasi-nilai.download-berita-acara');

    // 11. Timeline (Koordinator)
    Route::get('/timeline', [TimelineController::class, 'index'])->name('timeline.index');
    Route::post('/timeline', [TimelineController::class, 'store'])->name('timeline.store');
    Route::post('/timeline/bulk-destroy', [TimelineController::class, 'bulk-destroy'])->name('timeline.bulk-destroy');
    Route::put('/timeline/{id}', [TimelineController::class, 'update'])->name('timeline.update');
    // 12. Audit Log (Koordinator)
    Route::get('/audit-log', [App\Http\Controllers\Koordinator\AuditLogController::class, 'index'])->name('audit-log.index');

    // 13. Backup Database (Koordinator)
    Route::get('/backup', [App\Http\Controllers\Koordinator\BackupController::class, 'index'])->name('backup.index');
    Route::post('/backup/store', [App\Http\Controllers\Koordinator\BackupController::class, 'store'])->name('backup.store');
    Route::get('/backup/download/{filename}', [App\Http\Controllers\Koordinator\BackupController::class, 'download'])->name('backup.download');
    Route::delete('/backup/{filename}', [App\Http\Controllers\Koordinator\BackupController::class, 'destroy'])->name('backup.destroy');

    // 12. Pengumuman (Koordinator)
    Route::get('/pengumuman', [PengumumanController::class, 'index'])->name('pengumuman.index');
    Route::post('/pengumuman', [PengumumanController::class, 'store'])->name('pengumuman.store');
    Route::get('/pengumuman/{id}', [PengumumanController::class, 'show'])->name('pengumuman.show');
    Route::delete('/pengumuman/{id}', [PengumumanController::class, 'destroy'])->name('pengumuman.destroy');

    // 13. Notifikasi Koordinator (Inbox)
    Route::get('/notifikasi', [NotifikasiController::class, 'index'])->name('notifikasi');
    Route::get('/notifikasi/{id}', [NotifikasiController::class, 'show'])->name('notifikasi.show');

    // Laporan Dan Arsip
    Route::get('/laporan-arsip', [App\Http\Controllers\Koordinator\LaporanArsipController::class, 'index'])->name('laporan-arsip');
    Route::get('/laporan-arsip/download', [App\Http\Controllers\Koordinator\LaporanArsipController::class, 'downloadPdf'])->name('laporan-arsip.download');

    // 13. Catch-all for dummy routes on sidebar (Harus paling bawah di grup ini)
    Route::get('/{page}', function ($page) {
        $titles = [
            'data-mhs' => 'Data Mahasiswa KP',
            'pembimbing' => 'Pembimbing',
            'penjadwalan' => 'Penjadwalan Sidang',
            'penguji' => 'Dosen Penguji',
            'kalender' => 'Kalender Sidang',
            'revisi' => 'Revisi',
            'nilai-akhir' => 'Nilai Akhir',
            'laporan' => 'Laporan KP',
            'sistem' => 'Sistem',
            'pengumuman' => 'Pengumuman',
            'audit-log' => 'Audit Log',
            'panduan' => 'Panduan Website',
        ];

        if ($page === 'panduan') {
            return view('koordinator.panduan', [
                'active' => 'panduan',
                'userName' => auth()->user()->name,
                'roleName' => 'KOORDINATOR KP',
            ]);
        }

        return view('dummy', [
            'role' => 'koordinator',
            'active' => $page,
            'title' => $titles[$page] ?? ucwords(str_replace('-', ' ', $page)),
            'userName' => auth()->user()->name,
            'roleName' => 'KOORDINATOR KP',
        ]);
    })->name('dummy');
});

// ==========================================
// SIMULASI UI DASHBOARD MAHASISWA
// ==========================================
Route::prefix('mahasiswa')->name('mahasiswa.')->middleware(['auth', 'verified', 'role:mahasiswa'])->group(function () {
    Route::get('/dashboard', [App\Http\Controllers\Mahasiswa\DashboardController::class, 'index'])->name('dashboard');

    Route::get('/pendaftaran-kp', [MahasiswaPendaftaranKpController::class, 'create'])->name('pendaftaran-kp.create');
    Route::post('/pendaftaran-kp', [MahasiswaPendaftaranKpController::class, 'store'])->name('pendaftaran-kp.store');

    Route::get('/status-pendaftaran', [MahasiswaPendaftaranKpController::class, 'dataKpSaya'])->name('status-pendaftaran');

    // Bimbingan Dosen (Mahasiswa)
    Route::get('/bimbingan-dosen', [BimbinganController::class, 'index'])->name('bimbingan-dosen');
    Route::post('/bimbingan-dosen', [BimbinganController::class, 'store'])->name('bimbingan-dosen.store');
    Route::get('/bimbingan-dosen/export-pdf', [BimbinganController::class, 'exportPdf'])->name('bimbingan-dosen.export-pdf');

    // Persetujuan Sidang (Mahasiswa)
    Route::get('/persetujuan-sidang', [PersetujuanSidangController::class, 'index'])->name('persetujuan-sidang.index');
    Route::post('/persetujuan-sidang', [PersetujuanSidangController::class, 'store'])->name('persetujuan-sidang.store');
    Route::get('/persetujuan-sidang/{id}/cetak', [PersetujuanSidangController::class, 'cetakPersetujuan'])->name('persetujuan-sidang.cetak');

    // Pendaftaran Sidang (Mahasiswa)
    Route::get('/pendaftaran-sidang', [PendaftaranSidangController::class, 'index'])->name('pendaftaran-sidang.index');
    Route::post('/pendaftaran-sidang', [PendaftaranSidangController::class, 'store'])->name('pendaftaran-sidang.store');
    Route::get('/pendaftaran-sidang/template-supervisor', [PendaftaranSidangController::class, 'downloadTemplateSupervisor'])->name('pendaftaran-sidang.template-supervisor');

    // Jadwal Sidang Mahasiswa
    Route::get('/jadwal-sidang', [JadwalSidangController::class, 'index'])->name('jadwal-sidang');
    Route::post('/jadwal-sidang/kirim-kalender', [JadwalSidangController::class, 'kirimEmailKalender'])->name('jadwal-sidang.kirim-kalender');

    // Revisi (Mahasiswa)
    Route::get('/revisi', [App\Http\Controllers\Mahasiswa\RevisiController::class, 'index'])->name('revisi.index');
    Route::post('/revisi', [App\Http\Controllers\Mahasiswa\RevisiController::class, 'store'])->name('revisi.store');

    // Notifikasi Mahasiswa
    Route::get('/notifikasi', [App\Http\Controllers\Mahasiswa\NotifikasiController::class, 'index'])->name('notifikasi');
    Route::get('/notifikasi/{id}', [App\Http\Controllers\Mahasiswa\NotifikasiController::class, 'show'])->name('notifikasi.show');

    // Nilai Akhir Mahasiswa
    Route::get('/nilai-akhir', [App\Http\Controllers\Mahasiswa\NilaiAkhirController::class, 'index'])->name('nilai-akhir');
    Route::get('/nilai-akhir/download', [App\Http\Controllers\Mahasiswa\NilaiAkhirController::class, 'downloadNilai'])->name('nilai-akhir.download');
    Route::get('/nilai-akhir/download-berita-acara', [App\Http\Controllers\Mahasiswa\NilaiAkhirController::class, 'downloadBeritaAcara'])->name('nilai-akhir.download-berita-acara');

    // Hasil Sidang Mahasiswa
    Route::get('/hasil-sidang', [App\Http\Controllers\Mahasiswa\NilaiAkhirController::class, 'hasilSidang'])->name('hasil-sidang');

    // Panduan Website Mahasiswa
    Route::get('/panduan', function() {
        return view('mahasiswa.panduan', ['active' => 'panduan']);
    })->name('panduan');

    Route::get('/{page}', function ($page) {
        $titles = [
            'bimbingan-dosen' => 'Bimbingan Dosen',
            'bimbingan-supervisor' => 'Bimbingan Supervisor',
            'persetujuan-sidang' => 'Persetujuan Sidang KP',
            'jadwal-sidang' => 'Jadwal Sidang',
            'hasil-sidang' => 'Hasil Sidang',
            'revisi' => 'Revisi',
            'notifikasi' => 'Notifikasi',
            'panduan' => 'Panduan Website',
        ];

        return view('dummy', [
            'role' => 'mahasiswa',
            'active' => $page,
            'title' => $titles[$page] ?? ucwords(str_replace('-', ' ', $page)),
            'userName' => auth()->user()->name,
            'roleName' => 'MAHASISWA',
        ]);

    })->name('dummy');
});

// ==========================================
// SIMULASI UI DASHBOARD DOSEN
// ==========================================
Route::prefix('dosen')->name('dosen.')->middleware(['auth', 'verified', 'role:dosen'])->group(function () {
    Route::get('/dashboard', [App\Http\Controllers\Dosen\DashboardController::class, 'index'])->name('dashboard');

    // Daftar Mahasiswa (Dosen)
    Route::get('/daftar-mahasiswa', [DaftarBimbinganController::class, 'index'])->name('daftar-mahasiswa');
    Route::get('/daftar-mahasiswa/{id}/detail-log-bimbingan', [DaftarBimbinganController::class, 'detail'])->name('daftar-mahasiswa.detail');
    Route::put('/daftar-mahasiswa/log/{id}/status', [DaftarBimbinganController::class, 'updateStatus'])->name('daftar-mahasiswa.updateStatus');

    // Sidang Lifecycle (Dosen) - New Input Nilai Routes
    Route::get('/jadwal-menguji', [App\Http\Controllers\Dosen\JadwalMengujiController::class, 'index'])->name('jadwal-menguji');
    Route::get('/input-nilai', [App\Http\Controllers\Dosen\InputNilaiController::class, 'index'])->name('input-nilai.index');

    // Revisi (Dosen Penguji 1)
    Route::get('/revisi', [App\Http\Controllers\Dosen\RevisiController::class, 'index'])->name('revisi.index');
    Route::post('/revisi/{id}/terima', [App\Http\Controllers\Dosen\RevisiController::class, 'terima'])->name('revisi.terima');
    Route::post('/revisi/{id}/tolak', [App\Http\Controllers\Dosen\RevisiController::class, 'tolak'])->name('revisi.tolak');
    Route::post('/input-nilai/{id}/status', [App\Http\Controllers\Dosen\InputNilaiController::class, 'updateStatus'])->name('input-nilai.status');
    Route::get('/input-nilai/{id}/{role}', [App\Http\Controllers\Dosen\InputNilaiController::class, 'detail'])->name('input-nilai.detail');
    Route::post('/input-nilai/{id}/{role}', [App\Http\Controllers\Dosen\InputNilaiController::class, 'store'])->name('input-nilai.store');
    Route::get('/input-nilai/{id}/{role}/download', [App\Http\Controllers\Dosen\InputNilaiController::class, 'downloadPdf'])->name('input-nilai.download');

    // Halaman Persetujuan Sidang Dosen
    Route::get('/persetujuan-sidang', [DosenPersetujuanSidangController::class, 'index'])->name('persetujuan-sidang.index');
    Route::put('/persetujuan-sidang/{id}/update', [DosenPersetujuanSidangController::class, 'update'])->name('persetujuan-sidang.update');
    Route::delete('/persetujuan-sidang/{id}/tolak', [DosenPersetujuanSidangController::class, 'tolak'])->name('persetujuan-sidang.tolak');
    // Notifikasi Dosen
    Route::get('/notifikasi', [App\Http\Controllers\Dosen\NotifikasiController::class, 'index'])->name('notifikasi');
    Route::get('/notifikasi/{id}', [App\Http\Controllers\Dosen\NotifikasiController::class, 'show'])->name('notifikasi.show');

    // Panduan Website Dosen
    Route::get('/panduan', function() {
        return view('dosen.panduan', ['active' => 'panduan']);
    })->name('panduan');

    Route::get('/{page}', function ($page) {
        $titles = [
            'daftar-mahasiswa' => 'Daftar Mahasiswa',
            'persetujuan-sidang' => 'Persetujuan Sidang',
            'jadwal-sidang' => 'Jadwal Sidang',
            'input-nilai' => 'Input Nilai',
            'akumulasi-penilaian' => 'Akumulasi Penilaian',
            'berita-acara' => 'Berita Acara',
            'revisi' => 'Revisi',
            'panduan' => 'Panduan Website',
        ];

        $allBeritaAcaraSubmitted = true; // No longer controlled by Koordinator

        return view('dummy', [
            'role' => 'dosen',
            'active' => $page,
            'title' => $titles[$page] ?? ucwords(str_replace('-', ' ', $page)),
            'userName' => auth()->user()->name,
            'roleName' => 'DOSEN',
        ]);
    })->name('dummy');
});

require __DIR__.'/auth.php';
