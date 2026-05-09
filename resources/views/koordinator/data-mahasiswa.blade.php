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
            'pengerjaan_kp' => in_array(strtolower($p->pengerjaan_kp ?? ''), ['kelompok', 'berkelompok']) ? 'Kelompok' : 'Individu',
            'anggota_lain' => $p->anggotaLainList ?? [],
            'jenis_instansi' => $p->jenis_instansi ?? 'External',
            'supervisor' => $p->supervisorInstansi->nama_supervisor ?? '-',
            'email_supervisor' => $p->supervisorInstansi->email_supervisor ?? '-',
            'is_lanjutan' => $p->is_lanjutan ? true : false,
            'jenis_proyek' => $p->jenis_proyek ?? '-',
            'expanded' => false
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

        <div class="flex-1 bg-[#E6F0FA] border border-[#D0E3F5] rounded-[10px] p-4 lg:p-5 flex items-start gap-4 shadow-sm w-full mb-6">
            <div class="w-6 h-6 rounded-full bg-[#4285F4] text-white flex items-center justify-center font-bold flex-shrink-0 mt-0.5">i</div>
            <div class="flex flex-col gap-1">
                <p class="text-[14px] text-[#1A1A1A] font-medium leading-relaxed m-0">
                    Halaman ini menampilkan seluruh data mahasiswa KP secara lengkap. Silakan klik pada baris mahasiswa untuk melihat detail data KP.
                </p>
            </div>
        </div>

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
                            <th class="py-3 px-4 font-bold border-b border-gray-300">Pembimbing</th>
                        </tr>
                    </thead>
                    <template x-for="(p, index) in paginatedList" :key="p.id">
                        <tbody class="bg-white border-b border-gray-100 transition-colors">
                                <tr class="hover:bg-blue-50/40 transition-all duration-200 cursor-pointer group" @click="p.expanded = !p.expanded">
                                    <td class="py-3 px-4 text-center text-black/60 border-r border-gray-200" x-text="(currentPage - 1) * itemsPerPage + index + 1"></td>
                                    <td class="py-3 px-4 text-left border-r border-gray-200">
                                        <div class="flex items-center justify-between gap-2">
                                            <div>
                                                <div class="font-bold text-black text-[12px]" x-text="p.nama"></div>
                                                <div class="text-black/60 text-[10px]" x-text="p.nim"></div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="py-3 px-4 text-center text-black/80 font-medium leading-relaxed border-r border-gray-200 text-[11px]" x-text="p.judul"></td>
                                    <td class="py-3 px-4 text-center text-black/70 italic font-medium border-r border-gray-200 text-[11px]" x-text="p.instansi"></td>
                                    <td class="py-3 px-4 text-center text-black font-bold text-[10px]" x-text="p.pembimbing"></td>
                                </tr>
                                <tr>
                                    <td class="bg-[#F8FAFC] border-r border-gray-200"></td>
                                    <td colspan="4" class="p-0 border-0">
                                        <div class="grid transition-all duration-300 ease-in-out bg-[#F8FAFC]" :class="p.expanded ? 'grid-rows-[1fr] opacity-100' : 'grid-rows-[0fr] opacity-0'">
                                            <div class="overflow-hidden">
                                                <div class="p-6 border-t border-gray-100 shadow-[inset_0_4px_6px_-4px_rgba(0,0,0,0.05)] text-[12px] text-black space-y-4">
                                                    <h4 class="text-[12px] font-bold text-black border-b border-gray-200 pb-2 flex items-center gap-2">
                                                        <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                                        Informasi mahasiswa (pendaftaran kp)
                                                    </h4>
                                                    <div class="grid grid-cols-1 md:grid-cols-[180px_10px_1fr] gap-x-4 gap-y-3 font-medium text-[12px] leading-relaxed">
                                                        <div class="text-gray-500 font-medium md:text-black">Nama</div>
                                                        <div class="hidden md:block">:</div>
                                                        <div class="font-semibold text-black" x-text="p.nama"></div>

                                                        <div class="text-gray-500 font-medium md:text-black">NIM</div>
                                                        <div class="hidden md:block">:</div>
                                                        <div class="font-semibold text-black" x-text="p.nim"></div>

                                                        <div class="text-gray-500 font-medium md:text-black">Pengerjaan KP</div>
                                                        <div class="hidden md:block">:</div>
                                                        <div class="font-semibold text-black" x-text="p.pengerjaan_kp"></div>

                                                        <template x-if="p.pengerjaan_kp === 'Kelompok' && p.anggota_lain && p.anggota_lain.length">
                                                            <div class="contents">
                                                                <div class="text-gray-500 font-medium md:text-black align-top md:pt-[2px]">Anggota Kelompok</div>
                                                                <div class="hidden md:block align-top md:pt-[2px]">:</div>
                                                                <div class="font-semibold text-black md:pt-[2px]">
                                                                    <ul class="flex flex-col gap-1">
                                                                        <template x-for="ang in p.anggota_lain" :key="ang.nim">
                                                                            <li x-text="ang.nim + ' - ' + ang.nama"></li>
                                                                        </template>
                                                                    </ul>
                                                                </div>
                                                            </div>
                                                        </template>

                                                        <div class="text-gray-500 font-medium md:text-black">Jenis KP</div>
                                                        <div class="hidden md:block">:</div>
                                                        <div class="font-semibold text-black" x-text="p.jenis_instansi"></div>

                                                        <div class="text-gray-500 font-medium md:text-black">Nama Instansi</div>
                                                        <div class="hidden md:block">:</div>
                                                        <div class="font-semibold text-black" x-text="p.instansi"></div>

                                                        <div class="text-gray-500 font-medium md:text-black">Supervisor</div>
                                                        <div class="hidden md:block">:</div>
                                                        <div class="font-semibold text-black" x-text="p.supervisor"></div>

                                                        <template x-if="p.jenis_instansi !== 'Internal'">
                                                            <div class="contents">
                                                                <div class="text-gray-500 font-medium md:text-black">Email Supervisor</div>
                                                                <div class="hidden md:block">:</div>
                                                                <div class="font-semibold text-black" x-text="p.email_supervisor"></div>
                                                            </div>
                                                        </template>

                                                        <div class="text-gray-500 font-medium md:text-black">Judul KP</div>
                                                        <div class="hidden md:block">:</div>
                                                        <div class="font-semibold text-black" x-text="p.judul"></div>

                                                        <div class="text-gray-500 font-medium md:text-black">Status KP</div>
                                                        <div class="hidden md:block">:</div>
                                                        <div class="font-bold" :class="p.is_lanjutan ? 'text-red-500' : 'text-green-600'" x-text="p.is_lanjutan ? 'Lanjutan' : 'Baru'"></div>

                                                        <div class="text-gray-500 font-medium md:text-black align-top">Detail KP</div>
                                                        <div class="hidden md:block align-top">:</div>
                                                        <div class="font-semibold text-black leading-relaxed whitespace-pre-wrap" x-text="p.jenis_proyek"></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </template>
                        <tbody x-show="filteredList.length === 0">
                            <tr>
                                <td colspan="5" class="text-center py-20 text-gray-400 italic bg-gray-50 font-medium text-[12px]">
                                    Tidak ada data mahasiswa yang ditemukan.
                                </td>
                            </tr>
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
