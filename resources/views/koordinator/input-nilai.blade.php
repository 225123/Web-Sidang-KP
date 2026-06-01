<x-dashboard-layout header="Input Nilai" userName="{{ auth()->user()->name }}" roleName="KOORDINATOR KP">
    <x-slot:sidebar>
        @include('koordinator.components.sidebar', ['active' => 'input-nilai'])
    </x-slot>

    <div class="mt-8 px-2 w-full max-w-[1200px] mx-auto" x-data="inputNilaiPage()">
        
        <!-- Global Info -->
        <div class="bg-[#E6F0FA] border border-[#D0E3F5] rounded-[10px] p-4 lg:p-5 mb-6 flex items-start gap-4 shadow-sm">
            <div class="w-6 h-6 rounded-full bg-[#4285F4] text-white flex items-center justify-center font-bold flex-shrink-0 mt-0.5">
                i
            </div>
            <p class="text-[14px] text-[#1A1A1A] font-medium leading-relaxed m-0 mt-0.5">
                Lakukan penginputan nilai berdasarkan peran anda terhadap KP Mahasiswa.
            </p>
        </div>

        <!-- Tabs -->
        <div class="flex items-end h-[36px]">
            <button @click="activeTab = 'penguji'" 
               :class="activeTab === 'penguji' ? 'bg-white border border-gray-200 border-b-white h-[36px] z-10 font-bold text-black' : 'bg-gray-100 border border-gray-200 text-gray-500 h-[34px] hover:bg-gray-50 border-b-gray-200'"
               class="px-5 text-[12px] rounded-t-[10px] relative flex items-center justify-center transition-all gap-2">
               Penguji Sidang
               <span class="bg-[#4285F4]/10 text-[#4285F4] py-0.5 px-2 rounded-full text-[10px]" x-text="pengujiMenunggu"></span>
            </button>
            <button @click="activeTab = 'pembimbing'" 
               :class="activeTab === 'pembimbing' ? 'bg-white border border-gray-200 border-b-white h-[36px] z-10 font-bold text-black' : 'bg-gray-100 border border-gray-200 text-gray-500 h-[34px] hover:bg-gray-50 border-b-gray-200'"
               class="px-5 text-[12px] rounded-t-[10px] relative left-[-1px] flex items-center justify-center transition-all gap-2">
               Pembimbing KP
               <span class="bg-[#4285F4]/10 text-[#4285F4] py-0.5 px-2 rounded-full text-[10px]" x-text="pembimbingMenunggu"></span>
            </button>
            <button @click="activeTab = 'supervisor'" 
               :class="activeTab === 'supervisor' ? 'bg-white border border-gray-200 border-b-white h-[36px] z-10 font-bold text-black' : 'bg-gray-100 border border-gray-200 text-gray-500 h-[34px] hover:bg-gray-50 border-b-gray-200'"
               class="px-5 text-[12px] rounded-t-[10px] relative left-[-2px] flex items-center justify-center transition-all gap-2">
               Supervisor Lapangan
               <span class="bg-[#4285F4]/10 text-[#4285F4] py-0.5 px-2 rounded-full text-[10px]" x-text="supervisorMenunggu"></span>
            </button>
        </div>

        <div class="bg-white rounded-b-[15px] rounded-tr-[15px] border border-gray-200 shadow-sm overflow-hidden relative top-[-1px] mb-8">
            <!-- SECTION 1: TABEL INPUT NILAI PENGUJI -->
            <div x-show="activeTab === 'penguji'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" class="p-6" x-cloak>
            <div class="mb-6 flex flex-col sm:flex-row justify-between items-start gap-4">
                <div>
                    <h3 class="text-[18px] font-bold text-black tracking-tight uppercase">TABEL INPUT NILAI PENGUJI</h3>
                    <p class="text-[12px] text-black/60 font-medium mt-1">Manajemen penilaian mahasiswa bimbingan dan penguji sidang KP.</p>
                </div>
                <div class="flex gap-2 shrink-0">
                    <div class="bg-[#4285F4] text-white rounded-[5px] px-3 py-1.5 flex items-center gap-2 shadow-sm border border-blue-600/20">
                        <span class="text-[16px] font-bold leading-none" x-text="pengujiTotal"></span>
                        <span class="text-[11px] font-medium uppercase tracking-wider">Total</span>
                    </div>
                    <div class="bg-[#FBBC05] text-black rounded-[5px] px-3 py-1.5 flex items-center gap-2 shadow-sm border border-yellow-500/20">
                        <span class="text-[16px] font-bold leading-none" x-text="pengujiMenunggu"></span>
                        <span class="text-[11px] font-medium uppercase tracking-wider">Menunggu</span>
                    </div>
                    <div class="bg-[#34A853] text-white rounded-[5px] px-3 py-1.5 flex items-center gap-2 shadow-sm border border-green-600/20">
                        <span class="text-[16px] font-bold leading-none" x-text="pengujiDinilai"></span>
                        <span class="text-[11px] font-medium uppercase tracking-wider">Dinilai</span>
                    </div>
                </div>
            </div>

            <div class="flex flex-col sm:flex-row justify-between items-center gap-6 mb-6">
                <div class="flex flex-wrap items-center gap-4 w-full sm:w-auto">
                    <!-- Search -->
                    <div class="relative flex-1 sm:w-[300px]">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        </div>
                        <input type="text" x-model="searchPenguji" class="block w-full pl-9 pr-4 py-2 border border-gray-300 rounded-[5px] text-[12px] text-black focus:ring-[#4285F4]" placeholder="Cari Nama/NIM, atau Judul KP...">
                    </div>

                    <!-- Filter Pelaksanaan -->
                    <div x-data="{ openFilter: false }" class="relative w-full sm:w-[150px] z-[60]">
                        <button type="button" @click="openFilter = !openFilter" @click.outside="openFilter = false" class="w-full text-[12px] border border-gray-300 rounded-[5px] py-2 px-3 bg-white text-black font-medium focus:ring-[#4285F4] flex justify-between items-center text-left shadow-sm">
                            <span x-text="filterPelaksanaanPenguji === 'all' ? 'Pelaksanaan' : filterPelaksanaanPenguji"></span>
                            <svg :class="openFilter ? 'rotate-0' : 'rotate-90'" class="w-3.5 h-3.5 transition-all duration-200 text-gray-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <div x-show="openFilter" x-transition x-cloak class="absolute w-full mt-1 bg-white border border-gray-300 rounded-[5px] shadow-lg overflow-hidden py-1 z-50">
                            <label class="block px-3 py-2 text-[12px] hover:bg-gray-100 cursor-pointer text-black"><input type="radio" value="all" x-model="filterPelaksanaanPenguji" class="hidden" @change="openFilter = false">Semua</label>
                            <label class="block px-3 py-2 text-[12px] hover:bg-gray-100 cursor-pointer text-black"><input type="radio" value="Selesai" x-model="filterPelaksanaanPenguji" class="hidden" @change="openFilter = false">Selesai</label>
                            <label class="block px-3 py-2 text-[12px] hover:bg-gray-100 cursor-pointer text-black"><input type="radio" value="Berjalan" x-model="filterPelaksanaanPenguji" class="hidden" @change="openFilter = false">Berjalan</label>
                            <label class="block px-3 py-2 text-[12px] hover:bg-gray-100 cursor-pointer text-black"><input type="radio" value="Menunggu" x-model="filterPelaksanaanPenguji" class="hidden" @change="openFilter = false">Menunggu</label>
                            <label class="block px-3 py-2 text-[12px] hover:bg-gray-100 cursor-pointer text-black"><input type="radio" value="Dibatalkan" x-model="filterPelaksanaanPenguji" class="hidden" @change="openFilter = false">Dibatalkan</label>
                        </div>
                    </div>

                    <!-- Filter Penilaian -->
                    <div x-data="{ openFilter: false }" class="relative w-full sm:w-[150px] z-[50]">
                        <button type="button" @click="openFilter = !openFilter" @click.outside="openFilter = false" class="w-full text-[12px] border border-gray-300 rounded-[5px] py-2 px-3 bg-white text-black font-medium focus:ring-[#4285F4] flex justify-between items-center text-left shadow-sm">
                            <span x-text="filterPenilaianPenguji === 'all' ? 'Penilaian' : (filterPenilaianPenguji === 'sudah' ? 'Sudah Input' : 'Belum Input')"></span>
                            <svg :class="openFilter ? 'rotate-0' : 'rotate-90'" class="w-3.5 h-3.5 transition-all duration-200 text-gray-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <div x-show="openFilter" x-transition x-cloak class="absolute w-full mt-1 bg-white border border-gray-300 rounded-[5px] shadow-lg overflow-hidden py-1 z-50">
                            <label class="block px-3 py-2 text-[12px] hover:bg-gray-100 cursor-pointer text-black"><input type="radio" value="all" x-model="filterPenilaianPenguji" class="hidden" @change="openFilter = false">Semua</label>
                            <label class="block px-3 py-2 text-[12px] hover:bg-gray-100 cursor-pointer text-black"><input type="radio" value="sudah" x-model="filterPenilaianPenguji" class="hidden" @change="openFilter = false">Sudah Input</label>
                            <label class="block px-3 py-2 text-[12px] hover:bg-gray-100 cursor-pointer text-black"><input type="radio" value="belum" x-model="filterPenilaianPenguji" class="hidden" @change="openFilter = false">Belum Input</label>
                        </div>
                    </div>

                    <!-- Filter Peran -->
                    <div x-data="{ openFilter: false }" class="relative w-full sm:w-[150px] z-[40]">
                        <button type="button" @click="openFilter = !openFilter" @click.outside="openFilter = false" class="w-full text-[12px] border border-gray-300 rounded-[5px] py-2 px-3 bg-white text-black font-medium focus:ring-[#4285F4] flex justify-between items-center text-left shadow-sm">
                            <span x-text="filterPeranPenguji === 'all' ? 'Peran' : (filterPeranPenguji === 'PENGUJI 1' ? 'Penguji 1' : 'Penguji 2')"></span>
                            <svg :class="openFilter ? 'rotate-0' : 'rotate-90'" class="w-3.5 h-3.5 transition-all duration-200 text-gray-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <div x-show="openFilter" x-transition x-cloak class="absolute w-full mt-1 bg-white border border-gray-300 rounded-[5px] shadow-lg overflow-hidden py-1 z-50">
                            <label class="block px-3 py-2 text-[12px] hover:bg-gray-100 cursor-pointer text-black"><input type="radio" value="all" x-model="filterPeranPenguji" class="hidden" @change="openFilter = false">Semua Peran</label>
                            <label class="block px-3 py-2 text-[12px] hover:bg-gray-100 cursor-pointer text-black"><input type="radio" value="PENGUJI 1" x-model="filterPeranPenguji" class="hidden" @change="openFilter = false">Penguji 1</label>
                            <label class="block px-3 py-2 text-[12px] hover:bg-gray-100 cursor-pointer text-black"><input type="radio" value="PENGUJI 2" x-model="filterPeranPenguji" class="hidden" @change="openFilter = false">Penguji 2</label>
                        </div>
                    </div>

                    <div class="flex gap-2 w-full sm:w-auto">
                        <button type="button" @click="clearPenguji()" class="flex-1 sm:flex-none border border-[#EA4335] bg-[#EA4335] text-white hover:bg-red-600 transition-colors px-4 py-1.5 rounded-[5px] text-[12px] font-bold shadow-sm flex items-center justify-center">
                            Clear Filter
                        </button>
                    </div>
                </div>
            </div>

            <div class="border border-gray-200 rounded-[10px] overflow-x-auto">
                <table class="w-full border-collapse text-[12px] min-w-[1100px]">
                    <thead class="bg-[#EBEBEB] text-black">
                            <tr>
                                <th class="py-3 px-4 font-bold text-center w-[60px] border-b border-r border-gray-300">No</th>
                                <th class="py-3 px-4 font-bold text-left border-b border-r border-gray-300 w-[180px]">Jadwal</th>
                                <th class="py-3 px-4 font-bold text-left border-b border-r border-gray-300 w-[150px]">Peran Sidang</th>
                                <th class="py-3 px-4 font-bold text-left border-b border-r border-gray-300 w-[180px]">Mahasiswa</th>
                                <th class="py-3 px-4 font-bold text-left border-b border-r border-gray-300 min-w-[300px]">Judul KP</th>
                                <th class="py-3 px-4 font-bold text-center border-b border-r border-gray-300 w-[120px]">Status Kelulusan</th>
                                <th class="py-3 px-4 font-bold text-center border-b border-r border-gray-300 w-[140px]">Pelaksanaan</th>
                                <th class="py-3 px-4 font-bold text-center border-b border-gray-300 w-[150px]">Penilaian</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 bg-white">
                            <template x-for="(sidang, index) in paginatedPenguji" :key="'p-' + sidang.id">
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="py-3 px-4 text-center text-black/60 border-r border-gray-200" x-text="((currentPagePenguji - 1) * itemsPerPagePenguji) + index + 1"></td>
                                    <td class="py-3 px-4 text-left border-r border-gray-200">
                                        <div class="font-bold text-black uppercase" x-text="formatDate(sidang.tanggal_sidang)"></div>
                                        <div class="text-gray-600 mt-1" x-text="formatTime(sidang.waktu_mulai_sidang) + ' - ' + formatTime(sidang.waktu_selesai_sidang) + ' WIB'"></div>
                                        <div class="text-gray-400 italic mt-1" x-text="sidang.ruang_sidang || '-'"></div>
                                    </td>
                                    <td class="py-3 px-4 text-left border-r border-gray-200">
                                        <div class="flex flex-col gap-1">
                                            <template x-for="role in getSpecificRoles(sidang, ['PENGUJI 1', 'PENGUJI 2'])">
                                                <span class="text-[10px] font-bold bg-[#F0F0F0] text-gray-600 px-2 py-0.5 rounded-[3px] border border-gray-200" x-text="role"></span>
                                            </template>
                                        </div>
                                    </td>
                                    <td class="py-3 px-4 text-left border-r border-gray-200">
                                        <div class="font-bold text-black uppercase" x-text="sidang.mahasiswa.user.name"></div>
                                        <div class="text-gray-500 font-mono text-[11px]" x-text="sidang.mahasiswa.nim"></div>
                                    </td>
                                    <td class="py-3 px-4 text-left border-r border-gray-200">
                                        <p class="sentence-case leading-snug line-clamp-2 text-black font-normal" x-text="sidang.pendaftaran_kp.judul_kp" :title="sidang.pendaftaran_kp.judul_kp"></p>
                                    </td>
                                    <td class="border-r border-[#CAC0C0] px-4 py-4 text-center">
                                        <span class="font-bold text-gray-700" x-text="sidang.status_kelulusan || '-'"></span>
                                    </td>
                                    <td class="py-3 px-4 text-center border-r border-gray-200">
                                        <div class="flex flex-col items-center gap-2">
                                             <template x-if="sidang.is_penguji_1">
                                                 <div x-data="{ openExec: false }" class="relative w-full min-w-[115px]" @click.outside="openExec = false">
                                                     <button type="button" @click="openExec = !openExec"
                                                         :class="getStatusClass(sidang)"
                                                         @if(isset($isReadOnly) && $isReadOnly) disabled @endif
                                                         class="w-full text-[10px] font-bold px-3 py-1.5 rounded-[20px] shadow-sm cursor-pointer focus:outline-none transition-all border flex items-center justify-between gap-1 disabled:opacity-50 disabled:cursor-not-allowed">
                                                         <span class="truncate flex-1 text-center" x-text="getExecutionStatus(sidang)"></span>
                                                         @if(!isset($isReadOnly) || !$isReadOnly)
                                                         <svg :class="openExec ? 'rotate-0' : 'rotate-90'" class="w-3.5 h-3.5 transition-all duration-200 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                                                         @endif
                                                     </button>
                                                     @if(!isset($isReadOnly) || !$isReadOnly)
                                                     <div x-show="openExec" x-transition x-cloak class="absolute left-0 right-0 bottom-full mb-1 bg-white border border-gray-300 rounded-[5px] shadow-lg overflow-hidden py-1 z-[100] min-w-[115px]">
                                                         <button type="button" @click="confirmUpdateStatus(sidang.id, 'Selesai'); openExec = false" class="block w-full text-left px-3 py-1.5 text-[11px] font-medium hover:bg-gray-100 text-black">Selesai</button>
                                                         <button type="button" @click="confirmUpdateStatus(sidang.id, 'Dibatalkan'); openExec = false" class="block w-full text-left px-3 py-1.5 text-[11px] font-medium hover:bg-gray-100 text-black">Dibatalkan</button>
                                                     </div>
                                                     @endif
                                                 </div>
                                             </template>
                                            
                                            <template x-if="!sidang.is_penguji_1">
                                                <div class="flex flex-col items-center gap-1">
                                                    <div class="text-[10px] font-bold px-3 py-1.5 rounded-[20px] shadow-sm flex items-center justify-center gap-1.5 min-w-[115px] border"
                                                        :class="getStatusClass(sidang)">
                                                        <div class="w-1.5 h-1.5 rounded-full" :class="getStatusDotClass(sidang)"></div>
                                                        <span x-text="getExecutionStatus(sidang)"></span>
                                                    </div>
                                                    <div class="text-[9px] text-gray-400 italic font-medium text-center">Otoritas Penguji 1</div>
                                                </div>
                                            </template>
                                        </div>
                                    </td>
                                    <td class="py-3 px-4 text-center">
                                        <div class="flex flex-col gap-2">
                                            <template x-for="role in getSpecificRoles(sidang, ['PENGUJI 1', 'PENGUJI 2'])">
                                                 @if(!isset($isReadOnly) || !$isReadOnly)
                                                 <a :href="'{{ url('koordinator/input-nilai') }}/' + sidang.id + '/' + role.toLowerCase().replace(' ', '')"
                                                     class="w-full text-center bg-[#4CC098] hover:bg-[#3da681] text-white py-1.5 rounded-[4px] text-[10px] font-bold transition-all shadow-sm flex items-center justify-center gap-1">
                                                     INPUT <span x-text="role"></span>
                                                 </a>
                                                 @else
                                                 <span class="w-full text-center bg-gray-300 text-white py-1.5 rounded-[4px] text-[10px] font-bold shadow-sm flex items-center justify-center gap-1 cursor-not-allowed">
                                                     READ ONLY
                                                 </span>
                                                 @endif
                                             </template>
                                        </div>
                                    </td>
                                </tr>
                            </template>
                            <template x-if="filteredPenguji.length === 0">
                                <tr>
                                    <td colspan="8" class="py-12 text-center text-gray-500 italic text-[12px]">
                                        Tidak ada data penguji yang sesuai pencarian/filter.
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
            </div>

            <!-- Pagination for Penguji -->
            <div class="px-6 py-4 bg-white flex items-center justify-between border-t border-gray-200" x-show="totalPagesPenguji > 1">
                <span class="text-[12px] font-medium text-black/50" x-text="(filteredPenguji.length === 0 ? 0 : ((currentPagePenguji - 1) * itemsPerPagePenguji + 1)) + ' - ' + Math.min(currentPagePenguji * itemsPerPagePenguji, filteredPenguji.length) + ' dari ' + filteredPenguji.length + ' baris'"></span>
                <div class="flex items-center gap-2">
                    <button @click="if(currentPagePenguji > 1) currentPagePenguji--" :disabled="currentPagePenguji === 1" class="px-3 py-1 border border-gray-300 rounded text-[12px] hover:bg-gray-50 disabled:opacity-30 disabled:cursor-not-allowed transition-colors">Previous</button>
                    <div class="flex items-center gap-1">
                        <template x-for="p in totalPagesPenguji" :key="p">
                            <button @click="currentPagePenguji = p" class="w-8 h-8 rounded text-[12px] font-bold transition-all" :class="currentPagePenguji === p ? 'bg-blue-600 text-white shadow-md' : 'text-black hover:bg-gray-100'" x-text="p"></button>
                        </template>
                    </div>
                    <button @click="if(currentPagePenguji < totalPagesPenguji) currentPagePenguji++" :disabled="currentPagePenguji === totalPagesPenguji" class="px-3 py-1 border border-gray-300 rounded text-[12px] hover:bg-gray-50 disabled:opacity-30 disabled:cursor-not-allowed transition-colors">Next</button>
                </div>
            </div>
        </div>

        <!-- SECTION 2: TABEL INPUT NILAI PEMBIMBING -->
        <div x-show="activeTab === 'pembimbing'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" class="p-6" x-cloak>
            <div class="mb-6 flex flex-col sm:flex-row justify-between items-start gap-4">
                <div>
                    <h3 class="text-[18px] font-bold text-black tracking-tight uppercase">TABEL INPUT NILAI PEMBIMBING</h3>
                    <p class="text-[12px] text-black/60 font-medium mt-1">Manajemen penilaian mahasiswa khusus untuk peran Anda sebagai Dosen Pembimbing.</p>
                </div>
                <div class="flex gap-2 shrink-0">
                    <div class="bg-[#4285F4] text-white rounded-[5px] px-3 py-1.5 flex items-center gap-2 shadow-sm border border-blue-600/20">
                        <span class="text-[16px] font-bold leading-none" x-text="pembimbingTotal"></span>
                        <span class="text-[11px] font-medium uppercase tracking-wider">Total</span>
                    </div>
                    <div class="bg-[#FBBC05] text-black rounded-[5px] px-3 py-1.5 flex items-center gap-2 shadow-sm border border-yellow-500/20">
                        <span class="text-[16px] font-bold leading-none" x-text="pembimbingMenunggu"></span>
                        <span class="text-[11px] font-medium uppercase tracking-wider">Menunggu</span>
                    </div>
                    <div class="bg-[#34A853] text-white rounded-[5px] px-3 py-1.5 flex items-center gap-2 shadow-sm border border-green-600/20">
                        <span class="text-[16px] font-bold leading-none" x-text="pembimbingDinilai"></span>
                        <span class="text-[11px] font-medium uppercase tracking-wider">Dinilai</span>
                    </div>
                </div>
            </div>

            <div class="flex flex-col sm:flex-row justify-between items-center gap-6 mb-6">
                <div class="flex flex-wrap items-center gap-4 w-full sm:w-auto">
                    <!-- Search -->
                    <div class="relative flex-1 sm:w-[300px]">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        </div>
                        <input type="text" x-model="searchPembimbing" class="block w-full pl-9 pr-4 py-2 border border-gray-300 rounded-[5px] text-[12px] text-black focus:ring-[#4285F4]" placeholder="Cari Nama/NIM, atau Judul KP...">
                    </div>

                    <!-- Filter Penilaian -->
                    <div x-data="{ openFilter: false }" class="relative w-full sm:w-[150px] z-[50]">
                        <button type="button" @click="openFilter = !openFilter" @click.outside="openFilter = false" class="w-full text-[12px] border border-gray-300 rounded-[5px] py-2 px-3 bg-white text-black font-medium focus:ring-[#4285F4] flex justify-between items-center text-left shadow-sm">
                            <span x-text="filterPenilaianPembimbing === 'all' ? 'Penilaian' : (filterPenilaianPembimbing === 'sudah' ? 'Sudah Input' : 'Belum Input')"></span>
                            <svg :class="openFilter ? 'rotate-0' : 'rotate-90'" class="w-3.5 h-3.5 transition-all duration-200 text-gray-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <div x-show="openFilter" x-transition x-cloak class="absolute w-full mt-1 bg-white border border-gray-300 rounded-[5px] shadow-lg overflow-hidden py-1 z-50">
                            <label class="block px-3 py-2 text-[12px] hover:bg-gray-100 cursor-pointer text-black"><input type="radio" value="all" x-model="filterPenilaianPembimbing" class="hidden" @change="openFilter = false">Semua</label>
                            <label class="block px-3 py-2 text-[12px] hover:bg-gray-100 cursor-pointer text-black"><input type="radio" value="sudah" x-model="filterPenilaianPembimbing" class="hidden" @change="openFilter = false">Sudah Input</label>
                            <label class="block px-3 py-2 text-[12px] hover:bg-gray-100 cursor-pointer text-black"><input type="radio" value="belum" x-model="filterPenilaianPembimbing" class="hidden" @change="openFilter = false">Belum Input</label>
                        </div>
                    </div>

                    <div class="flex gap-2 w-full sm:w-auto">
                        <button type="button" @click="clearPembimbing()" class="flex-1 sm:flex-none border border-[#EA4335] bg-[#EA4335] text-white hover:bg-red-600 transition-colors px-4 py-1.5 rounded-[5px] text-[12px] font-bold shadow-sm flex items-center justify-center">
                            Clear Filter
                        </button>
                    </div>
                </div>
            </div>

            <div class="border border-gray-200 rounded-[10px] overflow-x-auto">
                <table class="w-full border-collapse text-[12px] min-w-[1100px]">
                    <thead class="bg-[#EBEBEB] text-black">
                            <tr>
                                <th class="py-3 px-4 font-bold text-center w-[60px] border-b border-r border-gray-300">No</th>
                                <th class="py-3 px-4 font-bold text-left border-b border-r border-gray-300 w-[250px]">Mahasiswa</th>
                                <th class="py-3 px-4 font-bold text-left border-b border-r border-gray-300 min-w-[300px]">Judul KP</th>
                                <th class="py-3 px-4 font-bold text-center border-b border-r border-gray-300 w-[150px]">Status</th>
                                <th class="py-3 px-4 font-bold text-center border-b border-gray-300 w-[200px]">Penilaian</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 bg-white">
                            <template x-for="(sidang, index) in paginatedPembimbing" :key="'pb-' + sidang.id">
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="py-3 px-4 text-center text-black/60 border-r border-gray-200" x-text="((currentPagePembimbing - 1) * itemsPerPagePembimbing) + index + 1"></td>
                                    <td class="py-3 px-4 text-left border-r border-gray-200">
                                        <div class="font-bold text-black uppercase" x-text="sidang.mahasiswa.user.name"></div>
                                        <div class="text-gray-500 font-mono text-[11px]" x-text="sidang.mahasiswa.nim"></div>
                                    </td>
                                    <td class="py-3 px-4 text-left border-r border-gray-200">
                                        <p class="sentence-case leading-snug line-clamp-2 text-black font-normal" x-text="sidang.pendaftaran_kp.judul_kp" :title="sidang.pendaftaran_kp.judul_kp"></p>
                                    </td>
                                    <td class="border-r border-[#CAC0C0] px-4 py-4 text-center">
                                        <template x-if="sidang.nilai_pembimbing !== null">
                                            <span class="inline-flex items-center gap-1.5 bg-green-100 text-green-700 px-3 py-1 rounded-full font-bold text-[10px] uppercase shadow-sm">
                                                Dinilai
                                            </span>
                                        </template>
                                        <template x-if="sidang.nilai_pembimbing === null">
                                            <span class="inline-flex items-center gap-1.5 bg-yellow-100 text-yellow-700 px-3 py-1 rounded-full font-bold text-[10px] uppercase shadow-sm">
                                                Menunggu
                                            </span>
                                        </template>
                                    </td>
                                    <td class="py-3 px-4 text-center">
                                        @if(!isset($isReadOnly) || !$isReadOnly)
                                        <a :href="'{{ url('koordinator/input-nilai') }}/' + sidang.id + '/pembimbing'"
                                            class="w-full text-center bg-[#4CC098] hover:bg-[#3da681] text-white py-2 rounded-[4px] text-[11px] font-bold transition-all shadow-sm flex items-center justify-center gap-1 uppercase">
                                            Input Nilai Pembimbing
                                        </a>
                                        @else
                                        <span class="w-full text-center bg-gray-300 text-white py-2 rounded-[4px] text-[11px] font-bold shadow-sm flex items-center justify-center gap-1 uppercase cursor-not-allowed">
                                            Read Only
                                        </span>
                                        @endif
                                    </td>
                                </tr>
                            </template>
                            <template x-if="filteredPembimbing.length === 0">
                                <tr>
                                    <td colspan="5" class="py-12 text-center text-gray-500 italic text-[12px]">
                                        Tidak ada data pembimbing yang sesuai pencarian/filter.
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
            </div>

            <!-- Pagination for Pembimbing -->
            <div class="px-6 py-4 bg-white flex items-center justify-between border-t border-gray-200" x-show="totalPagesPembimbing > 1">
                <span class="text-[12px] font-medium text-black/50" x-text="(filteredPembimbing.length === 0 ? 0 : ((currentPagePembimbing - 1) * itemsPerPagePembimbing + 1)) + ' - ' + Math.min(currentPagePembimbing * itemsPerPagePembimbing, filteredPembimbing.length) + ' dari ' + filteredPembimbing.length + ' baris'"></span>
                <div class="flex items-center gap-2">
                    <button @click="if(currentPagePembimbing > 1) currentPagePembimbing--" :disabled="currentPagePembimbing === 1" class="px-3 py-1 border border-gray-300 rounded text-[12px] hover:bg-gray-50 disabled:opacity-30 disabled:cursor-not-allowed transition-colors">Previous</button>
                    <div class="flex items-center gap-1">
                        <template x-for="p in totalPagesPembimbing" :key="p">
                            <button @click="currentPagePembimbing = p" class="w-8 h-8 rounded text-[12px] font-bold transition-all" :class="currentPagePembimbing === p ? 'bg-blue-600 text-white shadow-md' : 'text-black hover:bg-gray-100'" x-text="p"></button>
                        </template>
                    </div>
                    <button @click="if(currentPagePembimbing < totalPagesPembimbing) currentPagePembimbing++" :disabled="currentPagePembimbing === totalPagesPembimbing" class="px-3 py-1 border border-gray-300 rounded text-[12px] hover:bg-gray-50 disabled:opacity-30 disabled:cursor-not-allowed transition-colors">Next</button>
                </div>
            </div>
        </div>

        <!-- SECTION 3: TABEL INPUT NILAI SUPERVISOR -->
        <div x-show="activeTab === 'supervisor'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" class="p-6" x-cloak>
            <div class="mb-6 flex flex-col sm:flex-row justify-between items-start gap-4">
                <div>
                    <h3 class="text-[18px] font-bold text-black tracking-tight uppercase">TABEL INPUT NILAI SUPERVISOR</h3>
                    <p class="text-[12px] text-black/60 font-medium mt-1">Manajemen penilaian mahasiswa untuk peran Anda sebagai Supervisor (Pembimbing Lapangan).</p>
                </div>
                <div class="flex gap-2 shrink-0">
                    <div class="bg-[#4285F4] text-white rounded-[5px] px-3 py-1.5 flex items-center gap-2 shadow-sm border border-blue-600/20">
                        <span class="text-[16px] font-bold leading-none" x-text="supervisorTotal"></span>
                        <span class="text-[11px] font-medium uppercase tracking-wider">Total</span>
                    </div>
                    <div class="bg-[#FBBC05] text-black rounded-[5px] px-3 py-1.5 flex items-center gap-2 shadow-sm border border-yellow-500/20">
                        <span class="text-[16px] font-bold leading-none" x-text="supervisorMenunggu"></span>
                        <span class="text-[11px] font-medium uppercase tracking-wider">Menunggu</span>
                    </div>
                    <div class="bg-[#34A853] text-white rounded-[5px] px-3 py-1.5 flex items-center gap-2 shadow-sm border border-green-600/20">
                        <span class="text-[16px] font-bold leading-none" x-text="supervisorDinilai"></span>
                        <span class="text-[11px] font-medium uppercase tracking-wider">Dinilai</span>
                    </div>
                </div>
            </div>

            <div class="flex flex-col sm:flex-row justify-between items-center gap-6 mb-6">
                <div class="flex flex-wrap items-center gap-4 w-full sm:w-auto">
                    <!-- Search -->
                    <div class="relative flex-1 sm:w-[300px]">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        </div>
                        <input type="text" x-model="searchSupervisor" class="block w-full pl-9 pr-4 py-2 border border-gray-300 rounded-[5px] text-[12px] text-black focus:ring-[#4285F4]" placeholder="Cari Nama/NIM, atau Judul KP...">
                    </div>

                    <!-- Filter Penilaian -->
                    <div x-data="{ openFilter: false }" class="relative w-full sm:w-[150px] z-[50]">
                        <button type="button" @click="openFilter = !openFilter" @click.outside="openFilter = false" class="w-full text-[12px] border border-gray-300 rounded-[5px] py-2 px-3 bg-white text-black font-medium focus:ring-[#4285F4] flex justify-between items-center text-left shadow-sm">
                            <span x-text="filterPenilaianSupervisor === 'all' ? 'Penilaian' : (filterPenilaianSupervisor === 'sudah' ? 'Sudah Input' : 'Belum Input')"></span>
                            <svg :class="openFilter ? 'rotate-0' : 'rotate-90'" class="w-3.5 h-3.5 transition-all duration-200 text-gray-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <div x-show="openFilter" x-transition x-cloak class="absolute w-full mt-1 bg-white border border-gray-300 rounded-[5px] shadow-lg overflow-hidden py-1 z-50">
                            <label class="block px-3 py-2 text-[12px] hover:bg-gray-100 cursor-pointer text-black"><input type="radio" value="all" x-model="filterPenilaianSupervisor" class="hidden" @change="openFilter = false">Semua</label>
                            <label class="block px-3 py-2 text-[12px] hover:bg-gray-100 cursor-pointer text-black"><input type="radio" value="sudah" x-model="filterPenilaianSupervisor" class="hidden" @change="openFilter = false">Sudah Input</label>
                            <label class="block px-3 py-2 text-[12px] hover:bg-gray-100 cursor-pointer text-black"><input type="radio" value="belum" x-model="filterPenilaianSupervisor" class="hidden" @change="openFilter = false">Belum Input</label>
                        </div>
                    </div>

                    <div class="flex gap-2 w-full sm:w-auto">
                        <button type="button" @click="clearSupervisor()" class="flex-1 sm:flex-none border border-[#EA4335] bg-[#EA4335] text-white hover:bg-red-600 transition-colors px-4 py-1.5 rounded-[5px] text-[12px] font-bold shadow-sm flex items-center justify-center">
                            Clear Filter
                        </button>
                    </div>
                </div>
            </div>

            <div class="border border-gray-200 rounded-[10px] overflow-x-auto">
                <table class="w-full border-collapse text-[12px] min-w-[1100px]">
                    <thead class="bg-[#EBEBEB] text-black">
                            <tr>
                                <th class="py-3 px-4 font-bold text-center w-[60px] border-b border-r border-gray-300">No</th>
                                <th class="py-3 px-4 font-bold text-left border-b border-r border-gray-300 w-[250px]">Mahasiswa</th>
                                <th class="py-3 px-4 font-bold text-left border-b border-r border-gray-300 min-w-[300px]">Judul KP</th>
                                <th class="py-3 px-4 font-bold text-center border-b border-r border-gray-300 w-[150px]">Status</th>
                                <th class="py-3 px-4 font-bold text-center border-b border-gray-300 w-[200px]">Penilaian</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 bg-white">
                            <template x-for="(sidang, index) in paginatedSupervisor" :key="'sv-' + sidang.id">
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="py-3 px-4 text-center text-black/60 border-r border-gray-200" x-text="((currentPageSupervisor - 1) * itemsPerPageSupervisor) + index + 1"></td>
                                    <td class="py-3 px-4 text-left border-r border-gray-200">
                                        <div class="font-bold text-black uppercase" x-text="sidang.mahasiswa.user.name"></div>
                                        <div class="text-gray-500 font-mono text-[11px]" x-text="sidang.mahasiswa.nim"></div>
                                    </td>
                                    <td class="py-3 px-4 text-left border-r border-gray-200">
                                        <p class="sentence-case leading-snug line-clamp-2 text-black font-normal" x-text="sidang.pendaftaran_kp.judul_kp" :title="sidang.pendaftaran_kp.judul_kp"></p>
                                    </td>
                                    <td class="border-r border-[#CAC0C0] px-4 py-4 text-center">
                                        <template x-if="sidang.nilai_supervisor !== null">
                                            <span class="inline-flex items-center gap-1.5 bg-green-100 text-green-700 px-3 py-1 rounded-full font-bold text-[10px] uppercase shadow-sm">
                                                Dinilai
                                            </span>
                                        </template>
                                        <template x-if="sidang.nilai_supervisor === null">
                                            <span class="inline-flex items-center gap-1.5 bg-yellow-100 text-yellow-700 px-3 py-1 rounded-full font-bold text-[10px] uppercase shadow-sm">
                                                Menunggu
                                            </span>
                                        </template>
                                    </td>
                                    <td class="py-3 px-4 text-center">
                                        @if(!isset($isReadOnly) || !$isReadOnly)
                                        <a :href="'{{ url('koordinator/input-nilai') }}/' + sidang.id + '/supervisior'"
                                            class="w-full text-center bg-[#4CC098] hover:bg-[#3da681] text-white py-2 rounded-[4px] text-[11px] font-bold transition-all shadow-sm flex items-center justify-center gap-1 uppercase">
                                            Input Nilai Supervisior
                                        </a>
                                        @else
                                        <span class="w-full text-center bg-gray-300 text-white py-2 rounded-[4px] text-[11px] font-bold shadow-sm flex items-center justify-center gap-1 uppercase cursor-not-allowed">
                                            Read Only
                                        </span>
                                        @endif
                                    </td>
                                </tr>
                            </template>
                            <template x-if="filteredSupervisor.length === 0">
                                <tr>
                                    <td colspan="5" class="py-12 text-center text-gray-500 italic text-[12px]">
                                        Tidak ada data supervisior yang sesuai pencarian/filter.
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
            </div>

            <!-- Pagination for Supervisor -->
            <div class="px-6 py-4 bg-white flex items-center justify-between border-t border-gray-200" x-show="totalPagesSupervisor > 1">
                <span class="text-[12px] font-medium text-black/50" x-text="(filteredSupervisor.length === 0 ? 0 : ((currentPageSupervisor - 1) * itemsPerPageSupervisor + 1)) + ' - ' + Math.min(currentPageSupervisor * itemsPerPageSupervisor, filteredSupervisor.length) + ' dari ' + filteredSupervisor.length + ' baris'"></span>
                <div class="flex items-center gap-2">
                    <button @click="if(currentPageSupervisor > 1) currentPageSupervisor--" :disabled="currentPageSupervisor === 1" class="px-3 py-1 border border-gray-300 rounded text-[12px] hover:bg-gray-50 disabled:opacity-30 disabled:cursor-not-allowed transition-colors">Previous</button>
                    <div class="flex items-center gap-1">
                        <template x-for="p in totalPagesSupervisor" :key="p">
                            <button @click="currentPageSupervisor = p" class="w-8 h-8 rounded text-[12px] font-bold transition-all" :class="currentPageSupervisor === p ? 'bg-blue-600 text-white shadow-md' : 'text-black hover:bg-gray-100'" x-text="p"></button>
                        </template>
                    </div>
                    <button @click="if(currentPageSupervisor < totalPagesSupervisor) currentPageSupervisor++" :disabled="currentPageSupervisor === totalPagesSupervisor" class="px-3 py-1 border border-gray-300 rounded text-[12px] hover:bg-gray-50 disabled:opacity-30 disabled:cursor-not-allowed transition-colors">Next</button>
                </div>
            </div>
        </div>
        
        </div>

        <!-- Custom Global Confirm Modal -->
        <div x-show="showConfirmModal" style="display: none;" class="fixed inset-0 z-[110] flex items-center justify-center bg-black/40 backdrop-blur-sm p-4">
            <div @click.away="cancelUpdate()" class="bg-white rounded-[10px] w-full max-w-[450px] p-8 shadow-2xl flex flex-col items-center justify-center text-center transform transition-all">
                
                <!-- Icon -->
                <div class="mb-5">
                    <svg class="w-16 h-16 text-[#4CAF50]" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                    </svg>
                </div>

                <!-- Message -->
                <h3 class="text-black font-semibold text-[16px] mb-8">Apakah Anda yakin ingin memperbarui status pelaksanaan menjadi <span class="font-bold text-blue-600" x-text="confirmData ? confirmData.newStatus : ''"></span>?</h3>

                <!-- Buttons -->
                <div class="flex gap-4 w-full justify-center">
                    <button @click="cancelUpdate()" type="button" class="w-[100px] h-[34px] bg-[#E32727] hover:bg-red-700 text-white rounded-[5px] text-[14px] font-medium transition-colors shadow-sm">
                        Batal
                    </button>
                    <button @click="executeUpdate()" type="button" class="w-[100px] h-[34px] bg-[#456DA7] hover:bg-blue-700 text-white rounded-[5px] text-[14px] font-medium transition-colors shadow-sm">
                        Iya
                    </button>
                </div>
            </div>
        </div>

        <!-- Success Modal -->
        <div x-show="showSuccessModal" style="display: none;" class="fixed inset-0 z-[110] flex items-center justify-center bg-black/40 backdrop-blur-sm p-4">
            <div class="bg-white rounded-[10px] w-full max-w-[400px] p-6 shadow-2xl flex flex-col items-center text-center">
                <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center mb-4">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                </div>
                <h3 class="text-[16px] font-bold text-gray-900 mb-2">Berhasil!</h3>
                <p class="text-[14px] text-gray-500" x-text="successMessage"></p>
                <button @click="showSuccessModal = false" class="mt-6 w-full h-[36px] bg-green-500 hover:bg-green-600 text-white rounded-[5px] text-[13px] font-bold transition-colors">Tutup</button>
            </div>
        </div>

        <!-- Error Modal -->
        <div x-show="showErrorModal" style="display: none;" class="fixed inset-0 z-[110] flex items-center justify-center bg-black/40 backdrop-blur-sm p-4">
            <div class="bg-white rounded-[10px] w-full max-w-[400px] p-6 shadow-2xl flex flex-col items-center text-center">
                <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center mb-4">
                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </div>
                <h3 class="text-[16px] font-bold text-gray-900 mb-2">Gagal</h3>
                <p class="text-[14px] text-gray-500" x-text="errorMessage"></p>
                <button @click="showErrorModal = false" class="mt-6 w-full h-[36px] bg-red-500 hover:bg-red-600 text-white rounded-[5px] text-[13px] font-bold transition-colors">Tutup</button>
            </div>
        </div>
    </div>

    <script>
        function inputNilaiPage() {
            return {
                activeTab: 'penguji',
                sidangs: @json($sidangs),
                now: new Date(),
                
                searchPenguji: '',
                filterPelaksanaanPenguji: 'all',
                filterPenilaianPenguji: 'all',
                filterPeranPenguji: 'all',
                sortPenguji: 'date_near',

                searchPembimbing: '',
                filterPenilaianPembimbing: 'all',
                sortPembimbing: 'date_near',

                searchSupervisor: '',
                filterPenilaianSupervisor: 'all',
                sortSupervisor: 'date_near',

                clearPenguji() {
                    this.searchPenguji = '';
                    this.filterPelaksanaanPenguji = 'all';
                    this.filterPenilaianPenguji = 'all';
                    this.filterPeranPenguji = 'all';
                    this.sortPenguji = 'date_near';
                    this.currentPagePenguji = 1;
                },
                clearPembimbing() {
                    this.searchPembimbing = '';
                    this.filterPenilaianPembimbing = 'all';
                    this.sortPembimbing = 'date_near';
                    this.currentPagePembimbing = 1;
                },
                clearSupervisor() {
                    this.searchSupervisor = '';
                    this.filterPenilaianSupervisor = 'all';
                    this.sortSupervisor = 'date_near';
                    this.currentPageSupervisor = 1;
                },

                showConfirmModal: false,
                confirmData: null,
                showErrorModal: false,
                errorMessage: '',
                showSuccessModal: false,
                successMessage: '',

                currentPagePenguji: 1, itemsPerPagePenguji: 10,
                currentPagePembimbing: 1, itemsPerPagePembimbing: 10,
                currentPageSupervisor: 1, itemsPerPageSupervisor: 10,

                init() {
                    setInterval(() => { this.now = new Date(); }, 60000);
                    
                    this.$watch('searchPenguji', () => this.currentPagePenguji = 1);
                    this.$watch('filterPelaksanaanPenguji', () => this.currentPagePenguji = 1);
                    this.$watch('filterPenilaianPenguji', () => this.currentPagePenguji = 1);
                    this.$watch('filterPeranPenguji', () => this.currentPagePenguji = 1);
                    this.$watch('sortPenguji', () => this.currentPagePenguji = 1);

                    this.$watch('searchPembimbing', () => this.currentPagePembimbing = 1);
                    this.$watch('filterPenilaianPembimbing', () => this.currentPagePembimbing = 1);
                    this.$watch('sortPembimbing', () => this.currentPagePembimbing = 1);

                    this.$watch('searchSupervisor', () => this.currentPageSupervisor = 1);
                    this.$watch('filterPenilaianSupervisor', () => this.currentPageSupervisor = 1);
                    this.$watch('sortSupervisor', () => this.currentPageSupervisor = 1);
                },

                // ---------------- ACTIONS ----------------
                clearPenguji() {
                    this.searchPenguji = '';
                    this.filterPelaksanaanPenguji = 'all';
                    this.filterPenilaianPenguji = 'all';
                    this.filterPeranPenguji = 'all';
                    this.sortPenguji = 'date_near';
                    this.currentPagePenguji = 1;
                },

                clearPembimbing() {
                    this.searchPembimbing = '';
                    this.filterPenilaianPembimbing = 'all';
                    this.sortPembimbing = 'date_near';
                    this.currentPagePembimbing = 1;
                },

                clearSupervisor() {
                    this.searchSupervisor = '';
                    this.filterPenilaianSupervisor = 'all';
                    this.sortSupervisor = 'date_near';
                    this.currentPageSupervisor = 1;
                },

                // ---------------- STATS ----------------
                get basePenguji() {
                    return this.sidangs.filter(s => s.user_roles.includes('PENGUJI 1') || s.user_roles.includes('PENGUJI 2'));
                },
                get pengujiTotal() { return this.basePenguji.length; },
                get pengujiDinilai() {
                    return this.basePenguji.filter(s => {
                        let ok = true;
                        if(s.user_roles.includes('PENGUJI 1') && s.nilai_penguji_1 === null) ok = false;
                        if(s.user_roles.includes('PENGUJI 2') && s.nilai_penguji_2 === null) ok = false;
                        return ok;
                    }).length;
                },
                get pengujiMenunggu() { return this.pengujiTotal - this.pengujiDinilai; },

                get basePembimbing() { return this.sidangs.filter(s => s.user_roles.includes('PEMBIMBING')); },
                get pembimbingTotal() { return this.basePembimbing.length; },
                get pembimbingDinilai() { return this.basePembimbing.filter(s => s.nilai_pembimbing !== null).length; },
                get pembimbingMenunggu() { return this.pembimbingTotal - this.pembimbingDinilai; },

                get baseSupervisor() { return this.sidangs.filter(s => s.user_roles.includes('SUPERVISIOR')); },
                get supervisorTotal() { return this.baseSupervisor.length; },
                get supervisorDinilai() { return this.baseSupervisor.filter(s => s.nilai_supervisor !== null).length; },
                get supervisorMenunggu() { return this.supervisorTotal - this.supervisorDinilai; },

                // ---------------- FILTERED ARRAYS ----------------
                get filteredPenguji() {
                    let res = [...this.basePenguji];
                    if (this.searchPenguji) {
                        const q = this.searchPenguji.toLowerCase();
                        res = res.filter(s => s.mahasiswa.user.name.toLowerCase().includes(q) || s.mahasiswa.nim.includes(q) || (s.pendaftaran_kp.judul_kp && s.pendaftaran_kp.judul_kp.toLowerCase().includes(q)));
                    }
                    if (this.filterPelaksanaanPenguji !== 'all') {
                        res = res.filter(s => this.getExecutionStatus(s) === this.filterPelaksanaanPenguji);
                    }
                    if (this.filterPenilaianPenguji !== 'all') {
                        res = res.filter(s => {
                            let ok = true;
                            if(s.user_roles.includes('PENGUJI 1') && s.nilai_penguji_1 === null) ok = false;
                            if(s.user_roles.includes('PENGUJI 2') && s.nilai_penguji_2 === null) ok = false;
                            return this.filterPenilaianPenguji === 'sudah' ? ok : !ok;
                        });
                    }
                    if (this.filterPeranPenguji !== 'all') {
                        res = res.filter(s => s.user_roles.includes(this.filterPeranPenguji));
                    }
                    res.sort((a, b) => {
                        return this.sortPenguji === 'date_near' ? new Date(a.tanggal_sidang) - new Date(b.tanggal_sidang) : new Date(b.tanggal_sidang) - new Date(a.tanggal_sidang);
                    });
                    return res;
                },
                get paginatedPenguji() {
                    const start = (this.currentPagePenguji - 1) * this.itemsPerPagePenguji;
                    return this.filteredPenguji.slice(start, start + this.itemsPerPagePenguji);
                },
                get totalPagesPenguji() {
                    return Math.ceil(this.filteredPenguji.length / this.itemsPerPagePenguji);
                },

                get filteredPembimbing() {
                    let res = [...this.basePembimbing];
                    if (this.searchPembimbing) {
                        const q = this.searchPembimbing.toLowerCase();
                        res = res.filter(s => s.mahasiswa.user.name.toLowerCase().includes(q) || s.mahasiswa.nim.includes(q) || (s.pendaftaran_kp.judul_kp && s.pendaftaran_kp.judul_kp.toLowerCase().includes(q)));
                    }
                    if (this.filterPenilaianPembimbing !== 'all') {
                        res = res.filter(s => this.filterPenilaianPembimbing === 'sudah' ? s.nilai_pembimbing !== null : s.nilai_pembimbing === null);
                    }
                    res.sort((a, b) => {
                        return this.sortPembimbing === 'date_near' ? new Date(a.tanggal_sidang) - new Date(b.tanggal_sidang) : new Date(b.tanggal_sidang) - new Date(a.tanggal_sidang);
                    });
                    return res;
                },
                get paginatedPembimbing() {
                    const start = (this.currentPagePembimbing - 1) * this.itemsPerPagePembimbing;
                    return this.filteredPembimbing.slice(start, start + this.itemsPerPagePembimbing);
                },
                get totalPagesPembimbing() {
                    return Math.ceil(this.filteredPembimbing.length / this.itemsPerPagePembimbing);
                },

                get filteredSupervisor() {
                    let res = [...this.baseSupervisor];
                    if (this.searchSupervisor) {
                        const q = this.searchSupervisor.toLowerCase();
                        res = res.filter(s => s.mahasiswa.user.name.toLowerCase().includes(q) || s.mahasiswa.nim.includes(q) || (s.pendaftaran_kp.judul_kp && s.pendaftaran_kp.judul_kp.toLowerCase().includes(q)));
                    }
                    if (this.filterPenilaianSupervisor !== 'all') {
                        res = res.filter(s => this.filterPenilaianSupervisor === 'sudah' ? s.nilai_supervisor !== null : s.nilai_supervisor === null);
                    }
                    res.sort((a, b) => {
                        return this.sortSupervisor === 'date_near' ? new Date(a.tanggal_sidang) - new Date(b.tanggal_sidang) : new Date(b.tanggal_sidang) - new Date(a.tanggal_sidang);
                    });
                    return res;
                },
                get paginatedSupervisor() {
                    const start = (this.currentPageSupervisor - 1) * this.itemsPerPageSupervisor;
                    return this.filteredSupervisor.slice(start, start + this.itemsPerPageSupervisor);
                },
                get totalPagesSupervisor() {
                    return Math.ceil(this.filteredSupervisor.length / this.itemsPerPageSupervisor);
                },

                // ---------------- HELPERS ----------------
                getSpecificRoles(sidang, rolesToMatch) {
                    return sidang.user_roles.filter(r => rolesToMatch.includes(r));
                },

                getExecutionStatus(s) {
                    if (s.pelaksanaan === 'Selesai') return 'Selesai';
                    if (s.pelaksanaan === 'Dibatalkan') return 'Dibatalkan';
                    const start = new Date(`${s.tanggal_sidang}T${s.waktu_mulai_sidang}`);
                    const end = new Date(`${s.tanggal_sidang}T${s.waktu_selesai_sidang}`);
                    if (this.now < start) return 'Menunggu';
                    if (this.now >= start && this.now <= end) return 'Berjalan';
                    return s.pelaksanaan;
                },

                getStatusClass(s) {
                    const status = this.getExecutionStatus(s);
                    if (status === 'Menunggu') return 'bg-[#F9F9F9] text-gray-500 border border-gray-300';
                    if (status === 'Berjalan') return 'bg-[#DEF1FF] text-[#1D4ED8] border border-[#BFDBFE]';
                    if (status === 'Selesai') return 'bg-[#A1DFAC] text-[#1D5E2D] border border-[#BBF7D0]';
                    if (status === 'Dibatalkan') return 'bg-[#FFD3D3] text-[#B91C1C] border border-[#FECACA]';
                    return '';
                },

                getStatusDotClass(s) {
                    const status = this.getExecutionStatus(s);
                    if (status === 'Menunggu') return 'bg-gray-400';
                    if (status === 'Berjalan') return 'bg-[#1D4ED8]';
                    if (status === 'Selesai') return 'bg-[#1D5E2D]';
                    if (status === 'Dibatalkan') return 'bg-[#B91C1C]';
                    return '';
                },

                getStatusTextClass(s) {
                    const status = this.getExecutionStatus(s);
                    if (status === 'Menunggu') return 'text-gray-500';
                    if (status === 'Berjalan') return 'text-[#1D4ED8]';
                    if (status === 'Selesai') return 'text-[#1D5E2D]';
                    if (status === 'Dibatalkan') return 'text-[#B91C1C]';
                    return 'text-gray-700';
                },

                formatDate(dateString) {
                    if (!dateString) return '-';
                    return new Date(dateString).toLocaleDateString('id-ID', { weekday: 'long', day: 'numeric', month: 'short', year: 'numeric' });
                },

                formatTime(timeString) {
                    if (!timeString) return '-';
                    return timeString.substring(0, 5);
                },

                confirmUpdateStatus(id, valueOrSelect) {
                    const newStatus = typeof valueOrSelect === 'object' && valueOrSelect !== null ? valueOrSelect.value : valueOrSelect;
                    const sidang = this.sidangs.find(s => s.id === id);
                    if (!sidang) return;

                    const originalStatus = sidang.pelaksanaan;
                    this.confirmData = { id, newStatus, selectElement: typeof valueOrSelect === 'object' && valueOrSelect !== null ? valueOrSelect : null, originalStatus };
                    this.showConfirmModal = true;
                },

                cancelUpdate() {
                    if (this.confirmData && this.confirmData.selectElement) {
                        if (!['Selesai', 'Dibatalkan'].includes(this.confirmData.originalStatus)) {
                            this.confirmData.selectElement.value = "";
                        } else {
                            this.confirmData.selectElement.value = this.confirmData.originalStatus;
                        }
                    }
                    this.showConfirmModal = false;
                    this.confirmData = null;
                },

                async executeUpdate() {
                    if (!this.confirmData) return;
                    
                    this.showConfirmModal = false;
                    const { id, newStatus, selectElement, originalStatus } = this.confirmData;
                    
                    try {
                        const response = await fetch(`{{ url('koordinator/input-nilai') }}/${id}/status`, {
                            method: 'POST',
                            headers: { 
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}' 
                            },
                            body: JSON.stringify({ pelaksanaan: newStatus })
                        });

                        if (!response.ok) {
                            let errorMsg = `HTTP Error ${response.status}`;
                            try {
                                const errJson = await response.json();
                                errorMsg = errJson.message || errorMsg;
                            } catch (parseErr) {}
                            this.errorMessage = errorMsg;
                            this.showErrorModal = true;
                            this.revertSelect(selectElement, originalStatus);
                            this.confirmData = null;
                            return;
                        }

                        const res = await response.json();
                        if (res.success) {
                            const idx = this.sidangs.findIndex(s => s.id === id);
                            if (idx !== -1) {
                                this.sidangs[idx].pelaksanaan = newStatus;
                                this.now = new Date();
                            }
                            this.successMessage = 'Status pelaksanaan berhasil diperbarui.';
                            this.showSuccessModal = true;
                            setTimeout(() => this.showSuccessModal = false, 2500);
                        } else { 
                            this.errorMessage = res.message || 'Gagal memperbarui status.';
                            this.showErrorModal = true;
                            this.revertSelect(selectElement, originalStatus);
                        }
                    } catch (e) { 
                        this.errorMessage = 'Gagal terhubung ke server. (' + e.message + ')';
                        this.showErrorModal = true;
                        this.revertSelect(selectElement, originalStatus);
                    }
                    this.confirmData = null;
                },

                revertSelect(selectElement, originalStatus) {
                    if (!['Selesai', 'Dibatalkan'].includes(originalStatus)) {
                        selectElement.value = "";
                    } else {
                        selectElement.value = originalStatus;
                    }
                }
            }
        }
    </script>

    <style>
        .sentence-case { text-transform: lowercase; }
        .sentence-case::first-letter { text-transform: uppercase; }
        [x-cloak] { display: none !important; }
    </style>
</x-dashboard-layout>