<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Kolom status_kp seharusnya nullable karena mahasiswa yang belum mendaftar
     * KP tidak memiliki status (NULL = belum mendaftar).
     * PostgreSQL memiliki NOT NULL constraint yang mencegah insert null.
     */
    public function up(): void
    {
        Schema::table('pendaftaran_kp', function (Blueprint $table) {
            $table->string('status_kp')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Tidak di-revert karena nilai NULL memang valid untuk "belum mendaftar"
    }
};
