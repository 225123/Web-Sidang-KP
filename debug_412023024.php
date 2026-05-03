<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

use Illuminate\Support\Facades\DB;

$mahasiswa = DB::table('mahasiswa')->where('nim', '412023024')->first();
$user = $mahasiswa ? DB::table('users')->where('id', $mahasiswa->user_id)->first() : null;

echo "Mahasiswa:\n";
print_r($mahasiswa);
echo "\nUser:\n";
print_r($user);

$activePeriodId = \App\Models\TahunAjaran::aktif()->id;
echo "\nActive Period ID: $activePeriodId\n";

