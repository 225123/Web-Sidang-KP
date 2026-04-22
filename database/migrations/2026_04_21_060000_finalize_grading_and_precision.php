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
            // Enum for execution status if missing
            if (!Schema::hasColumn('pendaftaran_sidang', 'pelaksanaan')) {
                $table->enum('pelaksanaan', ['Menunggu', 'Berjalan', 'Selesai', 'Dibatalkan'])->default('Menunggu');
            }

            $cols63 = [
                'nb_laporan', 'nb_produk', 'nb_sikap',
                'n1_laporan', 'n1_produk', 'n1_presentasi',
                'n2_laporan', 'n2_produk', 'n2_presentasi',
                'ns_motivasi', 'ns_kualitas', 'ns_inisiatif', 'ns_sikap',
                'nilai_pembimbing', 'nilai_penguji_1', 'nilai_penguji_2', 'nilai_supervisor', 'nilai_akhir'
            ];

            foreach ($cols63 as $col) {
                if (Schema::hasColumn('pendaftaran_sidang', $col)) {
                    $table->decimal($col, 6, 3)->nullable()->change();
                } else {
                    $table->decimal($col, 6, 3)->nullable();
                }
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pendaftaran_sidang', function (Blueprint $table) {
            $cols52 = [
                'nb_laporan', 'nb_produk', 'nb_sikap',
                'n1_laporan', 'n1_produk', 'n1_presentasi',
                'n2_laporan', 'n2_produk', 'n2_presentasi',
                'ns_motivasi', 'ns_kualitas', 'ns_inisiatif', 'ns_sikap',
                'nilai_pembimbing', 'nilai_penguji_1', 'nilai_penguji_2', 'nilai_supervisor', 'nilai_akhir'
            ];

            foreach ($cols52 as $col) {
                $table->decimal($col, 5, 2)->nullable()->change();
            }
        });
    }
};
