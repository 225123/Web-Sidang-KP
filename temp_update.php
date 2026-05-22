<?php 
$s = \App\Models\PendaftaranSidang::find(3); 
if($s) { 
    $s->ns_motivasi = 87.38; 
    $s->ns_kualitas = 87.38; 
    $s->ns_inisiatif = 87.38; 
    $s->ns_sikap = 87.38; 
    $s->save(); 
    echo 'BERHASIL DIUPDATE'; 
} else { 
    echo 'TIDAK DITEMUKAN'; 
}
