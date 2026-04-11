<x-dashboard-layout header="DASHBOARD" userName="{{ auth()->user()->name ?? 'NAMA - 123456789' }}" roleName="KOORDINATOR KP">
    <x-slot:sidebar>
        @include('koordinator.components.sidebar', ['active' => 'dashboard'])
    </x-slot:sidebar>

    <x-slot:headerActions>
        <div x-data="{ open: false, selected: 'Genap 2025/2026' }" class="relative w-[212px]">
            <button @click="open = !open" @click.outside="open = false" type="button" 
                class="w-full flex items-center justify-between border border-[#CAC0C0] bg-[#FBFBFB] rounded-[5px] shadow-sm text-[13px] font-medium py-1.5 px-3 focus:outline-none focus:border-[#4CC098] focus:ring-1 focus:ring-[#4CC098] cursor-pointer text-black h-[32px]">
                
                <span x-text="selected"></span>
                
                <svg :class="open ? 'rotate-0' : 'rotate-180'" class="w-3.5 h-3.5 text-gray-500 transition-transform duration-200 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                </svg>
            </button>

            <div x-show="open" x-transition style="display: none;" 
                class="absolute z-50 w-full mt-1 bg-[#FBFBFB] border border-[#CAC0C0] rounded-[5px] shadow-lg overflow-hidden">
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

    <div class="flex flex-col gap-6 mt-4 w-full">
        <div class="flex flex-wrap gap-4 items-center">
            <div class="flex gap-4">
                <div class="w-[188px] h-[71px] bg-[#3B82F6] rounded-[5px] relative overflow-hidden flex flex-col justify-center items-center shadow-sm">
                    <svg class="w-5 h-5 absolute left-3 top-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    <span class="text-[#E8F5E9] text-[28px] font-bold font-inter leading-none">45</span>
                    <span class="text-[#E8F5E9] text-[12px] font-medium font-inter mt-1">Peserta KP</span>
                </div>
                <div class="w-[188px] h-[71px] bg-[#E57835] rounded-[5px] relative overflow-hidden flex flex-col justify-center items-center shadow-sm">
                    <svg class="w-5 h-5 absolute left-3 top-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span class="text-[#E8F5E9] text-[28px] font-bold font-inter leading-none">43</span>
                    <span class="text-[#E8F5E9] text-[12px] font-medium font-inter mt-1">KP Berjalan</span>
                </div>
                <div class="w-[188px] h-[71px] bg-[#4CAF50] rounded-[5px] relative overflow-hidden flex flex-col justify-center items-center shadow-sm">
                    <svg class="w-5 h-5 absolute left-3 top-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span class="text-[#E8F5E9] text-[28px] font-bold font-inter leading-none">2</span>
                    <span class="text-[#E8F5E9] text-[12px] font-medium font-inter mt-1">KP Selesai</span>
                </div>
                <div class="w-[188px] h-[71px] bg-[#F4B400] rounded-[5px] relative overflow-hidden flex flex-col justify-center items-center shadow-sm">
                    <svg class="w-5 h-5 absolute left-3 top-3 text-black" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    <span class="text-black text-[28px] font-bold font-inter leading-none">20</span>
                    <span class="text-black text-[12px] font-medium font-inter mt-1">Sidang Terjadwal</span>
                </div>
            </div>
            
            <div class="flex gap-4 ml-6">
                <div class="w-[131px] h-[49px] bg-[#3B82F6] rounded-[3.5px] flex flex-col justify-center items-center shadow-sm relative top-[-10px]">
                    <span class="text-[#E8F5E9] text-[19.6px] font-bold font-inter leading-none">39</span>
                    <span class="text-[#E8F5E9] text-[8.4px] font-medium font-inter mt-1">Sudah kumpul Berkas</span>
                </div>
                <div class="w-[131px] h-[49px] bg-[#E57835] rounded-[3.5px] flex flex-col justify-center items-center shadow-sm relative top-[-10px]">
                    <span class="text-[#E8F5E9] text-[19.6px] font-bold font-inter leading-none">5</span>
                    <span class="text-[#E8F5E9] text-[8.4px] font-medium font-inter mt-1">Belum kumpul berkas</span>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 lg:gap-8">
            <div class="lg:col-span-2 bg-[#FEFEFF] rounded-[10px] border border-[#D9D9D9] p-6 h-[302px] relative shadow-sm overflow-hidden flex flex-col">
                <div class="flex justify-between items-start mb-6 w-full">
                    <h3 class="font-semibold text-[#1A1A1A] text-[18px] font-inter">Statistik Jadwal Sidang</h3>
                    <div class="bg-[#EDEBEB] border border-[#CAC0C0] rounded-[5px] px-3 py-1">
                        <span class="text-[12px] text-[#1A1A1A] font-medium">Minggu</span>
                    </div>
                </div>
                
                <div class="flex-1 relative border-l border-b border-[#B5A6A6] mt-4 ml-8 pb-4">
                    <div class="absolute -left-10 bottom-[0%] text-[#B5A6A6] text-[14px] font-semibold">0</div>
                    <div class="absolute -left-10 bottom-[25%] text-[#B5A6A6] text-[14px] font-semibold">25</div>
                    <div class="absolute -left-10 bottom-[50%] text-[#B5A6A6] text-[14px] font-semibold">40</div>
                    <div class="absolute -left-10 bottom-[75%] text-[#B5A6A6] text-[14px] font-semibold">55</div>

                    <div class="absolute left-0 bottom-[25%] w-full border-t border-[#B5A6A6] opacity-50"></div>
                    <div class="absolute left-0 bottom-[50%] w-full border-t border-[#B5A6A6] opacity-50"></div>
                    <div class="absolute left-0 bottom-[75%] w-full border-t border-[#B5A6A6] opacity-50"></div>
                    <div class="absolute left-0 bottom-[100%] w-full border-t border-[#B5A6A6] opacity-50"></div>

                    <div class="w-full h-full flex justify-around items-end relative bottom-0">
                        <div class="flex gap-0.5 items-end"></div>
                        <div class="flex gap-0.5 items-end"></div>
                        <div class="flex gap-0.5 items-end"></div>
                        <div class="flex gap-0.5 items-end"></div>
                        <div class="flex gap-0.5 items-end h-full">
                            <div class="w-[10px] h-[10%] bg-[#3B82F6]"></div>
                            <div class="w-[10px] h-[15%] bg-[#F4B400]"></div>
                        </div>
                        <div class="flex gap-0.5 items-end h-full">
                            <div class="w-[10px] h-[90%] bg-[#3B82F6]"></div>
                            <div class="w-[10px] h-[100%] bg-[#F4B400]"></div>
                            <div class="w-[10px] h-[90%] bg-[#4CAF50]"></div>
                        </div>
                        <div class="flex gap-0.5 items-end h-full">
                            <div class="w-[10px] h-[65%] bg-[#3B82F6]"></div>
                            <div class="w-[10px] h-[90%] bg-[#F4B400]"></div>
                            <div class="w-[10px] h-[75%] bg-[#4CAF50]"></div>
                        </div>
                    </div>
                </div>
                
                <div class="flex justify-around items-center w-[calc(100%-2rem)] ml-8 mt-2 text-[#B5A6A6] text-[14px] font-semibold">
                    <span>Sun</span><span>Mon</span><span>Tue</span><span>Wed</span><span>Thru</span><span>Fri</span><span>Sut</span>
                </div>
                
                <div class="absolute left-[8%] bottom-[35%] bg-white border border-[#B5A6A6] rounded px-3 py-2 text-[9px] font-medium leading-tight shadow-sm z-10">
                    Sidang Terjadwal : 0<br/>
                    Sedang Menunggu : 0<br/>
                    Sidang Disetujui : 0<br/>
                    Sidang Ditolak : 2
                </div>
            </div>

            <div class="bg-[#9F9F9F] rounded-[10px] border border-[#D9D9D9] p-6 h-[135px] shadow-sm flex flex-col justify-center">
                <h3 class="font-semibold text-[#1A1A1A] text-[18px] mb-4">Timeline Terdekat</h3>
                <div class="grid grid-cols-[1fr_auto] gap-4 mb-2 items-center">
                    <span class="text-[18px] font-bold text-[#1A1A1A]">Pengumpulan Berkas</span>
                    <span class="text-[18px] font-normal text-[#1A1A1A]">: 30/06/2026</span>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 lg:gap-8 min-h-[302px]">
            <div class="lg:col-span-2 bg-[#FEFEFF] rounded-[10px] border border-[#D9D9D9] p-8 shadow-sm flex flex-col justify-center">
                <h3 class="font-semibold text-[#1A1A1A] text-[18px] mb-8">Progress Pelaksanaan Sidang</h3>
                <div class="flex items-center gap-12 w-full h-[180px]">
                    <div class="relative flex-shrink-0" style="width: 170px; height: 170px; border-radius: 50%; background: conic-gradient(#E7DDDD 0% 60%, #3B82F6 60% 100%); transform: rotate(180deg);">
                        <div class="absolute inset-0 m-auto bg-white rounded-full" style="width: 110px; height: 110px;"></div>
                    </div>
                    <div class="flex flex-col gap-6 ml-8 w-full relative">
                        <div class="flex items-center gap-4">
                            <span class="text-[30px] font-bold font-inter text-black w-[80px]">40 %</span>
                            <span class="text-[18px] font-medium text-black">Mahasiswa sudah melakukan Sidang</span>
                        </div>
                        <div class="flex items-center gap-4">
                            <span class="text-[30px] font-bold font-inter text-black w-[80px]">60 %</span>
                            <span class="text-[18px] font-medium text-black">Mahasiswa belum melakukan Sidang</span>
                        </div>
                        <div class="flex gap-8 mt-4 ml-6">
                            <div class="flex items-center gap-2">
                                <div class="w-3.5 h-3.5 bg-[#3B82F6]"></div>
                                <span class="text-[14px]">Sudah Sidang</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <div class="w-3.5 h-3.5 bg-[#E7DDDD]"></div>
                                <span class="text-[14px]">Belum Sidang</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-[#FEFEFF] rounded-[10px] border border-[#D9D9D9] p-6 shadow-sm flex flex-col h-full max-h-[302px]">
                <h3 class="font-semibold text-[#1A1A1A] text-[18px] mb-4">Notifikasi (10)</h3>
                <div class="overflow-y-auto flex-1 pr-2">
                    <div class="flex flex-col divide-y divide-[#D9D9D9]">
                        @for($i=1; $i<=6; $i++)
                            <div class="py-3">
                                <h4 class="text-[14px] font-medium text-[#1A1A1A]">Pemberitahuan Sistem</h4>
                                <p class="text-[11px] text-[#666666] mt-1 line-clamp-1">Data notifikasi dashboard koordinator...</p>
                            </div>
                        @endfor
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-dashboard-layout>