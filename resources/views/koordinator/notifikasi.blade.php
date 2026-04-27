<x-dashboard-layout header="Notifikasi" userName="{{ auth()->user()->name }}" roleName="KOORDINATOR KP">
    <x-slot:sidebar>
        @include('koordinator.components.sidebar', ['active' => 'notifikasi'])
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
        itemsPerPage: 50,
        notifikasis: {{ \Illuminate\Support\Js::from($notifikasis->getCollection()->map(fn($n) => [
            'id' => $n->id,
            'pengirim' => $n->sender->name ?? 'Sistem',
            'judul' => $n->judul,
            'pesan' => $n->pesan,
            'is_read' => $n->is_read,
            'file_path' => $n->file_path ? asset('storage/'.$n->file_path) : null,
            'hari' => $n->created_at->isoFormat('dddd,'),
            'tanggal' => $n->created_at->isoFormat('DD MMMM YYYY'),
            'timestamp' => $n->created_at->timestamp,
            'url' => route('koordinator.notifikasi.show', $n->id)
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

            if (this.sortOrder === 'desc') {
                list.sort((a, b) => b.timestamp - a.timestamp);
            } else {
                list.sort((a, b) => a.timestamp - b.timestamp);
            }

            return list;
        },

        get totalPages() {
            return Math.ceil(this.filteredList.length / this.itemsPerPage) || 1;
        },

        get paginatedList() {
            const start = (this.currentPage - 1) * this.itemsPerPage;
            return this.filteredList.slice(start, start + this.itemsPerPage);
        },

        nextPage() { if (this.currentPage < this.totalPages) this.currentPage++; },
        prevPage() { if (this.currentPage > 1) this.currentPage--; },
        resetPagination() { this.currentPage = 1; }
    }">
        
        <!-- Filter Section -->
        <div class="mb-6">
            <!-- Row 1: Search Bar -->
            <div class="relative w-full mb-4">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </div>
                <input type="text" x-model="searchQuery" @input="resetPagination()" class="block w-full pl-10 pr-4 py-2 border border-gray-300 rounded-[5px] text-sm text-black focus:ring-blue-500 focus:border-blue-500 shadow-sm" placeholder="Cari pengirim atau pesan ..">
            </div>

            <!-- Row 2: Filters Row -->
            <div class="flex flex-wrap items-center gap-4">
                <div class="flex items-center gap-2">
                    <label class="text-[13px] font-bold text-black whitespace-nowrap">Status :</label>
                    <select x-model="statusFilter" @change="resetPagination()" class="w-[180px] text-[13px] border border-gray-300 rounded-[5px] py-2 px-3 bg-white text-black font-medium focus:outline-none focus:ring-1 focus:ring-blue-500 shadow-sm">
                        <option value="all">Semua status</option>
                        <option value="unread">Belum dibaca</option>
                        <option value="read">Telah dibaca</option>
                    </select>
                </div>
                <div class="flex items-center gap-2">
                    <label class="text-[13px] font-bold text-black whitespace-nowrap">Urutkan :</label>
                    <select x-model="sortOrder" @change="resetPagination()" class="w-[150px] text-[13px] border border-gray-300 rounded-[5px] py-2 px-3 bg-white text-black font-medium focus:outline-none focus:ring-1 focus:ring-blue-500 shadow-sm">
                        <option value="desc">Terbaru</option>
                        <option value="asc">Terlama</option>
                    </select>
                </div>
                <button @click="searchQuery = ''; statusFilter = 'all'; sortOrder = 'desc'; resetPagination()" class="bg-[#EA3323] hover:bg-red-700 text-white font-bold text-[12px] px-6 py-2 rounded-[5px] shadow-sm transition-colors whitespace-nowrap uppercase">
                    Clear Filter
                </button>
            </div>
        </div>

        <!-- Notification Table -->
        <div class="bg-white border border-gray-300 rounded-[5px] overflow-hidden shadow-sm mb-12">
            <div class="overflow-x-auto custom-scrollbar">
                <table class="w-full border-collapse text-[11px]">
                    <thead>
                        <tr class="bg-[#EBEBEB] text-black text-center">
                            <th class="py-3 px-4 border-b border-r border-gray-300 w-[50px]">No</th>
                            <th class="py-3 px-4 font-bold text-center border-b border-r border-gray-300 w-[200px]">Pengirim</th>
                            <th class="py-3 px-4 font-bold text-center border-b border-r border-gray-300">Pesan</th>
                            <th class="py-3 px-4 font-bold text-center border-b border-gray-300 w-[150px]">Tanggal</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        <template x-for="(notif, index) in paginatedList" :key="notif.id">
                            <tr class="hover:bg-gray-50 transition-colors" :class="!notif.is_read ? 'bg-gray-100' : ''">
                                <td class="py-3 px-4 border-r border-gray-200 text-center text-black/50" x-text="(currentPage - 1) * itemsPerPage + index + 1"></td>
                                <td class="py-3 px-4 border-r border-gray-200">
                                    <a :href="notif.url" class="block font-bold text-black text-[12px] truncate hover:text-blue-600" x-text="notif.pengirim"></a>
                                    <template x-if="notif.pengirim === 'Sistem'">
                                        <span class="text-blue-600 block text-[10px] uppercase font-bold mt-0.5">Sistem KP</span>
                                    </template>
                                </td>
                                <td class="py-3 px-4 border-r border-gray-200 text-left">
                                    <a :href="notif.url" class="flex flex-col gap-0.5 max-w-[600px] group">
                                        <div class="font-bold text-black text-[12px] truncate flex items-center gap-2 group-hover:text-blue-600">
                                            <template x-if="notif.file_path">
                                                <svg class="w-3 h-3 text-blue-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8 4a3 3 0 00-3 3v4a5 5 0 0010 0V7a1 1 0 112 0v4a7 7 0 11-14 0V7a5 5 0 0110 0v4a3 3 0 11-6 0V7a1 1 0 012 0v4a1 1 0 102 0V7a3 3 0 00-3-3z" clip-rule="evenodd"></path></svg>
                                            </template>
                                            <span x-text="notif.judul"></span>
                                        </div>
                                        <div class="text-black/60 text-[11px] truncate" x-text="notif.pesan"></div>
                                    </a>
                                </td>
                                <td class="py-3 px-4 text-center">
                                    <div class="text-black/70 font-medium leading-snug">
                                        <span x-text="notif.hari"></span><br>
                                        <span x-text="notif.tanggal"></span>
                                    </div>
                                </td>
                            </tr>
                        </template>
                        <template x-if="filteredList.length === 0">
                            <tr>
                                <td colspan="4" class="py-20 text-center text-gray-400 italic bg-gray-50 uppercase tracking-widest font-medium">
                                    Belum ada notifikasi masuk
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>

            <div class="px-6 py-4 bg-white border-t border-gray-200 flex items-center justify-between" x-show="totalPages > 1">
                <span class="text-[11px] font-medium text-black/50" x-text="`Halaman ${currentPage} dari ${totalPages}`"></span>
                <div class="flex items-center gap-2">
                    <button @click="prevPage" :disabled="currentPage === 1" class="px-3 py-1 border border-gray-300 rounded text-[11px] hover:bg-gray-50 disabled:opacity-30">Previous</button>
                    <button @click="nextPage" :disabled="currentPage === totalPages" class="px-3 py-1 border border-gray-300 rounded text-[11px] hover:bg-gray-50 disabled:opacity-30">Next</button>
                </div>
            </div>
        </div>

        <div class="h-20"></div>
    </div>
</x-dashboard-layout>
