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
        Schema::create('riwayat_penolakan_sidangs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('pendaftaran_sidang_id');
            $table->unsignedBigInteger('mahasiswa_id');
            $table->text('feedback')->nullable();
            $table->string('ditolak_oleh')->default('koordinator');
            $table->timestamps();

            // Foreign keys can be added if needed, but keeping it simple
            // $table->foreign('pendaftaran_sidang_id')->references('id')->on('pendaftaran_sidang')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('riwayat_penolakan_sidangs');
    }
};
