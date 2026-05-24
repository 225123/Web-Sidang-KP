<x-dashboard-layout header="Laporan Dan Arsip" userName="{{ auth()->user()->name }}" roleName="KOORDINATOR KP">
    <x-slot:sidebar>
        @include('koordinator.components.sidebar', ['active' => 'laporan-arsip'])
    </x-slot>

    

    <div x-data="laporanPage()" class="max-w-[1200px] mx-auto">
        <!-- Main Container -->
        <div class="bg-white rounded-[15px] border border-gray-200 shadow-sm overflow-hidden p-6 mb-8">
            <!-- Header Section -->
            <div class="mb-6 flex flex-col sm:flex-row justify-between items-start gap-4">
                <div>
                    <h3 class="text-[18px] font-bold text-black tracking-tight uppercase">TABEL KELULUSAN MAHASISWA</h3>
                    <p class="text-[12px] text-black/60 font-medium mt-1">Laporan resmi hasil kelulusan mahasiswa yang telah disahkan dan difinalisasi oleh Koordinator.</p>
                </div>
                <div class="flex flex-wrap gap-2 shrink-0">
                    <div class="bg-[#4285F4] text-white rounded-[5px] px-3 py-1.5 flex items-center gap-2 shadow-sm border border-blue-600/20">
                        <span class="text-[16px] font-bold leading-none" x-text="stats.total"></span>
                        <span class="text-[11px] font-medium uppercase tracking-wider">Total</span>
                    </div>
                    <div class="bg-[#34A853] text-white rounded-[5px] px-3 py-1.5 flex items-center gap-2 shadow-sm border border-green-600/20">
                        <span class="text-[16px] font-bold leading-none" x-text="stats.lulus"></span>
                        <span class="text-[11px] font-medium uppercase tracking-wider">Lulus</span>
                    </div>
                    <div class="bg-[#FBBC05] text-black rounded-[5px] px-3 py-1.5 flex items-center gap-2 shadow-sm border border-yellow-500/20">
                        <span class="text-[16px] font-bold leading-none" x-text="stats.revisi"></span>
                        <span class="text-[11px] font-medium uppercase tracking-wider">Revisi</span>
                    </div>
                    <div class="bg-[#EA4335] text-white rounded-[5px] px-3 py-1.5 flex items-center gap-2 shadow-sm border border-red-600/20">
                        <span class="text-[16px] font-bold leading-none" x-text="stats.lanjut"></span>
                        <span class="text-[11px] font-medium uppercase tracking-wider">Lanjut</span>
                    </div>
                </div>
            </div>

            <!-- Controls Section -->
            <div class="flex flex-col sm:flex-row justify-between items-center gap-6 mb-6">
                <div class="flex flex-wrap items-center gap-4 w-full sm:w-auto">
                    <!-- Search -->
                    <div class="relative flex-1 sm:w-[300px]">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </div>
                        <input type="text" x-model="search"
                            class="block w-full pl-9 pr-4 py-2 border border-gray-300 rounded-[5px] text-[12px] text-black focus:ring-[#4285F4]"
                            placeholder="Cari Nama/NIM Mahasiswa...">
                    </div>

                    <!-- Filter Status -->
                    <div x-data="{ openFilter: false }" class="relative w-full sm:w-[200px] z-[50]">
                        <button type="button" @click="openFilter = !openFilter" @click.outside="openFilter = false"
                            class="w-full text-[12px] border border-gray-300 rounded-[5px] py-2 px-3 bg-white text-black font-medium focus:ring-[#4285F4] flex justify-between items-center text-left shadow-sm">
                            <span x-text="filterStatus === 'all' ? 'Status Kelulusan' : filterStatus"></span>
                            <svg :class="openFilter ? 'rotate-0' : 'rotate-90'"
                                class="w-3.5 h-3.5 transition-all duration-200 text-gray-500 flex-shrink-0"
                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>
                        <div x-show="openFilter" x-transition x-cloak
                            class="absolute w-full mt-1 bg-white border border-gray-300 rounded-[5px] shadow-lg overflow-hidden py-1 z-50">
                            <label class="block px-3 py-2 text-[12px] hover:bg-gray-100 cursor-pointer text-black"><input type="radio" value="all" x-model="filterStatus" class="hidden" @change="openFilter = false">Semua</label>
                            <label class="block px-3 py-2 text-[12px] hover:bg-gray-100 cursor-pointer text-black"><input type="radio" value="Lulus" x-model="filterStatus" class="hidden" @change="openFilter = false">Lulus</label>
                            <label class="block px-3 py-2 text-[12px] hover:bg-gray-100 cursor-pointer text-black"><input type="radio" value="Lulus Dengan Revisi" x-model="filterStatus" class="hidden" @change="openFilter = false">Lulus Dengan Revisi</label>
                            <label class="block px-3 py-2 text-[12px] hover:bg-gray-100 cursor-pointer text-black"><input type="radio" value="Lanjut" x-model="filterStatus" class="hidden" @change="openFilter = false">Lanjut (Tidak Lulus)</label>
                        </div>
                    </div>

                    <button type="button" @click="clearFilters()"
                        class="flex-1 sm:flex-none border border-[#EA4335] bg-[#EA4335] text-white hover:bg-red-600 transition-colors px-4 py-1.5 rounded-[5px] text-[12px] font-bold shadow-sm">
                        Clear Filter
                    </button>
                </div>
                
                <div class="flex gap-2">
                    <a href="{{ route('koordinator.laporan-arsip.download') }}" class="bg-red-600 text-white hover:bg-red-700 font-bold text-[12px] px-4 py-1.5 rounded-[5px] transition-colors shadow-sm flex items-center gap-2">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M6 2a2 2 0 00-2 2v12a2 2 0 002 2h8a2 2 0 002-2V7.414A2 2 0 0015.414 6L12 2.586A2 2 0 0010.586 2H6zm5 6a1 1 0 10-2 0v3.586l-1.293-1.293a1 1 0 10-1.414 1.414l3 3a1 1 0 001.414 0l3-3a1 1 0 00-1.414-1.414L11 11.586V8z" clip-rule="evenodd"></path></svg>
                        Download PDF
                    </a>
                </div>
            </div>

            <!-- Table Section -->
            <div class="border border-gray-200 rounded-[10px] overflow-hidden relative">
                @if(!$isAllNilaiDisahkan)
                <div class="absolute inset-0 z-10 bg-white/60 backdrop-blur-[2px] flex flex-col items-center justify-center">
                    <div class="bg-white p-6 rounded-xl shadow-lg border border-gray-200 text-center max-w-md">
                        <svg class="w-12 h-12 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                        </svg>
                        <h4 class="text-lg font-bold text-gray-900 mb-2">Data Terkunci</h4>
                        <p class="text-sm text-gray-600 mb-4">Anda harus melakukan finalisasi nilai pada periode ini sebelum dapat melihat dan mengunduh laporan kelulusan.</p>
                        <a href="{{ route('koordinator.finalisasi-nilai.index') }}" class="inline-flex items-center justify-center px-4 py-2 bg-[#4285F4] text-white rounded-[5px] text-[12px] font-bold shadow-sm hover:bg-blue-600 transition-colors">
                            Menuju Finalisasi Nilai
                        </a>
                    </div>
                </div>
                @endif
                <table class="w-full border-collapse text-[12px] {{ !$isAllNilaiDisahkan ? 'opacity-30 pointer-events-none' : '' }}">
                    <thead class="bg-[#EBEBEB] text-black">
                        <tr>
                            <th class="py-3 px-4 font-bold text-center w-[60px] border-b border-r border-gray-300">No</th>
                            <th class="py-3 px-4 font-bold text-left border-b border-r border-gray-300 w-[150px]">NIM</th>
                            <th class="py-3 px-4 font-bold text-left border-b border-r border-gray-300">Nama Mahasiswa</th>
                            <th class="py-3 px-4 font-bold text-center border-b border-r border-gray-300 w-[100px]">Nilai</th>
                            <th class="py-3 px-4 font-bold text-center border-b border-r border-gray-300 w-[100px]">Grade</th>
                            <th class="py-3 px-4 font-bold text-center border-b border-gray-300 w-[200px]">Status Kelulusan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 bg-white">
                        <template x-for="(mhs, index) in paginatedSidangs" :key="mhs.nim">
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="py-3 px-4 text-center text-black/60 border-r border-gray-200" x-text="((currentPage - 1) * itemsPerPage) + index + 1"></td>
                                <td class="py-3 px-4 text-left font-mono text-black border-r border-gray-200" x-text="mhs.nim"></td>
                                <td class="py-3 px-4 text-left font-bold text-black uppercase border-r border-gray-200" x-text="mhs.nama"></td>
                                <td class="py-3 px-4 text-center border-r border-gray-200 font-bold" x-text="mhs.nilai_akhir_display === '-' ? '-' : Number(mhs.nilai_akhir_display).toFixed(2)"></td>
                                <td class="py-3 px-4 text-center border-r border-gray-200 font-bold" x-text="mhs.grade_display"></td>
                                <td class="py-3 px-4 text-center">
                                    <template x-if="mhs.status_kelulusan_display === 'Lulus'">
                                        <span class="inline-flex items-center gap-1.5 bg-green-100 text-green-700 px-3 py-1 rounded-full font-bold text-[11px] uppercase shadow-sm border border-green-200">Lulus</span>
                                    </template>
                                    <template x-if="mhs.status_kelulusan_display === 'Lulus Dengan Revisi'">
                                        <span class="inline-flex items-center gap-1.5 bg-blue-100 text-blue-700 px-3 py-1 rounded-full font-bold text-[11px] uppercase shadow-sm border border-blue-200">Lulus Dengan Revisi</span>
                                    </template>
                                    <template x-if="mhs.status_kelulusan_display === 'Lanjut'">
                                        <span class="inline-flex items-center gap-1.5 bg-red-100 text-red-700 px-3 py-1 rounded-full font-bold text-[11px] uppercase shadow-sm border border-red-200">Lanjut (Tidak Lulus)</span>
                                    </template>
                                    <template x-if="mhs.status_kelulusan_display === 'Belum Finalisasi'">
                                        <span class="inline-flex items-center gap-1.5 bg-gray-100 text-gray-500 px-3 py-1 rounded-full font-bold text-[11px] uppercase shadow-sm border border-gray-200">Belum Finalisasi</span>
                                    </template>
                                </td>
                            </tr>
                        </template>
                        <template x-if="filteredSidangs.length === 0">
                            <tr>
                                <td colspan="4" class="py-12 text-center text-gray-500 italic text-[12px]">Belum ada data kelulusan yang difinalisasi.</td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="px-6 py-4 bg-white flex items-center justify-between border-t border-gray-200" x-show="totalPages > 1">
                <span class="text-[12px] font-medium text-black/50" x-text="(filteredSidangs.length === 0 ? 0 : ((currentPage - 1) * itemsPerPage + 1)) + ' - ' + Math.min(currentPage * itemsPerPage, filteredSidangs.length) + ' dari ' + filteredSidangs.length + ' baris'"></span>
                <div class="flex items-center gap-2">
                    <button @click="if(currentPage > 1) currentPage--" :disabled="currentPage === 1" class="px-3 py-1 border border-gray-300 rounded text-[12px] hover:bg-gray-50 disabled:opacity-30 disabled:cursor-not-allowed transition-colors">Previous</button>
                    <div class="flex items-center gap-1">
                        <template x-for="p in totalPages" :key="p">
                            <button @click="currentPage = p" class="w-8 h-8 rounded text-[12px] font-bold transition-all" :class="currentPage === p ? 'bg-blue-600 text-white shadow-md' : 'text-black hover:bg-gray-100'" x-text="p"></button>
                        </template>
                    </div>
                    <button @click="if(currentPage < totalPages) currentPage++" :disabled="currentPage === totalPages" class="px-3 py-1 border border-gray-300 rounded text-[12px] hover:bg-gray-50 disabled:opacity-30 disabled:cursor-not-allowed transition-colors">Next</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        window.laporanPage = function() {
            return {
                sidangs: @json($mahasiswas),
                search: '',
                filterStatus: 'all',
                currentPage: 1,
                itemsPerPage: 15,

                init() {
                    this.$watch('search', () => this.currentPage = 1);
                    this.$watch('filterStatus', () => this.currentPage = 1);
                },

                clearFilters() {
                    this.search = '';
                    this.filterStatus = 'all';
                    this.currentPage = 1;
                },

                get stats() {
                    return {
                        total: this.sidangs.length,
                        lulus: this.sidangs.filter(s => s.status_kelulusan_display === 'Lulus').length,
                        revisi: this.sidangs.filter(s => s.status_kelulusan_display === 'Lulus Dengan Revisi').length,
                        lanjut: this.sidangs.filter(s => s.status_kelulusan_display === 'Lanjut' || s.status_kelulusan_display === 'Tidak Lulus').length,
                    }
                },

                get filteredSidangs() {
                    let res = [...this.sidangs];
                    if (this.search) {
                        const q = this.search.toLowerCase();
                        res = res.filter(s => s.nim.toLowerCase().includes(q) || s.user.name.toLowerCase().includes(q));
                    }
                    if (this.filterStatus !== 'all') {
                        res = res.filter(s => {
                            if (this.filterStatus === 'Lanjut') return s.status_kelulusan_display === 'Lanjut' || s.status_kelulusan_display === 'Tidak Lulus';
                            return s.status_kelulusan_display === this.filterStatus;
                        });
                    }
                    return res;
                },

                get paginatedSidangs() {
                    const start = (this.currentPage - 1) * this.itemsPerPage;
                    return this.filteredSidangs.slice(start, start + this.itemsPerPage);
                },

                get totalPages() {
                    return Math.ceil(this.filteredSidangs.length / this.itemsPerPage) || 1;
                }
            }
        }
    </script>

    <style>
        [x-cloak] { display: none !important; }
        @media print {
            .no-print, header, nav, .header-actions, .controls-section, .sidebar, button { display: none !important; }
            .bg-white { border: none !important; }
            body { background: white !important; }
        }
    </style>
</x-dashboard-layout>
