<x-dashboard-layout header="Pendaftaran KP" userName="{{ auth()->user()->name ?? 'KOORDINATOR KP' }}" roleName="KOORDINATOR KP">
    <x-slot:sidebar>
        @include('koordinator.components.sidebar', ['active' => 'pendaftaran-kp'])
    </x-slot>

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

    <style>
        .counter-body { counter-reset: row-number {{ ($pendaftarans->currentPage() - 1) * $pendaftarans->perPage() }}; }
        .data-row:not([style*="display: none"]) .row-number-cell::before {
            counter-increment: row-number;
            content: counter(row-number);
        }
    </style>

    <div class="mt-8 px-2 w-full max-w-[1200px] mx-auto" x-data="pendaftaranScope()">
        
        <!-- Modal Catatan Mengambang (Opsional) -->
        <div x-show="modalCatatanOpen" style="display: none;" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-40">
            <div @click.outside="modalCatatanOpen = false" class="bg-white rounded-[5px] shadow-xl w-[400px] p-6 text-center transform inline-block">
                <h3 class="text-xl font-bold text-gray-800 mb-2 truncate" x-text="modalPesan"></h3>
                <p class="text-[13px] text-gray-500 mb-4 leading-snug">Silakan buat catatan khusus (opsional) atau kosongkan apabila tidak ada.</p>
                <textarea x-model="modalCatatanValue" class="w-full border border-gray-300 rounded-[5px] p-3 text-[13px] focus:outline-none focus:border-[#4285F4] mb-5 resize-none h-[100px]" placeholder="Ketik catatan di sini..."></textarea>
                <div class="flex justify-center gap-3">
                    <button @click="modalCatatanOpen = false" type="button" class="px-6 py-2 rounded-[5px] bg-gray-200 text-gray-700 font-bold text-[13px] hover:bg-gray-300 transition-colors">Batal</button>
                    <button @click="submitModalCatatan()" type="button" class="px-6 py-2 rounded-[5px] text-white font-bold text-[13px] transition-colors bg-[#4285F4] hover:bg-blue-600">Simpan & Proses</button>
                </div>
            </div>
        </div>
        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4 shadow-sm" role="alert">
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
        @endif

        <div class="bg-[#E6F0FA] border border-[#D0E3F5] rounded-[10px] p-4 lg:p-5 mb-6 flex items-start gap-4 shadow-sm">
            <div class="w-6 h-6 rounded-full bg-[#4285F4] text-white flex items-center justify-center font-bold flex-shrink-0 mt-0.5">
                i
            </div>
            <p class="text-[14px] text-[#1A1A1A] font-medium leading-relaxed m-0 mt-0.5">
                Tinjau List Mahasiswa yang mendaftarkan Proyek KP dan lakukan pengesahan atau penolakan terhadapnya.
            </p>
        </div>

        <div class="flex flex-wrap gap-4 mb-8">
            <div class="flex flex-col sm:flex-row gap-4 w-full xl:w-auto">
                <div class="w-full xl:w-[200px] h-[75px] bg-[#3B82F6] rounded-[5px] relative overflow-hidden flex flex-col justify-center items-center shadow-sm">
                    <svg class="w-6 h-6 absolute left-3 top-3 text-white opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    <span class="text-[#E8F5E9] text-[28px] font-bold font-inter leading-none mt-1">{{ $stats['total_mahasiswa'] ?? 0 }}</span>
                    <span class="text-[#E8F5E9] text-[12px] font-medium font-inter mt-1">Total Mahasiswa KP</span>
                </div>
                <div class="w-full xl:w-[200px] h-[75px] bg-[#4CAF50] rounded-[5px] relative overflow-hidden flex flex-col justify-center items-center shadow-sm">
                    <svg class="w-6 h-6 absolute left-3 top-3 text-white opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span class="text-[#E8F5E9] text-[28px] font-bold font-inter leading-none mt-1">{{ $stats['dapat_projek'] ?? 0 }}</span>
                    <span class="text-[#E8F5E9] text-[12px] font-medium font-inter mt-1">Sudah Dapat Projek</span>
                </div>
                <div class="w-full xl:w-[200px] h-[75px] bg-[#EA4335] rounded-[5px] relative overflow-hidden flex flex-col justify-center items-center shadow-sm">
                    <svg class="w-6 h-6 absolute left-3 top-3 text-white opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span class="text-[#E8F5E9] text-[28px] font-bold font-inter leading-none mt-1">{{ $stats['belum_dapat_projek'] ?? 0 }}</span>
                    <span class="text-[#E8F5E9] text-[12px] font-medium font-inter mt-1">Belum Dapat Projek</span>
                </div>
            </div>

            <div class="hidden xl:block w-px bg-gray-300 mx-2"></div>

            <div class="flex flex-col sm:flex-row flex-wrap gap-4 w-full xl:w-auto">
                <div class="bg-[#34A853] text-white rounded-[5px] w-full sm:w-[calc(33.33%-0.67rem)] xl:w-[100px] h-[75px] flex flex-col justify-center items-center shadow-sm">
                    <div class="flex items-center gap-1 mt-1">
                        <svg class="w-4 h-4 rounded-full border border-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        <span class="text-[24px] font-bold leading-none">{{ $stats['disetujui'] }}</span>
                    </div>
                    <span class="text-[11px] font-medium mt-1.5">Disetujui</span>
                </div>
                <div class="bg-[#FBBC05] text-black rounded-[5px] w-full sm:w-[calc(33.33%-0.67rem)] xl:w-[100px] h-[75px] flex flex-col justify-center items-center shadow-sm">
                    <div class="flex items-center gap-1 mt-1">
                        <div class="w-3.5 h-3.5 border-2 border-black rounded-sm"></div>
                        <span class="text-[24px] font-bold leading-none">{{ $stats['belum_diperiksa'] }}</span>
                    </div>
                    <span class="text-[11px] font-medium mt-1.5">Belum Diperiksa</span>
                </div>
                <div class="bg-[#EA4335] text-white rounded-[5px] w-full sm:w-[calc(33.33%-0.67rem)] xl:w-[100px] h-[75px] flex flex-col justify-center items-center shadow-sm">
                    <div class="flex items-center gap-1 mt-1">
                        <svg class="w-4 h-4 rounded-full border border-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        <span class="text-[24px] font-bold leading-none">{{ $stats['ditolak'] }}</span>
                    </div>
                    <span class="text-[11px] font-medium mt-1.5">Ditolak</span>
                </div>
            </div>
        </div>



        <div class="mb-4" id="main">
            @include('koordinator.components.pendaftaran-filter', ['prefix' => 'main', 'otherPrefix' => 'rejected', 'hideStatus' => false])
            @include('koordinator.components.kp-table', ['pendaftarans' => $pendaftarans, 'title' => 'Daftar Pengajuan KP', 'isRejected' => false, 'searchModel' => 'searchQuery'])
        </div>

        <div class="mt-8 mb-8" id="rejected">
            @include('koordinator.components.pendaftaran-filter', ['prefix' => 'rejected', 'otherPrefix' => 'main', 'hideStatus' => true])
            @include('koordinator.components.kp-table', ['pendaftarans' => $rejectedPendaftarans, 'title' => 'Riwayat Penolakan Pendaftaran KP', 'isRejected' => true, 'searchModel' => 'searchQueryRejected'])
        </div>

        <!-- Tabel Status Pendaftaran Mahasiswa -->
        <div class="mt-16 bg-white rounded-[15px] border border-gray-200 shadow-sm overflow-hidden p-6 mb-8">
            <div class="flex flex-col sm:flex-row justify-between items-center gap-6 mb-8">
                <div>
                    <h3 class="text-[18px] font-bold text-black uppercase tracking-tight">Status Pendaftaran Mahasiswa</h3>
                    <p class="text-[12px] text-black/60 font-medium">Rekapitulasi pendaftaran KP seluruh mahasiswa</p>
                </div>

                <div class="flex flex-wrap items-center gap-4 w-full sm:w-auto">
                    <div class="relative flex-1 sm:w-[300px]">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        </div>
                        <input type="text" x-model="searchStatus" class="block w-full pl-9 pr-4 py-2 border border-gray-300 rounded-[5px] text-[12px] text-black focus:ring-[#4285F4]" placeholder="Cari Nama/NIM Mahasiswa...">
                    </div>

                    <div x-data="{ openFilter: false }" class="relative w-full sm:w-[180px] z-[60]">
                        <button type="button" @click="openFilter = !openFilter" @click.outside="openFilter = false" class="w-full text-[12px] border border-gray-300 rounded-[5px] py-2 px-3 bg-white text-black font-medium focus:ring-[#4285F4] flex justify-between items-center text-left shadow-sm">
                            <span x-text="filterStatusUpload === 'all' ? 'Semua Status' : (filterStatusUpload === 'sudah' ? 'Sudah Mendaftar' : 'Belum Mendaftar')"></span>
                            <svg :class="openFilter ? 'rotate-0' : 'rotate-90'" class="w-3.5 h-3.5 transition-all duration-200 text-gray-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <div x-show="openFilter" x-transition x-cloak class="absolute w-full mt-1 bg-white border border-gray-300 rounded-[5px] shadow-lg overflow-hidden py-1 z-50">
                            <label class="block px-3 py-2 text-[12px] hover:bg-gray-100 cursor-pointer text-black"><input type="radio" value="all" x-model="filterStatusUpload" class="hidden" @change="openFilter = false">Semua Status</label>
                            <label class="block px-3 py-2 text-[12px] hover:bg-gray-100 cursor-pointer text-black"><input type="radio" value="sudah" x-model="filterStatusUpload" class="hidden" @change="openFilter = false">Sudah Mendaftar</label>
                            <label class="block px-3 py-2 text-[12px] hover:bg-gray-100 cursor-pointer text-black"><input type="radio" value="belum" x-model="filterStatusUpload" class="hidden" @change="openFilter = false">Belum Mendaftar</label>
                        </div>
                    </div>

                    <div class="relative shrink-0" x-data="{ exportOpen: false }" @click.outside="exportOpen = false">
                        <button @click="exportOpen = !exportOpen" class="bg-[#EA4335] hover:bg-red-700 text-white px-4 py-1.5 rounded-[5px] text-[12px] font-bold flex items-center shadow-sm uppercase transition-colors">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                            Cetak PDF
                        </button>
                        <div x-cloak x-show="exportOpen" class="absolute right-0 mt-2 w-52 bg-white rounded-[8px] shadow-xl border border-gray-200 z-[70] overflow-hidden">
                            <div class="bg-gray-50 px-4 py-2.5 text-[10px] font-bold text-gray-400 uppercase tracking-wider border-b border-gray-200">Format Laporan</div>
                            <button @click="exportPDF('all'); exportOpen = false" class="w-full text-left px-4 py-2.5 hover:bg-gray-50 text-[12px] text-black font-medium transition-colors">Semua Data</button>
                            <button @click="exportPDF('sudah'); exportOpen = false" class="w-full text-left px-4 py-2.5 hover:bg-gray-50 text-[12px] text-black font-medium transition-colors">Sudah Mendaftar</button>
                            <button @click="exportPDF('belum'); exportOpen = false" class="w-full text-left px-4 py-2.5 hover:bg-gray-50 text-[12px] text-black font-medium transition-colors border-t border-gray-100">Belum Mendaftar</button>
                        </div>
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
                            <th class="py-3 px-4 font-bold text-center border-b border-gray-300">Status Pendaftaran KP</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        <template x-for="(stat, index) in paginatedStatusRows" :key="stat.nim">
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="py-3 px-4 text-center text-black/60 border-r border-gray-200" x-text="(statusCurrentPage - 1) * statusItemsPerPage + index + 1"></td>
                                <td class="py-3 px-4 text-left font-medium text-black border-r border-gray-200" x-text="stat.nim"></td>
                                <td class="py-3 px-4 text-left font-normal text-black sentence-case border-r border-gray-200" x-text="stat.name"></td>
                                <td class="py-3 px-4 text-center">
                                    <template x-if="stat.status === 'Sudah Mendaftar'">
                                        <span class="inline-flex items-center gap-1.5 bg-green-100 text-green-700 px-4 py-1 rounded-full font-bold text-[10px] uppercase">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg> Sudah Mendaftar
                                        </span>
                                    </template>
                                    <template x-if="stat.status === 'Belum Mendaftar (Proyek Ada)'">
                                        <span class="inline-flex items-center gap-1.5 bg-orange-100 text-orange-700 px-4 py-1 rounded-full font-bold text-[10px] uppercase">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg> Belum (Proyek Ada)
                                        </span>
                                    </template>
                                    <template x-if="stat.status === 'Belum Mendaftar'">
                                        <span class="text-black/40 font-medium text-[11px] italic tracking-tight">Menunggu Pendaftaran ...</span>
                                    </template>
                                </td>
                            </tr>
                        </template>
                        <template x-if="filteredStatusRows.length === 0">
                            <tr>
                                <td colspan="4" class="py-12 text-center text-gray-400 italic font-medium bg-gray-50 uppercase tracking-widest">Tidak ada data ditemukan</td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>

            <!-- Pagination Footer Status -->
            <div class="px-6 py-4 bg-white flex items-center justify-between border-t border-gray-200" x-show="totalStatusPages > 1">
                <span class="text-[12px] font-medium text-black/50" x-text="`Halaman ${statusCurrentPage} dari ${totalStatusPages}`"></span>
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

    </div>

    @php
        $logoPath = public_path('images/logo.png');
        $logoData = '';
        if (file_exists($logoPath)) {
            $type = pathinfo($logoPath, PATHINFO_EXTENSION);
            $data = file_get_contents($logoPath);
            $logoData = 'data:image/' . $type . ';base64,' . base64_encode($data);
        } else {
            $logoData = 'https://upload.wikimedia.org/wikipedia/id/8/80/Logo_UKRIDA.png';
        }
        
        $koordinator = auth()->user();
        $sigData = '';
        if ($koordinator && $koordinator->signature_path) {
            $sp = public_path('storage/' . $koordinator->signature_path);
            if (file_exists($sp)) {
                $st = pathinfo($sp, PATHINFO_EXTENSION);
                $sigData = 'data:image/' . $st . ';base64,' . base64_encode(file_get_contents($sp));
            }
        }
        $koordName = $koordinator ? $koordinator->name : '-';
        $koordNidn = $koordinator && $koordinator->dosen ? $koordinator->dosen->nidn : '-';
    @endphp

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.8.2/jspdf.plugin.autotable.min.js"></script>

    <script>
        document.addEventListener('alpine:init', () => {
            window.pendaftaranScope = function() {
                return {
                    searchQuery: '{{ request('main.search') }}', 
                    searchQueryRejected: '{{ request('rejected.search') }}',
                    isSelectionMode: sessionStorage.getItem('kpSelectionMode') === 'true',
                    modalCatatanOpen: false,
                    modalFormEl: null,
                    modalPesan: '',
                    modalCatatanValue: '',

                    searchStatus: '',
                    filterStatusUpload: 'all',
                    statusCurrentPage: 1,
                    statusItemsPerPage: 10,
                    statusRows: @json($allStatusRows ?? []),

                    openModalCatatan(formElement, pesan) {
                        this.modalFormEl = formElement;
                        this.modalPesan = pesan;
                        this.modalCatatanValue = '';
                        this.modalCatatanOpen = true;
                    },
                    submitModalCatatan() {
                        let input = document.createElement('input');
                        input.type = 'hidden';
                        input.name = 'catatan';
                        input.value = this.modalCatatanValue;
                        this.modalFormEl.appendChild(input);
                        this.modalFormEl.submit();
                    },

                    get filteredStatusRows() {
                        let filtered = this.statusRows;
                        if (this.filterStatusUpload !== 'all') {
                            if (this.filterStatusUpload === 'sudah') {
                                filtered = filtered.filter(r => r.status === 'Sudah Mendaftar');
                            } else if (this.filterStatusUpload === 'belum') {
                                filtered = filtered.filter(r => r.status === 'Belum Mendaftar' || r.status === 'Belum Mendaftar (Proyek Ada)');
                            }
                        }
                        if (this.searchStatus.trim() !== '') {
                            const term = this.searchStatus.toLowerCase();
                            filtered = filtered.filter(r => 
                                (r.name && r.name.toLowerCase().includes(term)) || 
                                (r.nim && r.nim.toLowerCase().includes(term))
                            );
                        }
                        return filtered;
                    },
                    
                    get totalStatusPages() {
                        return Math.ceil(this.filteredStatusRows.length / this.statusItemsPerPage) || 1;
                    },

                    get paginatedStatusRows() {
                        const start = (this.statusCurrentPage - 1) * this.statusItemsPerPage;
                        return this.filteredStatusRows.slice(start, start + this.statusItemsPerPage);
                    },

                    nextStatusPage() { if (this.statusCurrentPage < this.totalStatusPages) this.statusCurrentPage++; },
                    prevStatusPage() { if (this.statusCurrentPage > 1) this.statusCurrentPage--; },
                    goToStatusPage(page) { this.statusCurrentPage = page; },

                    exportPDF(mode) {
                        let dataToExport = [];
                        if (mode === 'sudah') {
                            dataToExport = this.statusRows.filter(r => r.status === 'Sudah Mendaftar');
                        } else if (mode === 'belum') {
                            dataToExport = this.statusRows.filter(r => r.status === 'Belum Mendaftar' || r.status === 'Belum Mendaftar (Proyek Ada)');
                        } else {
                            dataToExport = this.statusRows;
                        }
                        
                        if (dataToExport.length === 0) {
                            alert('Tidak ada data untuk dieksport.');
                            return;
                        }
                        
                        if (typeof window.jspdf === 'undefined') {
                            alert('Modul PDF sedang dimuat...');
                            return;
                        }

                        const { jsPDF } = window.jspdf;
                        const doc = new jsPDF();
                        
                        const phpLogo = '{!! $logoData !!}';
                        const phpSig = '{!! $sigData !!}';
                        const phpName = '{!! addslashes($koordName) !!}';
                        const phpNidn = '{!! addslashes($koordNidn) !!}';

                        if (phpLogo) { try { doc.addImage(phpLogo, 'PNG', 14, 12, 18, 18); } catch(e) {} }
                        doc.setFont("helvetica", "bold");
                        doc.setFontSize(14);
                        doc.text("UNIVERSITAS KRISTEN KRIDA WACANA", 105, 18, { align: "center" });
                        doc.setFontSize(12);
                        doc.text("FAKULTAS TEKNOLOGI CERDAS", 105, 24, { align: "center" });
                        doc.text("PROGRAM STUDI INFORMATIKA", 105, 30, { align: "center" });
                        doc.line(14, 34, 196, 34);
                        
                        doc.setFontSize(11);
                        doc.text("DAFTAR STATUS PENDAFTARAN KP MAHASISWA", 105, 45, { align: "center" });
                        
                        const printDate = new Date().toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' });
                        doc.setFontSize(9);
                        doc.setFont("helvetica", "normal");
                        doc.text("Dicetak pada: " + printDate, 14, 55);
                        
                        const tableData = dataToExport.map((row, index) => [
                            index + 1,
                            row.nim,
                            row.name,
                            row.status
                        ]);
                        
                        doc.autoTable({
                            head: [['No', 'NIM', 'Nama Mahasiswa', 'Status']],
                            body: tableData,
                            startY: 60,
                            theme: 'grid',
                            headStyles: { fillColor: [66, 133, 244] } // Sesuai warna biru Pendaftaran KP
                        });
                        
                        let finalY = doc.lastAutoTable.finalY + 20;
                        if (finalY > 250) { doc.addPage(); finalY = 20; }
                        
                        doc.text("Jakarta, " + printDate, 140, finalY);
                        doc.text("Koordinator Kerja Praktik", 140, finalY + 5);
                        if (phpSig) { try { doc.addImage(phpSig, 'PNG', 140, finalY + 8, 35, 15); } catch(e) {} }
                        doc.setFont("helvetica", "bold");
                        doc.text(phpName, 140, finalY + 30);
                        doc.setFont("helvetica", "normal");
                        doc.text("NIDN: " + phpNidn, 140, finalY + 35);
                        
                        doc.save("Status_Pendaftaran_KP.pdf");
                    }
                }
            }
        });
    </script>
</x-dashboard-layout>