<?php

use Illuminate\Http\Request;

// 1. Load Autoloader
require __DIR__ . '/../vendor/autoload.php';

// 2. Persiapkan Lingkungan Vercel
putenv('LOG_CHANNEL=stderr');
putenv('APP_STORAGE=/tmp');
$_ENV['APP_STORAGE'] = '/tmp';

// 3. Siapkan folder writable di /tmp
$tmpDir = '/tmp/laravel/framework';
foreach (['/views', '/cache', '/sessions', '/logs'] as $path) {
    if (!is_dir($tmpDir . $path)) {
        @mkdir($tmpDir . $path, 0777, true);
    }
}

// 4. Buat Instansi Aplikasi
/** @var \Illuminate\Foundation\Application $app */
$app = require_once __DIR__ . '/../bootstrap/app.php';

// 5. PAKSA semua jalur ke /tmp agar tidak ada masalah Read-Only
$app->useStoragePath('/tmp');

// Trik Pamungkas: Paksa jalur manifest paket ke /tmp
$app->instance('manifest.path', '/tmp/packages.php');

// 6. Jalankan Request
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

$response = $kernel->handle(
    $request = Request::capture()
)->send();

$kernel->terminate($request, $response);
