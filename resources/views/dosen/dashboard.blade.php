<x-dashboard-layout header="DASHBOARD" userName="Nama - 123456789" roleName="DOSEN">
    <x-slot:sidebar>
        @include('dosen.components.sidebar', ['active' => 'dashboard'])
    </x-slot>

    <!-- Semester Dropdown -->
    <div class="mb-6">
        <select class="border-gray-300 rounded-md shadow-sm text-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
            <option>Genap 2025/2026</option>
            <option>Ganjil 2025/2026</option>
        </select>
    </div>

    <!-- Stat Cards Row -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-blue-500 text-white rounded-lg p-4 shadow flex items-center relative overflow-hidden h-24">
            <div class="absolute left-0 top-0 bottom-0 bg-blue-600/30 w-12 flex items-center justify-center">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 4v12l-4-2-4 2V4M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
            </div>
            <div class="ml-12 w-full text-center">
                <div class="text-3xl font-bold">3</div>
                <div class="text-[11px] font-medium mt-1">Mahasiswa Bimbingan KP</div>
            </div>
        </div>
        
        <div class="bg-orange-500 text-white rounded-lg p-4 shadow flex items-center relative overflow-hidden h-24">
            <div class="absolute left-0 top-0 bottom-0 bg-orange-600/30 w-12 flex items-center justify-center">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
            <div class="ml-12 w-full text-center">
                <div class="text-3xl font-bold">2</div>
                <div class="text-[11px] font-medium mt-1">Mahasiswa Belum Sidang</div>
            </div>
        </div>

        <div class="bg-[#48bb78] text-white rounded-lg p-4 shadow flex items-center relative overflow-hidden h-24">
            <div class="absolute left-0 top-0 bottom-0 bg-green-700/30 w-12 flex items-center justify-center">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
            <div class="ml-12 w-full text-center">
                <div class="text-3xl font-bold">1</div>
                <div class="text-[11px] font-medium mt-1">Mahasiswa Telah Sidang</div>
            </div>
        </div>

        <div class="bg-yellow-500 text-white rounded-lg p-4 shadow flex items-center relative overflow-hidden h-24">
            <div class="absolute left-0 top-0 bottom-0 bg-yellow-600/30 w-12 flex items-center justify-center">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
            </div>
            <div class="ml-12 w-full text-center">
                <div class="text-3xl font-bold">10</div>
                <div class="text-[11px] font-medium mt-1">Sidang Terjadwal</div>
            </div>
        </div>
    </div>
    
</x-dashboard-layout>
