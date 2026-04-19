<x-dashboard-layout header="Profil Pengguna" userName="{{ auth()->user()->name }}" roleName="DOSEN">
    <x-slot:sidebar>
        @include('dosen.components.sidebar', ['active' => 'profil'])
    </x-slot>

    <div class="mt-6">
        @include('components.profile-content')
    </div>
</x-dashboard-layout>
