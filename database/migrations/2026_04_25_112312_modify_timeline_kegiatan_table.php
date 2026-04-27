<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('timeline_kegiatan', function (Blueprint $table) {
            $table->renameColumn('tanggal_mulai', 'tanggal');
            $table->time('waktu')->after('tanggal_mulai')->nullable();
            $table->dropColumn('tanggal_selesai');
        });
    }

    public function down(): void
    {
        Schema::table('timeline_kegiatan', function (Blueprint $table) {
            $table->renameColumn('tanggal', 'tanggal_mulai');
            $table->date('tanggal_selesai')->after('tanggal_mulai')->nullable();
            $table->dropColumn('waktu');
        });
    }
};
