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
        searchQuery: '{{ request('main.search') }}', 
        searchQueryRejected: '{{ request('rejected.search') }}',
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
            <div class="flex flex-col sm:flex-row gap-4 w-full xl:w-auto">
                <div class="w-full xl:w-[200px] h-[75px] bg-[#3B82F6] rounded-[5px] relative overflow-hidden flex flex-col justify-center items-center shadow-sm">
                    <svg class="w-6 h-6 absolute left-3 top-3 text-white opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    <span class="text-[#E8F5E9] text-[28px] font-bold font-inter leading-none mt-1">{{ $stats['total_mahasiswa'] ?? 0 }}</span>
                    <span class="text-[#E8F5E9] text-[12px] font-medium font-inter mt-1">Total Mahasiswa KP</span>
                </div>
                <div class="w-full xl:w-[200px] h-[75px] bg-[#4CAF50] rounded-[5px] relative overflow-hidden flex flex-col justify-center items-center shadow-sm">
                    <svg class="w-6 h-6 absolute left-3 top-3 text-white opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span class="text-[#E8F5E9] text-[28px] font-bold font-inter leading-none mt-1">{{ $stats['dapat_projek'] ?? 0 }}</span>
                    <span class="text-[#E8F5E9] text-[12px] font-medium font-inter mt-1">Sudah Dapat Projek</span>
                </div>
                <div class="w-full xl:w-[200px] h-[75px] bg-[#EA4335] rounded-[5px] relative overflow-hidden flex flex-col justify-center items-center shadow-sm">
                    <svg class="w-6 h-6 absolute left-3 top-3 text-white opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span class="text-[#E8F5E9] text-[28px] font-bold font-inter leading-none mt-1">{{ $stats['belum_dapat_projek'] ?? 0 }}</span>
                    <span class="text-[#E8F5E9] text-[12px] font-medium font-inter mt-1">Belum Dapat Projek</span>
                </div>
            </div>

            <div class="hidden xl:block w-px bg-gray-300 mx-2"></div>

            <div class="flex flex-col sm:flex-row flex-wrap gap-4 w-full xl:w-auto">
                <div class="bg-[#34A853] text-white rounded-[5px] w-full sm:w-[calc(33.33%-0.67rem)] xl:w-[100px] h-[75px] flex flex-col justify-center items-center shadow-sm">
                    <div class="flex items-center gap-1 mt-1">
                        <svg class="w-4 h-4 rounded-full border border-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        <span class="text-[24px] font-bold leading-none">{{ $stats['disetujui'] }}</span>
                    </div>
                    <span class="text-[11px] font-medium mt-1.5">Disetujui</span>
                </div>
                <div class="bg-[#FBBC05] text-black rounded-[5px] w-full sm:w-[calc(33.33%-0.67rem)] xl:w-[100px] h-[75px] flex flex-col justify-center items-center shadow-sm">
                    <div class="flex items-center gap-1 mt-1">
                        <div class="w-3.5 h-3.5 border-2 border-black rounded-sm"></div>
                        <span class="text-[24px] font-bold leading-none">{{ $stats['belum_diperiksa'] }}</span>
                    </div>
                    <span class="text-[11px] font-medium mt-1.5">Belum Diperiksa</span>
                </div>
                <div class="bg-[#EA4335] text-white rounded-[5px] w-full sm:w-[calc(33.33%-0.67rem)] xl:w-[100px] h-[75px] flex flex-col justify-center items-center shadow-sm">
                    <div class="flex items-center gap-1 mt-1">
                        <svg class="w-4 h-4 rounded-full border border-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        <span class="text-[24px] font-bold leading-none">{{ $stats['ditolak'] }}</span>
                    </div>
                    <span class="text-[11px] font-medium mt-1.5">Ditolak</span>
                </div>
            </div>
        </div>



        <div class="mb-4" id="main">
            @include('koordinator.components.pendaftaran-filter', ['prefix' => 'main', 'otherPrefix' => 'rejected', 'hideStatus' => false])
            @include('koordinator.components.kp-table', ['pendaftarans' => $pendaftarans, 'title' => 'Daftar Pengajuan KP', 'isRejected' => false, 'searchModel' => 'searchQuery'])
        </div>

        <div class="mt-8 mb-4" id="rejected">
            @include('koordinator.components.pendaftaran-filter', ['prefix' => 'rejected', 'otherPrefix' => 'main', 'hideStatus' => true])
            @include('koordinator.components.kp-table', ['pendaftarans' => $rejectedPendaftarans, 'title' => 'Riwayat Penolakan Pendaftaran KP', 'isRejected' => true, 'searchModel' => 'searchQueryRejected'])
        </div>
    </div>
</x-dashboard-layout>