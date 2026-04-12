<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$user = App\Models\User::where('role', 'mahasiswa')->first();
auth()->login($user);

$request = Illuminate\Http\Request::create('/mahasiswa/status-pendaftaran', 'GET');
$response = app()->handle($request);

if ($response->getStatusCode() !== 200) {
    echo 'ERROR: ' . $response->getStatusCode() . "\n";
    echo strip_tags($response->getContent());
} else {
    echo 'SUCCESS 200';
}
