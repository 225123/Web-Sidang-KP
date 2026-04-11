@props(['active' => 'dashboard'])

@php
    $navItemClass = "flex items-center gap-3 px-3 py-2 rounded-md text-sm font-medium transition-colors cursor-pointer w-full text-left ";
    $activeClass = $navItemClass . "bg-[#5adbb5] text-white"; // somewhat match the emerald color tone
    $inactiveClass = $navItemClass . "text-gray-800 hover:bg-gray-300";

    $subItemClass = "block px-10 py-1.5 text-sm transition-colors ";
    $subActiveClass = $subItemClass . "font-semibold text-gray-900 bg-gray-200";
    $subInactiveClass = $subItemClass . "text-gray-700 hover:text-gray-900 hover:bg-gray-200";
@endphp

<!-- Dashboard -->
<a href="{{ route('koordinator.dashboard') }}" class="{{ $active == 'dashboard' ? $activeClass : $inactiveClass }}">
    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
    Dashboard
</a>

<!-- Timeline KP -->
<a href="{{ route('koordinator.dummy', 'timeline') }}" class="{{ $active == 'timeline' ? $activeClass : $inactiveClass }}">
    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
    Timeline KP
</a>

<!-- Pendaftaran KP -->
<a href="{{ route('koordinator.dummy', 'pendaftaran') }}" class="{{ $active == 'pendaftaran' ? $activeClass : $inactiveClass }}">
    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path></svg>
    Pendaftaran KP
</a>

<!-- Manajemen KP (Dropdown) -->
<div x-data="{ open: true }" class="mt-1">
    <button @click="open = !open" class="{{ $inactiveClass }} flex justify-between">
        <div class="flex items-center gap-3">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
            Manajemen KP
        </div>
        <svg :class="open ? 'rotate-180' : ''" class="w-4 h-4 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
    </button>
    <div x-show="open" class="py-1">
        <a href="{{ route('koordinator.dummy', 'data-mhs') }}" class="{{ $active == 'data-mhs' ? $subActiveClass : $subInactiveClass }}">Data Mahasiswa KP</a>
        <a href="{{ route('koordinator.dummy', 'pembimbing') }}" class="{{ $active == 'pembimbing' ? $subActiveClass : $subInactiveClass }}">Pembimbing</a>
    </div>
</div>

<!-- Pelaksanaan KP -->
<a href="{{ route('koordinator.dummy', 'pelaksanaan') }}" class="{{ $active == 'pelaksanaan' ? $activeClass : $inactiveClass }}">
    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
    Pelaksanaan KP
</a>

<!-- Manajemen Sidang (Dropdown) -->
<div x-data="{ open: true }" class="mt-1">
    <button @click="open = !open" class="{{ $inactiveClass }} flex justify-between">
        <div class="flex items-center gap-3">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>
            Manajemen Sidang
        </div>
        <svg :class="open ? 'rotate-180' : ''" class="w-4 h-4 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
    </button>
    <div x-show="open" class="py-1">
        <a href="{{ route('koordinator.dummy', 'verifikasi') }}" class="{{ $active == 'verifikasi' ? $subActiveClass : $subInactiveClass }}">Verifikasi Berkas</a>
        <a href="{{ route('koordinator.dummy', 'penjadwalan') }}" class="{{ $active == 'penjadwalan' ? $subActiveClass : $subInactiveClass }}">Penjadwalan Sidang</a>
        <a href="{{ route('koordinator.dummy', 'penguji') }}" class="{{ $active == 'penguji' ? $subActiveClass : $subInactiveClass }}">Dosen Penguji</a>
        <a href="{{ route('koordinator.dummy', 'kalender') }}" class="{{ $active == 'kalender' ? $subActiveClass : $subInactiveClass }}">Kalender Sidang</a>
    </div>
</div>

<!-- Pasca Sidang (Dropdown) -->
<div x-data="{ open: true }" class="mt-1">
    <button @click="open = !open" class="{{ $inactiveClass }} flex justify-between">
        <div class="flex items-center gap-3">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2"></path></svg>
            Pasca Sidang
        </div>
        <svg :class="open ? 'rotate-180' : ''" class="w-4 h-4 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
    </button>
    <div x-show="open" class="py-1">
        <a href="{{ route('koordinator.dummy', 'revisi') }}" class="{{ $active == 'revisi' ? $subActiveClass : $subInactiveClass }}">Revisi</a>
        <a href="{{ route('koordinator.dummy', 'nilai-akhir') }}" class="{{ $active == 'nilai-akhir' ? $subActiveClass : $subInactiveClass }}">Nilai Akhir</a>
        <a href="{{ route('koordinator.dummy', 'berita-acara') }}" class="{{ $active == 'berita-acara' ? $subActiveClass : $subInactiveClass }}">Berita Acara</a>
        <a href="{{ route('koordinator.dummy', 'laporan') }}" class="{{ $active == 'laporan' ? $subActiveClass : $subInactiveClass }}">Laporan KP</a>
    </div>
</div>

<!-- Sistem -->
<a href="{{ route('koordinator.dummy', 'sistem') }}" class="{{ $active == 'sistem' ? $activeClass : $inactiveClass }}">
    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
    Sistem
</a>

<a href="{{ route('koordinator.dummy', 'pengumuman') }}" class="{{ $active == 'pengumuman' ? $activeClass : $inactiveClass }}">
    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"></path></svg>
    Pengumuman
</a>

<a href="{{ route('koordinator.dummy', 'audit-log') }}" class="{{ $active == 'audit-log' ? $activeClass : $inactiveClass }}">
    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path></svg>
    Audit Log
</a>

<a href="{{ route('koordinator.dummy', 'panduan') }}" class="{{ $active == 'panduan' ? $activeClass : $inactiveClass }}">
    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
    Panduan Website
</a>

<div class="pt-4 mt-4 border-t border-gray-300">
    <a href="#" class="flex items-center gap-3 px-3 py-2 rounded-md text-sm font-medium text-red-500 hover:bg-red-50 transition-colors w-full text-left">
        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
        Sign Out
    </a>
</div>
