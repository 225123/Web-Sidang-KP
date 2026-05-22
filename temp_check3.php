<?php
$users = \App\Models\User::whereIn('id', [14, 13, 4])->get();
foreach($users as $u) {
    echo "ID: " . $u->id . " | Nama: " . $u->name . "\n";
}
