<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\TahunAjaran;
use App\Models\Mahasiswa;

// Check Alexandra by NIM instead of Name
$alex = Mahasiswa::where('nim', '412023004')->first();
if ($alex) {
    echo "Found Mahasiswa by NIM 412023004!\n";
    if ($alex->user) {
        echo "User Name: " . $alex->user->name . "\n";
        echo "Signature: " . $alex->user->signature_path . "\n";
    }
} else {
    echo "NIM 412023004 NOT FOUND!\n";
}

// List all periods
$periods = TahunAjaran::all();
echo "\nPeriods in DB:\n";
foreach ($periods as $p) {
    echo "- ID: " . $p->id . " | Label: " . $p->label_tahun_ajaran . "\n";
}

echo "\nLatest users added:\n";
$latestUsers = User::where('role', 'mahasiswa')->orderBy('id', 'desc')->take(10)->get();
foreach ($latestUsers as $u) {
    echo "- " . $u->name . " (ID: " . $u->id . ")\n";
    if ($u->mahasiswa) {
        echo "  NIM: " . $u->mahasiswa->nim . " | Tahun Ajaran ID: " . $u->mahasiswa->tahun_ajaran_id . "\n";
    }
}
