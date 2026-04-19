<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$sidangs = \App\Models\PendaftaranSidang::all();
foreach ($sidangs as $p) {
    echo "ID: {$p->id} | Mhs ID: {$p->mahasiswa_id} | Status Verifikasi: {$p->status_verifikasi} | Status Koord: {$p->status_koordinator} | Laporan: {$p->file_laporan}\n";
}
