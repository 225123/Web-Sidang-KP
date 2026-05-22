<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureMahasiswaAktif
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Jika belum login atau bukan mahasiswa, lewati
        if (!auth()->check() || auth()->user()->role !== 'mahasiswa') {
            return $next($request);
        }

        $mahasiswa = auth()->user()->mahasiswa;
        
        // Jika status mahasiswa tidak aktif, blokir request yang mengubah data
        if ($mahasiswa && !$mahasiswa->is_aktif) {
            if (in_array($request->method(), ['POST', 'PUT', 'PATCH', 'DELETE'])) {
                
                // Ijinkan logout
                if ($request->routeIs('logout')) {
                    return $next($request);
                }

                // Cek apakah request ajax
                if ($request->ajax()) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Akses ditolak. Status Anda saat ini tidak aktif (Mode Read-Only).'
                    ], 403);
                }

                return redirect()->back()->with('error', 'Akses ditolak. Status Anda di periode ini adalah Tidak Aktif (Hanya Pelihat).');
            }
        }

        return $next($request);
    }
}
