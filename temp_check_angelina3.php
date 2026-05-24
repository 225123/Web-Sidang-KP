<?php
$user = \App\Models\User::where('name', 'like', '%Angelina%')->first();
if ($user) {
    echo "User found: " . $user->name . "\n";
    $kp = \App\Models\PendaftaranKp::withoutGlobalScope('periode')->where('mahasiswa_id', $user->id)->latest()->first();
    if ($kp) {
        echo "KP found: ID=" . $kp->id . ", Status=" . $kp->status_kp . "\n";
        $sidangs = \App\Models\PendaftaranSidang::withoutGlobalScope('periode')->where('pendaftaran_kp_id', $kp->id)->get();
        foreach ($sidangs as $sidang) {
            echo "Sidang ID=" . $sidang->id . ", Pelaksanaan=" . $sidang->pelaksanaan . ", Status=" . $sidang->status_kelulusan . ", Nilai Akhir=" . $sidang->nilai_akhir . "\n";
        }
    }
}
