<x-dashboard-layout header="DASHBOARD" userName="Dr. Geovano Jas - 123456789" roleName="DOSEN">
    <x-slot:sidebar>
        @include('dosen.components.sidebar', ['active' => 'dashboard'])
    </x-slot>

    <x-slot:headerActions>
        <div x-data="{ open: false, selected: 'Genap 2025/2026' }" class="relative w-[212px]">
            <button @click="open = !open" @click.outside="open = false" type="button" 
                class="w-full flex items-center justify-between border border-[#CAC0C0] bg-[#FBFBFB] rounded-[5px] shadow-sm text-[13px] font-medium py-2 px-3 focus:outline-none focus:border-[#CDA057] focus:ring-1 focus:ring-[#CDA057] cursor-pointer text-black">
                
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

    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8 mt-6">
        <div class="bg-[#4285F4] text-white rounded-lg p-4 shadow-[0px_4px_4px_rgba(0,0,0,0.25)] flex items-center relative overflow-hidden h-24">
            <div class="absolute left-0 top-0 bottom-0 bg-black/10 w-12 flex items-center justify-center">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
            </div>
            <div class="ml-12 w-full text-center">
                <div class="text-3xl font-bold uppercase">3</div>
                <div class="text-[11px] font-bold uppercase leading-tight">Mahasiswa<br/>Bimbingan KP</div>
            </div>
        </div>
        
        <div class="bg-[#E67E22] text-white rounded-lg p-4 shadow-[0px_4px_4px_rgba(0,0,0,0.25)] flex items-center relative overflow-hidden h-24">
            <div class="absolute left-0 top-0 bottom-0 bg-black/10 w-12 flex items-center justify-center">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div class="ml-12 w-full text-center">
                <div class="text-3xl font-bold uppercase">2</div>
                <div class="text-[11px] font-bold uppercase leading-tight">Mahasiswa<br/>Belum Sidang</div>
            </div>
        </div>

        <div class="bg-[#27AE60] text-white rounded-lg p-4 shadow-[0px_4px_4px_rgba(0,0,0,0.25)] flex items-center relative overflow-hidden h-24">
            <div class="absolute left-0 top-0 bottom-0 bg-black/10 w-12 flex items-center justify-center">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div class="ml-12 w-full text-center">
                <div class="text-3xl font-bold uppercase">1</div>
                <div class="text-[11px] font-bold uppercase leading-tight">Mahasiswa<br/>Telah Sidang</div>
            </div>
        </div>

        <div class="bg-[#F1C40F] text-white rounded-lg p-4 shadow-[0px_4px_4px_rgba(0,0,0,0.25)] flex items-center relative overflow-hidden h-24">
            <div class="absolute left-0 top-0 bottom-0 bg-black/10 w-12 flex items-center justify-center">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
            </div>
            <div class="ml-12 w-full text-center">
                <div class="text-3xl font-bold uppercase">10</div>
                <div class="text-[11px] font-bold uppercase leading-tight">Sidang<br/>Terjadwal</div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">
        
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-lg shadow-md border border-gray-200 overflow-hidden">
                <div class="p-4 border-b border-gray-100 flex justify-between items-center bg-gray-50">
                    <h3 class="text-[14px] font-bold text-black uppercase">Statistik Bimbingan Mingguan</h3>
                    <span class="text-[11px] bg-white border border-gray-300 px-2 py-0.5 rounded text-gray-600">Minggu Ini</span>
                </div>
                <div class="p-6">
                    <div class="h-48 bg-gray-50 rounded-md flex items-end justify-around p-4 border border-dashed border-gray-300">
                        <div class="w-8 bg-[#4285F4] h-[40%] rounded-t-sm"></div>
                        <div class="w-8 bg-[#CDA057] h-[75%] rounded-t-sm"></div>
                        <div class="w-8 bg-[#4285F4] h-[60%] rounded-t-sm"></div>
                        <div class="w-8 bg-[#CDA057] h-[90%] rounded-t-sm"></div>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-md border border-gray-200 overflow-hidden">
                <div class="p-4 border-b border-gray-100 bg-gray-50">
                    <h3 class="text-[14px] font-bold text-black uppercase italic">Persetujuan Menunggu</h3>
                </div>
                <table class="w-full text-left">
                    <thead class="bg-gray-50 text-[11px] uppercase text-gray-500 font-bold border-b">
                        <tr>
                            <th class="px-6 py-3">Mahasiswa</th>
                            <th class="px-6 py-3 text-center">Jenis Berkas</th>
                            <th class="px-6 py-3 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 text-xs font-bold text-gray-700 uppercase">Geovano Yansen Jas</td>
                            <td class="px-6 py-4 text-center"><span class="px-2 py-1 bg-yellow-100 text-yellow-700 rounded-full text-[9px] font-black italic uppercase">Log Bimbingan</span></td>
                            <td class="px-6 py-4 text-right"><button class="text-blue-600 font-bold text-[11px] hover:underline">REVIEW</button></td>
                        </tr>
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 text-xs font-bold text-gray-700 uppercase">Justin Oskar</td>
                            <td class="px-6 py-4 text-center"><span class="px-2 py-1 bg-green-100 text-green-700 rounded-full text-[9px] font-black italic uppercase">Draft Laporan</span></td>
                            <td class="px-6 py-4 text-right"><button class="text-blue-600 font-bold text-[11px] hover:underline">REVIEW</button></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="space-y-6">
            <div class="bg-[#A0A0A0] text-black p-6 rounded-lg shadow-md h-fit flex flex-col">
                <h3 class="text-[16px] font-bold mb-6 uppercase tracking-widest border-b border-black/20 pb-2">Jadwal Terdekat</h3>
                
                <div class="space-y-6">
                    <div class="flex justify-between items-start">
                        <div class="flex flex-col">
                            <span class="text-[14px] font-bold uppercase">Sidang: Andi Wijaya</span>
                            <span class="text-[11px] opacity-80 uppercase font-medium">Ruang Rapat 1</span>
                        </div>
                        <span class="text-[14px] font-bold whitespace-nowrap">: 12/05</span>
                    </div>

                    <div class="flex justify-between items-start opacity-60">
                        <div class="flex flex-col">
                            <span class="text-[14px] font-bold uppercase">Sidang: Maria Ulfa</span>
                            <span class="text-[11px] uppercase font-medium">Online Zoom</span>
                        </div>
                        <span class="text-[14px] font-bold whitespace-nowrap">: 15/05</span>
                    </div>
                </div>

                <div class="mt-8 p-4 bg-[#CDA057] rounded-lg text-white shadow-inner border border-white/20">
                    <p class="text-[11px] font-bold uppercase mb-1 leading-tight">Berita Acara</p>
                    <p class="text-[10px] opacity-90 leading-tight mb-3">Tanda tangani berita acara yang telah selesai disidangkan.</p>
                    <a href="#" class="text-[10px] font-black underline uppercase hover:text-black transition-colors">Kelola Dokumen</a>
                </div>
            </div>
        </div>
    </div>
</x-dashboard-layout>