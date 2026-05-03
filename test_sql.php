<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

session()->put('selected_periode_id', 2);
$request = Illuminate\Http\Request::create('/', 'GET');
$app->instance('request', $request);

$query = \App\Models\PendaftaranSidang::query();
echo $query->toSql() . "\n";
echo json_encode($query->getBindings()) . "\n";

