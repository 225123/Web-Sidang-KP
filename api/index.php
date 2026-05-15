<?php

use Illuminate\Http\Request;

// 1. Aktifkan pelaporan error untuk debugging di browser
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

try {
    // 2. Load Autoloader
    require __DIR__ . '/../vendor/autoload.php';

    // 3. Paksa konfigurasi lingkungan Vercel
    putenv('LOG_CHANNEL=stderr');
    putenv('APP_STORAGE=/tmp');
    $_ENV['APP_STORAGE'] = '/tmp';

    // 4. Inisialisasi Aplikasi
    /** @var \Illuminate\Foundation\Application $app */
    $app = require_once __DIR__ . '/../bootstrap/app.php';

    // 5. Paksa jalur penyimpanan ke /tmp sebelum Kernel menangani request
    $app->useStoragePath('/tmp');

    // 6. Jalankan Kernel (Ini akan memicu proses bootstrap standar Laravel secara otomatis)
    $kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

    $response = $kernel->handle(
        $request = Request::capture()
    );

    $response->send();

    $kernel->terminate($request, $response);

} catch (\Throwable $e) {
    // Jika terjadi error di sini, ini adalah ERROR ASLI yang selama ini tersembunyi
    echo "<h1>ROOT CAUSE FOUND</h1>";
    echo "<p><b>Message:</b> " . $e->getMessage() . "</p>";
    echo "<p><b>File:</b> " . $e->getFile() . " on line " . $e->getLine() . "</p>";
    echo "<h3>Stack Trace:</h3>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}
