<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Normalize Roles
        DB::table('audit_logs')->where('role', 'Koordinator')->update(['role' => 'Koordinator KP']);
        
        // Normalize Modules
        DB::table('audit_logs')->where('module', 'Authentication')->update(['module' => 'Autentikasi']);
        DB::table('audit_logs')->where('module', 'System')->update(['module' => 'Sistem']);
        DB::table('audit_logs')->where('module', 'Koordinator')->update(['module' => 'Pendaftaran KP']);
        DB::table('audit_logs')->where('module', 'Dosen')->update(['module' => 'Penilaian']);
        DB::table('audit_logs')->where('module', 'Mahasiswa')->update(['module' => 'Bimbingan']);
    }

    public function down(): void
    {
        // No need to reverse
    }
};
