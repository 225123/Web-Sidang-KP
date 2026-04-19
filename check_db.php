<?php
require __DIR__.'/vendor/autoload.php'; 
$app = require_once __DIR__.'/bootstrap/app.php'; 
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class); 
$kernel->bootstrap(); 

$table = (new App\Models\PendaftaranSidang)->getTable();
$columns = Illuminate\Support\Facades\Schema::getColumnListing($table);
echo implode(',', $columns);
