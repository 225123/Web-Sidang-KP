<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$urls = \App\Models\NotifikasiLog::where('target_role', 'koordinator')
    ->orWhereHas('receiver', function($q) {
        $q->where('role', 'koordinator_kp');
    })
    ->pluck('target_url')
    ->unique()
    ->values()
    ->toArray();

print_r($urls);
