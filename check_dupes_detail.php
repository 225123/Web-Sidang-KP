<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\PendaftaranKp;
use App\Models\User;

$all_kps = PendaftaranKp::with('user.mahasiswa')->orderBy('mahasiswa_id')->get();

echo "ID | Student | NIM | Type | Members | Title\n";
echo str_repeat("-", 80) . "\n";

foreach ($all_kps as $kp) {
    if ($kp->status_kp === 'rejected') continue;
    
    $mhs = $kp->user;
    $nim = $mhs->mahasiswa->nim ?? 'N/A';
    $members = $kp->anggota_kelompok_ids;
    if (is_string($members)) $members = json_decode($members, true);
    $members_str = is_array($members) ? implode(',', $members) : '-';
    
    printf("%2d | %-15s | %-10s | %-10s | %-15s | %s\n", 
        $kp->id, 
        substr($mhs->name, 0, 15), 
        $nim, 
        $kp->pengerjaan_kp, 
        $members_str, 
        $kp->judul_kp
    );
}

// Summary count
$unique_students = [];
foreach ($all_kps as $kp) {
    if ($kp->status_kp === 'rejected') continue;
    $unique_students[$kp->mahasiswa_id] = true;
    $members = $kp->anggota_kelompok_ids;
    if (is_string($members)) $members = json_decode($members, true);
    if (is_array($members)) {
        foreach ($members as $mid) $unique_students[$mid] = true;
    }
}
echo str_repeat("-", 80) . "\n";
echo "Total Records (Excl Rejected): " . $all_kps->where('status_kp', '!=', 'rejected')->count() . "\n";
echo "Unique students involved: " . count($unique_students) . "\n";
