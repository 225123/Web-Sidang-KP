<?php
$mhs = \App\Models\Mahasiswa::where('nim', '412023025')->first();
$kp = \App\Models\PendaftaranKp::withoutGlobalScope('periode')->where('mahasiswa_id', $mhs->user_id)->latest()->first();
$sidang = $kp ? \App\Models\PendaftaranSidang::where('pendaftaran_kp_id', $kp->id)->latest()->first() : null;

echo json_encode([
    'mhs_is_aktif' => $mhs->is_aktif,
    'has_kp' => !!$kp,
    'kp_tahun_ajaran' => $kp ? $kp->tahun_ajaran_id : null,
    'mhs_tahun_ajaran' => $mhs->tahun_ajaran_id,
    'has_sidang' => !!$sidang,
    'sidang_dipublikasi' => $sidang ? $sidang->nilai_dipublikasi : null,
    'sidang_status' => $sidang ? $sidang->status_kelulusan : null
], JSON_PRETTY_PRINT);
