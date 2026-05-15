<?php

use Illuminate\Http\Request;

// 1. Pelaporan error total
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

try {
    // 2. Load Autoloader
    require __DIR__ . '/../vendor/autoload.php';

    // 3. SOLUSI TOTAL: Paksa semua file cache bootstrap ke /tmp
    // Ini mencegah error "bootstrap/cache directory must be writable"
    putenv('APP_SERVICES_CACHE=/tmp/services.php');
    putenv('APP_PACKAGES_CACHE=/tmp/packages.php');
    putenv('APP_CONFIG_CACHE=/tmp/config.php');
    putenv('APP_ROUTES_CACHE=/tmp/routes.php');
    putenv('APP_EVENTS_CACHE=/tmp/events.php');
    
    // Setel juga di $_ENV agar Laravel membacanya dengan pasti
    $_ENV['APP_SERVICES_CACHE'] = '/tmp/services.php';
    $_ENV['APP_PACKAGES_CACHE'] = '/tmp/packages.php';
    $_ENV['APP_CONFIG_CACHE'] = '/tmp/config.php';
    $_ENV['APP_ROUTES_CACHE'] = '/tmp/routes.php';
    $_ENV['APP_EVENTS_CACHE'] = '/tmp/events.php';

    // 4. Konfigurasi Lingkungan Lainnya
    putenv('LOG_CHANNEL=stderr');
    putenv('APP_STORAGE=/tmp');
    $_ENV['APP_STORAGE'] = '/tmp';

    // 5. Inisialisasi Aplikasi
    /** @var \Illuminate\Foundation\Application $app */
    $app = require_once __DIR__ . '/../bootstrap/app.php';

    // 6. Paksa jalur penyimpanan ke /tmp
    $app->useStoragePath('/tmp');

    // 7. Jalankan Kernel
    $kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

    $response = $kernel->handle(
        $request = Request::capture()
    );

    $response->send();

    $kernel->terminate($request, $response);

} catch (\Throwable $e) {
    echo "<h1>CRITICAL ERROR DETECTED</h1>";
    echo "<p><b>Message:</b> " . $e->getMessage() . "</p>";
    echo "<p><b>File:</b> " . $e->getFile() . " on line " . $e->getLine() . "</p>";
    
    if ($prev = $e->getPrevious()) {
        echo "<hr><h3>Underlying Error:</h3>";
        echo "<p><b>Message:</b> " . $prev->getMessage() . "</p>";
        echo "<p><b>File:</b> " . $prev->getFile() . " on line " . $prev->getLine() . "</p>";
    }

    echo "<hr><h3>Stack Trace:</h3>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}
