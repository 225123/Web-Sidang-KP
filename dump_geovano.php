<?php
$user = \App\Models\User::where('name', 'LIKE', '%Geovano%')->first();
if ($user) {
    echo "ID: " . $user->id . "\n";
    echo "Name: " . $user->name . "\n";
    echo "Email: " . $user->email . "\n";
    echo "Role: " . $user->role . "\n";
    
    $mhs = \App\Models\Mahasiswa::where('user_id', $user->id)->first();
    echo "Is in Mahasiswa table? " . ($mhs ? "Yes, NIM: ".$mhs->nim : "No") . "\n";

    $dosen = \App\Models\Dosen::where('user_id', $user->id)->first();
    echo "Is in Dosen table? " . ($dosen ? "Yes, NIDN: ".$dosen->nidn : "No") . "\n";
} else {
    echo "User not found";
}
