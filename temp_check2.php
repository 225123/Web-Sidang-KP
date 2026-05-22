<?php
$fred = \App\Models\User::where('name', 'like', '%Fredicia%')->first();
echo "Fredicia ID: " . ($fred->id ?? 'NOT FOUND') . "\n";

$kps = \App\Models\PendaftaranKp::where('pembimbing_id', $fred->id)->get();
foreach($kps as $kp) { 
    $anggota = is_array($kp->anggota_kelompok_ids) ? json_encode($kp->anggota_kelompok_ids) : $kp->anggota_kelompok_ids;
    echo 'ID: ' . $kp->id . ' | Mhs ID: ' . $kp->mahasiswa_id . ' | Pembimbing: ' . $kp->pembimbing_id . ' | Status: ' . $kp->status_kp . ' | Pengerjaan: ' . $kp->pengerjaan_kp . ' | Anggota: ' . $anggota . "\n"; 
}
