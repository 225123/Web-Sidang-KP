<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\TahunAjaran;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckPeriodeLock
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            $method = $request->method();
            
            // Hanya periksa request yang memodifikasi data (POST, PUT, PATCH, DELETE)
            if (in_array($method, ['POST', 'PUT', 'PATCH', 'DELETE'])) {
                // Kecualikan rute tertentu yang boleh diakses kapan saja
                $excludedRoutes = [
                    'logout',
                    'set-periode',
                    'profil.*',
                    'profile.*',
                    'backup.*', // Backup mungkin masih boleh didownload
                    'koordinator.periode-kp.*',
                    'koordinator.pengumuman.*',
                    'koordinator.audit-log.*',
                    'koordinator.backup.*',
                ];

                if ($request->route()) {
                    foreach ($excludedRoutes as $route) {
                        if ($request->routeIs($route)) {
                            return $next($request);
                        }
                    }
                }

                $selected_period_id = session('selected_periode_id');
                $latest_all = TahunAjaran::terbaru()->first();
                $user = Auth::user();

                $isPeriodeLocked = ($selected_period_id && $latest_all && $selected_period_id != $latest_all->id);
                
                $isUserInactive = false;
                if ($user->hasRole('mahasiswa') && $user->mahasiswa && !$user->mahasiswa->is_aktif) {
                    $isUserInactive = true;
                } elseif (($user->hasRole('dosen') || $user->hasRole('koordinator')) && $user->dosen && !$user->dosen->is_aktif) {
                    $isUserInactive = true;
                }

                if ($isPeriodeLocked || $isUserInactive) {
                    $reason = $isUserInactive ? 'akun Anda saat ini bersatus tidak aktif' : 'bukan periode terbaru';
                    
                    if ($request->ajax() || $request->wantsJson()) {
                        return response()->json(['message' => "Aksi ditolak: Anda hanya dapat melihat data karena $reason."], 403);
                    }
                    
                    return back()->with('error', "Aksi ditolak: Anda hanya dapat melihat data karena $reason.");
                }
            }
        }

        return $next($request);
    }
}
