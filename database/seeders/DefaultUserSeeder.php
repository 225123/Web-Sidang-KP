<?php

namespace Database\Seeders;

use App\Models\Dosen;
use App\Models\Mahasiswa;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DefaultUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Mahasiswa Default
        $mahasiswaUser = User::updateOrCreate(
            ['email' => 'mahasiswa@example.com'],
            [
                'name' => 'Mahasiswa Default',
                'password' => Hash::make('password'),
                'role' => 'mahasiswa',
            ]
        );

        Mahasiswa::updateOrCreate(
            ['nim' => '412023024'],
            [
                'user_id' => $mahasiswaUser->id,
                'prodi' => 'Informatika',
                'no_hp' => '081234567890',
                'email' => 'mahasiswa.nim@example.com',
            ]
        );

        // 2. Koordinator Default
        $koordinatorUser = User::updateOrCreate(
            ['email' => 'koordinator@example.com'],
            [
                'name' => 'Koordinator Default',
                'password' => Hash::make('password'),
                'role' => 'koordinator_kp',
            ]
        );

        Dosen::updateOrCreate(
            ['nidn' => '1234567890'],
            [
                'user_id' => $koordinatorUser->id,
                'kuota_bimbingan' => 20,
                'is_aktif' => true,
            ]
        );

        // 3. Dosen Default
        $dosenUser = User::updateOrCreate(
            ['email' => 'dosen@example.com'],
            [
                'name' => 'Dosen Default',
                'password' => Hash::make('password'),
                'role' => 'dosen',
            ]
        );

        Dosen::updateOrCreate(
            ['nidn' => '0987654321'],
            [
                'user_id' => $dosenUser->id,
                'kuota_bimbingan' => 10,
                'is_aktif' => true,
            ]
        );
    }
}
