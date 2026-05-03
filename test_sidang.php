<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$sidangs = \App\Models\PendaftaranSidang::count();
$withDate = \App\Models\PendaftaranSidang::whereNotNull('tanggal_sidang')->count();
echo "Sidang count (Scoped): $sidangs, with date: $withDate\n";

$allSidangs = \App\Models\PendaftaranSidang::withoutGlobalScopes()->count();
$allWithDate = \App\Models\PendaftaranSidang::withoutGlobalScopes()->whereNotNull('tanggal_sidang')->count();
echo "Sidang count (Unscoped): $allSidangs, with date: $allWithDate\n";

$koordinatorJadwal = \App\Models\PendaftaranSidang::with(['mahasiswa.user', 'penguji1', 'penguji2', 'pendaftaranKp.supervisorInternal'])
    ->whereNotNull('tanggal_sidang')
    ->get();
echo "Koordinator Jadwal (Kalender Sidang): " . $koordinatorJadwal->count() . "\n";
