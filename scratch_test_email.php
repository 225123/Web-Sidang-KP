<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$pengajuan = \App\Models\PendaftaranSidang::with('pendaftaranKp.supervisorInstansi')->where('status_koordinator', 'verified')->latest()->first();
if ($pengajuan) {
    try {
        \Illuminate\Support\Facades\Mail::to('ovanjas1712@gmail.com')->send(new \App\Mail\SupervisorPenilaianMail($pengajuan, 'http://test.com'));
        echo "SUCCESS_SENT\n";
    } catch (\Exception $e) {
        echo "ERROR_SENT: " . $e->getMessage() . "\n";
    }
} else {
    echo "NO_DATA\n";
}
