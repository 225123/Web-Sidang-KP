<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureTtdExists
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        // If user is authenticated
        if ($user) {
            // Check if they are trying to access a profile route or logout route
            if ($request->is('profil*') || $request->is('logout')) {
                return $next($request);
            }

            // Exclude API or other routes that might not need TTD
            // But we specifically want to guard Dashboard logic

            // If they don't have a signature path, force them to profile
            if (! $user->signature_path) {
                return redirect()->route('profil.index')
                    ->with('error', 'Anda harus membuat atau mengunggah tanda tangan digital (TTD) terlebih dahulu!');
            }
        }

        return $next($request);
    }
}
