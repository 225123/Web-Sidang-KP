<x-dashboard-layout header="Pemeriksaan Revisi Sidang" userName="{{ auth()->user()->name }}" roleName="KOORDINATOR">
    <x-slot:sidebar>
        @include('koordinator.components.sidebar', ['active' => 'revisi'])
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

    <div x-data="pemeriksaanRevisiPage()" class="mt-6">
        <!-- Unified Table Container -->
        <div class="bg-white rounded-[15px] border border-gray-200 shadow-sm overflow-hidden p-6 mb-8">
            <!-- Header Section -->
            <div class="border-b border-gray-200 pb-6 mb-6">
                <!-- Row 1: Title & Description -->
                <div class="mb-8">
                    <h3 class="text-[18px] font-bold text-black tracking-tight uppercase">Tabel Pemeriksaan Revisi Mahasiswa</h3>
                    <p class="text-[12px] text-black/60 font-medium mt-1">Daftar mahasiswa yang memerlukan pemeriksaan berkas revisi pasca sidang sebagai Dosen Penguji 1.</p>
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

                <!-- Search & Filters Row -->
                <div class="flex flex-col xl:flex-row gap-4">
                    <!-- Search Bar -->
                    <div class="relative w-full xl:w-[350px] shrink-0">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        </div>
                        <input type="text" x-model="search" 
                            class="w-full h-[36px] pl-10 pr-4 text-[13px] bg-white border border-[#CAC0C0] rounded-[10px] text-black focus:outline-none focus:border-[#4CC098] focus:ring-1 focus:ring-[#4CC098] transition-colors shadow-sm placeholder:text-gray-400" 
                            placeholder="Cari nama atau NIM...">
                    </div>

                    <!-- Dropdown Filters -->
                    <div class="flex flex-wrap items-center gap-3">
                        <!-- Filter Status -->
                        <div x-data="{ open: false }" class="relative w-full sm:w-[220px]">
                            <button @click="open = !open" @click.outside="open = false" type="button" 
                                class="w-full h-[36px] flex items-center justify-between border border-[#CAC0C0] bg-white rounded-[10px] px-3 text-[13px] text-black hover:bg-gray-50 transition-colors shadow-sm focus:outline-none focus:border-[#4CC098] focus:ring-1 focus:ring-[#4CC098]">
                                <div class="flex items-center gap-1.5 truncate">
                                    <span class="font-bold text-black shrink-0">Status:</span>
                                    <span class="font-medium truncate" x-text="filterStatus === 'all' ? 'Semua Status' : (filterStatus === 'Belum mengumpulkan' ? 'Belum Mengumpulkan' : (filterStatus === 'Menunggu' ? 'Sedang Diperiksa' : (filterStatus === 'Disahkan' ? 'Disetujui' : 'Ditolak')))"></span>
                                </div>
                                <svg :class="open ? 'rotate-180' : ''" class="w-4 h-4 text-gray-500 transition-transform duration-200 shrink-0 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                            </button>
                            <div x-show="open" x-transition.opacity.duration.200ms style="display: none;" class="absolute z-50 w-full mt-1 bg-white border border-[#CAC0C0] rounded-[10px] shadow-lg py-1">
                                <template x-for="option in [
                                    {value: 'all', label: 'Semua Status'},
                                    {value: 'Belum mengumpulkan', label: 'Belum Mengumpulkan'},
                                    {value: 'Menunggu', label: 'Sedang Diperiksa'},
                                    {value: 'Disahkan', label: 'Disetujui'},
                                    {value: 'Ditolak', label: 'Ditolak'}
                                ]" :key="option.value">
                                    <label class="flex items-center px-3 py-2 hover:bg-gray-50 cursor-pointer transition-colors group">
                                        <input type="radio" x-model="filterStatus" :value="option.value" class="sr-only" @change="open = false">
                                        <div class="flex items-center gap-2">
                                            <div class="w-3 h-3 rounded-full border flex items-center justify-center transition-colors" :class="filterStatus === option.value ? 'border-[#4CC098]' : 'border-gray-300 group-hover:border-[#4CC098]'">
                                                <div class="w-1.5 h-1.5 rounded-full bg-[#4CC098] transition-opacity" :class="filterStatus === option.value ? 'opacity-100' : 'opacity-0'"></div>
                                            </div>
                                            <span class="text-[13px] text-black group-hover:text-[#4CC098] transition-colors" :class="filterStatus === option.value ? 'font-bold' : 'font-medium'" x-text="option.label"></span>
                                        </div>
                                    </label>
                                </template>
                            </div>
                        </div>

                        <button @click="clearFilters()" class="h-[36px] bg-[#EA3323] hover:bg-red-700 text-white font-bold text-[12px] px-4 rounded-[10px] shadow-sm transition-colors uppercase whitespace-nowrap">
                            Clear Filter
                        </button>
                    </div>
                </div>
            </div>

            <div class="border border-gray-200 rounded-[10px] overflow-hidden">
                <div class="overflow-x-auto custom-scrollbar">
                    <table class="w-full min-w-[1250px] border-collapse text-[12px] text-center">
                    <thead>
                        <tr class="bg-[#EBEBEB] text-black h-[45px] font-bold border-b border-[#CAC0C0]">
                            <th class="py-2 px-3 w-[50px] border-r border-[#CAC0C0]">No</th>
                            <th class="py-2 px-4 text-left w-[200px] border-r border-[#CAC0C0]">Identitas Mahasiswa</th>
                            <th class="py-2 px-4 w-[180px] border-r border-[#CAC0C0]">Tanggal Sidang</th>
                            <th class="py-2 px-4 w-[180px] border-r border-[#CAC0C0]">Batas Pengumpulan</th>
                            <th class="py-2 px-4 w-[180px] border-r border-[#CAC0C0]">Dikumpul Pada</th>
                            <th class="py-2 px-4 w-[180px] border-r border-[#CAC0C0]">Berkas Revisi</th>
                            <th class="py-2 px-4 w-[120px] border-r border-[#CAC0C0]">Status</th>
                            <th class="py-2 px-4 w-[150px]">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <template x-for="(sidang, index) in filteredSidangs" :key="sidang.id">
                            <tr class="hover:bg-gray-50 transition-colors border-b border-[#CAC0C0] h-[60px]">
                                <td class="px-3 py-4 text-black font-medium border-r border-[#CAC0C0]" x-text="index + 1"></td>
                                <td class="px-4 py-4 text-left border-r border-[#CAC0C0]">
                                    <div class="font-bold text-black uppercase" x-text="sidang.mahasiswa.nim"></div>
                                    <div class="text-[11px] text-black/50 sentence-case font-medium mt-0.5" x-text="sidang.mahasiswa.user.name.toLowerCase()"></div>
                                </td>
                                <td class="px-4 py-4 text-center border-r border-[#CAC0C0]">
                                    <div class="font-bold text-black uppercase" x-text="formatDate(sidang.tanggal_sidang)"></div>
                                    <div class="text-[11px] text-black/50 mt-0.5" x-text="formatTime(sidang.waktu_mulai_sidang) + ' - ' + formatTime(sidang.waktu_selesai_sidang) + ' WIB'"></div>
                                </td>
                                <td class="px-4 py-4 text-center border-r border-[#CAC0C0]">
                                    <div class="font-bold text-black uppercase" x-text="getDeadline(sidang.tanggal_sidang)"></div>
                                    <div class="text-[11px] text-black/50 mt-0.5 font-medium">Pukul 23:59 WIB</div>
                                </td>
                                <td class="px-4 py-4 text-center border-r border-[#CAC0C0]">
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
                                <td class="px-4 py-4 text-center border-r border-[#CAC0C0]">
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
                                <td class="px-4 py-4 text-center border-r border-[#CAC0C0]">
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
                                <td class="px-4 py-4 text-center">
                                    <template x-if="sidang.status_revisi === 'Menunggu'">
                                        <div class="flex items-center justify-center gap-2">
                                            <form :action="'{{ url('koordinator/revisi') }}/' + sidang.id + '/terima'" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin MENGESAHKAN revisi mahasiswa ini?');">
                                                @csrf
                                                <button type="submit" class="bg-[#008000] hover:bg-green-700 text-white font-bold text-[10px] px-3 py-1.5 rounded-[5px] transition-colors uppercase tracking-wide">
                                                    Sahkan
                                                </button>
                                            </form>
                                            <form :action="'{{ url('koordinator/revisi') }}/' + sidang.id + '/tolak'" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin MENOLAK revisi mahasiswa ini?');">
                                                @csrf
                                                <button type="submit" class="bg-red-600 hover:bg-red-700 text-white font-bold text-[10px] px-3 py-1.5 rounded-[5px] transition-colors uppercase tracking-wide">
                                                    Tolak
                                                </button>
                                            </form>
                                        </div>
                                    </template>
                                    <template x-if="sidang.status_revisi === 'Disahkan' || sidang.status_revisi === 'Diterima'">
                                        <span class="text-green-600"><svg class="w-6 h-6 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg></span>
                                    </template>
                                    <template x-if="sidang.status_revisi === 'Ditolak'">
                                        <span class="text-red-600"><svg class="w-6 h-6 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg></span>
                                    </template>
                                    <template x-if="sidang.status_revisi === 'Belum mengumpulkan'">
                                        <span class="text-black/30 italic text-[11px]">Menunggu Upload</span>
                                    </template>
                                </td>
                            </tr>
                        </template>
                        <template x-if="filteredSidangs.length === 0">
                            <tr>
                                <td colspan="8" class="py-12 text-center text-black/50 italic text-[13px]">
                                    Belum ada data mahasiswa yang sesuai kriteria pencarian/filter.
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
                </div>
            </div>
        </div>
    </div>

    <script>
        function pemeriksaanRevisiPage() {
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
