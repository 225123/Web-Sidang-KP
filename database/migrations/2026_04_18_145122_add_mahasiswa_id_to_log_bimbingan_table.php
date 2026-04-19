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
        Schema::table('log_bimbingan', function (Blueprint $table) {
            $table->unsignedBigInteger('mahasiswa_id')->nullable();
            
            // To ensure compatibility, we leave it nullable.
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('log_bimbingan', function (Blueprint $table) {
            $table->dropColumn('mahasiswa_id');
        });
    }
};
