<x-dashboard-layout :header="$title" :userName="$userName" :roleName="$roleName">
    <x-slot:sidebar>
        @if($role == 'koordinator')
            @include('koordinator.components.sidebar', ['active' => $active])
        @elseif($role == 'mahasiswa')
            @include('mahasiswa.components.sidebar', ['active' => $active])
        @elseif($role == 'dosen')
            @include('dosen.components.sidebar', ['active' => $active])
        @endif
    </x-slot>

    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6 text-gray-900 flex flex-col items-center justify-center min-h-[300px]">
            <svg class="w-16 h-16 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 002-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
            <h3 class="text-xl font-bold text-gray-600">Halaman {{ $title }}</h3>
            <p class="text-gray-500 mt-2">Konten halaman ini belum diimplementasikan.</p>
        </div>
    </div>
</x-dashboard-layout>
