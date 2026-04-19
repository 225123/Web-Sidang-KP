<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

// Instantiate controller
$c = new \App\Http\Controllers\Koordinator\VerifikasiBerkasController();
try {
    $view = $c->index();
    $data = $view->getData();
    
    echo "MAIN ROWS Count: " . count($data['pengajuans']) . "\n";
    echo "REJECTED ROWS Count: " . count($data['ditolaks']) . "\n";
    echo "BELUM KUMPULS Count: " . count($data['belumKumpuls']) . "\n";
    
} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
