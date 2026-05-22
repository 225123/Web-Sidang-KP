<?php

$user = \App\Models\User::where('name', 'like', '%Alexandra%')->with('mahasiswa')->first();
if ($user) {
    echo json_encode($user->toArray(), JSON_PRETTY_PRINT);
} else {
    echo "User not found";
}
