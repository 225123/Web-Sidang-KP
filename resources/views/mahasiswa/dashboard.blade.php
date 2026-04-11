<x-dashboard-layout header="DASHBOARD" userName="{{ auth()->user()->name }}" roleName="MAHASISWA">
    <x-slot:sidebar>
        @include('mahasiswa.components.sidebar', ['active' => 'dashboard'])
    </x-slot>

    <!-- Header Actions passed to Layout to render nicely next to DASHBOARD text -->
    <x-slot:headerActions>
        <div x-data="{ open: false, selected: 'Genap 2025/2026' }" class="relative w-[212px]">
            
            <button @click="open = !open" @click.outside="open = false" type="button" class="w-full flex items-center justify-between border border-[#CAC0C0] bg-[#FBFBFB] rounded-[5px] shadow-sm text-[13px] font-medium py-2 px-3 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 cursor-pointer text-black">
                
                <span x-text="selected"></span>
                
                <svg :class="open ? 'rotate-90' : 'rotate-180'" class="w-3.5 h-3.5 text-gray-500 transition-transform duration-200 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
            </button>

            <div x-show="open" x-transition style="display: none;" class="absolute z-50 w-full bg-[#FBFBFB] border border-[#CAC0C0] rounded-[5px] shadow-lg overflow-hidden">
                <ul class="py-1 text-[13px] font-medium text-black">
                    <li>
                        <button @click="selected = 'Genap 2025/2026'; open = false" type="button" class="block w-full text-left px-3 py-2 hover:bg-[#E8E5E5] transition-colors cursor-pointer">
                            Genap 2025/2026
                        </button>
                    </li>
                    <li>
                        <button @click="selected = 'Ganjil 2025/2026'; open = false" type="button" class="block w-full text-left px-3 py-2 hover:bg-[#E8E5E5] transition-colors cursor-pointer">
                            Ganjil 2025/2026
                        </button>
                    </li>
                </ul>
            </div>
            
            <input type="hidden" name="periode" :value="selected">
        </div>
    </x-slot:headerActions>

    <style>
        /* Custom Native Scrollbar for Webkit */
        .custom-scrollbar::-webkit-scrollbar {
            width: 8px;
        }
        .custom-scrollbar::-webkit-scrollbar-track {
            background: #FFFFFF;
            border: 1px solid #D9D9D9;
            border-radius: 10px;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background-color: #666666;
            border-radius: 10px;
        }
    </style>

    <!-- Responsive Dashboard Content Layout -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 lg:gap-12 mt-6">
        <!-- Left Column -->
        <div class="flex flex-col gap-8 lg:gap-10 w-full">
            <!-- Status Kerja Praktik Card -->
            <div class="bg-[#ECECEC] rounded-[30px] p-8 shadow-sm min-h-[436px]">
                <div class="flex items-center gap-2 mb-6">
                    <h3 class="font-bold text-black text-[17px]">Status Kerja Praktik</h3>
                    <span class="font-medium text-black text-[17px] ml-1">: <span class="text-[#BFA512]">On Progress</span></span>
                </div>

                <div class="flex flex-col gap-3.5 text-[13px] font-medium text-black">
                    <div class="flex"><div class="w-[180px]">Judul Projek KP</div><div>: Website Sidang KP</div></div>
                    <div class="flex"><div class="w-[180px]">Sumber Instansi</div><div>: Internal UKRIDA</div></div>
                    <div class="flex"><div class="w-[180px]">Supervisor</div><div>: Rita Wiryasaputra, S.T., M.Cs., Ph. D.</div></div>
                    <div class="flex"><div class="w-[180px]">Dosen Pembimbing</div><div>: Dra. Florensa Rosani Purba, M.Si.</div></div>
                    <div class="flex"><div class="w-[180px]">Total bimbingan dosen</div><div>: 6 / 12</div></div>
                    <div class="flex"><div class="w-[180px]">Total bimbingan supervisior</div><div>: 3 / 6</div></div>
                    <div class="flex"><div class="w-[180px]">Progress KP</div><div>: 50 %</div></div>
                </div>

                <div class="mt-6 flex gap-12 items-center justify-center relative left-[-20px]">
                    <div class="relative rounded-full overflow-hidden flex-shrink-0" style="width: 125px; height: 125px; background: conic-gradient(black 0% 25%, #D1C6C6 25% 75%, black 75% 100%);">
                        <div class="absolute inset-0 z-20 flex flex-col justify-between items-center py-5">
                            <span class="text-[13px] text-white font-medium">50 %</span>
                            <span class="text-[13px] text-black font-medium">50 %</span>
                        </div>
                    </div>
                    
                    <div class="space-y-4">
                        <div class="flex items-center gap-3">
                            <div class="w-[14px] h-[14px] bg-black"></div>
                            <span class="text-[13px] text-black font-medium">tuntas</span>
                        </div>
                        <div class="flex items-center gap-3">
                            <div class="w-[14px] h-[14px] bg-[#D1C6C6]"></div>
                            <span class="text-[13px] text-black font-medium">belum tuntas</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Notifikasi -->
            <div class="bg-[#ECECEC] rounded-[10px] pt-4 pb-4 pl-6 pr-2 shadow-sm border border-[#D9D9D9] h-[252px] flex flex-col">
                <h3 class="font-semibold text-black text-[18px] mb-4 pr-4">Notifikasi (3)</h3>
                
                <div class="space-y-4 w-full flex-1 overflow-y-auto pr-4 custom-scrollbar">
                    <div class="border-b border-[#D9D9D9] pb-3">
                        <h4 class="font-medium text-[14px] text-[#1A1A1A]">Batas Pengajuan Sidang KP</h4>
                        <p class="text-[11px] text-[#666666] mt-1">Berdasarkan batas wakt uyang telah ditentukan, sidang ...</p>
                    </div>
                    <div class="border-b border-[#D9D9D9] pb-3">
                        <h4 class="font-medium text-[14px] text-[#1A1A1A]">Batas Pengajuan Sidang KP</h4>
                        <p class="text-[11px] text-[#666666] mt-1">Berdasarkan batas wakt uyang telah ditentukan, sidang...</p>
                    </div>
                    <div class="border-b border-[#D9D9D9] pb-3">
                        <h4 class="font-medium text-[14px] text-[#1A1A1A]">Batas Pengajuan Sidang KP</h4>
                        <p class="text-[11px] text-[#666666] mt-1">Berdasarkan batas wakt uyang telah ditentukan, sidang...</p>
                    </div>
                    <div class="border-b border-[#D9D9D9] pb-3 border-b-transparent">
                        <h4 class="font-medium text-[14px] text-[#1A1A1A]">Batas Pengajuan Sidang KP (Test Scroll)</h4>
                        <p class="text-[11px] text-[#666666] mt-1">Ini untuk mengetes fungsionalitas scroll bar asli bekerja.</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Column -->
        <div class="flex flex-col gap-8 lg:gap-10 w-full">
            <!-- Timeline Terdekat -->
            <div class="bg-[#ECECEC] rounded-[10px] p-6 shadow-sm border border-[#D9D9D9] h-[132px]">
                <h3 class="font-semibold text-[#1A1A1A] text-[18px] mb-4">Timeline Terdekat</h3>
                <p class="font-medium text-[#1A1A1A] text-[14px]">
                    Pengumpulan Berkas Sidang : 30/06/2026
                </p>
            </div>

            <!-- Status Sidang Kerja Praktik -->
            <div class="bg-[#ECECEC] rounded-[30px] p-8 shadow-sm h-[487px] border border-[#D9D9D9] flex flex-col justify-between">
                <div>
                    <h3 class="font-bold text-black text-[17px] mb-8">
                        Status Sidang Kerja Praktik : <span class="font-medium text-[#0E0E0B]">Belum Mendaftar</span>
                    </h3>

                    <div class="flex flex-col gap-5 text-[13px] font-medium text-black">
                        <div class="flex"><div class="w-[140px]">Hari / Tanggal</div><div>: -</div></div>
                        <div class="flex"><div class="w-[140px]">Waktu</div><div>: -</div></div>
                        <div class="flex"><div class="w-[140px]">Ruangan</div><div>: -</div></div>
                        <div class="flex mt-3"><div class="w-[140px]">Dosen Penguji 1</div><div>: -</div></div>
                        <div class="flex"><div class="w-[140px]">Dosen Penguji 2</div><div>: -</div></div>
                        <div class="flex"><div class="w-[140px]">Nilai</div><div>: -</div></div>
                    </div>
                </div>

                <div class="mt-8 flex justify-end">
                    <button class="bg-[#FFFF1A] hover:bg-yellow-400 text-black font-medium text-[13px] w-[184px] h-[34px] rounded-[20px] flex items-center justify-center gap-2 transform hover:-translate-y-0.5 transition-transform shadow">
                        <svg class="w-3.5 h-3.5 transform -rotate-45" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path></svg>
                        Mendaftar Sidang
                    </button>
                </div>
            </div>
        </div>
    </div>
</x-dashboard-layout>
