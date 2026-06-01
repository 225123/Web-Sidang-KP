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

    <script>
        document.addEventListener('alpine:init', () => {
            if (!Alpine.data('detailController')) {
                Alpine.data('detailController', () => ({
                    initialDosen: "{{ $initialDosen }}",
                    selectedDosen: "{{ $initialDosen }}",
                    supervisorId: "{{ $kp->supervisor_internal_id ?? '' }}",
                    openDropdown: false,
                    searchDosen: "",
                    confirmDialog: { show: false, title: '', message: '', dosenId: null, type: 'info' },
                    baseDosenData: @json($dosenDataArray),
                    get isDirty() { return this.initialDosen != this.selectedDosen; },
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
                    triggerConfirm(id) {
                        if (id !== '' && this.supervisorId == id) return;
                        this.confirmDialog.dosenId = id;
                        this.confirmDialog.title = 'Konfirmasi Perubahan';
                        this.confirmDialog.message = 'Anda yakin ingin merubah penugasan? (Perubahan tidak akan disimpan sampai Anda menekan tombol Selesai)';
                        this.confirmDialog.type = id === '' ? 'danger' : 'info';
                        this.confirmDialog.show = true;
                    },
                    executeConfirm() {
                        this.selectedDosen = this.confirmDialog.dosenId;
                        this.openDropdown = false;
                        this.confirmDialog.show = false;
                    }
                }));
            }
        });
        
        // Fallback for Turbo Drive (if alpine:init already fired)
        if (window.Alpine && !window.Alpine.data('detailController')) {
            window.Alpine.data('detailController', () => ({
                initialDosen: "{{ $initialDosen }}",
                selectedDosen: "{{ $initialDosen }}",
                supervisorId: "{{ $kp->supervisor_internal_id ?? '' }}",
                openDropdown: false,
                searchDosen: "",
                confirmDialog: { show: false, title: '', message: '', dosenId: null, type: 'info' },
                baseDosenData: @json($dosenDataArray),
                get isDirty() { return this.initialDosen != this.selectedDosen; },
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
                triggerConfirm(id) {
                    if (id !== '' && this.supervisorId == id) return;
                    this.confirmDialog.dosenId = id;
                    this.confirmDialog.title = 'Konfirmasi Perubahan';
                    this.confirmDialog.message = 'Anda yakin ingin merubah penugasan? (Perubahan tidak akan disimpan sampai Anda menekan tombol Selesai)';
                    this.confirmDialog.type = id === '' ? 'danger' : 'info';
                    this.confirmDialog.show = true;
                },
                executeConfirm() {
                    this.selectedDosen = this.confirmDialog.dosenId;
                    this.openDropdown = false;
                    this.confirmDialog.show = false;
                }
            }));
        }
    </script>

    <div class="mt-8 px-2 w-full max-w-[1200px] mx-auto pb-12" x-data="detailController()">
        
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
            <input type="hidden" name="assignments[{{ $clusterId }}]" :value="selectedDosen">
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
                <div class="mb-3 md:mb-0 font-medium text-[15px] md:text-[14px] {{ $kp->is_lanjutan ? 'text-[#EA4335] font-bold' : 'text-[#34A853]' }}">
                    {{ $kp->is_lanjutan ? 'Lanjutan' : 'Baru' }}
                </div>
                
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
                        @if($isReadOnly)
                        <div class="w-full py-1.5 px-3 border border-gray-200 bg-gray-50 rounded-[5px] text-[13px] font-semibold flex items-center text-gray-500 cursor-not-allowed h-[34px]">
                            <span class="truncate pr-2" x-text="selectedDosen !== '' ? getDosenName(selectedDosen) : 'Belum Ditugaskan'"></span>
                        </div>
                        @else
                        <button type="button" @click="$event.stopPropagation(); openDropdown = !openDropdown; searchDosen = ''" 
                                class="w-full py-1.5 px-3 border bg-white rounded-[5px] text-[13px] font-semibold flex items-center justify-between transition-colors shadow-sm focus:outline-none h-[34px]"
                                :class="selectedDosen !== '' ? 'text-[#4285F4] border-[#4285F4] hover:bg-blue-50' : 'text-gray-600 border-gray-400 hover:bg-gray-50'">
                            <span class="truncate pr-2" x-text="selectedDosen !== '' ? getDosenName(selectedDosen) : 'Tugaskan Dosen'"></span>
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
                                    <button type="button" @click.prevent="triggerConfirm('')" class="w-full text-left px-3 py-2 font-semibold text-red-600 hover:bg-red-50 flex items-center gap-2 border-b border-gray-100 whitespace-nowrap">
                                        Batalkan Pilihan
                                    </button>
                                </li>
                                <template x-for="dosen in filteredDosenList" :key="dosen.id">
                                    <li>
                                        <button type="button" @click.prevent="triggerConfirm(dosen.id)" 
                                                class="w-full text-left px-3 py-2.5 flex justify-between items-center transition-colors border-b border-gray-100/50"
                                                :class="[
                                                    supervisorId == dosen.id ? 'bg-red-50 text-red-500 cursor-not-allowed opacity-80' : 
                                                    (selectedDosen == dosen.id ? 'bg-[#E6F0FA] font-bold text-gray-900 pointer-events-none' : 'hover:bg-blue-50 cursor-pointer text-gray-700 font-bold'),
                                                    dosen.beban >= dosen.kuota && selectedDosen != dosen.id && supervisorId != dosen.id ? 'opacity-50 cursor-not-allowed text-gray-400' : ''
                                                ]"
                                                :disabled="(dosen.beban >= dosen.kuota && selectedDosen != dosen.id) || supervisorId == dosen.id">
                                            <div class="flex items-center gap-2 truncate pr-2">
                                                <span class="truncate" x-text="dosen.nama"></span>
                                                <template x-if="supervisorId == dosen.id">
                                                    <span class="text-[9px] bg-red-100 text-red-600 px-1 rounded uppercase font-bold flex-shrink-0 border border-red-200">SUPERVISOR</span>
                                                </template>
                                            </div>
                                            <span class="shrink-0 font-bold ml-auto text-right whitespace-nowrap text-[#4285F4]" x-text="dosen.beban"></span>
                                        </button>
                                    </li>
                                </template>
                            </ul>
                        </div>
                        @endif
                    </div>
                </div>

            </div>

            @if(!$isReadOnly)
            <div class="flex justify-end mt-8 border-t border-[#D9D9D9] pt-6">
                <button type="submit" form="form-assignment" 
                        class="bg-[#4285F4] hover:bg-blue-600 transition-colors text-white px-8 py-2.5 rounded-[5px] text-[13px] font-bold shadow-sm flex items-center justify-center gap-2 w-[140px] disabled:opacity-50 disabled:cursor-not-allowed disabled:bg-gray-300 disabled:text-gray-500"
                        :disabled="!isDirty">
                    <svg class="w-4 h-4 text-current" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    Selesai
                </button>
            </div>
            @else
            <div class="mt-8 border-t border-[#D9D9D9] pt-6">
                <div class="bg-red-50 text-red-600 p-3 rounded-[5px] text-[13px] font-bold border border-red-200 text-center">
                    Mode Read-Only. Anda sedang melihat data penugasan pada periode lampau dan tidak dapat melakukan perubahan.
                </div>
            </div>
            @endif
        </div>

        <!-- Custom Global Confirm Modal -->
        <div x-cloak x-show="confirmDialog.show" style="display: none;" class="fixed inset-0 z-[1000] flex items-center justify-center bg-black/40 backdrop-blur-sm p-4" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95">
            <div @click.away="confirmDialog.show = false" class="bg-white rounded-[15px] w-full max-w-[420px] p-8 shadow-2xl flex flex-col items-center text-center relative overflow-hidden border border-gray-100">
                <div class="mb-6">
                    <div class="w-20 h-20 bg-blue-50 rounded-full flex items-center justify-center">
                        <svg class="w-12 h-12 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                </div>

                <h3 class="text-[18px] font-bold text-gray-900 mb-3" x-text="confirmDialog.title"></h3>
                <p class="text-[14px] text-gray-500 mb-8 leading-relaxed px-2" x-text="confirmDialog.message"></p>

                <div class="flex gap-4 w-full">
                    <button @click="confirmDialog.show = false" type="button" class="flex-1 h-[45px] bg-gray-50 hover:bg-gray-100 text-gray-700 rounded-[10px] text-[14px] font-bold transition-all border border-gray-200">
                        Batal
                    </button>
                    <button @click="executeConfirm()" type="button" class="flex-1 h-[45px] text-white rounded-[10px] text-[14px] font-bold transition-all shadow-md active:transform active:scale-95 bg-[#4285F4] hover:bg-blue-700">
                        Lanjutkan
                    </button>
                </div>
            </div>
        </div>

    </div>
</x-dashboard-layout>
