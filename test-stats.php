<?php
require __DIR__."/vendor/autoload.php";
$app = require_once __DIR__."/bootstrap/app.php";
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$sidangQuery = \App\Models\PendaftaranSidang::whereNotNull("tanggal_sidang");
$sidangsThisPeriod = $sidangQuery->orderBy("tanggal_sidang", "asc")->pluck("tanggal_sidang");
$weeksData = [];
foreach ($sidangsThisPeriod as $tanggal) {
    $date = \Carbon\Carbon::parse($tanggal);
    $startOfWeek = $date->copy()->startOfWeek(\Carbon\Carbon::SUNDAY);
    $endOfWeek = $date->copy()->endOfWeek(\Carbon\Carbon::SATURDAY);
    $weekKey = $startOfWeek->format("Y-m-d");
    if (!isset($weeksData[$weekKey])) {
        $weeksData[$weekKey] = ["label" => "test", "stats" => array_fill(0, 7, 0)];
    }
    $dayOfWeek = $date->dayOfWeek;
    $weeksData[$weekKey]["stats"][$dayOfWeek]++;
}
echo json_encode(array_values($weeksData));
