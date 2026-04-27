

<div class="mb-4 flex flex-col gap-4 w-full">

    <div class="relative w-full lg:max-w-[400px]">
        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
            <svg class="h-4 w-4 text-gray-400 font-bold" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
        </div>
        <input type="text" name="{{ $prefix }}[search]" x-model="{{ $prefix === 'main' ? 'searchQuery' : 'searchQueryRejected' }}" placeholder="Cari berdasarkan Nama, NIM, Judul, atau Instansi..." 
            class="border border-[#CAC0C0] rounded-[5px] pl-10 pr-3 py-2 w-full text-[13px] focus:outline-none focus:border-[#4CC098] focus:ring-1 focus:ring-[#4CC098] font-normal shadow-sm">
    </div>

    <div class="flex flex-wrap items-center gap-4 w-full justify-start">

        @if($prefix === 'rejected')
        <div class="relative w-full sm:w-[150px] mt-2 sm:mt-0" x-data="{ openJenis: false, selectedJenis: '{{ request($prefix.'.jenis_kp', 'All') }}' }" @reset-dropdowns-{{ $prefix }}.window="selectedJenis = 'All'">
            <button type="button" @click="openJenis = !openJenis" @click.outside="openJenis = false" 
                class="w-full border border-[#CAC0C0] rounded px-3 py-1.5 focus:outline-none bg-white flex justify-between items-center text-[13px] font-medium cursor-pointer shadow-sm">
                <span x-text="'Jenis KP: ' + selectedJenis"></span>
                <svg :class="openJenis ? 'rotate-0' : 'rotate-90'" class="w-3.5 h-3.5 transition-all duration-200 text-gray-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
            </button>
            <div x-show="openJenis" x-transition style="display: none;" class="absolute z-50 w-full mt-1 bg-white border border-[#CAC0C0] rounded shadow-lg overflow-hidden">
                <ul class="py-1 text-[13px]">
                    <li><label class="block px-3 py-1.5 hover:bg-gray-100 cursor-pointer"><input type="radio" name="{{ $prefix }}[jenis_kp]" value="All" class="hidden" x-model="selectedJenis" @change="$dispatch('update-jenis-' + '{{ $prefix }}', selectedJenis)">All</label></li>
                    <li><label class="block px-3 py-1.5 hover:bg-gray-100 cursor-pointer"><input type="radio" name="{{ $prefix }}[jenis_kp]" value="Internal" class="hidden" x-model="selectedJenis" @change="$dispatch('update-jenis-' + '{{ $prefix }}', selectedJenis)">Internal</label></li>
                    <li><label class="block px-3 py-1.5 hover:bg-gray-100 cursor-pointer"><input type="radio" name="{{ $prefix }}[jenis_kp]" value="External" class="hidden" x-model="selectedJenis" @change="$dispatch('update-jenis-' + '{{ $prefix }}', selectedJenis)">Eksternal</label></li>
                </ul>
            </div>
        </div>
        @endif

        @if(!$hideStatus)
        <div class="relative w-full sm:w-[150px] mt-2 sm:mt-0" x-data="{ openStatus: false, selectedStatus: '{{ request($prefix.'.status_approval', 'All') }}' }" @reset-dropdowns-{{ $prefix }}.window="selectedStatus = 'All'">
            <button type="button" @click="openStatus = !openStatus" @click.outside="openStatus = false" 
                class="w-full border border-[#CAC0C0] rounded px-3 py-1.5 focus:outline-none bg-white flex justify-between items-center text-[13px] font-medium cursor-pointer shadow-sm">
                <span x-text="'Status: ' + selectedStatus"></span>
                <svg :class="openStatus ? 'rotate-0' : 'rotate-90'" class="w-3.5 h-3.5 transition-all duration-200 text-gray-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
            </button>
            <div x-show="openStatus" x-transition style="display: none;" class="absolute z-50 w-full mt-1 bg-white border border-[#CAC0C0] rounded shadow-lg overflow-hidden">
                <ul class="py-1 text-[13px]">
                    <li><label class="block px-3 py-1.5 hover:bg-gray-100 cursor-pointer"><input type="radio" name="{{ $prefix }}[status_approval]" value="All" class="hidden" x-model="selectedStatus" @change="$dispatch('update-status-' + '{{ $prefix }}', selectedStatus)">All</label></li>
                    <li><label class="block px-3 py-1.5 hover:bg-gray-100 cursor-pointer"><input type="radio" name="{{ $prefix }}[status_approval]" value="Belum Diperiksa" class="hidden" x-model="selectedStatus" @change="$dispatch('update-status-' + '{{ $prefix }}', selectedStatus)">Belum Diperiksa</label></li>
                    <li><label class="block px-3 py-1.5 hover:bg-gray-100 cursor-pointer"><input type="radio" name="{{ $prefix }}[status_approval]" value="Disetujui" class="hidden" x-model="selectedStatus" @change="$dispatch('update-status-' + '{{ $prefix }}', selectedStatus)">Disetujui</label></li>
                </ul>
            </div>
        </div>
        @endif

        <div class="relative w-full sm:w-[160px] mt-2 sm:mt-0" x-data="{ openPengerjaan: false, selectedPengerjaan: '{{ request($prefix.'.pengerjaan', 'All') }}' }" @reset-dropdowns-{{ $prefix }}.window="selectedPengerjaan = 'All'">
            <button type="button" @click="openPengerjaan = !openPengerjaan" @click.outside="openPengerjaan = false" 
                class="w-full border border-[#CAC0C0] rounded px-3 py-1.5 focus:outline-none bg-white flex justify-between items-center text-[13px] font-medium cursor-pointer shadow-sm">
                <span x-text="'Pengerjaan: ' + selectedPengerjaan"></span>
                <svg :class="openPengerjaan ? 'rotate-0' : 'rotate-90'" class="w-3.5 h-3.5 transition-all duration-200 text-gray-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
            </button>
            <div x-show="openPengerjaan" x-transition style="display: none;" class="absolute z-50 w-full mt-1 bg-white border border-[#CAC0C0] rounded shadow-lg overflow-hidden">
                <ul class="py-1 text-[13px]">
                    <li><label class="block px-3 py-1.5 hover:bg-gray-100 cursor-pointer"><input type="radio" name="{{ $prefix }}[pengerjaan]" value="All" class="hidden" x-model="selectedPengerjaan" @change="$dispatch('update-pengerjaan-' + '{{ $prefix }}', selectedPengerjaan)">All</label></li>
                    <li><label class="block px-3 py-1.5 hover:bg-gray-100 cursor-pointer"><input type="radio" name="{{ $prefix }}[pengerjaan]" value="Individu" class="hidden" x-model="selectedPengerjaan" @change="$dispatch('update-pengerjaan-' + '{{ $prefix }}', selectedPengerjaan)">Individu</label></li>
                    <li><label class="block px-3 py-1.5 hover:bg-gray-100 cursor-pointer"><input type="radio" name="{{ $prefix }}[pengerjaan]" value="Kelompok" class="hidden" x-model="selectedPengerjaan" @change="$dispatch('update-pengerjaan-' + '{{ $prefix }}', selectedPengerjaan)">Kelompok</label></li>
                </ul>
            </div>
        </div>

        <div class="flex gap-2 w-full sm:w-auto mt-2 sm:mt-0">
            <!-- Clear Filter strictly DOM dispatch -->
            <button type="button" 
                @click="$dispatch('clear-filters-{{ $prefix }}'); {{ $prefix === 'main' ? 'searchQuery' : 'searchQueryRejected' }} = ''; window.dispatchEvent(new CustomEvent('reset-dropdowns-{{ $prefix }}'));" 
                class="flex-1 sm:flex-none border border-[#EA4335] bg-[#EA4335] text-white hover:bg-red-600 transition-colors px-4 py-1.5 rounded-[5px] text-[12px] font-bold shadow-sm flex items-center justify-center">
                Clear Filter
            </button>
        </div>
    </div>
</div>
