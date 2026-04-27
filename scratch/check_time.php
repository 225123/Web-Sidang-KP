<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle(Illuminate\Http\Request::capture());

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

echo "PHP Time (now()): " . now()->toDateTimeString() . "\n";
echo "Config Timezone: " . config('app.timezone') . "\n";
echo "Database latest created_at: " . DB::table('audit_logs')->latest('id')->value('created_at') . "\n";
echo "Current UTC Time: " . Carbon::now('UTC')->toDateTimeString() . "\n";
