<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$users = App\Models\User::where('role', 'mahasiswa')->get();
foreach ($users as $u) {
    echo $u->name . "\n";
    $m = $u->mahasiswa;
    if ($m) {
        echo " - NIM: " . $m->nim . ", is_lanjutan: " . $m->is_lanjutan . "\n";
    }
}
