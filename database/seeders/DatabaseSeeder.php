<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Create Active Period (CRUCIAL for fixing 500 error)
        \App\Models\TahunAjaran::updateOrCreate(
            ['semester' => 'Ganjil', 'tahun' => '2024/2025'],
            [
                'label_tahun_ajaran' => 'Ganjil 2024/2025',
                'is_active' => true,
                'tanggal_mulai' => '2024-09-01',
                'tanggal_selesai' => '2025-01-31',
            ]
        );

        $this->call([
            DefaultUserSeeder::class,
        ]);
    }
}
