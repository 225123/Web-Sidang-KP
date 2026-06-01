<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

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

        \Illuminate\Support\Facades\View::composer('*', function ($view) {
            $isReadOnly = false;
            if (auth()->check()) {
                $latestPeriode = \App\Models\TahunAjaran::terbaru()->first();
                $isReadOnly = (session('selected_periode_id') && $latestPeriode && session('selected_periode_id') != $latestPeriode->id);
            }
            $view->with('isReadOnly', $isReadOnly);
        });
    }
}
