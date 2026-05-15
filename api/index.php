<?php

// 1. Tampilkan error secara verbose
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// 2. CEK APP_KEY (Ini sangat krusial!)
if (empty(getenv('APP_KEY'))) {
    die("<h1>ERROR: APP_KEY TIDAK DITEMUKAN!</h1><p>Anda belum memasukkan <b>APP_KEY</b> di Vercel Environment Variables. Laravel tidak bisa berjalan tanpanya.</p>");
}

// 3. Paksa folder cache ke /tmp
putenv('APP_CONFIG_CACHE=/tmp/config.php');
putenv('APP_EVENTS_CACHE=/tmp/events.php');
putenv('APP_PACKAGES_CACHE=/tmp/packages.php');
putenv('APP_ROUTES_CACHE=/tmp/routes.php');
putenv('VIEW_COMPILED_PATH=/tmp');

// 4. Jalankan aplikasi
require __DIR__ . '/../public/index.php';
