<x-dashboard-layout userName="{{ auth()->user()->name ?? 'Mahasiswa Name' }}" roleName="MAHASISWA">
    <x-slot:sidebar>
        @include('mahasiswa.components.sidebar', ['active' => 'pendaftaran-kp'])
        </x-slot>

        <div class="mt-6 w-full px-4 lg:px-8 pb-12">

            <h2 class="text-2xl font-bold font-inter text-black mb-6">Pendaftaran KP</h2>

            @if(session('success') || isset($existingKp))
                <div class="flex flex-col lg:flex-row justify-end items-start lg:items-center gap-6 mb-10 w-full">
                    <div x-data="{ open: false, selected: 'Genap 2025/2026' }"
                        class="relative w-[212px] flex-shrink-0 lg:mt-0 mt-2">
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
                            class="relative w-[212px] flex-shrink-0 lg:mt-0 mt-2">
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
                        <form action="{{ route('mahasiswa.pendaftaran-kp.store') }}" method="POST" x-data="{ 
                    jenisKp: '{{ old('jenis_instansi', '') }}', 
                    instansiNama: '{{ old('instansi_nama', '') }}',
                    openJenis: false
                }" x-effect="if(jenisKp === 'Internal') { instansiNama = 'Universitas Kristen Krida Wacana'; } else if(jenisKp === 'External' && instansiNama === 'Universitas Kristen Krida Wacana') { instansiNama = ''; }">
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
                                <button type="button" @click="openJenis = !openJenis"
                                    class="w-full flex items-center justify-between border border-[#CAC0C0] rounded bg-white px-4 py-3 text-[14px] focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">

                                    <span x-text="jenisKp !== '' ? jenisKp : 'Pilih Jenis KP'" class="flex-1 text-left truncate"
                                        :class="jenisKp !== '' ? 'text-black' : 'text-gray-400'"></span>

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
                                    :readonly="jenisKp !== 'External'"
                                    class="w-full border border-[#CAC0C0] rounded bg-white px-4 py-3 text-[14px] focus:outline-none focus:ring-1 focus:ring-blue-500 transition-colors"
                                    :class="jenisKp !== 'External' ? 'bg-gray-100 text-gray-500 cursor-not-allowed' : ''">
                                @error('instansi_nama') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>

                            <div class="mb-6" x-show="jenisKp === 'Internal'" x-transition style="display: none;">
                                <label for="dosen_pemberi_projek" class="block text-[14px] font-bold text-black mb-2">Dosen
                                    Pemberi Projek <span class="text-red-600">*</span></label>
                                <input type="text" name="dosen_pemberi_projek" id="dosen_pemberi_projek"
                                    placeholder="Masukan nama Dosen" value="{{ old('dosen_pemberi_projek') }}"
                                    :required="jenisKp === 'Internal'"
                                    class="w-full border border-[#CAC0C0] rounded bg-white px-4 py-3 text-[14px] focus:outline-none focus:ring-1 focus:ring-blue-500">
                                @error('dosen_pemberi_projek') <span class="text-red-500 text-xs">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="mb-6">
                                <label for="nama_supervisor" class="block text-[14px] font-bold text-black mb-2">Supervisior
                                    <span class="text-red-600">*</span></label>
                                <input type="text" name="nama_supervisor" id="nama_supervisor" required
                                    placeholder="Masukan nama supervisior" value="{{ old('nama_supervisor') }}"
                                    class="w-full border border-[#CAC0C0] rounded bg-white px-4 py-3 text-[14px] focus:outline-none focus:ring-1 focus:ring-blue-500">
                                @error('nama_supervisor') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
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
                                    class="bg-[#008000] hover:bg-green-700 text-white font-bold h-[40px] px-8 rounded-[30px] text-[14px] flex items-center justify-center shadow-md gap-2 transition-colors">
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