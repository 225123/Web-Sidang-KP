<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tahun_ajaran', function (Blueprint $table) {
            if (!Schema::hasColumn('tahun_ajaran', 'tanggal_mulai')) {
                $table->date('tanggal_mulai')->nullable()->after('semester');
            }
            if (!Schema::hasColumn('tahun_ajaran', 'tanggal_selesai')) {
                $table->date('tanggal_selesai')->nullable()->after('tanggal_mulai');
            }
            if (!Schema::hasColumn('tahun_ajaran', 'keterangan')) {
                $table->text('keterangan')->nullable()->after('tanggal_selesai');
            }
        });
    }

    public function down(): void
    {
        Schema::table('tahun_ajaran', function (Blueprint $table) {
            $table->dropColumn(['tanggal_mulai', 'tanggal_selesai', 'keterangan']);
        });
    }
};
