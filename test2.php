<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
$rows = App\Models\PendaftaranSidang::with('mahasiswa.user')->get();
foreach($rows as $row) {
    echo $row->mahasiswa->user->name . ' - KP: ' . $row->pendaftaran_kp_id . PHP_EOL;
}
