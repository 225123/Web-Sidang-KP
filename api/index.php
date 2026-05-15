<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Bersihkan cache bootstrap
$cacheDir = __DIR__ . '/../bootstrap/cache';
foreach (glob("$cacheDir/*.php") as $file) {
    if (basename($file) !== '.gitignore') {
        @unlink($file);
    }
}

try {
    require __DIR__ . '/../public/index.php';
} catch (\Throwable $e) {
    header('Content-Type: text/html');
    echo "<h1>Critical Bootstrap Error</h1>";
    
    // Tampilkan pesan error utama
    echo "<p><b>Primary Error:</b> " . $e->getMessage() . "</p>";
    echo "<p><b>File:</b> " . $e->getFile() . " on line " . $e->getLine() . "</p>";

    // Bongkar "Previous Exception" jika ada (ini yang biasanya jadi masalah asli)
    $prev = $e->getPrevious();
    while ($prev) {
        echo "<hr>";
        echo "<h3>Previous Error:</h3>";
        echo "<p><b>Message:</b> " . $prev->getMessage() . "</p>";
        echo "<p><b>File:</b> " . $prev->getFile() . " on line " . $prev->getLine() . "</p>";
        $prev = $prev->getPrevious();
    }

    echo "<hr><h3>Stack Trace:</h3>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}
