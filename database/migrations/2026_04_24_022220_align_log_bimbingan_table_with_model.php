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
            $table->renameColumn('tanggal_bimbingan', 'tanggal');
            $table->renameColumn('materi_bimbingan', 'materi_bahasan');
            $table->renameColumn('file_lampiran', 'file_progress');
            $table->renameColumn('status_bimbingan', 'status_approval');
            $table->boolean('is_supervisor')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('log_bimbingan', function (Blueprint $table) {
            $table->renameColumn('tanggal', 'tanggal_bimbingan');
            $table->renameColumn('materi_bahasan', 'materi_bimbingan');
            $table->renameColumn('file_progress', 'file_lampiran');
            $table->renameColumn('status_approval', 'status_bimbingan');
            $table->dropColumn('is_supervisor');
        });
    }
};
