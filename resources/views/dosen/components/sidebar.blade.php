@props(['active' => 'dashboard'])

@php
    // Menu Utama
    $baseClass = "flex items-center gap-3 pl-4 pr-3 h-[35px] text-[13px] font-medium transition-colors cursor-pointer text-left overflow-hidden whitespace-nowrap rounded-[5px] w-full";
    $inactiveClass = $baseClass . " text-[#333333] hover:bg-[#E8E5E5]";
    $activeClass = $baseClass . " text-white bg-[#CDA057]";

    // Submenu
    $subInactiveClass = "flex items-center gap-3 pl-10 pr-4 py-1.5 text-[12px] text-[#333333] transition-colors hover:bg-[#E8E5E5] w-full rounded-none whitespace-nowrap text-left";
    $subActiveClass = "flex items-center gap-3 pl-10 pr-4 py-1.5 text-[12px] font-medium text-white transition-colors bg-[#CDA057] w-full rounded-none whitespace-nowrap text-left";
@endphp

<a href="{{ route('dosen.dashboard') }}" class="{{ $active == 'dashboard' ? $activeClass : $inactiveClass }} mt-4 mb-2">
    <div class="flex items-center gap-3">
        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
        <span x-show="sidebarOpen" x-transition>Dashboard</span>
    </div>
</a>

<div x-data="{ open: localStorage.getItem('dosen_bimbingan') === 'true' }" class="mt-1">
    <button @click="open = !open; localStorage.setItem('dosen_bimbingan', open)" class="{{ $inactiveClass }} flex items-center justify-between outline-none" :class="!sidebarOpen && '!pr-4'">
        <div class="flex items-center gap-3">
            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
            <span x-show="sidebarOpen" x-transition>Bimbingan</span>
        </div>
        <svg :class="open ? 'rotate-0' : 'rotate-90'" class="w-3.5 h-3.5 transition-all duration-200 text-gray-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
    </button>
    <div x-show="open" x-transition class="mt-0.5 mb-2 bg-[#BBB8B8] w-full overflow-hidden flex flex-col divide-y divide-[#9E9B9B]">
        <a href="{{ route('dosen.daftar-mahasiswa') }}" class="{{ $active == 'daftar-mahasiswa' ? $subActiveClass : $subInactiveClass }}">
            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
            <span x-show="sidebarOpen" x-transition>Daftar Mahasiswa</span>
        </a>
        <a href="{{ route('dosen.persetujuan-sidang.index') }}" class="{{ $active == 'persetujuan-sidang' ? $subActiveClass : $subInactiveClass }}">
            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/></svg>
            <span x-show="sidebarOpen" x-transition>Persetujuan Sidang</span>
        </a>
    </div>
</div>

<a href="{{ route('dosen.jadwal-menguji') }}" class="{{ $active == 'jadwal-menguji' ? $activeClass : $inactiveClass }}">
    <div class="flex items-center gap-3">
        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
        <span x-show="sidebarOpen" x-transition>Jadwal Sidang</span>
    </div>
</a>

<div x-data="{ open: localStorage.getItem('dosen_penilaian') === 'true' }" class="mt-1">
    <button @click="open = !open; localStorage.setItem('dosen_penilaian', open)" class="{{ $inactiveClass }} flex items-center justify-between outline-none" :class="!sidebarOpen && '!pr-4'">
        <div class="flex items-center gap-3">
            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
            <span x-show="sidebarOpen" x-transition>Penilaian Sidang</span>
        </div>
        <svg :class="open ? 'rotate-0' : 'rotate-90'" class="w-3.5 h-3.5 transition-all duration-200 text-gray-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
    </button>
    <div x-show="open" x-transition class="mt-0.5 mb-2 bg-[#BBB8B8] w-full overflow-hidden flex flex-col divide-y divide-[#9E9B9B]">
        <a href="{{ route('dosen.input-nilai.index') }}" class="{{ $active == 'input-nilai' ? $subActiveClass : $subInactiveClass }}">
            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
            <span x-show="sidebarOpen" x-transition>Input Nilai</span>
        </a>
        <a href="{{ route('dosen.dummy', 'akumulasi-penilaian') }}" class="{{ $active == 'akumulasi-penilaian' ? $subActiveClass : $subInactiveClass }}">
            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
            <span x-show="sidebarOpen" x-transition>Akumulasi Penilaian</span>
        </a>
    </div>
</div>

<a href="{{ route('dosen.dummy', 'berita-acara') }}" class="{{ $active == 'berita-acara' ? $activeClass : $inactiveClass }}">
    <div class="flex items-center gap-3">
        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9.5a2.5 2.5 0 00-2.5-2.5H15"/></svg>
        <span x-show="sidebarOpen" x-transition>Berita Acara</span>
    </div>
</a>

<a href="{{ route('dosen.revisi.index') }}" class="{{ $active == 'revisi' ? $activeClass : $inactiveClass }}">
    <div class="flex items-center gap-3">
        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
        <span x-show="sidebarOpen" x-transition>Revisi</span>
    </div>
</a>

<a href="{{ route('dosen.notifikasi') }}" class="{{ $active == 'notifikasi' ? $activeClass : $inactiveClass }} mt-1">
    <div class="flex items-center gap-3">
        <svg class="w-4 h-4 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path d="M10 2a6 6 0 00-6 6v3.586l-1.707 1.707A1 1 0 003 14h14a1 1 0 00.707-1.707L16 11.586V8a6 6 0 00-6-6zM10 18a3 3 0 01-3-3h6a3 3 0 01-3 3z"/></svg>
        <span x-show="sidebarOpen" x-transition>Notifikasi</span>
    </div>
</a>

<div class="mt-4">
    <a href="{{ route('profil.index') }}" class="{{ $active == 'profil' ? $activeClass : $inactiveClass }}">
        <div class="flex items-center gap-3">
            <svg class="w-4 h-4 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/></svg>
            <span x-show="sidebarOpen" x-transition>Profil</span>
        </div>
    </a>

    <a href="{{ route('dosen.dummy', 'panduan') }}" class="{{ $active == 'panduan' ? $activeClass : $inactiveClass }} mt-1">
        <div class="flex items-center gap-3">
            <svg class="w-4 h-4 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/></svg>
            <span x-show="sidebarOpen" x-transition>Panduan Website</span>
        </div>
    </a>

    <div class="mt-1 pb-4">
        <form method="POST" action="{{ route('logout') }}" id="sidebar-logout-form-dosen">@csrf</form>
        <a href="#" onclick="event.preventDefault(); document.getElementById('sidebar-logout-form-dosen').submit();" class="flex items-center gap-3 pl-4 pr-3 h-[35px] w-full overflow-hidden whitespace-nowrap rounded-[5px] text-[12px] font-bold text-[#FF0000] hover:bg-[#E8E5E5] transition-colors cursor-pointer text-left">
            <div class="flex items-center gap-3">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                <span x-show="sidebarOpen" x-transition>Sign Out</span>
            </div>
        </a>
    </div>
</div>