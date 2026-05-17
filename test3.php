<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

// Assuming Dosen ID = 3 or similar
$currentUserId = 1; // Assuming Koordinator is also Pembimbing
// Or let's just run the map function logic for PendaftaranSidang KP = 7
$sidangs = App\Models\PendaftaranSidang::with(['mahasiswa.user'])->where('pendaftaran_kp_id', 7)->get();

foreach($sidangs as $sidang) {
    $kp = App\Models\PendaftaranKp::with(['supervisorInternal', 'supervisorInstansi'])
        ->where('mahasiswa_id', $sidang->mahasiswa_id)
        ->where('status_kp', 'approved')
        ->first();

    if (! $kp && $sidang->pendaftaran_kp_id) {
        $kp = App\Models\PendaftaranKp::with(['supervisorInternal', 'supervisorInstansi'])->find($sidang->pendaftaran_kp_id);
    }
    
    echo $sidang->mahasiswa->user->name . ' - KP found: ' . ($kp ? $kp->id : 'NO') . PHP_EOL;
}
