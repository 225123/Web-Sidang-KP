<?php

// 1. Pengaturan Lingkungan Vercel
putenv('LOG_CHANNEL=stderr');
putenv('APP_DEBUG=true');
putenv('APP_STORAGE=/tmp');

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// 2. Siapkan folder writable di /tmp
$tmpDir = '/tmp/laravel/framework';
foreach (['/views', '/cache', '/sessions', '/logs'] as $path) {
    if (!is_dir($tmpDir . $path)) {
        @mkdir($tmpDir . $path, 0777, true);
    }
}

// 3. JANGAN hapus file di bootstrap/cache. 
// Malah, kita pastikan folder tersebut "terasa" ada isinya agar Laravel tidak mencoba menulis.
$cacheDir = __DIR__ . '/../bootstrap/cache';
if (!is_dir($cacheDir)) {
    @mkdir($cacheDir, 0777, true);
}

// 4. Jalankan aplikasi melalui public/index.php
require __DIR__ . '/../public/index.php';
