<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

session()->put('selected_periode_id', 1);

// Call PenugasanPembimbingController index to see what it outputs
$request = \Illuminate\Http\Request::create('/koordinator/penugasan-pembimbing', 'GET');
app()->instance('request', $request);

$controller = app(\App\Http\Controllers\Koordinator\PenugasanPembimbingController::class);
$response = $controller->index($request);

$view = $response->original;
$pendaftarans = $view->getData()['pendaftarans'];

echo "Items count in paginator: " . count($pendaftarans->items()) . "\n";
echo "Total count in paginator: " . $pendaftarans->total() . "\n";
