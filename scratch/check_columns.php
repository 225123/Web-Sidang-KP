<?php

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Kernel::class);
$kernel->bootstrap();

use Illuminate\Contracts\Console\Kernel;
use Illuminate\Support\Facades\Schema;

$columns = Schema::getColumnListing('log_bimbingan');
echo "Columns in 'log_bimbingan' table:\n";
print_r($columns);
