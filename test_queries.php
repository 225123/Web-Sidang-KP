<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$periodeId = \App\Models\TahunAjaran::where('is_active', true)->value('id');
echo "Active Period ID: " . $periodeId . "\n";

$query = \App\Models\User::leftJoin('mahasiswa', 'users.id', '=', 'mahasiswa.user_id')
    ->select('users.*', 'mahasiswa.nim', 'mahasiswa.prodi', 'mahasiswa.angkatan', 'mahasiswa.no_hp')
    ->where('users.role', 'mahasiswa');
    
$query->where(function($q) use ($periodeId) {
    $q->where('mahasiswa.tahun_ajaran_id', $periodeId)
      ->orWhereIn('users.id', function($sub) use ($periodeId) {
          $sub->select('mahasiswa_id')->from('pendaftaran_kp')->where('tahun_ajaran_id', $periodeId);
      });
});

$count = $query->count();
echo "User Count (Manajemen Akses): " . $count . "\n";

$mhsQuery = \App\Models\Mahasiswa::with('user');
$mhsQuery->where(function($q) use ($periodeId) {
    $q->where('tahun_ajaran_id', $periodeId)
      ->orWhereIn('user_id', function($sub) use ($periodeId) {
          $sub->select('mahasiswa_id')->from('pendaftaran_kp')->where('tahun_ajaran_id', $periodeId);
      });
});
$mhsCount = $mhsQuery->count();
echo "Mahasiswa Count (Progress Umum): " . $mhsCount . "\n";
