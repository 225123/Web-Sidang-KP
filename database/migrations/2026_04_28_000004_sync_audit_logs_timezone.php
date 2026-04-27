<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Add 7 hours to existing audit logs to convert UTC to WIB
        DB::statement("UPDATE audit_logs SET created_at = datetime(created_at, '+7 hours')");
    }

    public function down(): void
    {
        // Subtract 7 hours to revert to UTC
        DB::statement("UPDATE audit_logs SET created_at = datetime(created_at, '-7 hours')");
    }
};
