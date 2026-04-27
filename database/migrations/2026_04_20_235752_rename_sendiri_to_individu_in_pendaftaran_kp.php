<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Update existing 'sendiri' records to 'individu'
        DB::table('pendaftaran_kp')
            ->where('pengerjaan_kp', 'sendiri')
            ->update(['pengerjaan_kp' => 'individu']);

        // 2. Change the default value of the column
        Schema::table('pendaftaran_kp', function (Blueprint $table) {
            $table->string('pengerjaan_kp')->default('individu')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // 1. Revert existing 'individu' records back to 'sendiri'
        DB::table('pendaftaran_kp')
            ->where('pengerjaan_kp', 'individu')
            ->update(['pengerjaan_kp' => 'sendiri']);

        // 2. Revert the default value
        Schema::table('pendaftaran_kp', function (Blueprint $table) {
            $table->string('pengerjaan_kp')->default('sendiri')->change();
        });
    }
};
