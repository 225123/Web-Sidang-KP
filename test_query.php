<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$periodeId = 1;
$count = \App\Models\User::with(['mahasiswa'])->where('role', 'mahasiswa')->where(function($q) use ($periodeId) {
    $q->whereHas('mahasiswa', function($sq) use ($periodeId) {
        $sq->where('tahun_ajaran_id', $periodeId);
    })->orWhereIn('id', function($sub) use ($periodeId) {
        $sub->select('mahasiswa_id')->from('pendaftaran_kp')->where('tahun_ajaran_id', $periodeId);
    });
})->count();

echo "Count: " . $count . "\n";
