<?php

use App\Models\User;
use Illuminate\Contracts\Console\Kernel;

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Kernel::class);
$kernel->bootstrap();

echo "Listing all users:\n";
$users = User::with(['mahasiswa', 'dosen'])->take(5)->get();
foreach ($users as $u) {
    $id = 'N/A';
    if ($u->mahasiswa) {
        $id = 'MHS: '.$u->mahasiswa->nim;
    } elseif ($u->dosen) {
        $id = 'DSN: '.$u->dosen->nidn;
    }
    echo "ID: {$u->id} | Name: {$u->name} | Email: {$u->email} | Role: {$u->role} | Login ID: {$id}\n";
}
