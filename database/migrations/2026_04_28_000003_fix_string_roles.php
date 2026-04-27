<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Fix roles based on string values in users table
        $users = DB::table('users')->get();
        foreach ($users as $user) {
            $roleStr = strtolower((string)$user->role);
            $roleName = 'Guest';
            
            if ($roleStr === 'koordinator_kp' || str_contains($roleStr, 'koordinator')) $roleName = 'Koordinator KP';
            elseif ($roleStr === 'dosen') $roleName = 'Dosen';
            elseif ($roleStr === 'mahasiswa') $roleName = 'Mahasiswa';
            elseif ($roleStr === 'kaprodi') $roleName = 'Kaprodi';
            else {
                $roleName = match((int)$user->role) {
                    1 => 'Koordinator KP',
                    2 => 'Dosen',
                    3 => 'Mahasiswa',
                    4 => 'Kaprodi',
                    default => 'Guest'
                };
            }
            
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
