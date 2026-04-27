<?php

use Illuminate\Contracts\Console\Kernel;
use Illuminate\Support\Facades\DB;

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Kernel::class);
$kernel->bootstrap();

echo "--- Users Table ---\n";
$users = DB::table('users')->get();
foreach ($users as $u) {
    echo "ID: {$u->id} | Email: {$u->email}\n";
}

echo "\n--- Mahasiswa Table ---\n";
$mahasiswa = DB::table('mahasiswa')->get();
foreach ($mahasiswa as $m) {
    echo "User ID: {$m->user_id} | NIM: {$m->nim}\n";
}

echo "\n--- Dosen Table ---\n";
$dosen = DB::table('dosen')->get();
foreach ($dosen as $d) {
    echo "User ID: {$d->user_id} | NIDN: {$d->nidn}\n";
}
