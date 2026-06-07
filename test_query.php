<?php
$cnt = \App\Models\PendaftaranSidang::where(function($q){$q->where('penguji_1_id', 11)->orWhere('penguji_2_id', 11);})->where('status_jadwal', 'submitted')->where(function($q){$q->where('pelaksanaan', '!=', 'Selesai')->orWhereNull('pelaksanaan');})->whereDate('tanggal_sidang', '>=', now())->count();
echo "COUNT IS: " . $cnt;
