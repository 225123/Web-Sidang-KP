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
            $table->string('token_penilaian_supervisor')->nullable();
            $table->boolean('is_penilaian_supervisor_submitted')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pendaftaran_sidang', function (Blueprint $table) {
            $table->dropColumn(['token_penilaian_supervisor', 'is_penilaian_supervisor_submitted']);
        });
    }
};
