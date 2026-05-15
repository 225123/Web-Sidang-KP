<?php

use Illuminate\Http\Request;

// 1. Pelaporan error total
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

try {
    // 2. Load Autoloader
    require __DIR__ . '/../vendor/autoload.php';

    // 3. SOLUSI CACHE BOOTSTRAP: Paksa semua file cache ke /tmp
    putenv('APP_SERVICES_CACHE=/tmp/services.php');
    putenv('APP_PACKAGES_CACHE=/tmp/packages.php');
    putenv('APP_CONFIG_CACHE=/tmp/config.php');
    putenv('APP_ROUTES_CACHE=/tmp/routes.php');
    putenv('APP_EVENTS_CACHE=/tmp/events.php');
    
    // 4. SOLUSI VIEW COMPILER: Paksa jalur kompilasi blade ke /tmp
    // Folder ini HARUS ada agar Blade tidak protes "Invalid cache path"
    $viewPath = '/tmp/framework/views';
    if (!is_dir($viewPath)) {
        @mkdir($viewPath, 0777, true);
    }
    putenv("VIEW_COMPILED_PATH=$viewPath");
    $_ENV['VIEW_COMPILED_PATH'] = $viewPath;

    // 5. Konfigurasi Lingkungan Lainnya
    putenv('LOG_CHANNEL=stderr');
    putenv('APP_STORAGE=/tmp');
    $_ENV['APP_STORAGE'] = '/tmp';

    // 6. Inisialisasi Aplikasi
    /** @var \Illuminate\Foundation\Application $app */
    $app = require_once __DIR__ . '/../bootstrap/app.php';

    // 7. Paksa jalur penyimpanan ke /tmp
    $app->useStoragePath('/tmp');

    // 8. Jalankan Kernel
    $kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

    $response = $kernel->handle(
        $request = Request::capture()
    );

    $response->send();

    $kernel->terminate($request, $response);

} catch (\Throwable $e) {
    echo "<h1>APPLICATION HALTED</h1>";
    echo "<p><b>Message:</b> " . $e->getMessage() . "</p>";
    echo "<p><b>File:</b> " . $e->getFile() . " on line " . $e->getLine() . "</p>";
    
    if ($prev = $e->getPrevious()) {
        echo "<hr><h3>Previous Error:</h3>";
        echo "<p><b>Message:</b> " . $prev->getMessage() . "</p>";
    }

    echo "<hr><h3>Trace:</h3>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}
