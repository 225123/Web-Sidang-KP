<x-dashboard-layout header="Timeline KP" userName="{{ auth()->user()->name ?? 'Koordinator' }}" roleName="KOORDINATOR KP">
    <x-slot:sidebar>
        @include('koordinator.components.sidebar', ['active' => 'timeline'])
    </x-slot>

    <x-slot:headerActions>
        <div x-data="{ open: false, selected: 'Genap 2025/2026' }" class="relative w-[212px]">
            <button @click="open = !open" @click.outside="open = false" type="button"
                class="w-full flex items-center justify-between border border-[#CAC0C0] bg-[#FBFBFB] rounded-[5px] shadow-sm text-[13px] font-medium py-1.5 px-3 focus:outline-none focus:border-[#4CC098] focus:ring-1 focus:ring-[#4CC098] cursor-pointer text-black h-[32px]">

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

    <div class="mt-8 px-4 w-full max-w-6xl mx-auto pb-12 font-inter" x-data="timelinePage()">
        
        <!-- Section 1: Buat Timeline Box -->
        <div class="bg-white rounded-[15px] border border-gray-200 shadow-sm overflow-hidden p-6 mb-8">
            <div class="mb-6 border-b border-gray-100 pb-4">
                <h3 class="text-[18px] font-bold text-black tracking-tight">Buat Timeline</h3>
                <p class="text-[12px] text-black/60 font-medium mt-1">Buatlah jadwal timeline penting selama proses KP.</p>
            </div>
            <div class="px-2">
                <form action="{{ route('koordinator.timeline.store') }}" method="POST" class="space-y-4">
                    @csrf
                    <input type="hidden" name="kategori" :value="tab">
                    
                    <div class="flex items-center gap-4">
                        <label class="text-[13px] font-bold text-black w-[100px] flex-shrink-0">Tanggal :</label>
                        <input type="date" name="tanggal" required class="w-[200px] h-[32px] border border-[#CAC0C0] rounded-[5px] px-3 text-[13px] focus:outline-none focus:ring-1 focus:ring-blue-500 bg-[#FBFBFB]">
                    </div>

                    <div class="flex items-center gap-4">
                        <label class="text-[13px] font-bold text-black w-[100px] flex-shrink-0">Waktu :</label>
                        <input type="time" name="waktu" required class="w-[120px] h-[32px] border border-[#CAC0C0] rounded-[5px] px-3 text-[13px] focus:outline-none focus:ring-1 focus:ring-blue-500 bg-[#FBFBFB]">
                    </div>

                    <div class="flex items-center gap-4">
                        <label class="text-[13px] font-bold text-black w-[100px] flex-shrink-0">Kegiatan :</label>
                        <input type="text" name="nama_kegiatan" placeholder="Ketik nama kegiatan..." required class="flex-1 h-[32px] border border-[#CAC0C0] rounded-[5px] px-3 text-[13px] focus:outline-none focus:ring-1 focus:ring-blue-500 bg-[#FBFBFB]">
                    </div>

                    <div class="flex justify-end mt-2">
                        <button type="submit" class="h-[32px] px-6 bg-[#4CAF50] hover:bg-green-700 text-white font-bold text-[11px] rounded-[5px] transition-colors shadow-sm flex items-center justify-center">
                            Simpan Timeline
                        </button>
                    </div>
                </form>
            </div>
        </div>

        @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg relative mb-6 shadow-sm flex items-center gap-3">
            <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
            <span class="text-sm font-medium">{{ session('success') }}</span>
        </div>
        @endif

        <!-- Tabs -->
        <div class="flex items-end h-[36px]">
            <button @click="switchTab('mahasiswa')" 
               :class="tab === 'mahasiswa' ? 'bg-white border border-gray-200 border-b-white h-[36px] z-10 font-bold' : 'bg-gray-100 border border-gray-200 text-gray-500 h-[34px] hover:bg-gray-50 border-b-gray-200'"
               class="w-[120px] text-[14px] rounded-t-[10px] relative flex items-center justify-center transition-all">
               Mahasiswa
            </button>
            <button @click="switchTab('dosen')" 
               :class="tab === 'dosen' ? 'bg-white border border-gray-200 border-b-white h-[36px] z-10 font-bold text-black' : 'bg-gray-100 border border-gray-200 text-gray-500 h-[34px] hover:bg-gray-50 border-b-gray-200'"
               class="w-[120px] text-[14px] rounded-t-[10px] relative left-[-1px] flex items-center justify-center transition-all">
               Dosen
            </button>
        </div>

        <!-- Section 2: Main Background Container for Table and Filters -->
        <div class="bg-white rounded-b-[15px] rounded-tr-[15px] border border-gray-200 shadow-sm overflow-hidden relative top-[-1px] mb-8 p-6">
            <!-- Header Section inside container -->
            <div>
                <!-- Title & Description -->
                <div class="mb-6 flex flex-col sm:flex-row justify-between items-start gap-4">
                    <div>
                        <h3 class="text-[18px] font-bold text-black tracking-tight" x-text="tab === 'mahasiswa' ? 'Tabel Timeline Mahasiswa' : 'Tabel Timeline Dosen'"></h3>
                        <p class="text-[12px] text-black/60 font-medium mt-1">Daftar seluruh agenda dan jadwal kegiatan yang berkaitan dengan <span x-text="tab === 'mahasiswa' ? 'mahasiswa' : 'dosen'"></span> dalam pelaksanaan kerja praktik.</p>
                    </div>
                </div>

                <div class="flex flex-col sm:flex-row justify-between items-center gap-6 mb-6">
                    <div class="flex flex-wrap items-center gap-4 w-full sm:w-auto">
                        <!-- Search bar -->
                        <div class="relative flex-1 sm:w-[300px]">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                            </div>
                            <input type="text" x-model="search" @input="currentPage = 1" class="block w-full pl-9 pr-4 py-2 border border-gray-300 rounded-[5px] text-[12px] text-black focus:ring-[#4285F4] shadow-sm" placeholder="Cari nama kegiatan...">
                        </div>

                        <!-- Urutkan Filter -->
                        <div x-data="{ openSort: false }" class="relative w-full sm:w-[150px] z-[50]" @click.outside="openSort = false">
                            <button type="button" @click="openSort = !openSort" class="w-full text-[12px] border border-gray-300 rounded-[5px] py-2 px-3 bg-white text-black font-medium focus:ring-[#4285F4] flex justify-between items-center text-left shadow-sm">
                                <span class="truncate" x-text="sort === 'closest' ? 'Terdekat' : 'Terlama'"></span>
                                <svg :class="openSort ? 'rotate-0' : 'rotate-90'" class="w-3.5 h-3.5 transition-all duration-200 text-gray-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                            </button>
                            <div x-show="openSort" x-transition x-cloak class="absolute w-full mt-1 bg-white border border-gray-300 rounded-[5px] shadow-lg overflow-hidden py-1 z-50">
                                <label class="block px-3 py-2 text-[12px] hover:bg-gray-100 cursor-pointer text-black"><input type="radio" value="closest" x-model="sort" class="hidden" @change="openSort = false; currentPage = 1">Terdekat</label>
                                <label class="block px-3 py-2 text-[12px] hover:bg-gray-100 cursor-pointer text-black"><input type="radio" value="oldest" x-model="sort" class="hidden" @change="openSort = false; currentPage = 1">Terlama</label>
                            </div>
                        </div>

                        <div class="flex gap-2">
                            <button @click="toggleSelection()" 
                                    :class="isSelecting ? 'bg-[#4285F4] text-white border-[#4285F4]' : 'bg-white text-black border-gray-300 hover:bg-gray-100'"
                                    class="py-1.5 border font-bold text-[12px] px-4 rounded-[5px] shadow-sm transition-colors uppercase flex items-center justify-center">
                                <span x-text="isSelecting ? 'Batal' : 'Pilih Multiple'"></span>
                            </button>

                            <button x-show="isSelecting" @click="selectAllToggle()" 
                                    class="py-1.5 bg-[#EBEBEB] hover:bg-gray-300 text-black border border-[#CAC0C0] font-bold text-[12px] px-4 rounded-[5px] shadow-sm transition-colors uppercase flex items-center justify-center">
                                <span x-text="allSelected ? 'Batal Pilih Semua' : 'Pilih Semua'"></span>
                            </button>
                        </div>
                    </div>

                    <div class="text-[11px] text-black/60 font-bold uppercase tracking-tight flex items-center gap-1 sm:w-auto w-full justify-end">
                        <span x-text="(totalEntries === 0 ? 0 : ((currentPage - 1) * perPage + 1)) + ' - ' + Math.min(currentPage * perPage, totalEntries) + ' dari ' + totalEntries + ' baris'"></span>
                    </div>
                </div>

                <!-- Table Section -->
                <div class="overflow-x-auto border border-[#CAC0C0] rounded-[5px]">
                    <table class="w-full text-center border-collapse text-[12px] min-w-[800px]">
                        <thead class="bg-[#EBEBEB] font-bold text-black border-b border-[#CAC0C0]">
                            <tr>
                                <th class="border-r border-[#CAC0C0] px-3 py-3 w-[80px]">
                                    <span x-show="!isSelecting">No</span>
                                    <span x-show="isSelecting">Pilih</span>
                                </th>
                                <th class="border-r border-[#CAC0C0] px-4 py-3 text-left">Nama Kegiatan</th>
                                <th class="border-r border-[#CAC0C0] px-4 py-3 w-[150px]">Tanggal</th>
                                <th class="border-r border-[#CAC0C0] px-4 py-3 w-[120px]">Waktu</th>
                                <th class="px-6 py-3 w-[100px]">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-[#CAC0C0]">
                            <template x-for="(item, index) in paginatedItems" :key="item.id">
                                <tr class="hover:bg-gray-50 transition-colors h-[50px]">
                                    <td class="border-r border-[#CAC0C0] px-3 py-3 font-medium">
                                        <div x-show="!isSelecting" x-text="startEntry + index"></div>
                                        <div x-show="isSelecting">
                                            <input type="checkbox" :value="item.id" x-model="selectedIds" class="w-4 h-4 text-blue-600 rounded border-gray-300 focus:ring-blue-500">
                                        </div>
                                    </td>
                                    <td class="border-r border-[#CAC0C0] px-4 py-3 text-left">
                                        <div class="text-black font-medium sentence-case" x-text="item.nama_kegiatan"></div>
                                    </td>
                                    <td class="border-r border-[#CAC0C0] px-4 py-3 text-black font-bold" x-text="formatDate(item.tanggal)"></td>
                                    <td class="border-r border-[#CAC0C0] px-4 py-3 text-black font-bold" x-text="formatTime(item.waktu)"></td>
                                    <td class="px-6 py-3 text-center">
                                        <div class="flex items-center justify-center gap-2">
                                            <button @click="openEditModal(item)" class="text-gray-400 hover:text-blue-600 transition-colors">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                            </button>
                                            <button @click="openDeleteSingle(item.id)" class="text-gray-400 hover:text-red-600 transition-colors">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            </template>
                            <template x-if="paginatedItems.length === 0">
                                <tr>
                                    <td colspan="5" class="py-12 text-gray-500 text-center font-medium italic italic">Belum ada timeline kegiatan yang dibuat...</td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>

                <!-- Footer Actions: Delete Button -->
                <div x-show="isSelecting && selectedIds.length > 0" class="mt-4 flex justify-start">
                    <button @click="openBulkDelete()" class="bg-[#EA3323] hover:bg-red-700 text-white font-bold text-[12px] px-8 py-2 rounded-[5px] shadow-sm transition-colors uppercase flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                        Hapus Yang Terpilih (<span x-text="selectedIds.length"></span>)
                    </button>
                </div>

                <!-- Pagination Footer -->
                <div class="pt-4 border-t border-gray-200 flex flex-col sm:flex-row items-center justify-between gap-4 mt-6" x-show="totalPages > 1">
                    <span class="text-[12px] font-medium text-black/50" x-text="(totalEntries === 0 ? 0 : ((currentPage - 1) * perPage + 1)) + ' - ' + Math.min(currentPage * perPage, totalEntries) + ' dari ' + totalEntries + ' baris'"></span>
                    <div class="flex items-center gap-2">
                        <button @click="prevPage()" :disabled="currentPage === 1" 
                            class="px-3 py-1 border border-gray-300 rounded text-[12px] hover:bg-gray-50 disabled:opacity-30 disabled:cursor-not-allowed transition-colors">Previous</button>
                        <div class="flex items-center gap-1">
                            <template x-for="p in totalPages" :key="p">
                                <button @click="currentPage = p" 
                                    class="w-8 h-8 rounded text-[12px] font-bold transition-all"
                                    :class="currentPage === p ? 'bg-blue-600 text-white shadow-md' : 'text-black hover:bg-gray-100'"
                                    x-text="p"></button>
                            </template>
                        </div>
                        <button @click="nextPage()" :disabled="currentPage === totalPages" 
                            class="px-3 py-1 border border-gray-300 rounded text-[12px] hover:bg-gray-50 disabled:opacity-30 disabled:cursor-not-allowed transition-colors">Next</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Edit Modal (Still useful for updating) -->
        <div x-show="showEditModal" style="display: none;" class="fixed inset-0 z-[150] flex items-center justify-center bg-black/40 backdrop-blur-sm p-4" x-transition>
            <div @click.away="showEditModal = false" class="bg-[#F4F3F3] border border-black/50 rounded-[30px] w-full max-w-[600px] shadow-2xl relative overflow-hidden">
                <div class="px-10 pt-8 pb-10">
                    <h2 class="text-[24px] font-bold text-black font-inter mb-8">Edit Timeline</h2>
                    <form :action="'/koordinator/timeline/' + editData.id" method="POST">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="kategori" :value="tab">
                        <div class="space-y-4">
                            <div class="flex flex-col">
                                <label class="text-[15px] text-black font-medium mb-1">Nama Kegiatan</label>
                                <input type="text" name="nama_kegiatan" x-model="editData.nama_kegiatan" required class="w-full h-[40px] bg-[#D9D9D9] px-3 text-[14px] text-black outline-none rounded-[5px]">
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div class="flex flex-col">
                                    <label class="text-[15px] text-black font-medium mb-1">Tanggal</label>
                                    <input type="date" name="tanggal" x-model="editData.tanggal" required class="w-full h-[40px] bg-[#D9D9D9] px-3 text-[14px] text-black outline-none rounded-[5px]">
                                </div>
                                <div class="flex flex-col">
                                    <label class="text-[15px] text-black font-medium mb-1">Waktu</label>
                                    <input type="time" name="waktu" x-model="editData.waktu" required class="w-full h-[40px] bg-[#D9D9D9] px-3 text-[14px] text-black outline-none rounded-[5px]">
                                </div>
                            </div>
                        </div>
                        <div class="flex justify-end gap-4 mt-10">
                            <button @click="showEditModal = false" type="button" class="w-[104px] h-[36px] bg-[#E32727] hover:bg-red-700 text-white font-medium text-[14px] rounded-[5px] transition-colors shadow-sm">Batal</button>
                            <button type="submit" class="w-[104px] h-[36px] bg-[#008000] hover:bg-green-700 text-white font-medium text-[14px] rounded-[5px] transition-colors shadow-sm">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Global Confirm Modal -->
        <div x-show="showConfirmModal" style="display: none;" class="fixed inset-0 z-[200] flex items-center justify-center bg-black/40 backdrop-blur-sm p-4" x-transition>
            <div @click.away="showConfirmModal = false" class="bg-white rounded-[10px] w-full max-w-[450px] p-8 shadow-2xl flex flex-col items-center justify-center text-center">
                <div class="mb-5">
                    <svg class="w-16 h-16 text-[#E53935]" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M1 21h22L12 2 1 21zm12-3h-2v-2h2v2zm0-4h-2v-4h2v4z"/>
                    </svg>
                </div>
                <h3 class="text-black font-semibold text-[16px] mb-8" x-text="confirmMessage"></h3>
                <div class="flex gap-4 w-full justify-center">
                    <button @click="showConfirmModal = false" type="button" class="w-[100px] h-[34px] bg-[#E32727] hover:bg-red-700 text-white rounded-[5px] text-[14px] font-medium transition-colors shadow-sm">Batal</button>
                    <button @click="executeConfirm()" type="button" class="w-[100px] h-[34px] bg-[#456DA7] hover:bg-blue-700 text-white rounded-[5px] text-[14px] font-medium transition-colors shadow-sm">Iya</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        function timelinePage() {
            return {
                tab: 'mahasiswa',
                search: '',
                sort: 'closest',
                isSelecting: false,
                selectedIds: [],
                allSelected: false,
                currentPage: 1,
                perPage: 15,
                
                showEditModal: false,
                editData: { id: null, nama_kegiatan: '', tanggal: '', waktu: '' },
                
                showConfirmModal: false,
                confirmMessage: '',
                confirmAction: null,
                
                timelineMahasiswa: @json($timelineMahasiswa),
                timelineDosen: @json($timelineDosen),

                switchTab(newTab) {
                    this.tab = newTab;
                    this.currentPage = 1;
                    this.isSelecting = false;
                    this.selectedIds = [];
                },

                get currentItems() {
                    return this.tab === 'mahasiswa' ? this.timelineMahasiswa : this.timelineDosen;
                },

                get filteredItems() {
                    let res = [...this.currentItems];
                    if (this.search) {
                        const q = this.search.toLowerCase();
                        res = res.filter(item => item.nama_kegiatan.toLowerCase().includes(q));
                    }
                    
                    // Sort
                    res.sort((a, b) => {
                        const dateA = new Date(a.tanggal + ' ' + a.waktu);
                        const dateB = new Date(b.tanggal + ' ' + b.waktu);
                        return this.sort === 'closest' ? dateA - dateB : dateB - dateA;
                    });

                    return res;
                },

                get paginatedItems() {
                    const start = (this.currentPage - 1) * this.perPage;
                    return this.filteredItems.slice(start, start + this.perPage);
                },

                get totalPages() { return Math.ceil(this.filteredItems.length / this.perPage) || 1; },
                get totalEntries() { return this.filteredItems.length; },
                get startEntry() { return this.totalEntries === 0 ? 0 : (this.currentPage - 1) * this.perPage + 1; },
                get endEntry() { return Math.min(this.currentPage * this.perPage, this.totalEntries); },

                nextPage() { if (this.currentPage < this.totalPages) this.currentPage++; },
                prevPage() { if (this.currentPage > 1) this.currentPage--; },

                formatDate(dateString) {
                    if (!dateString) return '-';
                    const date = new Date(dateString);
                    return date.toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' });
                },

                formatTime(timeString) {
                    if (!timeString) return '-';
                    return timeString.substring(0, 5) + ' WIB';
                },

                toggleSelection() {
                    this.isSelecting = !this.isSelecting;
                    if (!this.isSelecting) {
                        this.selectedIds = [];
                        this.allSelected = false;
                    }
                },

                selectAllToggle() {
                    this.allSelected = !this.allSelected;
                    if (this.allSelected) {
                        this.selectedIds = this.paginatedItems.map(item => item.id);
                    } else {
                        this.selectedIds = [];
                    }
                },

                openEditModal(item) {
                    this.editData = {
                        id: item.id,
                        nama_kegiatan: item.nama_kegiatan,
                        tanggal: item.tanggal.split('T')[0],
                        waktu: item.waktu.substring(0, 5)
                    };
                    this.showEditModal = true;
                },

                openDeleteSingle(id) {
                    this.confirmMessage = 'Apakah Anda yakin ingin menghapus kegiatan ini?';
                    this.confirmAction = () => this.deleteSingle(id);
                    this.showConfirmModal = true;
                },

                openBulkDelete() {
                    this.confirmMessage = `Apakah Anda yakin ingin menghapus ${this.selectedIds.length} kegiatan yang terpilih?`;
                    this.confirmAction = () => this.deleteBulk();
                    this.showConfirmModal = true;
                },

                executeConfirm() {
                    if (this.confirmAction) this.confirmAction();
                    this.showConfirmModal = false;
                },

                async deleteSingle(id) {
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = `/koordinator/timeline/${id}`;
                    form.innerHTML = `<input type="hidden" name="_token" value="{{ csrf_token() }}"><input type="hidden" name="_method" value="DELETE">`;
                    document.body.appendChild(form);
                    form.submit();
                },

                async deleteBulk() {
                    try {
                        const response = await fetch('/koordinator/timeline/bulk-destroy', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'X-Requested-With': 'XMLHttpRequest'
                            },
                            body: JSON.stringify({ ids: this.selectedIds })
                        });
                        const data = await response.json();
                        if (data.success) {
                            window.location.reload();
                        }
                    } catch (error) {
                        console.error('Error deleting bulk:', error);
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
