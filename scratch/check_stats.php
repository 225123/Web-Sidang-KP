<?php
use App\Models\PendaftaranKp;
use App\Models\PendaftaranSidang;

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "--- DATA KP BERJALAN (status_kp = approved) ---\n";
$kpBerjalan = PendaftaranKp::where('status_kp', 'approved')->with('mahasiswa.user')->get();
foreach ($kpBerjalan as $kp) {
    echo "Nama: " . ($kp->mahasiswa->user->name ?? 'N/A') . " | NIM: " . ($kp->mahasiswa->nim ?? 'N/A') . " | Periode ID: " . $kp->tahun_ajaran_id . "\n";
}

echo "\n--- DATA KP SELESAI (pelaksanaan = Selesai) ---\n";
$kpSelesai = PendaftaranSidang::where('pelaksanaan', 'Selesai')->with('mahasiswa.user', 'pendaftaranKp')->get();
foreach ($kpSelesai as $s) {
    echo "Nama: " . ($s->mahasiswa->user->name ?? 'N/A') . " | NIM: " . ($s->mahasiswa->nim ?? 'N/A') . " | Periode KP: " . ($s->pendaftaranKp->tahun_ajaran_id ?? 'N/A') . "\n";
}
