<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$users = \App\Models\User::whereHas('mahasiswa', function($q) {
    $q->whereIn('nim', ['412023024', '412023025', '4120230245']);
})->get();

foreach ($users as $u) {
    echo "NIM " . $u->mahasiswa->nim . " (ID User: " . $u->id . ")\n";
    $kps = \App\Models\PendaftaranKp::where('mahasiswa_id', $u->id)
        ->orWhereJsonContains('anggota_kelompok_ids', $u->id)
        ->orWhereJsonContains('anggota_kelompok_ids', (string)$u->id)
        ->get(['id', 'judul_kp', 'status_kp', 'created_at', 'anggota_kelompok_ids']);
    
    foreach($kps as $kp) {
        echo " - KP ID {$kp->id} | {$kp->judul_kp} | Status: {$kp->status_kp} | Created: {$kp->created_at}\n";
    }
    echo "--------------------------\n";
}
