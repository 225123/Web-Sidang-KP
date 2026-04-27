<x-dashboard-layout header="Data Mahasiswa KP" :userName="auth()->user()->name" roleName="KOORDINATOR KP">
    <x-slot:sidebar>
        @include('koordinator.components.sidebar', ['active' => 'data-mahasiswa'])
    </x-slot>

    <style>
        [x-cloak] { display: none !important; }
        .custom-scrollbar::-webkit-scrollbar { width: 5px; height: 5px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: #f1f1f1; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #ccc; border-radius: 10px; }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #999; }
    </style>

    <div class="mt-8 px-4 w-full" x-data="{ 
        searchQuery: '',
        pembimbingFilter: 'all',
        itemsPerPage: 15,
        currentPage: 1,
        pendaftarans: {{ \Illuminate\Support\Js::from($pendaftarans->map(fn($p) => [
            'id' => $p->id,
            'nama' => $p->mahasiswa->user->name ?? '-',
            'nim' => $p->mahasiswa->nim ?? '-',
            'judul' => $p->judul_kp ?? '-',
            'instansi' => $p->instansi_nama ?? '-',
            'pembimbing' => $p->pembimbing->name ?? '-',
            'status' => $p->status_kp,
            'show_url' => route('koordinator.data-mahasiswa.show', $p->id)
        ])) }},

        get filteredList() {
            return this.pendaftarans.filter(p => {
                const term = this.searchQuery.toLowerCase();
                const matchesSearch = !this.searchQuery || 
                    p.nama.toLowerCase().includes(term) ||
                    p.nim.toLowerCase().includes(term) ||
                    p.judul.toLowerCase().includes(term) ||
                    p.instansi.toLowerCase().includes(term);
                
                const matchesPembimbing = this.pembimbingFilter === 'all' || p.pembimbing === this.pembimbingFilter;
                return matchesSearch && matchesPembimbing;
            });
        },

        get paginatedList() {
            const start = (this.currentPage - 1) * this.itemsPerPage;
            return this.filteredList.slice(start, start + this.itemsPerPage);
        },

        get totalPages() {
            return Math.ceil(this.filteredList.length / this.itemsPerPage) || 1;
        }
    }">

        <!-- Page Description Box -->
        <div class="bg-[#E6F0FA] border border-[#D0E3F5] rounded-[10px] p-4 lg:p-5 mb-8 flex items-start gap-4 shadow-sm">
            <div class="w-6 h-6 rounded-full bg-[#4285F4] text-white flex items-center justify-center font-bold flex-shrink-0 mt-0.5">i</div>
            <p class="text-[14px] text-[#1A1A1A] font-medium leading-relaxed m-0 mt-0.5">
                Berikut adalah daftar seluruh mahasiswa yang terdaftar dalam program Kerja Praktik. Anda dapat melihat detail data akademik, instansi, serta progres bimbingan masing-masing mahasiswa.
            </p>
        </div>

        <!-- Table Box -->
        <div class="bg-white border border-gray-300 rounded-[10px] overflow-hidden shadow-sm mb-12">
            <div class="bg-[#EBEBEB] border-b border-gray-300 p-4 lg:p-6">
                <div class="flex flex-wrap items-center gap-4">
                    <div class="relative flex-1 min-w-[300px]">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        </div>
                        <input type="text" x-model="searchQuery" @input="currentPage = 1"
                            class="block w-full pl-9 pr-4 py-2 border border-gray-300 rounded-[5px] text-[13px] text-black focus:ring-1 focus:ring-blue-500 shadow-sm"
                            placeholder="Cari Nama, NIM, Judul, Instansi...">
                    </div>

                    <button @click="searchQuery = ''; pembimbingFilter = 'all'; currentPage = 1"
                        class="bg-[#EA3323] hover:bg-red-700 text-white font-bold text-[11px] px-5 py-2.5 rounded-[5px] shadow-md transition-all uppercase flex items-center gap-2">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path></svg>
                        Clear Filter
                    </button>
                </div>
            </div>

            <div class="overflow-x-auto custom-scrollbar">
                <table class="w-full border-collapse text-[11px]">
                    <thead>
                        <tr class="bg-[#EBEBEB] text-black text-center">
                            <th class="py-3 px-4 font-bold w-[50px] border-b border-gray-300 border-r border-gray-300">No</th>
                            <th class="py-3 px-4 font-bold text-left border-b border-gray-300 border-r border-gray-300">Mahasiswa</th>
                            <th class="py-3 px-4 font-bold border-b border-gray-300 border-r border-gray-300">Judul KP</th>
                            <th class="py-3 px-4 font-bold border-b border-gray-300 border-r border-gray-300">Instansi</th>
                            <th class="py-3 px-4 font-bold border-b border-gray-300 border-r border-gray-300">Pembimbing</th>
                            <th class="py-3 px-4 font-bold border-b border-gray-300">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        <template x-for="(p, index) in paginatedList" :key="p.id">
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="py-3 px-4 text-center text-black/60 border-r border-gray-200" x-text="(currentPage - 1) * itemsPerPage + index + 1"></td>
                                <td class="py-3 px-4 text-left border-r border-gray-200">
                                    <div class="font-bold text-black text-[12px]" x-text="p.nama"></div>
                                    <div class="text-black/60 text-[10px]" x-text="p.nim"></div>
                                </td>
                                <td class="py-3 px-4 text-center text-black/80 font-medium leading-relaxed border-r border-gray-200 text-[11px]" x-text="p.judul"></td>
                                <td class="py-3 px-4 text-center text-black/70 italic font-medium border-r border-gray-200 text-[11px]" x-text="p.instansi"></td>
                                <td class="py-3 px-4 text-center text-blue-800 font-bold text-[10px] border-r border-gray-200" x-text="p.pembimbing"></td>
                                <td class="py-3 px-4 text-center">
                                    <a :href="p.show_url" class="bg-[#3B5998] hover:bg-blue-800 text-white font-bold text-[10px] px-4 py-1.5 rounded-full shadow-sm transition-all uppercase tracking-wider">
                                        Lihat Detail
                                    </a>
                                </td>
                            </tr>
                        </template>
                        <template x-if="filteredList.length === 0">
                            <tr>
                                <td colspan="6" class="text-center py-20 text-gray-400 italic bg-gray-50 uppercase tracking-widest font-medium">
                                    Tidak ada data mahasiswa yang ditemukan
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>

            <div class="px-6 py-4 bg-white border-t border-gray-200 flex items-center justify-between" x-show="totalPages > 1">
                <span class="text-[12px] font-medium text-black/50" x-text="`Halaman ${currentPage} dari ${totalPages}`"></span>
                <div class="flex items-center gap-2">
                    <button @click="currentPage--" :disabled="currentPage === 1" 
                        class="px-3 py-1 border border-gray-300 rounded text-[12px] hover:bg-gray-50 disabled:opacity-30 disabled:cursor-not-allowed transition-colors">Previous</button>
                    <div class="flex items-center gap-1">
                        <template x-for="p in totalPages" :key="p">
                            <button @click="currentPage = p" 
                                class="w-8 h-8 rounded text-[12px] font-bold transition-all"
                                :class="currentPage === p ? 'bg-blue-600 text-white shadow-md' : 'text-black hover:bg-gray-100'"
                                x-text="p"></button>
                        </template>
                    </div>
                    <button @click="currentPage++" :disabled="currentPage === totalPages" 
                        class="px-3 py-1 border border-gray-300 rounded text-[12px] hover:bg-gray-50 disabled:opacity-30 disabled:cursor-not-allowed transition-colors">Next</button>
                </div>
            </div>
        </div>
    </div>
</x-dashboard-layout>
