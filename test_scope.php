<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$periodeId = 1; // Assuming Genap 2024/2025 is ID 1 (Periode lampau) or 2. We'll just test the global scopes.

echo "Query 1 (PendaftaranKp approved):\n";
echo \App\Models\PendaftaranKp::where('status_kp', 'approved')->toSql() . "\n";

echo "Query 2 (PendaftaranSidang selesai):\n";
echo \App\Models\PendaftaranSidang::where('pelaksanaan', 'Selesai')->toSql() . "\n";
