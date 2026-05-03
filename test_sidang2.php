<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Simulate session
session()->put('selected_periode_id', 1);

// Force request() to exist
$request = Illuminate\Http\Request::create('/', 'GET');
$app->instance('request', $request);

$sidangs1 = \App\Models\PendaftaranSidang::count();

session()->put('selected_periode_id', 2);
$sidangs2 = \App\Models\PendaftaranSidang::count();

echo "Sidang period 1: $sidangs1\n";
echo "Sidang period 2: $sidangs2\n";
