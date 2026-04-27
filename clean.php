<?php

use App\Models\PendaftaranKp;
use App\Models\User;
use Illuminate\Contracts\Console\Kernel;

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Kernel::class);
$kernel->bootstrap();

$users = User::where('role', 'mahasiswa')->get();
$deletedIds = [];

foreach ($users as $u) {
    if (! $u->mahasiswa) {
        continue;
    }

    // Get all KPs for this user, ordered by latest
    $kps = PendaftaranKp::where('mahasiswa_id', $u->id)
        ->orWhereJsonContains('anggota_kelompok_ids', $u->id)
        ->orWhereJsonContains('anggota_kelompok_ids', (string) $u->id)
        ->orderBy('created_at', 'desc')
        ->get();

    $hasActive = false;
    foreach ($kps as $kp) {
        if ($kp->status_kp !== 'rejected') {
            if ($hasActive) {
                // If we already found an active (newer) one, delete this older active one
                if (! in_array($kp->id, $deletedIds)) {
                    echo "Deleting redundant KP ID {$kp->id} for NIM {$u->mahasiswa->nim}\n";
                    $kp->delete();
                    $deletedIds[] = $kp->id;
                }
            } else {
                $hasActive = true;
            }
        }
    }
}
echo "Cleanup complete!\n";
