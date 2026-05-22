<?php
$mhs = \App\Models\Mahasiswa::with('user')->where('nim', '412023025')->first();
$sidangs = \App\Models\PendaftaranSidang::whereHas('pendaftaranKp', function($q) use ($mhs) {
    $q->withoutGlobalScope('periode')
        ->where('mahasiswa_id', $mhs->user_id);
})->get();

$out = [];
foreach($sidangs as $s) {
    $out[] = [
        'id' => $s->id,
        'kp_id' => $s->pendaftaran_kp_id,
        'nilai_dipublikasi' => $s->nilai_dipublikasi,
        'status_kelulusan' => $s->status_kelulusan,
        'nilai_akhir' => $s->nilai_akhir,
        'status_revisi' => $s->status_revisi,
    ];
}

echo json_encode([
    'mhs_is_aktif' => $mhs->is_aktif,
    'mhs_is_aktif_type' => gettype($mhs->is_aktif),
    'sidangs' => $out
], JSON_PRETTY_PRINT);
