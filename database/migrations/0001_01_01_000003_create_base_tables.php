<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dosen', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')->unique();
            $table->string('nidn')->primary();
            $table->integer('kuota_bimbingan')->default(10);
            $table->boolean('is_aktif')->default(true);
            $table->string('no_hp')->nullable();
        });

        Schema::create('mahasiswa', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')->unique();
            $table->string('nim')->primary();
            $table->string('prodi')->nullable();
            $table->string('angkatan')->nullable();
            $table->string('no_hp')->nullable();
            $table->string('email')->nullable();
            $table->unsignedBigInteger('pembimbing_id')->nullable();
        });

        Schema::create('pendaftaran_kp', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('mahasiswa_id');
            $table->unsignedBigInteger('tahun_ajaran_id')->nullable();
            $table->string('judul_kp')->nullable();
            $table->string('jenis_proyek')->nullable();
            $table->string('instansi_nama')->nullable();
            $table->text('instansi_alamat')->nullable();
            $table->unsignedBigInteger('pembimbing_id')->nullable();
            $table->string('status_kp')->nullable();
            $table->boolean('is_lanjutan')->default(false);
            $table->unsignedBigInteger('pendaftaran_asal_id')->nullable();
            $table->string('jenis_instansi')->nullable();
            $table->unsignedBigInteger('supervisor_internal_id')->nullable();
            $table->string('tipe_kp')->nullable();
            $table->string('pengerjaan_kp')->default('individu');
            $table->json('anggota_kelompok_ids')->nullable();
            $table->timestamps();
        });

        Schema::create('pendaftaran_sidang', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('pendaftaran_kp_id');
            $table->unsignedBigInteger('mahasiswa_id');
            $table->string('file_laporan')->nullable();
            $table->string('file_log_bimbingan')->nullable();
            $table->string('file_persetujuan_pembimbing')->nullable();
            $table->string('file_nilai_supervisor')->nullable();
            $table->string('file_berkas_lainnya')->nullable();
            $table->string('link_github')->nullable();
            $table->string('link_drive')->nullable();
            $table->string('link_deploy')->nullable();
            $table->string('status_verifikasi')->default('Pending');
            $table->string('status_koordinator')->default('Pending');
            $table->text('koordinator_feedback')->nullable();
            $table->text('dosen_feedback')->nullable();
            $table->date('tanggal_sidang')->nullable();
            $table->time('waktu_mulai_sidang')->nullable();
            $table->time('waktu_selesai_sidang')->nullable();
            $table->string('ruang_sidang')->nullable();
            $table->string('status_jadwal')->default('Belum Dijadwalkan');
            $table->unsignedBigInteger('penguji_1_id')->nullable();
            $table->unsignedBigInteger('penguji_2_id')->nullable();
            $table->decimal('nilai_pembimbing', 8, 2)->nullable();
            $table->decimal('nilai_penguji_1', 8, 2)->nullable();
            $table->decimal('nilai_penguji_2', 8, 2)->nullable();
            $table->decimal('nilai_akhir', 8, 2)->nullable();
            $table->string('grade')->nullable();
            $table->text('catatan_sidang')->nullable();
            $table->string('status_kelulusan')->nullable();
            $table->string('pelaksanaan')->nullable();
            // Fields for detailed grading
            $table->decimal('nb_laporan', 8, 2)->nullable();
            $table->decimal('nb_produk', 8, 2)->nullable();
            $table->decimal('nb_sikap', 8, 2)->nullable();
            $table->decimal('n1_laporan', 8, 2)->nullable();
            $table->decimal('n1_produk', 8, 2)->nullable();
            $table->decimal('n1_presentasi', 8, 2)->nullable();
            $table->decimal('n2_laporan', 8, 2)->nullable();
            $table->decimal('n2_produk', 8, 2)->nullable();
            $table->decimal('n2_presentasi', 8, 2)->nullable();
            $table->decimal('ns_motivasi', 8, 2)->nullable();
            $table->decimal('ns_kualitas', 8, 2)->nullable();
            $table->decimal('ns_inisiatif', 8, 2)->nullable();
            $table->decimal('ns_sikap', 8, 2)->nullable();
            $table->decimal('nilai_supervisor', 8, 2)->nullable();
            $table->string('file_revisi')->nullable();
            $table->string('link_revisi')->nullable();
            $table->string('status_revisi')->nullable();
            $table->timestamp('tanggal_revisi')->nullable();
        });

        Schema::create('supervisor_instansi', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('pendaftaran_kp_id');
            $table->string('nama_supervisor');
            $table->string('no_hp_supervisor')->nullable();
            $table->string('email_supervisor')->nullable();
            $table->string('jabatan_supervisor')->nullable();
            $table->timestamps();
        });

        Schema::create('log_bimbingan', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('pendaftaran_kp_id');
            $table->unsignedBigInteger('mahasiswa_id')->nullable();
            $table->date('tanggal_bimbingan');
            $table->text('materi_bimbingan');
            $table->string('file_lampiran')->nullable();
            $table->string('status_bimbingan')->default('pending');
            $table->text('komentar_dosen')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('log_bimbingan');
        Schema::dropIfExists('supervisor_instansi');
        Schema::dropIfExists('pendaftaran_sidang');
        Schema::dropIfExists('pendaftaran_kp');
        Schema::dropIfExists('mahasiswa');
        Schema::dropIfExists('dosen');
    }
};
