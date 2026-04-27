<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle(Illuminate\Http\Request::capture());

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

$data = [
    'php_now' => now()->toDateTimeString(),
    'db_latest' => DB::table('audit_logs')->latest('id')->value('created_at'),
    'timezone' => config('app.timezone'),
];

file_put_contents(__DIR__.'/scratch/time_dump.json', json_encode($data, JSON_PRETTY_PRINT));
echo "Time data dumped to scratch/time_dump.json\n";
