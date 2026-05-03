<x-dashboard-layout header="Profil Pengguna" userName="{{ auth()->user()->name }}" roleName="MAHASISWA" hidePeriodSelector="true">
    <x-slot:sidebar>
        @include('mahasiswa.components.sidebar', ['active' => 'profil'])
    </x-slot>


    <div class="mt-6">
        @include('components.profile-content')
    </div>
</x-dashboard-layout>
