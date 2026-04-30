<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>{{ config('app.name', 'Laravel') }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Bree+Serif&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])



    <style>
        .font-bree { font-family: 'Bree Serif', serif; }
        .font-inter { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body x-data="{ sidebarOpen: $persist(true), footerVisible: false }" @toggle-footer.window="footerVisible = $event.detail" class="font-inter antialiased bg-[#F5F6F8] text-gray-900 flex flex-col h-screen overflow-hidden">
    
    <header class="bg-[#D9D9D9] flex-shrink-0 relative top-0 z-50 shadow-[0px_4px_4px_rgba(0,0,0,0.25)] border-b border-gray-300 h-[76px]">
        <div class="flex items-center justify-between px-4 md:px-9 h-full">
            <div class="flex items-center gap-4 md:gap-6 shrink-0">
                <a href="{{ route('profil.index') }}" class="p-2 bg-gray-300 hover:bg-gray-400 rounded-lg transition-colors focus:outline-none z-50 relative flex items-center justify-center">
                    <svg class="w-5 h-5 md:w-6 md:h-6 text-black" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                </a>
                @php
                    $dashboardRoute = '#';
                    if (auth()->check()) {
                        $role = auth()->user()->role;
                        if ($role === 'koordinator_kp') $dashboardRoute = route('koordinator.dashboard');
                        elseif ($role === 'dosen') $dashboardRoute = route('dosen.dashboard');
                        elseif ($role === 'mahasiswa') $dashboardRoute = route('mahasiswa.dashboard');
                    }
                @endphp
                <a href="{{ $dashboardRoute }}" class="hidden md:block hover:opacity-70 transition-opacity">
                    <h1 class="text-[16px] lg:text-[20px] font-bold uppercase leading-tight text-black font-serif tracking-widest mt-2">
                        KERJA<br/>PRAKTEK
                    </h1>
                </a>
            </div>
            
            <div class="flex items-center gap-4 md:gap-6 mt-1">
                @php
                    $unreadCount = 0;
                    if (auth()->check()) {
                        $user = auth()->user();
                        $roleMap = [
                            'koordinator_kp' => 'koordinator',
                            'dosen' => 'dosen',
                            'mahasiswa' => 'mahasiswa'
                        ];
                        $roleToken = $roleMap[$user->role] ?? null;

                        $unreadCount = \App\Models\NotifikasiLog::where('is_read', false)
                            ->where(function($q) use ($user, $roleToken) {
                                $q->where('receiver_id', $user->id);
                                if ($roleToken) {
                                    $q->orWhere('target_role', $roleToken)
                                      ->orWhere('target_role', 'semua');
                                }
                            })
                            ->count();
                    }
                    
                    $notifRoute = '#';
                    if (auth()->check()) {
                        $role = auth()->user()->role;
                        if ($role === 'koordinator_kp') $notifRoute = route('koordinator.notifikasi');
                        elseif ($role === 'dosen') $notifRoute = route('dosen.notifikasi');
                        elseif ($role === 'mahasiswa') $notifRoute = route('mahasiswa.notifikasi');
                    }
                @endphp
                <a href="{{ $notifRoute }}" class="relative p-2 md:p-2.5 bg-[#9F9F9F] rounded-full hover:bg-gray-500 transition-colors flex items-center justify-center group">
                    <svg class="w-5 h-5 md:w-6 md:h-6 text-black group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                    </svg>
                    @if($unreadCount > 0)
                        <span class="absolute -top-1 -right-1 bg-[#FF0000] text-white text-[9px] md:text-[11px] font-bold rounded-full h-[15px] w-[15px] md:h-[18px] md:w-[18px] flex items-center justify-center shadow-[0_2px_4px_rgba(0,0,0,0.3)] ring-2 ring-[#D9D9D9]">
                            {{ $unreadCount > 99 ? '99+' : $unreadCount }}
                        </span>
                    @endif
                </a>

                <div class="flex items-center gap-3 md:gap-4">
                    <div class="text-right hidden md:flex flex-col justify-center">
                        <span class="text-[14px] lg:text-[17px] font-bree uppercase text-black font-normal">{{ $roleName ?? 'ROLE' }}</span>
                        <span class="text-[14px] lg:text-[17px] font-bree text-black font-normal">
                            @php
                                $dUser = auth()->user();
                                $dId = '';
                                if ($dUser) {
                                    if ($dUser->role === 'mahasiswa') {
                                        $dId = optional($dUser->mahasiswa)->nim;
                                    } elseif (in_array($dUser->role, ['dosen', 'koordinator_kp'])) {
                                        $dId = optional($dUser->dosen)->nidn;
                                    }
                                }
                            @endphp
                            {{ $userName ?? 'User' }}{{ $dId ? ' - ' . $dId : '' }}
                        </span>
                    </div>
                    <a href="{{ route('profil.index') }}" class="h-[40px] w-[40px] md:h-[58px] md:w-[58px] lg:h-[68px] lg:w-[68px] rounded-full bg-[#140EBF] flex items-center justify-center text-white font-bold text-lg md:text-xl lg:text-2xl border-[2px] md:border-[3px] lg:border-4 border-[#D9D9D9] overflow-hidden shadow-sm hover:scale-105 transition-transform">
                        @if(auth()->user() && auth()->user()->avatar)
                            <img src="{{ asset('storage/' . auth()->user()->avatar) }}" alt="Avatar" class="w-full h-full object-cover">
                        @else
                            {{ substr($userName ?? 'R', 0, 1) }}
                        @endif
                    </a>
                </div>
            </div>
        </div>
    </header>

    <div class="flex flex-1 overflow-hidden relative z-0 w-full">
        <!-- Mobile Sidebar Backdrop -->
        <div x-show="sidebarOpen" x-transition.opacity style="display: none;" class="fixed inset-0 bg-black/50 z-30 md:hidden" @click="sidebarOpen = false"></div>

        <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full md:translate-x-0 md:w-[88px]'" class="w-[221px] absolute md:relative z-40 md:z-10 bg-[#D9D9D9] flex-shrink-0 overflow-y-auto overflow-x-hidden h-full pb-10 transition-all duration-300 ease-in-out group sidebar-scroll shadow-xl md:shadow-none">
            <div class="py-6 w-full flex flex-col items-stretch px-2 space-y-1">
                {{ $sidebar }}
            </div>
        </aside>

        <main id="main-scroll-area" class="flex-1 overflow-y-auto bg-[#F5F6F8] transition-all duration-300 flex flex-col custom-scrollbar relative z-0 w-full">
            <div class="p-4 md:p-8 pb-10 flex-1 w-full max-w-[100vw] overflow-x-hidden">
                @if(isset($header))
                    <div class="mb-8 flex flex-col md:flex-row md:justify-between md:items-center gap-4 w-full">
                        <h2 class="text-2xl font-bold font-inter text-black">{{ $header }}</h2>
                        @if(isset($headerActions))
                            <div>{{ $headerActions }}</div>
                        @endif
                    </div>
                @endif
                
                <div class="mt-4 w-full relative z-0">
                    {{ $slot }}
                </div>
            </div>

            <!-- Sentinel element for tracking bottom of main content -->
            <div id="footer-sentinel" class="h-1 w-full mt-auto bg-transparent"></div>
        </main>
    </div>

    <!-- Fixed Footer that only appears when scrolled to bottom -->
    <footer :class="footerVisible ? 'translate-y-0' : 'translate-y-full'" class="fixed bottom-0 left-0 w-full bg-[#040404] text-white text-[11px] font-medium font-inter text-center h-[40px] flex items-center justify-center gap-2 z-[60] transition-transform duration-300 shadow-[0px_-4px_10px_rgba(0,0,0,0.15)]">
        <span>2026 Sidang KP | Universitas Kristen Krida Wacana</span>
        <div class="w-[9px] h-[9px] rounded-full bg-black border border-white flex items-center justify-center text-[9px] italic ml-1">
            <span class="leading-none transform -translate-y-px">c</span>
        </div>
    </footer>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            let observer = null;
            const setupObserver = () => {
                const sentinel = document.getElementById('footer-sentinel');
                const mainArea = document.getElementById('main-scroll-area');
                
                if(observer) {
                    observer.disconnect();
                }
                
                if(sentinel && mainArea) {
                    observer = new IntersectionObserver((entries) => {
                        entries.forEach(entry => {
                            window.dispatchEvent(new CustomEvent('toggle-footer', { detail: entry.isIntersecting }));
                        });
                    }, { root: mainArea, threshold: 0 });
                    observer.observe(sentinel);
                }
            };
            setupObserver();
            document.addEventListener('turbo:render', setupObserver);
        });
    </script>
</body>
</html>