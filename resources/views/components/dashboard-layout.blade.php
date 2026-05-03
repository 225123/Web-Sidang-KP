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
                <button @click="sidebarOpen = !sidebarOpen" type="button" class="p-1.5 md:p-2 bg-gray-300 hover:bg-gray-400 rounded-lg transition-colors focus:outline-none z-50 relative flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5 md:w-6 md:h-6 text-black" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                </button>
                @php
                    $dashboardRoute = '#';
                    if (auth()->check()) {
                        $roleStr = strtolower(auth()->user()->role);
                        if ($roleStr === 'koordinator_kp' || str_contains($roleStr, 'koordinator')) $dashboardRoute = route('koordinator.dashboard');
                        elseif ($roleStr === 'dosen') $dashboardRoute = route('dosen.dashboard');
                        elseif ($roleStr === 'mahasiswa') $dashboardRoute = route('mahasiswa.dashboard');
                    }
                @endphp
                <a href="{{ $dashboardRoute }}" class="block hover:opacity-70 transition-opacity shrink-0">
                    <h1 class="text-[11px] sm:text-[14px] md:text-[16px] lg:text-[20px] font-bold uppercase leading-tight text-black font-serif tracking-widest mt-1 md:mt-2">
                        KERJA<br/>PRAKTEK
                    </h1>
                </a>
            </div>
            
            <div class="flex items-center gap-4 md:gap-6 mt-1">
                @php
                    $unreadCount = 0;
                    if (auth()->check()) {
                        $user = auth()->user();
                        $roleStr = strtolower($user->role);
                        $roleToken = null;
                        
                        if ($roleStr === 'koordinator_kp' || str_contains($roleStr, 'koordinator')) $roleToken = 'koordinator';
                        elseif ($roleStr === 'dosen') $roleToken = 'dosen';
                        elseif ($roleStr === 'mahasiswa') $roleToken = 'mahasiswa';

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
                        $roleStr = strtolower(auth()->user()->role);
                        if ($roleStr === 'koordinator_kp' || str_contains($roleStr, 'koordinator')) $notifRoute = route('koordinator.notifikasi');
                        elseif ($roleStr === 'dosen') $notifRoute = route('dosen.notifikasi');
                        elseif ($roleStr === 'mahasiswa') $notifRoute = route('mahasiswa.notifikasi');
                    }
                @endphp
                <a href="{{ $notifRoute }}" class="relative p-1.5 sm:p-2 md:p-2.5 bg-[#9F9F9F] rounded-full hover:bg-gray-500 transition-colors flex items-center justify-center group shrink-0">
                    <svg class="w-4 h-4 sm:w-5 sm:h-5 md:w-6 md:h-6 lg:w-7 lg:h-7 text-black group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                    </svg>
                    @if($unreadCount > 0)
                        <span class="absolute -top-1 -right-1 bg-[#FF0000] text-white text-[8px] sm:text-[9px] md:text-[11px] font-bold rounded-full h-[14px] w-[14px] sm:h-[16px] sm:w-[16px] md:h-[18px] md:w-[18px] flex items-center justify-center shadow-[0_2px_4px_rgba(0,0,0,0.3)] ring-2 ring-[#D9D9D9]">
                            {{ $unreadCount > 99 ? '99+' : $unreadCount }}
                        </span>
                    @endif
                </a>

                <div class="flex items-center gap-3 md:gap-4">
                    <div class="text-right flex flex-col justify-center max-w-[90px] sm:max-w-[150px] md:max-w-none">
                        <span class="text-[10px] sm:text-[12px] md:text-[14px] lg:text-[17px] font-bree uppercase text-black font-normal truncate">{{ $roleName ?? 'ROLE' }}</span>
                        <span class="text-[10px] sm:text-[12px] md:text-[14px] lg:text-[17px] font-bree text-black font-normal truncate">
                            @php
                                $dUser = auth()->user();
                                $dId = '';
                                if ($dUser) {
                                    $roleStr = strtolower($dUser->role);
                                    if ($roleStr === 'mahasiswa') {
                                        $dId = optional($dUser->mahasiswa)->nim;
                                    } elseif ($roleStr === 'dosen' || str_contains($roleStr, 'koordinator')) {
                                        $dId = optional($dUser->dosen)->nidn;
                                    }
                                }
                            @endphp
                            {{ $userName ?? 'User' }}{{ $dId ? ' - ' . $dId : '' }}
                        </span>
                    </div>
                    <a href="{{ route('profil.index') }}" class="h-[32px] w-[32px] sm:h-[40px] sm:w-[40px] md:h-[58px] md:w-[58px] lg:h-[68px] lg:w-[68px] rounded-full bg-[#140EBF] flex items-center justify-center text-white font-bold text-sm md:text-xl lg:text-2xl border-[2px] md:border-[3px] lg:border-4 border-[#D9D9D9] overflow-hidden shadow-sm hover:scale-105 transition-transform shrink-0">
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
                        <h2 class="text-2xl font-bold font-inter text-black flex items-center gap-4">
                            @if(isset($backUrl))
                                <a href="{{ $backUrl }}" class="w-8 h-8 flex items-center justify-center rounded-full bg-gray-200 hover:bg-gray-300 transition-colors text-gray-700">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"></path></svg>
                                </a>
                            @endif
                            {{ $header }}
                        </h2>
                        @if(!isset($hidePeriodSelector) && isset($available_periods) && $available_periods->isNotEmpty())
                            <div class="relative w-full md:w-[212px] mt-2 md:mt-0 z-50">
                                <form method="POST" action="{{ route('set-periode') }}" id="periode-form">
                                    @csrf
                                    <input type="hidden" name="periode_id" id="periode-id-input" value="{{ $selected_period_id }}">
                                    <div x-data="{ open: false }" class="relative">
                                        <button @click="open = !open" @click.outside="open = false" type="button"
                                            class="w-full flex items-center justify-between border border-[#CAC0C0] bg-[#FBFBFB] rounded-[5px] shadow-sm text-[13px] font-medium py-1.5 px-3 focus:outline-none focus:border-[#4CC098] focus:ring-1 focus:ring-[#4CC098] cursor-pointer text-black h-[32px]">
                                            <span class="truncate">{{ $selected_period_label }}</span>
                                            <svg :class="open ? 'rotate-0' : 'rotate-90'"
                                                class="w-3.5 h-3.5 text-gray-500 transition-transform duration-200 flex-shrink-0" fill="none"
                                                stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                            </svg>
                                        </button>
                                        <div x-show="open" x-transition x-cloak style="display: none;"
                                            class="absolute right-0 z-50 w-full md:w-[212px] mt-1 bg-[#FBFBFB] border border-[#CAC0C0] rounded-[5px] shadow-lg overflow-hidden max-h-60 overflow-y-auto">
                                            <ul class="py-1 text-[13px] font-medium text-black">
                                                @foreach($available_periods as $period)
                                                    <li>
                                                        <button type="button" onclick="document.getElementById('periode-id-input').value = '{{ $period->id }}'; document.getElementById('periode-form').submit();"
                                                            class="block w-full text-left px-3 py-2 hover:bg-[#E8E5E5] transition-colors cursor-pointer {{ $selected_period_id == $period->id ? 'bg-[#E8E5E5]' : '' }}">
                                                            {{ $period->label_tahun_ajaran }}
                                                        </button>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        @endif
                    </div>
                @endif
                
                <div class="mt-4 w-full relative z-0">
                    @if(isset($is_locked) && $is_locked)
                        <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-6 rounded shadow-sm">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-yellow-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <p class="text-[13px] text-yellow-700 font-medium">
                                        <strong>Mode Lihat Saja:</strong> Anda sedang melihat data dari periode akademik lama. Semua aksi perubahan data telah dikunci.
                                    </p>
                                </div>
                            </div>
                        </div>
                        <script>
                            document.addEventListener('DOMContentLoaded', () => {
                                const actionKeywords = ['simpan', 'tambah', 'update', 'sahkan', 'hapus', 'terima', 'tolak', 'setujui', 'generate', 'auto', 'reset', 'cancel', 'kirim', 'upload', 'buka', 'tutup'];
                                const safeKeywords = ['download', 'export', 'detail', 'lihat', 'cari', 'clear', 'next', 'previous'];

                                function lockElements() {
                                    // 1. Disable all forms except GET forms and specific exclusions like periode-form
                                    document.querySelectorAll('form').forEach(form => {
                                        if (form.id === 'periode-form' || form.method.toUpperCase() === 'GET' || form.dataset.locked) return;
                                        form.dataset.locked = 'true';
                                        form.addEventListener('submit', e => {
                                            e.preventDefault();
                                            alert('Aksi tidak diizinkan: Anda berada di mode lihat saja (periode lama).');
                                        });
                                    });

                                    // 2. Disable buttons, links, selects, and inputs based on keywords
                                    document.querySelectorAll('button:not([data-locked="true"]), a:not([data-locked="true"]), select:not([data-locked="true"]), input[type="radio"]:not([data-locked="true"]), input[type="checkbox"]:not([data-locked="true"])').forEach(el => {
                                        const text = (el.textContent || el.title || el.placeholder || '').toLowerCase().trim();
                                        const alpineClick = (el.getAttribute('@click') || el.getAttribute('@change') || el.getAttribute('x-on:click') || el.getAttribute('x-on:change') || '').toLowerCase();
                                        
                                        // 1. ALWAYS skip specific safe zones
                                        if (el.closest('.pagination') || el.closest('#periode-form')) return;

                                        // 2. Detect Actions
                                        const actionKeywordsExtended = [...actionKeywords, 'edit', 'import', 'reset', 'pilih'];
                                        const isSubmit = el.tagName === 'BUTTON' && el.type === 'submit' && el.closest('form') !== null;
                                        const hasActionText = actionKeywordsExtended.some(kw => text.includes(kw));
                                        const isAlpineAction = alpineClick.includes('store') || alpineClick.includes('update') || alpineClick.includes('destroy') || alpineClick.includes('delete') || alpineClick.includes('save') || alpineClick.includes('status');

                                        if (isSubmit || hasActionText || isAlpineAction) {
                                            // DOUBLE CHECK: Even if it looks like an action, some things are explicitly safe
                                            // but ONLY if they aren't explicitly destructive/modifying
                                            const isExplicitlySafe = safeKeywords.some(kw => text.includes(kw));
                                            
                                            if (!isExplicitlySafe) {
                                                if (el.tagName === 'BUTTON' || el.tagName === 'INPUT' || el.tagName === 'SELECT') {
                                                    el.disabled = true;
                                                } else if (el.tagName === 'A') {
                                                    el.removeAttribute('href');
                                                    el.style.pointerEvents = 'none';
                                                }
                                                el.classList.add('opacity-50', 'cursor-not-allowed', 'grayscale');
                                                el.title = 'Terkunci di periode lama';
                                                el.dataset.locked = 'true';
                                                return; // Locked!
                                            }
                                        }

                                        // 3. Detect UI Toggles/Safe Keywords
                                        const isSafeKeyword = safeKeywords.some(kw => text.includes(kw));
                                        const isUIToggle = alpineClick.includes('tab') || alpineClick.includes('switch') || alpineClick.includes('open') || alpineClick.includes('toggle') || alpineClick.includes('show');

                                        if (isSafeKeyword || isUIToggle) {
                                            return; // It's a safe UI interaction
                                        }
                                    });
                                }

                                // Initial lock
                                lockElements();

                                // Observe DOM changes for AlpineJS dynamic renders
                                const observer = new MutationObserver((mutations) => {
                                    let shouldRun = false;
                                    mutations.forEach(m => {
                                        if (m.addedNodes.length > 0) shouldRun = true;
                                    });
                                    if (shouldRun) lockElements();
                                });

                                observer.observe(document.body, { childList: true, subtree: true });
                            });
                        </script>
                    @endif
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