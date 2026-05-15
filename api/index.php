<?php

use Illuminate\Http\Request;
use Illuminate\Foundation\PackageManifest;
use Illuminate\Filesystem\Filesystem;

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

    // 4. Inisialisasi Aplikasi
    /** @var \Illuminate\Foundation\Application $app */
    $app = require_once __DIR__ . '/../bootstrap/app.php';

    // 5. SOLUSI JALUR: Paksa PackageManifest menggunakan folder /tmp
    $app->instance(PackageManifest::class, new PackageManifest(
        new Filesystem,
        $app->basePath(),
        '/tmp/packages.php'
    ));

    // 6. DAFTARKAN PROVIDER INTI SECARA MANUAL
    // Ini agar jika terjadi error saat bootstrap, Exception Handler bisa menampilkan errornya (karena 'view' & 'files' sudah ada)
    $app->register(\Illuminate\Filesystem\FilesystemServiceProvider::class);
    $app->register(\Illuminate\View\ViewServiceProvider::class);

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
    echo "<h1>ROOT ERROR REVEALED</h1>";
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
