<x-dashboard-layout header="Pemeriksaan Revisi Sidang" userName="{{ auth()->user()->name }}" roleName="KOORDINATOR KP">
    <x-slot:sidebar>
        @include('koordinator.components.sidebar', ['active' => 'revisi'])
    </x-slot>

    

    <div x-data="revisiPage()" class="max-w-[1200px] mx-auto">
        <!-- Info Box -->
        <div class="bg-[#EAEFFF] border border-[#BACDFB] rounded-[10px] p-4 flex items-center gap-4 shadow-sm mb-6">
            <div class="bg-[#7896F8] w-6 h-6 rounded-full flex items-center justify-center text-white shrink-0 shadow-sm font-serif italic text-sm">i</div>
            <p class="text-[14px] text-black font-medium leading-relaxed">
                Tinjau dan Sahkan Berkas Revisi Laporan Yang Dikirimkan Mahasiswa Pasca Sidang KP.
            </p>
        </div>

        @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-6" role="alert">
            <span class="block sm:inline font-bold text-[13px]">{{ session('success') }}</span>
        </div>
        @endif

        @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-6" role="alert">
            <span class="block sm:inline font-bold text-[13px]">{{ session('error') }}</span>
        </div>
        @endif

        @if($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-6" role="alert">
            <ul class="list-disc list-inside text-[13px] font-bold">
                @foreach($errors->all() as $err)
                <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <!-- Main Container -->
        <div class="bg-white rounded-[15px] border border-gray-200 shadow-sm overflow-hidden p-6 mb-8">
            <!-- Header Section -->
            <div class="mb-6 flex flex-col sm:flex-row justify-between items-start gap-4">
                <div>
                    <h3 class="text-[18px] font-bold text-black tracking-tight uppercase">TABEL PEMERIKSAAN REVISI MAHASISWA</h3>
                    <p class="text-[12px] text-black/60 font-medium mt-1">Daftar mahasiswa yang memerlukan pemeriksaan berkas revisi pasca sidang sebagai Dosen Penguji 1.</p>
                </div>
                <div class="flex gap-2 shrink-0">
                    <div class="bg-[#4285F4] text-white rounded-[5px] px-3 py-1.5 flex items-center gap-2 shadow-sm border border-blue-600/20">
                        <span class="text-[16px] font-bold leading-none" x-text="stats.total"></span>
                        <span class="text-[11px] font-medium uppercase tracking-wider">Total</span>
                    </div>
                    <div class="bg-[#FBBC05] text-black rounded-[5px] px-3 py-1.5 flex items-center gap-2 shadow-sm border border-yellow-500/20">
                        <span class="text-[16px] font-bold leading-none" x-text="stats.menunggu"></span>
                        <span class="text-[11px] font-medium uppercase tracking-wider">Menunggu</span>
                    </div>
                    <div class="bg-[#34A853] text-white rounded-[5px] px-3 py-1.5 flex items-center gap-2 shadow-sm border border-green-600/20">
                        <span class="text-[16px] font-bold leading-none" x-text="stats.disahkan"></span>
                        <span class="text-[11px] font-medium uppercase tracking-wider">Selesai</span>
                    </div>
                </div>
            </div>

            <!-- Controls Section -->
            <div class="flex flex-col sm:flex-row justify-between items-center gap-6 mb-6">
                <div class="flex flex-wrap items-center gap-4 w-full sm:w-auto">
                    <!-- Search -->
                    <div class="relative flex-1 sm:w-[300px]">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </div>
                        <input type="text" x-model="search"
                            class="block w-full pl-9 pr-4 py-2 border border-gray-300 rounded-[5px] text-[12px] text-black focus:ring-[#4285F4]"
                            placeholder="Cari Nama/NIM Mahasiswa...">
                    </div>

                    <!-- Filter Status -->
                    <div x-data="{ openFilter: false }" class="relative w-full sm:w-[150px] z-[50]">
                        <button type="button" @click="openFilter = !openFilter" @click.outside="openFilter = false"
                            class="w-full text-[12px] border border-gray-300 rounded-[5px] py-2 px-3 bg-white text-black font-medium focus:ring-[#4285F4] flex justify-between items-center text-left shadow-sm">
                            <span x-text="filterStatus === 'all' ? 'Status Revisi' : (filterStatus === 'Menunggu' ? 'Menunggu' : (filterStatus === 'Disahkan' ? 'Disetujui' : (filterStatus === 'Ditolak' ? 'Ditolak' : 'Belum Kumpul')))"></span>
                            <svg :class="openFilter ? 'rotate-0' : 'rotate-90'"
                                class="w-3.5 h-3.5 transition-all duration-200 text-gray-500 flex-shrink-0"
                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>
                        <div x-show="openFilter" x-transition x-cloak
                            class="absolute w-full mt-1 bg-white border border-gray-300 rounded-[5px] shadow-lg overflow-hidden py-1 z-50">
                            <label class="block px-3 py-2 text-[12px] hover:bg-gray-100 cursor-pointer text-black"><input type="radio" value="all" x-model="filterStatus" class="hidden" @change="openFilter = false">Semua</label>
                            <label class="block px-3 py-2 text-[12px] hover:bg-gray-100 cursor-pointer text-black"><input type="radio" value="Menunggu" x-model="filterStatus" class="hidden" @change="openFilter = false">Menunggu</label>
                            <label class="block px-3 py-2 text-[12px] hover:bg-gray-100 cursor-pointer text-black"><input type="radio" value="Disahkan" x-model="filterStatus" class="hidden" @change="openFilter = false">Disetujui</label>
                            <label class="block px-3 py-2 text-[12px] hover:bg-gray-100 cursor-pointer text-black"><input type="radio" value="Ditolak" x-model="filterStatus" class="hidden" @change="openFilter = false">Ditolak</label>
                            <label class="block px-3 py-2 text-[12px] hover:bg-gray-100 cursor-pointer text-black"><input type="radio" value="Belum mengumpulkan" x-model="filterStatus" class="hidden" @change="openFilter = false">Belum Kumpul</label>
                        </div>
                    </div>

                    <!-- Filter Ketepatan Waktu -->
                    <div x-data="{ openWaktu: false }" class="relative w-full sm:w-[150px] z-[50]">
                        <button type="button" @click="openWaktu = !openWaktu" @click.outside="openWaktu = false"
                            class="w-full text-[12px] border border-gray-300 rounded-[5px] py-2 px-3 bg-white text-black font-medium focus:ring-[#4285F4] flex justify-between items-center text-left shadow-sm">
                            <span x-text="filterWaktu === 'all' ? 'Semua Waktu' : filterWaktu"></span>
                            <svg :class="openWaktu ? 'rotate-0' : 'rotate-90'"
                                class="w-3.5 h-3.5 transition-all duration-200 text-gray-500 flex-shrink-0"
                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>
                        <div x-show="openWaktu" x-transition x-cloak
                            class="absolute w-full mt-1 bg-white border border-gray-300 rounded-[5px] shadow-lg overflow-hidden py-1 z-50">
                            <label class="block px-3 py-2 text-[12px] hover:bg-gray-100 cursor-pointer text-black"><input type="radio" value="all" x-model="filterWaktu" class="hidden" @change="openWaktu = false">Semua</label>
                            <label class="block px-3 py-2 text-[12px] hover:bg-gray-100 cursor-pointer text-black"><input type="radio" value="Tepat Waktu" x-model="filterWaktu" class="hidden" @change="openWaktu = false">Tepat Waktu</label>
                            <label class="block px-3 py-2 text-[12px] hover:bg-gray-100 cursor-pointer text-black"><input type="radio" value="Terlambat" x-model="filterWaktu" class="hidden" @change="openWaktu = false">Terlambat</label>
                        </div>
                    </div>

                    <div class="flex gap-2 w-full sm:w-auto">
                        <button type="button" @click="clearFilters()"
                            class="flex-1 sm:flex-none border border-[#EA4335] bg-[#EA4335] text-white hover:bg-red-600 transition-colors px-4 py-1.5 rounded-[5px] text-[12px] font-bold shadow-sm flex items-center justify-center">
                            Clear Filter
                        </button>
                    </div>
                </div>
            </div>

            <!-- Table Section -->
            <div class="border border-gray-200 rounded-[10px] overflow-x-auto">
                <table class="w-full border-collapse text-[12px] min-w-[1200px]">
                    <thead class="bg-[#EBEBEB] text-black">
                        <tr>
                            <th class="py-3 px-4 font-bold text-center w-[60px] border-b border-r border-gray-300">No</th>
                            <th class="py-3 px-4 font-bold text-left border-b border-r border-gray-300 w-[250px]">Mahasiswa</th>
                            <th class="py-3 px-4 font-bold text-center border-b border-r border-gray-300 w-[180px]">Tanggal Sidang</th>
                            <th class="py-3 px-4 font-bold text-center border-b border-r border-gray-300 w-[180px] whitespace-nowrap">Batas Pengumpulan</th>
                            <th class="py-3 px-4 font-bold text-center border-b border-r border-gray-300 w-[150px]">Dikumpul Pada</th>
                            <th class="py-3 px-4 font-bold text-center border-b border-r border-gray-300 w-[150px]">Berkas Revisi</th>
                            <th class="py-3 px-4 font-bold text-center border-b border-r border-gray-300 w-[150px]">Edit</th>
                            <th class="py-3 px-4 font-bold text-center border-b border-gray-300 w-[180px]">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 bg-white">
                        <template x-for="(sidang, index) in paginatedSidangs" :key="sidang.id">
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="py-3 px-4 text-center text-black/60 border-r border-gray-200" x-text="((currentPage - 1) * itemsPerPage) + index + 1"></td>
                                <td class="py-3 px-4 text-left border-r border-gray-200">
                                    <div class="font-bold text-black uppercase" x-text="sidang.mahasiswa.user.name"></div>
                                    <div class="text-gray-500 font-mono text-[12px]" x-text="sidang.mahasiswa.nim"></div>
                                </td>
                                <td class="py-3 px-4 text-center border-r border-gray-200">
                                    <div class="font-bold text-black" x-text="formatDate(sidang.tanggal_sidang)"></div>
                                    <div class="text-[12px] text-gray-500" x-text="formatTime(sidang.waktu_mulai_sidang) + ' - ' + formatTime(sidang.waktu_selesai_sidang)"></div>
                                </td>
                                <td class="py-3 px-4 text-center border-r border-gray-200">
                                    <div class="font-bold text-red-600" x-text="getDeadline(sidang.tanggal_sidang)"></div>
                                    <div class="text-[12px] text-gray-400 font-medium uppercase mt-0.5">Pukul 23:59 WIB</div>
                                </td>
                                <td class="py-3 px-4 text-center border-r border-gray-200">
                                    <template x-if="sidang.tanggal_revisi">
                                        <div>
                                            <div class="font-bold" :class="isLate(sidang) ? 'text-red-600' : 'text-blue-600'" x-text="formatDate(sidang.tanggal_revisi)"></div>
                                            <div class="text-[12px] font-medium uppercase mt-0.5" :class="isLate(sidang) ? 'text-red-400' : 'text-gray-400'" x-text="formatTime(sidang.tanggal_revisi) + ' WIB'"></div>
                                            <template x-if="isLate(sidang)">
                                                <div class="text-[10px] text-red-500 font-bold uppercase mt-1">Terlambat</div>
                                            </template>
                                        </div>
                                    </template>
                                    <template x-if="!sidang.tanggal_revisi">
                                        <div class="flex flex-col items-center">
                                            <span class="italic font-medium" :class="isLate(sidang) ? 'text-red-500' : 'text-gray-300'" x-text="isLate(sidang) ? 'Terlambat' : '-'"></span>
                                        </div>
                                    </template>
                                </td>
                                <td class="py-3 px-4 text-center border-r border-gray-200">
                                    <div class="flex flex-col gap-2 items-center">
                                        <template x-if="sidang.file_revisi">
                                            <a :href="sidang.file_revisi_url" target="_blank" class="text-blue-600 hover:underline font-bold flex items-center gap-1 text-[12px]">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg> PDF
                                            </a>
                                        </template>
                                        <template x-if="sidang.link_revisi">
                                            <a :href="sidang.link_revisi" target="_blank" class="text-blue-600 hover:underline font-bold flex items-center gap-1 text-[12px]">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg> DRIVE
                                            </a>
                                        </template>
                                        <template x-if="!sidang.file_revisi && !sidang.link_revisi">
                                            <span class="text-gray-400 italic text-[12px]">Belum Ada</span>
                                        </template>
                                    </div>
                                </td>
                                <td class="py-3 px-4 text-center border-r border-gray-200">
                                    <template x-if="sidang.status_revisi === 'Menunggu'">
                                        <div class="flex gap-2">
                                            @if(!isset($isReadOnly) || !$isReadOnly)
                                            <template x-if="sidang.penguji_1_id == {{ auth()->id() }}">
                                                <button type="button" @click="openEditModal(sidang)" class="w-full bg-[#FBBC05] hover:bg-yellow-500 text-black font-bold text-[11px] px-3 py-1.5 rounded-[4px] shadow-sm transition-colors uppercase flex items-center justify-center gap-1">
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg> Edit Nilai
                                                </button>
                                            </template>
                                            <template x-if="sidang.penguji_1_id != {{ auth()->id() }}">
                                                <span class="text-gray-400 text-[11px] font-bold uppercase">Read Only</span>
                                            </template>
                                            @else
                                            <span class="text-gray-400 text-[11px] font-bold uppercase">Read Only</span>
                                            @endif
                                        </div>
                                    </template>
                                    <template x-if="sidang.status_revisi !== 'Menunggu' && sidang.status_revisi !== 'Belum mengumpulkan'">
                                        <span class="text-gray-400 text-[11px] font-bold uppercase">-</span>
                                    </template>
                                </td>
                                <td class="py-3 px-4 text-center">
                                    <div class="flex items-center justify-center gap-2">
                                        <template x-if="sidang.status_revisi === 'Menunggu'">
                                            <div class="flex gap-2 w-full">
                                                @if(!isset($isReadOnly) || !$isReadOnly)
                                                <template x-if="sidang.penguji_1_id == {{ auth()->id() }}">
                                                    <div class="flex flex-col gap-2 w-full">
                                                        <div class="flex gap-2 justify-center">
                                                            <form :action="'{{ url('koordinator/revisi') }}/' + sidang.id + '/terima'" method="POST" @submit="!confirm('Sahkan revisi mahasiswa ini?') && $event.preventDefault()">
                                                                @csrf
                                                                <button type="submit" class="bg-[#34A853] hover:bg-green-700 text-white font-bold text-[12px] px-3 py-1.5 rounded-[4px] shadow-sm transition-colors uppercase w-full">Sahkan</button>
                                                            </form>
                                                            <form :action="'{{ url('koordinator/revisi') }}/' + sidang.id + '/tolak'" method="POST" @submit="!confirm('Tolak revisi mahasiswa ini?') && $event.preventDefault()">
                                                                @csrf
                                                                <button type="submit" class="bg-[#EA4335] hover:bg-red-700 text-white font-bold text-[12px] px-3 py-1.5 rounded-[4px] shadow-sm transition-colors uppercase w-full">Tolak</button>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </template>
                                                <template x-if="sidang.penguji_1_id != {{ auth()->id() }}">
                                                    <span class="text-[10px] text-red-500 font-bold uppercase tracking-wide bg-gray-100 px-3 py-1.5 rounded border border-gray-200 w-full text-center">Read Only</span>
                                                </template>
                                                @else
                                                <div class="bg-gray-200 text-gray-500 font-bold px-3 py-1.5 rounded text-[11px] uppercase flex items-center justify-center w-full">
                                                    Read Only
                                                </div>
                                                @endif
                                            </div>
                                        </template>
                                        <template x-if="sidang.status_revisi === 'Disahkan' || sidang.status_revisi === 'Diterima'">
                                            <span class="inline-flex items-center gap-1.5 bg-green-100 text-green-700 px-3 py-1 rounded-full font-bold text-[12px] uppercase shadow-sm">Diterima</span>
                                        </template>
                                        <template x-if="sidang.status_revisi === 'Ditolak'">
                                            <span class="inline-flex items-center gap-1.5 bg-red-100 text-red-700 px-3 py-1 rounded-full font-bold text-[12px] uppercase shadow-sm">Ditolak</span>
                                        </template>
                                        <template x-if="sidang.status_revisi === 'Belum mengumpulkan'">
                                            <span class="inline-flex items-center gap-1.5 bg-yellow-100 text-yellow-700 px-3 py-1 rounded-full font-bold text-[12px] uppercase shadow-sm">Menunggu</span>
                                        </template>
                                    </div>
                                </td>
                            </tr>
                        </template>
                        <template x-if="filteredSidangs.length === 0">
                            <tr>
                                <td colspan="8" class="py-12 text-center text-gray-500 italic text-[12px]">Tidak ada data mahasiswa revisi yang sesuai pencarian/filter.</td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="px-6 py-4 bg-white flex items-center justify-between border-t border-gray-200" x-show="totalPages > 1">
                <span class="text-[12px] font-medium text-black/50" x-text="(filteredSidangs.length === 0 ? 0 : ((currentPage - 1) * itemsPerPage + 1)) + ' - ' + Math.min(currentPage * itemsPerPage, filteredSidangs.length) + ' dari ' + filteredSidangs.length + ' baris'"></span>
                <div class="flex items-center gap-2">
                    <button @click="if(currentPage > 1) currentPage--" :disabled="currentPage === 1" class="px-3 py-1 border border-gray-300 rounded text-[12px] hover:bg-gray-50 disabled:opacity-30 disabled:cursor-not-allowed transition-colors">Previous</button>
                    <div class="flex items-center gap-1">
                        <template x-for="p in totalPages" :key="p">
                            <button @click="currentPage = p" class="w-8 h-8 rounded text-[12px] font-bold transition-all" :class="currentPage === p ? 'bg-blue-600 text-white shadow-md' : 'text-black hover:bg-gray-100'" x-text="p"></button>
                        </template>
                    </div>
                    <button @click="if(currentPage < totalPages) currentPage++" :disabled="currentPage === totalPages" class="px-3 py-1 border border-gray-300 rounded text-[12px] hover:bg-gray-50 disabled:opacity-30 disabled:cursor-not-allowed transition-colors">Next</button>
                </div>
            </div>
        </div>

        <!-- Tabel Status Pengumpulan Mahasiswa -->
        <div class="mt-16 bg-white rounded-[15px] border border-gray-200 shadow-sm overflow-hidden p-6 mb-8">
            <div class="flex flex-col sm:flex-row justify-between items-start gap-4 mb-8">
                <div>
                    <h3 class="text-[18px] font-bold text-black uppercase tracking-tight">Status Pengumpulan Revisi Mahasiswa</h3>
                    <p class="text-[12px] text-black/60 font-medium mt-1">Rekapitulasi pengumpulan revisi oleh seluruh mahasiswa bimbingan.</p>
                </div>
                <div class="flex gap-2 shrink-0">
                    <div class="bg-[#EA4335] text-white rounded-[5px] px-3 py-1.5 flex items-center gap-2 shadow-sm border border-red-600/20">
                        <span class="text-[16px] font-bold leading-none" x-text="stats.belum"></span>
                        <span class="text-[11px] font-medium uppercase tracking-wider">Belum Mengumpulkan</span>
                    </div>
                    <div class="bg-[#34A853] text-white rounded-[5px] px-3 py-1.5 flex items-center gap-2 shadow-sm border border-green-600/20">
                        <span class="text-[16px] font-bold leading-none" x-text="sidangs.length - stats.belum"></span>
                        <span class="text-[11px] font-medium uppercase tracking-wider">Sudah Mengumpulkan</span>
                    </div>
                </div>
            </div>

            <div class="flex flex-col sm:flex-row justify-between items-center gap-6 mb-6">
                <div class="flex flex-wrap items-center gap-4 w-full sm:w-auto">
                    <div class="relative flex-1 sm:w-[300px]">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        </div>
                        <input type="text" x-model="searchStatus" class="block w-full pl-9 pr-4 py-2 border border-gray-300 rounded-[5px] text-[12px] text-black focus:ring-[#4285F4]" placeholder="Cari Nama/NIM Mahasiswa...">
                    </div>

                    <div x-data="{ openFilter: false }" class="relative w-full sm:w-[180px] z-[40]">
                        <button type="button" @click="openFilter = !openFilter" @click.outside="openFilter = false" class="w-full text-[12px] border border-gray-300 rounded-[5px] py-2 px-3 bg-white text-black font-medium focus:ring-[#4285F4] flex justify-between items-center text-left shadow-sm">
                            <span x-text="filterStatusUpload === 'all' ? 'Status Pengumpulan' : (filterStatusUpload === 'sudah' ? 'Sudah Mengumpulkan' : 'Belum Mengumpulkan')"></span>
                            <svg :class="openFilter ? 'rotate-0' : 'rotate-90'" class="w-3.5 h-3.5 transition-all duration-200 text-gray-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <div x-show="openFilter" x-transition x-cloak class="absolute w-full mt-1 bg-white border border-gray-300 rounded-[5px] shadow-lg overflow-hidden py-1 z-50">
                            <label class="block px-3 py-2 text-[12px] hover:bg-gray-100 cursor-pointer text-black"><input type="radio" value="all" x-model="filterStatusUpload" class="hidden" @change="openFilter = false">Semua</label>
                            <label class="block px-3 py-2 text-[12px] hover:bg-gray-100 cursor-pointer text-black"><input type="radio" value="sudah" x-model="filterStatusUpload" class="hidden" @change="openFilter = false">Sudah Mengumpulkan</label>
                            <label class="block px-3 py-2 text-[12px] hover:bg-gray-100 cursor-pointer text-black"><input type="radio" value="belum" x-model="filterStatusUpload" class="hidden" @change="openFilter = false">Belum Mengumpulkan</label>
                        </div>
                    </div>

                    <div class="flex gap-2 w-full sm:w-auto">
                        <button type="button" @click="searchStatus = ''; filterStatusUpload = 'all'; statusCurrentPage = 1" class="flex-1 sm:flex-none border border-[#EA4335] bg-[#EA4335] text-white hover:bg-red-600 transition-colors px-4 py-1.5 rounded-[5px] text-[12px] font-bold shadow-sm flex items-center justify-center">
                            Clear Filter
                        </button>
                    </div>
                </div>
            </div>

            <div class="border border-gray-200 rounded-[10px] overflow-hidden">
                <table class="w-full border-collapse text-[12px]">
                    <thead>
                        <tr class="bg-[#EBEBEB] text-black">
                            <th class="py-3 px-4 font-bold text-center w-[60px] border-b border-r border-gray-300">No</th>
                            <th class="py-3 px-4 font-bold text-left border-b border-r border-gray-300 w-[150px]">NIM</th>
                            <th class="py-3 px-4 font-bold text-left border-b border-r border-gray-300">Nama Mahasiswa</th>
                            <th class="py-3 px-4 font-bold text-center border-b border-gray-300">Status Pengumpulan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        <template x-for="(stat, index) in paginatedStatusRows" :key="stat.id">
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="py-3 px-4 text-center text-black/60 border-r border-gray-200" x-text="(statusCurrentPage - 1) * statusItemsPerPage + index + 1"></td>
                                <td class="py-3 px-4 text-left font-mono text-black border-r border-gray-200" x-text="stat.mahasiswa.nim"></td>
                                <td class="py-3 px-4 text-left font-bold text-black uppercase border-r border-gray-200" x-text="stat.mahasiswa.user.name"></td>
                                <td class="py-3 px-4 text-center">
                                    <template x-if="stat.status_revisi && stat.status_revisi !== 'Belum mengumpulkan'">
                                        <span class="inline-flex items-center gap-1.5 bg-green-100 text-green-700 px-4 py-1 rounded-full font-bold text-[10px] uppercase">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg> Sudah Mengumpulkan
                                        </span>
                                    </template>
                                    <template x-if="!stat.status_revisi || stat.status_revisi === 'Belum mengumpulkan'">
                                        <span class="text-black/40 font-medium text-[11px] italic tracking-tight">Menunggu Pengumpulan ...</span>
                                    </template>
                                </td>
                            </tr>
                        </template>
                        <template x-if="filteredStatusRows.length === 0">
                            <tr>
                                <td colspan="4" class="py-12 text-center text-gray-500 italic text-[12px]">Tidak Ada Data Ditemukan</td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>

            <!-- Pagination Footer Status -->
            <div class="px-6 py-4 bg-white flex items-center justify-between border-t border-gray-200" x-show="totalStatusPages > 1">
                <span class="text-[12px] font-medium text-black/50" x-text="(filteredStatusRows.length === 0 ? 0 : ((statusCurrentPage - 1) * statusItemsPerPage + 1)) + ' - ' + Math.min(statusCurrentPage * statusItemsPerPage, filteredStatusRows.length) + ' dari ' + filteredStatusRows.length + ' baris'"></span>
                <div class="flex items-center gap-2">
                    <button @click="if(statusCurrentPage > 1) statusCurrentPage--" :disabled="statusCurrentPage === 1" class="px-3 py-1 border border-gray-300 rounded text-[12px] hover:bg-gray-50 disabled:opacity-30 disabled:cursor-not-allowed transition-colors">Previous</button>
                    <div class="flex items-center gap-1">
                        <template x-for="p in totalStatusPages" :key="p">
                            <button @click="statusCurrentPage = p" class="w-8 h-8 rounded text-[12px] font-bold transition-all" :class="statusCurrentPage === p ? 'bg-blue-600 text-white shadow-md' : 'text-black hover:bg-gray-100'" x-text="p"></button>
                        </template>
                    </div>
                    <button @click="if(statusCurrentPage < totalStatusPages) statusCurrentPage++" :disabled="statusCurrentPage === totalStatusPages" class="px-3 py-1 border border-gray-300 rounded text-[12px] hover:bg-gray-50 disabled:opacity-30 disabled:cursor-not-allowed transition-colors">Next</button>
                </div>
            </div>
        </div>

        <!-- Edit Nilai Modal -->
        <div x-cloak x-show="editModal.show" style="display: none;" class="fixed inset-0 z-[100] flex items-center justify-center bg-black/50 backdrop-blur-sm p-4">
            <div @click.away="editModal.show = false" class="bg-white rounded-[15px] w-full max-w-[500px] shadow-2xl flex flex-col overflow-hidden border border-gray-100">
                <div class="p-6 border-b border-gray-100">
                    <h3 class="text-[18px] font-bold text-gray-900 uppercase">Edit Nilai Penguji 1</h3>
                    <p class="text-[12px] text-gray-500 mt-1">Sesuaikan nilai untuk mahasiswa <span class="font-bold uppercase text-black" x-text="editModal.mahasiswaName"></span>.</p>
                </div>
                
                <div class="p-6">
                    <form id="editNilaiForm" :action="'{{ url('koordinator/revisi') }}/' + editModal.id + '/update-nilai'" method="POST">
                        @csrf
                        <div class="space-y-4">
                            <!-- Laporan -->
                            <div class="flex flex-col gap-1.5">
                                <label class="text-[12px] font-bold text-gray-700 uppercase">Nilai Laporan <span class="text-red-500">*</span></label>
                                <input type="number" name="n1_laporan" x-model="editModal.n1_laporan" step="0.01" min="0" max="100" required
                                    class="w-full px-4 py-2 bg-gray-50 border border-gray-200 rounded-[8px] text-[13px] font-medium text-black focus:bg-white focus:border-[#4285F4] focus:ring-1 focus:ring-[#4285F4] transition-all outline-none">
                            </div>
                            <!-- Produk -->
                            <div class="flex flex-col gap-1.5">
                                <label class="text-[12px] font-bold text-gray-700 uppercase">Nilai Produk <span class="text-red-500">*</span></label>
                                <input type="number" name="n1_produk" x-model="editModal.n1_produk" step="0.01" min="0" max="100" required
                                    class="w-full px-4 py-2 bg-gray-50 border border-gray-200 rounded-[8px] text-[13px] font-medium text-black focus:bg-white focus:border-[#4285F4] focus:ring-1 focus:ring-[#4285F4] transition-all outline-none">
                            </div>
                            <!-- Presentasi -->
                            <div class="flex flex-col gap-1.5">
                                <label class="text-[12px] font-bold text-gray-700 uppercase">Nilai Presentasi <span class="text-red-500">*</span></label>
                                <input type="number" name="n1_presentasi" x-model="editModal.n1_presentasi" step="0.01" min="0" max="100" required
                                    class="w-full px-4 py-2 bg-gray-50 border border-gray-200 rounded-[8px] text-[13px] font-medium text-black focus:bg-white focus:border-[#4285F4] focus:ring-1 focus:ring-[#4285F4] transition-all outline-none">
                            </div>
                        </div>

                        <div class="flex gap-4 mt-8">
                            <button type="button" @click="editModal.show = false" class="flex-1 h-[45px] bg-gray-50 hover:bg-gray-100 text-gray-700 rounded-[10px] text-[14px] font-bold transition-all border border-gray-200">
                                Batal
                            </button>
                            <button type="submit" class="flex-1 h-[45px] bg-[#4285F4] hover:bg-blue-600 text-white rounded-[10px] text-[14px] font-bold transition-all shadow-md active:transform active:scale-95 flex items-center justify-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg> Simpan Perubahan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
    </div>

    @php
        $mappedSidangs = $sidangs->map(function ($item) {
            $arr = $item->toArray();
            // Assign relasi manual agar tidak hilang saat di-toArray()
            $arr['mahasiswa'] = $item->mahasiswa ? $item->mahasiswa->toArray() : null;
            if ($item->mahasiswa && $item->mahasiswa->user) {
                $arr['mahasiswa']['user'] = $item->mahasiswa->user->toArray();
            }
            $arr['file_revisi_url'] = $item->file_revisi ? storage_url($item->file_revisi) : null;
            return $arr;
        });
    @endphp

    <script>
        window.revisiPage = function() {
            return {
                sidangs: @json($mappedSidangs).map(s => {
                    if (!s.status_revisi) s.status_revisi = 'Belum mengumpulkan';
                    return s;
                }),
                search: '',
                filterStatus: 'all',
                filterWaktu: 'all',
                currentPage: 1,
                itemsPerPage: 10,

                // Logic for Status Table
                searchStatus: '',
                filterStatusUpload: 'all',
                statusCurrentPage: 1,
                statusItemsPerPage: 10,

                editModal: {
                    show: false,
                    id: null,
                    mahasiswaName: '',
                    n1_laporan: 0,
                    n1_produk: 0,
                    n1_presentasi: 0
                },

                init() {
                    this.$watch('search', () => this.currentPage = 1);
                    this.$watch('filterStatus', () => this.currentPage = 1);
                    this.$watch('filterWaktu', () => this.currentPage = 1);
                    this.$watch('searchStatus', () => this.statusCurrentPage = 1);
                    this.$watch('filterStatusUpload', () => this.statusCurrentPage = 1);
                },

                clearFilters() {
                    this.search = '';
                    this.filterStatus = 'all';
                    this.filterWaktu = 'all';
                    this.currentPage = 1;
                },

                get stats() {
                    const submittedSidangs = this.sidangs.filter(s => s.status_revisi && s.status_revisi !== 'Belum mengumpulkan');
                    return {
                        total: submittedSidangs.length,
                        belum: this.sidangs.filter(s => !s.status_revisi || s.status_revisi === 'Belum mengumpulkan').length,
                        menunggu: this.sidangs.filter(s => s.status_revisi === 'Menunggu').length,
                        disahkan: this.sidangs.filter(s => s.status_revisi === 'Disahkan' || s.status_revisi === 'Diterima').length,
                        ditolak: this.sidangs.filter(s => s.status_revisi === 'Ditolak').length,
                    }
                },

                get filteredSidangs() {
                    let res = [...this.sidangs];
                    if (this.search) {
                        const q = this.search.toLowerCase();
                        res = res.filter(s => s.mahasiswa.nim.toLowerCase().includes(q) || s.mahasiswa.user.name.toLowerCase().includes(q));
                    }
                    if (this.filterStatus !== 'all') {
                        res = res.filter(s => {
                            if (this.filterStatus === 'Disahkan') return s.status_revisi === 'Disahkan' || s.status_revisi === 'Diterima';
                            return s.status_revisi === this.filterStatus;
                        });
                    }
                    if (this.filterWaktu !== 'all') {
                        res = res.filter(s => {
                            if (this.filterWaktu === 'Terlambat') return this.isLate(s);
                            if (this.filterWaktu === 'Tepat Waktu') return !this.isLate(s);
                            return true;
                        });
                    }
                    return res;
                },

                get paginatedSidangs() {
                    const start = (this.currentPage - 1) * this.itemsPerPage;
                    return this.filteredSidangs.slice(start, start + this.itemsPerPage);
                },

                openEditModal(sidang) {
                    this.editModal.id = sidang.id;
                    this.editModal.mahasiswaName = sidang.mahasiswa.user.name;
                    this.editModal.n1_laporan = sidang.n1_laporan;
                    this.editModal.n1_produk = sidang.n1_produk;
                    this.editModal.n1_presentasi = sidang.n1_presentasi;
                    this.editModal.show = true;
                },

                get totalPages() {
                    return Math.ceil(this.filteredSidangs.length / this.itemsPerPage) || 1;
                },

                // Logic for Status Table Filtering
                get filteredStatusRows() {
                    let res = [...this.sidangs];
                    if (this.searchStatus) {
                        const q = this.searchStatus.toLowerCase();
                        res = res.filter(s => s.mahasiswa.nim.toLowerCase().includes(q) || s.mahasiswa.user.name.toLowerCase().includes(q));
                    }
                    if (this.filterStatusUpload !== 'all') {
                        if (this.filterStatusUpload === 'sudah') {
                            res = res.filter(s => s.status_revisi && s.status_revisi !== 'Belum mengumpulkan');
                        } else {
                            res = res.filter(s => !s.status_revisi || s.status_revisi === 'Belum mengumpulkan');
                        }
                    }
                    return res;
                },

                get paginatedStatusRows() {
                    const start = (this.statusCurrentPage - 1) * this.statusItemsPerPage;
                    return this.filteredStatusRows.slice(start, start + this.statusItemsPerPage);
                },

                get totalStatusPages() {
                    return Math.ceil(this.filteredStatusRows.length / this.statusItemsPerPage) || 1;
                },

                formatDate(dateString) {
                    if (!dateString) return '-';
                    return new Date(dateString.includes(' ') ? dateString.replace(' ', 'T') : dateString).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' });
                },

                formatTime(timeString) {
                    if (!timeString) return '-';
                    if (timeString.includes(' ')) {
                        return timeString.split(' ')[1].substring(0, 5);
                    }
                    if (timeString.includes('T')) {
                        const d = new Date(timeString);
                        return d.getHours().toString().padStart(2, '0') + ':' + d.getMinutes().toString().padStart(2, '0');
                    }
                    return timeString.substring(0, 5);
                },

                getDeadline(dateString) {
                    if (!dateString) return '-';
                    const d = new Date(dateString);
                    d.setDate(d.getDate() + 5);
                    return d.toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' });
                },

                isLate(sidang) {
                    if (!sidang.tanggal_sidang) return false;
                    const deadline = new Date(sidang.tanggal_sidang);
                    deadline.setDate(deadline.getDate() + 5);
                    deadline.setHours(23, 59, 59, 999);
                    
                    const submitDate = sidang.tanggal_revisi ? new Date(sidang.tanggal_revisi) : new Date();
                    return submitDate > deadline;
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
