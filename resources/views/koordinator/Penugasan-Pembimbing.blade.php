<x-dashboard-layout header="Penugasan Pembimbing" userName="{{ auth()->user()->name ?? 'KOORDINATOR KP' }}" roleName="KOORDINATOR KP">
    <x-slot:sidebar>
        @include('koordinator.components.sidebar', ['active' => 'penugasan-pembimbing'])
    </x-slot>

    <x-slot:headerActions>
        <div x-data="{ open: false, selected: 'Genap 2025/2026' }" class="relative w-[212px]">
            <button @click="open = !open" @click.outside="open = false" type="button" 
                class="w-full flex items-center justify-between border border-[#CAC0C0] bg-[#FBFBFB] rounded-[5px] shadow-sm text-[13px] font-medium py-1.5 px-3 focus:outline-none focus:border-[#4CC098] focus:ring-1 focus:ring-[#4CC098] cursor-pointer text-black h-[32px]">
                
                <span x-text="selected"></span>
                
                <svg :class="open ? 'rotate-0' : 'rotate-180'" class="w-3.5 h-3.5 text-gray-500 transition-transform duration-200 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                </svg>
            </button>

            <div x-show="open" x-transition style="display: none;" 
                class="absolute z-50 w-full mt-1 bg-[#FBFBFB] border border-[#CAC0C0] rounded-[5px] shadow-lg overflow-hidden">
                <ul class="py-1 text-[13px] font-medium text-black">
                    <li>
                        <button @click="selected = 'Genap 2025/2026'; open = false" type="button" class="block w-full text-left px-3 py-2 hover:bg-[#E8E5E5] transition-colors cursor-pointer">
                            Genap 2025/2026
                        </button>
                    </li>
                    <li>
                        <button @click="selected = 'Ganjil 2025/2026'; open = false" type="button" class="block w-full text-left px-3 py-2 hover:bg-[#E8E5E5] transition-colors cursor-pointer">
                            Ganjil 2025/2026
                        </button>
                    </li>
                </ul>
            </div>
            <input type="hidden" name="periode" :value="selected">
        </div>
    </x-slot:headerActions>

    <!-- Flash message on action success/error -->
    <div class="px-2 xl:px-0">
        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-[5px] relative mb-4 shadow-sm w-full" role="alert">
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
        @endif
        @if(session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-[5px] relative mb-4 shadow-sm w-full" role="alert">
                <span class="block sm:inline">{{ session('error') }}</span>
            </div>
        @endif
    </div>

    <div class="w-full flex-1 pb-10" x-data="penugasanController()">
        <form id="form-assignment" action="{{ route('koordinator.penugasan-pembimbing.store') }}" method="POST">
            @csrf
        </form>

        <!-- Top Header & Filters -->
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6 mb-6">
            <div class="flex-1 bg-[#E6F0FA] border border-[#D0E3F5] rounded-[10px] p-4 lg:p-5 flex items-start gap-4 shadow-sm w-full">
                <div class="w-6 h-6 rounded-full bg-[#4285F4] text-white flex items-center justify-center font-bold flex-shrink-0 mt-0.5">i</div>
                <p class="text-[14px] text-[#1A1A1A] font-medium leading-relaxed m-0 mt-0.5">
                    Penugasan Dosen Pembimbing Mahasiswa KP.<br>
                    Silakan klik 'Submit' setelah data terisi untuk dibagikan kepada mahasiswa dan dosen.
                </p>
            </div>

            <div class="flex items-center shrink-0 ml-auto w-full lg:w-auto">
                <form action="{{ route('koordinator.penugasan-pembimbing.auto') }}" method="POST" class="w-full">
                    @csrf
                    <button type="submit" onclick="return confirm('Proses ini akan menyimpan pembagian otomatis dosen pembimbing langsung ke database agar filter berfungsi akurat. Apakah Anda yakin?')" class="bg-[#4285F4] hover:bg-blue-600 font-bold text-white rounded-[5px] px-6 py-2 text-[13px] flex items-center justify-center gap-2 shadow-sm w-full lg:w-[120px] transition-colors" title="Bagi rata beban dosen pada mahasiswa yang belum ditugaskan">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" /></svg>
                        Auto
                    </button>
                </form>
            </div>
        </div>

            <!-- Beban Dosen Preview Card & Submit Action -->
            <div class="flex flex-col lg:flex-row justify-between items-end gap-6 mb-6">
                <!-- Beban Dosen Container -->
                <div class="bg-[#E6E6E6] rounded-[5px] p-5 w-full lg:w-[50%] h-auto flex flex-col justify-between">
                    <h3 class="font-bold text-[16px] text-black mb-2">Beban Dosen</h3>
                    
                    <div class="text-[12px] font-medium text-black pr-2">
                        <table class="w-auto border-collapse">
                            <tbody>
                                <template x-for="(load, index) in currentLoads" :key="load.id">
                                    <tr class="border-b border-gray-300">
                                        <td class="py-1.5 pr-2 whitespace-nowrap align-top text-left w-[20px]" x-text="(index + 1) + '.'"></td>
                                        <td class="py-1.5 pr-4 whitespace-nowrap align-top text-left" x-text="load.nama" :title="load.nama"></td>
                                        <td class="py-1.5 px-2 whitespace-nowrap align-top text-center">:</td>
                                        <td class="py-1.5 whitespace-nowrap align-top text-left"><span x-text="load.beban"></span> Mahasiswa</td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>

                    <div class="flex justify-between items-end mt-4 pt-2">
                        <button type="button" class="border border-black border-solid bg-transparent hover:bg-gray-200 text-black text-[11px] font-bold px-3 py-1 rounded-[5px] flex items-center gap-1 shadow-sm transition-colors">
                            <span class="text-[14px] leading-none">+</span> Tambah
                        </button>
                        <div class="font-bold text-[12px] text-black">Total Mahasiswa : <span class="ml-1">{{ $totalMahasiswa }}</span></div>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="w-full lg:w-auto flex justify-end">
                    <button type="submit" form="form-assignment" @click="isDirty = false" 
                        class="text-black font-bold rounded-[20px] px-8 py-2 text-[13px] flex items-center gap-2 shadow hover:shadow-md transition-colors"
                        :class="isDirty ? 'bg-[#4285F4] hover:bg-blue-600 text-white' : 'bg-[#FDE293] hover:bg-yellow-400 text-[#A67C00]'">
                        <svg class="w-3.5 h-3.5 fill-current" viewBox="0 0 20 20"><path d="M0 11l2-2 5 5L18 3l2 2L7 18z"/></svg>
                        <span x-text="isDirty ? 'SUBMIT KEMBALI' : 'TELAH DISUBMIT'"></span>
                    </button>
                </div>
            </div>

            <!-- Mahasiswa Section Header & Badges -->
            <div class="flex flex-col sm:flex-row justify-between items-center mb-6 mt-8 border-t border-gray-200 pt-6">
                <h2 class="text-[20px] font-bold text-black m-0 mb-4 sm:mb-0">Mahasiswa</h2>
                <div class="flex items-center gap-4">
                    <div class="bg-[#34A853] text-white rounded-[5px] w-[80px] h-[55px] flex flex-col justify-center items-center shadow-sm">
                        <div class="flex items-center gap-1.5 border-b border-green-400 pb-0.5 w-[80%] justify-center">
                            <svg class="w-3 h-3 rounded-full border border-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                            <span class="text-[18px] font-bold leading-none" x-text="stats.ditugaskan"></span>
                        </div>
                        <span class="text-[9px] font-medium mt-1">Ditugaskan</span>
                    </div>
                    <div class="bg-[#FBBC05] text-black rounded-[5px] w-[80px] h-[55px] flex flex-col justify-center items-center shadow-sm">
                        <div class="flex items-center gap-1.5 border-b border-[#D4A000] pb-0.5 w-[80%] justify-center">
                            <div class="w-2.5 h-2.5 border border-black rounded-sm"></div>
                            <span class="text-[18px] font-bold leading-none" x-text="stats.menunggu"></span>
                        </div>
                        <span class="text-[9px] font-medium mt-1">Menunggu</span>
                    </div>
                </div>
            </div>

            <!-- Modern Table Module -->
            <div class="bg-white rounded-xl">
                
                <form action="{{ route('koordinator.penugasan-pembimbing') }}" method="GET" data-turbo-frame="table-data" class="mb-6 flex flex-wrap items-center gap-4 w-full" id="filter-form" x-data="{ submitForm() { document.getElementById('hidden-filter-submit').click(); } }">
                    <button type="submit" id="hidden-filter-submit" class="hidden"></button>
                    
                    <div class="relative w-full sm:w-[320px] shrink-0">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-4 w-4 text-gray-400 font-bold" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        </div>
                        <input id="search-input" type="text" name="search" value="{{ request('search') }}" x-on:input.debounce.500ms="submitForm()" @clear-filters.window="$el.value = ''" placeholder="Cari berdasarkan Nama atau NIM..." 
                            class="border border-[#CAC0C0] rounded-[5px] pl-10 pr-3 py-2 w-full text-[13px] font-medium focus:outline-none focus:border-[#4CC098] focus:ring-1 focus:ring-[#4CC098] shadow-sm text-gray-700">
                    </div>
                        
                        <div class="flex items-center gap-2 w-full sm:w-auto" x-data="{ openJenis: false, selectedJenis: '{{ request('jenis_kp', 'All') }}' }" @clear-filters.window="selectedJenis = 'All'">
                            <label class="font-bold text-[13px] text-gray-700 whitespace-nowrap">Jenis KP:</label>
                            <div class="relative w-full sm:w-[120px]">
                                <button type="button" @click="openJenis = !openJenis" @click.outside="openJenis = false" 
                                    class="w-full flex justify-between items-center bg-white border border-[#CAC0C0] rounded px-3 py-1.5 focus:outline-none text-[13px] font-medium cursor-pointer shadow-sm text-gray-700">
                                    <span class="truncate w-full text-left pr-2" x-text="selectedJenis"></span>
                                    <svg :class="openJenis ? 'rotate-180' : 'rotate-0'" class="w-3.5 h-3.5 flex-shrink-0 transition-transform duration-200 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                                </button>
                                <div x-show="openJenis" x-transition style="display: none;" class="absolute z-50 w-full mt-1 bg-white border border-[#CAC0C0] rounded shadow-lg overflow-hidden">
                                    <ul class="py-1 text-[13px]">
                                        <li><label class="block px-3 py-1.5 hover:bg-gray-100 cursor-pointer"><input type="radio" name="jenis_kp" value="All" class="hidden" x-model="selectedJenis" @change="submitForm()">All</label></li>
                                        <li><label class="block px-3 py-1.5 hover:bg-gray-100 cursor-pointer"><input type="radio" name="jenis_kp" value="Internal" class="hidden" x-model="selectedJenis" @change="submitForm()">Internal</label></li>
                                        <li><label class="block px-3 py-1.5 hover:bg-gray-100 cursor-pointer"><input type="radio" name="jenis_kp" value="Eksternal" class="hidden" x-model="selectedJenis" @change="submitForm()">Eksternal</label></li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center gap-2 w-full sm:w-auto" x-data="{ openPengerjaan: false, selectedPengerjaan: '{{ request('pengerjaan', 'All') }}' }" @clear-filters.window="selectedPengerjaan = 'All'">
                            <label class="font-bold text-[13px] text-gray-700 whitespace-nowrap">Pengerjaan:</label>
                            <div class="relative w-full sm:w-[130px]">
                                <button type="button" @click="openPengerjaan = !openPengerjaan" @click.outside="openPengerjaan = false" 
                                    class="w-full flex justify-between items-center bg-white border border-[#CAC0C0] rounded px-3 py-1.5 focus:outline-none text-[13px] font-medium cursor-pointer shadow-sm text-gray-700">
                                    <span class="truncate w-full text-left pr-2" x-text="selectedPengerjaan"></span>
                                    <svg :class="openPengerjaan ? 'rotate-180' : 'rotate-0'" class="w-3.5 h-3.5 flex-shrink-0 transition-transform duration-200 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                                </button>
                                <div x-show="openPengerjaan" x-transition style="display: none;" class="absolute z-50 w-full mt-1 bg-white border border-[#CAC0C0] rounded shadow-lg overflow-hidden">
                                    <ul class="py-1 text-[13px]">
                                        <li><label class="block px-3 py-1.5 hover:bg-gray-100 cursor-pointer"><input type="radio" name="pengerjaan" value="All" class="hidden" x-model="selectedPengerjaan" @change="submitForm()">All</label></li>
                                        <li><label class="block px-3 py-1.5 hover:bg-gray-100 cursor-pointer"><input type="radio" name="pengerjaan" value="Individu" class="hidden" x-model="selectedPengerjaan" @change="submitForm()">Individu</label></li>
                                        <li><label class="block px-3 py-1.5 hover:bg-gray-100 cursor-pointer"><input type="radio" name="pengerjaan" value="Berkelompok" class="hidden" x-model="selectedPengerjaan" @change="submitForm()">Berkelompok</label></li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center gap-2 w-full sm:w-auto" x-data="{ 
                            openDosen: false, 
                            searchDosenText: '',
                            selectedDosenId: '{{ request('dosen_pembimbing', 'All') }}',
                            selectedDosenName: '{{ request('dosen_pembimbing') == 'Belum Ditugaskan' ? 'Belum Ditugaskan' : (request('dosen_pembimbing') && request('dosen_pembimbing') != 'All' ? addslashes(collect($dosenList)->where('id', request('dosen_pembimbing'))->first()['nama'] ?? 'All') : 'All') }}'
                        }" @clear-filters.window="selectedDosenId = 'All'; selectedDosenName = 'All'; searchDosenText = ''">
                            <label class="font-bold text-[13px] text-gray-700 whitespace-nowrap">Pembimbing:</label>
                            <div class="relative w-full sm:w-[200px]">
                                <button type="button" @click="openDosen = !openDosen" @click.outside="openDosen = false" 
                                    class="w-full flex justify-between items-center bg-white border border-[#CAC0C0] rounded px-3 py-1.5 focus:outline-none text-[13px] font-medium cursor-pointer shadow-sm text-gray-700">
                                    <span class="truncate w-full text-left pr-2" x-text="selectedDosenName"></span>
                                    <svg :class="openDosen ? 'rotate-180' : 'rotate-0'" class="w-3.5 h-3.5 flex-shrink-0 transition-transform duration-200 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                                </button>
                                <div x-show="openDosen" x-transition style="display: none;" class="absolute z-50 w-[240px] right-0 sm:right-auto mt-1 bg-white border border-[#CAC0C0] rounded shadow-lg flex flex-col overflow-hidden">
                                    <div class="px-2 py-2 sticky top-0 bg-[#F5F6F8] border-b border-gray-200 z-10 w-full shrink-0">
                                        <input type="text" x-model="searchDosenText" @click.stop class="w-full border border-gray-300 rounded px-2 py-1.5 text-[12px] focus:outline-none focus:border-[#4CC098]" placeholder="Ketik nama dosen...">
                                    </div>
                                    <ul class="py-1 text-[13px] max-h-[200px] overflow-y-auto overflow-x-hidden custom-scrollbar flex-1 w-full scale-100">
                                        <li x-show="'all'.includes(searchDosenText.toLowerCase())"><label class="block px-3 py-1.5 hover:bg-gray-100 cursor-pointer"><input type="radio" name="dosen_pembimbing" value="All" class="hidden" x-model="selectedDosenId" @change="selectedDosenName = 'All'; openDosen = false; submitForm()">All</label></li>
                                        <li x-show="'belum ditugaskan'.includes(searchDosenText.toLowerCase())"><label class="block px-3 py-1.5 hover:bg-gray-100 cursor-pointer"><input type="radio" name="dosen_pembimbing" value="Belum Ditugaskan" class="hidden" x-model="selectedDosenId" @change="selectedDosenName = 'Belum Ditugaskan'; openDosen = false; submitForm()">Belum Ditugaskan</label></li>
                                        @foreach($dosenList as $dl)
                                            <li x-show="'{{ strtolower(addslashes($dl['nama'])) }}'.includes(searchDosenText.toLowerCase())"><label class="block px-3 py-1.5 hover:bg-gray-100 cursor-pointer text-[12px]"><input type="radio" name="dosen_pembimbing" value="{{ $dl['id'] }}" class="hidden" x-model="selectedDosenId" @change="selectedDosenName = '{{ addslashes($dl['nama']) }}'; openDosen = false; submitForm()">{{ $dl['nama'] }}</label></li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center gap-2 w-full sm:w-auto" x-data="{ openStatus: false, selectedStatus: '{{ request('status_filter', 'All') }}' }" @clear-filters.window="selectedStatus = 'All'">
                            <label class="font-bold text-[13px] text-gray-700 whitespace-nowrap">Status:</label>
                            <div class="relative w-full sm:w-[130px]">
                                <button type="button" @click="openStatus = !openStatus" @click.outside="openStatus = false" 
                                    class="w-full flex justify-between items-center bg-white border border-[#CAC0C0] rounded px-3 py-1.5 focus:outline-none text-[13px] font-medium cursor-pointer shadow-sm text-gray-700">
                                    <span class="truncate w-full text-left pr-2" x-text="selectedStatus"></span>
                                    <svg :class="openStatus ? 'rotate-180' : 'rotate-0'" class="w-3.5 h-3.5 flex-shrink-0 transition-transform duration-200 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                                </button>
                                <div x-show="openStatus" x-transition style="display: none;" class="absolute z-50 w-full mt-1 bg-white border border-[#CAC0C0] rounded shadow-lg overflow-hidden">
                                    <ul class="py-1 text-[13px]">
                                        <li><label class="block px-3 py-1.5 hover:bg-gray-100 cursor-pointer"><input type="radio" name="status_filter" value="All" class="hidden" x-model="selectedStatus" @change="submitForm()">All</label></li>
                                        <li><label class="block px-3 py-1.5 hover:bg-gray-100 cursor-pointer"><input type="radio" name="status_filter" value="Menunggu" class="hidden" x-model="selectedStatus" @change="submitForm()">Menunggu</label></li>
                                        <li><label class="block px-3 py-1.5 hover:bg-gray-100 cursor-pointer"><input type="radio" name="status_filter" value="Ditugaskan" class="hidden" x-model="selectedStatus" @change="submitForm()">Ditugaskan</label></li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <button type="button" @click="$dispatch('clear-filters'); setTimeout(() => submitForm(), 50)" class="flex-1 sm:flex-none border border-[#EA4335] bg-[#EA4335] text-white hover:bg-red-600 transition-colors px-4 py-2 rounded-[5px] text-[13px] font-bold shadow-sm flex items-center justify-center">
                            Clear Filter
                        </button>
                </form>
                
                <form id="form-assignment" method="POST" action="{{ route('koordinator.penugasan-pembimbing.store') }}" class="hidden">
                    @csrf
                </form>

                <turbo-frame id="table-data">
                <!-- Shared Table Styling with Pendaftaran KP -->
                <div class="overflow-x-auto bg-[#F9F9F9] rounded-t-[10px] shadow-sm border border-[#CAC0C0] mb-8">
                    <table class="w-full min-w-[1000px] text-left border-collapse text-[12px] text-center">
                        <thead class="bg-[#E0DFDF] font-bold text-black h-[40px]">
                            <tr>
                                <th class="border border-[#CAC0C0] px-4 py-2 w-[40px]">No</th>
                                <th class="border border-[#CAC0C0] px-4 py-2">Mahasiswa</th>
                                <th class="border border-[#CAC0C0] px-4 py-2">Jenis KP</th>
                                <th class="border border-[#CAC0C0] px-4 py-2">Supervisor</th>
                                <th class="border border-[#CAC0C0] px-4 py-2">Judul KP</th>
                                <th class="border border-[#CAC0C0] px-4 py-2 min-w-[180px]">Dosen Pembimbing</th>
                                <th class="border border-[#CAC0C0] px-4 py-2 min-w-[120px]">Status</th>
                                <th class="border border-[#CAC0C0] px-4 py-2 w-[80px]">Detail KP</th>
                            </tr>
                        </thead>
                        <!-- Alpine State Injector for Turbo Fetch -->
                        @php
                            $assignmentsInitNew = [];
                            $groupSizesInitNew = [];
                            foreach($pendaftarans as $p) {
                                $assignmentsInitNew[$p['id']] = $p['dosen_id'] ?? '';
                                $groupSizesInitNew[$p['id']] = count($p['mahasiswas']);
                            }
                        @endphp
                        <tbody class="align-middle bg-white" x-init='$dispatch("update-assignments", { assignments: @json($assignmentsInitNew), groupSizes: @json($groupSizesInitNew) })'>
                            @php $noCounter = ($paginator->currentPage() - 1) * $paginator->perPage() + 1; @endphp
                            
                            @forelse($pendaftarans as $p)
                                @php 
                                    $mhsList = $p['mahasiswas'];
                                    $rowspan = count($mhsList); 
                                @endphp
                                
                                @foreach($mhsList as $mIndex => $mhs)
                                    <tr class="hover:bg-gray-50 border-b border-[#CAC0C0] font-medium transition-colors" :class="openDropdown === '{{ $p['id'] }}' ? 'relative z-[50]' : ''">
                                        
                                        <!-- Index -->
                                        <td class="border-r border-[#CAC0C0] px-4 py-4 text-center font-bold text-gray-700 align-middle">{{ $noCounter++ }}</td>
                                        
                                        <!-- Mahasiswa Data Stacked nicely -->
                                        <td class="border-r border-[#CAC0C0] px-4 py-2 align-middle">
                                            <div class="font-bold text-[12px] text-gray-800 leading-snug">{{ $mhs['nama'] }}</div>
                                            <div class="text-[11px] text-gray-500">{{ $mhs['nim'] }}</div>
                                        </td>

                                        <!-- The common Group Info -->
                                        @if($mIndex === 0)
                                            <td rowspan="{{ $rowspan }}" class="border-r border-[#CAC0C0] px-4 py-2 align-middle text-center">
                                                <div class="font-bold text-[12px] text-gray-800 leading-snug">{{ $p['jenis_kp'] }}</div>
                                                <div class="text-[11px] text-gray-500">{{ $p['instansi'] }}</div>
                                            </td>
                                            <td rowspan="{{ $rowspan }}" class="border-r border-[#CAC0C0] px-4 py-2 align-middle text-center">{{ $p['supervisor'] }}</td>
                                        @endif
                                        
                                        <!-- Judul KP for each Mahasiswa -->
                                        <td class="border-r border-[#CAC0C0] px-4 py-2 align-middle text-center max-w-[180px] break-words" title="{{ $mhs['judul_kp'] }}">{{ Str::limit($mhs['judul_kp'], 50) }}</td>

                                        @if($mIndex === 0)
                                            <!-- Dosen Plotting Column -->
                                            <td rowspan="{{ $rowspan }}" class="border-r border-[#CAC0C0] px-4 py-2 align-middle">
                                                <div class="relative w-full max-w-[200px] mx-auto" :class="openDropdown === '{{ $p['id'] }}' ? 'z-50' : 'z-0'" @click.outside="closeDropdown('{{ $p['id'] }}')">
                                                    <input type="hidden" name="assignments[{{ $p['id'] }}]" :value="assignments['{{ $p['id'] }}']" form="form-assignment">
                                                    
                                                    <!-- Dropdown Trigger button mimicking table cell look -->
                                                    <button type="button" @click="$event.stopPropagation(); triggerDropdown('{{ $p['id'] }}')" 
                                                            class="w-full py-1.5 px-3 border border-gray-400 bg-white rounded text-[12px] font-medium flex items-center justify-between transition-colors shadow-sm focus:outline-none"
                                                            :class="assignments['{{ $p['id'] }}'] !== '' ? 'text-gray-900 border-gray-400' : 'text-gray-500 border-dashed'">
                                                        
                                                        <span class="truncate pr-2 text-center flex-1" x-text="getDosenName(assignments['{{ $p['id'] }}'])"></span>
                                                        <span x-show="assignments['{{ $p['id'] }}'] === ''" class="shrink-0 text-[10px]">&#9660;</span>
                                                    </button>

                                                    <!-- Dropdown Menu -->
                                                    <div x-show="openDropdown === '{{ $p['id'] }}'" x-transition 
                                                         class="absolute z-[9999] mt-1 top-full right-0 min-w-max bg-white border border-[#CAC0C0] rounded shadow-xl overflow-hidden text-left" style="display: none;">
                                                        
                                                        <!-- Ghost elements to permanently reserve max width -->
                                                        <div class="h-0 overflow-hidden invisible pointer-events-none flex flex-col" aria-hidden="true">
                                                            <template x-for="dosen in currentLoads" :key="'ghost-'+dosen.id">
                                                                <div class="px-3 py-1.5 flex justify-between gap-6 text-[13px] font-bold">
                                                                    <span class="whitespace-nowrap" x-text="dosen.nama"></span>
                                                                    <span>(9)</span>
                                                                </div>
                                                            </template>
                                                        </div>

                                                        <div class="p-1.5 border-b border-gray-200 bg-gray-50 flex items-center gap-2">
                                                            <svg class="w-4 h-4 text-gray-400 ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                                                            <input type="text" x-model="searchDosen" class="w-full bg-transparent text-[13px] outline-none text-gray-700 py-1" placeholder="Cari Dosen...">
                                                        </div>
                                                        <ul class="py-1 text-[13px] max-h-[220px] overflow-y-auto custom-scrollbar bg-white">
                                                            <li x-show="assignments['{{ $p['id'] }}'] !== ''">
                                                                <button type="button" @click.prevent="setAssignment('{{ $p['id'] }}', '')" class="w-full text-left px-3 py-2 font-semibold text-red-600 hover:bg-red-50 flex items-center gap-2 border-b border-gray-100 whitespace-nowrap">
                                                                    Batalkan Pilihan
                                                                </button>
                                                            </li>
                                                            
                                                            <template x-for="dosen in filteredDosenList" :key="dosen.id">
                                                                <li>
                                                                    <button type="button" @click.prevent="setAssignment('{{ $p['id'] }}', dosen.id)" 
                                                                            class="w-full text-left px-3 py-1.5 flex justify-between items-center transition-colors"
                                                                            :class="[
                                                                                assignments['{{ $p['id'] }}'] == dosen.id ? 'bg-blue-50 font-bold text-gray-900 pointer-events-none' : 'hover:bg-gray-100',
                                                                                Number(dosen.beban) >= Number(dosen.kuota) && assignments['{{ $p['id'] }}'] != dosen.id ? 'opacity-50 cursor-not-allowed text-gray-400' : 'text-gray-700'
                                                                            ]"
                                                                            :disabled="Number(dosen.beban) >= Number(dosen.kuota) && assignments['{{ $p['id'] }}'] != dosen.id">
                                                                        <span class="whitespace-nowrap pr-4" x-text="dosen.nama"></span>
                                                                        <span class="shrink-0 font-bold ml-auto text-right whitespace-nowrap" :class="Number(dosen.beban) >= Number(dosen.kuota) ? 'text-red-500' : 'text-gray-500'" x-text="'(' + dosen.beban + ')'"></span>
                                                                    </button>
                                                                </li>
                                                            </template>
                                                        </ul>
                                                    </div>
                                                </div>
                                            </td>

                                            <!-- Status Label -->
                                            <td rowspan="{{ $rowspan }}" class="border-r border-[#CAC0C0] px-4 py-2 align-middle text-center">
                                                <div x-show="assignments['{{ $p['id'] }}'] !== ''" class="inline-flex items-center justify-center gap-1.5 bg-[#A1DFAC] text-[#1D5E2D] px-4 py-1.5 rounded-[20px] shadow-sm font-bold w-[110px] text-[11px]">
                                                    <div class="w-2.5 h-2.5 rounded-full bg-[#1D5E2D]"></div>
                                                    Ditugaskan
                                                </div>
                                                <div x-show="assignments['{{ $p['id'] }}'] === ''" style="display: none;" class="inline-flex items-center justify-center gap-1.5 bg-[#FDE293] text-[#A67C00] px-4 py-1.5 rounded-[20px] shadow-sm font-bold w-[110px] text-[11px]">
                                                    <div class="w-2.5 h-2.5 rounded-full bg-[#A67C00]"></div>
                                                    Menunggu
                                                </div>
                                            </td>

                                            <!-- Aksi / Detail  -->
                                            <td rowspan="{{ $rowspan }}" class="px-4 py-2 align-middle text-center">
                                                <div class="flex justify-center w-full">
                                                    <a href="{{ route('koordinator.penugasan-pembimbing.detail', ['slug' => $p['slug']]) }}" data-turbo-frame="_top" class="inline-block bg-[#4285F4] hover:bg-blue-600 text-white px-4 py-1.5 rounded-[20px] shadow-sm text-[11px] font-semibold transition-colors text-center w-[80px]">Detail</a>
                                                </div>
                                            </td>
                                        @endif
                                    </tr>
                                @endforeach
                            @empty
                                <tr>
                                    <td colspan="8" class="border border-[#CAC0C0] px-4 py-16 text-center bg-white">
                                        <div class="flex flex-col items-center justify-center text-gray-400">
                                            <svg class="w-12 h-12 mb-3 opacity-60" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                            <p class="text-[14px] font-medium text-gray-500">Belum ada mahasiswa yang Butuh Plotting.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Footer Pagination -->
                <div class="flex items-center gap-2 text-[12px] font-medium text-gray-600 justify-end w-full border border-[#CAC0C0] bg-white px-2 py-1.5 rounded shadow-sm">
                    @if(method_exists($paginator, 'hasPages'))
                        <span class="mr-4 text-[13px]">{{ $paginator->firstItem() ?? 0 }} - {{ $paginator->lastItem() ?? 0 }} of {{ $paginator->total() }} entries</span>
                        @if ($paginator->hasPages())
                            <div class="flex overflow-hidden">
                                @if ($paginator->onFirstPage())
                                    <span class="px-3 py-1 text-gray-400 cursor-not-allowed">&lt;</span>
                                @else
                                    <a href="{{ $paginator->appends(request()->query())->previousPageUrl() }}" class="px-3 py-1 hover:bg-gray-100 transition-colors">&lt;</a>
                                @endif
                                
                                @php
                                    $start = max($paginator->currentPage() - 2, 1);
                                    $end = min($start + 4, $paginator->lastPage());
                                    if ($end - $start < 4) {
                                        $start = max($end - 4, 1);
                                    }
                                @endphp
                                @for ($i = $start; $i <= $end; $i++)
                                    @if ($i == $paginator->currentPage())
                                        <span class="px-3 py-1 bg-[#4285F4] text-white font-bold rounded-sm mx-1">{{ $i }}</span>
                                    @else
                                        <a href="{{ $paginator->appends(request()->query())->url($i) }}" class="px-3 py-1 hover:bg-gray-100 transition-colors mx-0.5 text-gray-700 font-medium">{{ $i }}</a>
                                    @endif
                                @endfor

                                @if ($paginator->hasMorePages())
                                    <a href="{{ $paginator->appends(request()->query())->nextPageUrl() }}" class="px-3 py-1 hover:bg-gray-100 transition-colors">&gt;</a>
                                @else
                                    <span class="px-3 py-1 text-gray-400 cursor-not-allowed">&gt;</span>
                                @endif
                            </div>
                        @endif
                    @else
                        <span class="mr-4 text-[13px]">1 - {{ count($pendaftarans) }} of {{ count($pendaftarans) }} entries</span>
                    @endif
                </div>
                </turbo-frame>
                
            </div>
    </div>

    <!-- AlpineJS Controller Injection -->
    @php
        $assignmentsInit = [];
        $groupSizesInit = [];
        foreach($pendaftarans as $p) {
            $assignmentsInit[$p['id']] = $p['dosen_id'] ?? '';
            $groupSizesInit[$p['id']] = count($p['mahasiswas']);
        }
        
        $dosenDataArray = collect($dosenList)->map(function($d) {
            return [
                'id' => $d['id'],
                'nama' => $d['nama'],
                'beban_awal' => $d['beban'],
                'kuota' => $d['kuota']
            ];
        })->keyBy('id')->toArray();
    @endphp
    <script>
        function penugasanController() {
            return {
                baseDosenData: @json($dosenDataArray),
                assignments: @json($assignmentsInit),
                initialAssignments: @json($assignmentsInit),
                groupSizes: @json($groupSizesInit),
                isDirty: false,
                openDropdown: null,
                searchDosen: '',

                init() {
                    window.addEventListener('update-assignments', (e) => {
                        let newAssignments = e.detail.assignments;
                        let newGroupSizes = e.detail.groupSizes;
                        for (let key in newAssignments) {
                            if (this.assignments[key] === undefined) {
                                this.assignments[key] = newAssignments[key];
                                this.initialAssignments[key] = newAssignments[key];
                                this.groupSizes[key] = newGroupSizes[key];
                            }
                        }
                    });

                    window.addEventListener('beforeunload', (e) => {
                        if (this.isDirty) {
                            e.preventDefault();
                            e.returnValue = '';
                        }
                    });
                },

                // Compute real-time Dosen Loads
                get currentLoads() {
                    let loads = {};
                    for(let id in this.baseDosenData) {
                        loads[id] = { 
                            id: this.baseDosenData[id].id,
                            nama: this.baseDosenData[id].nama, 
                            beban: this.baseDosenData[id].beban_awal,
                            kuota: this.baseDosenData[id].kuota 
                        };
                    }

                    for(let pid in this.assignments) {
                        let currentDosen = this.assignments[pid];
                        let initialDosen = this.initialAssignments[pid];
                        let size = this.groupSizes[pid];

                        if (currentDosen !== initialDosen) {
                            if (initialDosen !== '' && loads[initialDosen]) {
                                loads[initialDosen].beban -= size;
                            }
                            if (currentDosen !== '' && loads[currentDosen]) {
                                loads[currentDosen].beban += size;
                            }
                        }
                    }
                    
                    return Object.values(loads).sort((a, b) => a.nama.localeCompare(b.nama));
                },

                // Compute Header Stats
                get stats() {
                    let ditugaskan = {{ $ditugaskanCount }};
                    let menunggu = {{ $menungguCount }};

                    for(let pid in this.assignments) {
                        let size = this.groupSizes[pid] || 1;
                        let currentDosen = this.assignments[pid];
                        let initialDosen = this.initialAssignments[pid];

                        if (currentDosen !== initialDosen) {
                            if (currentDosen !== '' && initialDosen === '') {
                                ditugaskan += size;
                                menunggu -= size;
                            } else if (currentDosen === '' && initialDosen !== '') {
                                ditugaskan -= size;
                                menunggu += size;
                            }
                        }
                    }
                    return { ditugaskan, menunggu };
                },

                get filteredDosenList() {
                    let list = this.currentLoads;
                    if(this.searchDosen.trim() !== '') {
                        let query = this.searchDosen.toLowerCase();
                        list = list.filter(d => d.nama.toLowerCase().includes(query));
                    }
                    return list;
                },

                getDosenName(dosenId) {
                    if(!dosenId || dosenId === '') return 'Tugaskan';
                    return this.baseDosenData[dosenId] ? this.baseDosenData[dosenId].nama : 'Unknown';
                },

                triggerDropdown(pId) {
                    if(this.openDropdown === pId) {
                        this.openDropdown = null;
                    } else {
                        this.openDropdown = pId;
                        this.searchDosen = ''; 
                    }
                },

                closeDropdown(pId) {
                    if(this.openDropdown === pId) {
                        this.openDropdown = null;
                    }
                },

                setAssignment(pId, dosenId) {
                    this.assignments[pId] = dosenId;
                    this.isDirty = true;
                    this.closeDropdown(pId);
                }
            };
        }
    </script>
</x-dashboard-layout>
