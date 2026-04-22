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
            // Update precision to 3 decimal places (decimal(6, 3) allows up to 999.999)
            $table->decimal('nb_laporan', 6, 3)->change();
            $table->decimal('nb_produk', 6, 3)->change();
            $table->decimal('nb_sikap', 6, 3)->change();

            $table->decimal('n1_laporan', 6, 3)->change();
            $table->decimal('n1_produk', 6, 3)->change();
            $table->decimal('n1_presentasi', 6, 3)->change();

            $table->decimal('n2_laporan', 6, 3)->change();
            $table->decimal('n2_produk', 6, 3)->change();
            $table->decimal('n2_presentasi', 6, 3)->change();

            $table->decimal('ns_motivasi', 6, 3)->change();
            $table->decimal('ns_kualitas', 6, 3)->change();
            $table->decimal('ns_inisiatif', 6, 3)->change();
            $table->decimal('ns_sikap', 6, 3)->change();

            $table->decimal('nilai_pembimbing', 6, 3)->change();
            $table->decimal('nilai_penguji_1', 6, 3)->change();
            $table->decimal('nilai_penguji_2', 6, 3)->change();
            $table->decimal('nilai_supervisor', 6, 3)->change();
            $table->decimal('nilai_akhir', 6, 3)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pendaftaran_sidang', function (Blueprint $table) {
            $table->decimal('nb_laporan', 5, 2)->change();
            $table->decimal('nb_produk', 5, 2)->change();
            $table->decimal('nb_sikap', 5, 2)->change();

            $table->decimal('n1_laporan', 5, 2)->change();
            $table->decimal('n1_produk', 5, 2)->change();
            $table->decimal('n1_presentasi', 5, 2)->change();

            $table->decimal('n2_laporan', 5, 2)->change();
            $table->decimal('n2_produk', 5, 2)->change();
            $table->decimal('n2_presentasi', 5, 2)->change();

            $table->decimal('ns_motivasi', 5, 2)->change();
            $table->decimal('ns_kualitas', 5, 2)->change();
            $table->decimal('ns_inisiatif', 5, 2)->change();
            $table->decimal('ns_sikap', 5, 2)->change();

            $table->decimal('nilai_pembimbing', 5, 2)->change();
            $table->decimal('nilai_penguji_1', 5, 2)->change();
            $table->decimal('nilai_penguji_2', 5, 2)->change();
            $table->decimal('nilai_supervisor', 5, 2)->change();
            $table->decimal('nilai_akhir', 5, 2)->change();
        });
    }
};
