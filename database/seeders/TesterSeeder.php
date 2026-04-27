<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class TesterSeeder extends Seeder
{
    public function run()
    {
        // Menggunakan Transaction agar jika satu gagal, gagal semua (aman)
        DB::transaction(function () {

            // ==========================================
            // 1. AKUN TESTER MAHASISWA
            // ==========================================
            $mahasiswa = User::create([
                'name' => 'Mahasiswa - 412011111',
                'email' => 'mahasiswa_tester@test.com', // Email dummy
                'password' => Hash::make('password'), // Password: password
                'role' => 'mahasiswa',
            ]);

            DB::table('mahasiswa')->insert([
                'user_id' => $mahasiswa->id,
                'nim' => '412011111',
                'prodi' => 'Informatika',
                'email' => 'mahasiswa_tester@test.com',
            ]);

            // ==========================================
            // 2. AKUN TESTER KOORDINATOR
            // ==========================================
            $koor = User::create([
                'name' => 'Koordinator - 1111111111',
                'email' => 'koor@test.com', // Email dummy
                'password' => Hash::make('password'), // Password: password
                'role' => 'koordinator_kp',
            ]);

            DB::table('dosen')->insert([
                'user_id' => $koor->id,
                'nidn' => '1111111111', // ID Koordinator
                'is_aktif' => true,
            ]);

            // ==========================================
            // 3. AKUN TESTER DOSEN
            // ==========================================
            $dosen = User::create([
                'name' => 'Dosen - 2222222222',
                'email' => 'dosen@test.com', // Email dummy
                'password' => Hash::make('password'), // Password: password
                'role' => 'dosen',
            ]);

            DB::table('dosen')->insert([
                'user_id' => $dosen->id,
                'nidn' => '2222222222', // ID Dosen
                'is_aktif' => true,
            ]);

        });
    }
}
