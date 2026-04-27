<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Fix 'Guest' for users who have a role ID in the users table
        $users = DB::table('users')->get();
        foreach ($users as $user) {
            $roleName = match((int)$user->role) {
                1 => 'Koordinator KP',
                2 => 'Dosen',
                3 => 'Mahasiswa',
                4 => 'Kaprodi',
                default => 'Guest'
            };
            
            if ($roleName !== 'Guest') {
                DB::table('audit_logs')
                    ->where('user_id', $user->id)
                    ->where('role', 'Guest')
                    ->update(['role' => $roleName]);
            }
        }
    }

    public function down(): void
    {
    }
};
