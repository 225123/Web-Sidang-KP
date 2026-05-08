<?php
$user = \App\Models\User::where('name', 'LIKE', '%Geovano%')->first();
echo "Role: " . $user->role . "\n";
