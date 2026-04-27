<x-dashboard-layout header="Input Nilai Sidang" userName="{{ auth()->user()->name }}" roleName="DOSEN">
    <x-slot:sidebar>
        @include('dosen.components.sidebar', ['active' => 'input-nilai'])
    </x-slot>

    <div x-data="inputNilaiPage()" class="p-6">
        
        <!-- Global Info -->
        <div class="bg-[#EAEFFF] border border-[#BACDFB] rounded-[10px] p-4 flex items-center gap-4 shadow-sm mb-10">
            <div class="bg-[#4285F4] w-6 h-6 rounded-full flex items-center justify-center text-white shrink-0 shadow-sm font-bold text-sm">i</div>
            <p class="text-[14px] text-black font-medium leading-relaxed">
                Lakukan penginputan nilai berdasarkan peran anda terhadap KP Mahasiswa.
            </p>
        </div>

        <!-- SECTION 1: TABEL INPUT NILAI PENGUJI -->
        <div class="mb-20">
            <!-- Unified Table Container -->
            <div class="bg-white rounded-[10px] border border-[#CAC0C0] shadow-sm overflow-hidden">
                <!-- Header, Stats, Search & Filters Section -->
                <div class="p-6 border-b border-[#CAC0C0]">
                    <!-- Title & Stats Row -->
                    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6 mb-8">
                        <div>
                            <h3 class="text-[18px] font-bold text-black tracking-tight uppercase">TABEL INPUT NILAI PENGUJI</h3>
                            <p class="text-[12px] text-black/60 font-medium mt-1">Manajemen penilaian mahasiswa bimbingan dan penguji sidang KP.</p>
                        </div>
                        <div class="flex gap-4">
                            <div class="bg-[#4285F4] rounded-[10px] p-3 flex flex-col justify-center items-center w-[90px] shadow-sm text-white">
                                <div class="flex items-center gap-2">
                                    <div class="bg-white/20 p-1 rounded-full"><svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg></div>
                                    <span class="text-xl font-bold" x-text="pengujiTotal">0</span>
                                </div>
                                <span class="text-[11px] font-medium mt-1">Total</span>
                            </div>
                            <div class="bg-[#FBC610] rounded-[10px] p-3 flex flex-col justify-center items-center w-[90px] shadow-sm text-black">
                                <div class="flex items-center gap-2">
                                    <div class="border border-black p-0.5 rounded-sm"><svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"></path></svg></div>
                                    <span class="text-xl font-bold" x-text="pengujiMenunggu">0</span>
                                </div>
                                <span class="text-[11px] font-medium mt-1 text-center leading-tight">Menunggu</span>
                            </div>
                            <div class="bg-[#38913B] rounded-[10px] p-3 flex flex-col justify-center items-center w-[90px] shadow-sm text-white">
                                <div class="flex items-center gap-2">
                                    <div class="bg-white/20 p-1 rounded-full"><svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg></div>
                                    <span class="text-xl font-bold" x-text="pengujiDinilai">0</span>
                                </div>
                                <span class="text-[11px] font-medium mt-1">Dinilai</span>
                            </div>
                        </div>
                    </div>

                    <!-- Search (Top) -->
                    <div class="relative w-full mb-4">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        </div>
                        <input type="text" x-model="searchPenguji" class="block w-full pl-10 pr-4 py-2 border border-gray-300 rounded-[5px] text-sm text-black focus:ring-blue-500 focus:border-blue-500 shadow-sm" placeholder="Cari nama, NIM, atau judul KP...">
                    </div>

                    <!-- Filters (Bottom) -->
                    <div class="flex flex-wrap items-center gap-4">
                        <div class="flex items-center gap-2">
                            <label class="text-[13px] font-bold text-black whitespace-nowrap">Pelaksanaan :</label>
                            <select x-model="filterPelaksanaanPenguji" class="w-[140px] text-[13px] border border-gray-300 rounded-[5px] py-2 px-3 bg-white text-black font-medium focus:outline-none focus:ring-1 focus:ring-blue-500">
                                <option value="all">Semua Kondisi</option>
                                <option value="Selesai">Selesai</option>
                                <option value="Berjalan">Berjalan</option>
                                <option value="Menunggu">Menunggu</option>
                                <option value="Dibatalkan">Dibatalkan</option>
                            </select>
                        </div>

                        <div class="flex items-center gap-2">
                            <label class="text-[13px] font-bold text-black whitespace-nowrap">Penilaian :</label>
                            <select x-model="filterPenilaianPenguji" class="w-[130px] text-[13px] border border-gray-300 rounded-[5px] py-2 px-3 bg-white text-black font-medium focus:outline-none focus:ring-1 focus:ring-blue-500">
                                <option value="all">Semua</option>
                                <option value="sudah">Sudah Input</option>
                                <option value="belum">Belum Input</option>
                            </select>
                        </div>
                        
                        <div class="flex items-center gap-2">
                            <label class="text-[13px] font-bold text-black whitespace-nowrap">Peran :</label>
                            <select x-model="filterPeranPenguji" class="w-[120px] text-[13px] border border-gray-300 rounded-[5px] py-2 px-3 bg-white text-black font-medium focus:outline-none focus:ring-1 focus:ring-blue-500">
                                <option value="all">Semua Peran</option>
                                <option value="PENGUJI 1">Penguji 1</option>
                                <option value="PENGUJI 2">Penguji 2</option>
                            </select>
                        </div>

                        <div class="flex items-center gap-2">
                            <label class="text-[13px] font-bold text-black whitespace-nowrap">Jadwal :</label>
                            <select x-model="sortPenguji" class="w-[120px] text-[13px] border border-gray-300 rounded-[5px] py-2 px-3 bg-white text-black font-medium focus:outline-none focus:ring-1 focus:ring-blue-500">
                                <option value="date_near">Terdekat</option>
                                <option value="date_far">Terjauh</option>
                            </select>
                        </div>

                        <button @click="clearPenguji()" class="bg-[#EA3323] hover:bg-red-700 text-white font-bold text-[12px] px-4 py-2 rounded-[5px] shadow-sm transition-colors whitespace-nowrap uppercase">
                            Clear Filter
                        </button>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-center border-collapse text-[12px] min-w-[1000px]">
                        <thead class="bg-[#EBEBEB] font-bold text-black border-b border-[#CAC0C0] h-[45px]">
                            <tr>
                                <th class="border-r border-[#CAC0C0] px-3 py-2 w-[50px]">No</th>
                                <th class="border-r border-[#CAC0C0] px-4 py-2 text-left w-[180px]">Jadwal</th>
                                <th class="border-r border-[#CAC0C0] px-4 py-2 text-left w-[150px]">Peran Sidang</th>
                                <th class="border-r border-[#CAC0C0] px-4 py-2 text-left w-[180px]">Mahasiswa</th>
                                <th class="border-r border-[#CAC0C0] px-4 py-2 text-left">Judul KP</th>
                                <th class="border-r border-[#CAC0C0] px-4 py-2 w-[120px]">Status Kelulusan</th>
                                <th class="border-r border-[#CAC0C0] px-4 py-2 w-[140px]">Pelaksanaan</th>
                                <th class="px-4 py-2 w-[150px]">Penilaian</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white">
                            <template x-for="(sidang, index) in filteredPenguji" :key="'p-' + sidang.id">
                                <tr class="border-b border-[#CAC0C0] hover:bg-gray-50 transition-colors">
                                    <td class="border-r border-[#CAC0C0] px-3 py-4 text-gray-700" x-text="index + 1"></td>
                                    <td class="border-r border-[#CAC0C0] px-4 py-4 text-left">
                                        <div class="font-bold text-black uppercase" x-text="formatDate(sidang.tanggal_sidang)"></div>
                                        <div class="text-gray-600 mt-1" x-text="formatTime(sidang.waktu_mulai_sidang) + ' - ' + formatTime(sidang.waktu_selesai_sidang) + ' WIB'"></div>
                                        <div class="text-gray-400 italic mt-1" x-text="sidang.ruang_sidang || '-'"></div>
                                    </td>
                                    <td class="border-r border-[#CAC0C0] px-4 py-4 text-left">
                                        <div class="flex flex-col gap-1">
                                            <template x-for="role in getSpecificRoles(sidang, ['PENGUJI 1', 'PENGUJI 2'])">
                                                <span class="text-[10px] font-bold bg-[#F0F0F0] text-gray-600 px-2 py-0.5 rounded-[3px] border border-gray-200" x-text="role"></span>
                                            </template>
                                        </div>
                                    </td>
                                    <td class="border-r border-[#CAC0C0] px-4 py-4 text-left">
                                        <div class="font-bold text-black uppercase" x-text="sidang.mahasiswa.user.name"></div>
                                        <div class="text-gray-500 font-mono text-[11px]" x-text="sidang.mahasiswa.nim"></div>
                                    </td>
                                    <td class="border-r border-[#CAC0C0] px-4 py-4 text-left">
                                        <p class="sentence-case leading-snug line-clamp-2 text-black font-normal" x-text="sidang.pendaftaran_kp.judul_kp" :title="sidang.pendaftaran_kp.judul_kp"></p>
                                    </td>
                                    <td class="border-r border-[#CAC0C0] px-4 py-4 text-center">
                                        <span class="font-bold text-gray-700" x-text="sidang.status_kelulusan || '-'"></span>
                                    </td>
                                    <td class="border-r border-[#CAC0C0] px-4 py-4">
                                        <div class="flex flex-col items-center gap-2">
                                            <template x-if="sidang.is_penguji_1">
                                                <div class="relative w-full min-w-[115px]">
                                                    <select @change="confirmUpdateStatus(sidang.id, $event.target)" 
                                                        :class="getStatusClass(sidang)"
                                                        class="w-full appearance-none text-[10px] font-bold pl-3 pr-6 py-1.5 rounded-[20px] shadow-sm text-center cursor-pointer focus:outline-none focus:ring-2 focus:ring-[#4CC098] transition-colors border">
                                                        <option value="" disabled :selected="!['Selesai', 'Dibatalkan'].includes(sidang.pelaksanaan)" x-text="getExecutionStatus(sidang)" class="bg-white text-black font-medium"></option>
                                                        <option value="Selesai" :selected="sidang.pelaksanaan === 'Selesai'" class="bg-white text-black font-medium">Selesai</option>
                                                        <option value="Dibatalkan" :selected="sidang.pelaksanaan === 'Dibatalkan'" class="bg-white text-black font-medium">Dibatalkan</option>
                                                    </select>
                                                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-2" :class="getStatusTextClass(sidang)">
                                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                                    </div>
                                                </div>
                                            </template>
                                            
                                            <template x-if="!sidang.is_penguji_1">
                                                <div class="flex flex-col items-center gap-1">
                                                    <div class="text-[10px] font-bold px-3 py-1.5 rounded-[20px] shadow-sm flex items-center justify-center gap-1.5 min-w-[115px] border"
                                                        :class="getStatusClass(sidang)">
                                                        <div class="w-1.5 h-1.5 rounded-full" :class="getStatusDotClass(sidang)"></div>
                                                        <span x-text="getExecutionStatus(sidang)"></span>
                                                    </div>
                                                    <div class="text-[9px] text-gray-400 italic font-medium text-center">Otoritas Penguji 1</div>
                                                </div>
                                            </template>
                                        </div>
                                    </td>
                                    <td class="px-4 py-4">
                                        <div class="flex flex-col gap-2">
                                            <template x-for="role in getSpecificRoles(sidang, ['PENGUJI 1', 'PENGUJI 2'])">
                                                <a :href="'{{ url('dosen/input-nilai') }}/' + sidang.id + '/' + role.toLowerCase().replace(' ', '')"
                                                    class="w-full text-center bg-[#4CC098] hover:bg-[#3da681] text-white py-1.5 rounded-[4px] text-[10px] font-bold transition-all shadow-sm flex items-center justify-center gap-1">
                                                    INPUT <span x-text="role"></span>
                                                </a>
                                            </template>
                                        </div>
                                    </td>
                                </tr>
                            </template>
                            <template x-if="filteredPenguji.length === 0">
                                <tr>
                                    <td colspan="8" class="py-12 text-center text-gray-500 italic text-[13px]">
                                        Tidak ada data penguji yang sesuai pencarian/filter.
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- SECTION 2: TABEL INPUT NILAI PEMBIMBING -->
        <div class="mb-20">
            <!-- Unified Table Container -->
            <div class="bg-white rounded-[10px] border border-[#CAC0C0] shadow-sm overflow-hidden">
                <!-- Header, Stats, Search & Filters Section -->
                <div class="p-6 border-b border-[#CAC0C0]">
                    <!-- Title & Stats Row -->
                    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6 mb-8">
                        <div>
                            <h3 class="text-[18px] font-bold text-black tracking-tight uppercase">TABEL INPUT NILAI PEMBIMBING</h3>
                            <p class="text-[12px] text-black/60 font-medium mt-1">Manajemen penilaian mahasiswa khusus untuk peran Anda sebagai Dosen Pembimbing.</p>
                        </div>
                        <div class="flex gap-4">
                            <div class="bg-[#4285F4] rounded-[10px] p-3 flex flex-col justify-center items-center w-[90px] shadow-sm text-white">
                                <div class="flex items-center gap-2">
                                    <div class="bg-white/20 p-1 rounded-full"><svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg></div>
                                    <span class="text-xl font-bold" x-text="pembimbingTotal">0</span>
                                </div>
                                <span class="text-[11px] font-medium mt-1">Total</span>
                            </div>
                            <div class="bg-[#FBC610] rounded-[10px] p-3 flex flex-col justify-center items-center w-[90px] shadow-sm text-black">
                                <div class="flex items-center gap-2">
                                    <div class="border border-black p-0.5 rounded-sm"><svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"></path></svg></div>
                                    <span class="text-xl font-bold" x-text="pembimbingMenunggu">0</span>
                                </div>
                                <span class="text-[11px] font-medium mt-1 text-center leading-tight">Menunggu</span>
                            </div>
                            <div class="bg-[#38913B] rounded-[10px] p-3 flex flex-col justify-center items-center w-[90px] shadow-sm text-white">
                                <div class="flex items-center gap-2">
                                    <div class="bg-white/20 p-1 rounded-full"><svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg></div>
                                    <span class="text-xl font-bold" x-text="pembimbingDinilai">0</span>
                                </div>
                                <span class="text-[11px] font-medium mt-1">Dinilai</span>
                            </div>
                        </div>
                    </div>

                    <!-- Search (Top) -->
                    <div class="relative w-full mb-4">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        </div>
                        <input type="text" x-model="searchPembimbing" class="block w-full pl-10 pr-4 py-2 border border-gray-300 rounded-[5px] text-sm text-black focus:ring-blue-500 focus:border-blue-500 shadow-sm" placeholder="Cari nama, NIM, atau judul KP...">
                    </div>

                    <!-- Filters (Bottom) -->
                    <div class="flex flex-wrap items-center gap-4">
                        <div class="flex items-center gap-2">
                            <label class="text-[13px] font-bold text-black whitespace-nowrap">Penilaian :</label>
                            <select x-model="filterPenilaianPembimbing" class="w-[140px] text-[13px] border border-gray-300 rounded-[5px] py-2 px-3 bg-white text-black font-medium focus:outline-none focus:ring-1 focus:ring-blue-500">
                                <option value="all">Semua Kondisi</option>
                                <option value="sudah">Sudah Input</option>
                                <option value="belum">Belum Input</option>
                            </select>
                        </div>

                        <div class="flex items-center gap-2">
                            <label class="text-[13px] font-bold text-black whitespace-nowrap">Jadwal :</label>
                            <select x-model="sortPembimbing" class="w-[120px] text-[13px] border border-gray-300 rounded-[5px] py-2 px-3 bg-white text-black font-medium focus:outline-none focus:ring-1 focus:ring-blue-500">
                                <option value="date_near">Terdekat</option>
                                <option value="date_far">Terjauh</option>
                            </select>
                        </div>

                        <button @click="clearPembimbing()" class="bg-[#EA3323] hover:bg-red-700 text-white font-bold text-[12px] px-4 py-2 rounded-[5px] shadow-sm transition-colors whitespace-nowrap uppercase">
                            Clear Filter
                        </button>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-center border-collapse text-[12px] min-w-[800px]">
                        <thead class="bg-[#EBEBEB] font-bold text-black border-b border-[#CAC0C0] h-[45px]">
                            <tr>
                                <th class="border-r border-[#CAC0C0] px-3 py-2 w-[50px]">No</th>
                                <th class="border-r border-[#CAC0C0] px-4 py-2 text-left w-[250px]">Mahasiswa</th>
                                <th class="border-r border-[#CAC0C0] px-4 py-2 text-left">Judul KP</th>
                                <th class="border-r border-[#CAC0C0] px-4 py-2 w-[150px]">Status</th>
                                <th class="px-4 py-2 w-[200px]">Penilaian</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white">
                            <template x-for="(sidang, index) in filteredPembimbing" :key="'pb-' + sidang.id">
                                <tr class="border-b border-[#CAC0C0] hover:bg-gray-50 transition-colors">
                                    <td class="border-r border-[#CAC0C0] px-3 py-4 text-gray-700" x-text="index + 1"></td>
                                    <td class="border-r border-[#CAC0C0] px-4 py-4 text-left">
                                        <div class="font-bold text-black uppercase" x-text="sidang.mahasiswa.user.name"></div>
                                        <div class="text-gray-500 font-mono text-[11px]" x-text="sidang.mahasiswa.nim"></div>
                                    </td>
                                    <td class="border-r border-[#CAC0C0] px-4 py-4 text-left">
                                        <p class="sentence-case leading-snug line-clamp-2 text-black font-normal" x-text="sidang.pendaftaran_kp.judul_kp" :title="sidang.pendaftaran_kp.judul_kp"></p>
                                    </td>
                                    <td class="border-r border-[#CAC0C0] px-4 py-4 text-center">
                                        <template x-if="sidang.nilai_pembimbing !== null">
                                            <span class="inline-flex items-center gap-1.5 bg-green-100 text-green-700 px-3 py-1 rounded-full font-bold text-[10px] uppercase shadow-sm">
                                                Dinilai
                                            </span>
                                        </template>
                                        <template x-if="sidang.nilai_pembimbing === null">
                                            <span class="inline-flex items-center gap-1.5 bg-yellow-100 text-yellow-700 px-3 py-1 rounded-full font-bold text-[10px] uppercase shadow-sm">
                                                Menunggu
                                            </span>
                                        </template>
                                    </td>
                                    <td class="px-4 py-4">
                                        <a :href="'{{ url('dosen/input-nilai') }}/' + sidang.id + '/pembimbing'"
                                            class="w-full text-center bg-[#4CC098] hover:bg-[#3da681] text-white py-2 rounded-[4px] text-[11px] font-bold transition-all shadow-sm flex items-center justify-center gap-1 uppercase">
                                            Input Nilai Pembimbing
                                        </a>
                                    </td>
                                </tr>
                            </template>
                            <template x-if="filteredPembimbing.length === 0">
                                <tr>
                                    <td colspan="5" class="py-12 text-center text-gray-500 italic text-[13px]">
                                        Tidak ada data pembimbing yang sesuai pencarian/filter.
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- SECTION 3: TABEL INPUT NILAI SUPERVISOR -->
        <div class="mb-20">
            <!-- Unified Table Container -->
            <div class="bg-white rounded-[10px] border border-[#CAC0C0] shadow-sm overflow-hidden">
                <!-- Header, Stats, Search & Filters Section -->
                <div class="p-6 border-b border-[#CAC0C0]">
                    <!-- Title & Stats Row -->
                    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6 mb-8">
                        <div>
                            <h3 class="text-[18px] font-bold text-black tracking-tight uppercase">TABEL INPUT NILAI SUPERVISOR</h3>
                            <p class="text-[12px] text-black/60 font-medium mt-1">Manajemen penilaian mahasiswa untuk peran Anda sebagai Supervisor (Pembimbing Lapangan).</p>
                        </div>
                        <div class="flex gap-4">
                            <div class="bg-[#4285F4] rounded-[10px] p-3 flex flex-col justify-center items-center w-[90px] shadow-sm text-white">
                                <div class="flex items-center gap-2">
                                    <div class="bg-white/20 p-1 rounded-full"><svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg></div>
                                    <span class="text-xl font-bold" x-text="supervisorTotal">0</span>
                                </div>
                                <span class="text-[11px] font-medium mt-1">Total</span>
                            </div>
                            <div class="bg-[#FBC610] rounded-[10px] p-3 flex flex-col justify-center items-center w-[90px] shadow-sm text-black">
                                <div class="flex items-center gap-2">
                                    <div class="border border-black p-0.5 rounded-sm"><svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"></path></svg></div>
                                    <span class="text-xl font-bold" x-text="supervisorMenunggu">0</span>
                                </div>
                                <span class="text-[11px] font-medium mt-1 text-center leading-tight">Menunggu</span>
                            </div>
                            <div class="bg-[#38913B] rounded-[10px] p-3 flex flex-col justify-center items-center w-[90px] shadow-sm text-white">
                                <div class="flex items-center gap-2">
                                    <div class="bg-white/20 p-1 rounded-full"><svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg></div>
                                    <span class="text-xl font-bold" x-text="supervisorDinilai">0</span>
                                </div>
                                <span class="text-[11px] font-medium mt-1">Dinilai</span>
                            </div>
                        </div>
                    </div>

                    <!-- Search (Top) -->
                    <div class="relative w-full mb-4">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        </div>
                        <input type="text" x-model="searchSupervisor" class="block w-full pl-10 pr-4 py-2 border border-gray-300 rounded-[5px] text-sm text-black focus:ring-blue-500 focus:border-blue-500 shadow-sm" placeholder="Cari nama, NIM, atau judul KP...">
                    </div>

                    <!-- Filters (Bottom) -->
                    <div class="flex flex-wrap items-center gap-4">
                        <div class="flex items-center gap-2">
                            <label class="text-[13px] font-bold text-black whitespace-nowrap">Penilaian :</label>
                            <select x-model="filterPenilaianSupervisor" class="w-[140px] text-[13px] border border-gray-300 rounded-[5px] py-2 px-3 bg-white text-black font-medium focus:outline-none focus:ring-1 focus:ring-blue-500">
                                <option value="all">Semua Kondisi</option>
                                <option value="sudah">Sudah Input</option>
                                <option value="belum">Belum Input</option>
                            </select>
                        </div>

                        <div class="flex items-center gap-2">
                            <label class="text-[13px] font-bold text-black whitespace-nowrap">Jadwal :</label>
                            <select x-model="sortSupervisor" class="w-[120px] text-[13px] border border-gray-300 rounded-[5px] py-2 px-3 bg-white text-black font-medium focus:outline-none focus:ring-1 focus:ring-blue-500">
                                <option value="date_near">Terdekat</option>
                                <option value="date_far">Terjauh</option>
                            </select>
                        </div>

                        <button @click="clearSupervisor()" class="bg-[#EA3323] hover:bg-red-700 text-white font-bold text-[12px] px-4 py-2 rounded-[5px] shadow-sm transition-colors whitespace-nowrap uppercase">
                            Clear Filter
                        </button>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-center border-collapse text-[12px] min-w-[800px]">
                        <thead class="bg-[#EBEBEB] font-bold text-black border-b border-[#CAC0C0] h-[45px]">
                            <tr>
                                <th class="border-r border-[#CAC0C0] px-3 py-2 w-[50px]">No</th>
                                <th class="border-r border-[#CAC0C0] px-4 py-2 text-left w-[250px]">Mahasiswa</th>
                                <th class="border-r border-[#CAC0C0] px-4 py-2 text-left">Judul KP</th>
                                <th class="border-r border-[#CAC0C0] px-4 py-2 w-[150px]">Status</th>
                                <th class="px-4 py-2 w-[200px]">Penilaian</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white">
                            <template x-for="(sidang, index) in filteredSupervisor" :key="'sv-' + sidang.id">
                                <tr class="border-b border-[#CAC0C0] hover:bg-gray-50 transition-colors">
                                    <td class="border-r border-[#CAC0C0] px-3 py-4 text-gray-700" x-text="index + 1"></td>
                                    <td class="border-r border-[#CAC0C0] px-4 py-4 text-left">
                                        <div class="font-bold text-black uppercase" x-text="sidang.mahasiswa.user.name"></div>
                                        <div class="text-gray-500 font-mono text-[11px]" x-text="sidang.mahasiswa.nim"></div>
                                    </td>
                                    <td class="border-r border-[#CAC0C0] px-4 py-4 text-left">
                                        <p class="sentence-case leading-snug line-clamp-2 text-black font-normal" x-text="sidang.pendaftaran_kp.judul_kp" :title="sidang.pendaftaran_kp.judul_kp"></p>
                                    </td>
                                    <td class="border-r border-[#CAC0C0] px-4 py-4 text-center">
                                        <template x-if="sidang.nilai_supervisor !== null">
                                            <span class="inline-flex items-center gap-1.5 bg-green-100 text-green-700 px-3 py-1 rounded-full font-bold text-[10px] uppercase shadow-sm">
                                                Dinilai
                                            </span>
                                        </template>
                                        <template x-if="sidang.nilai_supervisor === null">
                                            <span class="inline-flex items-center gap-1.5 bg-yellow-100 text-yellow-700 px-3 py-1 rounded-full font-bold text-[10px] uppercase shadow-sm">
                                                Menunggu
                                            </span>
                                        </template>
                                    </td>
                                    <td class="px-4 py-4">
                                        <a :href="'{{ url('dosen/input-nilai') }}/' + sidang.id + '/supervisior'"
                                            class="w-full text-center bg-[#4CC098] hover:bg-[#3da681] text-white py-2 rounded-[4px] text-[11px] font-bold transition-all shadow-sm flex items-center justify-center gap-1 uppercase">
                                            Input Nilai Supervisior
                                        </a>
                                    </td>
                                </tr>
                            </template>
                            <template x-if="filteredSupervisor.length === 0">
                                <tr>
                                    <td colspan="5" class="py-12 text-center text-gray-500 italic text-[13px]">
                                        Tidak ada data supervisior yang sesuai pencarian/filter.
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        
        <!-- Custom Global Confirm Modal -->
        <div x-show="showConfirmModal" style="display: none;" class="fixed inset-0 z-[110] flex items-center justify-center bg-black/40 backdrop-blur-sm p-4">
            <div @click.away="cancelUpdate()" class="bg-white rounded-[10px] w-full max-w-[450px] p-8 shadow-2xl flex flex-col items-center justify-center text-center transform transition-all">
                
                <!-- Icon -->
                <div class="mb-5">
                    <svg class="w-16 h-16 text-[#4CAF50]" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                    </svg>
                </div>

                <!-- Message -->
                <h3 class="text-black font-semibold text-[16px] mb-8">Apakah Anda yakin ingin memperbarui status pelaksanaan menjadi <span class="font-bold text-blue-600" x-text="confirmData ? confirmData.newStatus : ''"></span>?</h3>

                <!-- Buttons -->
                <div class="flex gap-4 w-full justify-center">
                    <button @click="cancelUpdate()" type="button" class="w-[100px] h-[34px] bg-[#E32727] hover:bg-red-700 text-white rounded-[5px] text-[14px] font-medium transition-colors shadow-sm">
                        Batal
                    </button>
                    <button @click="executeUpdate()" type="button" class="w-[100px] h-[34px] bg-[#456DA7] hover:bg-blue-700 text-white rounded-[5px] text-[14px] font-medium transition-colors shadow-sm">
                        Iya
                    </button>
                </div>
            </div>
        </div>

        <!-- Success Modal -->
        <div x-show="showSuccessModal" style="display: none;" class="fixed inset-0 z-[110] flex items-center justify-center bg-black/40 backdrop-blur-sm p-4">
            <div class="bg-white rounded-[10px] w-full max-w-[400px] p-6 shadow-2xl flex flex-col items-center text-center">
                <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center mb-4">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                </div>
                <h3 class="text-[16px] font-bold text-gray-900 mb-2">Berhasil!</h3>
                <p class="text-[14px] text-gray-500" x-text="successMessage"></p>
                <button @click="showSuccessModal = false" class="mt-6 w-full h-[36px] bg-green-500 hover:bg-green-600 text-white rounded-[5px] text-[13px] font-bold transition-colors">Tutup</button>
            </div>
        </div>

        <!-- Error Modal -->
        <div x-show="showErrorModal" style="display: none;" class="fixed inset-0 z-[110] flex items-center justify-center bg-black/40 backdrop-blur-sm p-4">
            <div class="bg-white rounded-[10px] w-full max-w-[400px] p-6 shadow-2xl flex flex-col items-center text-center">
                <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center mb-4">
                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </div>
                <h3 class="text-[16px] font-bold text-gray-900 mb-2">Gagal</h3>
                <p class="text-[14px] text-gray-500" x-text="errorMessage"></p>
                <button @click="showErrorModal = false" class="mt-6 w-full h-[36px] bg-red-500 hover:bg-red-600 text-white rounded-[5px] text-[13px] font-bold transition-colors">Tutup</button>
            </div>
        </div>
    </div>

    <script>
        function inputNilaiPage() {
            return {
                sidangs: @json($sidangs),
                now: new Date(),
                
                searchPenguji: '',
                filterPelaksanaanPenguji: 'all',
                filterPenilaianPenguji: 'all',
                filterPeranPenguji: 'all',
                sortPenguji: 'date_near',

                searchPembimbing: '',
                filterPenilaianPembimbing: 'all',
                sortPembimbing: 'date_near',

                searchSupervisor: '',
                filterPenilaianSupervisor: 'all',
                sortSupervisor: 'date_near',

                clearPenguji() {
                    this.searchPenguji = '';
                    this.filterPelaksanaanPenguji = 'all';
                    this.filterPenilaianPenguji = 'all';
                    this.filterPeranPenguji = 'all';
                    this.sortPenguji = 'date_near';
                },
                clearPembimbing() {
                    this.searchPembimbing = '';
                    this.filterPenilaianPembimbing = 'all';
                    this.sortPembimbing = 'date_near';
                },
                clearSupervisor() {
                    this.searchSupervisor = '';
                    this.filterPenilaianSupervisor = 'all';
                    this.sortSupervisor = 'date_near';
                },

                showConfirmModal: false,
                confirmData: null,
                showErrorModal: false,
                errorMessage: '',
                showSuccessModal: false,
                successMessage: '',

                init() {
                    setInterval(() => { this.now = new Date(); }, 60000);
                },

                // ---------------- STATS ----------------
                get basePenguji() {
                    return this.sidangs.filter(s => s.user_roles.includes('PENGUJI 1') || s.user_roles.includes('PENGUJI 2'));
                },
                get pengujiTotal() { return this.basePenguji.length; },
                get pengujiDinilai() {
                    return this.basePenguji.filter(s => {
                        let ok = true;
                        if(s.user_roles.includes('PENGUJI 1') && s.nilai_penguji_1 === null) ok = false;
                        if(s.user_roles.includes('PENGUJI 2') && s.nilai_penguji_2 === null) ok = false;
                        return ok;
                    }).length;
                },
                get pengujiMenunggu() { return this.pengujiTotal - this.pengujiDinilai; },

                get basePembimbing() { return this.sidangs.filter(s => s.user_roles.includes('PEMBIMBING')); },
                get pembimbingTotal() { return this.basePembimbing.length; },
                get pembimbingDinilai() { return this.basePembimbing.filter(s => s.nilai_pembimbing !== null).length; },
                get pembimbingMenunggu() { return this.pembimbingTotal - this.pembimbingDinilai; },

                get baseSupervisor() { return this.sidangs.filter(s => s.user_roles.includes('SUPERVISIOR')); },
                get supervisorTotal() { return this.baseSupervisor.length; },
                get supervisorDinilai() { return this.baseSupervisor.filter(s => s.nilai_supervisor !== null).length; },
                get supervisorMenunggu() { return this.supervisorTotal - this.supervisorDinilai; },

                // ---------------- FILTERED ARRAYS ----------------
                get filteredPenguji() {
                    let res = [...this.basePenguji];
                    if (this.searchPenguji) {
                        const q = this.searchPenguji.toLowerCase();
                        res = res.filter(s => s.mahasiswa.user.name.toLowerCase().includes(q) || s.mahasiswa.nim.includes(q) || (s.pendaftaran_kp.judul_kp && s.pendaftaran_kp.judul_kp.toLowerCase().includes(q)));
                    }
                    if (this.filterPelaksanaanPenguji !== 'all') {
                        res = res.filter(s => this.getExecutionStatus(s) === this.filterPelaksanaanPenguji);
                    }
                    if (this.filterPenilaianPenguji !== 'all') {
                        res = res.filter(s => {
                            let ok = true;
                            if(s.user_roles.includes('PENGUJI 1') && s.nilai_penguji_1 === null) ok = false;
                            if(s.user_roles.includes('PENGUJI 2') && s.nilai_penguji_2 === null) ok = false;
                            return this.filterPenilaianPenguji === 'sudah' ? ok : !ok;
                        });
                    }
                    if (this.filterPeranPenguji !== 'all') {
                        res = res.filter(s => s.user_roles.includes(this.filterPeranPenguji));
                    }
                    res.sort((a, b) => {
                        return this.sortPenguji === 'date_near' ? new Date(a.tanggal_sidang) - new Date(b.tanggal_sidang) : new Date(b.tanggal_sidang) - new Date(a.tanggal_sidang);
                    });
                    return res;
                },

                get filteredPembimbing() {
                    let res = [...this.basePembimbing];
                    if (this.searchPembimbing) {
                        const q = this.searchPembimbing.toLowerCase();
                        res = res.filter(s => s.mahasiswa.user.name.toLowerCase().includes(q) || s.mahasiswa.nim.includes(q) || (s.pendaftaran_kp.judul_kp && s.pendaftaran_kp.judul_kp.toLowerCase().includes(q)));
                    }
                    if (this.filterPenilaianPembimbing !== 'all') {
                        res = res.filter(s => this.filterPenilaianPembimbing === 'sudah' ? s.nilai_pembimbing !== null : s.nilai_pembimbing === null);
                    }
                    res.sort((a, b) => {
                        return this.sortPembimbing === 'date_near' ? new Date(a.tanggal_sidang) - new Date(b.tanggal_sidang) : new Date(b.tanggal_sidang) - new Date(a.tanggal_sidang);
                    });
                    return res;
                },

                get filteredSupervisor() {
                    let res = [...this.baseSupervisor];
                    if (this.searchSupervisor) {
                        const q = this.searchSupervisor.toLowerCase();
                        res = res.filter(s => s.mahasiswa.user.name.toLowerCase().includes(q) || s.mahasiswa.nim.includes(q) || (s.pendaftaran_kp.judul_kp && s.pendaftaran_kp.judul_kp.toLowerCase().includes(q)));
                    }
                    if (this.filterPenilaianSupervisor !== 'all') {
                        res = res.filter(s => this.filterPenilaianSupervisor === 'sudah' ? s.nilai_supervisor !== null : s.nilai_supervisor === null);
                    }
                    res.sort((a, b) => {
                        return this.sortSupervisor === 'date_near' ? new Date(a.tanggal_sidang) - new Date(b.tanggal_sidang) : new Date(b.tanggal_sidang) - new Date(a.tanggal_sidang);
                    });
                    return res;
                },

                // ---------------- HELPERS ----------------
                getSpecificRoles(sidang, rolesToMatch) {
                    return sidang.user_roles.filter(r => rolesToMatch.includes(r));
                },

                getExecutionStatus(s) {
                    if (s.pelaksanaan === 'Selesai') return 'Selesai';
                    if (s.pelaksanaan === 'Dibatalkan') return 'Dibatalkan';
                    const start = new Date(`${s.tanggal_sidang}T${s.waktu_mulai_sidang}`);
                    const end = new Date(`${s.tanggal_sidang}T${s.waktu_selesai_sidang}`);
                    if (this.now < start) return 'Menunggu';
                    if (this.now >= start && this.now <= end) return 'Berjalan';
                    return s.pelaksanaan;
                },

                getStatusClass(s) {
                    const status = this.getExecutionStatus(s);
                    if (status === 'Menunggu') return 'bg-[#F9F9F9] text-gray-500 border border-gray-300';
                    if (status === 'Berjalan') return 'bg-[#DEF1FF] text-[#1D4ED8] border border-[#BFDBFE]';
                    if (status === 'Selesai') return 'bg-[#A1DFAC] text-[#1D5E2D] border border-[#BBF7D0]';
                    if (status === 'Dibatalkan') return 'bg-[#FFD3D3] text-[#B91C1C] border border-[#FECACA]';
                    return '';
                },

                getStatusDotClass(s) {
                    const status = this.getExecutionStatus(s);
                    if (status === 'Menunggu') return 'bg-gray-400';
                    if (status === 'Berjalan') return 'bg-[#1D4ED8]';
                    if (status === 'Selesai') return 'bg-[#1D5E2D]';
                    if (status === 'Dibatalkan') return 'bg-[#B91C1C]';
                    return '';
                },

                getStatusTextClass(s) {
                    const status = this.getExecutionStatus(s);
                    if (status === 'Menunggu') return 'text-gray-500';
                    if (status === 'Berjalan') return 'text-[#1D4ED8]';
                    if (status === 'Selesai') return 'text-[#1D5E2D]';
                    if (status === 'Dibatalkan') return 'text-[#B91C1C]';
                    return 'text-gray-700';
                },

                formatDate(dateString) {
                    if (!dateString) return '-';
                    return new Date(dateString).toLocaleDateString('id-ID', { weekday: 'long', day: 'numeric', month: 'short', year: 'numeric' });
                },

                formatTime(timeString) {
                    if (!timeString) return '-';
                    return timeString.substring(0, 5);
                },

                confirmUpdateStatus(id, selectElement) {
                    const newStatus = selectElement.value;
                    const sidang = this.sidangs.find(s => s.id === id);
                    if (!sidang) return;

                    const originalStatus = sidang.pelaksanaan;
                    this.confirmData = { id, newStatus, selectElement, originalStatus };
                    this.showConfirmModal = true;
                },

                cancelUpdate() {
                    if (this.confirmData) {
                        if (!['Selesai', 'Dibatalkan'].includes(this.confirmData.originalStatus)) {
                            this.confirmData.selectElement.value = "";
                        } else {
                            this.confirmData.selectElement.value = this.confirmData.originalStatus;
                        }
                    }
                    this.showConfirmModal = false;
                    this.confirmData = null;
                },

                async executeUpdate() {
                    if (!this.confirmData) return;
                    
                    this.showConfirmModal = false;
                    const { id, newStatus, selectElement, originalStatus } = this.confirmData;
                    
                    try {
                        const response = await fetch(`{{ url('dosen/input-nilai') }}/${id}/status`, {
                            method: 'POST',
                            headers: { 
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}' 
                            },
                            body: JSON.stringify({ pelaksanaan: newStatus })
                        });

                        if (!response.ok) {
                            let errorMsg = `HTTP Error ${response.status}`;
                            try {
                                const errJson = await response.json();
                                errorMsg = errJson.message || errorMsg;
                            } catch (parseErr) {}
                            this.errorMessage = errorMsg;
                            this.showErrorModal = true;
                            this.revertSelect(selectElement, originalStatus);
                            this.confirmData = null;
                            return;
                        }

                        const res = await response.json();
                        if (res.success) {
                            const idx = this.sidangs.findIndex(s => s.id === id);
                            if (idx !== -1) {
                                this.sidangs[idx].pelaksanaan = newStatus;
                                this.now = new Date();
                            }
                            this.successMessage = 'Status pelaksanaan berhasil diperbarui.';
                            this.showSuccessModal = true;
                            setTimeout(() => this.showSuccessModal = false, 2500);
                        } else { 
                            this.errorMessage = res.message || 'Gagal memperbarui status.';
                            this.showErrorModal = true;
                            this.revertSelect(selectElement, originalStatus);
                        }
                    } catch (e) { 
                        this.errorMessage = 'Gagal terhubung ke server. (' + e.message + ')';
                        this.showErrorModal = true;
                        this.revertSelect(selectElement, originalStatus);
                    }
                    this.confirmData = null;
                },

                revertSelect(selectElement, originalStatus) {
                    if (!['Selesai', 'Dibatalkan'].includes(originalStatus)) {
                        selectElement.value = "";
                    } else {
                        selectElement.value = originalStatus;
                    }
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
