<x-dashboard-layout header="DASHBOARD" userName="123456789" roleName="KOORDINATOR KP">
    <x-slot:sidebar>
        @include('koordinator.components.sidebar', ['active' => 'dashboard'])
    </x-slot>

    <!-- Semester Dropdown -->
    <div class="mb-6">
        <select class="border-gray-300 rounded-md shadow-sm text-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
            <option>Genap 2025/2026</option>
            <option>Ganjil 2025/2026</option>
        </select>
    </div>

    <!-- Stat Cards Row -->
    <div class="grid grid-cols-1 md:grid-cols-6 gap-4 mb-6">
        <!-- 4 Main Cards -->
        <div class="md:col-span-4 grid grid-cols-4 gap-4">
            <div class="bg-blue-500 text-white rounded-lg p-4 shadow flex items-center relative overflow-hidden">
                <div class="absolute left-0 top-0 bottom-0 bg-blue-600/30 w-12 flex items-center justify-center">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 4v12l-4-2-4 2V4M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                </div>
                <div class="ml-12 w-full text-center">
                    <div class="text-3xl font-bold">45</div>
                    <div class="text-xs mt-1">Peserta KP</div>
                </div>
            </div>
            
            <div class="bg-orange-500 text-white rounded-lg p-4 shadow flex items-center relative overflow-hidden">
                <div class="absolute left-0 top-0 bottom-0 bg-orange-600/30 w-12 flex items-center justify-center">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <div class="ml-12 w-full text-center">
                    <div class="text-3xl font-bold">43</div>
                    <div class="text-xs mt-1">KP Berjalan</div>
                </div>
            </div>

            <div class="bg-green-500 text-white rounded-lg p-4 shadow flex items-center relative overflow-hidden">
                <div class="absolute left-0 top-0 bottom-0 bg-green-600/30 w-12 flex items-center justify-center">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <div class="ml-12 w-full text-center">
                    <div class="text-3xl font-bold">2</div>
                    <div class="text-xs mt-1">KP Selesai</div>
                </div>
            </div>

            <div class="bg-yellow-500 text-white rounded-lg p-4 shadow flex items-center relative overflow-hidden">
                <div class="absolute left-0 top-0 bottom-0 bg-yellow-600/30 w-12 flex items-center justify-center">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                </div>
                <div class="ml-12 w-full text-center">
                    <div class="text-3xl font-bold">20</div>
                    <div class="text-xs mt-1">Sidang Terjadwal</div>
                </div>
            </div>
        </div>

        <!-- 2 Side Badges -->
        <div class="md:col-span-2 flex flex-col justify-center gap-2">
            <div class="bg-blue-500 text-white text-sm font-semibold rounded-md py-2 px-4 shadow text-center w-full max-w-[200px]">
                <div class="text-xl">39</div>
                <div class="text-[10px] font-normal mt-0.5">Sudah kumpul Berkas</div>
            </div>
            <div class="bg-orange-500 text-white text-sm font-semibold rounded-md py-2 px-4 shadow text-center w-full max-w-[200px]">
                <div class="text-xl">5</div>
                <div class="text-[10px] font-normal mt-0.5">Belum kumpul berkas</div>
            </div>
        </div>
    </div>

    <!-- Main Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Left Column (Charts) -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Bar Chart -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 min-h-[300px] flex flex-col">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="font-bold text-gray-800">Statistik Jadwal Sidang</h3>
                    <button class="bg-gray-100 border border-gray-200 text-xs px-3 py-1 rounded">Minggu</button>
                </div>
                <!-- Chart Placeholder -->
                <div class="flex-1 border-b border-l border-gray-200 relative flex items-end justify-around pb-2 pt-6">
                    <!-- Y Axis Labels -->
                    <div class="absolute left-[-25px] top-0 bottom-0 flex flex-col justify-between text-xs text-gray-400 py-2">
                        <span>55</span><span>40</span><span>25</span><span>0</span>
                    </div>
                    
                    <!-- Bars -->
                    <div class="w-10 bg-transparent h-4"></div>
                    <div class="w-10 bg-transparent h-4"></div>
                    <div class="w-10 h-10 flex gap-1 items-end">
                        <div class="w-2.5 h-6 bg-blue-500"></div>
                        <div class="w-2.5 h-12 bg-yellow-500"></div>
                        <div class="w-2.5 h-0 bg-green-500"></div>
                    </div>
                    <div class="w-10 h-full flex gap-1 items-end">
                        <div class="w-2.5 h-[85%] bg-blue-500"></div>
                        <div class="w-2.5 h-[95%] bg-yellow-500"></div>
                        <div class="w-2.5 h-[85%] bg-green-500"></div>
                        <div class="w-2.5 h-[5%] bg-red-500"></div>
                    </div>
                    <div class="w-10 h-full flex gap-1 items-end relative group">
                        <div class="w-2.5 h-[65%] bg-blue-500"></div>
                        <div class="w-2.5 h-[85%] bg-yellow-500"></div>
                        <div class="w-2.5 h-[75%] bg-green-500"></div>
                        
                        <!-- Tooltip Example -->
                        <div class="absolute -top-16 -left-10 bg-white border border-gray-200 shadow-md p-2 rounded text-[10px] hidden group-hover:block z-10 w-32">
                            <div>Sidang Terjadwal: 20</div>
                            <div>Sidang Menunggu: 15</div>
                            <div>Sidang Disetujui: 18</div>
                            <div>Sidang Ditolak: 2</div>
                        </div>
                    </div>
                </div>
                <!-- X Axis Labels -->
                <div class="flex justify-around text-xs text-gray-400 mt-2 pl-4">
                    <span>Sun</span><span>Mon</span><span>Tue</span><span>Wed</span><span>Thu</span><span>Fri</span><span>Sat</span>
                </div>
            </div>

            <!-- Pie Chart Card -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
                <h3 class="font-bold text-gray-800 mb-6">Progress Pelaksanaan Sidang</h3>
                <div class="flex flex-col sm:flex-row items-center gap-8">
                    <!-- Donut Chart Placeholder -->
                    <div class="relative w-40 h-40 rounded-full bg-gray-200 flex items-center justify-center" style="background: conic-gradient(#3b82f6 0% 40%, #e5e7eb 40% 100%);">
                        <div class="w-24 h-24 bg-white rounded-full"></div>
                    </div>
                    <!-- Legend Data -->
                    <div class="flex-1 space-y-4">
                        <div class="flex items-center gap-4">
                            <span class="text-2xl font-bold">40 %</span>
                            <span class="text-sm font-semibold text-gray-700">Mahasiswa sudah melakukan Sidang</span>
                        </div>
                        <div class="flex items-center gap-4">
                            <span class="text-2xl font-bold">60 %</span>
                            <span class="text-sm font-semibold text-gray-700">Mahasiswa belum melakukan Sidang</span>
                        </div>
                        
                        <div class="flex items-center gap-6 mt-4 pt-4 border-t border-gray-100 text-sm">
                            <div class="flex items-center gap-2">
                                <div class="w-3 h-3 bg-blue-500 rounded-sm"></div>
                                <span>Sudah Sidang</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <div class="w-3 h-3 bg-gray-200 rounded-sm"></div>
                                <span>Belum Sidang</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Column -->
        <div class="space-y-6">
            <!-- Timeline Terdekat -->
            <div class="bg-[#9ca3af] rounded-xl p-5 text-gray-800 shadow">
                <h3 class="font-bold mb-6">Timeline Terdekat</h3>
                <p class="font-bold text-base flex justify-between">
                    <span>Pengumpulan Berkas</span>
                    <span>: 30/06/2026</span>
                </p>
            </div>

            <!-- Notifikasi -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 h-[400px] flex flex-col">
                <h3 class="font-bold text-gray-800 mb-4">Notifikasi (10)</h3>
                
                <div class="overflow-y-auto flex-1 pr-2 space-y-4 custom-scrollbar">
                    <div class="border-b border-gray-100 pb-3">
                        <h4 class="font-semibold text-sm">Pengumpulan Berkas - Dari Mhs 1</h4>
                        <p class="text-xs text-gray-500 mt-1 truncate">Berdasarkan batas wakt uyang telah ditentukan, sidang ...</p>
                    </div>
                    <div class="border-b border-gray-100 pb-3">
                        <h4 class="font-semibold text-sm">Status Approval Jadwal Oleh Kaprodi</h4>
                        <p class="text-xs text-gray-500 mt-1 truncate">Jadwal disetujui</p>
                    </div>
                    <div class="border-b border-gray-100 pb-3">
                        <h4 class="font-semibold text-sm">Status Approval Dosen Penguji Oleh Kaprodi</h4>
                        <p class="text-xs text-gray-500 mt-1 truncate">Pembagian Disetujui</p>
                    </div>
                     <div class="border-b border-gray-100 pb-3">
                        <h4 class="font-semibold text-sm">Pengumpulan Berkas - Dari Mhs 1</h4>
                        <p class="text-xs text-gray-500 mt-1 truncate">Berdasarkan batas wakt uyang telah ditentukan, sidang ...</p>
                    </div>
                    <div class="border-b border-gray-100 pb-3">
                        <h4 class="font-semibold text-sm">Pengumpulan Berkas - Dari Mhs 1</h4>
                        <p class="text-xs text-gray-500 mt-1 truncate">Berdasarkan batas wakt uyang telah ditentukan, sidang ...</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-dashboard-layout>
