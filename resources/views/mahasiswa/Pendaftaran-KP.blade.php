<x-dashboard-layout userName="{{ auth()->user()->name ?? 'Mahasiswa Name' }}" roleName="MAHASISWA">
    <x-slot:sidebar>
        @include('mahasiswa.components.sidebar', ['active' => 'pendaftaran-kp'])
        </x-slot>

        <div class="mt-6 w-full px-4 lg:px-8 pb-12">

            <h2 class="text-2xl font-bold font-inter text-black mb-6">Pendaftaran KP</h2>

            @if(session('success') || isset($existingKp))
                <div class="flex flex-col lg:flex-row justify-end items-start lg:items-center gap-6 mb-10 w-full">
                    <div x-data="{ open: false, selected: 'Genap 2025/2026' }"
                        class="relative w-full md:w-[212px] flex-shrink-0 lg:mt-0 mt-2">
                        <button @click="open = !open" @click.outside="open = false" type="button"
                            class="w-full flex items-center justify-between border border-[#CAC0C0] bg-[#FBFBFB] rounded-[5px] shadow-sm text-[13px] font-medium py-2 px-3 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 cursor-pointer text-black">
                            <span x-text="selected"></span>
                            <svg :class="open ? 'rotate-90' : 'rotate-180'"
                                class="w-3.5 h-3.5 text-gray-500 transition-transform duration-200 flex-shrink-0"
                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7">
                                </path>
                            </svg>
                        </button>
                        <div x-show="open" x-transition style="display: none;"
                            class="absolute z-50 w-full mt-1 bg-[#FBFBFB] border border-[#CAC0C0] rounded-[5px] shadow-lg overflow-hidden">
                            <ul class="py-1 text-[13px] font-medium text-black">
                                <li><button @click="selected = 'Genap 2025/2026'; open = false" type="button"
                                        class="block w-full text-left px-3 py-2 hover:bg-[#E8E5E5] transition-colors cursor-pointer">Genap
                                        2025/2026</button></li>
                                <li><button @click="selected = 'Ganjil 2025/2026'; open = false" type="button"
                                        class="block w-full text-left px-3 py-2 hover:bg-[#E8E5E5] transition-colors cursor-pointer">Ganjil
                                        2025/2026</button></li>
                            </ul>
                        </div>
                        <input type="hidden" name="periode" :value="selected">
                    </div>
                </div>

                <div class="flex flex-col items-center justify-center mt-12 w-full text-center">
                    <svg class="w-28 h-28 mb-4 text-[#008000]" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                        <path
                            d="M12 2l2.4 2.6L18 4l.6 3.4 3.4.6L20 10.4 22 14l-2.6 2.4L20 20l-3.4-.6L13.2 22 12 19.6 9.6 22 6.2 21.4 5.6 18 2 17.4 4 14 2 10.4l2.6-2.4L4 4l3.4.6L10.8 2 12 4.4z">
                        </path>
                        <polyline points="8 12 11 15 16 9" stroke-width="2.5"></polyline>
                    </svg>

                    <h3 class="text-[17px] font-bold text-black mb-2">
                        {{ isset($existingKp) && $existingKp->status_kp === 'approved' ? 'Pendaftaran KP Disetujui' : 'Kamu Telah Berhasil Mendaftar' }}
                    </h3>
                    <p class="text-[14px] text-[#1A1A1A] font-medium">Informasi selanjutnya akan diumumkan oleh koordinator
                        KP melalui Email atau Notifikasi</p>
                </div>
            @else
                    <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-6 mb-10 w-full">
                        <div class="bg-[#F8D7DA] rounded-[30px] py-4 px-6 flex items-center gap-4 w-full lg:max-w-3xl">
                            <div
                                class="w-8 h-8 flex-shrink-0 flex items-center justify-center bg-yellow-400 font-bold text-xl rounded">
                                !
                            </div>
                            <p class="text-[14px] text-[#1A1A1A] font-medium m-0">
                                Lengkapi formulir pendaftaran Kerja Praktik (KP) di bawah ini untuk mengajukan Kerja Praktik !
                            </p>
                        </div>

                        <div x-data="{ open: false, selected: 'Genap 2025/2026' }"
                            class="relative w-full md:w-[212px] flex-shrink-0 lg:mt-0 mt-2">
                            <button @click="open = !open" @click.outside="open = false" type="button"
                                class="w-full flex items-center justify-between border border-[#CAC0C0] bg-[#FBFBFB] rounded-[5px] shadow-sm text-[13px] font-medium py-2 px-3 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 cursor-pointer text-black">
                                <span x-text="selected"></span>
                                <svg :class="open ? 'rotate-90' : 'rotate-180'"
                                    class="w-3.5 h-3.5 text-gray-500 transition-transform duration-200 flex-shrink-0"
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7">
                                    </path>
                                </svg>
                            </button>
                            <div x-show="open" x-transition style="display: none;"
                                class="absolute z-50 w-full mt-1 bg-[#FBFBFB] border border-[#CAC0C0] rounded-[5px] shadow-lg overflow-hidden">
                                <ul class="py-1 text-[13px] font-medium text-black">
                                    <li><button @click="selected = 'Genap 2025/2026'; open = false" type="button"
                                            class="block w-full text-left px-3 py-2 hover:bg-[#E8E5E5] transition-colors cursor-pointer">Genap
                                            2025/2026</button></li>
                                    <li><button @click="selected = 'Ganjil 2025/2026'; open = false" type="button"
                                            class="block w-full text-left px-3 py-2 hover:bg-[#E8E5E5] transition-colors cursor-pointer">Ganjil
                                            2025/2026</button></li>
                                </ul>
                            </div>
                            <input type="hidden" name="periode" :value="selected">
                        </div>
                    </div>

                    <div class="w-full">
                        @if(session('error'))
                            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-[10px] relative mb-6 shadow-sm"
                                role="alert">
                                <strong class="font-bold flex items-center gap-1"><svg class="w-4 h-4 inline" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg> Perhatian!</strong>
                                <span class="block sm:inline mt-1 text-[13px]">{{ session('error') }}</span>
                            </div>
                        @endif

                        @if($errors->any())
                            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-[10px] relative mb-6 shadow-sm"
                                role="alert">
                                <strong class="font-bold flex items-center gap-1"><svg class="w-4 h-4 inline" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg> Validasi Gagal!</strong>
                                <span class="block sm:inline mt-1 text-[13px]">Mohon periksa kembali perumusan data yang Anda
                                    masukan di bawah, ada beberapa entri yang kurang lengkap atau tidak valid.</span>
                            </div>
                        @endif
                    </div>

                    <div class="w-full max-w-2xl mx-auto mt-4">
                        @php
                            $isInvited = isset($invitation) && $invitation ? 'true' : 'false';
                            $initJenis = old('jenis_instansi', $invitation->jenis_instansi ?? '');
                            $initInstansi = old('instansi_nama', $invitation->instansi_nama ?? '');
                            $initPengerjaan = old('pengerjaan_kp', $invitation ? 'kelompok' : 'individu');
                            $oldAnggota = old('anggota_kelompok_ids', '[]');
                        @endphp
                        <form action="{{ route('mahasiswa.pendaftaran-kp.store') }}" method="POST" x-data="{ 
                            jenisKp: '{{ $initJenis }}', 
                            instansiNama: '{{ $initInstansi }}',
                            openJenis: false,
                            openPengerjaan: false,
                            pengerjaanKp: '{{ $initPengerjaan }}',
                            isInvited: {{ $isInvited }},
                            searchAnggota: '',
                            openAnggota: false,
                            selectedAnggota: {{ $oldAnggota }},
                            allMahasiswa: {{ $allMahasiswa->map(function($m) { return ['id' => (string)$m->id, 'label' => ($m->mahasiswa->nim ?? 'NIM') . ' - ' . $m->name, 'is_unavailable' => $m->is_unavailable]; })->values()->toJson() }},
                            
                            allDosen: {{ $allDosen->map(function($d) { return ['id' => (string)$d->id, 'name' => $d->name]; })->values()->toJson() }},
                            searchDosen: '',
                            openDosen: false,
                            selectedDosenId: '{{ old('supervisor_internal_id', '') }}',
                            selectedDosenName: '{{ old('nama_supervisor', '') }}',
                            
                            selectedGiverId: '{{ old('dosen_pemberi_projek_id', '') }}',
                            selectedGiverName: '{{ old('dosen_pemberi_projek', '') }}',
                            searchGiver: '',
                            openGiver: false,

                             get filteredDosenFromInput() {
                                if (this.searchDosen === '') return this.allDosen;
                                return this.allDosen.filter(d => d.name.toLowerCase().includes(this.searchDosen.toLowerCase()));
                            },

                            selectDosen(dosen) {
                                this.selectedDosenId = String(dosen.id);
                                this.selectedDosenName = dosen.name;
                                this.searchDosen = '';
                                this.openDosen = false;
                            },

                            get filteredGiver() {
                                if (this.searchGiver === '') return this.allDosen;
                                return this.allDosen.filter(d => d.name.toLowerCase().includes(this.searchGiver.toLowerCase()));
                            },

                            selectGiver(dosen) {
                                this.selectedGiverId = String(dosen.id);
                                this.selectedGiverName = dosen.name;
                                this.searchGiver = '';
                                this.openGiver = false;
                            },

                            get filteredMahasiswa() {
                                if(this.searchAnggota === '') return this.allMahasiswa.filter(m => !this.selectedAnggota.includes(m.id));
                                return this.allMahasiswa.filter(m => !this.selectedAnggota.includes(m.id) && m.label.toLowerCase().includes(this.searchAnggota.toLowerCase()));
                            },
                            toggleAnggota(id) {
                                if(this.selectedAnggota.includes(id)) {
                                    this.selectedAnggota = this.selectedAnggota.filter(i => i !== id);
                                } else {
                                    this.selectedAnggota.push(id);
                                }
                                this.searchAnggota = '';
                            },
                            removeAnggota(id) {
                                this.selectedAnggota = this.selectedAnggota.filter(i => i !== id);
                            },
                            getLabel(id) {
                                let m = this.allMahasiswa.find(item => item.id == id);
                                return m ? m.label : '';
                            }
                        }" x-effect="if(jenisKp === 'Internal') { instansiNama = 'Universitas Kristen Krida Wacana'; } else if(jenisKp === 'External' && instansiNama === 'Universitas Kristen Krida Wacana' && !isInvited) { instansiNama = ''; }">
                            @csrf


                            <div class="mb-6">
                                <label for="judul_kp" class="block text-[14px] font-bold text-black mb-2">Judul KP <span
                                        class="text-red-600">*</span></label>
                                <input type="text" name="judul_kp" id="judul_kp" required
                                    placeholder="Masukan judul kerja praktek" value="{{ old('judul_kp') }}"
                                    class="w-full border border-[#CAC0C0] rounded bg-white px-4 py-3 text-[14px] focus:outline-none focus:ring-1 focus:ring-blue-500">
                                @error('judul_kp') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>

                            <div class="mb-6 relative" @click.outside="openJenis = false">
                                <label class="block text-[14px] font-bold text-black mb-2">Jenis KP <span
                                        class="text-red-600">*</span></label>
                                <button type="button" @click="if(!isInvited) openJenis = !openJenis"
                                    :class="isInvited ? 'bg-gray-100 text-gray-500 cursor-not-allowed' : 'bg-white'"
                                    class="w-full flex items-center justify-between border border-[#CAC0C0] rounded px-4 py-3 text-[14px] focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">

                                    <span x-text="jenisKp !== '' ? jenisKp : 'Pilih Jenis KP'" class="flex-1 text-left truncate"
                                        :class="jenisKp !== '' ? (isInvited ? 'text-gray-500' : 'text-black') : 'text-gray-400'"></span>

                                    <svg :class="openJenis ? 'rotate-90' : 'rotate-180'"
                                        class="w-5 h-5 text-gray-500 transition-transform duration-200 flex-shrink-0 ml-2 min-w-[20px]"
                                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7">
                                        </path>
                                    </svg>
                                </button>

                                <div x-show="openJenis" x-transition style="display: none;"
                                    class="absolute z-50 w-full mt-1 bg-white border border-[#CAC0C0] rounded shadow-lg overflow-hidden">
                                    <ul class="py-1 text-[14px]">
                                        <li>
                                            <button type="button" @click="jenisKp = 'Internal'; openJenis = false"
                                                class="block w-full text-left px-4 py-2 hover:bg-yellow-200 transition-colors"
                                                :class="jenisKp === 'Internal' ? 'bg-yellow-300 font-bold' : ''">
                                                Internal
                                            </button>
                                        </li>
                                        <li>
                                            <button type="button" @click="jenisKp = 'External'; openJenis = false"
                                                class="block w-full text-left px-4 py-2 hover:bg-yellow-200 transition-colors"
                                                :class="jenisKp === 'External' ? 'bg-yellow-300 font-bold' : ''">
                                                External
                                            </button>
                                        </li>
                                    </ul>
                                </div>
                                <input type="hidden" name="jenis_instansi" :value="jenisKp" required>
                                @error('jenis_instansi') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>

                            <div class="mb-6">
                                <label for="instansi_nama" class="block text-[14px] font-bold text-black mb-2">Nama Instansi
                                    <span class="text-red-600">*</span></label>
                                <input type="text" name="instansi_nama" id="instansi_nama" required
                                    placeholder="Masukan nama instansi" x-model="instansiNama"
                                    :readonly="jenisKp !== 'External' || isInvited"
                                    class="w-full border border-[#CAC0C0] rounded px-4 py-3 text-[14px] focus:outline-none focus:ring-1 focus:ring-blue-500 transition-colors"
                                    :class="jenisKp !== 'External' || isInvited ? 'bg-gray-100 text-gray-500 cursor-not-allowed' : 'bg-white'">
                                @error('instansi_nama') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>

                            <!-- Dosen Pemberi Projek: Only visible for Internal -->
                            <div class="mb-6 relative" x-show="jenisKp === 'Internal'" x-transition style="display: none;">
                                <label for="dosen_pemberi_projek" class="block text-[14px] font-bold text-black mb-2">Dosen
                                    Pemberi Projek <span class="text-red-600">*</span></label>

                                <!-- Internal Mode: Searchable Dosen Dropdown -->
                                <template x-if="jenisKp === 'Internal'">
                                    <div class="relative" @click.outside="openGiver = false">
                                        <button type="button" @click="openGiver = !openGiver; if(openGiver) searchGiver = ''"
                                            class="w-full flex items-center justify-between border border-[#CAC0C0] rounded bg-white px-4 py-3 text-[14px] focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                                            <span x-text="selectedGiverName !== '' ? selectedGiverName : '--- Pilih Dosen Pemberi Projek ---'" 
                                                class="flex-1 text-left truncate"
                                                :class="selectedGiverName !== '' ? 'text-black' : 'text-gray-400'"></span>
                                            <svg :class="openGiver ? 'rotate-90' : 'rotate-180'"
                                                class="w-5 h-5 text-gray-500 transition-transform duration-200 flex-shrink-0 ml-2 min-w-[20px]"
                                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                            </svg>
                                        </button>

                                        <div x-show="openGiver" x-transition style="display: none;"
                                            class="absolute z-50 w-full mt-1 bg-white border border-[#CAC0C0] rounded shadow-lg overflow-hidden">
                                            <div class="p-2 border-b border-gray-100 bg-gray-50">
                                                <input type="text" x-model="searchGiver" placeholder="Cari dosen..." 
                                                    class="w-full px-3 py-1.5 text-[13px] border border-gray-200 rounded focus:outline-none focus:ring-1 focus:ring-blue-500">
                                            </div>
                                            <ul class="py-1 text-[14px] max-h-48 overflow-y-auto">
                                                <template x-for="dosen in filteredGiver" :key="dosen.id">
                                                    <li>
                                                        <button type="button" @click="selectGiver(dosen)"
                                                            class="block w-full text-left px-4 py-2 hover:bg-yellow-200 transition-colors"
                                                            :class="selectedGiverId == dosen.id ? 'bg-yellow-100 font-bold' : ''">
                                                            <span x-text="dosen.name"></span>
                                                        </button>
                                                    </li>
                                                </template>
                                                <template x-if="filteredGiver.length === 0">
                                                    <li class="px-4 py-2 text-gray-500 text-center">Dosen tidak ditemukan...</li>
                                                </template>
                                            </ul>
                                        </div>
                                        <input type="hidden" name="dosen_pemberi_projek" :value="selectedGiverName" :required="jenisKp === 'Internal'">
                                    </div>
                                </template>

                                @error('dosen_pemberi_projek') <span class="text-red-500 text-xs">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="mb-6 relative" @click.outside="openPengerjaan = false">
                                <label class="block text-[14px] font-bold text-black mb-2">Pengerjaan KP <span
                                        class="text-red-600">*</span></label>
                                <button type="button" @click="if(!isInvited) openPengerjaan = !openPengerjaan"
                                    :class="isInvited ? 'bg-gray-100 text-gray-500 cursor-not-allowed' : 'bg-white'"
                                    class="w-full flex items-center justify-between border border-[#CAC0C0] rounded px-4 py-3 text-[14px] focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">

                                    <span x-text="pengerjaanKp !== '' ? (pengerjaanKp === 'individu' ? 'Individu' : 'Kelompok') : 'Pilih Pengerjaan KP'" class="flex-1 text-left truncate"
                                        :class="pengerjaanKp !== '' ? (isInvited ? 'text-gray-500' : 'text-black') : 'text-gray-400'"></span>

                                    <svg :class="openPengerjaan ? 'rotate-90' : 'rotate-180'"
                                        class="w-5 h-5 text-gray-500 transition-transform duration-200 flex-shrink-0 ml-2 min-w-[20px]"
                                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7">
                                        </path>
                                    </svg>
                                </button>

                                <div x-show="openPengerjaan" x-transition style="display: none;"
                                    class="absolute z-50 w-full mt-1 bg-white border border-[#CAC0C0] rounded shadow-lg overflow-hidden">
                                    <ul class="py-1 text-[14px]">
                                        <li>
                                            <button type="button" @click="pengerjaanKp = 'individu'; openPengerjaan = false"
                                                class="block w-full text-left px-4 py-2 hover:bg-yellow-200 transition-colors"
                                                :class="pengerjaanKp === 'individu' ? 'bg-yellow-300 font-bold' : ''">
                                                Individu
                                            </button>
                                        </li>
                                        <li>
                                            <button type="button" @click="pengerjaanKp = 'kelompok'; openPengerjaan = false"
                                                class="block w-full text-left px-4 py-2 hover:bg-yellow-200 transition-colors"
                                                :class="pengerjaanKp === 'kelompok' ? 'bg-yellow-300 font-bold' : ''">
                                                Kelompok
                                            </button>
                                        </li>
                                    </ul>
                                </div>
                                <input type="hidden" name="pengerjaan_kp" :value="pengerjaanKp" required>
                                @error('pengerjaan_kp') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>

                            <div class="mb-6 relative" x-show="pengerjaanKp === 'kelompok'" x-transition style="display: none;">
                                <label class="block text-[14px] font-bold text-black mb-2">Anggota Kelompok <span
                                        class="text-red-600">*</span></label>
                                
                                <!-- Read-only state for invited users -->
                                <template x-if="isInvited">
                                    <div class="w-full border border-[#CAC0C0] bg-gray-100 rounded px-4 py-3 text-[14px] text-gray-700 min-h-[46px] flex flex-wrap gap-2">
                                        @foreach($anggotaTerpilih ?? [] as $anggota)
                                            <span class="bg-[#E8E5E5] px-3 py-1 rounded-full text-[13px] border border-[#d1cdcd]">
                                                {{ $anggota->mahasiswa->nim ?? 'NIM' }} - {{ $anggota->name }}
                                            </span>
                                        @endforeach
                                    </div>
                                </template>

                                <!-- Interactive multi-select state for creator -->
                                <template x-if="!isInvited">
                                    <div class="relative" @click.outside="openAnggota = false">
                                        <div class="w-full min-h-[46px] border border-[#CAC0C0] bg-white rounded px-4 py-2 flex flex-wrap gap-2 items-center cursor-text" @click="openAnggota = true; $refs.searchAnggota.focus()">
                                            <template x-for="id in selectedAnggota" :key="id">
                                                <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded-[5px] text-[13px] flex items-center gap-1 border border-blue-200">
                                                    <span x-text="getLabel(id)"></span>
                                                    <button type="button" @click.stop="removeAnggota(id)" class="text-blue-500 hover:text-blue-700 focus:outline-none">
                                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                                    </button>
                                                </span>
                                            </template>
                                            <input x-ref="searchAnggota" type="text" x-model="searchAnggota" placeholder="Cari by NIM / Nama..." class="flex-1 min-w-[120px] border-0 focus:ring-0 p-0 m-0 text-[14px] bg-transparent shadow-none outline-none">
                                        </div>
                                        
                                        <div x-show="openAnggota" x-transition style="display: none;" class="absolute z-50 w-full mt-1 bg-white border border-[#CAC0C0] rounded shadow-lg max-h-48 overflow-y-auto">
                                            <ul class="py-1 text-[13px]">
                                                <template x-for="m in filteredMahasiswa" :key="m.id">
                                                    <li>
                                                        <button type="button" @click.stop="m.is_unavailable ? null : toggleAnggota(m.id)" class="block w-full text-left px-4 py-2 transition-colors relative" :class="m.is_unavailable ? 'opacity-50 cursor-not-allowed bg-gray-50' : 'hover:bg-[#E8E5E5]'">
                                                            <div class="flex items-center justify-between pointer-events-none">
                                                                <span x-text="m.label" :class="m.is_unavailable ? 'text-gray-400' : ''"></span>
                                                                <span x-show="m.is_unavailable" class="text-[10px] font-bold text-red-500 bg-red-100 px-2 py-0.5 rounded border border-red-200">Sedang KP</span>
                                                            </div>
                                                        </button>
                                                    </li>
                                                </template>
                                                <template x-if="filteredMahasiswa.length === 0">
                                                    <li class="px-4 py-2 text-gray-500">Pencarian tidak ditemukan...</li>
                                                </template>
                                            </ul>
                                        </div>
                                    </div>
                                </template>
                                
                                <input type="hidden" name="anggota_kelompok_ids" :value="JSON.stringify(selectedAnggota)">
                            </div>

                            <div class="mb-6 relative">
                                <label for="nama_supervisor" class="block text-[14px] font-bold text-black mb-2">Supervisior
                                    <span class="text-red-600">*</span></label>
                                
                                <!-- Searchable Dosen Dropdown (Now for both Internal and External) -->
                                <div class="relative" @click.outside="openDosen = false">
                                    <div class="relative">
                                        <input type="text" name="nama_supervisor" id="nama_supervisor" required
                                            placeholder="Cari Dosen atau ketik nama supervisior..."
                                            x-model="selectedDosenName"
                                            @focus="openDosen = true; searchDosen = ''"
                                            @input="openDosen = true; selectedDosenId = ''; searchDosen = $event.target.value"
                                            class="w-full border border-[#CAC0C0] rounded bg-white px-4 py-3 text-[14px] focus:outline-none focus:ring-1 focus:ring-blue-500">
                                        
                                        <button type="button" @click="openDosen = !openDosen"
                                            class="absolute right-3 top-1/2 -translate-y-1/2">
                                            <svg :class="openDosen ? 'rotate-90' : 'rotate-180'"
                                                class="w-5 h-5 text-gray-400 transition-transform duration-200"
                                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                            </svg>
                                        </button>
                                    </div>

                                    <div x-show="openDosen && filteredDosenFromInput.length > 0" x-transition style="display: none;"
                                        class="absolute z-50 w-full mt-1 bg-white border border-[#CAC0C0] rounded shadow-lg overflow-hidden">
                                        <ul class="py-1 text-[14px] max-h-48 overflow-y-auto">
                                            <template x-for="dosen in filteredDosenFromInput" :key="dosen.id + '-supervisor'">
                                                <li>
                                                    <button type="button" @click="selectDosen(dosen)"
                                                        class="block w-full text-left px-4 py-2 hover:bg-yellow-200 transition-colors"
                                                        :class="selectedDosenId == dosen.id ? 'bg-yellow-100 font-bold' : ''">
                                                        <span x-text="dosen.name"></span>
                                                    </button>
                                                </li>
                                            </template>
                                        </ul>
                                    </div>
                                    <input type="hidden" name="supervisor_internal_id" :value="selectedDosenId">
                                </div>
                                <p class="mt-1 text-[11px] text-gray-500 italic">Ketik nama lengkap jika supervisior bukan dosen (External).</p>
                                
                                @error('nama_supervisor') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                @error('supervisor_internal_id') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>

                            <div class="mb-8">
                                <label for="deskripsi_kp" class="block text-[14px] font-bold text-black mb-2">Deskripsi KP <span
                                        class="text-red-600">*</span></label>
                                <textarea name="deskripsi_kp" id="deskripsi_kp" required rows="5"
                                    placeholder="Deskripsikan singkat tentang projrk KP Kamu"
                                    class="w-full border border-[#CAC0C0] rounded bg-white px-4 py-3 text-[14px] focus:outline-none focus:ring-1 focus:ring-blue-500 resize-none">{{ old('deskripsi_kp') }}</textarea>
                                @error('deskripsi_kp') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>

                            <div class="flex justify-center mt-8 relative pb-8">
                                <button type="submit"
                                    class="w-full sm:w-auto bg-[#008000] hover:bg-green-700 text-white font-bold h-[40px] px-8 rounded-[30px] text-[14px] flex items-center justify-center shadow-md gap-2 transition-colors">
                                    <svg class="w-4 h-4 transform -rotate-45 mb-1" fill="currentColor" viewBox="0 0 20 20"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path
                                            d="M10.894 2.553a1 1 0 00-1.788 0l-7 14a1 1 0 001.169 1.409l5-1.429A1 1 0 009 15.571V11a1 1 0 112 0v4.571a1 1 0 00.725.962l5 1.428a1 1 0 001.17-1.408l-7-14z">
                                        </path>
                                    </svg>
                                    SUBMIT
                                </button>

                                <div class="absolute bottom-0 flex justify-center gap-2">
                                    <div class="w-1.5 h-1.5 bg-black rounded-full"></div>
                                    <div class="w-1.5 h-1.5 bg-gray-300 rounded-full"></div>
                                </div>
                            </div>
                        </form>
                    </div>
            @endif
        </div>
</x-dashboard-layout>