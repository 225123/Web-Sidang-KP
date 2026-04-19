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
            $table->string('file_log_bimbingan')->nullable()->after('file_laporan');
            $table->string('file_berkas_lainnya')->nullable()->after('file_nilai_supervisor');
            $table->string('link_drive')->nullable()->after('link_github');
            $table->string('link_deploy')->nullable()->after('link_drive');
            $table->enum('status_koordinator', ['unsubmitted', 'pending', 'verified', 'rejected'])->default('unsubmitted')->after('status_verifikasi');
            $table->text('koordinator_feedback')->nullable()->after('status_koordinator');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pendaftaran_sidang', function (Blueprint $table) {
            $table->dropColumn([
                'file_log_bimbingan', 
                'file_berkas_lainnya', 
                'link_drive', 
                'link_deploy', 
                'status_koordinator', 
                'koordinator_feedback'
            ]);
        });
    }
};
