<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle(Illuminate\Http\Request::capture());

use Illuminate\Support\Facades\DB;

$tables = DB::select("SELECT name FROM sqlite_master WHERE type='table' AND name NOT LIKE 'sqlite_%'");
foreach ($tables as $table) {
    $count = DB::table($table->name)->count();
    echo "Table: {$table->name} -> Count: $count\n";
}
echo "\nDB File Path: " . database_path('database.sqlite') . "\n";
echo "DB File Size: " . (file_exists(database_path('database.sqlite')) ? filesize(database_path('database.sqlite')) : 'Not Found') . " bytes\n";
