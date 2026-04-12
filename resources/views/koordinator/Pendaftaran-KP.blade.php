<x-dashboard-layout header="Pendaftaran KP" userName="{{ auth()->user()->name ?? 'KOORDINATOR KP' }}" roleName="KOORDINATOR KP">
    <x-slot:sidebar>
        @include('koordinator.components.sidebar', ['active' => 'pendaftaran-kp'])
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

    <style>
        .counter-body { counter-reset: row-number {{ ($pendaftarans->currentPage() - 1) * $pendaftarans->perPage() }}; }
        .data-row:not([style*="display: none"]) .row-number-cell::before {
            counter-increment: row-number;
            content: counter(row-number);
        }
    </style>

    <div class="mt-8 px-2 w-full max-w-[1200px] mx-auto" x-data="{ 
        searchQuery: '', 
        isSelectionMode: sessionStorage.getItem('kpSelectionMode') === 'true',
        modalCatatanOpen: false,
        modalFormEl: null,
        modalPesan: '',
        modalCatatanValue: '',
        openModalCatatan(formElement, pesan) {
            this.modalFormEl = formElement;
            this.modalPesan = pesan;
            this.modalCatatanValue = '';
            this.modalCatatanOpen = true;
        },
        submitModalCatatan() {
            let input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'catatan';
            input.value = this.modalCatatanValue;
            this.modalFormEl.appendChild(input);
            this.modalFormEl.submit();
        }
    }">
        
        <!-- Modal Catatan Mengambang (Opsional) -->
        <div x-show="modalCatatanOpen" style="display: none;" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-40">
            <div @click.outside="modalCatatanOpen = false" class="bg-white rounded-[5px] shadow-xl w-[400px] p-6 text-center transform inline-block">
                <h3 class="text-xl font-bold text-gray-800 mb-2 truncate" x-text="modalPesan"></h3>
                <p class="text-[13px] text-gray-500 mb-4 leading-snug">Silakan buat catatan khusus (opsional) atau kosongkan apabila tidak ada.</p>
                <textarea x-model="modalCatatanValue" class="w-full border border-gray-300 rounded-[5px] p-3 text-[13px] focus:outline-none focus:border-[#4285F4] mb-5 resize-none h-[100px]" placeholder="Ketik catatan di sini..."></textarea>
                <div class="flex justify-center gap-3">
                    <button @click="modalCatatanOpen = false" type="button" class="px-6 py-2 rounded-[5px] bg-gray-200 text-gray-700 font-bold text-[13px] hover:bg-gray-300 transition-colors">Batal</button>
                    <button @click="submitModalCatatan()" type="button" class="px-6 py-2 rounded-[5px] text-white font-bold text-[13px] transition-colors bg-[#4285F4] hover:bg-blue-600">Simpan & Proses</button>
                </div>
            </div>
        </div>
        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4 shadow-sm" role="alert">
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
        @endif

        <div class="bg-[#E6F0FA] border border-[#D0E3F5] rounded-[10px] p-4 lg:p-5 mb-6 flex items-start gap-4 shadow-sm">
            <div class="w-6 h-6 rounded-full bg-[#4285F4] text-white flex items-center justify-center font-bold flex-shrink-0 mt-0.5">
                i
            </div>
            <p class="text-[14px] text-[#1A1A1A] font-medium leading-relaxed m-0 mt-0.5">
                Tinjau List Mahasiswa yang mendaftarkan Proyek KP dan lakukan pengesahan atau penolakan terhadapnya.
            </p>
        </div>

        <div class="flex flex-wrap gap-4 mb-8">
            <div class="flex gap-4">
                <div class="w-[200px] h-[75px] bg-[#3B82F6] rounded-[5px] relative overflow-hidden flex flex-col justify-center items-center shadow-sm">
                    <svg class="w-6 h-6 absolute left-3 top-3 text-white opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    <span class="text-[#E8F5E9] text-[28px] font-bold font-inter leading-none mt-1">{{ $stats['total_mahasiswa'] ?? 0 }}</span>
                    <span class="text-[#E8F5E9] text-[12px] font-medium font-inter mt-1">Total Mahasiswa KP</span>
                </div>
                <div class="w-[200px] h-[75px] bg-[#4CAF50] rounded-[5px] relative overflow-hidden flex flex-col justify-center items-center shadow-sm">
                    <svg class="w-6 h-6 absolute left-3 top-3 text-white opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span class="text-[#E8F5E9] text-[28px] font-bold font-inter leading-none mt-1">{{ $stats['dapat_projek'] ?? 0 }}</span>
                    <span class="text-[#E8F5E9] text-[12px] font-medium font-inter mt-1">Sudah Dapat Projek</span>
                </div>
                <div class="w-[200px] h-[75px] bg-[#EA4335] rounded-[5px] relative overflow-hidden flex flex-col justify-center items-center shadow-sm">
                    <svg class="w-6 h-6 absolute left-3 top-3 text-white opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span class="text-[#E8F5E9] text-[28px] font-bold font-inter leading-none mt-1">{{ $stats['belum_dapat_projek'] ?? 0 }}</span>
                    <span class="text-[#E8F5E9] text-[12px] font-medium font-inter mt-1">Belum Dapat Projek</span>
                </div>
            </div>

            <div class="hidden xl:block w-px bg-gray-300 mx-2"></div>

            <div class="flex gap-4">
                <div class="bg-[#34A853] text-white rounded-[5px] w-[100px] h-[75px] flex flex-col justify-center items-center shadow-sm">
                    <div class="flex items-center gap-1 mt-1">
                        <svg class="w-4 h-4 rounded-full border border-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        <span class="text-[24px] font-bold leading-none">{{ $stats['disetujui'] }}</span>
                    </div>
                    <span class="text-[11px] font-medium mt-1.5">Disetujui</span>
                </div>
                <div class="bg-[#FBBC05] text-black rounded-[5px] w-[100px] h-[75px] flex flex-col justify-center items-center shadow-sm">
                    <div class="flex items-center gap-1 mt-1">
                        <div class="w-3.5 h-3.5 border-2 border-black rounded-sm"></div>
                        <span class="text-[24px] font-bold leading-none">{{ $stats['belum_diperiksa'] }}</span>
                    </div>
                    <span class="text-[11px] font-medium mt-1.5">Belum Diperiksa</span>
                </div>
                <div class="bg-[#EA4335] text-white rounded-[5px] w-[100px] h-[75px] flex flex-col justify-center items-center shadow-sm">
                    <div class="flex items-center gap-1 mt-1">
                        <svg class="w-4 h-4 rounded-full border border-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        <span class="text-[24px] font-bold leading-none">{{ $stats['ditolak'] }}</span>
                    </div>
                    <span class="text-[11px] font-medium mt-1.5">Ditolak</span>
                </div>
            </div>
        </div>

        <form method="GET" action="{{ route('koordinator.pendaftaran-kp') }}" class="mb-4 flex flex-col lg:flex-row justify-between items-center gap-4 w-full" x-data="{ submitForm() { this.$el.submit(); } }">
            
            <div class="relative w-full lg:max-w-[400px]">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="h-4 w-4 text-gray-400 font-bold" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </div>
                <input type="text" x-model="searchQuery" placeholder="Cari berdasarkan Nama, NIM, Judul, atau Instansi..." 
                    class="border border-[#CAC0C0] rounded-[5px] pl-10 pr-3 py-2 w-full text-[13px] focus:outline-none focus:border-[#4CC098] focus:ring-1 focus:ring-[#4CC098] font-normal shadow-sm">
            </div>

            <div class="flex flex-wrap items-center gap-4 w-full lg:w-auto justify-start lg:justify-end">
                <div class="flex items-center gap-2" x-data="{ openJenis: false, selectedJenis: '{{ request('jenis_kp', 'All') }}' }">
                    <label class="font-bold text-[13px] text-gray-700">Jenis KP:</label>
                    <div class="relative w-[130px]">
                        <button type="button" @click="openJenis = !openJenis" @click.outside="openJenis = false" 
                            class="w-full border border-[#CAC0C0] rounded px-3 py-1.5 focus:outline-none bg-white flex justify-between items-center text-[13px] font-medium cursor-pointer shadow-sm">
                            <span x-text="selectedJenis"></span>
                            <svg :class="openJenis ? 'rotate-180' : 'rotate-0'" class="w-3.5 h-3.5 transition-transform duration-200 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                            </svg>
                        </button>
                        <div x-show="openJenis" x-transition style="display: none;" class="absolute z-50 w-full mt-1 bg-white border border-[#CAC0C0] rounded shadow-lg overflow-hidden">
                            <ul class="py-1 text-[13px]">
                                <li><label class="block px-3 py-1.5 hover:bg-gray-100 cursor-pointer"><input type="radio" name="jenis_kp" value="All" class="hidden" x-model="selectedJenis" @change="$el.closest('form').submit()">All</label></li>
                                <li><label class="block px-3 py-1.5 hover:bg-gray-100 cursor-pointer"><input type="radio" name="jenis_kp" value="Internal" class="hidden" x-model="selectedJenis" @change="$el.closest('form').submit()">Internal</label></li>
                                <li><label class="block px-3 py-1.5 hover:bg-gray-100 cursor-pointer"><input type="radio" name="jenis_kp" value="External" class="hidden" x-model="selectedJenis" @change="$el.closest('form').submit()">External</label></li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="flex items-center gap-2" x-data="{ openStatus: false, selectedStatus: '{{ request('status_approval', 'All') }}' }">
                    <label class="font-bold text-[13px] text-gray-700">Status:</label>
                    <div class="relative w-[130px]">
                        <button type="button" @click="openStatus = !openStatus" @click.outside="openStatus = false" 
                            class="w-full border border-[#CAC0C0] rounded px-3 py-1.5 focus:outline-none bg-white flex justify-between items-center text-[13px] font-medium cursor-pointer shadow-sm">
                            <span x-text="selectedStatus"></span>
                            <svg :class="openStatus ? 'rotate-180' : 'rotate-0'" class="w-3.5 h-3.5 transition-transform duration-200 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                            </svg>
                        </button>
                        <div x-show="openStatus" x-transition style="display: none;" class="absolute z-50 w-full mt-1 bg-white border border-[#CAC0C0] rounded shadow-lg overflow-hidden">
                            <ul class="py-1 text-[13px]">
                                <li><label class="block px-3 py-1.5 hover:bg-gray-100 cursor-pointer"><input type="radio" name="status_approval" value="All" class="hidden" x-model="selectedStatus" @change="$el.closest('form').submit()">All</label></li>
                                <li><label class="block px-3 py-1.5 hover:bg-gray-100 cursor-pointer"><input type="radio" name="status_approval" value="Belum Diperiksa" class="hidden" x-model="selectedStatus" @change="$el.closest('form').submit()">Belum Diperiksa</label></li>
                                <li><label class="block px-3 py-1.5 hover:bg-gray-100 cursor-pointer"><input type="radio" name="status_approval" value="Disetujui" class="hidden" x-model="selectedStatus" @change="$el.closest('form').submit()">Disetujui</label></li>
                                <li><label class="block px-3 py-1.5 hover:bg-gray-100 cursor-pointer"><input type="radio" name="status_approval" value="Ditolak" class="hidden" x-model="selectedStatus" @change="$el.closest('form').submit()">Ditolak</label></li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="flex gap-2">
                    <a href="{{ route('koordinator.pendaftaran-kp') }}" class="bg-[#EA4335] text-white hover:bg-red-600 transition-colors px-4 py-1.5 rounded-[5px] text-[12px] font-bold shadow-sm flex items-center justify-center">Clear Filter</a>
                    <button type="button" @click="isSelectionMode = !isSelectionMode; sessionStorage.setItem('kpSelectionMode', isSelectionMode)" class="bg-[#4285F4] hover:bg-blue-600 transition-colors text-white px-4 py-1.5 rounded-[5px] text-[12px] font-bold shadow-sm whitespace-nowrap" x-text="isSelectionMode ? 'Batal Pilih' : 'Pilih Beberapa'"></button>
                </div>
            </div>
            
            <button type="submit" class="hidden">Search</button>
        </form>

        <div class="overflow-x-auto bg-[#F9F9F9] rounded-t-[10px] mb-4">
            <table class="w-full text-left border-collapse text-[12px] text-center border border-[#CAC0C0]">
                <thead>
                    <tr class="bg-[#E0DFDF] font-bold text-black h-[40px]">
                        <th class="border border-[#CAC0C0] px-4 py-2 w-[40px]">No</th>
                        <th class="border border-[#CAC0C0] px-4 py-2">Mahasiswa</th>
                        <th class="border border-[#CAC0C0] px-4 py-2">Jenis KP</th>
                        <th class="border border-[#CAC0C0] px-4 py-2">Nama Instansi</th>
                        <th class="border border-[#CAC0C0] px-4 py-2">Supervisor</th>
                        <th class="border border-[#CAC0C0] px-4 py-2">Judul KP</th>
                        <th class="border border-[#CAC0C0] px-4 py-2 min-w-[200px]">Status Approval</th>
                        <th class="border border-[#CAC0C0] px-4 py-2 w-[80px]">Detail KP</th>
                    </tr>
                </thead>
                <tbody class="counter-body" x-data="{
                    isEmpty: {{ count($pendaftarans) === 0 ? 'true' : 'false' }},
                    checkEmpty() {
                        this.$nextTick(() => {
                            this.isEmpty = Array.from(document.querySelectorAll('.data-row')).filter(el => el.style.display !== 'none').length === 0;
                        });
                    }
                }" x-effect="searchQuery; checkEmpty()">
                    
                    @forelse($pendaftarans as $index => $kp)
                    <tr class="data-row h-[52px] bg-white hover:bg-gray-50 font-medium transition-colors"
                        x-data="{ rowSearchData: {{ json_encode(strtolower(($kp->user->name ?? '') . ' ' . ($kp->user->mahasiswa->nim ?? '') . ' ' . $kp->judul_kp . ' ' . $kp->instansi_nama)) }} }"
                        x-show="!searchQuery || rowSearchData.includes(searchQuery.toLowerCase())">
                        <td class="border border-[#CAC0C0] px-4 py-2 text-center">
                            <span x-show="!isSelectionMode" class="row-number-cell text-gray-700 font-bold"></span>
                            <div x-show="isSelectionMode" style="display: none;">
                                @if($kp->status_kp === 'pending')
                                    <input type="checkbox" name="selected_ids[]" value="{{ $kp->id }}" class="rounded shadow-sm border-[#CAC0C0] focus:ring-[#4CC098] cursor-pointer w-4 h-4 text-[#4CC098]">
                                @else
                                    <input type="checkbox" disabled class="rounded shadow-sm border-gray-200 bg-gray-100 cursor-not-allowed opacity-50 w-4 h-4">
                                @endif
                            </div>
                        </td>
                        <td class="border border-[#CAC0C0] px-4 py-2 leading-tight">
                            <div class="font-bold text-[12px]">{{ $kp->user->name ?? 'Unknown User' }}</div>
                            <div class="text-[11px] text-gray-500 mt-0.5">{{ $kp->user->mahasiswa->nim ?? 'Unknown NIM' }}</div>
                        </td>
                        <td class="border border-[#CAC0C0] px-4 py-2">{{ $kp->jenis_instansi }}</td>
                        <td class="border border-[#CAC0C0] px-4 py-2">{{ $kp->instansi_nama }}</td>
                        <td class="border border-[#CAC0C0] px-4 py-2">{{ $kp->supervisorInstansi->nama_supervisor ?? '-' }}</td>
                        <td class="border border-[#CAC0C0] px-4 py-2 text-left pl-4 max-w-[200px] truncate" title="{{ $kp->judul_kp }}">{{ Str::limit($kp->judul_kp, 35) }}</td>
                        
                        <td class="border border-[#CAC0C0] px-4 py-2">
                            @if($kp->status_kp === 'pending')
                                <div x-show="!isSelectionMode" class="flex items-center justify-center gap-2">
                                    <form method="POST" action="{{ route('koordinator.pendaftaran-kp.status', $kp->id) }}">
                                        @csrf
                                        @method('PUT')
                                        <input type="hidden" name="status" value="rejected">
                                        <button type="button" @click="openModalCatatan($el.closest('form'), 'Tolak Pendaftaran KP?')" class="bg-[#EA4335] text-white px-3 py-1 rounded-[20px] shadow-sm flex items-center justify-center gap-1 w-[80px] hover:bg-red-600 transition">
                                            <svg class="w-3 h-3 rounded-full border border-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                            Tolak
                                        </button>
                                    </form>
                                    <form method="POST" action="{{ route('koordinator.pendaftaran-kp.status', $kp->id) }}">
                                        @csrf
                                        @method('PUT')
                                        <input type="hidden" name="status" value="approved">
                                        <button type="button" @click="openModalCatatan($el.closest('form'), 'Sahkan Pendaftaran KP?')" class="bg-[#34A853] text-white px-3 py-1 rounded-[20px] shadow-sm flex items-center justify-center gap-1 w-[80px] hover:bg-green-600 transition">
                                            <svg class="w-3 h-3 rounded-full border border-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                            Sahkan
                                        </button>
                                    </form>
                                </div>
                                <div x-show="isSelectionMode" style="display: none;" class="flex items-center justify-center">
                                    <div class="inline-flex items-center justify-center bg-[#FDE293] text-[#A67C00] border border-[#FDE293] px-6 py-1.5 rounded-[20px] font-bold w-[120px] shadow-sm text-[11px] cursor-not-allowed">
                                        Menunggu
                                    </div>
                                </div>
                            @elseif($kp->status_kp === 'approved')
                                <div x-data="{ open: false }" class="relative flex items-center justify-center">
                                    <button @click="open = !open" class="inline-flex items-center justify-center bg-[#A1DFAC] text-[#1D5E2D] px-6 py-1.5 rounded-[20px] font-bold w-[120px] shadow-sm text-[11px] hover:bg-green-300 transition cursor-pointer">
                                        Disetujui <svg class="w-3 h-3 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                    </button>
                                    <div x-show="open" @click.outside="open = false" style="display: none;" class="absolute top-full mt-1 left-1/2 -translate-x-1/2 bg-white border border-gray-200 shadow-lg rounded-[8px] overflow-hidden z-20 w-[120px]">
                                        <form method="POST" action="{{ route('koordinator.pendaftaran-kp.status', $kp->id) }}">
                                            @csrf
                                            @method('PUT')
                                            <input type="hidden" name="status" value="rejected">
                                            <button type="button" class="w-full text-left px-4 py-2 hover:bg-gray-100 text-[11px] font-bold text-[#EA4335]" @click="openModalCatatan($el.closest('form'), 'Ubah Status ke Ditolak')">
                                                Ubah: Tolak
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            @elseif($kp->status_kp === 'rejected')
                                <div x-data="{ open: false }" class="relative flex items-center justify-center">
                                    <button @click="open = !open" class="inline-flex items-center justify-center bg-[#F3A5A1] text-[#711611] px-6 py-1.5 rounded-[20px] font-bold w-[120px] shadow-sm text-[11px] hover:bg-red-300 transition cursor-pointer">
                                        Ditolak <svg class="w-3 h-3 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                    </button>
                                    <div x-show="open" @click.outside="open = false" style="display: none;" class="absolute top-full mt-1 left-1/2 -translate-x-1/2 bg-white border border-gray-200 shadow-lg rounded-[8px] overflow-hidden z-20 w-[120px]">
                                        <form method="POST" action="{{ route('koordinator.pendaftaran-kp.status', $kp->id) }}">
                                            @csrf
                                            @method('PUT')
                                            <input type="hidden" name="status" value="approved">
                                            <button type="button" class="w-full text-left px-4 py-2 hover:bg-gray-100 text-[11px] font-bold text-[#34A853]" @click="openModalCatatan($el.closest('form'), 'Ubah Status ke Disahkan')">
                                                Ubah: Sahkan
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            @endif
                        </td>
                        
                        <td class="border border-[#CAC0C0] px-4 py-2">
                            <a href="{{ route('koordinator.pendaftaran-kp.show', \Str::slug(($kp->user->name ?? 'user') . '-' . ($kp->user->mahasiswa->nim ?? '000'))) }}" class="inline-block bg-[#4285F4] hover:bg-blue-600 text-white px-4 py-1.5 rounded-[20px] shadow-sm text-[11px] font-semibold transition-colors text-center w-full max-w-[80px]">Detail</a>
                        </td>
                    </tr>
                    @empty
                    @endforelse

                    <tr class="empty-state-row" x-show="isEmpty" style="{{ count($pendaftarans) > 0 ? 'display: none;' : '' }}">
                        <td colspan="8" class="border border-[#CAC0C0] px-4 py-16 text-center bg-white">
                            <div class="flex flex-col items-center justify-center text-gray-400">
                                <svg class="w-12 h-12 mb-3 opacity-60" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                                <p class="text-[14px] font-medium text-gray-500">Data pendaftaran tidak ditemukan.</p>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="flex flex-col lg:flex-row justify-between items-center mb-8 gap-4">
            <div class="flex gap-2 w-full lg:w-auto overflow-hidden transition-all duration-300" 
                :class="isSelectionMode ? 'opacity-100 max-h-[50px] translate-y-0' : 'opacity-0 max-h-0 -translate-y-4 pointer-events-none'">
                <button type="button" class="bg-[#34A853] hover:bg-green-600 transition-colors text-white px-5 py-2 rounded-[5px] text-[13px] font-medium shadow flex items-center gap-2 cursor-pointer" onclick="alert('Checkboxes are for UI demo, bulk approve logic can be added later.')">
                    <svg class="w-3.5 h-3.5 rounded-full border border-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    Sahkan Terpilih
                </button>
                <button type="button" class="bg-[#EA4335] hover:bg-red-600 transition-colors text-white px-5 py-2 rounded-[5px] text-[13px] font-medium shadow flex items-center gap-2 cursor-pointer" onclick="alert('Checkboxes are for UI demo, bulk reject logic can be added later.')">
                    <svg class="w-3.5 h-3.5 rounded-full border border-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    Tolak Terpilih
                </button>
            </div>

            <div class="flex items-center gap-2 text-[12px] font-medium text-gray-600">
                @if(method_exists($pendaftarans, 'hasPages'))
                    <span class="mr-4 text-[13px]">{{ $pendaftarans->firstItem() ?? 0 }} - {{ $pendaftarans->lastItem() ?? 0 }} dari {{ $pendaftarans->total() }} entri</span>
                    
                    @if ($pendaftarans->hasPages())
                        <div class="flex border border-[#CAC0C0] rounded shadow-sm bg-white overflow-hidden">
                            @if ($pendaftarans->onFirstPage())
                                <span class="px-3 py-1.5 text-gray-400 bg-gray-50 cursor-not-allowed">&lt;</span>
                            @else
                                <a href="{{ $pendaftarans->appends(request()->query())->previousPageUrl() }}" class="px-3 py-1.5 hover:bg-gray-100 transition-colors border-r border-[#CAC0C0]">&lt;</a>
                            @endif

                            @foreach ($pendaftarans->appends(request()->query())->links()->elements as $element)
                                @if (is_string($element))
                                    <span class="px-3 py-1.5 text-gray-400 bg-gray-50 border-r border-[#CAC0C0]">{{ $element }}</span>
                                @endif

                                @if (is_array($element))
                                    @foreach ($element as $page => $url)
                                        @if ($page == $pendaftarans->currentPage())
                                            <span class="px-3 py-1.5 bg-[#4285F4] text-white font-bold border-r border-[#CAC0C0]">{{ $page }}</span>
                                        @else
                                            <a href="{{ $url }}" class="px-3 py-1.5 hover:bg-gray-100 transition-colors border-r border-[#CAC0C0]">{{ $page }}</a>
                                        @endif
                                    @endforeach
                                @endif
                            @endforeach

                            @if ($pendaftarans->hasMorePages())
                                <a href="{{ $pendaftarans->appends(request()->query())->nextPageUrl() }}" class="px-3 py-1.5 hover:bg-gray-100 transition-colors">&gt;</a>
                            @else
                                <span class="px-3 py-1.5 text-gray-400 bg-gray-50 cursor-not-allowed">&gt;</span>
                            @endif
                        </div>
                    @endif
                @else
                    <span class="mr-4 text-[13px]">1 - {{ count($pendaftarans) }} dari {{ count($pendaftarans) }} entri</span>
                    <div class="flex border border-[#CAC0C0] rounded shadow-sm bg-white overflow-hidden">
                        <span class="px-3 py-1.5 text-gray-400 bg-gray-50 cursor-not-allowed border-r border-[#CAC0C0]">&lt;</span>
                        <span class="px-3 py-1.5 bg-[#4285F4] text-white font-bold border-r border-[#CAC0C0]">1</span>
                        <span class="px-3 py-1.5 text-gray-400 bg-gray-50 cursor-not-allowed">&gt;</span>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-dashboard-layout>