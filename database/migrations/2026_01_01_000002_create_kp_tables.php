<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Pendaftaran KP Table
        Schema::create('pendaftaran_kp', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mahasiswa_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('tahun_ajaran_id')->constrained('tahun_ajaran')->onDelete('cascade');
            $table->string('judul_kp')->nullable();
            $table->string('jenis_proyek')->nullable();
            $table->string('instansi_nama')->nullable();
            $table->text('instansi_alamat')->nullable();
            $table->foreignId('pembimbing_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('status_kp')->default('Pending');
            $table->boolean('is_lanjutan')->default(false);
            $table->foreignId('pendaftaran_asal_id')->nullable()->constrained('pendaftaran_kp')->onDelete('set null');
            $table->string('jenis_instansi')->nullable();
            $table->foreignId('supervisor_internal_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('tipe_kp')->nullable();
            $table->string('pengerjaan_kp')->nullable(); // Individu / Kelompok
            $table->json('anggota_kelompok_ids')->nullable();
            $table->text('catatan')->nullable();
            $table->timestamps();
        });

        // 2. Supervisor Instansi Table
        Schema::create('supervisor_instansi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pendaftaran_kp_id')->constrained('pendaftaran_kp')->onDelete('cascade');
            $table->string('nama_supervisor')->nullable();
            $table->string('kontak_supervisor')->nullable();
            $table->string('no_hp_supervisor')->nullable();
            $table->string('email_supervisor')->nullable();
            $table->string('jabatan_supervisor')->nullable();
        });

        // 3. Log Bimbingan Table
        Schema::create('log_bimbingan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pendaftaran_kp_id')->constrained('pendaftaran_kp')->onDelete('cascade');
            $table->foreignId('mahasiswa_id')->constrained('users')->onDelete('cascade');
            $table->date('tanggal');
            $table->text('materi_bahasan');
            $table->string('file_progress')->nullable();
            $table->string('status_approval')->default('Pending');
            $table->text('komentar_dosen')->nullable();
            $table->boolean('is_supervisor')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('log_bimbingan');
        Schema::dropIfExists('supervisor_instansi');
        Schema::dropIfExists('pendaftaran_kp');
    }
};
