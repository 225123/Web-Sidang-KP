import re

file_path = 'f:/SEMESTER 6/KP/KP-Web_Sidang_KP/resources/views/dosen/input-nilai.blade.php'
with open(file_path, 'r', encoding='utf-8') as f:
    content = f.read()

# Fix Penguji header
content = re.sub(
    r'<!-- SECTION 1: TABEL INPUT NILAI PENGUJI -->.*?<!-- Search & Filters -->',
    '''<!-- SECTION 1: TABEL INPUT NILAI PENGUJI -->
        <div class="bg-white rounded-[15px] border border-gray-200 shadow-sm overflow-hidden p-6 mb-8">
            <div class="mb-6 flex flex-col sm:flex-row justify-between items-start gap-4">
                <div>
                    <h3 class="text-[18px] font-bold text-black tracking-tight">Tabel Input Nilai Penguji</h3>
                    <p class="text-[12px] text-black/60 font-medium mt-1">Manajemen penilaian mahasiswa bimbingan dan penguji sidang KP.</p>
                </div>
                <div class="flex gap-2 shrink-0">
                    <div class="bg-[#FBBC05] text-black rounded-[5px] px-3 py-1.5 flex items-center gap-2 shadow-sm border border-yellow-500/20">
                        <span class="text-[16px] font-bold leading-none" x-text="pengujiMenunggu"></span>
                        <span class="text-[11px] font-medium uppercase tracking-wider">Menunggu</span>
                    </div>
                    <div class="bg-[#34A853] text-white rounded-[5px] px-3 py-1.5 flex items-center gap-2 shadow-sm border border-green-600/20">
                        <span class="text-[16px] font-bold leading-none" x-text="pengujiDinilai"></span>
                        <span class="text-[11px] font-medium uppercase tracking-wider">Dinilai</span>
                    </div>
                </div>
            </div>

            <div class="flex flex-col sm:flex-row justify-between items-center gap-6 mb-6">
                <div class="flex flex-wrap items-center gap-4 w-full sm:w-auto">
                    <!-- Search -->
                    <div class="relative flex-1 sm:w-[300px]">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        </div>
                        <input type="text" x-model="searchPenguji" class="block w-full pl-9 pr-4 py-2 border border-gray-300 rounded-[5px] text-[12px] text-black focus:ring-[#4285F4]" placeholder="Cari Nama/NIM, atau Judul KP...">
                    </div>

                    <!-- Filter Pelaksanaan -->
                    <div x-data="{ openFilter: false }" class="relative w-full sm:w-[150px] z-[60]">
                        <button type="button" @click="openFilter = !openFilter" @click.outside="openFilter = false" class="w-full text-[12px] border border-gray-300 rounded-[5px] py-2 px-3 bg-white text-black font-medium focus:ring-[#4285F4] flex justify-between items-center text-left shadow-sm">
                            <span x-text="filterPelaksanaanPenguji === 'all' ? 'Pelaksanaan' : filterPelaksanaanPenguji"></span>
                            <svg :class="openFilter ? 'rotate-0' : 'rotate-90'" class="w-3.5 h-3.5 transition-all duration-200 text-gray-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <div x-show="openFilter" x-transition x-cloak class="absolute w-full mt-1 bg-white border border-gray-300 rounded-[5px] shadow-lg overflow-hidden py-1 z-50">
                            <label class="block px-3 py-2 text-[12px] hover:bg-gray-100 cursor-pointer text-black"><input type="radio" value="all" x-model="filterPelaksanaanPenguji" class="hidden" @change="openFilter = false">Semua</label>
                            <label class="block px-3 py-2 text-[12px] hover:bg-gray-100 cursor-pointer text-black"><input type="radio" value="Selesai" x-model="filterPelaksanaanPenguji" class="hidden" @change="openFilter = false">Selesai</label>
                            <label class="block px-3 py-2 text-[12px] hover:bg-gray-100 cursor-pointer text-black"><input type="radio" value="Berjalan" x-model="filterPelaksanaanPenguji" class="hidden" @change="openFilter = false">Berjalan</label>
                            <label class="block px-3 py-2 text-[12px] hover:bg-gray-100 cursor-pointer text-black"><input type="radio" value="Menunggu" x-model="filterPelaksanaanPenguji" class="hidden" @change="openFilter = false">Menunggu</label>
                            <label class="block px-3 py-2 text-[12px] hover:bg-gray-100 cursor-pointer text-black"><input type="radio" value="Dibatalkan" x-model="filterPelaksanaanPenguji" class="hidden" @change="openFilter = false">Dibatalkan</label>
                        </div>
                    </div>

                    <!-- Filter Penilaian -->
                    <div x-data="{ openFilter: false }" class="relative w-full sm:w-[150px] z-[50]">
                        <button type="button" @click="openFilter = !openFilter" @click.outside="openFilter = false" class="w-full text-[12px] border border-gray-300 rounded-[5px] py-2 px-3 bg-white text-black font-medium focus:ring-[#4285F4] flex justify-between items-center text-left shadow-sm">
                            <span x-text="filterPenilaianPenguji === 'all' ? 'Penilaian' : (filterPenilaianPenguji === 'sudah' ? 'Sudah Input' : 'Belum Input')"></span>
                            <svg :class="openFilter ? 'rotate-0' : 'rotate-90'" class="w-3.5 h-3.5 transition-all duration-200 text-gray-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <div x-show="openFilter" x-transition x-cloak class="absolute w-full mt-1 bg-white border border-gray-300 rounded-[5px] shadow-lg overflow-hidden py-1 z-50">
                            <label class="block px-3 py-2 text-[12px] hover:bg-gray-100 cursor-pointer text-black"><input type="radio" value="all" x-model="filterPenilaianPenguji" class="hidden" @change="openFilter = false">Semua</label>
                            <label class="block px-3 py-2 text-[12px] hover:bg-gray-100 cursor-pointer text-black"><input type="radio" value="sudah" x-model="filterPenilaianPenguji" class="hidden" @change="openFilter = false">Sudah Input</label>
                            <label class="block px-3 py-2 text-[12px] hover:bg-gray-100 cursor-pointer text-black"><input type="radio" value="belum" x-model="filterPenilaianPenguji" class="hidden" @change="openFilter = false">Belum Input</label>
                        </div>
                    </div>
                    
                    <!-- Filter Peran -->
                    <div x-data="{ openFilter: false }" class="relative w-full sm:w-[150px] z-[40]">
                        <button type="button" @click="openFilter = !openFilter" @click.outside="openFilter = false" class="w-full text-[12px] border border-gray-300 rounded-[5px] py-2 px-3 bg-white text-black font-medium focus:ring-[#4285F4] flex justify-between items-center text-left shadow-sm">
                            <span x-text="filterPeranPenguji === 'all' ? 'Peran' : (filterPeranPenguji === 'PENGUJI 1' ? 'Penguji 1' : 'Penguji 2')"></span>
                            <svg :class="openFilter ? 'rotate-0' : 'rotate-90'" class="w-3.5 h-3.5 transition-all duration-200 text-gray-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <div x-show="openFilter" x-transition x-cloak class="absolute w-full mt-1 bg-white border border-gray-300 rounded-[5px] shadow-lg overflow-hidden py-1 z-50">
                            <label class="block px-3 py-2 text-[12px] hover:bg-gray-100 cursor-pointer text-black"><input type="radio" value="all" x-model="filterPeranPenguji" class="hidden" @change="openFilter = false">Semua Peran</label>
                            <label class="block px-3 py-2 text-[12px] hover:bg-gray-100 cursor-pointer text-black"><input type="radio" value="PENGUJI 1" x-model="filterPeranPenguji" class="hidden" @change="openFilter = false">Penguji 1</label>
                            <label class="block px-3 py-2 text-[12px] hover:bg-gray-100 cursor-pointer text-black"><input type="radio" value="PENGUJI 2" x-model="filterPeranPenguji" class="hidden" @change="openFilter = false">Penguji 2</label>
                        </div>
                    </div>

                    <!-- Filter Jadwal -->
                    <div x-data="{ openFilter: false }" class="relative w-full sm:w-[130px] z-[30]">
                        <button type="button" @click="openFilter = !openFilter" @click.outside="openFilter = false" class="w-full text-[12px] border border-gray-300 rounded-[5px] py-2 px-3 bg-white text-black font-medium focus:ring-[#4285F4] flex justify-between items-center text-left shadow-sm">
                            <span x-text="sortPenguji === 'date_near' ? 'Terdekat' : 'Terjauh'"></span>
                            <svg :class="openFilter ? 'rotate-0' : 'rotate-90'" class="w-3.5 h-3.5 transition-all duration-200 text-gray-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <div x-show="openFilter" x-transition x-cloak class="absolute w-full mt-1 bg-white border border-gray-300 rounded-[5px] shadow-lg overflow-hidden py-1 z-50">
                            <label class="block px-3 py-2 text-[12px] hover:bg-gray-100 cursor-pointer text-black"><input type="radio" value="date_near" x-model="sortPenguji" class="hidden" @change="openFilter = false">Terdekat</label>
                            <label class="block px-3 py-2 text-[12px] hover:bg-gray-100 cursor-pointer text-black"><input type="radio" value="date_far" x-model="sortPenguji" class="hidden" @change="openFilter = false">Terjauh</label>
                        </div>
                    </div>

                    <div class="flex gap-2 w-full sm:w-auto">
                        <button type="button" @click="clearPenguji()" class="flex-1 sm:flex-none border border-[#EA4335] bg-[#EA4335] text-white hover:bg-red-600 transition-colors px-4 py-1.5 rounded-[5px] text-[12px] font-bold shadow-sm flex items-center justify-center">
                            Clear Filter
                        </button>
                    </div>
                </div>
            </div>

            <!-- Search & Filters -->''',
    content,
    flags=re.DOTALL
)

# Fix Pembimbing header
content = re.sub(
    r'<!-- SECTION 2: TABEL INPUT NILAI PEMBIMBING -->.*?<!-- Search & Filters -->',
    '''<!-- SECTION 2: TABEL INPUT NILAI PEMBIMBING -->
        <div class="bg-white rounded-[15px] border border-gray-200 shadow-sm overflow-hidden p-6 mb-8">
            <div class="mb-6 flex flex-col sm:flex-row justify-between items-start gap-4">
                <div>
                    <h3 class="text-[18px] font-bold text-black tracking-tight">Tabel Input Nilai Pembimbing</h3>
                    <p class="text-[12px] text-black/60 font-medium mt-1">Manajemen penilaian mahasiswa bimbingan KP.</p>
                </div>
                <div class="flex gap-2 shrink-0">
                    <div class="bg-[#FBBC05] text-black rounded-[5px] px-3 py-1.5 flex items-center gap-2 shadow-sm border border-yellow-500/20">
                        <span class="text-[16px] font-bold leading-none" x-text="pembimbingMenunggu"></span>
                        <span class="text-[11px] font-medium uppercase tracking-wider">Menunggu</span>
                    </div>
                    <div class="bg-[#34A853] text-white rounded-[5px] px-3 py-1.5 flex items-center gap-2 shadow-sm border border-green-600/20">
                        <span class="text-[16px] font-bold leading-none" x-text="pembimbingDinilai"></span>
                        <span class="text-[11px] font-medium uppercase tracking-wider">Dinilai</span>
                    </div>
                </div>
            </div>

            <div class="flex flex-col sm:flex-row justify-between items-center gap-6 mb-6">
                <div class="flex flex-wrap items-center gap-4 w-full sm:w-auto">
                    <!-- Search -->
                    <div class="relative flex-1 sm:w-[300px]">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        </div>
                        <input type="text" x-model="searchPembimbing" class="block w-full pl-9 pr-4 py-2 border border-gray-300 rounded-[5px] text-[12px] text-black focus:ring-[#4285F4]" placeholder="Cari Nama/NIM, atau Judul KP...">
                    </div>

                    <!-- Filter Pelaksanaan -->
                    <div x-data="{ openFilter: false }" class="relative w-full sm:w-[150px] z-[60]">
                        <button type="button" @click="openFilter = !openFilter" @click.outside="openFilter = false" class="w-full text-[12px] border border-gray-300 rounded-[5px] py-2 px-3 bg-white text-black font-medium focus:ring-[#4285F4] flex justify-between items-center text-left shadow-sm">
                            <span x-text="filterPelaksanaanPembimbing === 'all' ? 'Pelaksanaan' : filterPelaksanaanPembimbing"></span>
                            <svg :class="openFilter ? 'rotate-0' : 'rotate-90'" class="w-3.5 h-3.5 transition-all duration-200 text-gray-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <div x-show="openFilter" x-transition x-cloak class="absolute w-full mt-1 bg-white border border-gray-300 rounded-[5px] shadow-lg overflow-hidden py-1 z-50">
                            <label class="block px-3 py-2 text-[12px] hover:bg-gray-100 cursor-pointer text-black"><input type="radio" value="all" x-model="filterPelaksanaanPembimbing" class="hidden" @change="openFilter = false">Semua</label>
                            <label class="block px-3 py-2 text-[12px] hover:bg-gray-100 cursor-pointer text-black"><input type="radio" value="Selesai" x-model="filterPelaksanaanPembimbing" class="hidden" @change="openFilter = false">Selesai</label>
                            <label class="block px-3 py-2 text-[12px] hover:bg-gray-100 cursor-pointer text-black"><input type="radio" value="Berjalan" x-model="filterPelaksanaanPembimbing" class="hidden" @change="openFilter = false">Berjalan</label>
                            <label class="block px-3 py-2 text-[12px] hover:bg-gray-100 cursor-pointer text-black"><input type="radio" value="Menunggu" x-model="filterPelaksanaanPembimbing" class="hidden" @change="openFilter = false">Menunggu</label>
                            <label class="block px-3 py-2 text-[12px] hover:bg-gray-100 cursor-pointer text-black"><input type="radio" value="Dibatalkan" x-model="filterPelaksanaanPembimbing" class="hidden" @change="openFilter = false">Dibatalkan</label>
                        </div>
                    </div>

                    <!-- Filter Penilaian -->
                    <div x-data="{ openFilter: false }" class="relative w-full sm:w-[150px] z-[50]">
                        <button type="button" @click="openFilter = !openFilter" @click.outside="openFilter = false" class="w-full text-[12px] border border-gray-300 rounded-[5px] py-2 px-3 bg-white text-black font-medium focus:ring-[#4285F4] flex justify-between items-center text-left shadow-sm">
                            <span x-text="filterPenilaianPembimbing === 'all' ? 'Penilaian' : (filterPenilaianPembimbing === 'sudah' ? 'Sudah Input' : 'Belum Input')"></span>
                            <svg :class="openFilter ? 'rotate-0' : 'rotate-90'" class="w-3.5 h-3.5 transition-all duration-200 text-gray-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <div x-show="openFilter" x-transition x-cloak class="absolute w-full mt-1 bg-white border border-gray-300 rounded-[5px] shadow-lg overflow-hidden py-1 z-50">
                            <label class="block px-3 py-2 text-[12px] hover:bg-gray-100 cursor-pointer text-black"><input type="radio" value="all" x-model="filterPenilaianPembimbing" class="hidden" @change="openFilter = false">Semua</label>
                            <label class="block px-3 py-2 text-[12px] hover:bg-gray-100 cursor-pointer text-black"><input type="radio" value="sudah" x-model="filterPenilaianPembimbing" class="hidden" @change="openFilter = false">Sudah Input</label>
                            <label class="block px-3 py-2 text-[12px] hover:bg-gray-100 cursor-pointer text-black"><input type="radio" value="belum" x-model="filterPenilaianPembimbing" class="hidden" @change="openFilter = false">Belum Input</label>
                        </div>
                    </div>

                    <!-- Filter Jadwal -->
                    <div x-data="{ openFilter: false }" class="relative w-full sm:w-[130px] z-[40]">
                        <button type="button" @click="openFilter = !openFilter" @click.outside="openFilter = false" class="w-full text-[12px] border border-gray-300 rounded-[5px] py-2 px-3 bg-white text-black font-medium focus:ring-[#4285F4] flex justify-between items-center text-left shadow-sm">
                            <span x-text="sortPembimbing === 'date_near' ? 'Terdekat' : 'Terjauh'"></span>
                            <svg :class="openFilter ? 'rotate-0' : 'rotate-90'" class="w-3.5 h-3.5 transition-all duration-200 text-gray-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <div x-show="openFilter" x-transition x-cloak class="absolute w-full mt-1 bg-white border border-gray-300 rounded-[5px] shadow-lg overflow-hidden py-1 z-50">
                            <label class="block px-3 py-2 text-[12px] hover:bg-gray-100 cursor-pointer text-black"><input type="radio" value="date_near" x-model="sortPembimbing" class="hidden" @change="openFilter = false">Terdekat</label>
                            <label class="block px-3 py-2 text-[12px] hover:bg-gray-100 cursor-pointer text-black"><input type="radio" value="date_far" x-model="sortPembimbing" class="hidden" @change="openFilter = false">Terjauh</label>
                        </div>
                    </div>

                    <div class="flex gap-2 w-full sm:w-auto">
                        <button type="button" @click="clearPembimbing()" class="flex-1 sm:flex-none border border-[#EA4335] bg-[#EA4335] text-white hover:bg-red-600 transition-colors px-4 py-1.5 rounded-[5px] text-[12px] font-bold shadow-sm flex items-center justify-center">
                            Clear Filter
                        </button>
                    </div>
                </div>
            </div>

            <!-- Search & Filters -->''',
    content,
    flags=re.DOTALL
)

# Fix Supervisor header (if exists)
# In dosen there is no Supervisor table typically, but let's check
if '<!-- SECTION 3: TABEL INPUT NILAI SUPERVISOR -->' in content:
    content = re.sub(
        r'<!-- SECTION 3: TABEL INPUT NILAI SUPERVISOR -->.*?<!-- Search & Filters -->',
        '''<!-- SECTION 3: TABEL INPUT NILAI SUPERVISOR -->
            <div class="bg-white rounded-[15px] border border-gray-200 shadow-sm overflow-hidden p-6 mb-8">
                <div class="mb-6 flex flex-col sm:flex-row justify-between items-start gap-4">
                    <div>
                        <h3 class="text-[18px] font-bold text-black tracking-tight">Tabel Input Nilai Supervisor</h3>
                        <p class="text-[12px] text-black/60 font-medium mt-1">Manajemen penilaian mahasiswa dari instansi/perusahaan.</p>
                    </div>
                    <div class="flex gap-2 shrink-0">
                        <div class="bg-[#FBBC05] text-black rounded-[5px] px-3 py-1.5 flex items-center gap-2 shadow-sm border border-yellow-500/20">
                            <span class="text-[16px] font-bold leading-none" x-text="supervisorMenunggu"></span>
                            <span class="text-[11px] font-medium uppercase tracking-wider">Menunggu</span>
                        </div>
                        <div class="bg-[#34A853] text-white rounded-[5px] px-3 py-1.5 flex items-center gap-2 shadow-sm border border-green-600/20">
                            <span class="text-[16px] font-bold leading-none" x-text="supervisorDinilai"></span>
                            <span class="text-[11px] font-medium uppercase tracking-wider">Dinilai</span>
                        </div>
                    </div>
                </div>

                <div class="flex flex-col sm:flex-row justify-between items-center gap-6 mb-6">
                    <div class="flex flex-wrap items-center gap-4 w-full sm:w-auto">
                        <!-- Search -->
                        <div class="relative flex-1 sm:w-[300px]">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                            </div>
                            <input type="text" x-model="searchSupervisor" class="block w-full pl-9 pr-4 py-2 border border-gray-300 rounded-[5px] text-[12px] text-black focus:ring-[#4285F4]" placeholder="Cari Nama/NIM, atau Judul KP...">
                        </div>

                        <!-- Filter Penilaian -->
                        <div x-data="{ openFilter: false }" class="relative w-full sm:w-[150px] z-[50]">
                            <button type="button" @click="openFilter = !openFilter" @click.outside="openFilter = false" class="w-full text-[12px] border border-gray-300 rounded-[5px] py-2 px-3 bg-white text-black font-medium focus:ring-[#4285F4] flex justify-between items-center text-left shadow-sm">
                                <span x-text="filterPenilaianSupervisor === 'all' ? 'Penilaian' : (filterPenilaianSupervisor === 'sudah' ? 'Sudah Input' : 'Belum Input')"></span>
                                <svg :class="openFilter ? 'rotate-0' : 'rotate-90'" class="w-3.5 h-3.5 transition-all duration-200 text-gray-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                            </button>
                            <div x-show="openFilter" x-transition x-cloak class="absolute w-full mt-1 bg-white border border-gray-300 rounded-[5px] shadow-lg overflow-hidden py-1 z-50">
                                <label class="block px-3 py-2 text-[12px] hover:bg-gray-100 cursor-pointer text-black"><input type="radio" value="all" x-model="filterPenilaianSupervisor" class="hidden" @change="openFilter = false">Semua</label>
                                <label class="block px-3 py-2 text-[12px] hover:bg-gray-100 cursor-pointer text-black"><input type="radio" value="sudah" x-model="filterPenilaianSupervisor" class="hidden" @change="openFilter = false">Sudah Input</label>
                                <label class="block px-3 py-2 text-[12px] hover:bg-gray-100 cursor-pointer text-black"><input type="radio" value="belum" x-model="filterPenilaianSupervisor" class="hidden" @change="openFilter = false">Belum Input</label>
                            </div>
                        </div>

                        <!-- Filter Jadwal -->
                        <div x-data="{ openFilter: false }" class="relative w-full sm:w-[130px] z-[40]">
                            <button type="button" @click="openFilter = !openFilter" @click.outside="openFilter = false" class="w-full text-[12px] border border-gray-300 rounded-[5px] py-2 px-3 bg-white text-black font-medium focus:ring-[#4285F4] flex justify-between items-center text-left shadow-sm">
                                <span x-text="sortSupervisor === 'date_near' ? 'Terdekat' : 'Terjauh'"></span>
                                <svg :class="openFilter ? 'rotate-0' : 'rotate-90'" class="w-3.5 h-3.5 transition-all duration-200 text-gray-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                            </button>
                            <div x-show="openFilter" x-transition x-cloak class="absolute w-full mt-1 bg-white border border-gray-300 rounded-[5px] shadow-lg overflow-hidden py-1 z-50">
                                <label class="block px-3 py-2 text-[12px] hover:bg-gray-100 cursor-pointer text-black"><input type="radio" value="date_near" x-model="sortSupervisor" class="hidden" @change="openFilter = false">Terdekat</label>
                                <label class="block px-3 py-2 text-[12px] hover:bg-gray-100 cursor-pointer text-black"><input type="radio" value="date_far" x-model="sortSupervisor" class="hidden" @change="openFilter = false">Terjauh</label>
                            </div>
                        </div>

                        <div class="flex gap-2 w-full sm:w-auto">
                            <button type="button" @click="clearSupervisor()" class="flex-1 sm:flex-none border border-[#EA4335] bg-[#EA4335] text-white hover:bg-red-600 transition-colors px-4 py-1.5 rounded-[5px] text-[12px] font-bold shadow-sm flex items-center justify-center">
                                Clear Filter
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Search & Filters -->''',
        content,
        flags=re.DOTALL
    )

content = re.sub(
    r'<!-- Search & Filters -->.*?<div class="border border-gray-200 rounded-\[10px\] overflow-hidden">',
    '<div class="border border-gray-200 rounded-[10px] overflow-hidden">',
    content,
    flags=re.DOTALL
)

# Replace table bodies / styling
content = content.replace('class="w-full text-center border-collapse text-[12px] min-w-[1000px]"', 'class="w-full border-collapse text-[13px] min-w-[1000px]"')
content = content.replace('class="w-full text-center border-collapse text-[12px] min-w-[800px]"', 'class="w-full border-collapse text-[13px] min-w-[1000px]"')
content = content.replace('class="bg-[#EBEBEB] font-bold text-black border-b border-[#CAC0C0] h-[45px]"', 'class="bg-[#EBEBEB] text-black"')
content = content.replace('class="border-r border-[#CAC0C0] px-3 py-2 w-[50px]"', 'class="py-3 px-4 font-bold text-center w-[60px] border-b border-r border-gray-300"')
content = content.replace('class="border-r border-[#CAC0C0] px-4 py-2 text-left w-[180px]"', 'class="py-3 px-4 font-bold text-left border-b border-r border-gray-300 w-[180px]"')
content = content.replace('class="border-r border-[#CAC0C0] px-4 py-2 text-left w-[150px]"', 'class="py-3 px-4 font-bold text-left border-b border-r border-gray-300 w-[150px]"')
content = content.replace('class="border-r border-[#CAC0C0] px-4 py-2 text-left w-[250px]"', 'class="py-3 px-4 font-bold text-left border-b border-r border-gray-300 w-[250px]"')
content = content.replace('class="border-r border-[#CAC0C0] px-4 py-2 text-left"', 'class="py-3 px-4 font-bold text-left border-b border-r border-gray-300"')
content = content.replace('class="border-r border-[#CAC0C0] px-4 py-2 w-[120px]"', 'class="py-3 px-4 font-bold text-center border-b border-r border-gray-300 w-[120px]"')
content = content.replace('class="border-r border-[#CAC0C0] px-4 py-2 w-[140px]"', 'class="py-3 px-4 font-bold text-center border-b border-r border-gray-300 w-[140px]"')
content = content.replace('class="border-r border-[#CAC0C0] px-4 py-2 w-[150px]"', 'class="py-3 px-4 font-bold text-center border-b border-r border-gray-300 w-[150px]"')
content = content.replace('class="px-4 py-2 w-[150px]"', 'class="py-3 px-4 font-bold text-center border-b border-gray-300 w-[150px]"')
content = content.replace('class="px-4 py-2 w-[200px]"', 'class="py-3 px-4 font-bold text-center border-b border-gray-300 w-[200px]"')

content = content.replace('class="border-b border-[#CAC0C0] hover:bg-gray-50 transition-colors"', 'class="hover:bg-gray-50 transition-colors"')
content = content.replace('class="border-r border-[#CAC0C0] px-3 py-4 text-gray-700"', 'class="py-3 px-4 text-center text-black/60 border-r border-gray-200"')
content = content.replace('class="border-r border-[#CAC0C0] px-4 py-4 text-left font-bold text-black uppercase text-[11px]"', 'class="py-3 px-4 text-left font-bold text-black uppercase text-[11px] border-r border-gray-200"')
content = content.replace('class="border-r border-[#CAC0C0] px-4 py-4 text-left"', 'class="py-3 px-4 text-left border-r border-gray-200"')
content = content.replace('class="border-r border-[#CAC0C0] px-4 py-4"', 'class="py-3 px-4 text-center border-r border-gray-200"')
content = content.replace('class="px-4 py-4"', 'class="py-3 px-4 text-center"')

content = content.replace('<tbody class="bg-white">', '<tbody class="divide-y divide-gray-200 bg-white">')

# Fix info banner
content = content.replace(
    '''<div class="bg-[#EAEFFF] border border-[#BACDFB] rounded-[10px] p-4 flex items-center gap-4 shadow-sm mb-10">
            <div class="bg-[#4285F4] w-6 h-6 rounded-full flex items-center justify-center text-white shrink-0 shadow-sm font-bold text-sm">i</div>
            <p class="text-[14px] text-black font-medium leading-relaxed">
                Lakukan penginputan nilai berdasarkan peran anda terhadap KP Mahasiswa.
            </p>
        </div>''',
    '''<div class="bg-[#E6F0FA] border border-[#D0E3F5] rounded-[10px] p-4 lg:p-5 mb-6 flex items-start gap-4 shadow-sm">
            <div class="w-6 h-6 rounded-full bg-[#4285F4] text-white flex items-center justify-center font-bold flex-shrink-0 mt-0.5">
                i
            </div>
            <p class="text-[14px] text-[#1A1A1A] font-medium leading-relaxed m-0 mt-0.5">
                Lakukan penginputan nilai berdasarkan peran anda terhadap KP Mahasiswa.
            </p>
        </div>'''
)

with open(file_path, 'w', encoding='utf-8') as f:
    f.write(content)
print('Done.')
