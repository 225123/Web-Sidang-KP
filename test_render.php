<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$user = App\Models\User::where('role', 'mahasiswa')->first();
if (!$user) {
    die("No mahasiswa user");
}

auth()->login($user);

try {
    $html = view('mahasiswa.Status-Pendaftaran', [
        'riwayatKp' => App\Models\PendaftaranKp::paginate(10)
    ])->render();
    echo "SUCCESS\n";
    file_put_contents('test_out.html', $html);
} catch (\Throwable $e) {
    echo "ERROR: " . $e->getMessage() . " in " . $e->getFile() . ":" . $e->getLine() . "\n";
}
