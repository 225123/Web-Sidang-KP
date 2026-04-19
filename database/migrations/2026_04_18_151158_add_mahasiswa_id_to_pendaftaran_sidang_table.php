<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Drop constraint manually due to postgres naming conventions
        \Illuminate\Support\Facades\DB::statement('ALTER TABLE pendaftaran_sidang DROP CONSTRAINT IF EXISTS pendaftaran_sidang_pendaftaran_kp_id_unique CASCADE;');
        \Illuminate\Support\Facades\DB::statement('ALTER TABLE pendaftaran_sidang DROP CONSTRAINT IF EXISTS pendaftaran_sidang_pendaftaran_kp_id_key CASCADE;');

        Schema::table('pendaftaran_sidang', function (Blueprint $table) {
            $table->unsignedBigInteger('mahasiswa_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pendaftaran_sidang', function (Blueprint $table) {
            $table->dropColumn('mahasiswa_id');
            // Catatan: mengembalikan constraint unique mungkin akan gagal jika ada data ganda
            $table->unique('pendaftaran_kp_id');
        });
    }
};
