<x-dashboard-layout header="Verifikasi Berkas Sidang KP" userName="{{ auth()->user()->name }}" roleName="KOORDINATOR KP">
    <x-slot:sidebar>
        @include('koordinator.components.sidebar', ['active' => 'verifikasi'])
    </x-slot>

    

    <style>
        [x-cloak] { display: none !important; }
        .custom-scrollbar::-webkit-scrollbar { width: 5px; height: 5px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: #f1f1f1; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #ccc; border-radius: 10px; }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #999; }
        .sentence-case { text-transform: lowercase; }
        .sentence-case::first-letter { text-transform: uppercase; }
    </style>

    @php
        $mapper = function ($item) {
            $isComplete = $item->file_laporan && $item->file_log_bimbingan && $item->link_github && $item->link_deploy && $item->status_verifikasi === 'verified';
            return [
                'id' => $item->id,
                'name' => $item->mahasiswa->user->name ?? 'N/A',
                'nim' => $item->mahasiswa->nim ?? 'N/A',
                'status' => $item->status_koordinator,
                'is_complete' => $isComplete,
                'file_laporan' => $item->file_laporan ? storage_url($item->file_laporan) : null,
                'file_log_bimbingan' => $item->file_log_bimbingan ? storage_url($item->file_log_bimbingan) : null,
                'file_persetujuan' => $item->status_verifikasi === 'verified' ? route('koordinator.persetujuan-sidang.cetak', $item->id) : null,
                'file_lainnya' => $item->file_berkas_lainnya ? storage_url($item->file_berkas_lainnya) : null,
                'link_drive' => $item->link_drive,
                'link_github' => $item->link_github,
                'link_deploy' => $item->link_deploy,
                'feedback' => $item->koordinator_feedback,
                'token_penilaian_supervisor' => $item->token_penilaian_supervisor,
            ];
        };

        $mainRows = $pengajuans->map(function ($item) use ($mapper) {
            $mapped = $mapper($item);
            $mapped['expanded'] = false;
            return $mapped;
        });

        $allVerifikasiRows = $mainRows->sortBy('nim')->values();

        $activePeriodId = session('selected_periode_id') ?? \App\Models\TahunAjaran::aktif()->id;
        
        // Get students who have a KP registration in this period
        $semuaMahasiswa = \App\Models\Mahasiswa::with('user')
            ->whereIn('user_id', function($query) use ($activePeriodId) {
                $query->select('mahasiswa_id')
                      ->from('pendaftaran_kp')
                      ->where('tahun_ajaran_id', $activePeriodId);
            })->get();

        $allStatusRows = $semuaMahasiswa->map(function($mhs) use ($pengajuans) {
            $pengajuan = $pengajuans->where('mahasiswa_id', $mhs->user_id)->first();
            $isSudah = $pengajuan && $pengajuan->status_koordinator !== 'rejected';
            return [
                'nim'    => $mhs->nim ?? '-',
                'name'   => $mhs->user->name ?? '-',
                'status' => $isSudah ? 'Sudah Upload' : 'Belum Upload'
            ];
        })->sortBy('nim')->values();
    @endphp

    <div class="mt-6" x-data="verifikasiTable()">
        <div class="flex flex-col xl:flex-row gap-6 mb-8 items-start xl:items-stretch">
            <div class="flex-1 bg-[#EAEFFF] border border-[#BACDFB] rounded-[10px] p-4 flex items-center gap-4 shadow-sm">
                <div class="bg-[#7896F8] w-6 h-6 rounded-full flex items-center justify-center text-white shrink-0 shadow-sm font-serif italic text-sm">i</div>
                <p class="text-[14px] text-black font-medium leading-relaxed">
                    Tinjau dan Sahkan Kelengkapan Berkas Yang dikirimkan Mahasiswa Sebagai Syarat Melakukan Sidang KP.
                </p>
            </div>
            <div class="flex gap-4">
                <div class="bg-[#38913B] rounded-[10px] p-3 flex flex-col justify-center items-center w-[90px] shadow-sm text-white">
                    <div class="flex items-center gap-2">
                        <div class="bg-white/20 p-1 rounded-full"><svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg></div>
                        <span class="text-xl font-bold" x-text="statDisahkan"></span>
                    </div>
                    <span class="text-[11px] font-medium mt-1">Disahkan</span>
                </div>
                <div class="bg-[#FBC610] rounded-[10px] p-3 flex flex-col justify-center items-center w-[90px] shadow-sm text-black">
                    <div class="flex items-center gap-2">
                        <div class="border border-black p-0.5 rounded-sm"><svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"></path></svg></div>
                        <span class="text-xl font-bold" x-text="statBelum"></span>
                    </div>
                    <span class="text-[11px] font-medium text-center leading-tight mt-1">Belum<br>Diperiksa</span>
                </div>
                <div class="bg-[#EA3323] rounded-[10px] p-3 flex flex-col justify-center items-center w-[90px] shadow-sm text-white">
                    <div class="flex items-center gap-2">
                        <div class="bg-white/20 p-1 rounded-full"><svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg></div>
                        <span class="text-xl font-bold" x-text="statDitolak"></span>
                    </div>
                    <span class="text-[11px] font-medium mt-1">Ditolak</span>
                </div>
            </div>
        </div>

        <!-- Berkas Unggahan Section -->
        <div class="mt-8 bg-white rounded-[15px] border border-gray-200 shadow-sm overflow-hidden p-6 mb-12">
            <div class="flex flex-col xl:flex-row justify-between items-start xl:items-center gap-6 mb-8">
                <div>
                    <h3 class="text-[18px] font-bold text-black tracking-tight">Berkas Unggahan</h3>
                    <p class="text-[12px] text-black/60 font-medium">Tinjau dan sahkan kelengkapan berkas sidang mahasiswa</p>
                </div>

                <div class="flex flex-col sm:flex-row items-center gap-4 w-full xl:w-auto">
                    <div class="relative flex-1 w-full sm:w-[250px]">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        </div>
                        <input type="text" x-model="searchQuery" @input="currentPage = 1" class="block w-full pl-9 pr-4 py-2 border border-gray-300 rounded-[5px] text-[12px] text-black focus:ring-[#4285F4] shadow-sm" placeholder="Cari Nama/NIM...">
                    </div>

                    <div x-data="{ openStatus: false }" class="relative w-full sm:w-[150px] z-[60]" @click.outside="openStatus = false">
                        <button type="button" @click="openStatus = !openStatus" class="w-full text-[12px] border border-gray-300 rounded-[5px] py-2 px-3 bg-white text-black font-medium focus:ring-[#4285F4] flex justify-between items-center text-left shadow-sm">
                            <span class="truncate" x-text="filterStatus === 'all' ? 'Semua Status' : (filterStatus === 'pending' ? 'Belum Diperiksa' : 'Disahkan')"></span>
                            <svg :class="openStatus ? 'rotate-0' : 'rotate-90'" class="w-3.5 h-3.5 transition-all duration-200 text-gray-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <div x-show="openStatus" x-transition x-cloak class="absolute w-full mt-1 bg-white border border-gray-300 rounded-[5px] shadow-lg overflow-hidden py-1 z-50">
                            <label class="block px-3 py-2 text-[12px] hover:bg-gray-100 cursor-pointer text-black"><input type="radio" value="all" x-model="filterStatus" class="hidden" @change="openStatus = false; currentPage = 1">Semua Status</label>
                            <label class="block px-3 py-2 text-[12px] hover:bg-gray-100 cursor-pointer text-black"><input type="radio" value="pending" x-model="filterStatus" class="hidden" @change="openStatus = false; currentPage = 1">Belum Diperiksa</label>
                            <label class="block px-3 py-2 text-[12px] hover:bg-gray-100 cursor-pointer text-black"><input type="radio" value="verified" x-model="filterStatus" class="hidden" @change="openStatus = false; currentPage = 1">Disahkan</label>
                        </div>
                    </div>

                    <div x-data="{ openKondisi: false }" class="relative w-full sm:w-[150px] z-[50]" @click.outside="openKondisi = false">
                        <button type="button" @click="openKondisi = !openKondisi" class="w-full text-[12px] border border-gray-300 rounded-[5px] py-2 px-3 bg-white text-black font-medium focus:ring-[#4285F4] flex justify-between items-center text-left shadow-sm">
                            <span class="truncate" x-text="filterKondisiMain === 'all' ? 'Semua Kondisi' : (filterKondisiMain === 'lengkap' ? 'Lengkap' : 'Tidak Lengkap')"></span>
                            <svg :class="openKondisi ? 'rotate-0' : 'rotate-90'" class="w-3.5 h-3.5 transition-all duration-200 text-gray-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <div x-show="openKondisi" x-transition x-cloak class="absolute w-full mt-1 bg-white border border-gray-300 rounded-[5px] shadow-lg overflow-hidden py-1 z-50">
                            <label class="block px-3 py-2 text-[12px] hover:bg-gray-100 cursor-pointer text-black"><input type="radio" value="all" x-model="filterKondisiMain" class="hidden" @change="openKondisi = false; currentPage = 1">Semua Kondisi</label>
                            <label class="block px-3 py-2 text-[12px] hover:bg-gray-100 cursor-pointer text-black"><input type="radio" value="lengkap" x-model="filterKondisiMain" class="hidden" @change="openKondisi = false; currentPage = 1">Lengkap</label>
                            <label class="block px-3 py-2 text-[12px] hover:bg-gray-100 cursor-pointer text-black"><input type="radio" value="tidak_lengkap" x-model="filterKondisiMain" class="hidden" @change="openKondisi = false; currentPage = 1">Tidak Lengkap</label>
                        </div>
                    </div>

                    <button @click="clearMainFilter()" class="w-full sm:w-auto bg-[#EA3323] hover:bg-red-700 text-white font-bold text-[12px] px-4 py-2 rounded-[5px] shadow-sm transition-colors whitespace-nowrap shrink-0 flex items-center justify-center">
                        Clear Filter
                    </button>
                </div>
            </div>

            <div class="border border-gray-200 rounded-[10px] overflow-hidden">
                <div class="overflow-x-auto custom-scrollbar">
                    <table class="w-full border-collapse text-[11px]" style="min-width: 800px;">
                    <thead>
                        <tr class="bg-[#EBEBEB] text-black">
                            <th class="py-3 px-4 font-bold text-center w-[50px] border-b border-r border-gray-300">No</th>
                            <th class="py-3 px-4 font-bold text-left w-[120px] border-b border-r border-gray-300">NIM</th>
                            <th class="py-3 px-4 font-bold text-left border-b border-r border-gray-300">Nama Mahasiswa</th>
                            <th class="py-3 px-4 font-bold text-center w-[140px] border-b border-r border-gray-300">Kondisi</th>
                            <th class="py-3 px-4 font-bold text-center w-[180px] border-b border-gray-300">Aksi & Status</th>
                        </tr>
                    </thead>
                    <template x-for="(row, index) in paginatedRows" :key="row.id">
                        <tbody class="bg-white border-b border-gray-100 transition-colors">
                            <tr class="hover:bg-blue-50/40 transition-all duration-200 cursor-pointer group" @click="row.expanded = !row.expanded">
                                <td class="py-4 px-4 text-center text-gray-500 font-medium border-r border-gray-100" x-text="(currentPage - 1) * itemsPerPage + index + 1"></td>
                                <td class="py-4 px-4 text-left border-r border-gray-100 font-medium text-black text-[12px]" x-text="row.nim"></td>
                                <td class="py-4 px-4 text-left border-r border-gray-100">
                                    <div class="font-normal text-black sentence-case text-[12px]" x-text="row.name"></div>
                                    <template x-if="row.status === 'rejected' && row.feedback">
                                        <div class="mt-2.5 p-2.5 bg-red-50 border-l-[3px] border-red-500 rounded-r text-[10.5px] text-red-700 italic leading-relaxed w-max max-w-md shadow-sm">
                                            <b class="font-bold not-italic">Catatan Revisi:</b> <span x-text="row.feedback"></span>
                                        </div>
                                    </template>
                                </td>
                                <td class="py-4 px-4 text-center border-r border-gray-100">
                                    <template x-if="row.is_complete">
                                        <span class="inline-flex items-center gap-1.5 bg-[#E6F4EA] text-[#137333] border border-[#CEEAD6] px-3.5 py-1.5 rounded-full font-bold text-[10px] uppercase shadow-sm tracking-wide">
                                            <div class="w-1.5 h-1.5 rounded-full bg-[#137333]"></div> Lengkap
                                        </span>
                                    </template>
                                    <template x-if="!row.is_complete">
                                        <span class="inline-flex items-center gap-1.5 bg-[#FCE8E6] text-[#C5221F] border border-[#FAD2CF] px-3.5 py-1.5 rounded-full font-bold text-[10px] uppercase shadow-sm tracking-wide">
                                            <div class="w-1.5 h-1.5 rounded-full bg-[#C5221F]"></div> Tdk Lengkap
                                        </span>
                                    </template>
                                </td>
                                <td class="py-4 px-4 text-center" @click.stop>
                                    <template x-if="row.status === 'pending'">
                                        <div class="flex items-center justify-center gap-2">
                                            <button @click="updateRowStatus(row.id, 'verified')" :disabled="isUpdating" class="bg-[#1A73E8] hover:bg-blue-700 text-white text-[10px] font-bold px-3.5 py-1.5 rounded-[6px] shadow-sm transition-colors uppercase tracking-wide">Sahkan</button>
                                            <button @click="openTolakModal(row.id)" :disabled="isUpdating" class="bg-white border border-gray-300 hover:bg-red-50 hover:text-red-600 hover:border-red-200 text-gray-700 text-[10px] font-bold px-3.5 py-1.5 rounded-[6px] shadow-sm transition-colors uppercase tracking-wide">Tolak</button>
                                        </div>
                                    </template>
                                    <template x-if="row.status === 'verified'">
                                        <span class="bg-[#E6F4EA] text-[#137333] border border-[#CEEAD6] font-bold px-5 py-1.5 rounded-full text-[10px] flex items-center justify-center w-max mx-auto gap-1.5 shadow-sm uppercase tracking-wide">
                                            Selesai
                                        </span>
                                    </template>
                                    <template x-if="row.status === 'rejected'">
                                        <span class="bg-[#FCE8E6] text-[#C5221F] border border-[#FAD2CF] font-bold px-5 py-1.5 rounded-full text-[10px] shadow-sm uppercase tracking-wide">Ditolak</span>
                                    </template>
                                </td>
                            </tr>
                            <!-- Expandable Row for Details (Animated Slide Down) -->
                            <tr>
                                <td colspan="5" class="p-0 border-0">
                                    <div class="grid transition-all duration-300 ease-in-out" :class="row.expanded ? 'grid-rows-[1fr] opacity-100' : 'grid-rows-[0fr] opacity-0'">
                                        <div class="overflow-hidden">
                                            <div class="p-5 bg-[#F8FAFC] border-t border-gray-100 shadow-[inset_0_4px_6px_-4px_rgba(0,0,0,0.05)]">
                                                <h4 class="text-[11px] font-bold text-gray-500 uppercase tracking-wider mb-4 border-b border-gray-200 pb-2">Kelengkapan Berkas & Tautan</h4>
                                                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
                                                    
                                                    <!-- Laporan KP -->
                                                    <div class="flex items-center justify-between p-3 bg-white border border-gray-200 rounded-[8px] shadow-sm">
                                                        <div class="flex items-center gap-3">
                                                            <template x-if="row.file_laporan">
                                                                <div class="w-8 h-8 rounded-full bg-green-50 text-green-600 flex items-center justify-center shrink-0">
                                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                                                </div>
                                                            </template>
                                                            <template x-if="!row.file_laporan">
                                                                <div class="w-8 h-8 rounded-full bg-red-50 text-red-500 flex items-center justify-center shrink-0">
                                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12"></path></svg>
                                                                </div>
                                                            </template>
                                                            <div class="flex flex-col">
                                                                <span class="text-[11px] font-bold text-gray-700">Laporan KP *</span>
                                                                <span class="text-[10px]" :class="row.file_laporan ? 'text-green-600' : 'text-red-500'" x-text="row.file_laporan ? 'Terkumpul' : 'Belum Ada'"></span>
                                                            </div>
                                                        </div>
                                                        <template x-if="row.file_laporan">
                                                            <a :href="row.file_laporan" target="_blank" class="px-3 py-1.5 bg-[#F8F9FA] hover:bg-gray-200 text-gray-700 border border-gray-300 text-[9px] font-bold rounded-[5px] transition-colors uppercase tracking-wider">Lihat</a>
                                                        </template>
                                                    </div>

                                                    <!-- Log Bimbingan -->
                                                    <div class="flex items-center justify-between p-3 bg-white border border-gray-200 rounded-[8px] shadow-sm">
                                                        <div class="flex items-center gap-3">
                                                            <template x-if="row.file_log_bimbingan">
                                                                <div class="w-8 h-8 rounded-full bg-green-50 text-green-600 flex items-center justify-center shrink-0">
                                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                                                </div>
                                                            </template>
                                                            <template x-if="!row.file_log_bimbingan">
                                                                <div class="w-8 h-8 rounded-full bg-red-50 text-red-500 flex items-center justify-center shrink-0">
                                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12"></path></svg>
                                                                </div>
                                                            </template>
                                                            <div class="flex flex-col">
                                                                <span class="text-[11px] font-bold text-gray-700">Log Bimb. *</span>
                                                                <span class="text-[10px]" :class="row.file_log_bimbingan ? 'text-green-600' : 'text-red-500'" x-text="row.file_log_bimbingan ? 'Terkumpul' : 'Belum Ada'"></span>
                                                            </div>
                                                        </div>
                                                        <template x-if="row.file_log_bimbingan">
                                                            <a :href="row.file_log_bimbingan" target="_blank" class="px-3 py-1.5 bg-[#F8F9FA] hover:bg-gray-200 text-gray-700 border border-gray-300 text-[9px] font-bold rounded-[5px] transition-colors uppercase tracking-wider">Lihat</a>
                                                        </template>
                                                    </div>

                                                    <!-- Persetujuan -->
                                                    <div class="flex items-center justify-between p-3 bg-white border border-gray-200 rounded-[8px] shadow-sm">
                                                        <div class="flex items-center gap-3">
                                                            <template x-if="row.file_persetujuan">
                                                                <div class="w-8 h-8 rounded-full bg-green-50 text-green-600 flex items-center justify-center shrink-0">
                                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                                                </div>
                                                            </template>
                                                            <template x-if="!row.file_persetujuan">
                                                                <div class="w-8 h-8 rounded-full bg-red-50 text-red-500 flex items-center justify-center shrink-0">
                                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12"></path></svg>
                                                                </div>
                                                            </template>
                                                            <div class="flex flex-col">
                                                                <span class="text-[11px] font-bold text-gray-700">Persetujuan *</span>
                                                                <span class="text-[10px]" :class="row.file_persetujuan ? 'text-green-600' : 'text-red-500'" x-text="row.file_persetujuan ? 'Terkumpul' : 'Belum Ada'"></span>
                                                            </div>
                                                        </div>
                                                        <template x-if="row.file_persetujuan">
                                                            <a :href="row.file_persetujuan" target="_blank" class="px-3 py-1.5 bg-[#F8F9FA] hover:bg-gray-200 text-gray-700 border border-gray-300 text-[9px] font-bold rounded-[5px] transition-colors uppercase tracking-wider">Lihat</a>
                                                        </template>
                                                    </div>


                                                    <!-- Lainnya -->
                                                    <div class="flex items-center justify-between p-3 bg-white border border-gray-200 rounded-[8px] shadow-sm">
                                                        <div class="flex items-center gap-3">
                                                            <template x-if="row.file_lainnya">
                                                                <div class="w-8 h-8 rounded-full bg-green-50 text-green-600 flex items-center justify-center shrink-0">
                                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                                                </div>
                                                            </template>
                                                            <template x-if="!row.file_lainnya">
                                                                <div class="w-8 h-8 rounded-full bg-gray-100 text-gray-400 flex items-center justify-center shrink-0">
                                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M20 12H4"></path></svg>
                                                                </div>
                                                            </template>
                                                            <div class="flex flex-col">
                                                                <span class="text-[11px] font-bold text-gray-700">Lainnya</span>
                                                                <span class="text-[10px]" :class="row.file_lainnya ? 'text-green-600' : 'text-gray-400'" x-text="row.file_lainnya ? 'Terkumpul' : 'Opsional'"></span>
                                                            </div>
                                                        </div>
                                                        <template x-if="row.file_lainnya">
                                                            <a :href="row.file_lainnya" target="_blank" class="px-3 py-1.5 bg-[#F8F9FA] hover:bg-gray-200 text-gray-700 border border-gray-300 text-[9px] font-bold rounded-[5px] transition-colors uppercase tracking-wider">Lihat</a>
                                                        </template>
                                                    </div>

                                                    <!-- Drive -->
                                                    <div class="flex items-center justify-between p-3 bg-white border border-gray-200 rounded-[8px] shadow-sm">
                                                        <div class="flex items-center gap-3">
                                                            <div class="w-8 h-8 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center shrink-0">
                                                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M21.1 12H13.6l4-6.8h7.5l-4 6.8zm-1.8 1.5L15.6 20H8.1l3.7-6.5h7.5zm-14.7 0l3.8 6.5-3.8-6.5zm-1.8-1.5h7.5l-3.7-6.5H2.8l4 6.5z"/></svg>
                                                            </div>
                                                            <div class="flex flex-col">
                                                                <span class="text-[11px] font-bold text-gray-700">Google Drive</span>
                                                                <span class="text-[10px] text-blue-600 font-medium whitespace-nowrap overflow-hidden text-ellipsis max-w-[80px]" x-text="row.link_drive ? 'Tersedia' : 'Opsional'"></span>
                                                            </div>
                                                        </div>
                                                        <template x-if="row.link_drive">
                                                            <a :href="row.link_drive" target="_blank" class="px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white border border-transparent text-[9px] font-bold rounded-[5px] transition-colors uppercase tracking-wider flex items-center gap-1">Buka <span>&rarr;</span></a>
                                                        </template>
                                                    </div>

                                                    <!-- Github -->
                                                    <div class="flex items-center justify-between p-3 bg-white border border-gray-200 rounded-[8px] shadow-sm">
                                                        <div class="flex items-center gap-3">
                                                            <div class="w-8 h-8 rounded-full bg-gray-100 text-gray-800 flex items-center justify-center shrink-0">
                                                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M12 0c-6.626 0-12 5.373-12 12 0 5.302 3.438 9.8 8.207 11.387.599.111.793-.261.793-.577v-2.234c-3.338.726-4.033-1.416-4.033-1.416-.546-1.387-1.333-1.756-1.333-1.756-1.089-.745.083-.729.083-.729 1.205.084 1.839 1.237 1.839 1.237 1.07 1.834 2.807 1.304 3.492.997.107-.775.418-1.305.762-1.604-2.665-.305-5.467-1.334-5.467-5.931 0-1.311.469-2.381 1.236-3.221-.124-.303-.535-1.524.117-3.176 0 0 1.008-.322 3.301 1.23.957-.266 1.983-.399 3.003-.404 1.02.005 2.047.138 3.006.404 2.291-1.552 3.297-1.23 3.297-1.23.653 1.653.242 2.874.118 3.176.77.84 1.235 1.911 1.235 3.221 0 4.609-2.807 5.624-5.479 5.921.43.372.823 1.102.823 2.222v3.293c0 .319.192.694.801.576 4.765-1.589 8.199-6.086 8.199-11.386 0-6.627-5.373-12-12-12z"/></svg>
                                                            </div>
                                                            <div class="flex flex-col">
                                                                <span class="text-[11px] font-bold text-gray-700">Github Rep. *</span>
                                                                <span class="text-[10px]" :class="row.link_github ? 'text-gray-800 font-medium' : 'text-red-500 font-medium'" class="whitespace-nowrap overflow-hidden text-ellipsis max-w-[80px]" x-text="row.link_github ? 'Tersedia' : 'Belum Ada'"></span>
                                                            </div>
                                                        </div>
                                                        <template x-if="row.link_github">
                                                            <a :href="row.link_github" target="_blank" class="px-3 py-1.5 bg-gray-800 hover:bg-black text-white border border-transparent text-[9px] font-bold rounded-[5px] transition-colors uppercase tracking-wider flex items-center gap-1">Buka <span>&rarr;</span></a>
                                                        </template>
                                                    </div>

                                                    <!-- Deploy -->
                                                    <div class="flex items-center justify-between p-3 bg-white border border-gray-200 rounded-[8px] shadow-sm">
                                                        <div class="flex items-center gap-3">
                                                            <div class="w-8 h-8 rounded-full bg-purple-50 text-purple-600 flex items-center justify-center shrink-0">
                                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"></path></svg>
                                                            </div>
                                                            <div class="flex flex-col">
                                                                <span class="text-[11px] font-bold text-gray-700">Live Deploy *</span>
                                                                <span class="text-[10px]" :class="row.link_deploy ? 'text-purple-600 font-medium' : 'text-red-500 font-medium'" class="whitespace-nowrap overflow-hidden text-ellipsis max-w-[80px]" x-text="row.link_deploy ? 'Tersedia' : 'Belum Ada'"></span>
                                                            </div>
                                                        </div>
                                                        <template x-if="row.link_deploy">
                                                            <a :href="row.link_deploy" target="_blank" class="px-3 py-1.5 bg-purple-600 hover:bg-purple-700 text-white border border-transparent text-[9px] font-bold rounded-[5px] transition-colors uppercase tracking-wider flex items-center gap-1">Buka <span>&rarr;</span></a>
                                                        </template>
                                                    </div>

                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </template>
                    <template x-if="filteredRows.length === 0">
                        <tbody class="bg-white">
                            <tr>
                                <td colspan="5" class="py-12 text-center text-gray-400 italic font-medium bg-gray-50 tracking-widest border-t border-gray-200">Tidak Ada Data Ditemukan</td>
                            </tr>
                        </tbody>
                    </template>
                </table>
                </div>
            </div>

            <div class="pt-4 border-t border-gray-200 flex flex-col sm:flex-row items-center justify-between gap-4 mt-6" x-show="totalPages > 1">
                <span class="text-[12px] font-medium text-black/50" x-text="(filteredRows.length === 0 ? 0 : ((currentPage - 1) * itemsPerPage + 1)) + ' - ' + Math.min(currentPage * itemsPerPage, filteredRows.length) + ' dari ' + filteredRows.length + ' baris'"></span>
                <div class="flex items-center gap-2">
                    <button @click="prevPage" :disabled="currentPage === 1" class="px-3 py-1 border border-gray-300 rounded text-[12px] hover:bg-gray-50 disabled:opacity-30 disabled:cursor-not-allowed transition-colors">Previous</button>
                    <div class="flex items-center gap-1">
                        <template x-for="p in totalPages" :key="p">
                            <button @click="goToPage(p)" class="w-8 h-8 rounded text-[12px] font-bold transition-all" :class="currentPage === p ? 'bg-blue-600 text-white shadow-md' : 'text-black hover:bg-gray-100'" x-text="p"></button>
                        </template>
                    </div>
                    <button @click="nextPage" :disabled="currentPage === totalPages" class="px-3 py-1 border border-gray-300 rounded text-[12px] hover:bg-gray-50 disabled:opacity-30 disabled:cursor-not-allowed transition-colors">Next</button>
                </div>
            </div>
        </div>

        <!-- Riwayat Penolakan Section -->
        <div class="mt-16 bg-white rounded-[15px] border border-gray-200 shadow-sm overflow-hidden p-6 mb-12">
            <div class="flex flex-col sm:flex-row justify-between items-center gap-6 mb-8">
                <div>
                    <h3 class="text-[18px] font-bold text-black tracking-tight">Riwayat Penolakan Berkas</h3>
                    <p class="text-[12px] text-black/60 font-medium">Rekapitulasi semua log penolakan berkas mahasiswa</p>
                </div>

                <div class="flex items-center w-full sm:w-auto">
                    <div class="relative flex-1 sm:w-[300px]">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        </div>
                        <input type="text" x-model="searchRiwayat" @input="riwayatCurrentPage = 1" class="block w-full pl-9 pr-4 py-2 border border-gray-300 rounded-[5px] text-[12px] text-black focus:ring-blue-500" placeholder="Cari Nama/NIM/Catatan...">
                    </div>
                </div>
            </div>

            <div class="border border-gray-200 rounded-[10px] overflow-hidden">
                <table class="w-full border-collapse text-[13px]">
                    <thead>
                        <tr class="bg-[#EBEBEB] text-black">
                            <th class="py-3 px-4 font-bold text-center w-[60px] border-b border-r border-gray-300">No</th>
                            <th class="py-3 px-4 font-bold text-left border-b border-r border-gray-300 w-[120px]">NIM</th>
                            <th class="py-3 px-4 font-bold text-left border-b border-r border-gray-300 w-[200px]">Nama Mahasiswa</th>
                            <th class="py-3 px-4 font-bold text-center border-b border-r border-gray-300 w-[150px]">Tanggal</th>
                            <th class="py-3 px-4 font-bold text-left border-b border-gray-300">Catatan Revisi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        <template x-for="(riwayat, index) in paginatedRiwayatRows" :key="riwayat.id">
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="py-3 px-4 text-center text-black/60 border-r border-gray-200" x-text="(riwayatCurrentPage - 1) * riwayatItemsPerPage + index + 1"></td>
                                <td class="py-3 px-4 text-left font-medium text-black border-r border-gray-200" x-text="riwayat.mahasiswa?.nim || '-'"></td>
                                <td class="py-3 px-4 text-left font-normal text-black sentence-case border-r border-gray-200" x-text="riwayat.mahasiswa?.user?.name || '-'"></td>
                                <td class="py-3 px-4 text-center border-r border-gray-200">
                                    <span class="text-black/70 text-[11px]" x-text="new Date(riwayat.created_at).toLocaleDateString('id-ID', {day: 'numeric', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit'})"></span>
                                </td>
                                <td class="py-3 px-4 text-left font-medium text-red-600 italic text-[11px] leading-snug" x-text="riwayat.feedback || '-'"></td>
                            </tr>
                        </template>
                        <template x-if="filteredRiwayatRows.length === 0">
                            <tr>
                                <td colspan="5" class="py-12 text-center text-gray-400 italic font-medium bg-gray-50 uppercase tracking-widest">Tidak ada riwayat penolakan</td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>

            <!-- AlpineJS Dynamic Paginator for Riwayat Table -->
            <div class="pt-4 border-t border-gray-200 flex flex-col sm:flex-row items-center justify-between gap-4 mt-6" x-show="totalRiwayatPages > 1">
                <span class="text-[12px] font-medium text-black/50" x-text="(filteredRiwayatRows.length === 0 ? 0 : ((riwayatCurrentPage - 1) * riwayatItemsPerPage + 1)) + ' - ' + Math.min(riwayatCurrentPage * riwayatItemsPerPage, filteredRiwayatRows.length) + ' dari ' + filteredRiwayatRows.length + ' baris'"></span>
                <div class="flex items-center gap-2">
                    <button @click="prevRiwayatPage" :disabled="riwayatCurrentPage === 1" class="px-3 py-1 border border-gray-300 rounded text-[12px] hover:bg-gray-50 disabled:opacity-30 disabled:cursor-not-allowed transition-colors">Previous</button>
                    <div class="flex items-center gap-1">
                        <template x-for="p in totalRiwayatPages" :key="p">
                            <button @click="goToRiwayatPage(p)" class="w-8 h-8 rounded text-[12px] font-bold transition-all" :class="riwayatCurrentPage === p ? 'bg-blue-600 text-white shadow-md' : 'text-black hover:bg-gray-100'" x-text="p"></button>
                        </template>
                    </div>
                    <button @click="nextRiwayatPage" :disabled="riwayatCurrentPage === totalRiwayatPages" class="px-3 py-1 border border-gray-300 rounded text-[12px] hover:bg-gray-50 disabled:opacity-30 disabled:cursor-not-allowed transition-colors">Next</button>
                </div>
            </div>
        </div>

        <!-- Status Summary Section -->
        <div class="mt-16 bg-white rounded-[15px] border border-gray-200 shadow-sm overflow-hidden p-6 mb-12">
            <div class="flex flex-col sm:flex-row justify-between items-center gap-6 mb-8">
                <div>
                    <h3 class="text-[18px] font-bold text-black tracking-tight">Status Pengunggahan Berkas</h3>
                    <p class="text-[12px] text-black/60 font-medium">Rekapitulasi berkas persyaratan milik mahasiswa</p>
                </div>

                <div class="flex flex-wrap items-center gap-4 w-full sm:w-auto">
                    <div class="relative flex-1 sm:w-[300px]">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        </div>
                        <input type="text" x-model="searchStatus" @input="statusCurrentPage = 1" class="block w-full pl-9 pr-4 py-2 border border-gray-300 rounded-[5px] text-[12px] text-black focus:ring-blue-500" placeholder="Cari Nama/NIM Summary...">
                    </div>

                    <div x-data="{ openFilter: false }" class="relative w-full sm:w-[180px] z-[60]">
                        <button type="button" @click="openFilter = !openFilter" @click.outside="openFilter = false" class="w-full text-[12px] border border-gray-300 rounded-[5px] py-2 px-3 bg-white text-black font-medium focus:ring-[#4285F4] flex justify-between items-center text-left shadow-sm">
                            <span x-text="filterStatusUpload === 'all' ? 'Semua Status Upload' : (filterStatusUpload === 'sudah' ? 'Sudah Upload' : 'Belum Upload')"></span>
                            <svg :class="openFilter ? 'rotate-0' : 'rotate-90'" class="w-3.5 h-3.5 transition-all duration-200 text-gray-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <div x-show="openFilter" x-transition x-cloak class="absolute w-full mt-1 bg-white border border-gray-300 rounded-[5px] shadow-lg overflow-hidden py-1 z-50">
                            <label class="block px-3 py-2 text-[12px] hover:bg-gray-100 cursor-pointer text-black"><input type="radio" value="all" x-model="filterStatusUpload" class="hidden" @change="openFilter = false; statusCurrentPage = 1">Semua Status Upload</label>
                            <label class="block px-3 py-2 text-[12px] hover:bg-gray-100 cursor-pointer text-black"><input type="radio" value="sudah" x-model="filterStatusUpload" class="hidden" @change="openFilter = false; statusCurrentPage = 1">Sudah Upload</label>
                            <label class="block px-3 py-2 text-[12px] hover:bg-gray-100 cursor-pointer text-black"><input type="radio" value="belum" x-model="filterStatusUpload" class="hidden" @change="openFilter = false; statusCurrentPage = 1">Belum Upload</label>
                        </div>
                    </div>

                    <div class="relative shrink-0" x-data="{ exportOpen: false }" @click.outside="exportOpen = false">
                        <button @click="exportOpen = !exportOpen" class="bg-[#EA4335] hover:bg-red-700 text-white px-4 py-1.5 rounded-[5px] text-[12px] font-bold flex items-center shadow-sm uppercase transition-colors">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                            Cetak PDF
                        </button>
                        <div x-cloak x-show="exportOpen" class="absolute right-0 mt-2 w-52 bg-white rounded-[8px] shadow-xl border border-gray-200 z-[70] overflow-hidden">
                            <div class="bg-gray-50 px-4 py-2.5 text-[10px] font-bold text-gray-400 uppercase tracking-wider border-b border-gray-200">Format Laporan</div>
                            <button @click="exportPDF('all'); exportOpen = false" class="w-full text-left px-4 py-2.5 hover:bg-gray-50 text-[12px] text-black font-medium transition-colors">Semua Data</button>
                            <button @click="exportPDF('sudah'); exportOpen = false" class="w-full text-left px-4 py-2.5 hover:bg-gray-50 text-[12px] text-black font-medium transition-colors">Sudah Mengupload</button>
                            <button @click="exportPDF('belum'); exportOpen = false" class="w-full text-left px-4 py-2.5 hover:bg-gray-50 text-[12px] text-black font-medium transition-colors border-t border-gray-100">Belum Mengupload</button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="border border-gray-200 rounded-[10px] overflow-hidden">
                <table class="w-full border-collapse text-[13px]">
                    <thead>
                        <tr class="bg-[#EBEBEB] text-black">
                            <th class="py-3 px-4 font-bold text-center w-[60px] border-b border-r border-gray-300">No</th>
                            <th class="py-3 px-4 font-bold text-left border-b border-r border-gray-300">NIM</th>
                            <th class="py-3 px-4 font-bold text-left border-b border-r border-gray-300">Nama Mahasiswa</th>
                            <th class="py-3 px-4 font-bold text-center border-b border-gray-300">Status Berkas</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        <template x-for="(stat, index) in paginatedStatusRows" :key="stat.nim">
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="py-3 px-4 text-center text-black/60 border-r border-gray-200" x-text="(statusCurrentPage - 1) * statusItemsPerPage + index + 1"></td>
                                <td class="py-3 px-4 text-left font-medium text-black border-r border-gray-200" x-text="stat.nim"></td>
                                <td class="py-3 px-4 text-left font-normal text-black sentence-case border-r border-gray-200" x-text="stat.name"></td>
                                <td class="py-3 px-4 text-center">
                                    <template x-if="stat.status === 'Sudah Upload'">
                                        <span class="inline-flex items-center gap-1.5 bg-green-100 text-green-700 px-4 py-1 rounded-full font-bold text-[10px] uppercase">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg> Sudah Upload
                                        </span>
                                    </template>
                                    <template x-if="stat.status === 'Belum Upload'">
                                        <span class="text-black/40 font-medium text-[11px] italic tracking-tight">Belum Upload</span>
                                    </template>
                                </td>
                            </tr>
                        </template>
                        <template x-if="filteredStatusRows.length === 0">
                            <tr>
                                <td colspan="4" class="py-12 text-center text-gray-400 italic font-medium bg-gray-50 uppercase tracking-widest">Tidak ada data ditemukan</td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>

            <!-- AlpineJS Dynamic Paginator for Status Table -->
            <div class="pt-4 border-t border-gray-200 flex flex-col sm:flex-row items-center justify-between gap-4 mt-6" x-show="totalStatusPages > 1">
                <span class="text-[12px] font-medium text-black/50" x-text="(filteredStatusRows.length === 0 ? 0 : ((statusCurrentPage - 1) * statusItemsPerPage + 1)) + ' - ' + Math.min(statusCurrentPage * statusItemsPerPage, filteredStatusRows.length) + ' dari ' + filteredStatusRows.length + ' baris'"></span>
                <div class="flex items-center gap-2">
                    <button @click="prevStatusPage" :disabled="statusCurrentPage === 1" class="px-3 py-1 border border-gray-300 rounded text-[12px] hover:bg-gray-50 disabled:opacity-30 disabled:cursor-not-allowed transition-colors">Previous</button>
                    <div class="flex items-center gap-1">
                        <template x-for="p in totalStatusPages" :key="p">
                            <button @click="goToStatusPage(p)" class="w-8 h-8 rounded text-[12px] font-bold transition-all" :class="statusCurrentPage === p ? 'bg-blue-600 text-white shadow-md' : 'text-black hover:bg-gray-100'" x-text="p"></button>
                        </template>
                    </div>
                    <button @click="nextStatusPage" :disabled="statusCurrentPage === totalStatusPages" class="px-3 py-1 border border-gray-300 rounded text-[12px] hover:bg-gray-50 disabled:opacity-30 disabled:cursor-not-allowed transition-colors">Next</button>
                </div>
            </div>
        </div>

        <!-- Modern Rejection Modal -->
        <div x-cloak x-show="showTolakModal" class="fixed inset-0 z-[100] flex items-center justify-center bg-black/50 backdrop-blur-sm p-4">
            <div @click.outside="closeTolakModal()" class="bg-white w-full max-w-md rounded-[10px] shadow-2xl overflow-hidden transform transition-all scale-100">
                <div class="px-6 py-4 border-b border-gray-200 bg-red-50 flex justify-between items-center">
                    <h2 class="text-lg font-bold text-red-700 uppercase tracking-tight flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                        Tolak Berkas
                    </h2>
                    <button @click="closeTolakModal()" class="text-gray-400 hover:text-red-500 transition-colors"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path></svg></button>
                </div>
                <form @submit.prevent="updateRowStatus(selectedId, 'rejected', rejectFeedback)" class="p-6">
                    <p class="text-[13px] text-black font-medium mb-4 leading-relaxed">Pemberitahuan penolakan akan dikirimkan ke mahasiswa agar segera direvisi.</p>
                    <textarea x-model="rejectFeedback" rows="4" class="w-full border border-gray-300 rounded-[5px] p-4 text-[13px] text-black font-normal focus:ring-1 focus:ring-red-500 outline-none resize-none mb-6 shadow-sm" placeholder="Misal: File laporan belum ditandatangan supervisor..."></textarea>
                    <div class="flex justify-end gap-3">
                        <button type="button" @click="closeTolakModal()" :disabled="isUpdating" class="px-5 py-2 bg-gray-100 hover:bg-gray-200 text-black text-[12px] font-bold rounded-[5px] uppercase transition-colors">Batal</button>
                        <button type="submit" :disabled="isUpdating" class="px-5 py-2 bg-[#EA3323] hover:bg-red-700 text-white text-[12px] font-bold rounded-[5px] shadow-md uppercase transition-all flex items-center gap-2">
                            <span x-show="!isUpdating">Kembalikan Berkas</span>
                            <span x-show="isUpdating">Memproses...</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @php
        $logoPath = public_path('images/logo.png');
        $logoData = '';
        if (file_exists($logoPath)) {
            $type = pathinfo($logoPath, PATHINFO_EXTENSION);
            $data = file_get_contents($logoPath);
            $logoData = 'data:image/' . $type . ';base64,' . base64_encode($data);
        } else {
            $logoData = 'https://upload.wikimedia.org/wikipedia/id/8/80/Logo_UKRIDA.png';
        }
        
        $koordinator = auth()->user();
        $sigData = '';
        if ($koordinator && $koordinator->signature_path) {
            if (str_starts_with($koordinator->signature_path, 'data:')) {
                $sigData = $koordinator->signature_path;
            } else {
                $sp = storage_path('app/public/' . $koordinator->signature_path);
                if (file_exists($sp)) {
                    $st = pathinfo($sp, PATHINFO_EXTENSION);
                    $sigData = 'data:image/' . $st . ';base64,' . base64_encode(file_get_contents($sp));
                }
            }
        }
        $koordName = $koordinator ? $koordinator->name : '-';
        $koordNidn = $koordinator && $koordinator->dosen ? $koordinator->dosen->nidn : '-';
    @endphp

    <script>
        function verifikasiTable() {
            return {
                searchQuery: '',
                filterStatus: 'all',
                filterKondisiMain: 'all',
                searchStatus: '',
                filterStatusUpload: 'all',

                rawRows: @json($allVerifikasiRows),
                statusRows: @json($allStatusRows),
                
                statDisahkan: {{ $statDisahkan ?? 0 }},
                statBelum: {{ $statBelum ?? 0 }},
                statDitolak: {{ $statDitolak ?? 0 }},

                showTolakModal: false,
                selectedId: null,
                rejectFeedback: '',
                isUpdating: false,
                currentPage: 1,
                itemsPerPage: 10,
                statusCurrentPage: 1,
                statusItemsPerPage: 10,

                get filteredRows() {
                    let filtered = this.rawRows.filter(r => r.status !== 'rejected');
                    if (this.filterStatus !== 'all') {
                        filtered = filtered.filter(r => r.status === this.filterStatus);
                    }
                    if (this.filterKondisiMain !== 'all') {
                        filtered = filtered.filter(r => this.filterKondisiMain === 'lengkap' ? r.is_complete : !r.is_complete);
                    }
                    if (this.searchQuery.trim() !== '') {
                        const term = this.searchQuery.toLowerCase();
                        filtered = filtered.filter(r => 
                            r.name.toLowerCase().includes(term) || 
                            r.nim.toLowerCase().includes(term)
                        );
                    }
                    return filtered;
                },

                get filteredStatusRows() {
                    let filtered = this.statusRows;
                    if (this.filterStatusUpload !== 'all') {
                        const targetStr = this.filterStatusUpload === 'sudah' ? 'Sudah Upload' : 'Belum Upload';
                        filtered = filtered.filter(r => r.status === targetStr);
                    }
                    if (this.searchStatus.trim() !== '') {
                        const term = this.searchStatus.toLowerCase();
                        filtered = filtered.filter(r => 
                            r.name.toLowerCase().includes(term) || 
                            r.nim.toLowerCase().includes(term)
                        );
                    }
                    return filtered;
                },

                clearMainFilter() {
                    this.searchQuery = '';
                    this.filterStatus = 'all';
                    this.filterKondisiMain = 'all';
                    this.currentPage = 1;
                },

                clearStatusFilter() {
                    this.searchStatus = '';
                    this.filterStatusUpload = 'all';
                    this.statusCurrentPage = 1;
                },

                rawRiwayatRows: @json($ditolaks ?? []),
                searchRiwayat: '',
                riwayatCurrentPage: 1,
                riwayatItemsPerPage: 20,

                get filteredRiwayatRows() {
                    let filtered = this.rawRiwayatRows;
                    if (this.searchRiwayat.trim() !== '') {
                        const term = this.searchRiwayat.toLowerCase();
                        filtered = filtered.filter(r => 
                            (r.mahasiswa?.user?.name || '').toLowerCase().includes(term) || 
                            (r.mahasiswa?.nim || '').toLowerCase().includes(term) ||
                            (r.feedback || '').toLowerCase().includes(term)
                        );
                    }
                    return filtered;
                },
                get totalRiwayatPages() {
                    return Math.ceil(this.filteredRiwayatRows.length / this.riwayatItemsPerPage) || 1;
                },
                get paginatedRiwayatRows() {
                    const start = (this.riwayatCurrentPage - 1) * this.riwayatItemsPerPage;
                    return this.filteredRiwayatRows.slice(start, start + this.riwayatItemsPerPage);
                },
                nextRiwayatPage() { if (this.riwayatCurrentPage < this.totalRiwayatPages) this.riwayatCurrentPage++; },
                prevRiwayatPage() { if (this.riwayatCurrentPage > 1) this.riwayatCurrentPage--; },
                goToRiwayatPage(page) { this.riwayatCurrentPage = page; },

                get totalPages() {
                    return Math.ceil(this.filteredRows.length / this.itemsPerPage) || 1;
                },

                get paginatedRows() {
                    const start = (this.currentPage - 1) * this.itemsPerPage;
                    return this.filteredRows.slice(start, start + this.itemsPerPage);
                },

                get totalStatusPages() {
                    return Math.ceil(this.filteredStatusRows.length / this.statusItemsPerPage) || 1;
                },

                get paginatedStatusRows() {
                    const start = (this.statusCurrentPage - 1) * this.statusItemsPerPage;
                    return this.filteredStatusRows.slice(start, start + this.statusItemsPerPage);
                },

                nextPage() { if (this.currentPage < this.totalPages) this.currentPage++; },
                prevPage() { if (this.currentPage > 1) this.currentPage--; },
                goToPage(page) { this.currentPage = page; },

                nextStatusPage() { if (this.statusCurrentPage < this.totalStatusPages) this.statusCurrentPage++; },
                prevStatusPage() { if (this.statusCurrentPage > 1) this.statusCurrentPage--; },
                goToStatusPage(page) { this.statusCurrentPage = page; },

                openTolakModal(id) {
                    this.selectedId = id;
                    this.showTolakModal = true;
                    this.rejectFeedback = '';
                },
                closeTolakModal() {
                    this.showTolakModal = false;
                    this.selectedId = null;
                },

                async updateRowStatus(id, newStatus, feedback = '') {
                    if (this.isUpdating) return;
                    this.isUpdating = true;

                    try {
                        const response = await fetch(`{{ url('koordinator/verifikasi') }}/${id}/status`, {
                            method: 'PUT',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'X-Requested-With': 'XMLHttpRequest'
                            },
                            body: JSON.stringify({ status_koordinator: newStatus, feedback: feedback })
                        });

                        const result = await response.json();
                        if (result.success) {
                            const idx = this.rawRows.findIndex(r => r.id == id);
                            if (idx !== -1) {
                                this.rawRows[idx].status = newStatus;
                                if (newStatus === 'rejected') this.rawRows[idx].feedback = feedback;
                                if (result.stats) {
                                    this.statDisahkan = result.stats.disahkan;
                                    this.statBelum = result.stats.belum;
                                    this.statDitolak = result.stats.ditolak;
                                }
                            }
                            if (newStatus === 'rejected') this.closeTolakModal();
                        }
                    } catch (error) {
                        alert('Gagal memperbarui status.');
                    } finally {
                        this.isUpdating = false;
                    }
                },

                exportPDF(mode) {
                    let dataToExport = [];
                    if (mode === 'sudah') {
                        dataToExport = this.statusRows.filter(r => r.status === 'Sudah Mengupload Berkas');
                    } else if (mode === 'belum') {
                        dataToExport = this.statusRows.filter(r => r.status === 'Belum Mengupload Berkas');
                    } else {
                        dataToExport = this.statusRows;
                    }
                    
                    if (dataToExport.length === 0) {
                        alert('Tidak ada data untuk dieksport.');
                        return;
                    }
                    
                    if (typeof window.jspdf === 'undefined') {
                        alert('Modul PDF sedang dimuat...');
                        return;
                    }

                    const { jsPDF } = window.jspdf;
                    const doc = new jsPDF();
                    
                    const phpLogo = '{!! $logoData !!}';
                    const phpSig = '{!! $sigData !!}';
                    const phpName = '{!! addslashes($koordName) !!}';
                    const phpNidn = '{!! addslashes($koordNidn) !!}';

                    if (phpLogo) { try { doc.addImage(phpLogo, 'PNG', 14, 12, 18, 18); } catch(e) {} }
                    doc.setFont("helvetica", "bold");
                    doc.setFontSize(14);
                    doc.text("UNIVERSITAS KRISTEN KRIDA WACANA", 105, 18, { align: "center" });
                    doc.setFontSize(12);
                    doc.text("FAKULTAS TEKNOLOGI CERDAS", 105, 24, { align: "center" });
                    doc.text("PROGRAM STUDI INFORMATIKA", 105, 30, { align: "center" });
                    doc.line(14, 34, 196, 34);
                    
                    doc.setFontSize(11);
                    doc.text("DAFTAR STATUS PENGUNGGAHAN BERKAS SIDANG KP", 105, 45, { align: "center" });
                    
                    const printDate = new Date().toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' });
                    doc.setFontSize(9);
                    doc.setFont("helvetica", "normal");
                    doc.text("Dicetak pada: " + printDate, 14, 55);
                    
                    const tableData = dataToExport.map((row, index) => [
                        index + 1,
                        row.nim,
                        row.name,
                        row.status
                    ]);
                    
                    doc.autoTable({
                        head: [['No', 'NIM', 'Nama Mahasiswa', 'Status']],
                        body: tableData,
                        startY: 60,
                        theme: 'grid',
                        headStyles: { fillColor: [44, 62, 80] }
                    });
                    
                    let finalY = doc.lastAutoTable.finalY + 20;
                    if (finalY > 250) { doc.addPage(); finalY = 20; }
                    
                    doc.text("Jakarta, " + printDate, 140, finalY);
                    doc.text("Koordinator Kerja Praktik", 140, finalY + 5);
                    if (phpSig) { try { doc.addImage(phpSig, 'PNG', 140, finalY + 8, 35, 15); } catch(e) {} }
                    doc.setFont("helvetica", "bold");
                    doc.text(phpName, 140, finalY + 30);
                    doc.setFont("helvetica", "normal");
                    doc.text("NIDN: " + phpNidn, 140, finalY + 35);
                    
                    doc.save("Status_Berkas_Sidang.pdf");
                }
            };
        }
    </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.8.2/jspdf.plugin.autotable.min.js"></script>
</x-dashboard-layout>