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
        Schema::table('pendaftaran_sidang', function (Blueprint $table) {
            $table->boolean('berita_acara_disubmit')->default(false)->after('status_revisi');
            $table->boolean('nilai_dipublikasi')->default(false)->after('berita_acara_disubmit');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pendaftaran_sidang', function (Blueprint $table) {
            $table->dropColumn(['berita_acara_disubmit', 'nilai_dipublikasi']);
        });
    }
};
