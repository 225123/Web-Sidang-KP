<?php

use Illuminate\Http\Request;

// 1. Pelaporan error total
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

try {
    // 2. Load Autoloader
    require __DIR__ . '/../vendor/autoload.php';

    // 3. Konfigurasi Lingkungan
    putenv('LOG_CHANNEL=stderr');
    putenv('APP_STORAGE=/tmp');
    $_ENV['APP_STORAGE'] = '/tmp';

    // 4. Bersihkan Cache Bootstrap (Path Windows sering tersangkut di sini)
    $cacheDir = __DIR__ . '/../bootstrap/cache';
    if (is_dir($cacheDir)) {
        foreach (glob("$cacheDir/*.php") as $file) {
            if (basename($file) !== '.gitignore') {
                @unlink($file);
            }
        }
    }

    // 5. Inisialisasi Aplikasi
    $app = require_once __DIR__ . '/../bootstrap/app.php';

    // 6. Daftarkan Provider Dasar Secara Paksa (Agar Laravel bisa melapor jika ada error)
    $app->register(\Illuminate\Filesystem\FilesystemServiceProvider::class);
    $app->register(\Illuminate\View\ViewServiceProvider::class);
    $app->register(\Illuminate\Events\EventServiceProvider::class);
    $app->register(\Illuminate\Routing\RoutingServiceProvider::class);

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
    echo "<h1>DIAGNOSTIC REPORT</h1>";
    echo "<p><b>Message:</b> " . $e->getMessage() . "</p>";
    echo "<p><b>File:</b> " . $e->getFile() . " on line " . $e->getLine() . "</p>";
    
    // Cari error pendahulu (Previous Exception)
    if ($prev = $e->getPrevious()) {
        echo "<hr><h3>Previous Error (The Real Cause):</h3>";
        echo "<p><b>Message:</b> " . $prev->getMessage() . "</p>";
        echo "<p><b>File:</b> " . $prev->getFile() . " on line " . $prev->getLine() . "</p>";
    }

    echo "<hr><h3>Full Stack Trace:</h3>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}
