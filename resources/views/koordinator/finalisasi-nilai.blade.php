<x-dashboard-layout header="Finalisasi Nilai KP" userName="{{ auth()->user()->name }}" roleName="KOORDINATOR KP">
    <x-slot:sidebar>
        @include('koordinator.components.sidebar', ['active' => 'finalisasi-nilai'])
        </x-slot>

    

        <div x-data="finalisasiNilaiPage()" class="mt-6 space-y-6 pb-40">

            <!-- Summary Cards Section -->
            <div class="flex flex-col xl:flex-row gap-6 mb-8 items-start xl:items-stretch">
                <div class="flex-1 bg-[#EAEFFF] border border-[#BACDFB] rounded-[10px] p-4 flex items-center gap-4 shadow-sm">
                    <div class="bg-[#7896F8] w-6 h-6 rounded-full flex items-center justify-center text-white shrink-0 shadow-sm font-serif italic text-sm">i</div>
                    <p class="text-[14px] text-black font-medium leading-relaxed">
                        Periksa kelengkapan Input Nilai Mahasiswa dan klik tombol sahkan untuk Finalisasi Nilai untuk diberikan kepada mahasiswa sebagai bukti nilai akhir KP.
                    </p>
                </div>
                <div class="flex gap-4">
                    <div class="bg-[#38913B] rounded-[10px] p-3 flex flex-col justify-center items-center w-[90px] shadow-sm text-white">
                        <div class="flex items-center gap-2">
                            <div class="bg-white/20 p-1 rounded-full"><svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg></div>
                            <span class="text-xl font-bold" x-text="stats.lulus"></span>
                        </div>
                        <span class="text-[11px] font-medium mt-1">Lulus</span>
                    </div>
                    <div class="bg-[#FBC610] rounded-[10px] p-3 flex flex-col justify-center items-center w-[90px] shadow-sm text-black">
                        <div class="flex items-center gap-2">
                            <div class="border border-black p-0.5 rounded-sm"><svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"></path></svg></div>
                            <span class="text-xl font-bold" x-text="stats.revisi"></span>
                        </div>
                        <span class="text-[11px] font-medium text-center leading-tight mt-1">Lulus<br>Revisi</span>
                    </div>
                    <div class="bg-[#EA3323] rounded-[10px] p-3 flex flex-col justify-center items-center w-[90px] shadow-sm text-white">
                        <div class="flex items-center gap-2">
                            <div class="bg-white/20 p-1 rounded-full"><svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg></div>
                            <span class="text-xl font-bold" x-text="stats.lanjut"></span>
                        </div>
                        <span class="text-[11px] font-medium mt-1 text-center leading-tight">Lanjut<br>(Gagal)</span>
                    </div>
                </div>
            </div>

            <!-- Unified Table Container -->
            <div class="mt-8 bg-white rounded-[15px] border border-gray-200 shadow-sm overflow-hidden p-6 mb-12">
                <!-- Header Section -->
                <div class="mb-6 flex flex-col sm:flex-row justify-between items-start gap-4">
                    <div>
                        <h3 class="text-[18px] font-bold text-black tracking-tight">Tabel Finalisasi Nilai Mahasiswa</h3>
                        <p class="text-[12px] text-black/60 font-medium mt-1">Data rekapitulasi nilai mahasiswa berdasarkan status kelulusan yang sah (Lulus, Lulus Revisi, dan Lanjut).</p>
                    </div>
                </div>

                <div class="flex flex-col sm:flex-row justify-between items-center gap-6 mb-6">
                    <div class="flex flex-wrap items-center gap-4 w-full sm:w-auto">
                        <div class="relative flex-1 sm:w-[250px]">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                            </div>
                            <input type="text" x-model="search" @input="currentPage = 1"
                                class="block w-full pl-9 pr-4 py-2 border border-gray-300 rounded-[5px] text-[12px] text-black focus:ring-[#4285F4]"
                                placeholder="Cari nama, NIM, atau judul KP...">
                        </div>

                        <!-- Status Filter Dropdown -->
                        <div x-data="{ openStatus: false }" class="relative w-full sm:w-[150px] z-[60]">
                            <button type="button" @click="openStatus = !openStatus" @click.outside="openStatus = false" class="w-full text-[12px] border border-gray-300 rounded-[5px] py-2 px-3 bg-white text-black font-medium focus:ring-[#4285F4] flex justify-between items-center text-left shadow-sm">
                                <span x-text="filterStatus === 'all' ? 'Semua Status' : filterStatus"></span>
                                <svg :class="openStatus ? 'rotate-0' : 'rotate-90'" class="w-3.5 h-3.5 transition-all duration-200 text-gray-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                            </button>
                            <div x-show="openStatus" x-transition x-cloak class="absolute w-full mt-1 bg-white border border-gray-300 rounded-[5px] shadow-lg overflow-hidden py-1 z-50">
                                <label class="block px-3 py-2 text-[12px] hover:bg-gray-100 cursor-pointer text-black"><input type="radio" value="all" x-model="filterStatus" class="hidden" @change="openStatus = false; currentPage = 1">Semua Status</label>
                                <label class="block px-3 py-2 text-[12px] hover:bg-gray-100 cursor-pointer text-black"><input type="radio" value="Lulus" x-model="filterStatus" class="hidden" @change="openStatus = false; currentPage = 1">Lulus</label>
                                <label class="block px-3 py-2 text-[12px] hover:bg-gray-100 cursor-pointer text-black"><input type="radio" value="Lulus Dengan Revisi" x-model="filterStatus" class="hidden" @change="openStatus = false; currentPage = 1">Lulus Dengan Revisi</label>
                                <label class="block px-3 py-2 text-[12px] hover:bg-gray-100 cursor-pointer text-black"><input type="radio" value="Lanjut" x-model="filterStatus" class="hidden" @change="openStatus = false; currentPage = 1">Lanjut</label>
                            </div>
                        </div>

                        <!-- Grade Filter Dropdown -->
                        <div x-data="{ openGrade: false }" class="relative w-full sm:w-[130px] z-[50]">
                            <button type="button" @click="openGrade = !openGrade" @click.outside="openGrade = false" class="w-full text-[12px] border border-gray-300 rounded-[5px] py-2 px-3 bg-white text-black font-medium focus:ring-[#4285F4] flex justify-between items-center text-left shadow-sm">
                                <span x-text="filterGrade === 'all' ? 'Semua Grade' : filterGrade"></span>
                                <svg :class="openGrade ? 'rotate-0' : 'rotate-90'" class="w-3.5 h-3.5 transition-all duration-200 text-gray-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                            </button>
                            <div x-show="openGrade" x-transition x-cloak class="absolute w-full mt-1 bg-white border border-gray-300 rounded-[5px] shadow-lg overflow-y-auto max-h-[200px] py-1 z-50 custom-scrollbar">
                                <label class="block px-3 py-2 text-[12px] hover:bg-gray-100 cursor-pointer text-black"><input type="radio" value="all" x-model="filterGrade" class="hidden" @change="openGrade = false; currentPage = 1">Semua Grade</label>
                                <label class="block px-3 py-2 text-[12px] hover:bg-gray-100 cursor-pointer text-black"><input type="radio" value="A" x-model="filterGrade" class="hidden" @change="openGrade = false; currentPage = 1">A</label>
                                <label class="block px-3 py-2 text-[12px] hover:bg-gray-100 cursor-pointer text-black"><input type="radio" value="A-" x-model="filterGrade" class="hidden" @change="openGrade = false; currentPage = 1">A-</label>
                                <label class="block px-3 py-2 text-[12px] hover:bg-gray-100 cursor-pointer text-black"><input type="radio" value="B+" x-model="filterGrade" class="hidden" @change="openGrade = false; currentPage = 1">B+</label>
                                <label class="block px-3 py-2 text-[12px] hover:bg-gray-100 cursor-pointer text-black"><input type="radio" value="B" x-model="filterGrade" class="hidden" @change="openGrade = false; currentPage = 1">B</label>
                                <label class="block px-3 py-2 text-[12px] hover:bg-gray-100 cursor-pointer text-black"><input type="radio" value="B-" x-model="filterGrade" class="hidden" @change="openGrade = false; currentPage = 1">B-</label>
                                <label class="block px-3 py-2 text-[12px] hover:bg-gray-100 cursor-pointer text-black"><input type="radio" value="C+" x-model="filterGrade" class="hidden" @change="openGrade = false; currentPage = 1">C+</label>
                                <label class="block px-3 py-2 text-[12px] hover:bg-gray-100 cursor-pointer text-black"><input type="radio" value="C" x-model="filterGrade" class="hidden" @change="openGrade = false; currentPage = 1">C</label>
                                <label class="block px-3 py-2 text-[12px] hover:bg-gray-100 cursor-pointer text-black"><input type="radio" value="D" x-model="filterGrade" class="hidden" @change="openGrade = false; currentPage = 1">D</label>
                                <label class="block px-3 py-2 text-[12px] hover:bg-gray-100 cursor-pointer text-black"><input type="radio" value="E" x-model="filterGrade" class="hidden" @change="openGrade = false; currentPage = 1">E</label>
                            </div>
                        </div>
                        
                        <div class="flex gap-2 w-full sm:w-auto">
                            <button @click="clearFilters()" class="flex-1 sm:flex-none border border-[#EA4335] bg-[#EA4335] text-white hover:bg-red-600 transition-colors px-4 py-1.5 rounded-[5px] text-[12px] font-bold shadow-sm flex items-center justify-center">
                                Clear Filter
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Table Section -->
                <div class="border border-gray-200 rounded-[10px] overflow-hidden">
                    <div class="overflow-x-auto custom-scrollbar">
                        <table class="w-full border-collapse text-[11px]" style="min-width: 800px;">
                            <thead>
                                <tr class="bg-[#EBEBEB] text-black">
                                    <th
                                        class="py-3 px-4 font-bold text-center w-[50px] border-b border-r border-gray-300">
                                        No</th>
                                    <th
                                        class="py-3 px-4 font-bold text-left w-[180px] border-b border-r border-gray-300">
                                        Mahasiswa</th>
                                    <th class="py-3 px-4 font-bold text-left border-b border-r border-gray-300">Judul KP
                                    </th>
                                    <th
                                        class="py-3 px-4 font-bold text-center w-[120px] border-b border-r border-gray-300">
                                        Nilai Akhir</th>
                                    <th
                                        class="py-3 px-4 font-bold text-center w-[150px] border-b border-r border-gray-300">
                                        Status Kelulusan</th>
                                    <th class="py-3 px-4 font-bold text-center w-[130px] border-b border-gray-300">
                                        Detail</th>
                                </tr>
                            </thead>
                            <template x-for="(sidang, index) in paginatedSidangs" :key="sidang.id">
                                <tbody class="bg-white border-b border-gray-100 transition-colors">
                                    <tr class="hover:bg-blue-50/40 transition-all duration-200 cursor-pointer group"
                                        @click="sidang.expanded = !sidang.expanded">
                                        <td class="py-4 px-4 text-center text-gray-500 font-medium border-r border-gray-100"
                                            x-text="startEntry + index"></td>
                                        <td class="py-4 px-4 text-left border-r border-gray-100">
                                            <div class="font-bold text-black uppercase text-[11px]"
                                                x-text="sidang.mahasiswa.user.name"></div>
                                            <div class="text-[10px] text-black/50 font-bold mt-0.5"
                                                x-text="sidang.mahasiswa.nim"></div>
                                        </td>
                                        <td class="py-4 px-4 text-left border-r border-gray-100">
                                            <div class="text-black font-medium sentence-case leading-tight text-[11px]"
                                                x-text="sidang.judul_kp_display ? sidang.judul_kp_display.toLowerCase() : '-'"></div>
                                        </td>
                                        <td class="py-4 px-4 text-center border-r border-gray-100">
                                            <div class="flex flex-col items-center">
                                                <span class="font-black text-[13px]"
                                                    :class="sidang.status_kelulusan === 'Lanjut' ? 'text-red-600' : 'text-black'">
                                                    <span x-text="parseFloat(sidang.nilai_akhir_display).toFixed(2)"></span>
                                                    <span x-text="' (' + sidang.grade_display + ')'"></span>
                                                    <template x-if="sidang.is_penalized">
                                                        <span class="text-[10px] text-black/40 font-bold ml-1"
                                                            x-text="'(Asli: ' + sidang.original_grade + ')'"></span>
                                                    </template>
                                                </span>
                                                <template x-if="sidang.is_penalized">
                                                    <span
                                                        class="text-[9px] font-bold text-red-600 mt-1 bg-red-50 px-1.5 py-0.5 rounded border border-red-200">
                                                        PENALTI
                                                    </span>
                                                </template>
                                            </div>
                                        </td>
                                        <td class="py-4 px-4 text-center border-r border-gray-100">
                                            <template x-if="sidang.status_kelulusan === 'Lulus'">
                                                <span
                                                    class="px-2 py-1 bg-green-100 text-green-700 rounded-md font-bold text-[10px] border border-green-200 uppercase">Lulus</span>
                                            </template>
                                            <template x-if="sidang.status_kelulusan === 'Lulus Dengan Revisi'">
                                                <span
                                                    class="px-2 py-1 bg-yellow-100 text-yellow-700 rounded-md font-bold text-[10px] border border-yellow-200 uppercase">Lulus
                                                    Revisi</span>
                                            </template>
                                            <template x-if="sidang.status_kelulusan === 'Lanjut'">
                                                <span
                                                    class="px-2 py-1 bg-red-100 text-red-700 rounded-md font-bold text-[10px] border border-red-200 uppercase">Lanjut</span>
                                            </template>
                                        </td>
                                        <td class="py-4 px-4 text-center" @click.stop>
                                            <a :href="'{{ url('koordinator/finalisasi-nilai') }}/' + sidang.id"
                                                class="bg-[#EBEBEB] hover:bg-gray-300 text-black border border-[#CAC0C0] font-bold text-[10px] px-3 py-1.5 rounded-[5px] inline-flex items-center justify-center gap-1.5 transition-colors uppercase shadow-sm">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z">
                                                    </path>
                                                </svg>
                                                Detail
                                            </a>
                                        </td>
                                    </tr>
                                    <!-- Expandable Row for Details (Animated Slide Down) -->
                                    <tr>
                                        <td colspan="6" class="p-0 border-0">
                                            <div class="grid transition-all duration-300 ease-in-out"
                                                :class="sidang.expanded ? 'grid-rows-[1fr] opacity-100' : 'grid-rows-[0fr] opacity-0'">
                                                <div class="overflow-hidden">
                                                    <div
                                                        class="p-5 bg-[#F8FAFC] border-t border-gray-100 shadow-[inset_0_4px_6px_-4px_rgba(0,0,0,0.05)]">
                                                        <h4
                                                            class="text-[11px] font-bold text-gray-500 uppercase tracking-wider mb-4 border-b border-gray-200 pb-2">
                                                            Komponen Nilai</h4>
                                                        <div
                                                            class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
                                                            <!-- Supervisor -->
                                                            <div
                                                                class="flex items-center justify-between p-3 bg-white border border-gray-200 rounded-[8px] shadow-sm">
                                                                <div class="flex items-center gap-3">
                                                                    <div class="flex flex-col">
                                                                        <span
                                                                            class="text-[11px] font-bold text-gray-700">Supervisor</span>
                                                                        <span class="text-[14px] font-black text-black"
                                                                            x-text="(sidang.nilai_supervisor !== null && sidang.nilai_supervisor !== '') ? sidang.nilai_supervisor : '-'"></span>
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            <!-- Pembimbing -->
                                                            <div
                                                                class="flex items-center justify-between p-3 bg-white border border-gray-200 rounded-[8px] shadow-sm">
                                                                <div class="flex items-center gap-3">
                                                                    <div class="flex flex-col">
                                                                        <span
                                                                            class="text-[11px] font-bold text-gray-700">Pembimbing</span>
                                                                        <span class="text-[14px] font-black text-black"
                                                                            x-text="(sidang.nilai_pembimbing !== null && sidang.nilai_pembimbing !== '') ? sidang.nilai_pembimbing : '-'"></span>
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            <!-- Penguji 1 -->
                                                            <div
                                                                class="flex items-center justify-between p-3 bg-white border border-gray-200 rounded-[8px] shadow-sm">
                                                                <div class="flex items-center gap-3">
                                                                    <div class="flex flex-col">
                                                                        <span
                                                                            class="text-[11px] font-bold text-gray-700">Penguji
                                                                            1</span>
                                                                        <span class="text-[14px] font-black text-black">
                                                                            <span x-text="(sidang.nilai_penguji_1 !== null && sidang.nilai_penguji_1 !== '') ? sidang.nilai_penguji_1 : '-'"></span>
                                                                            <template x-if="sidang.original_nilai_penguji_1 !== null && parseFloat(sidang.nilai_penguji_1) > parseFloat(sidang.original_nilai_penguji_1)">
                                                                                <span class="text-[10px] text-green-600 ml-1 italic font-medium">(dinaikkan)</span>
                                                                            </template>
                                                                            <template x-if="sidang.original_nilai_penguji_1 !== null && parseFloat(sidang.nilai_penguji_1) < parseFloat(sidang.original_nilai_penguji_1)">
                                                                                <span class="text-[10px] text-red-600 ml-1 italic font-medium">(diturunkan)</span>
                                                                            </template>
                                                                        </span>
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            <!-- Penguji 2 -->
                                                            <div
                                                                class="flex items-center justify-between p-3 bg-white border border-gray-200 rounded-[8px] shadow-sm">
                                                                <div class="flex items-center gap-3">
                                                                    <div class="flex flex-col">
                                                                        <span
                                                                            class="text-[11px] font-bold text-gray-700">Penguji
                                                                            2</span>
                                                                        <span class="text-[14px] font-black text-black"
                                                                            x-text="(sidang.nilai_penguji_2 !== null && sidang.nilai_penguji_2 !== '') ? sidang.nilai_penguji_2 : '-'"></span>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </template>
                            <template x-if="filteredSidangs.length === 0">
                                <tbody class="bg-white">
                                    <tr>
                                        <td colspan="6"
                                            class="py-12 text-center text-gray-400 italic font-medium bg-gray-50 tracking-widest border-t border-gray-200 uppercase">
                                            Tidak Ada Data Ditemukan</td>
                                    </tr>
                                </tbody>
                            </template>
                        </table>
                    </div>
                </div>

                <!-- Pagination Footer -->
                <div class="px-6 py-4 bg-white border-t border-gray-200 flex items-center justify-between"
                    x-show="totalPages > 1">
                    <span class="text-[12px] font-medium text-black/50"
                        x-text="(filteredSidangs.length === 0 ? 0 : ((currentPage - 1) * itemsPerPage + 1)) + ' - ' + Math.min(currentPage * itemsPerPage, filteredSidangs.length) + ' dari ' + filteredSidangs.length + ' baris'"></span>
                    <div class="flex items-center gap-2">
                        <button @click="prevPage" :disabled="currentPage === 1"
                            class="px-3 py-1 border border-gray-300 rounded text-[12px] hover:bg-gray-50 disabled:opacity-30 disabled:cursor-not-allowed">Previous</button>
                        <div class="flex items-center gap-1">
                            <template x-for="p in totalPages" :key="p">
                                <button @click="goToPage(p)"
                                    class="w-8 h-8 rounded text-[12px] font-bold transition-all"
                                    :class="currentPage === p ? 'bg-blue-600 text-white shadow-md' : 'text-black hover:bg-gray-100'"
                                    x-text="p"></button>
                            </template>
                        </div>
                        <button @click="nextPage" :disabled="currentPage === totalPages"
                            class="px-3 py-1 border border-gray-300 rounded text-[12px] hover:bg-gray-50 disabled:opacity-30 disabled:cursor-not-allowed">Next</button>
                    </div>
                </div>
            </div>

            <!-- Cek Kelengkapan Input Section -->
            <div class="mt-16 bg-white rounded-[15px] border border-gray-200 shadow-sm overflow-hidden p-6 mb-12">
                <div class="mb-6 flex flex-col sm:flex-row justify-between items-start gap-4">
                    <div>
                        <h3 class="text-[18px] font-bold text-black tracking-tight">Cek Kelengkapan Input</h3>
                        <p class="text-[12px] text-black/60 font-medium mt-1">Pantau kelengkapan nilai mahasiswa yang terinput oleh pemberi nilai.</p>
                    </div>
                </div>

                <div class="flex flex-col sm:flex-row justify-between items-center gap-6 mb-6">
                    <div class="flex flex-wrap items-center gap-4 w-full sm:w-auto">
                        <div class="relative flex-1 sm:w-[300px]">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                            </div>
                            <input type="text" x-model="searchStatus" @input="statusCurrentPage = 1" class="block w-full pl-9 pr-4 py-2 border border-gray-300 rounded-[5px] text-[12px] text-black focus:ring-[#4285F4]" placeholder="Cari Nama/NIM...">
                        </div>

                        <div class="flex gap-2 w-full sm:w-auto">
                            <button type="button" @click="searchStatus = ''; statusCurrentPage = 1" class="flex-1 sm:flex-none border border-[#EA4335] bg-[#EA4335] text-white hover:bg-red-600 transition-colors px-4 py-1.5 rounded-[5px] text-[12px] font-bold shadow-sm flex items-center justify-center">
                                Clear Filter
                            </button>
                        </div>
                    </div>
                </div>

                <div class="border border-gray-200 rounded-[10px] overflow-hidden">
                    <table class="w-full border-collapse text-[13px]">
                        <thead>
                            <tr class="bg-[#EBEBEB] text-black">
                                <th class="py-3 px-4 font-bold text-center w-[60px] border-b border-r border-gray-300">No</th>
                                <th class="py-3 px-4 font-bold text-left border-b border-r border-gray-300">NIM</th>
                                <th class="py-3 px-4 font-bold text-left border-b border-r border-gray-300">Nama Mahasiswa</th>
                                <th class="py-3 px-4 font-bold text-center border-b border-gray-300">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            <template x-for="(stat, index) in paginatedInputStatusRows" :key="stat.nim">
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="py-3 px-4 text-center text-black/60 border-r border-gray-200" x-text="(statusCurrentPage - 1) * statusPerPage + index + 1"></td>
                                    <td class="py-3 px-4 text-left font-medium text-black border-r border-gray-200" x-text="stat.nim"></td>
                                    <td class="py-3 px-4 text-left font-normal text-black sentence-case border-r border-gray-200" x-text="stat.name"></td>
                                    <td class="py-3 px-4 text-center">
                                        <template x-if="stat.status === 'Input Lengkap'">
                                            <span class="inline-flex items-center gap-1.5 bg-green-100 text-green-700 px-4 py-1 rounded-full font-bold text-[10px] uppercase">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg> Input Lengkap
                                            </span>
                                        </template>
                                        <template x-if="stat.status === 'Input Belum Lengkap'">
                                            <span class="text-black/40 font-medium text-[11px] italic tracking-tight">Input Belum Lengkap</span>
                                        </template>
                                    </td>
                                </tr>
                            </template>
                            <template x-if="filteredInputStatusRows.length === 0">
                                <tr>
                                    <td colspan="4" class="py-12 text-center text-gray-400 italic font-medium bg-gray-50 uppercase tracking-widest">Tidak ada data ditemukan</td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination Footer -->
                <div class="px-6 py-4 bg-white flex items-center justify-between border-t border-gray-200" x-show="totalStatusPages > 1">
                    <span class="text-[12px] font-medium text-black/50" x-text="(filteredInputStatusRows.length === 0 ? 0 : ((statusCurrentPage - 1) * statusPerPage + 1)) + ' - ' + Math.min(statusCurrentPage * statusPerPage, filteredInputStatusRows.length) + ' dari ' + filteredInputStatusRows.length + ' baris'"></span>
                    <div class="flex items-center gap-2">
                        <button @click="prevStatusPage" :disabled="statusCurrentPage === 1" class="px-3 py-1 border border-gray-300 rounded text-[12px] hover:bg-gray-50 disabled:opacity-30 disabled:cursor-not-allowed transition-colors">Previous</button>
                        <div class="flex items-center gap-1">
                            <template x-for="p in totalStatusPages" :key="p">
                                <button @click="goToStatusPage(p)" class="w-8 h-8 rounded text-[12px] font-bold transition-all" :class="statusCurrentPage === p ? 'bg-blue-600 text-white shadow-md' : 'text-black hover:bg-gray-100'" x-text="p"></button>
                            </template>
                        </div>
                        <button @click="nextStatusPage" :disabled="statusCurrentPage === totalStatusPages" class="px-3 py-1 border border-gray-300 rounded text-[12px] hover:bg-gray-50 disabled:opacity-30 disabled:cursor-not-allowed transition-colors">Next</button>
                    </div>
                </div>
            </div>

            <!-- Submit Button Section -->
            <div class="flex flex-col items-end justify-end mt-8 mb-10">
                @if(!$hasValidSidangs)
                    <button type="button" disabled class="bg-gray-400 text-white font-bold py-3 px-8 rounded-[10px] shadow-md flex items-center gap-2 text-[14px] uppercase tracking-wide cursor-not-allowed">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                        Belum Ada Data Nilai
                    </button>
                @elseif($isAllNilaiDisahkan)
                    <button type="button" disabled class="bg-green-600 border-2 border-green-700 text-white font-bold py-3 px-8 rounded-[10px] shadow-md flex items-center gap-2 text-[14px] uppercase tracking-wide cursor-not-allowed opacity-90">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                        NILAI TELAH DISAHKAN & DITERBITKAN
                    </button>
                    <p class="text-[11px] font-bold text-green-600 mt-2 text-right w-full">Seluruh Finalisasi Nilai telah berhasil dikunci dan diterbitkan.</p>
                @elseif(isset($isReadOnly) && $isReadOnly)
                    <button type="button" disabled class="bg-gray-400 border-2 border-gray-500 text-white font-bold py-3 px-8 rounded-[10px] shadow-md flex items-center gap-2 text-[14px] uppercase tracking-wide cursor-not-allowed opacity-90">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                        MODE READ-ONLY
                    </button>
                @else
                    <form id="sahkan-form" action="{{ route('koordinator.finalisasi-nilai.sahkan') }}" method="POST">
                        @csrf
                        <button type="button" @click="sahkanNilai()" 
                            class="bg-[#4285F4] hover:bg-blue-600 text-white font-bold py-3 px-8 rounded-[10px] shadow-md flex items-center gap-2 transition-all text-[14px] uppercase tracking-wide">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                            Sahkan Finalisasi Nilai
                        </button>
                    </form>
                @endif
            </div>

            <!-- Custom Global Confirm Modal -->
            <div x-cloak x-show="confirmDialog.show" style="display: none;" class="fixed inset-0 z-[1000] flex items-center justify-center bg-black/40 backdrop-blur-sm p-4" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95">
                <div @click.away="confirmDialog.show = false" class="bg-white rounded-[15px] w-full max-w-[420px] p-8 shadow-2xl flex flex-col items-center text-center relative overflow-hidden border border-gray-100">
                    
                    <div class="mb-6">
                        <template x-if="confirmDialog.type === 'danger'">
                            <div class="w-20 h-20 bg-red-50 rounded-full flex items-center justify-center">
                                <svg class="w-12 h-12 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                            </div>
                        </template>
                        <template x-if="confirmDialog.type === 'info'">
                            <div class="w-20 h-20 bg-blue-50 rounded-full flex items-center justify-center">
                                <svg class="w-12 h-12 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            </div>
                        </template>
                    </div>

                    <h3 class="text-[18px] font-bold text-gray-900 mb-3" x-text="confirmDialog.title"></h3>
                    <p class="text-[14px] text-gray-500 mb-8 leading-relaxed px-2" x-text="confirmDialog.message"></p>

                    <div class="flex gap-4 w-full">
                        <button @click="confirmDialog.show = false" type="button" class="flex-1 h-[45px] bg-gray-50 hover:bg-gray-100 text-gray-700 rounded-[10px] text-[14px] font-bold transition-all border border-gray-200">
                            Batal
                        </button>
                        <button @click="executeConfirm()" type="button" 
                            class="flex-1 h-[45px] text-white rounded-[10px] text-[14px] font-bold transition-all shadow-md active:transform active:scale-95"
                            :class="[
                                confirmDialog.type === 'danger' ? 'bg-[#E53935] hover:bg-red-700' : '',
                                confirmDialog.type === 'info' ? 'bg-[#4285F4] hover:bg-blue-700' : ''
                            ]"
                            x-text="confirmDialog.confirmText">
                        </button>
                    </div>
                </div>
            </div>

        </div>

        <script>
            function finalisasiNilaiPage() {
                return {
                    sidangs: @json($sidangs).map(s => ({ ...s, expanded: false })),
                    search: '',
                    filterStatus: 'all',
                    filterGrade: 'all',
                    currentPage: 1,
                    perPage: 10, // Use 10 similar to verifikasi berkas sidang
                    confirmDialog: { show: false, title: '', message: '', type: 'info', confirmText: 'Iya, Lanjutkan', callback: null },

                    sahkanNilai() {

                        this.confirmDialog = {
                            show: true,
                            title: 'Sahkan Finalisasi Nilai',
                            message: 'Pastikan telah memeriksa seluruh kelengkapan nilai dan semacamnya karena hasil ini akan menjadi nilai akhir bagi mahasiswa. Nilai dan Berita Acara akan langsung terbit ke mahasiswa.',
                            type: 'info',
                            confirmText: 'Sahkan & Terbitkan',
                            callback: () => {
                                document.getElementById('sahkan-form').submit();
                            }
                        };
                    },

                    executeConfirm() {
                        if (this.confirmDialog.callback) {
                            this.confirmDialog.callback();
                        }
                    },

                    get stats() {
                        return {
                            lulus: this.sidangs.filter(s => s.status_kelulusan === 'Lulus').length,
                            revisi: this.sidangs.filter(s => s.status_kelulusan === 'Lulus Dengan Revisi').length,
                            lanjut: this.sidangs.filter(s => s.status_kelulusan === 'Lanjut').length,
                        };
                    },

                    clearFilters() {
                        this.search = '';
                        this.filterStatus = 'all';
                        this.filterGrade = 'all';
                        this.currentPage = 1;
                    },

                    get filteredSidangs() {
                        let res = [...this.sidangs];
                        if (this.search) {
                            const q = this.search.toLowerCase();
                            res = res.filter(s =>
                                s.mahasiswa.nim.toLowerCase().includes(q) ||
                                s.mahasiswa.user.name.toLowerCase().includes(q) ||
                                (s.judul_kp_display && s.judul_kp_display.toLowerCase().includes(q))
                            );
                        }
                        if (this.filterStatus !== 'all') res = res.filter(s => s.status_kelulusan === this.filterStatus);
                        if (this.filterGrade !== 'all') res = res.filter(s => s.grade_display === this.filterGrade);
                        return res;
                    },

                    get paginatedSidangs() {
                        const start = (this.currentPage - 1) * this.perPage;
                        return this.filteredSidangs.slice(start, start + this.perPage);
                    },

                    get totalPages() { return Math.ceil(this.filteredSidangs.length / this.perPage) || 1; },
                    get totalEntries() { return this.filteredSidangs.length; },
                    get startEntry() { return this.totalEntries === 0 ? 0 : (this.currentPage - 1) * this.perPage + 1; },
                    get endEntry() { return Math.min(this.currentPage * this.perPage, this.totalEntries); },

                    nextPage() { if (this.currentPage < this.totalPages) this.currentPage++; },
                    prevPage() { if (this.currentPage > 1) this.currentPage--; },
                    goToPage(page) { this.currentPage = page; },

                    get inputStatusRows() {
                        return this.sidangs.map(s => {
                            const isLengkap = s.nilai_pembimbing !== null && 
                                              s.nilai_penguji_1 !== null && 
                                              s.nilai_penguji_2 !== null;
                            return {
                                nim: s.mahasiswa.nim,
                                name: s.mahasiswa.user.name,
                                status: isLengkap ? 'Input Lengkap' : 'Input Belum Lengkap'
                            };
                        });
                    },
                    
                    searchStatus: '',
                    statusCurrentPage: 1,
                    statusPerPage: 10,

                    get filteredInputStatusRows() {
                        let res = this.inputStatusRows;
                        if (this.searchStatus) {
                            const q = this.searchStatus.toLowerCase();
                            res = res.filter(r => r.nim.toLowerCase().includes(q) || r.name.toLowerCase().includes(q));
                        }
                        return res;
                    },

                    get paginatedInputStatusRows() {
                        const start = (this.statusCurrentPage - 1) * this.statusPerPage;
                        return this.filteredInputStatusRows.slice(start, start + this.statusPerPage);
                    },

                    get totalStatusPages() { return Math.ceil(this.filteredInputStatusRows.length / this.statusPerPage) || 1; },
                    nextStatusPage() { if (this.statusCurrentPage < this.totalStatusPages) this.statusCurrentPage++; },
                    prevStatusPage() { if (this.statusCurrentPage > 1) this.statusCurrentPage--; },
                    goToStatusPage(page) { this.statusCurrentPage = page; }
                }
            }
        </script>

        <style>
            .sentence-case {
                text-transform: lowercase;
            }

            .sentence-case::first-letter {
                text-transform: uppercase;
            }

            [x-cloak] {
                display: none !important;
            }

            .custom-scrollbar::-webkit-scrollbar {
                width: 5px;
                height: 5px;
            }

            .custom-scrollbar::-webkit-scrollbar-track {
                background: #f1f1f1;
            }

            .custom-scrollbar::-webkit-scrollbar-thumb {
                background: #ccc;
                border-radius: 10px;
            }

            .custom-scrollbar::-webkit-scrollbar-thumb:hover {
                background: #999;
            }
        </style>
</x-dashboard-layout>