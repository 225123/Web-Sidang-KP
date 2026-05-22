<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tahun_ajaran', function (Blueprint $table) {
            $table->softDeletes();
            $table->integer('total_mahasiswa')->nullable()->after('keterangan');
            $table->integer('total_dosen')->nullable()->after('total_mahasiswa');
        });
    }

    public function down(): void
    {
        Schema::table('tahun_ajaran', function (Blueprint $table) {
            $table->dropSoftDeletes();
            $table->dropColumn(['total_mahasiswa', 'total_dosen']);
        });
    }
};
