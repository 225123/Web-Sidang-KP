<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Storage;
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

        try {
            Storage::extend('google', function($app, $config) {
                $options = [];
                if (!empty($config['teamDriveId'] ?? null)) {
                    $options['teamDriveId'] = $config['teamDriveId'];
                }
                if (!empty($config['sharedFolderId'] ?? null)) {
                    $options['sharedFolderId'] = $config['sharedFolderId'];
                }
                $client = new \Google\Client();
                $client->setClientId($config['clientId']);
                $client->setClientSecret($config['clientSecret']);
                $client->refreshToken($config['refreshToken']);
                $service = new \Google\Service\Drive($client);
                $adapter = new \Masbug\Flysystem\GoogleDriveAdapter($service, $config['folder'] ?? '/', $options);
                $driver = new \League\Flysystem\Filesystem($adapter);
                return new \Illuminate\Filesystem\FilesystemAdapter($driver, $adapter);
            });
        } catch(\Exception $e) {
            // Log or ignore
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
