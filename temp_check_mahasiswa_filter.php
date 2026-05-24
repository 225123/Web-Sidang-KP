<?php
$periodeId = \App\Models\TahunAjaran::where('is_active', true)->value('id');
echo "Active Periode ID: $periodeId\n";

$query = \App\Models\User::leftJoin('mahasiswa', 'users.id', '=', 'mahasiswa.user_id')
    ->select('users.name', 'mahasiswa.tahun_ajaran_id')
    ->where('users.role', 'mahasiswa');

$query->where(function($q) use ($periodeId) {
    $q->where('mahasiswa.tahun_ajaran_id', $periodeId)
      ->orWhereIn('users.id', function($sub) use ($periodeId) {
          $sub->select('mahasiswa_id')->from('pendaftaran_kp')->where('tahun_ajaran_id', $periodeId);
      });
});

$result = $query->get();
echo "Mahasiswa count in active period: " . $result->count() . "\n";
foreach ($result as $idx => $r) {
    if ($idx < 5) echo "- " . $r->name . " (TA ID: " . $r->tahun_ajaran_id . ")\n";
}
