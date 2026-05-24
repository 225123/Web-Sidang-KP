<?php
$files = glob('resources/views/mahasiswa/*.blade.php');
foreach($files as $f) {
    $content = file_get_contents($f);
    preg_match_all('/<(button|a)[^>]*>(.*?)<\/\1>/is', $content, $matches);
    echo strtoupper(basename($f)) . "\n";
    foreach($matches[0] as $match) {
        $clean = trim(strip_tags($match));
        if(!empty($clean)) {
            // Find class or title for context
            preg_match('/class=\"([^\"]*)\"/', $match, $class);
            preg_match('/title=\"([^\"]*)\"/', $match, $title);
            echo "  - Text: " . substr(str_replace("\n", " ", $clean), 0, 50) . "\n";
            if(isset($title[1])) echo "    Title: " . $title[1] . "\n";
        }
    }
    echo "\n";
}
