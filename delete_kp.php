<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Mahasiswa;
use App\Models\PendaftaranKp;
use App\Models\PendaftaranSidang;
use App\Models\SupervisorInstansi;

$nim = '412023051';
$mahasiswa = Mahasiswa::where('nim', $nim)->first();

if (!$mahasiswa) {
    echo "Mahasiswa dengan NIM $nim tidak ditemukan.\n";
    exit;
}

$kpList = PendaftaranKp::where('mahasiswa_id', $mahasiswa->user_id)->get();

if ($kpList->isEmpty()) {
    echo "Tidak ada pendaftaran KP untuk mahasiswa ini.\n";
    exit;
}

foreach ($kpList as $kp) {
    // Delete Sidang
    PendaftaranSidang::where('pendaftaran_kp_id', $kp->id)->delete();
    // Delete Supervisor
    SupervisorInstansi::where('pendaftaran_kp_id', $kp->id)->delete();
    // Delete KP
    $kp->delete();
    echo "Berhasil menghapus pendaftaran KP ID: {$kp->id}\n";
}

echo "Proses selesai.\n";
