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
        Schema::table('pendaftaran_sidang', function (Blueprint $table) {
            $table->date('tanggal_sidang')->nullable();
            $table->time('waktu_mulai_sidang')->nullable();
            $table->time('waktu_selesai_sidang')->nullable();
            $table->string('ruang_sidang')->nullable();
            $table->foreignId('penguji_1_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('penguji_2_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('status_jadwal')->default('draft'); // draft atau submitted
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pendaftaran_sidang', function (Blueprint $table) {
            $table->dropForeign(['penguji_1_id']);
            $table->dropForeign(['penguji_2_id']);
            $table->dropColumn([
                'tanggal_sidang',
                'waktu_mulai_sidang',
                'waktu_selesai_sidang',
                'ruang_sidang',
                'penguji_1_id',
                'penguji_2_id',
                'status_jadwal'
            ]);
        });
    }
};
