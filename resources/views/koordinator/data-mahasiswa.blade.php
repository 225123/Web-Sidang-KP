<x-dashboard-layout header="Data Mahasiswa KP" :userName="auth()->user()->name" roleName="KOORDINATOR KP">
    <x-slot:sidebar>
        @include('koordinator.components.sidebar', ['active' => 'data-mahasiswa'])
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

        <div class="bg-white rounded-[15px] border border-gray-200 shadow-sm overflow-hidden p-6 mb-8">
            <div class="mb-6 flex flex-col sm:flex-row justify-between items-start gap-4">
                <div>
                    <h3 class="text-[18px] font-bold text-black tracking-tight">Daftar Mahasiswa</h3>
                    <p class="text-[12px] text-black/60 font-medium mt-1">Berikut adalah daftar seluruh mahasiswa yang terdaftar dalam program Kerja Praktik beserta detail data akademik, instansi, dan progres bimbingan.</p>
                </div>
            </div>

            <div class="flex flex-col sm:flex-row justify-between items-center gap-6 mb-6">
                <div class="flex flex-wrap items-center gap-4 w-full sm:w-auto">
                    <div class="relative flex-1 sm:w-[300px]">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        </div>
                        <input type="text" x-model="searchQuery" @input="currentPage = 1" class="block w-full pl-9 pr-4 py-1.5 border border-gray-300 rounded-[5px] text-[12px] text-black focus:outline-none focus:ring-1 focus:ring-blue-500 h-[34px] shadow-sm" placeholder="Cari Nama, NIM, Judul, Instansi...">
                    </div>

                    <div x-data="{ openPembimbing: false }" class="relative w-full sm:w-[220px]" @click.outside="openPembimbing = false">
                        <button type="button" @click="openPembimbing = !openPembimbing" class="w-full text-[12px] border border-gray-300 rounded-[5px] py-1.5 px-3 bg-white text-black font-medium focus:outline-none focus:ring-1 focus:ring-blue-500 flex justify-between items-center text-left shadow-sm h-[34px]">
                            <span class="truncate" x-text="pembimbingFilter === 'all' ? 'Semua Pembimbing' : pembimbingFilter"></span>
                            <svg :class="openPembimbing ? 'rotate-0' : 'rotate-90'" class="w-3.5 h-3.5 transition-all duration-200 text-gray-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <div x-show="openPembimbing" x-transition x-cloak class="absolute w-full mt-1 bg-white border border-gray-300 rounded-[5px] shadow-lg overflow-y-auto max-h-[250px] py-1 z-50">
                            <label class="block px-3 py-2 text-[12px] hover:bg-gray-100 cursor-pointer text-black font-medium"><input type="radio" value="all" x-model="pembimbingFilter" class="hidden" @change="openPembimbing = false; currentPage = 1">Semua Pembimbing</label>
                            <template x-for="dosen in [...new Set(pendaftarans.map(p => p.pembimbing))].filter(d => d !== '-').sort()" :key="dosen">
                                <label class="block px-3 py-2 text-[12px] hover:bg-gray-100 cursor-pointer text-black font-medium"><input type="radio" :value="dosen" x-model="pembimbingFilter" class="hidden" @change="openPembimbing = false; currentPage = 1"><span x-text="dosen"></span></label>
                            </template>
                        </div>
                    </div>

                    <div class="flex gap-2 w-full sm:w-auto">
                        <button type="button" @click="searchQuery = ''; pembimbingFilter = 'all'; currentPage = 1" class="flex-1 sm:flex-none border border-[#EA4335] bg-[#EA4335] text-white hover:bg-red-600 transition-colors px-4 py-1.5 rounded-[5px] text-[12px] font-bold shadow-sm flex items-center justify-center">
                            Clear Filter
                        </button>
                    </div>
                </div>
            </div>

            <div class="border border-gray-200 rounded-[10px] overflow-hidden">
                <div class="overflow-x-auto">
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
                                <td class="py-3 px-4 text-center text-black font-bold text-[10px] border-r border-gray-200" x-text="p.pembimbing"></td>
                                <td class="py-3 px-4 text-center">
                                    <a :href="p.show_url" class="bg-[#3B5998] hover:bg-blue-800 text-white font-bold text-[10px] px-4 py-1.5 rounded-full shadow-sm transition-all uppercase tracking-wider">
                                        Lihat Detail
                                    </a>
                                </td>
                            </tr>
                        </template>
                        <template x-if="filteredList.length === 0">
                            <tr>
                                <td colspan="6" class="text-center py-20 text-gray-400 italic bg-gray-50 font-medium text-[12px]">
                                    Tidak ada data mahasiswa yang ditemukan.
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>

            <div class="px-6 py-4 bg-white border-t border-gray-200 flex items-center justify-between" x-show="totalPages > 1">
                <span class="text-[12px] font-medium text-black/50" x-text="(filteredList.length === 0 ? 0 : ((currentPage - 1) * itemsPerPage + 1)) + ' - ' + Math.min(currentPage * itemsPerPage, filteredList.length) + ' dari ' + filteredList.length + ' baris'"></span>
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
