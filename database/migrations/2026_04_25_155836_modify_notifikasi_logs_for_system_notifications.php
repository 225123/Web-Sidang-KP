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
        Schema::table('notifikasi_logs', function (Blueprint $table) {
            $table->foreignId('sender_id')->nullable()->change();
            $table->string('target_url')->nullable()->after('pesan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('notifikasi_logs', function (Blueprint $table) {
            $table->foreignId('sender_id')->nullable(false)->change();
            $table->dropColumn('target_url');
        });
    }
};
