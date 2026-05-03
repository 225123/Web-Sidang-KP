<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\TahunAjaran;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class SetActivePeriode
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            $selected_period_id = session('selected_periode_id');
            
            if (!$selected_period_id) {
                $user = Auth::user();
                $roleStr = strtolower($user->role);
                $available_periods = collect();
                
                if ($roleStr === 'koordinator_kp' || str_contains($roleStr, 'koordinator') || $roleStr === 'dosen') {
                    $available_periods = TahunAjaran::terbaru()->get();
                } elseif ($roleStr === 'mahasiswa') {
                    // Hanya periode yang ada pendaftaran mahasiswanya
                    $available_periods = TahunAjaran::whereHas('pendaftaranKps', function($q) use ($user) {
                        $q->withoutGlobalScope('periode')->where('mahasiswa_id', $user->id);
                    })->terbaru()->get();
                } else {
                    $userCreatedAt = $user->created_at;
                    $available_periods = TahunAjaran::whereDate('tanggal_selesai', '>=', $userCreatedAt)
                        ->orWhere('is_active', true)
                        ->terbaru()
                        ->get();
                }

                // Fokus pada periode terbaru (paling atas di list)
                $selected_period_id = $available_periods->first()->id ?? null;
                
                if ($selected_period_id) {
                    session(['selected_periode_id' => $selected_period_id]);
                }
            }
        }

        return $next($request);
    }
}
