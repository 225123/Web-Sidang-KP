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
             // Status Pelaksanaan (Decided by Penguji 1)
             $table->enum('pelaksanaan', ['Menunggu', 'Berjalan', 'Selesai', 'Dibatalkan'])->default('Menunggu');
 
             // Pembimbing Components (Image 1: 40/40/20)
             $table->decimal('nb_laporan', 5, 2)->nullable();
             $table->decimal('nb_produk', 5, 2)->nullable();
             $table->decimal('nb_sikap', 5, 2)->nullable();
 
             // Penguji 1 Components (Image 2: 40/40/20)
             $table->decimal('n1_laporan', 5, 2)->nullable();
             $table->decimal('n1_produk', 5, 2)->nullable();
             $table->decimal('n1_presentasi', 5, 2)->nullable();
 
             // Penguji 2 Components (Image 2: 40/40/20)
             $table->decimal('n2_laporan', 5, 2)->nullable();
             $table->decimal('n2_produk', 5, 2)->nullable();
             $table->decimal('n2_presentasi', 5, 2)->nullable();
 
             // Supervisior Components (Image 3: 25/25/25/25)
             $table->decimal('ns_motivasi', 5, 2)->nullable();
             $table->decimal('ns_kualitas', 5, 2)->nullable();
             $table->decimal('ns_inisiatif', 5, 2)->nullable();
             $table->decimal('ns_sikap', 5, 2)->nullable();
             $table->decimal('nilai_supervisor', 5, 2)->nullable();
         });
     }
 
     /**
      * Reverse the migrations.
      */
     public function down(): void
     {
         Schema::table('pendaftaran_sidang', function (Blueprint $table) {
             $table->dropColumn([
                 'pelaksanaan',
                 'nb_laporan', 'nb_produk', 'nb_sikap',
                 'n1_laporan', 'n1_produk', 'n1_presentasi',
                 'n2_laporan', 'n2_produk', 'n2_presentasi',
                 'ns_motivasi', 'ns_kualitas', 'ns_inisiatif', 'ns_sikap',
             ]);
         });
     }
 };
 
