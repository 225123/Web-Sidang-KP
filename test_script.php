<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$columns = \Illuminate\Support\Facades\Schema::getColumnListing('pendaftaran_sidangs');
print_r($columns);

$mhs = \App\Models\PendaftaranSidang::first();
if ($mhs) {
   dump($mhs->toArray());
}
