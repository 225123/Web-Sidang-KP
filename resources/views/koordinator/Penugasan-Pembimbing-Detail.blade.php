<x-dashboard-layout header="" userName="{{ auth()->user()->name ?? 'KOORDINATOR KP' }}" roleName="KOORDINATOR KP">
    <x-slot:sidebar>
        @include('koordinator.components.sidebar', ['active' => 'penugasan-pembimbing'])
    </x-slot>

    <!-- Passing Data to Alpine to manage form state -->
    @php
        $initialDosen = $kp->pembimbing_id ?? '';
        $dosenDataArray = collect($dosenList)->map(function($d) {
            return [
                'id' => $d['id'],
                'nama' => $d['nama'],
                'beban' => $d['beban'],
                'kuota' => $d['kuota']
            ];
        })->keyBy('id')->toArray();
    @endphp

    <div class="mt-8 px-2 w-full max-w-[1200px] mx-auto pb-12" 
         x-data='{ 
            initialDosen: "{{ $initialDosen }}",
            selectedDosen: "{{ $initialDosen }}",
            openDropdown: false,
            searchDosen: "",
            baseDosenData: @json($dosenDataArray),
            get isDirty() { return this.initialDosen !== this.selectedDosen; },
            get filteredDosenList() {
                let list = Object.values(this.baseDosenData);
                if(this.searchDosen.trim() !== "") {
                    let query = this.searchDosen.toLowerCase();
                    list = list.filter(d => d.nama.toLowerCase().includes(query));
                }
                return list.sort((a,b) => a.nama.localeCompare(b.nama));
            },
            getDosenName(id) {
                if(!id || id === "") return "-";
                return this.baseDosenData[id] ? this.baseDosenData[id].nama : "Unknown";
            },
            setAssignment(id) {
                this.selectedDosen = id;
                this.openDropdown = false;
                this.$nextTick(() => {
                    document.getElementById('form-assignment').submit();
                });
            }
         }'>
        
        <!-- Flash message -->
        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-[5px] relative mb-6 shadow-sm w-full max-w-5xl" role="alert">
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
        @endif
        @if(session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-[5px] relative mb-6 shadow-sm w-full max-w-5xl" role="alert">
                <span class="block sm:inline">{{ session('error') }}</span>
            </div>
        @endif

        <div class="flex items-center gap-4 mb-6">
            <a href="{{ route('koordinator.penugasan-pembimbing') }}" 
               @click="if(isDirty && !confirm('Anda memiliki perubahan (Dosen Pembimbing) yang belum disimpan. Yakin ingin kembali?')) $event.preventDefault();"
               class="w-8 h-8 flex items-center justify-center rounded-full bg-gray-200 hover:bg-gray-300 transition-colors text-gray-700">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"></path></svg>
            </a>
            <h2 class="text-[22px] font-bold text-black border-none m-0">Detail KP</h2>
        </div>

        <form id="form-assignment" action="{{ route('koordinator.penugasan-pembimbing.store') }}" method="POST">
            @csrf
            <!-- Hidden input for assignment -->
            <input type="hidden" name="assignments[{{ $kp->id }}]" :value="selectedDosen">
        </form>

        <div class="bg-[#E6E6E6] rounded-[15px] p-6 lg:p-8 w-full max-w-5xl mt-2 relative">
            <h3 class="text-[17px] font-bold text-black mb-6">Informasi Mahasiswa</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-[200px_10px_1fr] gap-y-1 md:gap-y-4 text-[14px] text-[#1A1A1A] leading-[24px]">
                
                <!-- Nama -->
                <div class="font-bold md:font-medium text-gray-500 md:text-[#1A1A1A]">Nama</div>
                <div class="hidden md:block">:</div>
                <div class="uppercase mb-3 md:mb-0 font-medium text-[15px] md:text-[14px]">{{ $kp->user->name ?? '-' }}</div>
                
                <!-- NIM -->
                <div class="font-bold md:font-medium text-gray-500 md:text-[#1A1A1A]">NIM</div>
                <div class="hidden md:block">:</div>
                <div class="mb-3 md:mb-0 font-medium text-[15px] md:text-[14px]">{{ $kp->user->mahasiswa->nim ?? '-' }}</div>
                
                <!-- Pengerjaan KP -->
                <div class="font-bold md:font-medium text-gray-500 md:text-[#1A1A1A]">Pengerjaan KP</div>
                <div class="hidden md:block">:</div>
                <div class="mb-3 md:mb-0 font-medium text-[15px] md:text-[14px] capitalize">
                    {{ in_array(strtolower($kp->pengerjaan_kp ?? ''), ['kelompok', 'berkelompok']) ? 'Kelompok' : 'Individu' }}
                </div>
                
                @if($kp->pengerjaan_kp === 'kelompok' && !empty($kp->anggotaLainList))
                <!-- Anggota Kelompok -->
                <div class="font-bold md:font-medium text-gray-500 md:text-[#1A1A1A] align-top md:pt-[2px]">Anggota Kelompok</div>
                <div class="hidden md:block align-top md:pt-[2px]">:</div>
                <div class="mb-3 md:mb-0 font-medium text-[15px] md:text-[14px] md:pt-[2px]">
                    <ul class="flex flex-col gap-1">
                        @foreach($kp->anggotaLainList as $anggota)
                            <li>{{ $anggota['nim'] }} - {{ $anggota['nama'] }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif
                
                <!-- Jenis KP -->
                <div class="font-bold md:font-medium text-gray-500 md:text-[#1A1A1A]">Jenis KP</div>
                <div class="hidden md:block">:</div>
                <div class="mb-3 md:mb-0 font-medium text-[15px] md:text-[14px] capitalize">
                    {{ $kp->status_kp === 'approved' ? ($kp->jenis_instansi ?? 'External') : '-' }}
                </div>
                
                <!-- Nama Instansi -->
                <div class="font-bold md:font-medium text-gray-500 md:text-[#1A1A1A]">Nama Instansi</div>
                <div class="hidden md:block">:</div>
                <div class="mb-3 md:mb-0 font-medium text-[15px] md:text-[14px]">{{ $kp->status_kp === 'approved' ? ($kp->instansi_nama ?? '-') : '-' }}</div>
                
                <!-- Supervisor -->
                <div class="font-bold md:font-medium text-gray-500 md:text-[#1A1A1A]">Supervisor</div>
                <div class="hidden md:block">:</div>
                <div class="mb-3 md:mb-0 font-medium text-[15px] md:text-[14px]">{{ $kp->status_kp === 'approved' && $kp->supervisorInstansi ? $kp->supervisorInstansi->nama_supervisor : '-' }}</div>
                
                <!-- Judul KP -->
                <div class="font-bold md:font-medium text-gray-500 md:text-[#1A1A1A]">Judul KP</div>
                <div class="hidden md:block">:</div>
                <div class="mb-3 md:mb-0 font-medium text-[15px] md:text-[14px]">{{ $kp->status_kp === 'approved' ? ($kp->judul_kp ?? '-') : '-' }}</div>
                
                <!-- Status KP -->
                <div class="font-bold md:font-medium text-gray-500 md:text-[#1A1A1A]">Status KP</div>
                <div class="hidden md:block">:</div>
                <div class="mb-3 md:mb-0 font-medium text-[15px] md:text-[14px]">Baru /Lanjut</div>
                
                <!-- Detail KP -->
                <div class="font-bold md:font-medium text-gray-500 md:text-[#1A1A1A] align-top md:pt-2">Detail KP</div>
                <div class="hidden md:block align-top md:pt-2">:</div>
                <div class="md:pt-2 font-medium text-[15px] md:text-[14px] mb-6">
                    <div class="text-[13px] leading-[1.8]">
                        @if($kp->jenis_proyek)
                            {!! nl2br(e($kp->jenis_proyek)) !!}
                        @else
                            <span class="text-gray-400 italic">Tidak ada deskripsi detail KP</span>
                        @endif
                    </div>
                </div>


                
                <!-- Dosen Pembimbing and Dropdown -->
                <div class="font-bold md:font-medium text-gray-500 md:text-[#1A1A1A] flex items-center h-[34px]">Dosen Pembimbing</div>
                <div class="hidden md:flex items-center h-[34px]">:</div>
                <div class="flex items-center gap-4">
                    <div class="font-bold text-[15px] md:text-[14px] min-w-[10px]" x-text="getDosenName(selectedDosen)"></div>
                    <div class="relative w-[220px]" @click.outside="openDropdown = false">
                        <button type="button" @click="$event.stopPropagation(); openDropdown = !openDropdown; searchDosen = ''" 
                                class="w-full py-1.5 px-3 border bg-white rounded-[5px] text-[13px] font-semibold flex items-center justify-between transition-colors shadow-sm focus:outline-none h-[34px]"
                                :class="selectedDosen !== '' ? 'text-[#4285F4] border-[#4285F4] hover:bg-blue-50' : 'text-gray-600 border-gray-400 hover:bg-gray-50'">
                            <span class="truncate pr-2" x-text="selectedDosen !== '' ? 'Ubah Penugasan' : 'Tugaskan Dosen'"></span>
                            <svg class="w-3 h-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </button>

                        <div x-show="openDropdown" x-transition 
                             class="absolute z-50 mt-1 top-full left-0 w-full min-w-[260px] bg-white border border-[#CAC0C0] rounded shadow-xl overflow-hidden text-left" style="display: none;">
                            <div class="p-2 border-b border-gray-200 bg-gray-50 flex items-center gap-2">
                                <svg class="w-4 h-4 text-gray-400 ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                                <input type="text" x-model="searchDosen" @click.stop class="w-full bg-transparent text-[13px] outline-none text-gray-700 py-1" placeholder="Cari Dosen...">
                            </div>
                            <ul class="py-1 text-[13px] max-h-[200px] overflow-y-auto custom-scrollbar bg-white">
                                <li x-show="selectedDosen !== ''">
                                    <button type="button" @click.prevent="setAssignment('')" class="w-full text-left px-3 py-2 font-semibold text-red-600 hover:bg-red-50 flex items-center gap-2 border-b border-gray-100 whitespace-nowrap">
                                        Batalkan Pilihan
                                    </button>
                                </li>
                                <template x-for="dosen in filteredDosenList" :key="dosen.id">
                                    <li>
                                        <button type="button" @click.prevent="setAssignment(dosen.id)" 
                                                class="w-full text-left px-3 py-1.5 flex justify-between items-center transition-colors"
                                                :class="[
                                                    selectedDosen == dosen.id ? 'bg-blue-50 font-bold' : 'hover:bg-gray-100',
                                                    dosen.beban >= dosen.kuota && selectedDosen != dosen.id ? 'opacity-50 cursor-not-allowed text-gray-400' : 'text-gray-700'
                                                ]"
                                                :disabled="dosen.beban >= dosen.kuota && selectedDosen != dosen.id">
                                            <span class="whitespace-nowrap pr-4" x-text="dosen.nama"></span>
                                            <span class="shrink-0 font-bold ml-auto text-right whitespace-nowrap" :class="dosen.beban >= dosen.kuota ? 'text-red-500' : 'text-gray-500'" x-text="'(' + dosen.beban + ')'"></span>
                                        </button>
                                    </li>
                                </template>
                            </ul>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Buttons at the bottom right inside the gray box -->
            <div class="flex justify-end mt-8 border-t border-[#D9D9D9] pt-6">
                <button type="submit" form="form-assignment" 
                        class="bg-[#4285F4] hover:bg-blue-600 transition-colors text-white px-8 py-2.5 rounded-[5px] text-[13px] font-bold shadow-sm flex items-center justify-center gap-2 w-[140px] disabled:opacity-50 disabled:cursor-not-allowed disabled:bg-gray-300 disabled:text-gray-500"
                        :disabled="!isDirty">
                    <svg class="w-4 h-4 text-current" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    Selesai
                </button>
            </div>
        </div>
    </div>
</x-dashboard-layout>
