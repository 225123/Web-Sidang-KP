<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\PendaftaranKp;
use App\Models\User;

$total_kps = PendaftaranKp::count();
echo "Total Pendaftaran KP Records: $total_kps\n";

$dupes = \DB::table('pendaftaran_kp')
    ->select('mahasiswa_id', \DB::raw('count(*) as c'))
    ->groupBy('mahasiswa_id')
    ->havingRaw('count(*) > 1')
    ->get();

echo "Students with multiple main registrations:\n";
foreach ($dupes as $d) {
    echo "User ID: {$d->mahasiswa_id} | Count: {$d->c}\n";
}

$all_kps = PendaftaranKp::all();
$members_count = [];
foreach ($all_kps as $kp) {
    if ($kp->pengerjaan_kp === 'kelompok' && $kp->anggota_kelompok_ids) {
        $ids = is_string($kp->anggota_kelompok_ids) ? json_decode($kp->anggota_kelompok_ids, true) : $kp->anggota_kelompok_ids;
        if (is_array($ids)) {
            foreach ($ids as $id) {
                $members_count[$id] = ($members_count[$id] ?? 0) + 1;
            }
        }
    }
}

echo "\nStudents who are members in multiple groups:\n";
foreach ($members_count as $id => $count) {
    if ($count > 1) echo "User ID: $id | Count: $count\n";
}

echo "\nStudents who are main registrant AND member elsewehere:\n";
foreach ($all_kps as $kp) {
    if (isset($members_count[$kp->mahasiswa_id])) {
        echo "User ID: {$kp->mahasiswa_id} is main in KP {$kp->id} and member in others.\n";
    }
}
