<x-dashboard-layout header="Rekap Revisi Sidang" userName="{{ auth()->user()->name }}" roleName="KOORDINATOR">
    <x-slot:sidebar>
        @include('koordinator.components.sidebar', ['active' => 'rekap-revisi'])
    </x-slot>

    <div x-data="rekapRevisiPage()" class="mt-6">
        <!-- Unified Table Container -->
        <div class="bg-white rounded-[10px] border border-[#CAC0C0] shadow-sm overflow-hidden">
            <!-- Header Section -->
            <div class="p-6 border-b border-[#CAC0C0]">
                <!-- Row 1: Title & Description -->
                <div class="mb-8">
                    <h3 class="text-[20px] font-bold text-black tracking-tight uppercase">TABEL REKAP REVISI MAHASISWA</h3>
                    <p class="text-[12px] text-black/60 font-medium mt-1">Manajemen pemantauan berkas revisi mahasiswa pasca sidang untuk status Lulus Dengan Revisi.</p>
                </div>

                <!-- Row 2: Statistics Boxes -->
                <div class="flex flex-wrap gap-4 mb-8">
                    <!-- Total -->
                    <div class="bg-[#4285F4] rounded-[10px] p-3 flex flex-col justify-center items-center w-[110px] shadow-sm text-white">
                        <div class="flex items-center gap-2">
                            <div class="bg-white/20 p-1 rounded-full"><svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg></div>
                            <span class="text-xl font-bold" x-text="stats.total">0</span>
                        </div>
                        <span class="text-[9px] font-bold mt-1 uppercase text-center leading-tight">Total Mahasiswa</span>
                    </div>
                    <!-- Belum Kumpul -->
                    <div class="bg-[#8E8E8E] rounded-[10px] p-3 flex flex-col justify-center items-center w-[110px] shadow-sm text-white">
                        <div class="flex items-center gap-2">
                            <div class="bg-white/20 p-1 rounded-full"><svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"></path></svg></div>
                            <span class="text-xl font-bold" x-text="stats.belum">0</span>
                        </div>
                        <span class="text-[9px] font-bold mt-1 uppercase text-center leading-tight">Belum Kumpul</span>
                    </div>
                    <!-- Sedang Periksa -->
                    <div class="bg-[#FBC610] rounded-[10px] p-3 flex flex-col justify-center items-center w-[110px] shadow-sm text-black">
                        <div class="flex items-center gap-2">
                            <div class="border border-black p-0.5 rounded-sm"><svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg></div>
                            <span class="text-xl font-bold" x-text="stats.menunggu">0</span>
                        </div>
                        <span class="text-[9px] font-bold mt-1 uppercase text-center leading-tight">Sedang Periksa</span>
                    </div>
                    <!-- Disahkan -->
                    <div class="bg-[#38913B] rounded-[10px] p-3 flex flex-col justify-center items-center w-[110px] shadow-sm text-white">
                        <div class="flex items-center gap-2">
                            <div class="bg-white/20 p-1 rounded-full"><svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg></div>
                            <span class="text-xl font-bold" x-text="stats.disahkan">0</span>
                        </div>
                        <span class="text-[9px] font-bold mt-1 uppercase text-center leading-tight">Disahkan</span>
                    </div>
                    <!-- Ditolak -->
                    <div class="bg-[#EA3323] rounded-[10px] p-3 flex flex-col justify-center items-center w-[110px] shadow-sm text-white">
                        <div class="flex items-center gap-2">
                            <div class="bg-white/20 p-1 rounded-full"><svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg></div>
                            <span class="text-xl font-bold" x-text="stats.ditolak">0</span>
                        </div>
                        <span class="text-[9px] font-bold mt-1 uppercase text-center leading-tight">Ditolak</span>
                    </div>
                </div>

                <!-- Row 3: Search Bar -->
                <div class="relative w-full mb-4">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </div>
                    <input type="text" x-model="search" class="block w-full pl-10 pr-4 py-2 border border-gray-300 rounded-[5px] text-sm text-black focus:ring-blue-500 focus:border-blue-500 shadow-sm" placeholder="Cari nama, NIM, atau judul KP...">
                </div>

                <!-- Row 4: Filters Row -->
                <div class="flex flex-wrap items-center gap-4">
                    <div class="flex items-center gap-2">
                        <label class="text-[13px] font-bold text-black whitespace-nowrap">Status :</label>
                        <select x-model="filterStatus" class="w-[180px] text-[13px] border border-gray-300 rounded-[5px] py-2 px-3 bg-white text-black font-medium focus:outline-none focus:ring-1 focus:ring-blue-500 shadow-sm">
                            <option value="all">Semua Status</option>
                            <option value="Belum mengumpulkan">Belum Mengumpulkan</option>
                            <option value="Menunggu">Sedang Diperiksa</option>
                            <option value="Disahkan">Disetujui</option>
                            <option value="Ditolak">Ditolak</option>
                        </select>
                    </div>

                    <button @click="clearFilters()" class="bg-[#EA3323] hover:bg-red-700 text-white font-bold text-[12px] px-6 py-2 rounded-[5px] shadow-sm transition-colors whitespace-nowrap uppercase">
                        Clear Filter
                    </button>
                </div>
            </div>

            <!-- Table Section -->
            <div class="overflow-x-auto">
                <table class="w-full text-center border-collapse text-[12px] min-w-[1250px]">
                    <thead class="bg-[#EBEBEB] font-bold text-black border-b border-[#CAC0C0] h-[45px]">
                        <tr>
                            <th class="border-r border-[#CAC0C0] px-3 py-2 w-[50px]">No</th>
                            <th class="border-r border-[#CAC0C0] px-4 py-2 text-left w-[200px]">Identitas Mahasiswa</th>
                            <th class="border-r border-[#CAC0C0] px-4 py-2 text-center w-[180px]">Tanggal Sidang</th>
                            <th class="border-r border-[#CAC0C0] px-4 py-2 text-center w-[180px]">Batas Pengumpulan</th>
                            <th class="border-r border-[#CAC0C0] px-4 py-2 text-center w-[180px]">Dikumpul Pada</th>
                            <th class="border-r border-[#CAC0C0] px-6 py-2 text-center w-[180px]">Berkas Revisi</th>
                            <th class="px-6 py-2 text-center w-[150px]">Status</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white">
                        <template x-for="(sidang, index) in filteredSidangs" :key="sidang.id">
                            <tr class="border-b border-[#CAC0C0] hover:bg-gray-50 transition-colors h-[60px]">
                                <td class="border-r border-[#CAC0C0] px-3 py-4 text-black font-medium" x-text="index + 1"></td>
                                <td class="border-r border-[#CAC0C0] px-4 py-4 text-left">
                                    <div class="font-bold text-black uppercase" x-text="sidang.mahasiswa.nim"></div>
                                    <div class="text-[11px] text-black/50 sentence-case font-medium mt-0.5" x-text="sidang.mahasiswa.user.name.toLowerCase()"></div>
                                </td>
                                <td class="border-r border-[#CAC0C0] px-4 py-4 text-center">
                                    <div class="font-bold text-black uppercase" x-text="formatDate(sidang.tanggal_sidang)"></div>
                                    <div class="text-[11px] text-black/50 mt-0.5" x-text="formatTime(sidang.waktu_mulai_sidang) + ' - ' + formatTime(sidang.waktu_selesai_sidang) + ' WIB'"></div>
                                </td>
                                <td class="border-r border-[#CAC0C0] px-4 py-4 text-center">
                                    <div class="font-bold text-black uppercase" x-text="getDeadline(sidang.tanggal_sidang)"></div>
                                    <div class="text-[11px] text-black/50 mt-0.5 font-medium">Pukul 23:59 WIB</div>
                                </td>
                                <td class="border-r border-[#CAC0C0] px-4 py-4 text-center">
                                    <template x-if="sidang.tanggal_revisi">
                                        <div>
                                            <div class="font-bold text-black uppercase" x-text="formatDate(sidang.tanggal_revisi)"></div>
                                            <div class="text-[11px] text-black/50 mt-0.5" x-text="formatTime(sidang.tanggal_revisi.split(' ')[1]) + ' WIB'"></div>
                                        </div>
                                    </template>
                                    <template x-if="!sidang.tanggal_revisi">
                                        <span class="text-black/30 italic font-medium">-</span>
                                    </template>
                                </td>
                                <td class="border-r border-[#CAC0C0] px-6 py-4 text-center">
                                    <div class="flex flex-col items-center gap-2">
                                        <template x-if="sidang.file_revisi">
                                            <a :href="'/storage/' + sidang.file_revisi" target="_blank" class="text-black hover:underline font-bold inline-flex items-center gap-1.5 text-[11px]">
                                                <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                                LIHAT PDF
                                            </a>
                                        </template>
                                        <template x-if="sidang.link_revisi">
                                            <a :href="sidang.link_revisi" target="_blank" class="text-black hover:underline font-bold inline-flex items-center gap-1.5 text-[11px]">
                                                <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                                                LIHAT DRIVE
                                            </a>
                                        </template>
                                        <template x-if="!sidang.file_revisi && !sidang.link_revisi">
                                            <span class="text-black/30 italic font-medium">-</span>
                                        </template>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <template x-if="sidang.status_revisi === 'Belum mengumpulkan'">
                                        <span class="px-3 py-1 bg-gray-100 text-black rounded-[5px] text-[10px] font-bold border border-gray-300 uppercase tracking-tight">Belum Kumpul</span>
                                    </template>
                                    <template x-if="sidang.status_revisi === 'Menunggu'">
                                        <span class="px-3 py-1 bg-yellow-100 text-black rounded-[5px] text-[10px] font-bold border border-yellow-300 uppercase tracking-tight">Diperiksa</span>
                                    </template>
                                    <template x-if="sidang.status_revisi === 'Disahkan' || sidang.status_revisi === 'Diterima'">
                                        <span class="px-3 py-1 bg-green-100 text-black rounded-[5px] text-[10px] font-bold border border-green-300 uppercase tracking-tight">Disetujui</span>
                                    </template>
                                    <template x-if="sidang.status_revisi === 'Ditolak'">
                                        <span class="px-3 py-1 bg-red-100 text-black rounded-[5px] text-[10px] font-bold border border-red-300 uppercase tracking-tight">Ditolak</span>
                                    </template>
                                </td>
                            </tr>
                        </template>
                        <template x-if="filteredSidangs.length === 0">
                            <tr>
                                <td colspan="7" class="py-12 text-center text-black/50 italic text-[13px]">
                                    Tidak ada data mahasiswa yang sesuai pencarian/filter.
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        function rekapRevisiPage() {
            return {
                sidangs: @json($sidangs),
                search: '',
                filterStatus: 'all',

                clearFilters() {
                    this.search = '';
                    this.filterStatus = 'all';
                },

                get stats() {
                    return {
                        total: this.sidangs.length,
                        belum: this.sidangs.filter(s => s.status_revisi === 'Belum mengumpulkan').length,
                        menunggu: this.sidangs.filter(s => s.status_revisi === 'Menunggu').length,
                        disahkan: this.sidangs.filter(s => s.status_revisi === 'Disahkan' || s.status_revisi === 'Diterima').length,
                        ditolak: this.sidangs.filter(s => s.status_revisi === 'Ditolak').length,
                    }
                },

                get filteredSidangs() {
                    let res = [...this.sidangs];
                    
                    if (this.search) {
                        const q = this.search.toLowerCase();
                        res = res.filter(s => 
                            s.mahasiswa.nim.toLowerCase().includes(q) || 
                            s.mahasiswa.user.name.toLowerCase().includes(q)
                        );
                    }

                    if (this.filterStatus !== 'all') {
                        res = res.filter(s => {
                            if (this.filterStatus === 'Disahkan') {
                                return s.status_revisi === 'Disahkan' || s.status_revisi === 'Diterima';
                            }
                            return s.status_revisi === this.filterStatus;
                        });
                    }

                    return res;
                },

                formatDate(dateString) {
                    if (!dateString) return '-';
                    const d = new Date(dateString);
                    return d.toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' });
                },

                formatTime(timeString) {
                    if (!timeString) return '-';
                    return timeString.substring(0, 5);
                },

                getDeadline(dateString) {
                    if (!dateString) return '-';
                    const d = new Date(dateString);
                    d.setDate(d.getDate() + 5);
                    return d.toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' });
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
