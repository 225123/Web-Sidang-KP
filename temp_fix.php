<?php
$kpAngelina = \App\Models\PendaftaranKp::find(3);
if ($kpAngelina) {
    $kpAngelina->pembimbing_id = 5; // Sync to Gisela
    $kpAngelina->save();
    echo "BERHASIL SYNC ANGELINA KE GISELA\n";
}
