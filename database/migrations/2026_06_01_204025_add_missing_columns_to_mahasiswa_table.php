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
            $table->unsignedBigInteger('tahun_ajaran_id')->nullable();
            $table->string('status_mahasiswa')->default('Aktif');
            $table->boolean('is_aktif')->default(true);
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
