<?php

use Illuminate\Http\Request;

// Aktifkan pelaporan error sedini mungkin
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

try {
    // 1. Load Autoloader
    $autoload = __DIR__ . '/../vendor/autoload.php';
    if (!file_exists($autoload)) {
        die("Autoloader not found at: $autoload");
    }
    require $autoload;

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
    $bootstrap = __DIR__ . '/../bootstrap/app.php';
    if (!file_exists($bootstrap)) {
        die("Bootstrap file not found at: $bootstrap");
    }
    $app = require $bootstrap;

    // 5. PAKSA semua jalur ke /tmp
    $app->useStoragePath('/tmp');

    // 6. Jalankan Request
    $kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

    $response = $kernel->handle(
        $request = Request::capture()
    );

    $response->send();

    $kernel->terminate($request, $response);

} catch (\Throwable $e) {
    echo "<h1>FATA ERROR DURING BOOTSTRAP</h1>";
    echo "<p><b>Message:</b> " . $e->getMessage() . "</p>";
    echo "<p><b>File:</b> " . $e->getFile() . "</p>";
    echo "<p><b>Line:</b> " . $e->getLine() . "</p>";
    echo "<h3>Stack Trace:</h3>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}
