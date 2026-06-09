<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if (config('app.env') === 'production') {
            \Illuminate\Support\Facades\URL::forceScheme('https');
        }
        \Illuminate\Pagination\Paginator::useTailwind();

        Password::defaults(function () {
            return Password::min(8)
                ->letters()
                ->mixedCase()
                ->numbers()
                ->symbols();
        });

        \Illuminate\Support\Facades\View::composer('*', function ($view) {
            $isReadOnly = false;
            if (auth()->check()) {
                $latestPeriode = \App\Models\TahunAjaran::terbaru()->first();
                $isReadOnly = (session('selected_periode_id') && $latestPeriode && session('selected_periode_id') != $latestPeriode->id);
                
                // Tambahan: Jika status user tidak aktif, jadikan mode pelihat
                $user = auth()->user();
                if (!$isReadOnly) {
                    if ($user->role === 'mahasiswa' && $user->mahasiswa && !$user->mahasiswa->is_aktif) {
                        $isReadOnly = true;
                    } elseif (in_array($user->role, ['dosen', 'koordinator_kp']) && $user->dosen && !$user->dosen->is_aktif) {
                        $isReadOnly = true;
                    }
                }

                if ($isReadOnly && request()->route()) {
                    $excludedRoutes = [
                        'koordinator.periode-kp.*',
                        'koordinator.pengumuman.*',
                        'koordinator.audit-log.*',
                        'koordinator.backup.*',
                        'koordinator.manajemen-akses.*',
                        'profil.*',
                        '*.notifikasi*',
                        '*.panduan*',
                    ];
                    foreach ($excludedRoutes as $excluded) {
                        if (request()->routeIs($excluded) || request()->is('*profil*') || request()->is('*notifikasi*') || request()->is('*panduan*')) {
                            $isReadOnly = false;
                            break;
                        }
                    }
                }
            }
            $view->with('isReadOnly', $isReadOnly);
        });
    }
}
