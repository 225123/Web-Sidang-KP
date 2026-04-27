<x-dashboard-layout header="Berita Acara" userName="{{ auth()->user()->name }}" roleName="KOORDINATOR KP">
    <x-slot:sidebar>
        @include('koordinator.components.sidebar', ['active' => 'berita-acara'])
    </x-slot>

    <style>
        .custom-scrollbar::-webkit-scrollbar { width: 6px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
        
        /* Donut Chart CSS */
        .donut-chart {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: conic-gradient(#3b82f6 0% {{ $percentage }}%, #d1d5db {{ $percentage }}% 100%);
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .donut-chart::before {
            content: "";
            width: 50px;
            height: 50px;
            background-color: #E6E6E6; /* Matches card background */
            border-radius: 50%;
        }

        .sentence-case { text-transform: lowercase; }
        .sentence-case::first-letter { text-transform: uppercase; }
        [x-cloak] { display: none !important; }

        /* Editable inputs in PDF */
        .pdf-input {
            border: none;
            border-bottom: 1px dashed #666;
            background: transparent;
            font-size: inherit;
            font-family: inherit;
            padding: 0 2px;
            outline: none;
            width: 100%;
        }
        .pdf-input:focus {
            border-bottom: 1px solid #000;
            background: rgba(59, 130, 246, 0.05);
        }

        .box-search { padding: 6px 10px 6px 30px; font-size: 11px; border-radius: 4px; border: 1px solid rgba(0,0,0,0.1); width: 100%; margin-bottom: 10px; outline: none; transition: all 0.2s; }
        .box-search:focus { border-color: #4285F4; box-shadow: 0 0 0 2px rgba(66, 133, 244, 0.1); }
    </style>

    <script>
        window.beritaAcaraManager = function() {
            return {
                sudahSidang: @json(array_values($sudahSidang)),
                belumSidang: @json(array_values($belumSidang)),
                searchTelah: '',
                searchBelum: '',
                openIdTelah: null,
                openIdBelum: null,
                
                get filteredTelah() {
                    const q = this.searchTelah.toLowerCase();
                    return this.sudahSidang.filter(m => m.name.toLowerCase().includes(q) || m.nim.toLowerCase().includes(q));
                },
                
                get filteredBelum() {
                    const q = this.searchBelum.toLowerCase();
                    return this.belumSidang.filter(m => m.name.toLowerCase().includes(q) || m.nim.toLowerCase().includes(q));
                }
            };
        };
    </script>

    <div class="w-full flex-1 pb-10 px-4 lg:px-6" x-data="beritaAcaraManager()">
        <div class="flex flex-col lg:flex-row gap-8">
            
            <!-- KIRI: Progress & Template PDF (lg:w-[40%]) -->
            <div class="w-full lg:w-[45%] flex flex-col gap-6">
                
                <!-- PROGRES SIDANG BOX -->
                <div class="bg-[#E6E6E6] rounded-[5px] p-5 flex flex-col h-auto border border-[#CAC0C0] shadow-sm">
                    <div class="flex justify-between items-center border-b border-gray-300 pb-2 mb-4">
                        <h3 class="font-bold text-[16px] text-black uppercase tracking-tight">Progres Sidang</h3>
                    </div>
                    
                    <div class="flex items-center gap-6 mb-5">
                        <div class="donut-chart flex-shrink-0 shadow-inner"></div>
                        <div>
                            <div class="flex items-baseline gap-2 mb-1">
                                <span class="text-[28px] font-bold text-black leading-none">{{ $percentage }}%</span>
                            </div>
                            <span class="text-[13px] font-medium text-gray-700 leading-tight block">Mahasiswa Telah Melaksanakan Sidang</span>
                        </div>
                    </div>

                    <div class="flex items-center gap-12 mt-4 pt-5 border-t border-gray-300">
                        <div>
                            <div class="text-[28px] font-extrabold text-[#34A853] leading-none mb-1">{{ $totalSudah }}</div>
                            <div class="text-[11px] font-bold text-gray-600 uppercase tracking-wider">Sudah Sidang</div>
                        </div>
                        <div class="border-l border-gray-300 h-10"></div>
                        <div>
                            <div class="text-[28px] font-extrabold text-[#EA4335] leading-none mb-1">{{ $totalBelum }}</div>
                            <div class="text-[11px] font-bold text-gray-600 uppercase tracking-wider">Belum Sidang</div>
                        </div>
                    </div>
                </div>

                <!-- TEMPLATE PDF BOX -->
                <div class="bg-[#E6E6E6] rounded-[5px] p-5 flex flex-col h-[700px] border border-[#CAC0C0] shadow-sm flex-1">
                    <div class="flex justify-between items-center border-b border-gray-300 pb-2 mb-4">
                        <h3 class="font-bold text-[16px] text-black uppercase tracking-tight">Review Template Surat Berita Acara</h3>
                    </div>
                    
                    <iframe src="{{ route('koordinator.berita-acara.preview-pdf') }}" class="w-full h-full flex-1 rounded border border-gray-400 shadow-md bg-white"></iframe>
                </div>

            </div>

            <!-- KANAN: Daftar Mahasiswa & Submit (lg:w-[55%]) -->
            <div class="w-full lg:w-[55%] flex flex-col gap-6">
                
                <!-- DAFTAR MAHASISWA TELAH SIDANG -->
                <div class="bg-[#E6E6E6] rounded-[5px] p-5 flex flex-col h-auto border border-[#CAC0C0] shadow-sm">
                    <div class="flex justify-between items-center border-b border-gray-300 pb-2 mb-3">
                        <h3 class="font-bold text-[16px] text-black uppercase tracking-tight">Daftar Mahasiswa Telah Sidang</h3>
                        <div class="font-bold text-[12px] text-black">
                            <span x-text="filteredTelah.length"></span> / <span>{{ $totalSidang }}</span>
                        </div>
                    </div>

                    <!-- Search Box -->
                    <div class="relative">
                        <span class="absolute left-3 top-2 text-gray-400"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg></span>
                        <input type="text" x-model="searchTelah" placeholder="Cari NIM atau Nama..." class="box-search">
                    </div>
                    
                    <div class="text-[12px] font-medium text-black pr-2 max-h-[300px] overflow-y-auto custom-scrollbar">
                        <template x-for="(mhs, index) in filteredTelah" :key="mhs.id">
                            <div class="mb-2">
                                <div @click="openIdTelah = (openIdTelah === mhs.id ? null : mhs.id)" 
                                     class="flex items-center justify-between border-b border-gray-300 py-3 hover:bg-gray-200 cursor-pointer transition-colors group px-2 rounded">
                                    <div class="flex items-center gap-3 truncate">
                                        <span class="font-bold text-gray-400 w-5 flex-shrink-0" x-text="(index + 1) + '.'"></span>
                                        <div class="truncate">
                                            <div class="font-normal text-black sentence-case" x-text="mhs.nim"></div>
                                            <div class="font-normal text-gray-700 truncate sentence-case" x-text="mhs.name"></div>
                                        </div>
                                    </div>
                                    <div class="flex-shrink-0 ml-4 group-hover:translate-x-1 transition-transform duration-200" :class="openIdTelah === mhs.id ? 'rotate-90 !translate-x-0' : ''">
                                        <svg class="w-5 h-5 text-black" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"></path></svg>
                                    </div>
                                </div>
                                <div x-show="openIdTelah === mhs.id" x-cloak x-transition class="bg-white border border-gray-200 rounded p-3 mt-1 shadow-sm text-[11px] grid grid-cols-1 md:grid-cols-2 gap-3 mx-2">
                                    <div>
                                        <div class="text-gray-500 font-semibold mb-1 uppercase">Jadwal Sidang</div>
                                        <div class="font-bold text-black" x-text="mhs.jadwal"></div>
                                        <div class="text-gray-600 uppercase mt-0.5" x-text="'Ruang: ' + mhs.ruang"></div>
                                    </div>
                                    <div>
                                        <div class="text-gray-500 font-semibold mb-1 uppercase">Dosen Penguji</div>
                                        <div class="font-bold text-black sentence-case mb-0.5" x-text="'1. ' + mhs.p1"></div>
                                        <div class="font-bold text-black sentence-case" x-text="'2. ' + mhs.p2"></div>
                                    </div>
                                </div>
                            </div>
                        </template>
                        <template x-if="filteredTelah.length === 0">
                            <div class="text-center py-10 text-gray-500 italic font-medium">Tidak ada mahasiswa yang ditemukan.</div>
                        </template>
                    </div>
                </div>

                <!-- DAFTAR MAHASISWA BELUM SIDANG -->
                <div class="bg-[#E6E6E6] rounded-[5px] p-5 flex flex-col h-auto border border-[#CAC0C0] shadow-sm">
                    <div class="flex justify-between items-center border-b border-gray-300 pb-2 mb-3">
                        <h3 class="font-bold text-[16px] text-black uppercase tracking-tight">Daftar Mahasiswa Belum Sidang</h3>
                        <div class="font-bold text-[12px] text-black">
                            <span x-text="filteredBelum.length"></span> / <span>{{ $totalSidang }}</span>
                        </div>
                    </div>
                    
                    <!-- Search Box -->
                    <div class="relative">
                        <span class="absolute left-3 top-2 text-gray-400"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg></span>
                        <input type="text" x-model="searchBelum" placeholder="Cari NIM atau Nama..." class="box-search">
                    </div>

                    <div class="text-[12px] font-medium text-black pr-2 max-h-[300px] overflow-y-auto custom-scrollbar">
                        <template x-for="(mhs, index) in filteredBelum" :key="mhs.id">
                            <div class="mb-2">
                                <div @click="openIdBelum = (openIdBelum === mhs.id ? null : mhs.id)" 
                                     class="flex items-center justify-between border-b border-gray-300 py-3 hover:bg-gray-200 cursor-pointer transition-colors group px-2 rounded">
                                    <div class="flex items-center gap-3 truncate">
                                        <span class="font-bold text-gray-400 w-5 flex-shrink-0" x-text="(index + 1) + '.'"></span>
                                        <div class="truncate">
                                            <div class="font-normal text-black sentence-case" x-text="mhs.nim"></div>
                                            <div class="font-normal text-gray-700 truncate sentence-case" x-text="mhs.name"></div>
                                        </div>
                                    </div>
                                    <div class="flex-shrink-0 ml-4 group-hover:translate-x-1 transition-transform duration-200" :class="openIdBelum === mhs.id ? 'rotate-90 !translate-x-0' : ''">
                                        <svg class="w-5 h-5 text-black" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"></path></svg>
                                    </div>
                                </div>
                                <div x-show="openIdBelum === mhs.id" x-cloak x-transition class="bg-white border border-gray-200 rounded p-3 mt-1 shadow-sm text-[11px] grid grid-cols-1 md:grid-cols-2 gap-3 mx-2">
                                    <div>
                                        <div class="text-gray-500 font-semibold mb-1 uppercase">Jadwal Sidang</div>
                                        <div class="font-bold text-black" x-text="mhs.jadwal"></div>
                                        <div class="text-gray-600 uppercase mt-0.5" x-text="'Ruang: ' + mhs.ruang"></div>
                                    </div>
                                    <div>
                                        <div class="text-gray-500 font-semibold mb-1 uppercase">Dosen Penguji</div>
                                        <div class="font-bold text-black sentence-case mb-0.5" x-text="'1. ' + mhs.p1"></div>
                                        <div class="font-bold text-black sentence-case" x-text="'2. ' + mhs.p2"></div>
                                    </div>
                                </div>
                            </div>
                        </template>
                        <template x-if="filteredBelum.length === 0">
                            <div class="text-center py-10 text-gray-500 italic font-medium">Tidak ada mahasiswa yang ditemukan.</div>
                        </template>
                    </div>
                </div>

                <!-- SUBMIT BUTTON -->
                <div class="mt-4 flex flex-col items-center">
                    <p class="text-[13px] font-medium text-gray-600 mb-4 text-center">
                        Pastikan seluruh mahasiswa telah sidang sebelum mengirimkan Berita Acara ke Kaprodi.
                    </p>
                    <button class="bg-[#34A853] hover:bg-green-700 text-white font-bold py-3 px-12 rounded-[25px] flex items-center gap-3 transition-all shadow-md text-[14px]">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path></svg>
                        <span>SUBMIT BERITA ACARA</span>
                    </button>
                </div>

            </div>
        </div>
    </div>
</x-dashboard-layout>
