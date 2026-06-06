@props(['active' => 'dashboard'])

@php
    // Menu Utama
    $baseClass = "flex items-center gap-3 pl-4 pr-3 h-[35px] text-[13px] font-medium transition-colors cursor-pointer text-left overflow-hidden whitespace-nowrap rounded-[5px] w-full";
    $inactiveClass = $baseClass . " text-[#333333] hover:bg-[#E8E5E5]";
    $activeClass = $baseClass . " text-white bg-[#4CC098]";

    // Submenu
    $subInactiveClass = "flex items-center gap-3 pl-10 pr-4 py-1.5 text-[12px] text-[#333333] transition-colors hover:bg-[#E8E5E5] w-full rounded-none whitespace-nowrap text-left";
    $subActiveClass = "flex items-center gap-3 pl-10 pr-4 py-1.5 text-[12px] font-medium text-white transition-colors bg-[#4CC098] w-full rounded-none whitespace-nowrap text-left";
@endphp

<a href="{{ route('koordinator.dashboard') }}" class="{{ $active == 'dashboard' ? $activeClass : $inactiveClass }} mt-4 mb-1">
    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
    <span x-show="sidebarOpen" x-transition>Dashboard</span>
</a>


<a href="{{ route('koordinator.timeline.index') }}" class="{{ $active == 'timeline' ? $activeClass : $inactiveClass }}">
    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
    <span x-show="sidebarOpen" x-transition>Timeline KP</span>
</a>

<div x-data="{ open: localStorage.getItem('koor_pendaftaran') === 'true' }" class="mt-1">
    <button @click="open = !open; localStorage.setItem('koor_pendaftaran', open)" class="{{ $inactiveClass }} flex items-center justify-between outline-none" :class="!sidebarOpen && '!pr-4'">
        <div class="flex items-center gap-3">
            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
            <span x-show="sidebarOpen" x-transition>Manajemen KP</span>
        </div>
        <svg :class="open ? 'rotate-0' : 'rotate-90'" class="w-3.5 h-3.5 transition-all duration-200 text-gray-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
    </button>
    <div x-show="open" x-transition class="mt-0.5 bg-[#BBB8B8] w-full overflow-hidden flex flex-col divide-y divide-[#9E9B9B]">
        <a href="{{ route('koordinator.pendaftaran-kp') }}" class="{{ $active == 'pendaftaran-kp' ? $subActiveClass : $subInactiveClass }}">
            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            <span x-show="sidebarOpen" x-transition>Pendaftaran KP</span>
        </a>
        <a href="{{ route('koordinator.data-mahasiswa.index') }}" class="{{ $active == 'data-mahasiswa' ? $subActiveClass : $subInactiveClass }}">
            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
            <span x-show="sidebarOpen" x-transition>Data Mahasiswa KP</span>
        </a>
        <a href="{{ route('koordinator.penugasan-pembimbing') }}" class="{{ $active == 'penugasan-pembimbing' ? $subActiveClass : $subInactiveClass }}">
            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
            <span x-show="sidebarOpen" x-transition>Penugasan Pembimbing</span>
        </a>
    </div>
</div>

<div x-data="{ open: localStorage.getItem('koor_bimbingan') === 'true' }" class="mt-1">
    <button @click="open = !open; localStorage.setItem('koor_bimbingan', open)" class="{{ $inactiveClass }} flex items-center justify-between outline-none" :class="!sidebarOpen && '!pr-4'">
        <div class="flex items-center gap-3">
            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l.586-.586z"/></svg>
            <span x-show="sidebarOpen" x-transition> Bimbingan</span>
        </div>
        <svg :class="open ? 'rotate-0' : 'rotate-90'" class="w-3.5 h-3.5 transition-all duration-200 text-gray-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
    </button>
    <div x-show="open" x-transition class="mt-0.5 bg-[#BBB8B8] w-full overflow-hidden flex flex-col divide-y divide-[#9E9B9B]">
        <a href="{{ route('koordinator.progress-umum') }}" class="{{ $active == 'progress-umum' ? $subActiveClass : $subInactiveClass }}">
            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
            <span x-show="sidebarOpen" x-transition>Progress Umum</span>
        </a>
        <a href="{{ route('koordinator.bimbingan-saya') }}" class="{{ $active == 'bimbingan-saya' ? $subActiveClass : $subInactiveClass }}">
            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/></svg>
            <span x-show="sidebarOpen" x-transition>Bimbingan Saya</span>
        </a>
        <a href="{{ route('koordinator.persetujuan-sidang.index') }}" class="{{ $active == 'persetujuan-sidang' ? $subActiveClass : $subInactiveClass }}">
            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/></svg>
            <span x-show="sidebarOpen" x-transition>Persetujuan Sidang</span>
        </a>
    </div>
</div>

<div x-data="{ open: localStorage.getItem('koor_manajemen_sidang') === 'true' }" class="mt-1">
    <button @click="open = !open; localStorage.setItem('koor_manajemen_sidang', open)" class="{{ $inactiveClass }} flex items-center justify-between outline-none" :class="!sidebarOpen && '!pr-4'">
        <div class="flex items-center gap-3">
            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"/></svg>
            <span x-show="sidebarOpen" x-transition>Manajemen Sidang</span>
        </div>
        <svg :class="open ? 'rotate-0' : 'rotate-90'" class="w-3.5 h-3.5 transition-all duration-200 text-gray-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
    </button>
    <div x-show="open" x-transition class="mt-0.5 bg-[#BBB8B8] w-full overflow-hidden flex flex-col divide-y divide-[#9E9B9B]">
        <a href="{{ route('koordinator.verifikasi-berkas') }}" class="{{ $active == 'verifikasi' ? $subActiveClass : $subInactiveClass }}">
            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            <span x-show="sidebarOpen" x-transition>Verifikasi Berkas Sidang</span>
        </a>
        <a href="{{ route('koordinator.penjadwalan.index') }}" class="{{ $active == 'penjadwalan' ? $subActiveClass : $subInactiveClass }}">
            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
            <span x-show="sidebarOpen" x-transition>Penjadwalan Sidang</span>
        </a>
        <a href="{{ route('koordinator.dosen-penguji') }}" class="{{ $active == 'dosen-penguji' ? $subActiveClass : $subInactiveClass }}">
            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
            <span x-show="sidebarOpen" x-transition>Penugasan Penguji</span>
        </a>
        <a href="{{ route('koordinator.kalender-sidang') }}" class="{{ $active == 'kalender-sidang' ? $subActiveClass : $subInactiveClass }}">
            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2"/></svg>
            <span x-show="sidebarOpen" x-transition>Kalender Sidang</span>
        </a>
    </div>
</div>

<div x-data="{ open: localStorage.getItem('koor_penilaian') === 'true' }" class="mt-1">
    <button @click="open = !open; localStorage.setItem('koor_penilaian', open)" class="{{ $inactiveClass }} flex items-center justify-between outline-none" :class="!sidebarOpen && '!pr-4'">
        <div class="flex items-center gap-3">
            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/></svg>
            <span x-show="sidebarOpen" x-transition>Sidang</span>
        </div>
        <svg :class="open ? 'rotate-0' : 'rotate-90'" class="w-3.5 h-3.5 transition-all duration-200 text-gray-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
    </button>
    <div x-show="open" x-transition class="mt-0.5 bg-[#BBB8B8] w-full overflow-hidden flex flex-col divide-y divide-[#9E9B9B]">
        <a href="{{ route('koordinator.jadwal-menguji') }}" class="{{ $active == 'jadwal-menguji' ? $subActiveClass : $subInactiveClass }}">
            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <span x-show="sidebarOpen" x-transition>Jadwal Menguji</span>
        </a>
        <a href="{{ route('koordinator.input-nilai.index') }}" class="{{ $active == 'input-nilai' ? $subActiveClass : $subInactiveClass }}">
            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
            <span x-show="sidebarOpen" x-transition>Input Nilai</span>
        </a>
    </div>
</div>

<div x-data="{ open: localStorage.getItem('koor_pasca_sidang') === 'true' }" class="mt-1">
    <button @click="open = !open; localStorage.setItem('koor_pasca_sidang', open)" class="{{ $inactiveClass }} flex items-center justify-between outline-none" :class="!sidebarOpen && '!pr-4'">
        <div class="flex items-center gap-3">
            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            <span x-show="sidebarOpen" x-transition>Pasca Sidang</span>
        </div>
        <svg :class="open ? 'rotate-0' : 'rotate-90'" class="w-3.5 h-3.5 transition-all duration-200 text-gray-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
    </button>
    <div x-show="open" x-transition class="mt-0.5 bg-[#BBB8B8] w-full overflow-hidden flex flex-col divide-y divide-[#9E9B9B]">
        <a href="{{ route('koordinator.rekap-revisi') }}" class="{{ $active == 'rekap-revisi' ? $subActiveClass : $subInactiveClass }}">
            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2a4 4 0 00-4-4H5m11 4v-2a4 4 0 014-4h.5m-11 4h7m-7 0L10 14m3 0l3 3m-3-3l-3 3"/></svg>
            <span x-show="sidebarOpen" x-transition>Rekap Revisi</span>
        </a>
        <a href="{{ route('koordinator.revisi.index') }}" class="{{ $active == 'revisi' ? $subActiveClass : $subInactiveClass }}">
            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
            <span x-show="sidebarOpen" x-transition>Revisi Sidang</span>
        </a>
        <a href="{{ route('koordinator.finalisasi-nilai.index') }}" class="{{ $active == 'finalisasi-nilai' ? $subActiveClass : $subInactiveClass }}">
            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/></svg>
            <span x-show="sidebarOpen" x-transition>Finalisasi Nilai</span>
        </a>
        <a href="{{ route('koordinator.laporan-arsip') }}" class="{{ $active == 'laporan-arsip' ? $subActiveClass : $subInactiveClass }}">
            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 19a2 2 0 01-2-2V7a2 2 0 012-2h4l2 2h4a2 2 0 012 2v1M5 19h14a2 2 0 002-2v-5a2 2 0 00-2-2H9a2 2 0 00-2 2v5a2 2 0 01-2 2z"/></svg>
            <span x-show="sidebarOpen" x-transition>Laporan Dan Arsip</span>
        </a>
    </div>
</div>

<div x-data="{ open: localStorage.getItem('koor_sistem') === 'true' }" class="mt-1">
    <button @click="open = !open; localStorage.setItem('koor_sistem', open)" class="{{ $inactiveClass }} flex items-center justify-between outline-none" :class="!sidebarOpen && '!pr-4'">
        <div class="flex items-center gap-3">
            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            <span x-show="sidebarOpen" x-transition>Sistem</span>
        </div>
        <svg :class="open ? 'rotate-0' : 'rotate-90'" class="w-3.5 h-3.5 transition-all duration-200 text-gray-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
    </button>
    <div x-show="open" x-transition class="mt-0.5 bg-[#BBB8B8] w-full overflow-hidden flex flex-col divide-y divide-[#9E9B9B]">
        <a href="{{ route('koordinator.periode-kp.index') }}" class="{{ $active == 'periode-kp' ? $subActiveClass : $subInactiveClass }}">
            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
            <span x-show="sidebarOpen" x-transition>Periode KP</span>
        </a>
       <a href="{{ route('koordinator.manajemen-akses') }}" class="{{ $active == 'manajemen-akses' ? $subActiveClass : $subInactiveClass }}">
    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
    <span x-show="sidebarOpen" x-transition>Manajemen User</span>
</a>
        <a href="{{ route('koordinator.dummy', 'pengumuman') }}" class="{{ $active == 'pengumuman' ? $subActiveClass : $subInactiveClass }}">
            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/></svg>
            <span x-show="sidebarOpen" x-transition>Pengumuman</span>
        </a>
        <a href="{{ route('koordinator.audit-log.index') }}" class="{{ $active == 'audit-log' ? $subActiveClass : $subInactiveClass }}">
            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 13h6m-3-3v6m5 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            <span x-show="sidebarOpen" x-transition>Audit Log</span>
        </a>
        <a href="{{ route('koordinator.backup.index') }}" class="{{ $active == 'backup' ? $subActiveClass : $subInactiveClass }}">
            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
            <span x-show="sidebarOpen" x-transition>Backup Database</span>
        </a>
        <a href="{{ route('koordinator.pemulihan-data.index') }}" class="{{ $active == 'pemulihan-data' ? $subActiveClass : $subInactiveClass }}">
            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
            <span x-show="sidebarOpen" x-transition>Pemulihan Data</span>
        </a>
    </div>
</div>

<div class="mt-4">
    <a href="{{ route('koordinator.notifikasi') }}" class="{{ $active == 'notifikasi' ? $activeClass : $inactiveClass }} mb-1">
        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
        <span x-show="sidebarOpen" x-transition>Notifikasi</span>
    </a>

    <a href="{{ route('profil.index') }}" class="{{ $active == 'profil' ? $activeClass : $inactiveClass }}">
        <svg class="w-4 h-4 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/></svg>
        <span x-show="sidebarOpen" x-transition>Profil</span>
    </a>

    <a href="{{ route('koordinator.dummy', 'panduan') }}" class="{{ $active == 'panduan' ? $activeClass : $inactiveClass }} mt-1">
        <svg class="w-4 h-4 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/></svg>
        <span x-show="sidebarOpen" x-transition>Panduan Website</span>
    </a>

    <div class="mt-1 pb-4">
        <form method="POST" action="{{ route('logout') }}" id="sidebar-logout-form-koor">
            @csrf
        </form>
        <a href="#" onclick="event.preventDefault(); document.getElementById('sidebar-logout-form-koor').submit();" class="flex items-center gap-3 pl-4 pr-3 h-[35px] w-full overflow-hidden whitespace-nowrap rounded-[5px] text-[12px] font-bold text-[#FF0000] hover:bg-[#E8E5E5] transition-colors cursor-pointer text-left">
            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
            <span x-show="sidebarOpen" x-transition>Sign Out</span>
        </a>
    </div>
</div>