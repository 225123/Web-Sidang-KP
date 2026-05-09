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
                    // Periode yang ada pendaftaran KP-nya ATAU periode yang sedang aktif
                    $available_periods = TahunAjaran::with('koordinator')->where(function($q) use ($user) {
                        $q->whereHas('pendaftaranKps', function($q2) use ($user) {
                            $q2->withoutGlobalScope('periode')->where('mahasiswa_id', $user->id);
                        })->orWhere('is_active', true);
                    })->terbaru()->get();
                } else {
                    $userCreatedAt = $user->created_at;
                    $available_periods = TahunAjaran::with('koordinator')->whereDate('tanggal_selesai', '>=', $userCreatedAt)
                        ->orWhere('is_active', true)
                        ->terbaru()
                        ->get();
                }
            }
            
            $selected_period = $available_periods->where('id', $selected_period_id)->first();
            $koordinatorName = $selected_period && $selected_period->koordinator ? ' - ' . $selected_period->koordinator->name : '';
            $selected_period_label = $selected_period ? ($selected_period->label_tahun_ajaran . $koordinatorName) : 'Pilih Periode';

            // Cek apakah periode yang dipilih adalah periode absolut terbaru di database
            $latest_all = TahunAjaran::terbaru()->first();
            $is_locked = $selected_period_id && $latest_all && ($selected_period_id != $latest_all->id);

            View::share('is_locked', $is_locked);

            $view->with(compact('available_periods', 'selected_period_id', 'selected_period_label', 'is_locked'));
        });
    }
}
