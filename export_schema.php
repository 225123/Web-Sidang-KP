<?php

// export_schema.php
// Script untuk mengekstrak skema database SQLite ke file .sql

$dbPath = __DIR__ . '/database/database.sqlite';
$outputPath = __DIR__ . '/database_schema.sql';

if (!file_exists($dbPath)) {
    die("Database tidak ditemukan di: " . $dbPath . "\n");
}

try {
    $pdo = new PDO('sqlite:' . $dbPath);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Dapatkan semua nama tabel
    $stmt = $pdo->query("SELECT sql FROM sqlite_master WHERE type IN ('table', 'index') AND name NOT LIKE 'sqlite_%' ORDER BY name;");
    $schemas = $stmt->fetchAll(PDO::FETCH_COLUMN);

    $sqlContent = "-- Dump Skema Database KP-Web_Sidang_KP\n";
    $sqlContent .= "-- Dibuat pada: " . date('Y-m-d H:i:s') . "\n\n";

    foreach ($schemas as $schema) {
        if (!empty($schema)) {
            $sqlContent .= $schema . ";\n\n";
        }
    }

    file_put_contents($outputPath, $sqlContent);

    echo "Berhasil! Skema database telah diexport ke: " . $outputPath . "\n";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
