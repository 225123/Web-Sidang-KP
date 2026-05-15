<?php

// 1. Matikan error reporting verbose untuk produksi (opsional, tapi biarkan dulu untuk debug)
ini_set('display_errors', 1);
error_reporting(E_ALL);

// 2. Bersihkan folder cache bootstrap agar tidak ada path Windows
$cacheDir = __DIR__ . '/../bootstrap/cache';
foreach (glob("$cacheDir/*.php") as $file) {
    if (basename($file) !== '.gitignore') {
        @unlink($file);
    }
}

// 3. Panggil index asli Laravel
require __DIR__ . '/../public/index.php';
