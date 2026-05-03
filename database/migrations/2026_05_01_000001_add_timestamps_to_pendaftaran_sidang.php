<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pendaftaran_sidang', function (Blueprint $table) {
            if (!Schema::hasColumn('pendaftaran_sidang', 'created_at')) {
                $table->timestamps();
            }
        });
    }

    public function down(): void
    {
        Schema::table('pendaftaran_sidang', function (Blueprint $table) {
            $table->dropTimestamps();
        });
    }
};
