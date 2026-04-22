<x-dashboard-layout header="Verifikasi Berkas Sidang KP" userName="{{ auth()->user()->id }}" roleName="KOORDINATOR KP">
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
            $isComplete = $item->file_laporan && $item->file_log_bimbingan && $item->status_verifikasi === 'verified';
            return [
                'id' => $item->id,
                'name' => $item->mahasiswa->user->name ?? 'N/A',
                'nim' => $item->mahasiswa->nim ?? 'N/A',
                'status' => $item->status_koordinator,
                'is_complete' => $isComplete,
                'file_laporan' => $item->file_laporan ? asset('storage/'.$item->file_laporan) : null,
                'file_log_bimbingan' => $item->file_log_bimbingan ? asset('storage/'.$item->file_log_bimbingan) : null,
                'file_persetujuan' => $item->status_verifikasi === 'verified' ? route('mahasiswa.persetujuan-sidang.cetak', $item->id) : null,
                'file_supervisor' => $item->file_nilai_supervisor ? asset('storage/'.$item->file_nilai_supervisor) : null,
                'file_lainnya' => $item->file_berkas_lainnya ? asset('storage/'.$item->file_berkas_lainnya) : null,
                'link_drive' => $item->link_drive,
                'link_github' => $item->link_github,
                'link_deploy' => $item->link_deploy,
                'feedback' => $item->koordinator_feedback,
            ];
        };

        $mainRows = $pengajuans->map($mapper);
        $rejectedRows = $ditolaks->map(function($riwayat) use ($mapper) {
            if (!$riwayat->pendaftaranSidang) return null;
            $mapped = $mapper($riwayat->pendaftaranSidang);
            $mapped['status'] = 'rejected';
            $mapped['feedback'] = $riwayat->feedback;
            return $mapped;
        })->filter();

        $allVerifikasiRows = $mainRows->concat($rejectedRows)->sortBy('nim')->values();

        $semuaMahasiswa = \App\Models\Mahasiswa::with('user')->get();
        $allStatusRows = $semuaMahasiswa->map(function($mhs) use ($pengajuans) {
            $isSudah = $pengajuans->contains('mahasiswa_id', $mhs->user_id);
            return [
                'nim'    => $mhs->nim ?? '-',
                'name'   => $mhs->user->name ?? '-',
                'status' => $isSudah ? 'Sudah Mengupload Berkas' : 'Belum Mengupload Berkas'
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

        <!-- Modern Unified Filter Bar -->
        <div class="bg-white p-4 rounded-[10px] border border-gray-200 shadow-sm mb-6">
            <div class="flex flex-col lg:flex-row items-center gap-4">
                <div class="relative flex-1 w-full">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </div>
                    <input type="text" x-model="searchQuery" class="block w-full pl-10 pr-4 py-2 border border-gray-300 rounded-[5px] text-sm text-black focus:ring-blue-500 focus:border-blue-500" placeholder="Cari Nama Mahasiswa atau NIM...">
                </div>

                <div class="flex items-center gap-2 shrink-0">
                    <label class="text-[13px] font-bold text-black whitespace-nowrap uppercase">Status :</label>
                    <select x-model="filterStatus" class="w-[180px] text-[13px] border border-gray-300 rounded-[5px] py-2 px-3 bg-white text-black font-medium focus:outline-none focus:ring-1 focus:ring-blue-500">
                        <option value="all">Semua Status</option>
                        <option value="pending">Belum Diperiksa</option>
                        <option value="verified">Disahkan</option>
                        <option value="rejected">Ditolak</option>
                    </select>
                </div>

                <div class="flex items-center gap-2 shrink-0">
                    <label class="text-[13px] font-bold text-black whitespace-nowrap uppercase">Kondisi :</label>
                    <select x-model="filterKondisiMain" class="w-[160px] text-[13px] border border-gray-300 rounded-[5px] py-2 px-3 bg-white text-black font-medium focus:outline-none focus:ring-1 focus:ring-blue-500">
                        <option value="all">Semua Kondisi</option>
                        <option value="lengkap">Lengkap</option>
                        <option value="tidak_lengkap">Tidak Lengkap</option>
                    </select>
                </div>

                <button @click="clearMainFilter()" class="bg-[#EA3323] hover:bg-red-700 text-white font-bold text-[12px] px-6 py-2.5 rounded-[5px] shadow-sm transition-colors uppercase whitespace-nowrap">
                    Clear Filter
                </button>
            </div>
        </div>

        <!-- Main Unified Table -->
        <div class="bg-white border border-gray-200 rounded-[10px] overflow-hidden shadow-sm mb-12">
            <div class="overflow-x-auto custom-scrollbar">
                <table class="w-full border-collapse text-[11px]" style="min-width: 1400px;">
                    <thead>
                        <tr class="bg-[#EBEBEB] text-black">
                            <th rowspan="2" class="py-3 px-2 font-bold text-center border-b border-gray-300 w-[40px]">No</th>
                            <th rowspan="2" class="py-3 px-4 font-bold text-left border-b border-gray-300 w-[180px]">Mahasiswa</th>
                            <th colspan="8" class="py-2 px-4 font-bold text-center border-b border-gray-300">Kelengkapan Berkas & Link Pelengkap</th>
                            <th rowspan="2" class="py-3 px-4 font-bold text-center border-b border-gray-300 w-[90px]">Kondisi</th>
                            <th rowspan="2" class="py-3 px-4 font-bold text-center border-b border-gray-300 w-[140px]">Aksi & Status</th>
                        </tr>
                        <tr class="bg-[#F5F5F5] text-black">
                            <th class="py-2 px-2 font-bold text-center border-b border-gray-300 min-w-[90px]">Laporan KP *</th>
                            <th class="py-2 px-2 font-bold text-center border-b border-gray-300 min-w-[90px]">Log Bimb. *</th>
                            <th class="py-2 px-2 font-bold text-center border-b border-gray-300 min-w-[95px]">Persetujuan *</th>
                            <th class="py-2 px-2 font-bold text-center border-b border-gray-300 min-w-[90px]">Nilai Superv.</th>
                            <th class="py-2 px-2 font-bold text-center border-b border-gray-300 min-w-[85px]">Lainnya</th>
                            <th class="py-2 px-2 font-bold text-center border-b border-gray-300 min-w-[70px]">Drive</th>
                            <th class="py-2 px-2 font-bold text-center border-b border-gray-300 min-w-[70px]">Github</th>
                            <th class="py-2 px-2 font-bold text-center border-b border-gray-300 min-w-[70px]">Deploy</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        <template x-for="(row, index) in paginatedRows" :key="row.id">
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="py-4 px-2 text-center text-black font-normal" x-text="(currentPage - 1) * itemsPerPage + index + 1"></td>
                                <td class="py-4 px-4 text-left">
                                    <div class="font-normal text-black text-[12px] sentence-case" x-text="row.name"></div>
                                    <div class="text-black/60 text-[10px]" x-text="row.nim"></div>
                                    <template x-if="row.status === 'rejected' && row.feedback">
                                        <div class="mt-1.5 p-2 bg-red-50 border border-red-100 rounded text-[9px] text-red-600 italic leading-snug">
                                            <b>Note:</b> <span x-text="row.feedback"></span>
                                        </div>
                                    </template>
                                </td>
                                
                                <td class="py-4 px-2 text-center">
                                    <template x-if="row.file_laporan">
                                        <div class="flex flex-col items-center gap-1">
                                            <span class="text-green-600 font-bold">✓</span>
                                            <a :href="row.file_laporan" target="_blank" class="bg-gray-800 hover:bg-black text-white text-[9px] px-2.5 py-1 rounded-full shadow-sm transition-colors uppercase">View</a>
                                        </div>
                                    </template>
                                    <template x-if="!row.file_laporan"><span class="text-red-500 font-bold">✕</span></template>
                                </td>
                                <td class="py-4 px-2 text-center">
                                    <template x-if="row.file_log_bimbingan">
                                        <div class="flex flex-col items-center gap-1">
                                            <span class="text-green-600 font-bold">✓</span>
                                            <a :href="row.file_log_bimbingan" target="_blank" class="bg-gray-800 hover:bg-black text-white text-[9px] px-2.5 py-1 rounded-full shadow-sm transition-colors uppercase">View</a>
                                        </div>
                                    </template>
                                    <template x-if="!row.file_log_bimbingan"><span class="text-red-500 font-bold">✕</span></template>
                                </td>
                                <td class="py-4 px-2 text-center">
                                    <template x-if="row.file_persetujuan">
                                        <div class="flex flex-col items-center gap-1">
                                            <span class="text-green-600 font-bold">✓</span>
                                            <a :href="row.file_persetujuan" target="_blank" class="bg-gray-800 hover:bg-black text-white text-[9px] px-2.5 py-1 rounded-full shadow-sm transition-colors uppercase">View</a>
                                        </div>
                                    </template>
                                    <template x-if="!row.file_persetujuan"><span class="text-red-500 font-bold">✕</span></template>
                                </td>
                                <td class="py-4 px-2 text-center">
                                    <template x-if="row.file_supervisor">
                                        <div class="flex flex-col items-center gap-1">
                                            <span class="text-green-600 font-bold">✓</span>
                                            <a :href="row.file_supervisor" target="_blank" class="bg-gray-800 hover:bg-black text-white text-[9px] px-2.5 py-1 rounded-full shadow-sm transition-colors uppercase">View</a>
                                        </div>
                                    </template>
                                    <template x-if="!row.file_supervisor"><span class="text-black/40">-</span></template>
                                </td>
                                <td class="py-4 px-2 text-center">
                                    <template x-if="row.file_lainnya">
                                        <div class="flex flex-col items-center gap-1">
                                            <span class="text-green-600 font-bold">✓</span>
                                            <a :href="row.file_lainnya" target="_blank" class="bg-gray-800 hover:bg-black text-white text-[9px] px-2.5 py-1 rounded-full shadow-sm transition-colors uppercase">View</a>
                                        </div>
                                    </template>
                                    <template x-if="!row.file_lainnya"><span class="text-black/40">-</span></template>
                                </td>

                                <td class="py-4 px-2 text-center">
                                    <template x-if="row.link_drive">
                                        <a :href="row.link_drive" target="_blank" class="bg-blue-600 hover:bg-blue-800 text-white text-[9px] px-3 py-1 rounded-sm shadow-sm flex items-center justify-center gap-1 uppercase transition-colors">Go ➜</a>
                                    </template>
                                    <template x-if="!row.link_drive"><span class="text-black/40">-</span></template>
                                </td>
                                <td class="py-4 px-2 text-center">
                                    <template x-if="row.link_github">
                                        <a :href="row.link_github" target="_blank" class="bg-blue-600 hover:bg-blue-800 text-white text-[9px] px-3 py-1 rounded-sm shadow-sm flex items-center justify-center gap-1 uppercase transition-colors">Go ➜</a>
                                    </template>
                                    <template x-if="!row.link_github"><span class="text-black/40">-</span></template>
                                </td>
                                <td class="py-4 px-2 text-center">
                                    <template x-if="row.link_deploy">
                                        <a :href="row.link_deploy" target="_blank" class="bg-blue-600 hover:bg-blue-800 text-white text-[9px] px-3 py-1 rounded-sm shadow-sm flex items-center justify-center gap-1 uppercase transition-colors">Go ➜</a>
                                    </template>
                                    <template x-if="!row.link_deploy"><span class="text-black/40">-</span></template>
                                </td>

                                <td class="py-4 px-4 text-center">
                                    <template x-if="row.is_complete">
                                        <span class="inline-flex items-center gap-1.5 bg-green-100 text-green-700 px-3 py-1 rounded-full font-bold text-[9px] uppercase shadow-sm">
                                            <div class="w-1.5 h-1.5 rounded-full bg-green-500"></div> Lengkap
                                        </span>
                                    </template>
                                    <template x-if="!row.is_complete">
                                        <span class="inline-flex items-center gap-1.5 bg-red-100 text-red-700 px-3 py-1 rounded-full font-bold text-[9px] uppercase shadow-sm">
                                            <div class="w-1.5 h-1.5 rounded-full bg-red-500"></div> Tidak Lengkap
                                        </span>
                                    </template>
                                </td>

                                <td class="py-4 px-4 text-center">
                                    <template x-if="row.status === 'pending'">
                                        <div class="flex items-center justify-center gap-2">
                                            <button @click="updateRowStatus(row.id, 'verified')" :disabled="isUpdating" class="bg-[#38913B] hover:bg-green-700 text-white text-[10px] font-bold px-3 py-1.5 rounded shadow-sm uppercase transition-colors">Sahkan</button>
                                            <button @click="openTolakModal(row.id)" :disabled="isUpdating" class="bg-[#EA3323] hover:bg-red-700 text-white text-[10px] font-bold px-3 py-1.5 rounded shadow-sm uppercase transition-colors">Tolak</button>
                                        </div>
                                    </template>
                                    <template x-if="row.status === 'verified'">
                                        <span class="bg-[#86EFAC] text-[#166534] font-bold px-4 py-1.5 rounded-full text-[10px] uppercase flex items-center justify-center w-max mx-auto gap-1.5 shadow-sm">
                                            <div class="w-2 h-2 rounded-full bg-green-600"></div> Selesai
                                        </span>
                                    </template>
                                    <template x-if="row.status === 'rejected'">
                                        <span class="bg-red-100 text-red-700 font-bold px-4 py-1.5 rounded-full text-[10px] uppercase shadow-sm">Ditolak</span>
                                    </template>
                                </td>
                            </tr>
                        </template>
                        <template x-if="filteredRows.length === 0">
                            <tr>
                                <td colspan="12" class="py-12 text-center text-gray-400 italic font-medium bg-gray-50 uppercase tracking-widest">Tidak ada data ditemukan</td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>

            <div class="px-6 py-4 bg-white border-t border-gray-200 flex items-center justify-between" x-show="totalPages > 1">
                <span class="text-[12px] font-medium text-black/50" x-text="`Halaman ${currentPage} dari ${totalPages}`"></span>
                <div class="flex items-center gap-2">
                    <button @click="prevPage" :disabled="currentPage === 1" class="px-3 py-1 border border-gray-300 rounded text-[12px] hover:bg-gray-50 disabled:opacity-30 disabled:cursor-not-allowed">Previous</button>
                    <div class="flex items-center gap-1">
                        <template x-for="p in totalPages" :key="p">
                            <button @click="goToPage(p)" class="w-8 h-8 rounded text-[12px] font-bold transition-all" :class="currentPage === p ? 'bg-blue-600 text-white shadow-md' : 'text-black hover:bg-gray-100'" x-text="p"></button>
                        </template>
                    </div>
                    <button @click="nextPage" :disabled="currentPage === totalPages" class="px-3 py-1 border border-gray-300 rounded text-[12px] hover:bg-gray-50 disabled:opacity-30 disabled:cursor-not-allowed">Next</button>
                </div>
            </div>
        </div>

        <!-- Status Summary Section -->
        <div class="mt-16 bg-white rounded-[15px] border border-gray-200 shadow-sm overflow-hidden p-6">
            <div class="flex flex-col sm:flex-row justify-between items-center gap-6 mb-8">
                <div class="border-l-4 border-blue-600 pl-4">
                    <h3 class="text-[18px] font-bold text-black uppercase tracking-tight">Status Pengunggahan Berkas</h3>
                    <p class="text-[12px] text-black/60 font-medium">Rekapitulasi seluruh mahasiswa KP aktif</p>
                </div>

                <div class="flex flex-wrap items-center gap-4 w-full sm:w-auto">
                    <div class="relative flex-1 sm:w-[300px]">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        </div>
                        <input type="text" x-model="searchStatus" class="block w-full pl-9 pr-4 py-2 border border-gray-300 rounded-[5px] text-[12px] text-black focus:ring-blue-500" placeholder="Cari Nama/NIM Summary...">
                    </div>

                    <select x-model="filterStatusUpload" class="w-[180px] text-[12px] border border-gray-300 rounded-[5px] py-2 px-3 bg-white text-black font-medium">
                        <option value="all">Semua Status Upload</option>
                        <option value="sudah">Sudah Mengupload</option>
                        <option value="belum">Belum Mengupload</option>
                    </select>

                    <div class="relative shrink-0" x-data="{ exportOpen: false }" @click.outside="exportOpen = false">
                        <button @click="exportOpen = !exportOpen" class="bg-[#1D6F42] hover:bg-green-700 text-white px-5 py-2 rounded-[5px] text-[12px] font-bold flex items-center gap-2 shadow-sm uppercase transition-colors">
                            <span>📥</span> Cetak PDF
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
                            <th class="py-3 px-4 font-bold text-center w-[60px] border-b border-gray-300">No</th>
                            <th class="py-3 px-4 font-bold text-left border-b border-gray-300">NIM</th>
                            <th class="py-3 px-4 font-bold text-left border-b border-gray-300">Nama Mahasiswa</th>
                            <th class="py-3 px-4 font-bold text-center border-b border-gray-300">Status Berkas</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        <template x-for="(stat, index) in filteredStatusRows" :key="stat.nim">
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="py-3 px-4 text-center text-black/60" x-text="index + 1"></td>
                                <td class="py-3 px-4 text-left font-medium text-black" x-text="stat.nim"></td>
                                <td class="py-3 px-4 text-left font-normal text-black sentence-case" x-text="stat.name"></td>
                                <td class="py-3 px-4 text-center">
                                    <template x-if="stat.status === 'Sudah Mengupload Berkas'">
                                        <span class="inline-flex items-center gap-1.5 bg-green-100 text-green-700 px-4 py-1 rounded-full font-bold text-[10px] uppercase">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg> Sudah Upload
                                        </span>
                                    </template>
                                    <template x-if="stat.status === 'Belum Mengupload Berkas'">
                                        <span class="text-black/40 font-medium text-[11px] italic tracking-tight">Menunggu Upload ...</span>
                                    </template>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
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
            $sp = public_path('storage/' . $koordinator->signature_path);
            if (file_exists($sp)) {
                $st = pathinfo($sp, PATHINFO_EXTENSION);
                $sigData = 'data:image/' . $st . ';base64,' . base64_encode(file_get_contents($sp));
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

                get filteredRows() {
                    let filtered = this.rawRows;
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
                        const targetStr = this.filterStatusUpload === 'sudah' ? 'Sudah Mengupload Berkas' : 'Belum Mengupload Berkas';
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
                },

                get totalPages() {
                    return Math.ceil(this.filteredRows.length / this.itemsPerPage) || 1;
                },

                get paginatedRows() {
                    const start = (this.currentPage - 1) * this.itemsPerPage;
                    return this.filteredRows.slice(start, start + this.itemsPerPage);
                },

                nextPage() { if (this.currentPage < this.totalPages) this.currentPage++; },
                prevPage() { if (this.currentPage > 1) this.currentPage--; },
                goToPage(page) { this.currentPage = page; },

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