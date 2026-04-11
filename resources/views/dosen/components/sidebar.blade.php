@props(['active' => 'dashboard'])

@php
    $navItemClass = "flex items-center gap-3 px-3 py-2 rounded-md text-sm font-medium transition-colors cursor-pointer w-full text-left ";
    // Use golden/brownish color for active state to match image 3
    $activeClass = $navItemClass . "bg-[#cda057] text-white shadow-sm"; 
    $inactiveClass = $navItemClass . "text-gray-800 hover:bg-gray-300 hover:text-gray-900";

    $subItemClass = "block px-10 py-1.5 text-sm transition-colors ";
    $subActiveClass = $subItemClass . "font-semibold text-gray-900 bg-gray-200";
    $subInactiveClass = $subItemClass . "text-gray-700 hover:text-gray-900 hover:bg-gray-200";
@endphp

<!-- Dashboard -->
<a href="{{ route('dosen.dashboard') }}" class="{{ $active == 'dashboard' ? $activeClass : $inactiveClass }}">
    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
    Dashboard
</a>

<!-- Bimbingan (Dropdown) -->
<div x-data="{ open: true }" class="mt-1">
    <button @click="open = !open" class="{{ $inactiveClass }} flex justify-between">
        <div class="flex items-center gap-3">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
            Bimbingan
        </div>
        <svg :class="open ? 'rotate-180' : ''" class="w-4 h-4 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
    </button>
    <div x-show="open" class="py-1">
        <a href="{{ route('dosen.dummy', 'daftar-mahasiswa') }}" class="{{ $active == 'daftar-mahasiswa' ? $subActiveClass : $subInactiveClass }}">Daftar Mahasiswa</a>
        <a href="{{ route('dosen.dummy', 'persetujuan-sidang') }}" class="{{ $active == 'persetujuan-sidang' ? $subActiveClass : $subInactiveClass }}">Persetujuan Sidang</a>
    </div>
</div>

<!-- Jadwal Sidang -->
<a href="{{ route('dosen.dummy', 'jadwal-sidang') }}" class="{{ $active == 'jadwal-sidang' ? $activeClass : $inactiveClass }}">
    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
    Jadwal Sidang
</a>

<!-- Penilaian Sidang (Dropdown) -->
<div x-data="{ open: true }" class="mt-1">
    <button @click="open = !open" class="{{ $inactiveClass }} flex justify-between">
        <div class="flex items-center gap-3">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
            Penilaian Sidang
        </div>
        <svg :class="open ? 'rotate-180' : ''" class="w-4 h-4 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
    </button>
    <div x-show="open" class="py-1">
        <a href="{{ route('dosen.dummy', 'input-nilai') }}" class="{{ $active == 'input-nilai' ? $subActiveClass : $subInactiveClass }}">Input Nilai</a>
        <a href="{{ route('dosen.dummy', 'akumulasi-penilaian') }}" class="{{ $active == 'akumulasi-penilaian' ? $subActiveClass : $subInactiveClass }}">Akumulasi Penilaian</a>
    </div>
</div>

<!-- Berita Acara -->
<a href="{{ route('dosen.dummy', 'berita-acara') }}" class="{{ $active == 'berita-acara' ? $activeClass : $inactiveClass }}">
    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9.5a2.5 2.5 0 00-2.5-2.5H15"></path></svg>
    Berita Acara
</a>

<!-- Revisi -->
<a href="{{ route('dosen.dummy', 'revisi') }}" class="{{ $active == 'revisi' ? $activeClass : $inactiveClass }}">
    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
    Revisi
</a>

<!-- Profil -->
<a href="{{ route('dosen.dummy', 'profil') }}" class="{{ $active == 'profil' ? $activeClass : $inactiveClass }}">
    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
    Profil
</a>

<!-- Panduan Website -->
<a href="{{ route('dosen.dummy', 'panduan') }}" class="{{ $active == 'panduan' ? $activeClass : $inactiveClass }}">
    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
    Panduan Website
</a>

<div class="pt-4 mt-4 border-t border-gray-300">
    <a href="#" class="flex items-center gap-3 px-3 py-2 rounded-md text-sm font-medium text-red-500 hover:bg-red-50 transition-colors w-full text-left">
        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
        Sign Out
    </a>
</div>
