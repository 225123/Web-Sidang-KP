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
        Schema::create('backup_histories', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('koordinator_id')->nullable();
            $table->unsignedBigInteger('tahun_ajaran_id')->nullable();
            $table->string('file_name');
            $table->timestamps();

            // Opsional: Foreign Key (set null on delete agar riwayat tidak hilang jika user/periode dihapus)
            $table->foreign('koordinator_id')->references('id')->on('users')->onDelete('set null');
            $table->foreign('tahun_ajaran_id')->references('id')->on('tahun_ajaran')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('backup_histories');
    }
};
