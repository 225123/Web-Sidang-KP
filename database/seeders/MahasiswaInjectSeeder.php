<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Mahasiswa;
use App\Models\TahunAjaran;
use Faker\Factory as Faker;

class MahasiswaInjectSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create('id_ID');
        $tahunAjaran = TahunAjaran::where('is_active', true)->first();
        
        if (!$tahunAjaran) {
            $this->command->error('No active Tahun Ajaran found. Seeding one...');
            $tahunAjaran = TahunAjaran::create([
                'semester' => 'Genap',
                'tahun' => '2025/2026',
                'label_tahun_ajaran' => 'Genap 2025/2026',
                'is_active' => true,
            ]);
        }

        $this->command->info('Mulai menyuntikkan 50 data mahasiswa ke tahun ajaran: ' . $tahunAjaran->label_tahun_ajaran);

        for ($i = 0; $i < 50; $i++) {
            $user = User::create([
                'name' => $faker->name,
                'email' => $faker->unique()->safeEmail,
                'password' => Hash::make('password123'),
                'role' => 'mahasiswa',
            ]);

            Mahasiswa::create([
                'user_id' => $user->id,
                'nim' => '120' . $faker->unique()->numerify('#####'),
                'prodi' => 'Teknik Informatika',
                'angkatan' => 2021,
                'no_hp' => $faker->phoneNumber,
                'email' => $user->email,
                'tahun_ajaran_id' => $tahunAjaran->id,
                'status_mahasiswa' => 'Aktif',
                'is_aktif' => true,
            ]);
        }

        $this->command->info('Berhasil menyuntikkan 50 data mahasiswa.');
    }
}
