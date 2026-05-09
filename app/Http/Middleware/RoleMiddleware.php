<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        if (!auth()->check()) {
            return redirect('login');
        }

        $userRole = strtolower(auth()->user()->role);
        
        // Let koordinator_kp also pass if 'koordinator' is required
        if ($role === 'koordinator' && ($userRole === 'koordinator_kp' || str_contains($userRole, 'koordinator'))) {
            return $next($request);
        }

        // Let koordinator pass if 'dosen' is required (since Koordinator is also a Dosen)
        if ($role === 'dosen' && ($userRole === 'koordinator_kp' || str_contains($userRole, 'koordinator'))) {
            return $next($request);
        }

        if ($userRole !== strtolower($role)) {
            // Redirect to their own dashboard
            if ($userRole === 'koordinator_kp' || str_contains($userRole, 'koordinator')) {
                return redirect()->route('koordinator.dashboard');
            } elseif ($userRole === 'dosen') {
                return redirect()->route('dosen.dashboard');
            } elseif ($userRole === 'mahasiswa') {
                return redirect()->route('mahasiswa.dashboard');
            }
            return redirect('/');
        }

        return $next($request);
    }
}
