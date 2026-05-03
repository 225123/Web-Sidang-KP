<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Mark all existing users that already have passwords as email-verified,
     * so they are not locked out after enabling MustVerifyEmail.
     */
    public function up(): void
    {
        // Users with a password set (i.e., pre-existing accounts) are treated as verified
        DB::table('users')
            ->whereNotNull('password')
            ->whereNull('email_verified_at')
            ->update(['email_verified_at' => now()]);
    }

    public function down(): void
    {
        // Non-reversible data migration
    }
};
