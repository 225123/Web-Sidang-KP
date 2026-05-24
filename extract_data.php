<?php
$files = glob('resources/views/mahasiswa/*.blade.php');
foreach($files as $f) {
    echo "\n================================\n";
    echo "FILE: " . basename($f) . "\n";
    $content = file_get_contents($f);
    
    // Get table headers
    preg_match_all('/<th[^>]*>(.*?)<\/th>/is', $content, $ths);
    if (!empty($ths[1])) {
        echo "TABLE COLUMNS:\n";
        foreach($ths[1] as $th) {
            echo " - " . trim(strip_tags($th)) . "\n";
        }
    }
    
    // Get form inputs/labels
    preg_match_all('/<label[^>]*>(.*?)<\/label>/is', $content, $labels);
    if (!empty($labels[1])) {
        echo "FORM LABELS:\n";
        foreach($labels[1] as $label) {
            $clean = trim(strip_tags($label));
            if(!empty($clean)) echo " - " . $clean . "\n";
        }
    }
}
