<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Users Table
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');
            $table->string('role')->default('mahasiswa'); // mahasiswa, dosen, koordinator
            $table->string('avatar')->nullable();
            $table->string('signature_path')->nullable();
            $table->string('reset_token')->nullable();
            $table->timestamp('reset_token_expires')->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->rememberToken();
            $table->timestamps();
        });

        // 2. Tahun Ajaran Table
        Schema::create('tahun_ajaran', function (Blueprint $table) {
            $table->id();
            $table->string('semester');
            $table->string('tahun');
            $table->string('label_tahun_ajaran');
            $table->boolean('is_active')->default(false);
            $table->date('tanggal_mulai')->nullable();
            $table->date('tanggal_selesai')->nullable();
            $table->text('keterangan')->nullable();
            $table->foreignId('koordinator_id')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
        });

        // 3. Mahasiswa Table
        Schema::create('mahasiswa', function (Blueprint $table) {
            $table->string('nim')->primary();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('prodi')->nullable();
            $table->string('angkatan')->nullable();
            $table->string('no_hp')->nullable();
            $table->string('email')->nullable();
            $table->foreignId('pembimbing_id')->nullable()->constrained('users')->onDelete('set null');
        });

        // 4. Dosen Table
        Schema::create('dosen', function (Blueprint $table) {
            $table->string('nidn')->primary();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->integer('kuota_bimbingan')->default(10);
            $table->boolean('is_aktif')->default(true);
            $table->string('no_hp')->nullable();
            // Dosen model uses public $timestamps = false
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dosen');
        Schema::dropIfExists('mahasiswa');
        Schema::dropIfExists('tahun_ajaran');
        Schema::dropIfExists('users');
    }
};
