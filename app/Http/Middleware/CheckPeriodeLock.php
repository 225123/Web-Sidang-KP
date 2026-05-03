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

                // Jika periode terpilih bukanlah periode terbaru, maka kunci akses modifikasi
                if ($selected_period_id && $latest_all && $selected_period_id != $latest_all->id) {
                    if ($request->ajax() || $request->wantsJson()) {
                        return response()->json(['message' => 'Aksi ditolak: Periode ini sudah dikunci karena bukan periode terbaru.'], 403);
                    }
                    
                    return back()->with('error', 'Aksi ditolak: Periode ini sudah dikunci karena bukan periode terbaru. Anda hanya dapat melihat data.');
                }
            }
        }

        return $next($request);
    }
}
