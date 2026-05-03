<x-dashboard-layout header="Daftar Bimbingan Mahasiswa" userName="{{ auth()->user()->name }}" roleName="KOORDINATOR KP">
    <x-slot:sidebar>
        @include('koordinator.components.sidebar', ['active' => 'bimbingan-saya'])
        </x-slot>

        

        <style>
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

        <div class="mt-6" x-data="{ 
        searchQuery: '',
        statusFilter: 'all',
        pendaftarans: {{ \Illuminate\Support\Js::from($pendaftarans->map(fn($p) => [
    'id' => $p['id'],
    'nama' => $p['display_mahasiswa']->user->name ?? ($p['display_mahasiswa']->nama ?? '-'),
    'nim' => $p['display_mahasiswa']->nim ?? '-',
    'judul' => $p['display_judul_kp'] ?? '-',
    'instansi' => $p['display_instansi'] ?? '-',
    'supervisor' => $p['display_supervisor'] ?? '-',
    'total_log' => $p['total_log'] ?? 0,
    'status_label' => $p['status_approval_semua'] ?? '-',
    'detail_url' => $p['id'] ? route('koordinator.bimbingan-saya.detail', $p['id']) : '#'
])) }},
        get filteredList() {
            return this.pendaftarans.filter(p => {
                const term = (this.searchQuery || '').toLowerCase();
                const matchesSearch = !this.searchQuery || 
                    (p.nama || '').toLowerCase().includes(term) ||
                    (p.nim || '').toLowerCase().includes(term) ||
                    (p.judul || '').toLowerCase().includes(term) ||
                    (p.instansi || '').toLowerCase().includes(term) ||
                    (p.supervisor || '').toLowerCase().includes(term);
                
                const matchesStatus = this.statusFilter === 'all' || 
                    (this.statusFilter === 'diperiksa' && p.status_label === 'Diperiksa') ||
                    (this.statusFilter === 'pending' && p.status_label === 'Menunggu pengecekan');
                    
                return matchesSearch && matchesStatus;
            });
        }
    }">
            <!-- Unified Table Container -->
            <div class="bg-white rounded-[15px] border border-gray-200 shadow-sm overflow-hidden p-6 mb-8">
                <!-- Header Section -->
                <div class="border-b border-gray-200 pb-6 mb-6">
                    <!-- Title & Description Row with Stats -->
                    <div class="mb-8 flex flex-col sm:flex-row justify-between items-start gap-4">
                        <div>
                            <h3 class="text-[18px] font-bold text-black tracking-tight ">Tabel Daftar Bimbingan Mahasiswa</h3>
                            <p class="text-[12px] text-black/60 font-medium mt-1">Silahkan meninjau jumlah Mahasiswa
                                bimbingan Anda dan lakukan verifikasi bimbingan di dalam Log Bimbingan tiap mahasiswa.
                            </p>
                        </div>
                        <div class="flex flex-wrap gap-2 shrink-0">
                            <div
                                class="bg-[#38913B] text-white rounded-[5px] px-3 py-1.5 flex items-center gap-2 shadow-sm border border-green-600/20">
                                <span class="text-[16px] font-bold leading-none">{{ $jumlahSelesai ?? 0 }}</span>
                                <span class="text-[11px] font-medium  tracking-wider">Selesai</span>
                            </div>
                            <div
                                class="bg-[#FBC610] text-black rounded-[5px] px-3 py-1.5 flex items-center gap-2 shadow-sm border border-yellow-500/20">
                                <span class="text-[16px] font-bold leading-none">{{ $jumlahBelumDiperiksa ?? 0 }}</span>
                                <span class="text-[11px] font-medium uppercase tracking-wider">Belum Diperiksa</span>
                            </div>
                        </div>
                    </div>

                    <!-- Search & Filters Row -->
                    <div class="flex flex-col xl:flex-row gap-4">
                        <!-- Search Bar -->
                        <div class="relative w-full xl:w-[350px] shrink-0">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                            </div>
                                <input type="text" x-model="searchQuery"
                                    class="w-full h-[36px] pl-10 pr-4 text-[13px] bg-white border border-[#CAC0C0] rounded-[5px] text-black focus:outline-none focus:border-gray-400 focus:ring-0 transition-colors shadow-sm placeholder:text-gray-400"
                                    placeholder="Cari nama, NIM, atau judul KP...">
                            </div>

                        <!-- Dropdown Filters -->
                        <div class="flex flex-wrap items-center gap-3">
                            <div x-data="{ open: false }" class="relative w-full sm:w-[220px]">
                                <button @click="open = !open" @click.outside="open = false" type="button"
                                    class="w-full h-[36px] flex items-center justify-between border border-[#CAC0C0] bg-white rounded-[5px] px-3 text-[13px] text-black hover:bg-gray-50 transition-colors shadow-sm focus:outline-none focus:border-gray-400 focus:ring-0">
                                    <span class="font-medium truncate" x-text="statusFilter === 'all' ? 'Status Approval' : (statusFilter === 'diperiksa' ? 'Diperiksa' : 'Menunggu Pengecekan')"></span>
                                    <svg :class="open ? 'rotate-0' : 'rotate-90'"
                                        class="w-3.5 h-3.5 text-gray-500 transition-transform duration-200 shrink-0 ml-2"
                                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 9l-7 7-7-7"></path>
                                    </svg>
                                </button>
                                <div x-show="open" x-transition.opacity.duration.200ms style="display: none;" class="absolute z-50 w-full mt-1 bg-white border border-[#CAC0C0] rounded-[5px] shadow-lg py-1">
                                    <label class="block px-3 py-2 text-[12px] hover:bg-gray-100 cursor-pointer text-black">
                                        <input type="radio" value="all" x-model="statusFilter" class="hidden" @change="open = false">Semua Status
                                    </label>
                                    <label class="block px-3 py-2 text-[12px] hover:bg-gray-100 cursor-pointer text-black">
                                        <input type="radio" value="diperiksa" x-model="statusFilter" class="hidden" @change="open = false">Diperiksa
                                    </label>
                                    <label class="block px-3 py-2 text-[12px] hover:bg-gray-100 cursor-pointer text-black">
                                        <input type="radio" value="pending" x-model="statusFilter" class="hidden" @change="open = false">Menunggu Pengecekan
                                    </label>
                                </div>
                            </div>

                            <button @click="searchQuery = ''; statusFilter = 'all'"
                                class="h-[36px] bg-[#EA3323] hover:bg-red-700 text-white font-bold text-[12px] px-4 rounded-[10px] shadow-sm transition-colors uppercase whitespace-nowrap">
                                Clear Filter
                            </button>
                        </div>
                    </div>
                </div>

                <div class="border border-gray-200 rounded-[10px] overflow-hidden">
                    <div class="overflow-x-auto custom-scrollbar">
                        <table class="w-full border-collapse text-[13px] min-w-[1250px] text-center">
                            <thead>
                                <tr class="bg-[#EBEBEB] text-black">
                                    <th
                                        class="py-4 px-4 font-bold w-[60px] border-b border-gray-300 border-r border-gray-300 uppercase tracking-wider">
                                        No</th>
                                    <th
                                        class="py-4 px-4 font-bold text-left text-black border-b border-gray-300 border-r border-gray-300">
                                        Mahasiswa</th>
                                    <th
                                        class="py-4 px-4 font-bold text-black border-b border-gray-300 border-r border-gray-300">
                                        Judul KP</th>
                                    <th
                                        class="py-4 px-4 font-bold text-black border-b border-gray-300 border-r border-gray-300">
                                        Instansi</th>
                                    <th
                                        class="py-4 px-4 font-bold text-black border-b border-gray-300 border-r border-gray-300">
                                        Supervisor</th>
                                    <th
                                        class="py-4 px-4 font-bold text-black border-b border-gray-300 border-r border-gray-300">
                                        Bimbingan</th>
                                    <th
                                        class="py-4 px-4 font-bold text-black border-b border-gray-300 border-r border-gray-300">
                                        Status</th>
                                    <th class="py-4 px-4 font-bold text-black border-b border-gray-300">
                                        Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                <template x-for="(p, index) in filteredList" :key="p.id + '-' + p.nim">
                                    <tr class="hover:bg-gray-50 transition-colors">
                                        <td class="py-4 px-4 text-center text-black/60 border-r border-gray-200"
                                            x-text="index + 1"></td>
                                        <td class="py-4 px-4 text-left border-r border-gray-200">
                                            <div class="font-bold text-black" x-text="p.nama"></div>
                                            <div class="text-black/60 text-[11px]" x-text="p.nim"></div>
                                        </td>
                                        <td class="py-4 px-4 text-center text-black/80 font-medium leading-relaxed border-r border-gray-200"
                                            x-text="p.judul"></td>
                                        <td class="py-4 px-4 text-center text-black/70 italic font-medium border-r border-gray-200"
                                            x-text="p.instansi"></td>
                                        <td class="py-4 px-4 text-center text-black font-bold text-[10px] border-r border-gray-200"
                                            x-text="p.supervisor"></td>
                                        <td class="py-4 px-4 text-center border-r border-gray-200">
                                            <span
                                                class="bg-gray-100 text-black px-3 py-1 rounded-full font-bold text-[12px]">
                                                <span x-text="p.total_log"></span>/12
                                            </span>
                                        </td>
                                        <td class="py-4 px-4 text-center border-r border-gray-200">
                                            <template x-if="p.status_label === '-'">
                                                <span class="text-black/40 font-bold">-</span>
                                            </template>
                                            <template x-if="p.status_label === 'Menunggu pengecekan'">
                                                <span
                                                    class="bg-[#FDE68A] text-[#92400E] font-bold px-4 py-1.5 rounded-full text-[10px] uppercase whitespace-nowrap shadow-sm">
                                                    Menunggu
                                                </span>
                                            </template>
                                            <template x-if="p.status_label === 'Diperiksa'">
                                                <span
                                                    class="bg-[#86EFAC] text-[#166534] font-bold px-4 py-1.5 rounded-full text-[10px] uppercase flex items-center justify-center w-max mx-auto gap-1.5 shadow-sm">
                                                    <div class="w-1.5 h-1.5 rounded-full bg-green-600"></div> Selesai
                                                </span>
                                            </template>
                                        </td>
                                        <td class="py-4 px-4 text-center">
                                            <template x-if="p.id">
                                                <a :href="p.detail_url"
                                                    class="bg-[#3B5998] hover:bg-blue-800 text-white text-[11px] font-bold px-6 py-2 rounded-full inline-block shadow-md transition-all uppercase tracking-wide">
                                                    Detail
                                                </a>
                                            </template>
                                            <template x-if="!p.id">
                                                <span
                                                    class="text-gray-400 italic text-[10px] uppercase font-bold tracking-tight">Belum
                                                    Daftar</span>
                                            </template>
                                        </td>
                                    </tr>
                                </template>
                                <template x-if="filteredList.length === 0">
                                    <tr>
                                        <td colspan="8"
                                            class="text-center py-16 text-gray-400 italic bg-gray-50 uppercase tracking-widest font-medium">
                                            Tidak ada data mahasiswa bimbingan
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="h-20"></div> <!-- Jarak ke footer -->
        </div>
</x-dashboard-layout>