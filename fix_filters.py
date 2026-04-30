import re

file_path = 'f:/SEMESTER 6/KP/KP-Web_Sidang_KP/resources/views/koordinator/input-nilai.blade.php'
with open(file_path, 'r', encoding='utf-8') as f:
    content = f.read()

# Fix Pembimbing header
content = re.sub(
    r'<!-- Search \(Top\) -->.*?<div class="overflow-x-auto">',
    '''<!-- Search & Filters -->
                    <div class="flex flex-col sm:flex-row justify-between items-center gap-6 mb-6">
                        <div class="flex flex-wrap items-center gap-4 w-full sm:w-auto">
                            <!-- Search -->
                            <div class="relative flex-1 sm:w-[300px]">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                                </div>
                                <input type="text" x-model="searchPembimbing" class="block w-full pl-9 pr-4 py-2 border border-gray-300 rounded-[5px] text-[12px] text-black focus:ring-[#4285F4]" placeholder="Cari Nama/NIM, atau Judul KP...">
                            </div>

                            <!-- Filter Penilaian -->
                            <div x-data="{ openFilter: false }" class="relative w-full sm:w-[150px] z-[50]">
                                <button type="button" @click="openFilter = !openFilter" @click.outside="openFilter = false" class="w-full text-[12px] border border-gray-300 rounded-[5px] py-2 px-3 bg-white text-black font-medium focus:ring-[#4285F4] flex justify-between items-center text-left shadow-sm">
                                    <span x-text="filterPenilaianPembimbing === \'all\' ? \'Penilaian\' : (filterPenilaianPembimbing === \'sudah\' ? \'Sudah Input\' : \'Belum Input\')"></span>
                                    <svg :class="openFilter ? \'rotate-0\' : \'rotate-90\'" class="w-3.5 h-3.5 transition-all duration-200 text-gray-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
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
                                    <span x-text="sortPembimbing === \'date_near\' ? \'Terdekat\' : \'Terjauh\'"></span>
                                    <svg :class="openFilter ? \'rotate-0\' : \'rotate-90\'" class="w-3.5 h-3.5 transition-all duration-200 text-gray-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
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
                </div>

                <div class="overflow-x-auto">''',
    content,
    count=1,
    flags=re.DOTALL
)

# Fix Supervisor header
content = re.sub(
    r'<!-- Search \(Top\) -->.*?<div class="overflow-x-auto">',
    '''<!-- Search & Filters -->
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
                                    <span x-text="filterPenilaianSupervisor === \'all\' ? \'Penilaian\' : (filterPenilaianSupervisor === \'sudah\' ? \'Sudah Input\' : \'Belum Input\')"></span>
                                    <svg :class="openFilter ? \'rotate-0\' : \'rotate-90\'" class="w-3.5 h-3.5 transition-all duration-200 text-gray-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
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
                                    <span x-text="sortSupervisor === \'date_near\' ? \'Terdekat\' : \'Terjauh\'"></span>
                                    <svg :class="openFilter ? \'rotate-0\' : \'rotate-90\'" class="w-3.5 h-3.5 transition-all duration-200 text-gray-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
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
                </div>

                <div class="overflow-x-auto">''',
    content,
    count=1,
    flags=re.DOTALL
)

with open(file_path, 'w', encoding='utf-8') as f:
    f.write(content)
print('Done fixing missing filter sections in Pembimbing and Supervisor')
