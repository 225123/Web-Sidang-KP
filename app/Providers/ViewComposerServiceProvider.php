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
            }
            
            $selected_period_label = $available_periods->where('id', $selected_period_id)->first()->label_tahun_ajaran ?? 'Pilih Periode';

            $view->with(compact('available_periods', 'selected_period_id', 'selected_period_label'));
        });
    }
}
