<?php
use App\Models\User;
use App\Models\Mahasiswa;
use Illuminate\Support\Facades\Hash;
$user = User::firstOrCreate(
    ['email' => 'mahasiswadefault2@test.com'],
    [
        'name' => 'Mahasiswa default 2',
        'password' => Hash::make('password'),
        'role' => 'mahasiswa'
    ]
);
Mahasiswa::updateOrCreate(
    ['user_id' => $user->id],
    [
        'nim' => '412023025',
        'program_studi' => 'Informatika'
    ]
);
echo "Account created successfully\n";
