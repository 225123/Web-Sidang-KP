<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

return new class extends Migration
{
    public function up(): void
    {
        // Only seed if the table is empty
        if (DB::table('tahun_ajaran')->count() === 0) {
            $periods = [
                ['semester' => 'Ganjil', 'tahun' => '2024/2025', 'label_tahun_ajaran' => 'Ganjil 2024/2025', 'is_active' => false, 'tanggal_mulai' => '2024-09-01', 'tanggal_selesai' => '2025-01-31'],
                ['semester' => 'Genap', 'tahun' => '2024/2025', 'label_tahun_ajaran' => 'Genap 2024/2025', 'is_active' => false, 'tanggal_mulai' => '2025-02-01', 'tanggal_selesai' => '2025-07-31'],
                ['semester' => 'Ganjil', 'tahun' => '2025/2026', 'label_tahun_ajaran' => 'Ganjil 2025/2026', 'is_active' => false, 'tanggal_mulai' => '2025-09-01', 'tanggal_selesai' => '2026-01-31'],
                ['semester' => 'Genap', 'tahun' => '2025/2026', 'label_tahun_ajaran' => 'Genap 2025/2026', 'is_active' => true,  'tanggal_mulai' => '2026-02-01', 'tanggal_selesai' => '2026-07-31'],
            ];

            foreach ($periods as $period) {
                DB::table('tahun_ajaran')->insert(array_merge($period, [
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ]));
            }
        }
    }

    public function down(): void
    {
        DB::table('tahun_ajaran')->truncate();
    }
};
