<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Recreate timeline_kegiatan with the correct schema expected by all controllers.
     * Old schema had: tahun_ajaran_id, nama_kegiatan, tanggal_mulai, tanggal_selesai, warna
     * New schema has: periode_id, nama_kegiatan, tanggal, waktu, kategori, keterangan
     */
    public function up(): void
    {
        Schema::dropIfExists('timeline_kegiatan');

        Schema::create('timeline_kegiatan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('periode_id')->nullable()->constrained('tahun_ajaran')->onDelete('cascade');
            $table->string('nama_kegiatan');
            $table->date('tanggal');
            $table->time('waktu')->nullable();
            $table->string('kategori')->default('mahasiswa'); // 'mahasiswa' | 'dosen'
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('timeline_kegiatan');

        Schema::create('timeline_kegiatan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tahun_ajaran_id')->constrained('tahun_ajaran')->onDelete('cascade');
            $table->string('nama_kegiatan');
            $table->date('tanggal_mulai');
            $table->date('tanggal_selesai');
            $table->string('warna')->default('blue');
            $table->timestamps();
        });
    }
};
