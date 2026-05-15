<?php

// 1. Tampilkan error mentah PHP jika terjadi crash fatal
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// 2. Bersihkan sisa-sisa cache bootstrap yang mungkin terbawa dari Windows
$cacheFiles = [
    __DIR__ . '/../bootstrap/cache/config.php',
    __DIR__ . '/../bootstrap/cache/services.php',
    __DIR__ . '/../bootstrap/cache/packages.php',
    __DIR__ . '/../bootstrap/cache/routes-v7.php',
];
foreach ($cacheFiles as $file) {
    if (file_exists($file)) {
        @unlink($file);
    }
}

// 3. Pastikan kita punya folder yang bisa ditulis untuk views & cache
// Vercel hanya mengizinkan penulisan di folder /tmp
$tmpDir = '/tmp/laravel/framework';
foreach (['/views', '/cache', '/sessions'] as $path) {
    if (!is_dir($tmpDir . $path)) {
        mkdir($tmpDir . $path, 0777, true);
    }
}

// 3. Override konfigurasi Laravel secara paksa sebelum aplikasi berjalan
// Kita gunakan variabel env yang akan dibaca oleh config/view.php, config/cache.php, dll
putenv("VIEW_COMPILED_PATH=/tmp/laravel/framework/views");
putenv("SESSION_DRIVER=cookie"); // Hindari database session sementara untuk testing
putenv("LOG_CHANNEL=stderr");    // Arahkan log ke stderr agar muncul di Vercel Logs

// 4. Load Autoloader & Bootstrap
try {
    require __DIR__ . '/../vendor/autoload.php';
    
    /** @var \Illuminate\Foundation\Application $app */
    $app = require_once __DIR__ . '/../bootstrap/app.php';

    // 5. Jalankan Request
    $kernel = $app->make(\Illuminate\Contracts\Http\Kernel::class);
    $response = $kernel->handle(
        $request = \Illuminate\Http\Request::capture()
    );

    $response->send();
    $kernel->terminate($request, $response);

} catch (\Throwable $e) {
    // Jika crash bahkan sebelum Laravel jalan, tangkap di sini!
    header('Content-Type: text/html');
    echo "<h1>Laravel Bootstrap Error</h1>";
    echo "<p><b>Message:</b> " . $e->getMessage() . "</p>";
    echo "<p><b>File:</b> " . $e->getFile() . " on line " . $e->getLine() . "</p>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}
