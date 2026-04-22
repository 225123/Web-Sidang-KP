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
            $table->decimal('nilai_pembimbing', 5, 2)->nullable();
            $table->decimal('nilai_penguji_1', 5, 2)->nullable();
            $table->decimal('nilai_penguji_2', 5, 2)->nullable();
            $table->decimal('nilai_akhir', 5, 2)->nullable();
            $table->string('grade', 5)->nullable();
            $table->text('catatan_sidang')->nullable();
            $table->enum('status_kelulusan', ['pending', 'lulus', 'tidak_lulus'])->default('pending');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pendaftaran_sidang', function (Blueprint $table) {
            $table->dropColumn([
                'nilai_pembimbing',
                'nilai_penguji_1',
                'nilai_penguji_2',
                'nilai_akhir',
                'grade',
                'catatan_sidang',
                'status_kelulusan'
            ]);
        });
    }
};
