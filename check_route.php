<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
echo route('koordinator.notifikasi.show', 1) . PHP_EOL;
echo route('koordinator.notifikasi.redirect', 1) . PHP_EOL;
