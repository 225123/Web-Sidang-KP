<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
foreach(\App\Models\NotifikasiLog::take(10)->get() as $n) {
    echo $n->target_url . PHP_EOL;
}
