<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\PendaftaranKp;

$kp = PendaftaranKp::find(99);
if ($kp) {
    echo "Pendaftaran KP 99 User ID: " . $kp->mahasiswa_id . "\n";
    echo "Pendaftaran KP is_lanjutan: " . ($kp->is_lanjutan ? 'true' : 'false') . "\n";

    $ryu = $kp->user;
    if ($ryu) {
        echo "User Name: " . $ryu->name . "\n";
        $mhs = $ryu->mahasiswa;
        if ($mhs) {
            echo "Mahasiswa Status KP: " . $mhs->status_kp . "\n";
        } else {
            echo "Mahasiswa record not found.\n";
        }
    }

    $kp->is_lanjutan = true;
    $kp->save();
    echo "Force updated Pendaftaran KP 99 to is_lanjutan = true.\n";
} else {
    echo "Pendaftaran KP 99 not found.\n";
}
