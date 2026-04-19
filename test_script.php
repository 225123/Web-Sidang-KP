<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$dosens = App\Models\Dosen::with('user')->where('is_aktif', true)->get();
$dosenStats = [];
foreach ($dosens as $d) {
    if (!$d->user) continue;
    $dosenStats[] = [
        'id' => $d->user_id,
        'nama' => $d->user->name,
        'kuota' => $d->kuota_bimbingan,
        'beban' => 0
    ];
}

$allKps = App\Models\PendaftaranKp::where('status_kp', 'approved')->get();
$groups = []; $individuals = [];

foreach ($allKps as $kp) {
    $count = 1;
    if ($kp->pengerjaan_kp === 'kelompok' && !empty($kp->anggota_kelompok_ids)) {
        $anggotaIds = is_string($kp->anggota_kelompok_ids) ? json_decode($kp->anggota_kelompok_ids, true) : $kp->anggota_kelompok_ids;
        if (is_array($anggotaIds)) {
            $filtered = array_filter($anggotaIds, fn($id) => $id != $kp->mahasiswa_id);
            $count += count($filtered);
        }
    }
    
    if ($count > 1) {
        $groups[] = ['model' => $kp, 'size' => $count];
    } else {
        $individuals[] = ['model' => $kp, 'size' => 1];
    }
}

usort($groups, function ($a, $b) { return $b['size'] <=> $a['size']; });
$toProcess = array_merge($groups, $individuals);

foreach ($toProcess as $item) {
    $size = $item['size'];
    $kp = $item['model'];

    usort($dosenStats, function($a, $b) {
        return $a['beban'] <=> $b['beban'];
    });

    foreach ($dosenStats as &$dData) {
        if (($dData['beban'] + $size) <= $dData['kuota']) {
            $kp->pembimbing_id = $dData['id'];
            $kp->save();
            $dData['beban'] += $size;
            break;
        }
    }
}

echo "AFTER MASSIVE RE-PLOT:\n";
usort($dosenStats, function($a, $b) { return $b['beban'] <=> $a['beban']; });
foreach ($dosenStats as $d) {
    echo "Dosen: {$d['nama']} | ID: {$d['id']} | Beban -> {$d['beban']} / {$d['kuota']}\n";
}
