<?php
$dosenId = 11;
$jadwalTerdekat = \App\Models\PendaftaranSidang::with(['mahasiswa.user', 'mahasiswa'])
    ->whereHas('pendaftaranKp', function ($q) {
        $q->withoutGlobalScope('periode');
    })
    ->where(function ($q) use ($dosenId) {
        $q->where('penguji_1_id', $dosenId)
            ->orWhere('penguji_2_id', $dosenId);
    })
    ->where('status_jadwal', 'submitted')
    ->count();

$jadwalKoordinator = \App\Models\PendaftaranSidang::with(['mahasiswa.user', 'mahasiswa'])
    ->where(function ($q) use ($dosenId) {
        $q->where('penguji_1_id', $dosenId)
            ->orWhere('penguji_2_id', $dosenId);
    })
    ->where('status_jadwal', 'submitted')
    ->count();

echo "DOSEN COUNT: $jadwalTerdekat\n";
echo "KOORDINATOR COUNT: $jadwalKoordinator\n";
