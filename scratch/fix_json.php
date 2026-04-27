<?php
$files = [
    'resources/views/mahasiswa/notifikasi.blade.php',
    'resources/views/mahasiswa/Pendaftaran-KP.blade.php',
    'resources/views/koordinator/persetujuan-sidang.blade.php',
    'resources/views/koordinator/progress-umum.blade.php',
    'resources/views/koordinator/pengumuman.blade.php',
    'resources/views/koordinator/notifikasi.blade.php',
    'resources/views/koordinator/Jadwal-menguji.blade.php',
    'resources/views/dosen/persetujuan-sidang.blade.php',
    'resources/views/dosen/jadwal-menguji.blade.php',
    'resources/views/dosen/daftar-mahasiswa.blade.php'
];

foreach ($files as $file) {
    $path = dirname(__DIR__) . '/' . $file;
    if (file_exists($path)) {
        $content = file_get_contents($path);
        $content = preg_replace('/\{\{\s*(.+?->toJson\(\))\s*\}\}/s', '{!! $1 !!}', $content);
        file_put_contents($path, $content);
        echo "Updated $file\n";
    }
}
