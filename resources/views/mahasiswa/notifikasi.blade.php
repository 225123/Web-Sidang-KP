<x-dashboard-layout header="Notifikasi" userName="{{ auth()->user()->name }}" roleName="MAHASISWA" hidePeriodSelector="true" :noTurboCache="true">
    <x-slot:sidebar>
        @include('mahasiswa.components.sidebar', ['active' => 'notifikasi'])
    </x-slot>

    

    <style>
        [x-cloak] { display: none !important; }
        .custom-scrollbar::-webkit-scrollbar { width: 5px; height: 5px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: #f1f1f1; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #ccc; border-radius: 10px; }
    </style>

    <div class="mt-8 px-4 w-full" x-data="{ 
        searchQuery: '',
        statusFilter: 'all',
        sortOrder: 'desc',
        currentPage: 1,
        itemsPerPage: 15,
        notifikasis: {{ \Illuminate\Support\Js::from($notifikasis->getCollection()->map(fn($n) => [
            'id' => $n->id,
            'pengirim' => $n->sender->name ?? 'Sistem',
            'judul' => $n->judul,
            'pesan' => $n->pesan,
            'pesan_preview' => strlen($n->pesan) > 100 ? substr($n->pesan, 0, 100) . '...' : $n->pesan,
            'is_read' => $n->is_read,
            'file_path' => $n->file_path ? storage_url($n->file_path) : null,
            'hari' => $n->created_at->isoFormat('dddd,'),
            'tanggal' => $n->created_at->isoFormat('DD MMMM YYYY'),
            'timestamp' => $n->created_at->timestamp,
            'url' => route('mahasiswa.notifikasi.show', $n->id)
        ])) }},

        get filteredList() {
            let list = this.notifikasis.filter(n => {
                const term = this.searchQuery.toLowerCase();
                const matchesSearch = n.pengirim.toLowerCase().includes(term) || 
                                    n.judul.toLowerCase().includes(term) || 
                                    n.pesan.toLowerCase().includes(term);
                const matchesStatus = this.statusFilter === 'all' || 
                                     (this.statusFilter === 'read' && n.is_read) || 
                                     (this.statusFilter === 'unread' && !n.is_read);
                return matchesSearch && matchesStatus;
            });
            list.sort((a, b) => this.sortOrder === 'desc' ? b.timestamp - a.timestamp : a.timestamp - b.timestamp);
            return list;
        },

        get totalPages() { return Math.ceil(this.filteredList.length / this.itemsPerPage) || 1; },
        get paginatedList() {
            const start = (this.currentPage - 1) * this.itemsPerPage;
            return this.filteredList.slice(start, start + this.itemsPerPage);
        },
        resetPagination() { this.currentPage = 1; }
    }">
        
        <div class="bg-white rounded-[15px] border border-gray-200 shadow-sm overflow-hidden p-6 mb-8">
            <div class="mb-6 flex flex-col sm:flex-row justify-between items-start gap-4">
                <div>
                    <h3 class="text-[18px] font-bold text-black tracking-tight uppercase">Daftar Notifikasi</h3>
                    <p class="text-[12px] text-black/60 font-medium mt-1">Daftar pesan dan pemberitahuan sistem masuk.</p>
                </div>
                <div class="flex gap-2 shrink-0">
                    <div class="bg-[#EA4335] text-white rounded-[5px] px-3 py-1.5 flex items-center gap-2 shadow-sm border border-red-600/20 cursor-pointer hover:scale-105 transition-transform" @click="statusFilter = 'unread'; resetPagination()">
                        <span class="text-[16px] font-bold leading-none" x-text="notifikasis.filter(n => !n.is_read).length"></span>
                        <span class="text-[11px] font-medium uppercase tracking-wider">Belum Dibaca</span>
                    </div>
                </div>
            </div>

            <div class="flex flex-col sm:flex-row justify-between items-center gap-6 mb-6">
                <div class="flex flex-wrap items-center gap-4 w-full sm:w-auto">
                    <!-- Search Input -->
                    <div class="relative flex-1 sm:w-[300px]">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        </div>
                        <input type="text" x-model="searchQuery" @input="resetPagination()" class="block w-full pl-9 pr-4 py-2 border border-gray-300 rounded-[5px] text-[12px] text-black focus:ring-[#4285F4]" placeholder="Cari pengirim atau pesan...">
                    </div>

                    <!-- Status Dropdown -->
                    <div x-data="{ openStatus: false }" class="relative w-full sm:w-[150px] z-[60]" @click.outside="openStatus = false">
                        <button type="button" @click="openStatus = !openStatus" class="w-full text-[12px] border border-gray-300 rounded-[5px] py-2 px-3 bg-white text-black font-medium flex justify-between items-center text-left shadow-sm">
                            <span x-text="statusFilter === 'all' ? 'Semua Status' : (statusFilter === 'unread' ? 'Belum Dibaca' : 'Telah Dibaca')"></span>
                            <svg :class="openStatus ? 'rotate-0' : 'rotate-90'" class="w-3.5 h-3.5 transition-all duration-200 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <div x-show="openStatus" x-transition x-cloak class="absolute w-full mt-1 bg-white border border-gray-300 rounded-[5px] shadow-lg py-1 z-50">
                            <label class="block px-3 py-2 text-[12px] hover:bg-gray-100 cursor-pointer"><input type="radio" value="all" x-model="statusFilter" class="hidden" @change="openStatus = false; resetPagination()">Semua Status</label>
                            <label class="block px-3 py-2 text-[12px] hover:bg-gray-100 cursor-pointer"><input type="radio" value="unread" x-model="statusFilter" class="hidden" @change="openStatus = false; resetPagination()">Belum Dibaca</label>
                            <label class="block px-3 py-2 text-[12px] hover:bg-gray-100 cursor-pointer"><input type="radio" value="read" x-model="statusFilter" class="hidden" @change="openStatus = false; resetPagination()">Telah Dibaca</label>
                        </div>
                    </div>

                    <!-- Sort Dropdown -->
                    <div x-data="{ openSort: false }" class="relative w-full sm:w-[130px] z-[50]" @click.outside="openSort = false">
                        <button type="button" @click="openSort = !openSort" class="w-full text-[12px] border border-gray-300 rounded-[5px] py-2 px-3 bg-white text-black font-medium flex justify-between items-center text-left shadow-sm">
                            <span x-text="sortOrder === 'desc' ? 'Terbaru' : 'Terlama'"></span>
                            <svg :class="openSort ? 'rotate-0' : 'rotate-90'" class="w-3.5 h-3.5 transition-all duration-200 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <div x-show="openSort" x-transition x-cloak class="absolute w-full mt-1 bg-white border border-gray-300 rounded-[5px] shadow-lg py-1 z-50">
                            <label class="block px-3 py-2 text-[12px] hover:bg-gray-100 cursor-pointer"><input type="radio" value="desc" x-model="sortOrder" class="hidden" @change="openSort = false; resetPagination()">Terbaru</label>
                            <label class="block px-3 py-2 text-[12px] hover:bg-gray-100 cursor-pointer"><input type="radio" value="asc" x-model="sortOrder" class="hidden" @change="openSort = false; resetPagination()">Terlama</label>
                        </div>
                    </div>
                </div>

                <button type="button" @click="searchQuery = ''; statusFilter = 'all'; sortOrder = 'desc'; resetPagination()" class="bg-[#EA4335] text-white hover:bg-red-600 px-4 py-1.5 rounded-[5px] text-[12px] font-bold shadow-sm">Clear Filter</button>
            </div>

            <div class="border border-gray-200 rounded-[10px] overflow-hidden">
                <div class="overflow-x-auto custom-scrollbar">
                    <table class="w-full border-collapse text-[12px] min-w-[800px]">
                        <thead>
                            <tr class="bg-[#EBEBEB] text-black text-center">
                                <th class="py-3 px-4 border-b border-r border-gray-300 w-[50px]">No</th>
                                <th class="py-3 px-4 font-bold border-b border-r border-gray-300 w-[200px]">Pengirim</th>
                                <th class="py-3 px-4 font-bold border-b border-r border-gray-300">Pesan</th>
                                <th class="py-3 px-4 font-bold border-b border-r border-gray-300 w-[150px]">Tanggal</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            <template x-for="(notif, index) in paginatedList" :key="notif.id">
                                <tr class="hover:bg-gray-50 transition-colors relative" :class="!notif.is_read ? 'bg-blue-50/40' : ''">
                                    <td class="py-3 px-4 border-r border-gray-200 text-center text-black/50" x-text="(currentPage - 1) * itemsPerPage + index + 1"></td>
                                    <td class="py-3 px-4 border-r border-gray-200">
                                        <div class="flex items-center gap-2">
                                            <template x-if="!notif.is_read">
                                                <div class="w-2 h-2 bg-blue-600 rounded-full shrink-0 shadow-sm"></div>
                                            </template>
                                            <a :href="notif.url" class="block font-bold text-black text-[12px] hover:text-blue-600 truncate" x-text="notif.pengirim"></a>
                                        </div>
                                    </td>
                                    <td class="py-3 px-4 border-r border-gray-200 text-left">
                                        <a :href="notif.url" class="flex flex-col gap-0.5 group max-w-[600px]">
                                            <div class="font-bold text-black text-[12px] flex items-center gap-2 group-hover:text-blue-600 truncate">
                                                <span x-text="notif.judul"></span>
                                            </div>
                                            <div class="text-black/60 text-[11px] line-clamp-1" x-text="notif.pesan_preview"></div>
                                        </a>
                                    </td>
                                    <td class="py-3 px-4 text-center">
                                        <div class="text-black/70 font-medium leading-snug">
                                            <span x-text="notif.hari"></span><br><span x-text="notif.tanggal"></span>
                                        </div>
                                    </td>
                                </tr>
                            </template>
                            <template x-if="filteredList.length === 0">
                                <tr><td colspan="4" class="py-20 text-center text-gray-400 italic bg-gray-50 uppercase tracking-widest font-medium">Belum ada notifikasi masuk</td></tr>
                            </template>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="px-6 py-4 border-t border-gray-200 flex items-center justify-between mt-4" x-show="totalPages > 1">
                <span class="text-[12px] font-medium text-black/50" x-text="(filteredList.length === 0 ? 0 : ((currentPage - 1) * itemsPerPage + 1)) + ' - ' + Math.min(currentPage * itemsPerPage, filteredList.length) + ' dari ' + filteredList.length + ' baris'"></span>
                <div class="flex items-center gap-2">
                    <button @click="currentPage > 1 && currentPage--" :disabled="currentPage === 1" class="px-3 py-1 border border-gray-300 rounded text-[12px] hover:bg-gray-50 disabled:opacity-30 disabled:cursor-not-allowed">Previous</button>
                    <div class="flex gap-1">
                        <template x-for="p in totalPages" :key="p">
                            <button @click="currentPage = p" class="w-8 h-8 rounded text-[12px] font-bold" :class="currentPage === p ? 'bg-blue-600 text-white shadow-md' : 'text-black hover:bg-gray-100'" x-text="p"></button>
                        </template>
                    </div>
                    <button @click="currentPage < totalPages && currentPage++" :disabled="currentPage === totalPages" class="px-3 py-1 border border-gray-300 rounded text-[12px] hover:bg-gray-50 disabled:opacity-30 disabled:cursor-not-allowed">Next</button>
                </div>
            </div>
        </div>
    </div>
</x-dashboard-layout>
