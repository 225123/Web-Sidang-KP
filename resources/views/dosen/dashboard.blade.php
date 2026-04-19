<x-dashboard-layout header="DASHBOARD" userName="{{ auth()->user()->name }}" roleName="DOSEN">
    <x-slot:sidebar>
        @include('dosen.components.sidebar', ['active' => 'dashboard'])
        </x-slot>

        <x-slot:headerActions>
            <div x-data="{ open: false, selected: 'Genap 2025/2026' }" class="relative w-full md:w-[212px] mt-2 md:mt-0">
                <button @click="open = !open" @click.outside="open = false" type="button"
                    class="w-full flex items-center justify-between border border-[#CAC0C0] bg-[#FBFBFB] rounded-[5px] shadow-sm text-[13px] font-medium py-1.5 px-3 focus:outline-none focus:border-[#4285F4] focus:ring-1 focus:ring-[#4285F4] cursor-pointer text-black h-[32px]">

                    <span x-text="selected"></span>

                    <svg :class="open ? 'rotate-0' : 'rotate-90'"
                        class="w-3.5 h-3.5 text-gray-500 transition-transform duration-200 flex-shrink-0" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </button>

                <div x-show="open" x-transition style="display: none;"
                    class="absolute z-50 w-full mt-1 bg-[#FBFBFB] border border-[#CAC0C0] rounded-[5px] shadow-lg overflow-hidden">
                    <ul class="py-1 text-[13px] font-medium text-black">
                        <li>
                            <button @click="selected = 'Genap 2025/2026'; open = false" type="button"
                                class="block w-full text-left px-3 py-2 hover:bg-[#E8E5E5] transition-colors cursor-pointer">
                                Genap 2025/2026
                            </button>
                        </li>
                        <li>
                            <button @click="selected = 'Ganjil 2025/2026'; open = false" type="button"
                                class="block w-full text-left px-3 py-2 hover:bg-[#E8E5E5] transition-colors cursor-pointer">
                                Ganjil 2025/2026
                            </button>
                        </li>
                    </ul>
                </div>
                <input type="hidden" name="periode" :value="selected">
            </div>
        </x-slot:headerActions>

        <div class="flex flex-col gap-6 mt-4 w-full">
            <div class="flex flex-wrap gap-4 items-center mb-2 w-full">
                <div class="flex flex-wrap gap-4 w-full xl:w-auto">
                    <div
                        class="w-full sm:w-[calc(50%-0.5rem)] xl:w-[188px] h-[71px] bg-[#3B82F6] rounded-[5px] relative overflow-hidden flex flex-col justify-center items-center shadow-sm">
                        <svg class="w-5 h-5 absolute left-3 top-3 text-white" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                        <span class="text-[#E8F5E9] text-[28px] font-bold font-inter leading-none">3</span>
                        <span class="text-[#E8F5E9] text-[12px] font-medium font-inter mt-1">Mahasiswa Bimbingan</span>
                    </div>

                    <div
                        class="w-full sm:w-[calc(50%-0.5rem)] xl:w-[188px] h-[71px] bg-[#E57835] rounded-[5px] relative overflow-hidden flex flex-col justify-center items-center shadow-sm">
                        <svg class="w-5 h-5 absolute left-3 top-3 text-white" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span class="text-[#E8F5E9] text-[28px] font-bold font-inter leading-none">2</span>
                        <span class="text-[#E8F5E9] text-[12px] font-medium font-inter mt-1">Belum Sidang</span>
                    </div>

                    <div
                        class="w-full sm:w-[calc(50%-0.5rem)] xl:w-[188px] h-[71px] bg-[#4CAF50] rounded-[5px] relative overflow-hidden flex flex-col justify-center items-center shadow-sm">
                        <svg class="w-5 h-5 absolute left-3 top-3 text-white" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span class="text-[#E8F5E9] text-[28px] font-bold font-inter leading-none">1</span>
                        <span class="text-[#E8F5E9] text-[12px] font-medium font-inter mt-1">Telah Sidang</span>
                    </div>

                    <div
                        class="w-full sm:w-[calc(50%-0.5rem)] xl:w-[188px] h-[71px] bg-[#F4B400] rounded-[5px] relative overflow-hidden flex flex-col justify-center items-center shadow-sm">
                        <svg class="w-5 h-5 absolute left-3 top-3 text-black" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        <span class="text-black text-[28px] font-bold font-inter leading-none">10</span>
                        <span class="text-black text-[12px] font-medium font-inter mt-1">Sidang Terjadwal</span>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 lg:gap-8">
                <div class="lg:col-span-2 space-y-6">
                    <!-- Statistik Bimbingan Mingguan -->
                    <div
                        class="bg-[#FEFEFF] rounded-[10px] border border-[#D9D9D9] p-6 shadow-sm overflow-hidden flex flex-col h-[302px]">
                        <div class="flex justify-between items-start mb-6">
                            <h3 class="font-semibold text-[#1A1A1A] text-[18px] font-inter">Statistik Bimbingan Mingguan
                            </h3>
                            <div class="bg-[#EDEBEB] border border-[#CAC0C0] rounded-[5px] px-3 py-1">
                                <span class="text-[12px] text-[#1A1A1A] font-medium">Minggu Ini</span>
                            </div>
                        </div>
                        <div class="flex-1 relative border-l border-b border-[#B5A6A6] mt-4 ml-8 pb-4">
                            <div class="absolute -left-10 bottom-[0%] text-[#B5A6A6] text-[14px] font-semibold">0</div>
                            <div class="absolute -left-10 bottom-[33%] text-[#B5A6A6] text-[14px] font-semibold">3</div>
                            <div class="absolute -left-10 bottom-[66%] text-[#B5A6A6] text-[14px] font-semibold">6</div>
                            <div class="absolute -left-10 bottom-[100%] text-[#B5A6A6] text-[14px] font-semibold">9
                            </div>

                            <div class="absolute left-0 bottom-[33%] w-full border-t border-[#B5A6A6] opacity-50"></div>
                            <div class="absolute left-0 bottom-[66%] w-full border-t border-[#B5A6A6] opacity-50"></div>
                            <div class="absolute left-0 bottom-[100%] w-full border-t border-[#B5A6A6] opacity-50">
                            </div>

                            <div class="w-full h-full flex justify-around items-end relative bottom-0">
                                <div
                                    class="w-8 bg-[#3B82F6] h-[40%] rounded-t-sm shadow-sm transition-all duration-300 hover:opacity-80 cursor-pointer">
                                </div>
                                <div
                                    class="w-8 bg-[#CDA057] h-[75%] rounded-t-sm shadow-sm transition-all duration-300 hover:opacity-80 cursor-pointer">
                                </div>
                                <div
                                    class="w-8 bg-[#3B82F6] h-[60%] rounded-t-sm shadow-sm transition-all duration-300 hover:opacity-80 cursor-pointer">
                                </div>
                                <div
                                    class="w-8 bg-[#CDA057] h-[90%] rounded-t-sm shadow-sm transition-all duration-300 hover:opacity-80 cursor-pointer">
                                </div>
                            </div>
                        </div>
                        <div
                            class="flex justify-around items-center w-[calc(100%-2rem)] ml-8 mt-2 text-[#B5A6A6] text-[14px] font-semibold">
                            <span>Sen</span><span>Sel</span><span>Rab</span><span>Kam</span>
                        </div>
                    </div>

                    <!-- Persetujuan Menunggu -->
                    <div
                        class="bg-[#FEFEFF] rounded-[10px] border border-[#D9D9D9] p-6 shadow-sm overflow-hidden min-h-[220px]">
                        <div class="mb-4">
                            <h3 class="font-semibold text-[#1A1A1A] text-[18px] font-inter">Persetujuan Menunggu</h3>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="w-full min-w-[500px] text-left">
                                <thead class="bg-[#F9F9F9] text-[13px] text-gray-500 font-bold border-b border-[#D9D9D9]">
                                <tr>
                                    <th class="px-4 py-2 w-[40px]">No</th>
                                    <th class="px-4 py-2">Mahasiswa</th>
                                    <th class="px-4 py-2 text-center">Jenis Berkas</th>
                                    <th class="px-4 py-2 text-center w-[100px]">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-[#D9D9D9] text-[13px] font-medium text-black">
                                <tr class="hover:bg-gray-50 transition-colors h-[48px]">
                                    <td class="px-4 py-2">1</td>
                                    <td class="px-4 py-2">Geovano Yansen Jas</td>
                                    <td class="px-4 py-2 text-center">
                                        <span
                                            class="px-3 py-1 bg-[#FFF9C4] text-[#827717] rounded-[5px] text-[11px] font-bold border border-[#FBC02D]">Log
                                            Bimbingan</span>
                                    </td>
                                    <td class="px-4 py-2 text-center">
                                        <button
                                            class="text-white bg-[#4285F4] hover:bg-blue-600 px-3 py-1 font-bold text-[11px] rounded-[5px] shadow-sm transition-colors">Review</button>
                                    </td>
                                </tr>
                                <tr class="hover:bg-gray-50 transition-colors h-[48px]">
                                    <td class="px-4 py-2">2</td>
                                    <td class="px-4 py-2">Mahasiswa 2</td>
                                    <td class="px-4 py-2 text-center">
                                        <span
                                            class="px-3 py-1 bg-[#C8E6C9] text-[#1B5E20] rounded-[5px] text-[11px] font-bold border border-[#4CAF50]">Draft
                                            Laporan</span>
                                    </td>
                                    <td class="px-4 py-2 text-center">
                                        <button
                                            class="text-white bg-[#4285F4] hover:bg-blue-600 px-3 py-1 font-bold text-[11px] rounded-[5px] shadow-sm transition-colors">Review</button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        </div>
                    </div>
                </div>

                <div class="flex flex-col gap-6 h-full">
                    <!-- Jadwal Sidang Terdekat -->
                    <div class="bg-[#FEFEFF] rounded-[10px] border border-[#D9D9D9] p-6 shadow-sm flex flex-col flex-1">
                        <h3 class="font-semibold text-[#1A1A1A] text-[18px] mb-4 font-inter">Jadwal Sidang Terdekat</h3>

                        <div class="flex flex-col gap-4">
                            <!-- Item 1 -->
                            <div class="flex flex-col border-l-4 border-[#3B82F6] pl-3 py-1 bg-gray-50/50 rounded-r-md">
                                <span class="text-[11px] font-bold text-gray-500 mb-0.5 font-inter">12 Mei 2026 • 09:00
                                    - 11:00</span>
                                <span class="text-[14px] font-bold text-[#1A1A1A] font-inter">Andi Wijaya</span>
                                <div class="flex justify-between items-end mt-1 font-inter">
                                    <span class="text-[11px] text-[#666666] font-medium">412020001</span>
                                    <span
                                        class="text-[10px] font-bold px-2 py-0.5 bg-[#E8F5E9] text-[#1B5E20] rounded border border-[#4CAF50]">Ruang
                                        Rapat 1</span>
                                </div>
                            </div>

                            <!-- Item 2 -->
                            <div
                                class="flex flex-col border-l-4 border-[#E57835] pl-3 py-1 bg-gray-50/50 rounded-r-md opacity-70">
                                <span class="text-[11px] font-bold text-gray-500 mb-0.5 font-inter">15 Mei 2026 • 13:00
                                    - 15:00</span>
                                <span class="text-[14px] font-bold text-[#1A1A1A] font-inter">Maria Ulfa</span>
                                <div class="flex justify-between items-end mt-1 font-inter">
                                    <span class="text-[11px] text-[#666666] font-medium">412020002</span>
                                    <span
                                        class="text-[10px] font-bold px-2 py-0.5 bg-[#FFF9C4] text-[#827717] rounded border border-[#FBC02D]">Online
                                        Zoom</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Berita Acara Tertunda -->
                    <div class="p-5 bg-[#FFFFFF] rounded-[10px] shadow-sm border border-[#D9D9D9]">
                        <div class="flex items-start gap-3">
                            <div class="p-2 bg-[#FFF9C4] rounded-lg border border-[#FBC02D]">
                                <svg class="w-5 h-5 text-[#827717]" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                    </path>
                                </svg>
                            </div>
                            <div class="flex-1">
                                <p class="text-[14px] font-bold text-[#1A1A1A] mb-1 leading-tight font-inter">Berita
                                    Acara Tertunda</p>
                                <p class="text-[11px] text-[#666666] leading-snug mb-2 font-inter">Tanda tangani 2
                                    berita acara yang telah selesai disidangkan.</p>
                                <a href="#"
                                    class="text-[11px] font-bold text-[#4285F4] hover:text-blue-700 hover:underline transition-colors font-inter">Kelola
                                    Dokumen &rarr;</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
</x-dashboard-layout>