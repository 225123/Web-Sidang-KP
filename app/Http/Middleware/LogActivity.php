<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;

class LogActivity
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        $user = Auth::user();
        
        // If Auth::user() is null, try to get it from session explicitly
        if (!$user && session()->has('login_web_59ba36addc2b2f9401580f014c7f58ea4e30989d')) {
             // This is a hacky way to check Laravel's internal session key if needed, 
             // but usually Auth::user() should work if session is started.
        }

        $roleName = 'Guest';
        if ($user) {
            $roleName = $this->getRoleName($user->role);
        }
        
        // Determine module and action
        $module = $this->getModule($request);
        $action = $this->getAction($request);

        // Broaden logging: log everything except common system noise
        $excludedPaths = [
            'livewire/*',
            '*/assets/*',
            '*/notifikasi/count*', // Only exclude the count polling if it exists
        ];

        $shouldLog = true;
        foreach ($excludedPaths as $excluded) {
            if ($request->is($excluded)) {
                $shouldLog = false;
                break;
            }
        }

        if ($shouldLog && $module !== 'Audit Log') {
            AuditLog::create([
                'user_id' => $user?->id,
                'role' => $roleName,
                'module' => $module,
                'action' => $action,
                'url' => $request->fullUrl(),
                'method' => $request->method(),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'created_at' => now(), // Force Laravel's Asia/Jakarta time
            ]);
        }

        return $response;
    }

    private function getRoleName($role)
    {
        if (!$role) return 'Guest';

        $roleStr = strtolower((string)$role);

        // 1. Check String Roles (from Manajemen User)
        if ($roleStr === 'koordinator_kp' || str_contains($roleStr, 'koordinator')) return 'Koordinator KP';
        if ($roleStr === 'dosen') return 'Dosen';
        if ($roleStr === 'mahasiswa') return 'Mahasiswa';
        if ($roleStr === 'kaprodi') return 'Kaprodi';

        // 2. Check Numeric IDs (Fallback)
        return match((int)$role) {
            1 => 'Koordinator KP',
            2 => 'Dosen',
            3 => 'Mahasiswa',
            4 => 'Kaprodi',
            default => 'Guest'
        };
    }

    private function getModule(Request $request)
    {
        $path = $request->path();

        // 1. Dashboard
        if (str_contains($path, 'dashboard')) {
            return 'Dashboard';
        }

        // 2. Authentication
        if (str_contains($path, 'login') || str_contains($path, 'logout') || str_contains($path, 'password')) {
            return 'Autentikasi';
        }

        // 2. Profil & User Management
        if (str_contains($path, 'profil') || str_contains($path, 'manajemen-user') || str_contains($path, 'manajemen-akses')) {
            return 'User/Profile';
        }

        // 3. Pendaftaran KP
        if (str_contains($path, 'pendaftaran-kp') || str_contains($path, 'penugasan-pembimbing') || str_contains($path, 'daftar-mahasiswa')) {
            return 'Pendaftaran KP';
        }

        // 4. Bimbingan
        if (str_contains($path, 'bimbingan') || str_contains($path, 'log-bimbingan') || str_contains($path, 'progress-umum') || str_contains($path, 'persetujuan')) {
            return 'Bimbingan';
        }

        // 5. Sidang & Berkas
        if (str_contains($path, 'sidang') || str_contains($path, 'verifikasi-berkas') || str_contains($path, 'penjadwalan') || str_contains($path, 'dosen-penguji') || str_contains($path, 'berkas')) {
            return 'Sidang';
        }

        // 6. Penilaian & Finalisasi
        if (str_contains($path, 'nilai') || str_contains($path, 'finalisasi') || str_contains($path, 'berita-acara') || str_contains($path, 'grading')) {
            return 'Penilaian';
        }

        // 7. Sistem & Utilities
        if (str_contains($path, 'timeline')) return 'Manajemen Timeline';
        if (str_contains($path, 'pengumuman')) return 'Pengumuman';
        if (str_contains($path, 'audit-log')) return 'Audit Log';
        if (str_contains($path, 'periode')) return 'Manajemen Periode';

        return 'Sistem';
    }

    private function getAction(Request $request)
    {
        $method = $request->method();
        $path = $request->path();

        // Detection based on Path/Route Keywords
        if (str_contains($path, 'login')) return 'LOGIN_USER';
        if (str_contains($path, 'logout')) return 'LOGOUT_USER';
        if (str_contains($path, 'download') || str_contains($path, 'export')) return 'DOWNLOAD_DOKUMEN';
        
        // CRUD Operations
        if ($method === 'POST') {
            if (str_contains($path, 'verifikasi') || str_contains($path, 'approve') || str_contains($path, 'sahkan')) return 'APPROVE/VERIFIKASI';
            if (str_contains($path, 'tolak') || str_contains($path, 'reject')) return 'REJECT/TOLAK';
            return 'SIMPAN_DATA_BARU';
        }
        if ($method === 'PUT' || $method === 'PATCH') return 'UPDATE_DATA';
        if ($method === 'DELETE') return 'HAPUS_DATA';
        
        // View Actions
        return 'VIEW_' . strtoupper(str_replace(['/', '-'], '_', basename($path)));
    }
}
