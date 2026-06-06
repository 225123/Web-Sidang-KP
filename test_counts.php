<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$mahasiswa = \App\Models\Mahasiswa::get(['user_id', 'nim', 'tahun_ajaran_id']);
echo "Mahasiswa count: " . $mahasiswa->count() . "\n";
echo "Mahasiswa with tahun_ajaran_id = 1: " . $mahasiswa->where('tahun_ajaran_id', 1)->count() . "\n";
echo "Mahasiswa with tahun_ajaran_id = 2: " . $mahasiswa->where('tahun_ajaran_id', 2)->count() . "\n";

$kps = \App\Models\PendaftaranKp::withoutGlobalScope('periode')->get(['id', 'mahasiswa_id', 'tahun_ajaran_id', 'status_kp']);
echo "PendaftaranKp count: " . $kps->count() . "\n";
echo "PendaftaranKp with tahun_ajaran_id = 1: " . $kps->where('tahun_ajaran_id', 1)->count() . "\n";
echo "PendaftaranKp with tahun_ajaran_id = 2: " . $kps->where('tahun_ajaran_id', 2)->count() . "\n";

$sidangs = \App\Models\PendaftaranSidang::withoutGlobalScope('periode')->get(['id', 'pendaftaran_kp_id', 'mahasiswa_id']);
echo "PendaftaranSidang count: " . $sidangs->count() . "\n";
