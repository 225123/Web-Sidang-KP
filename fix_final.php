<?php

$files = [
    __DIR__ . '/resources/views/dosen/jadwal-menguji.blade.php',
    __DIR__ . '/resources/views/koordinator/bimbingan-saya.blade.php',
    __DIR__ . '/resources/views/koordinator/Jadwal-menguji.blade.php',
    __DIR__ . '/resources/views/koordinator/notifikasi.blade.php',
    __DIR__ . '/resources/views/koordinator/pengumuman.blade.php',
    __DIR__ . '/resources/views/koordinator/persetujuan-sidang.blade.php',
    __DIR__ . '/resources/views/koordinator/progress-umum.blade.php',
    __DIR__ . '/resources/views/mahasiswa/notifikasi.blade.php',
    __DIR__ . '/resources/views/mahasiswa/Pendaftaran-KP.blade.php',
];

foreach ($files as $file) {
    if (!file_exists($file)) continue;
    $content = file_get_contents($file);
    
    // Match {!! ... ->toJson() !!} or {{ ... ->toJson() }} carefully
    // We want to avoid crossing over HTML structure or other tags.
    // Instead of regex, let's match specifically where it happens inside x-data or known objects.
    
    // We can use a regex that matches {!! followed by anything but {!! or !!} until ->toJson() !!}
    $pattern = '/\{!!\s*((?:(?!!\}|\{!!).)*?)->toJson\(\)\s*!!\}/s';
    
    if (preg_match($pattern, $content)) {
        $newContent = preg_replace($pattern, '{{ \Illuminate\Support\Js::from($1) }}', $content);
        if ($newContent !== $content) {
            file_put_contents($file, $newContent);
            echo "Fixed JSON in: $file\n";
        }
    }
}
