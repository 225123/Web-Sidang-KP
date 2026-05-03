<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$count = \App\Models\PendaftaranSidang::count();
echo "Total PendaftaranSidang: $count\n";

$records = \App\Models\PendaftaranSidang::with('mahasiswa.user')->get();
foreach($records as $r) {
    echo "ID: {$r->id}, Mhs: {$r->mahasiswa->user->name}, Status Verif: {$r->status_verifikasi}, Status Koord: {$r->status_koordinator}\n";
}
