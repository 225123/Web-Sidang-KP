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
        Schema::table('pendaftaran_kp', function (Blueprint $table) {
            $table->string('pengerjaan_kp')->default('sendiri')->after('status_kp');
            $table->json('anggota_kelompok_ids')->nullable()->after('pengerjaan_kp');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pendaftaran_kp', function (Blueprint $table) {
            $table->dropColumn(['pengerjaan_kp', 'anggota_kelompok_ids']);
        });
    }
};
