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

    <script defer src="https://cdn.jsdelivr.net/npm/@alpinejs/persist@3.x.x/dist/cdn.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        .font-bree { font-family: 'Bree Serif', serif; }
        .font-inter { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body x-data="{ sidebarOpen: true }" class="font-inter antialiased bg-[#F5F6F8] text-gray-900 flex flex-col min-h-screen">
    
    <header class="bg-[#D9D9D9] sticky top-0 z-50 shadow-[0px_4px_4px_rgba(0,0,0,0.25)] border-b border-gray-300 h-[76px]">
        <div class="flex items-center justify-between px-9 h-full">
            <div class="flex items-center gap-6 shrink-0">
                <button @click="sidebarOpen = !sidebarOpen" class="p-2 bg-gray-300 hover:bg-gray-400 rounded-lg transition-colors focus:outline-none">
                    <svg class="w-6 h-6 text-black" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                </button>
                <h1 class="text-[20px] font-bold uppercase leading-tight text-black font-serif tracking-widest mt-2">
                    KERJA<br/>PRAKTEK
                </h1>
            </div>
            
            <div class="flex items-center gap-6 mt-1">
                <button class="relative p-2.5 bg-[#9F9F9F] rounded-full hover:bg-gray-500 transition-colors">
                    <svg class="w-6 h-6 text-black" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                    </svg>
                    <span class="absolute top-0 right-0 bg-[#FF0000] text-white text-[11px] font-bold rounded-full h-[15px] w-[15px] flex items-center justify-center translate-x-1 -translate-y-1">
                        {{ $notificationCount ?? 3 }}
                    </span>
                </button>

                <div class="flex items-center gap-4">
                    <div class="text-right flex flex-col justify-center">
                        <span class="text-[17px] font-bree uppercase text-black font-normal">{{ $roleName ?? 'ROLE' }}</span>
                        <span class="text-[17px] font-bree text-black font-normal">{{ $userName ?? '123456789' }}</span>
                    </div>
                    <div class="h-[68px] w-[68px] rounded-full bg-[#140EBF] flex items-center justify-center text-white font-bold text-2xl border-4 border-[#D9D9D9]">
                        {{ substr($userName ?? 'R', 0, 1) }}
                    </div>
                </div>
            </div>
        </div>
    </header>

    <div class="flex flex-1 overflow-hidden relative z-0">
        <aside :class="sidebarOpen ? 'w-[221px]' : 'w-[76px]'" class="bg-[#D9D9D9] flex-shrink-0 overflow-y-auto overflow-x-hidden min-h-screen pb-10 transition-all duration-300 ease-in-out relative group">
            <div class="py-6 w-[221px]">
                {{ $sidebar }}
            </div>
        </aside>

        <main class="flex-1 overflow-y-auto p-8 bg-[#F5F6F8] pb-20 transition-all duration-300">
            @if(isset($header))
                <div class="mb-8 flex flex-col md:flex-row md:justify-between md:items-center gap-4 w-full">
                    <h2 class="text-2xl font-bold font-inter text-black">{{ $header }}</h2>
                    @if(isset($headerActions))
                        <div>{{ $headerActions }}</div>
                    @endif
                </div>
            @endif
            
            <div class="mt-4 w-full">
                {{ $slot }}
            </div>
        </main>
    </div>

    <footer class="bg-[#040404] text-white text-[11px] font-medium font-inter text-center py-5 w-full relative z-50 h-[93px] flex items-center justify-center gap-2">
        <span>2026 Sidang KP | Universitas Kristen Krida Wacana</span>
        <div class="w-[9px] h-[9px] rounded-full bg-black border border-white flex items-center justify-center text-[9px] italic ml-1">
            <span class="leading-none transform -translate-y-px">c</span>
        </div>
    </footer>

</body>
</html>