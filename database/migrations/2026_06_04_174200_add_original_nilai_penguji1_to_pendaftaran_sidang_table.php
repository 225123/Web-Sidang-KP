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
            if (!Schema::hasColumn('pendaftaran_sidang', 'original_n1_laporan')) {
                $table->decimal('original_n1_laporan', 5, 2)->nullable()->after('nilai_penguji_1');
            }
            if (!Schema::hasColumn('pendaftaran_sidang', 'original_n1_produk')) {
                $table->decimal('original_n1_produk', 5, 2)->nullable()->after('original_n1_laporan');
            }
            if (!Schema::hasColumn('pendaftaran_sidang', 'original_n1_presentasi')) {
                $table->decimal('original_n1_presentasi', 5, 2)->nullable()->after('original_n1_produk');
            }
            if (!Schema::hasColumn('pendaftaran_sidang', 'original_nilai_penguji_1')) {
                $table->decimal('original_nilai_penguji_1', 5, 2)->nullable()->after('original_n1_presentasi');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pendaftaran_sidang', function (Blueprint $table) {
            $table->dropColumn([
                'original_n1_laporan',
                'original_n1_produk',
                'original_n1_presentasi',
                'original_nilai_penguji_1'
            ]);
        });
    }
};
