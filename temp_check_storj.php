<?php

use App\Models\TahunAjaran;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

// 1. Cek Tahun Ajaran
echo "=== TAHUN AJARAN ===\n";
foreach (TahunAjaran::all() as $ta) {
    echo "ID: {$ta->id} | Nama: {$ta->nama_tahun_ajaran} | Semester: {$ta->semester}\n";
}

// 2. Cek Kolom Tahun Ajaran
echo "\n=== KOLOM TAHUN AJARAN ===\n";
$columns = DB::getSchemaBuilder()->getColumnListing('tahun_ajaran');
print_r($columns);

// 3. Kalkulasi Storj
echo "\n=== STORJ S3 ===\n";
$s3Size = 0;
$files = Storage::disk('s3')->allFiles();
foreach ($files as $file) {
    $s3Size += Storage::disk('s3')->size($file);
}
echo "Total Storj Files: " . count($files) . "\n";
echo "Total Storj Size: " . $s3Size . " Bytes\n";

