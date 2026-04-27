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
                <button type="button" @click="autoPlot()" :disabled="isLoadingAuto" class="bg-[#4285F4] hover:bg-blue-600 font-bold text-white rounded-[5px] px-6 py-2 text-[13px] flex items-center justify-center gap-2 shadow-sm w-full lg:w-[120px] transition-colors disabled:opacity-50" title="Bagi rata beban dosen pada mahasiswa yang belum ditugaskan">
                    <svg x-show="!isLoadingAuto" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" /></svg>
                    <div x-show="isLoadingAuto" class="animate-spin rounded-full h-4 w-4 border-b-2 border-white" style="display: none;"></div>
                    <span x-text="isLoadingAuto ? 'Proses...' : 'Auto'"></span>
                </button>
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
                <div class="flex gap-3">
                    <button type="button" @click="submitForm()" 
                        class="text-white font-bold py-2 px-8 rounded-[5px] shadow-md flex items-center gap-2 transition-all h-[42px] text-[14px] whitespace-nowrap border"
                        :class="isDirty ? 'bg-[#1A73E8] hover:bg-[#1557B0] border-[#1A73E8] animate-pulse' : 'bg-[#34A853] hover:bg-[#2B8A44] border-[#34A853]'">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                        <span x-text="isDirty ? 'SUBMIT' : 'TELAH DISUBMIT'"></span>
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
                
                <form action="{{ route('koordinator.penugasan-pembimbing') }}" method="GET" data-turbo-frame="table-data" class="bg-white p-4 rounded-[10px] border border-gray-200 shadow-sm mb-6" id="filter-form" x-data="{ submitForm() { document.getElementById('hidden-filter-submit').click(); } }">
                    <button type="submit" id="hidden-filter-submit" class="hidden"></button>
                    
                    <div class="flex flex-col gap-4">
                        <div class="relative w-full">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="w-5 h-5 text-gray-400 font-bold" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                            </div>
                            <input id="search-input" type="text" name="search" value="{{ request('search') }}" x-on:input.debounce.500ms="submitForm()" @clear-filters.window="$el.value = ''" placeholder="Cari Nama Mahasiswa atau NIM..." 
                                class="block w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-[5px] text-[13px] text-black focus:outline-none focus:ring-1 focus:ring-blue-500 shadow-sm">
                        </div>
                        
                        <div class="flex flex-wrap items-center justify-between gap-4 w-full">
                            <div class="flex flex-wrap items-center gap-3">
                                <div class="flex items-center gap-2 shrink-0">
                                    <label class="text-[13px] font-bold text-black whitespace-nowrap">Jenis KP:</label>
                                    <select name="jenis_kp" class="w-[120px] text-[13px] border border-gray-300 rounded-[5px] py-2 px-3 bg-white text-black font-medium focus:outline-none focus:ring-1 focus:ring-blue-500 shadow-sm" @change="submitForm()">
                                        <option value="All" {{ request('jenis_kp') == 'All' ? 'selected' : '' }}>All</option>
                                        <option value="Internal" {{ request('jenis_kp') == 'Internal' ? 'selected' : '' }}>Internal</option>
                                        <option value="Eksternal" {{ request('jenis_kp') == 'Eksternal' ? 'selected' : '' }}>Eksternal</option>
                                    </select>
                                </div>

                                <div class="flex items-center gap-2 shrink-0">
                                    <label class="text-[13px] font-bold text-black whitespace-nowrap">Pengerjaan:</label>
                                    <select name="pengerjaan" class="w-[130px] text-[13px] border border-gray-300 rounded-[5px] py-2 px-3 bg-white text-black font-medium focus:outline-none focus:ring-1 focus:ring-blue-500 shadow-sm" @change="submitForm()">
                                        <option value="All" {{ request('pengerjaan') == 'All' ? 'selected' : '' }}>All</option>
                                        <option value="Individu" {{ request('pengerjaan') == 'Individu' ? 'selected' : '' }}>Individu</option>
                                        <option value="Berkelompok" {{ request('pengerjaan') == 'Berkelompok' ? 'selected' : '' }}>Berkelompok</option>
                                    </select>
                                </div>

                                <div class="flex items-center gap-2 shrink-0">
                                    <label class="text-[13px] font-bold text-black whitespace-nowrap">Status:</label>
                                    <select name="status_filter" class="w-[130px] text-[13px] border border-gray-300 rounded-[5px] py-2 px-3 bg-white text-black font-medium focus:outline-none focus:ring-1 focus:ring-blue-500 shadow-sm" @change="submitForm()">
                                        <option value="All" {{ request('status_filter') == 'All' ? 'selected' : '' }}>Semua Status</option>
                                        <option value="Menunggu" {{ request('status_filter') == 'Menunggu' ? 'selected' : '' }}>Menunggu</option>
                                        <option value="Ditugaskan" {{ request('status_filter') == 'Ditugaskan' ? 'selected' : '' }}>Ditugaskan</option>
                                    </select>
                                </div>

                                <div class="flex items-center gap-2 shrink-0" x-data="{ 
                                    openDosen: false, 
                                    searchDosenText: '',
                                    selectedDosenId: '{{ request('dosen_pembimbing', 'All') }}',
                                    selectedDosenName: '{{ request('dosen_pembimbing') == 'Belum Ditugaskan' ? 'Belum Ditugaskan' : (request('dosen_pembimbing') && request('dosen_pembimbing') != 'All' ? addslashes(collect($dosenList)->where('id', request('dosen_pembimbing'))->first()['nama'] ?? 'All') : 'All') }}'
                                }" @clear-filters.window="selectedDosenId = 'All'; selectedDosenName = 'All'; searchDosenText = ''">
                                    <label class="text-[13px] font-bold text-black whitespace-nowrap">Pembimbing:</label>
                                    <div class="relative w-[180px]">
                                        <button type="button" @click="openDosen = !openDosen" @click.outside="openDosen = false" 
                                            class="w-full flex justify-between items-center bg-white border border-gray-300 rounded-[5px] px-3 py-2 text-[13px] font-medium shadow-sm text-black focus:outline-none focus:ring-1 focus:ring-blue-500">
                                            <span class="truncate w-full text-left pr-2" x-text="selectedDosenName"></span>
                                            <svg :class="openDosen ? 'rotate-180' : 'rotate-0'" class="w-3.5 h-3.5 flex-shrink-0 transition-transform duration-200 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                        </button>
                                        <div x-show="openDosen" x-transition style="display: none;" class="absolute z-50 w-[240px] right-0 sm:right-auto mt-1 bg-white border border-gray-300 rounded-[5px] shadow-lg flex flex-col overflow-hidden">
                                            <div class="px-2 py-2 sticky top-0 bg-[#F5F6F8] border-b border-gray-200 z-10 w-full shrink-0">
                                                <input type="text" x-model="searchDosenText" @click.stop class="w-full border border-gray-300 rounded-[3px] px-2 py-1.5 text-[12px] focus:outline-none focus:ring-1 focus:ring-blue-500" placeholder="Ketik nama dosen...">
                                            </div>
                                            <ul class="py-1 text-[13px] max-h-[200px] overflow-y-auto overflow-x-hidden custom-scrollbar flex-1 w-full scale-100 text-black">
                                                <li x-show="'all'.includes(searchDosenText.toLowerCase())"><label class="block px-3 py-1.5 hover:bg-gray-100 cursor-pointer"><input type="radio" name="dosen_pembimbing" value="All" class="hidden" x-model="selectedDosenId" @change="selectedDosenName = 'All'; openDosen = false; submitForm()">All</label></li>
                                                <li x-show="'belum ditugaskan'.includes(searchDosenText.toLowerCase())"><label class="block px-3 py-1.5 hover:bg-gray-100 cursor-pointer"><input type="radio" name="dosen_pembimbing" value="Belum Ditugaskan" class="hidden" x-model="selectedDosenId" @change="selectedDosenName = 'Belum Ditugaskan'; openDosen = false; submitForm()">Belum Ditugaskan</label></li>
                                                @foreach($dosenList as $dl)
                                                    <li x-show="'{{ strtolower(addslashes($dl['nama'])) }}'.includes(searchDosenText.toLowerCase())"><label class="block px-3 py-1.5 hover:bg-gray-100 cursor-pointer text-[12px]"><input type="radio" name="dosen_pembimbing" value="{{ $dl['id'] }}" class="hidden" x-model="selectedDosenId" @change="selectedDosenName = '{{ addslashes($dl['nama']) }}'; openDosen = false; submitForm()">{{ $dl['nama'] }}</label></li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    </div>
                                </div>

                                <button type="button" @click="$dispatch('clear-filters'); setTimeout(() => submitForm(), 50)" class="bg-[#757575] hover:bg-[#616161] text-white font-bold text-[12px] px-5 py-2.5 rounded-[5px] shadow-sm transition-colors whitespace-nowrap border border-[#616161]">
                                    Clear Filter
                                </button>
                            </div>
                            
                            <div class="flex items-center gap-3 ml-auto">
                                <button @click="resetPlotting()" type="button" class="bg-[#D32F2F] hover:bg-[#B71C1C] text-white font-bold py-2.5 px-6 rounded-[5px] shadow-md flex items-center gap-2 transition-colors text-[13px] whitespace-nowrap border border-[#B71C1C]">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                                    Reset Penugasan
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
                
                <form id="form-assignment" method="POST" action="{{ route('koordinator.penugasan-pembimbing.store') }}" class="hidden">
                    @csrf
                </form>

                <turbo-frame id="table-data">
                <div class="bg-white border border-gray-200 rounded-[10px] overflow-hidden shadow-sm mb-12">
                    <div class="overflow-x-auto custom-scrollbar">
                        <table class="w-full min-w-[1000px] border-collapse text-[12px] text-center">
                            <thead class="bg-[#EBEBEB] font-bold text-black h-[45px]">
                                <tr>
                                    <th class="border-b border-r border-gray-300 px-4 py-3 w-[40px]">No</th>
                                    <th class="border-b border-r border-gray-300 px-4 py-3">Mahasiswa</th>
                                    <th class="border-b border-r border-gray-300 px-4 py-3">Jenis KP</th>
                                    <th class="border-b border-r border-gray-300 px-4 py-3">Supervisor</th>
                                    <th class="border-b border-r border-gray-300 px-4 py-3">Judul KP</th>
                                    <th class="border-b border-r border-gray-300 px-4 py-3 min-w-[200px]">Dosen Pembimbing</th>
                                    <th class="border-b border-r border-gray-300 px-4 py-3 min-w-[120px]">Status</th>
                                    <th class="border-b border-gray-300 px-4 py-3 w-[80px]">Detail KP</th>
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
                            @php $noCounter = $startNumber; @endphp
                            
                            @forelse($pendaftarans as $p)
                                @php 
                                    $mhsList = $p['mahasiswas'];
                                    $rowspan = count($mhsList); 
                                @endphp
                                
                                @foreach($mhsList as $mIndex => $mhs)
                                    <tr class="hover:bg-gray-50 border-b border-gray-200 font-medium transition-colors" :class="openDropdown === '{{ $p['id'] }}' ? 'relative z-[50]' : ''">
                                        
                                        <!-- Index -->
                                        <td class="border-r border-gray-200 px-4 py-4 text-center font-bold text-gray-700 align-middle">{{ $noCounter++ }}</td>
                                        
                                        <!-- Mahasiswa Data Stacked nicely -->
                                        <td class="border-r border-gray-200 px-4 py-2 align-middle">
                                            <div class="font-bold text-[12px] text-gray-800 leading-snug">{{ $mhs['nama'] }}</div>
                                            <div class="text-[11px] text-gray-500">{{ $mhs['nim'] }}</div>
                                        </td>

                                        <!-- The common Group Info -->
                                        @if($mIndex === 0)
                                            <td rowspan="{{ $rowspan }}" class="border-r border-gray-200 px-4 py-2 align-middle text-center">
                                                <div class="font-bold text-[12px] text-gray-800 leading-snug">{{ $p['jenis_kp'] }}</div>
                                                <div class="text-[11px] text-gray-500">{{ $p['instansi'] }}</div>
                                            </td>
                                            <td rowspan="{{ $rowspan }}" class="border-r border-gray-200 px-4 py-2 align-middle text-center">{{ $p['supervisor'] }}</td>
                                        @endif
                                        
                                        <!-- Judul KP for each Mahasiswa -->
                                        <td class="border-r border-gray-200 px-4 py-2 align-middle text-center max-w-[180px] break-words" title="{{ $mhs['judul_kp'] }}">{{ Str::limit($mhs['judul_kp'], 50) }}</td>

                                        @if($mIndex === 0)
                                            <!-- Dosen Plotting Column -->
                                            <td rowspan="{{ $rowspan }}" class="border-r border-gray-200 px-4 py-2 align-middle">
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
                                                         class="absolute z-[9999] mt-1 top-full right-0 min-w-max bg-white border-2 border-[#A8A8A8] rounded-md shadow-[0_8px_30px_rgb(0,0,0,0.2)] ring-1 ring-black/5 overflow-hidden text-left" style="display: none;">
                                                        
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
                                            <td rowspan="{{ $rowspan }}" class="border-r border-gray-200 px-4 py-2 align-middle text-center">
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
                                    <td colspan="8" class="border border-gray-200 px-4 py-16 text-center bg-white">
                                        <div class="flex flex-col items-center justify-center text-gray-400">
                                            <svg class="w-12 h-12 mb-3 opacity-60" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                            <p class="text-[14px] font-medium text-gray-500">Belum ada mahasiswa yang Butuh Plotting.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                    
                    <!-- Footer Pagination -->
                    <div class="px-6 py-4 bg-white border-t border-gray-200 flex items-center justify-between">
                        @if(method_exists($paginator, 'hasPages'))
                            <span class="text-[12px] font-medium text-black/50">{{ $startNumber }} - {{ $endNumber }} dari {{ $filteredMahasiswaCount }} baris</span>
                            @if ($paginator->hasPages())
                                <div class="flex items-center gap-2">
                                    @if ($paginator->onFirstPage())
                                        <button disabled class="px-3 py-1 border border-gray-300 rounded text-[12px] opacity-30 cursor-not-allowed">Previous</button>
                                    @else
                                        <a href="{{ $paginator->appends(request()->query())->previousPageUrl() }}" class="px-3 py-1 border border-gray-300 rounded text-[12px] hover:bg-gray-50 transition-colors">Previous</a>
                                    @endif
                                    
                                    <div class="flex items-center gap-1">
                                    @php
                                        $start = max($paginator->currentPage() - 2, 1);
                                        $end = min($start + 4, $paginator->lastPage());
                                        if ($end - $start < 4) {
                                            $start = max($end - 4, 1);
                                        }
                                    @endphp
                                    @for ($i = $start; $i <= $end; $i++)
                                        @if ($i == $paginator->currentPage())
                                            <span class="w-8 h-8 rounded text-[12px] font-bold bg-blue-600 text-white shadow-md flex items-center justify-center">{{ $i }}</span>
                                        @else
                                            <a href="{{ $paginator->appends(request()->query())->url($i) }}" class="w-8 h-8 rounded text-[12px] font-bold text-black hover:bg-gray-100 flex items-center justify-center transition-all">{{ $i }}</a>
                                        @endif
                                    @endfor
                                    </div>

                                    @if ($paginator->hasMorePages())
                                        <a href="{{ $paginator->appends(request()->query())->nextPageUrl() }}" class="px-3 py-1 border border-gray-300 rounded text-[12px] hover:bg-gray-50 transition-colors">Next</a>
                                    @else
                                        <button disabled class="px-3 py-1 border border-gray-300 rounded text-[12px] opacity-30 cursor-not-allowed">Next</button>
                                    @endif
                                </div>
                            @endif
                        @else
                            <span class="text-[12px] font-medium text-black/50">{{ $startNumber }} - {{ $endNumber }} dari {{ $filteredMahasiswaCount }} Mahasiswa</span>
                        @endif
                    </div>
                </div>
                </turbo-frame>
                
            </div>

        <!-- Custom Global Confirm Modal -->
        <div x-cloak x-show="confirmDialog.show" style="display: none;" class="fixed inset-0 z-[1000] flex items-center justify-center bg-black/40 backdrop-blur-sm p-4" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95">
            <div @click.away="confirmDialog.show = false" class="bg-white rounded-[15px] w-full max-w-[420px] p-8 shadow-2xl flex flex-col items-center text-center relative overflow-hidden border border-gray-100">
                
                <!-- Icon Header Based on Type -->
                <div class="mb-6">
                    <template x-if="confirmDialog.type === 'danger'">
                        <div class="w-20 h-20 bg-red-50 rounded-full flex items-center justify-center">
                            <svg class="w-12 h-12 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                        </div>
                    </template>
                    <template x-if="confirmDialog.type === 'info'">
                        <div class="w-20 h-20 bg-blue-50 rounded-full flex items-center justify-center">
                            <svg class="w-12 h-12 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                    </template>
                </div>

                <h3 class="text-[18px] font-bold text-gray-900 mb-3" x-text="confirmDialog.title"></h3>
                <p class="text-[14px] text-gray-500 mb-8 leading-relaxed px-2" x-text="confirmDialog.message"></p>

                <div class="flex gap-4 w-full">
                    <button @click="confirmDialog.show = false" type="button" class="flex-1 h-[45px] bg-gray-50 hover:bg-gray-100 text-gray-700 rounded-[10px] text-[14px] font-bold transition-all border border-gray-200">
                        Batal
                    </button>
                    <button @click="executeConfirm()" type="button" 
                        class="flex-1 h-[45px] text-white rounded-[10px] text-[14px] font-bold transition-all shadow-md active:transform active:scale-95"
                        :class="[
                            confirmDialog.type === 'danger' ? 'bg-[#E53935] hover:bg-red-700' : '',
                            confirmDialog.type === 'info' ? 'bg-[#4285F4] hover:bg-blue-700' : ''
                        ]"
                        x-text="confirmDialog.confirmText">
                    </button>
                </div>
            </div>
        </div>

    </div>

    <!-- AlpineJS Controller Injection -->
    @php
        $assignmentsInit = [];
        foreach($pendaftarans as $p) {
            $assignmentsInit[$p['id']] = $p['dosen_id'] ?? '';
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
                groupSizes: @json($allGroupSizes),
                isDirty: false,
                isLoadingAuto: false,
                openDropdown: null,
                searchDosen: '',
                confirmDialog: { show: false, title: '', message: '', type: 'danger', confirmText: 'Iya, Lanjutkan', callback: null },

                init() {
                    window.addEventListener('update-assignments', (e) => {
                        let newAssignments = e.detail.assignments;
                        let newGroupSizes = e.detail.groupSizes;
                        
                        let tempAssignments = { ...this.assignments };
                        let tempInitial = { ...this.initialAssignments };
                        let tempGroups = { ...this.groupSizes };

                        for (let key in newAssignments) {
                            if (tempAssignments[key] === undefined) {
                                tempAssignments[key] = newAssignments[key];
                                tempInitial[key] = newAssignments[key];
                                tempGroups[key] = newGroupSizes[key];
                            }
                        }
                        
                        this.assignments = tempAssignments;
                        this.initialAssignments = tempInitial;
                        this.groupSizes = tempGroups;
                    });

                    window.addEventListener('beforeunload', (e) => {
                        if (this.isDirty) {
                            e.preventDefault();
                            e.returnValue = '';
                        }
                    });
                },

                triggerConfirm(options) {
                    this.confirmDialog = {
                        show: true,
                        title: options.title || 'Konfirmasi Aksi',
                        message: options.message || 'Apakah Anda yakin ingin melanjutkan?',
                        type: options.type || 'danger',
                        confirmText: options.confirmText || 'Iya, Lanjutkan',
                        callback: options.callback || null
                    };
                },

                executeConfirm() {
                    if (this.confirmDialog.callback) {
                        this.confirmDialog.callback();
                    }
                    this.confirmDialog.show = false;
                },

                async autoPlot() {
                    let total = {{ $menungguCount }};
                    this.triggerConfirm({
                        title: 'Otomatiskan Penugasan',
                        message: 'Sistem akan membagi rata dosen pembimbing kepada ' + total + ' mahasiswa yang belum memiliki dosen. Hasil plotting ini hanya akan disimpan sebagai draft sementara sampai Anda menekan tombol Submit. Lanjutkan?',
                        type: 'info',
                        confirmText: 'Lanjutkan',
                        callback: async () => {
                            this.isLoadingAuto = true;
                            try {
                                const res = await fetch("{{ route('koordinator.penugasan-pembimbing.auto') }}", {
                                    method: "POST", headers: { "X-CSRF-TOKEN": "{{ csrf_token() }}", "Accept": "application/json" }
                                });
                                const result = await res.json();
                                if (result.success) {
                                    for (let key in result.assignments) {
                                        this.assignments[key] = result.assignments[key];
                                    }
                                    this.isDirty = true;
                                } else {
                                    alert('Error: ' + result.message);
                                }
                            } catch (e) { alert('Terjadi kesalahan sistem.'); }
                            finally { this.isLoadingAuto = false; }
                        }
                    });
                },

                resetPlotting() {
                    this.triggerConfirm({
                        title: 'Reset Semua Penugasan',
                        message: 'Tindakan ini akan membatalkan SEMUA penugasan dosen pembimbing untuk seluruh mahasiswa. Anda harus melakukan plotting ulang dari awal. Apakah Anda yakin?',
                        type: 'danger',
                        confirmText: 'Ya, Kosongkan',
                        callback: () => {
                            let form = document.createElement('form');
                            form.method = 'POST';
                            form.action = "{{ route('koordinator.penugasan-pembimbing.reset') }}";
                            let csrf = document.createElement('input');
                            csrf.type = 'hidden';
                            csrf.name = '_token';
                            csrf.value = "{{ csrf_token() }}";
                            form.appendChild(csrf);
                            document.body.appendChild(form);
                            this.isDirty = false;
                            form.submit();
                        }
                    });
                },

                submitForm() {
                    if (!this.isDirty) return;
                    
                    let form = document.createElement('form');
                    form.method = 'POST';
                    form.action = "{{ route('koordinator.penugasan-pembimbing.store') }}";
                    
                    let csrf = document.createElement('input');
                    csrf.type = 'hidden';
                    csrf.name = '_token';
                    csrf.value = "{{ csrf_token() }}";
                    form.appendChild(csrf);

                    // Hanya submit yang dirty atau initial agar validasi backend sukses
                    for (let key in this.assignments) {
                        let input = document.createElement('input');
                        input.type = 'hidden';
                        input.name = `assignments[${key}]`;
                        input.value = this.assignments[key] || '';
                        form.appendChild(input);
                    }

                    document.body.appendChild(form);
                    this.isDirty = false;
                    form.submit();
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

                    let keys = Object.keys(this.assignments);
                    for(let pid of keys) {
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

                get stats() {
                    let ditugaskan = 0;
                    
                    for(let pid in this.assignments) {
                        if (this.assignments[pid] !== '') {
                            ditugaskan += this.groupSizes[pid] || 1;
                        }
                    }
                    
                    let menunggu = {{ $totalMahasiswa }} - ditugaskan;
                    return { ditugaskan, menunggu: menunggu > 0 ? menunggu : 0 };
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
