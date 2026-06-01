<?php

namespace App\Providers;

use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use App\Models\TahunAjaran;
use Illuminate\Support\Facades\Auth;

class ViewComposerServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        View::composer('components.dashboard-layout', function ($view) {
            $selected_period_id = session('selected_periode_id');
            $available_periods = collect();

            if (Auth::check()) {
                $user = Auth::user();
                $roleStr = strtolower($user->role);
                
                if ($roleStr === 'koordinator_kp' || str_contains($roleStr, 'koordinator') || $roleStr === 'dosen') {
                    $available_periods = TahunAjaran::with('koordinator')->terbaru()->get();
                } elseif ($roleStr === 'mahasiswa') {
                    $mhs = $user->mahasiswa;
                    $available_periods = TahunAjaran::with('koordinator')->where(function($q) use ($user, $mhs) {
                        $q->where('id', optional($mhs)->tahun_ajaran_id)
                          ->orWhereHas('pendaftaranKps', function($q2) use ($user) {
                              $q2->withoutGlobalScope('periode')
                                 ->whereNotNull('status_kp')
                                 ->where(function ($q3) use ($user) {
                                     $q3->where('mahasiswa_id', $user->id)
                                        ->orWhereJsonContains('anggota_kelompok_ids', $user->id)
                                        ->orWhereJsonContains('anggota_kelompok_ids', (string) $user->id);
                                 });
                          });
                    })->terbaru()->get();
                    
                    if ($available_periods->isEmpty()) {
                        $available_periods = TahunAjaran::with('koordinator')->where('is_active', true)->terbaru()->get();
                    }
                } else {
                    $userCreatedAt = $user->created_at;
                    $available_periods = TahunAjaran::with('koordinator')->whereDate('tanggal_selesai', '>=', $userCreatedAt)
                        ->orWhere('is_active', true)
                        ->terbaru()
                        ->get();
                }
            }
            
            $selected_period = $available_periods->where('id', $selected_period_id)->first();
            $selected_period_label = $selected_period ? $selected_period->label_tahun_ajaran : 'Pilih Periode';

            // Cek apakah periode yang dipilih adalah periode absolut terbaru di database
            $latest_all = TahunAjaran::terbaru()->first();
            $is_locked = $selected_period_id && $latest_all && ($selected_period_id != $latest_all->id);

            if ($is_locked && request()->route()) {
                $excludedRoutes = [
                    'koordinator.periode-kp.*',
                    'koordinator.pengumuman.*',
                    'koordinator.audit-log.*',
                    'koordinator.backup.*',
                ];
                foreach ($excludedRoutes as $excluded) {
                    if (request()->routeIs($excluded)) {
                        $is_locked = false;
                        break;
                    }
                }
            }

            View::share('is_locked', $is_locked);

            $view->with(compact('available_periods', 'selected_period_id', 'selected_period_label', 'is_locked'));
        });
    }
}
