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
        Schema::table('mahasiswa', function (Blueprint $table) {
            if (!Schema::hasColumn('mahasiswa', 'tahun_ajaran_id')) {
                $table->unsignedBigInteger('tahun_ajaran_id')->nullable();
            }
            if (!Schema::hasColumn('mahasiswa', 'status_mahasiswa')) {
                $table->string('status_mahasiswa')->default('Aktif');
            }
            if (!Schema::hasColumn('mahasiswa', 'is_aktif')) {
                $table->boolean('is_aktif')->default(true);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mahasiswa', function (Blueprint $table) {
            $table->dropColumn('tahun_ajaran_id');
            $table->dropColumn('status_mahasiswa');
            $table->dropColumn('is_aktif');
        });
    }
};
