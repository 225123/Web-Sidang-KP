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
                    $mhs = $user->mahasiswa;
                    $available_periods = TahunAjaran::where(function($q) use ($user, $mhs) {
                        $q->where('id', optional($mhs)->tahun_ajaran_id)
                          ->orWhereHas('pendaftaranKps', function($q2) use ($user) {
                              $q2->withoutGlobalScope('periode')
                                 ->where(function ($q3) use ($user) {
                                     $q3->where('mahasiswa_id', $user->id)
                                        ->orWhereJsonContains('anggota_kelompok_ids', $user->id)
                                        ->orWhereJsonContains('anggota_kelompok_ids', (string) $user->id);
                                 });
                          });
                    })->terbaru()->get();
                    
                    if ($available_periods->isEmpty()) {
                        $available_periods = TahunAjaran::where('is_active', true)->terbaru()->get();
                    }
                } else {
                    $userCreatedAt = $user->created_at;
                    $available_periods = TahunAjaran::whereDate('tanggal_selesai', '>=', $userCreatedAt)
                        ->orWhere('is_active', true)
                        ->terbaru()
                        ->get();
                }

                // Fokus pada periode terbaru (paling atas di list)
                $selected_period_id = optional($available_periods->first())->id;
                
                if ($selected_period_id) {
                    session(['selected_periode_id' => $selected_period_id]);
                }
            }
        }

        return $next($request);
    }
}
