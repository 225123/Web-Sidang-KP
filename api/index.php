<?php

use Illuminate\Http\Request;

// Aktifkan pelaporan error
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

try {
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
    $app = require_once __DIR__ . '/../bootstrap/app.php';

    // 5. Pendaftaran Manual Provider Penting (Solusi untuk masalah "view not exist")
    // Kita daftar manual karena auto-discovery gagal di Vercel
    $app->register(\Illuminate\Events\EventServiceProvider::class);
    $app->register(\Illuminate\Routing\RoutingServiceProvider::class);
    $app->register(\Illuminate\View\ViewServiceProvider::class);

    // 6. PAKSA semua jalur ke /tmp
    $app->useStoragePath('/tmp');

    // 7. Jalankan Request
    $kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

    $response = $kernel->handle(
        $request = Request::capture()
    );

    $response->send();

    $kernel->terminate($request, $response);

} catch (\Throwable $e) {
    echo "<h1>CRITICAL ERROR DURING MANIFEST BOOTSTRAP</h1>";
    echo "<p><b>Message:</b> " . $e->getMessage() . "</p>";
    echo "<p><b>File:</b> " . $e->getFile() . "</p>";
    echo "<p><b>Line:</b> " . $e->getLine() . "</p>";
    echo "<h3>Stack Trace:</h3>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}
