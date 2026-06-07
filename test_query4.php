<?php
$dosenId = 11;
$all_sidang = \App\Models\PendaftaranSidang::where(function ($q) use ($dosenId) {
        $q->where('penguji_1_id', $dosenId)
            ->orWhere('penguji_2_id', $dosenId);
    })->get(['id', 'tanggal_sidang', 'status_jadwal', 'pelaksanaan', 'status_koordinator']);
echo json_encode($all_sidang, JSON_PRETTY_PRINT);
