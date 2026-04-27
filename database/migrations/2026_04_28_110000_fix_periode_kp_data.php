<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Delete the incorrectly created duplicate "Ganjil 2025/2026"
        //    (the one that was just opened by mistake — keep the original seeded one)
        //    We keep the one with the lowest ID (original seed), delete any newer duplicates.
        $ganjil2025 = DB::table('tahun_ajaran')
            ->where('label_tahun_ajaran', 'Ganjil 2025/2026')
            ->orderBy('id', 'asc')
            ->get();

        if ($ganjil2025->count() > 1) {
            // Keep the first (original), delete the rest
            $keepId = $ganjil2025->first()->id;
            DB::table('tahun_ajaran')
                ->where('label_tahun_ajaran', 'Ganjil 2025/2026')
                ->where('id', '!=', $keepId)
                ->delete();
        }

        // 2. Deactivate everything
        DB::table('tahun_ajaran')->update(['is_active' => false]);

        // 3. Set "Genap 2025/2026" as active (the correct current period)
        DB::table('tahun_ajaran')
            ->where('label_tahun_ajaran', 'Genap 2025/2026')
            ->update(['is_active' => true]);

        // 4. Make sure the original Ganjil 2025/2026 is marked inactive
        DB::table('tahun_ajaran')
            ->where('label_tahun_ajaran', 'Ganjil 2025/2026')
            ->update(['is_active' => false]);
    }

    public function down(): void {}
};
