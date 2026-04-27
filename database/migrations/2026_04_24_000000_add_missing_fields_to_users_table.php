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
        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'role')) {
                $table->string('role')->nullable()->after('password');
            }
            if (! Schema::hasColumn('users', 'avatar')) {
                $table->string('avatar')->nullable()->after('role');
            }
            if (! Schema::hasColumn('users', 'signature_path')) {
                $table->string('signature_path')->nullable()->after('avatar');
            }
            if (! Schema::hasColumn('users', 'reset_token')) {
                $table->string('reset_token')->nullable()->after('signature_path');
            }
            if (! Schema::hasColumn('users', 'reset_token_expires')) {
                $table->timestamp('reset_token_expires')->nullable()->after('reset_token');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['role', 'avatar', 'signature_path', 'reset_token', 'reset_token_expires']);
        });
    }
};
