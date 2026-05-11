<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Pendaftaran Sidang Table
        Schema::create('pendaftaran_sidang', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pendaftaran_kp_id')->constrained('pendaftaran_kp')->onDelete('cascade');
            $table->foreignId('mahasiswa_id')->constrained('users')->onDelete('cascade');
            
            // Files
            $table->string('file_laporan')->nullable();
            $table->string('file_log_bimbingan')->nullable();
            $table->string('file_persetujuan_pembimbing')->nullable();
            $table->string('file_nilai_supervisor')->nullable();
            $table->string('file_berkas_lainnya')->nullable();
            
            // Links
            $table->string('link_github')->nullable();
            $table->string('link_drive')->nullable();
            $table->string('link_deploy')->nullable();
            
            // Statuses
            $table->string('status_verifikasi')->default('Pending');
            $table->string('status_koordinator')->default('Pending');
            $table->text('koordinator_feedback')->nullable();
            $table->text('dosen_feedback')->nullable();
            
            // Schedule
            $table->date('tanggal_sidang')->nullable();
            $table->time('waktu_mulai_sidang')->nullable();
            $table->time('waktu_selesai_sidang')->nullable();
            $table->string('ruang_sidang')->nullable();
            $table->string('status_jadwal')->default('Belum Dijadwalkan');
            $table->foreignId('penguji_1_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('penguji_2_id')->nullable()->constrained('users')->onDelete('set null');
            
            // Grades (Decimal for precision)
            $table->decimal('nilai_pembimbing', 5, 2)->nullable();
            $table->decimal('nilai_penguji_1', 5, 2)->nullable();
            $table->decimal('nilai_penguji_2', 5, 2)->nullable();
            $table->decimal('nilai_akhir', 5, 2)->nullable();
            $table->string('grade', 5)->nullable();
            $table->text('catatan_sidang')->nullable();
            $table->string('status_kelulusan')->nullable();
            
            // New Detail Grades
            $table->string('pelaksanaan')->nullable();
            $table->decimal('nb_laporan', 5, 2)->nullable();
            $table->decimal('nb_produk', 5, 2)->nullable();
            $table->decimal('nb_sikap', 5, 2)->nullable();
            $table->decimal('n1_laporan', 5, 2)->nullable();
            $table->decimal('n1_produk', 5, 2)->nullable();
            $table->decimal('n1_presentasi', 5, 2)->nullable();
            $table->decimal('n2_laporan', 5, 2)->nullable();
            $table->decimal('n2_produk', 5, 2)->nullable();
            $table->decimal('n2_presentasi', 5, 2)->nullable();
            $table->decimal('ns_motivasi', 5, 2)->nullable();
            $table->decimal('ns_kualitas', 5, 2)->nullable();
            $table->decimal('ns_inisiatif', 5, 2)->nullable();
            $table->decimal('ns_sikap', 5, 2)->nullable();
            $table->decimal('nilai_supervisor', 5, 2)->nullable();
            
            // Revision
            $table->string('file_revisi')->nullable();
            $table->string('link_revisi')->nullable();
            $table->string('status_revisi')->default('Belum Reviu');
            $table->date('tanggal_revisi')->nullable();
            
            // Flags
            $table->boolean('berita_acara_disubmit')->default(false);
            $table->boolean('nilai_dipublikasi')->default(false);
            $table->string('token_penilaian_supervisor')->nullable();
            $table->boolean('is_penilaian_supervisor_submitted')->default(false);
            
            $table->timestamps();
        });

        // 2. Riwayat Penolakan Table
        Schema::create('riwayat_penolakan_sidang', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pendaftaran_sidang_id')->constrained('pendaftaran_sidang')->onDelete('cascade');
            $table->text('alasan_penolakan');
            $table->string('ditolak_oleh'); // Koordinator / Tata Usaha
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('riwayat_penolakan_sidang');
        Schema::dropIfExists('pendaftaran_sidang');
    }
};
