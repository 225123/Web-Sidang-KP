<?php
$cnt1 = \App\Models\PendaftaranSidang::where(function($q){$q->where('penguji_1_id', 11)->orWhere('penguji_2_id', 11);})->where('status_jadwal', 'submitted')->count();
$cnt2 = \App\Models\PendaftaranSidang::where(function($q){$q->where('penguji_1_id', 11)->orWhere('penguji_2_id', 11);})->where('status_jadwal', 'submitted')->where(function($q){$q->where('pelaksanaan', '!=', 'Selesai')->orWhereNull('pelaksanaan');})->count();
$cnt3 = \App\Models\PendaftaranSidang::where(function($q){$q->where('penguji_1_id', 11)->orWhere('penguji_2_id', 11);})->where('status_jadwal', 'submitted')->where(function($q){$q->where('pelaksanaan', '!=', 'Selesai')->orWhereNull('pelaksanaan');})->whereDate('tanggal_sidang', '>=', now())->count();
echo "TOTAL SUBMITTED: $cnt1\n";
echo "NOT SELESAI: $cnt2\n";
echo ">= NOW: $cnt3\n";

// check what the dates actually are
$dates = \App\Models\PendaftaranSidang::where(function($q){$q->where('penguji_1_id', 11)->orWhere('penguji_2_id', 11);})->where('status_jadwal', 'submitted')->pluck('tanggal_sidang');
echo "DATES: " . json_encode($dates) . "\n";
echo "NOW IS: " . now() . "\n";
