@props(['active' => 'dashboard'])

@php
    // Menu Utama
    $inactiveClass = "flex items-center gap-3 px-6 h-[35px] mx-[1px] text-[13px] font-medium text-[#333333] transition-colors cursor-pointer text-left hover:bg-[#E8E5E5] overflow-hidden whitespace-nowrap rounded-[5px] w-full max-w-[219px]";
    
    $activeClass = "flex items-center gap-3 px-6 h-[35px] mx-[6px] text-[13px] font-medium text-white bg-[#F48200] rounded-[5px] transition-colors cursor-pointer text-left overflow-hidden whitespace-nowrap w-full max-w-[207px]";

    // Submenu
    $subInactiveClass = "flex items-center gap-3 pl-10 pr-4 py-1.5 text-[12px] text-[#333333] transition-colors hover:bg-[#E8E5E5] w-full rounded-none whitespace-nowrap";
    $subActiveClass = "flex items-center gap-3 pl-10 pr-4 py-1.5 text-[12px] font-medium text-white transition-colors bg-[#F48200] w-full rounded-none whitespace-nowrap";
@endphp

<a href="{{ route('mahasiswa.dashboard') }}" class="{{ $active == 'dashboard' ? $activeClass : $inactiveClass }} mt-4 mb-2">
    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
    <span x-show="sidebarOpen" x-transition>Dashboard</span>
</a>

<div x-data="{ open: localStorage.getItem('menu_kp') === 'true' }" class="mt-1">
    <button @click="open = !open; localStorage.setItem('menu_kp', open)" class="{{ $inactiveClass }} flex justify-between items-center outline-none">
        <div class="flex items-center gap-3">
            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
            <span x-show="sidebarOpen" x-transition>KP</span>
        </div>
        <svg x-show="sidebarOpen" :class="open ? 'rotate-0' : 'rotate-90'" class="w-3.5 h-3.5 transition-transform duration-200 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
    </button>
    
    <div x-show="open" x-transition class="mt-0.5 bg-[#BBB8B8] w-full overflow-hidden flex flex-col divide-y divide-[#9E9B9B]">
        <a href="{{ route('mahasiswa.dummy', 'pendaftaran-kp') }}" class="{{ $active == 'pendaftaran-kp' ? $subActiveClass : $subInactiveClass }}">
            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path></svg>
            <span x-show="sidebarOpen" x-transition>Mendaftar KP</span>
        </a>
        <a href="{{ route('mahasiswa.status-pendaftaran') }}" class="{{ $active == 'status-pendaftaran' ? $subActiveClass : $subInactiveClass }}">
            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 19a2 2 0 01-2-2V7a2 2 0 012-2h4l2 2h4a2 2 0 012 2v1M5 19h14a2 2 0 002-2v-5a2 2 0 00-2-2H9a2 2 0 00-2 2v5a2 2 0 01-2 2z"/></svg>
            <span x-show="sidebarOpen" x-transition>Status Pendaftaran</span>
        </a>
    </div>
</div>

<div x-data="{ open: localStorage.getItem('menu_bimbingan') === 'true' }" class="mt-1">
    <button @click="open = !open; localStorage.setItem('menu_bimbingan', open)" class="{{ $inactiveClass }} flex justify-between items-center outline-none">
        <div class="flex items-center gap-3">
            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l.586-.586z"/></svg>
            <span x-show="sidebarOpen" x-transition>Log Bimbingan</span>
        </div>
        <svg x-show="sidebarOpen" :class="open ? 'rotate-0' : 'rotate-90'" class="w-3.5 h-3.5 transition-transform duration-200 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
    </button>
    
    <div x-show="open" x-transition class="mt-0.5 bg-[#BBB8B8] w-full overflow-hidden flex flex-col divide-y divide-[#9E9B9B]">
        <a href="{{ route('mahasiswa.dummy', 'bimbingan-dosen') }}" class="{{ $active == 'bimbingan-dosen' ? $subActiveClass : $subInactiveClass }}">
            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5zm0 0v6"/></svg>
            <span x-show="sidebarOpen" x-transition>Bimbingan Dosen</span>
        </a>
        <a href="{{ route('mahasiswa.dummy', 'persetujuan-sidang') }}" class="{{ $active == 'persetujuan-sidang' ? $subActiveClass : $subInactiveClass }}">
            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/></svg>
            <span x-show="sidebarOpen" x-transition>Persetujuan Sidang KP</span>
        </a>
    </div>
</div>

<div x-data="{ open: localStorage.getItem('menu_sidang') === 'true' }" class="mt-1">
    <button @click="open = !open; localStorage.setItem('menu_sidang', open)" class="{{ $inactiveClass }} flex justify-between items-center outline-none">
        <div class="flex items-center gap-3">
            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"/></svg>
            <span x-show="sidebarOpen" x-transition>Sidang</span>
        </div>
        <svg x-show="sidebarOpen" :class="open ? 'rotate-0' : 'rotate-90'" class="w-3.5 h-3.5 transition-transform duration-200 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
    </button>
    
    <div x-show="open" x-transition class="mt-0.5 bg-[#BBB8B8] w-full overflow-hidden flex flex-col divide-y divide-[#9E9B9B]">
        <a href="{{ route('mahasiswa.dummy', 'pendaftaran-sidang') }}" class="{{ $active == 'pendaftaran-sidang' ? $subActiveClass : $subInactiveClass }}">
            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
            <span x-show="sidebarOpen" x-transition>Pendaftaran Sidang</span>
        </a>
        <a href="{{ route('mahasiswa.dummy', 'hasil-sidang') }}" class="{{ $active == 'hasil-sidang' ? $subActiveClass : $subInactiveClass }}">
            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
            <span x-show="sidebarOpen" x-transition>Hasil Sidang</span>
        </a>
        <a href="{{ route('mahasiswa.dummy', 'revisi') }}" class="{{ $active == 'revisi' ? $subActiveClass : $subInactiveClass }}">
            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
            <span x-show="sidebarOpen" x-transition>Revisi</span>
        </a>
    </div>
</div>

<a href="{{ route('mahasiswa.dummy', 'nilai-akhir') }}" class="{{ $active == 'nilai-akhir' ? $activeClass : $inactiveClass }} mt-1">
    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/></svg>
    <span x-show="sidebarOpen" x-transition>Nilai Akhir KP</span>
</a>

<a href="{{ route('mahasiswa.dummy', 'notifikasi') }}" class="{{ $active == 'notifikasi' ? $activeClass : $inactiveClass }}">
    <svg class="w-4 h-4 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path d="M10 2a6 6 0 00-6 6v3.586l-1.707 1.707A1 1 0 003 14h14a1 1 0 00.707-1.707L16 11.586V8a6 6 0 00-6-6zM10 18a3 3 0 01-3-3h6a3 3 0 01-3 3z"/></svg>
    <span x-show="sidebarOpen" x-transition>Notifikasi</span>
</a>

<div class="mt-1">
    <a href="{{ route('mahasiswa.dummy', 'profil') }}" class="{{ $active == 'profil' ? $activeClass : $inactiveClass }}">
        <svg class="w-4 h-4 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/></svg>
        <span x-show="sidebarOpen" x-transition>Profil</span>
    </a>

    <a href="{{ route('mahasiswa.dummy', 'panduan') }}" class="{{ $active == 'panduan' ? $activeClass : $inactiveClass }} mt-1">
        <svg class="w-4 h-4 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/></svg>
        <span x-show="sidebarOpen" x-transition>Panduan Website</span>
    </a>

    <div class="mt-1 pb-4">
        <form method="POST" action="{{ route('logout') }}" id="sidebar-logout-form">
            @csrf
        </form>
        <a href="#" onclick="event.preventDefault(); document.getElementById('sidebar-logout-form').submit();" class="flex items-center gap-3 px-6 h-[35px] mx-[1px] w-full max-w-[219px] overflow-hidden whitespace-nowrap rounded-[5px] text-[12px] font-bold text-[#FF0000] hover:bg-[#E8E5E5] transition-colors cursor-pointer text-left">
            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
            <span x-show="sidebarOpen" x-transition>Sign Out</span>
        </a>
    </div>
</div>