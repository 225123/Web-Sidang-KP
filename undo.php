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
    
    // The broken regex wrapped the entire block in {{ \Illuminate\Support\Js::from( ... ) }}
    // The block always starts with: auth()->user()->name
    // Let's match: {{ \Illuminate\Support\Js::from(auth()->user()->name
    // And end with: ) }}
    
    $pattern = '/\{\{\s*\\\\Illuminate\\\\Support\\\\Js::from\((auth\(\)->user\(\)->name.*?)\)\s*\}\}/s';
    
    if (preg_match($pattern, $content, $matches)) {
        // $matches[1] contains the original inner content.
        // We restore it by prepending {{ and appending ->toJson() !!}
        $restored = '{{ ' . $matches[1] . '->toJson() !!}';
        
        $newContent = preg_replace($pattern, $restored, $content);
        file_put_contents($file, $newContent);
        echo "Undone: $file\n";
    }
}
