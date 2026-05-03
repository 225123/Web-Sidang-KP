<?php
$db = new SQLite3(__DIR__ . '/../database/database.sqlite');
$result = $db->query("SELECT name FROM sqlite_master WHERE type='table' ORDER BY name");
while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
    echo $row['name'] . "\n";
}
