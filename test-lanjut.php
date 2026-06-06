<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\PendaftaranKp;

PendaftaranKp::whereHas('user.mahasiswa', function ($q) {
    $q->where('status_kp', 'Lanjut');
})->update(['is_lanjutan' => true]);

echo "Updated retroactively successfully.\n";
