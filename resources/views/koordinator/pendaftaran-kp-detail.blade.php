<x-dashboard-layout header="" :hidePeriodSelector="true" userName="{{ auth()->user()->name ?? 'KOORDINATOR KP' }}" roleName="KOORDINATOR KP">
    <x-slot:sidebar>
        @include('koordinator.components.sidebar', ['active' => 'pendaftaran-kp'])
    </x-slot>

    <div class="mt-8 px-2 w-full max-w-[1200px] mx-auto pb-12" x-data="{ 
        modalCatatanOpen: false, modalFormEl: null, modalPesan: '', modalCatatanValue: '',
        openModalCatatan(formElement, pesan) { this.modalFormEl = formElement; this.modalPesan = pesan; this.modalCatatanValue = ''; this.modalCatatanOpen = true; },
        submitModalCatatan() { let input = document.createElement('input'); input.type = 'hidden'; input.name = 'catatan'; input.value = this.modalCatatanValue; this.modalFormEl.appendChild(input); this.modalFormEl.submit(); }
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
        
        <!-- Custom Original Position Title with Back Button -->
        <div class="flex items-center gap-4 mb-6">
            <a href="{{ route('koordinator.pendaftaran-kp') }}" class="w-8 h-8 flex items-center justify-center rounded-full bg-gray-200 hover:bg-gray-300 transition-colors text-gray-700">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"></path></svg>
            </a>
            <h2 class="text-[22px] font-bold text-black border-none m-0">Detail Pendaftaran KP</h2>
        </div>

        <!-- Flash message on action success -->
        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-[5px] relative mb-6 shadow-sm w-full max-w-5xl" role="alert">
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
        @endif
        
        <div class="bg-[#E6E6E6] rounded-[15px] p-6 lg:p-8 w-full max-w-5xl mt-2">
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
                    {{ $kp->status_kp !== 'rejected' ? (($kp->pengerjaan_kp ?? '') == 'sendiri' || ($kp->pengerjaan_kp ?? '') == 'individu' ? 'Individu' : $kp->pengerjaan_kp) : '-' }}
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
                <div class="mb-3 md:mb-0 font-medium text-[15px] md:text-[14px]">{{ $kp->status_kp !== 'rejected' ? $kp->jenis_instansi : '-' }}</div>
                
                <!-- Nama Instansi -->
                <div class="font-bold md:font-medium text-gray-500 md:text-[#1A1A1A]">Nama Instansi</div>
                <div class="hidden md:block">:</div>
                <div class="mb-3 md:mb-0 font-medium text-[15px] md:text-[14px]">{{ $kp->status_kp !== 'rejected' ? $kp->instansi_nama : '-' }}</div>
                
                <!-- Supervisor -->
                <div class="font-bold md:font-medium text-gray-500 md:text-[#1A1A1A]">Supervisor</div>
                <div class="hidden md:block">:</div>
                <div class="mb-3 md:mb-0 font-medium text-[15px] md:text-[14px]">{{ $kp->status_kp !== 'rejected' && $kp->supervisorInstansi ? $kp->supervisorInstansi->nama_supervisor : '-' }}</div>

                @if($kp->jenis_instansi !== 'Internal')
                <!-- Email Supervisor -->
                <div class="font-bold md:font-medium text-gray-500 md:text-[#1A1A1A]">Email Supervisor</div>
                <div class="hidden md:block">:</div>
                <div class="mb-3 md:mb-0 font-medium text-[15px] md:text-[14px]">{{ $kp->status_kp !== 'rejected' && $kp->supervisorInstansi ? $kp->supervisorInstansi->email_supervisor : '-' }}</div>
                @endif
                
                <!-- Judul KP -->
                <div class="font-bold md:font-medium text-gray-500 md:text-[#1A1A1A]">Judul KP</div>
                <div class="hidden md:block">:</div>
                <div class="mb-3 md:mb-0 font-medium text-[15px] md:text-[14px]">{{ $kp->status_kp !== 'rejected' ? $kp->judul_kp : '-' }}</div>
                
                <!-- Status KP (Baru/Lanjut) -->
                <div class="font-bold md:font-medium text-gray-500 md:text-[#1A1A1A]">Status KP</div>
                <div class="hidden md:block">:</div>
                <div class="mb-3 md:mb-0 font-medium text-[15px] md:text-[14px] {{ $kp->is_lanjutan ? 'text-[#EA4335] font-bold' : 'text-[#34A853]' }}">
                    {{ $kp->is_lanjutan ? 'Lanjutan' : 'Baru' }}
                </div> 
                
                <!-- Detail KP -->
                <div class="font-bold md:font-medium text-gray-500 md:text-[#1A1A1A] align-top md:pt-2">Detail KP</div>
                <div class="hidden md:block align-top md:pt-2">:</div>
                <div class="md:pt-2 font-medium text-[15px] md:text-[14px]">
                    <div class="text-[13px] leading-[1.8]">
                        @if($kp->status_kp !== 'rejected' && $kp->jenis_proyek)
                            {!! nl2br(e($kp->jenis_proyek)) !!}
                        @else
                            -
                        @endif
                    </div>
                </div>
            </div>

            <!-- Buttons at the bottom right inside the gray box -->
            <div class="flex justify-end gap-3 mt-10" x-data="{ editMode: false }">
                @if($kp->status_kp === 'pending' && (!isset($isReadOnly) || !$isReadOnly))
                    <form method="POST" action="{{ route('koordinator.pendaftaran-kp.status', $kp->id) }}" class="inline-block">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="status" value="rejected">
                        <button type="button" class="bg-[#EA4335] hover:bg-red-600 transition-colors text-white px-6 py-2 rounded-[5px] text-[13px] font-bold shadow-sm flex items-center justify-center gap-2 w-[110px]" 
                            @click="openModalCatatan($el.closest('form'), 'Tolak Pendaftaran KP?')">
                            Tolak
                        </button>
                    </form>

                    <form method="POST" action="{{ route('koordinator.pendaftaran-kp.status', $kp->id) }}" class="inline-block">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="status" value="approved">
                        <button type="button" class="bg-[#34A853] hover:bg-green-600 transition-colors text-white px-6 py-2 rounded-[5px] text-[13px] font-bold shadow-sm flex items-center justify-center gap-2 w-[110px]" 
                            @click="openModalCatatan($el.closest('form'), 'Sahkan Pendaftaran KP?')">
                            Sahkan
                        </button>
                    </form>
                @else
                    <div class="flex items-center">
                        <a href="{{ route('koordinator.pendaftaran-kp') }}" class="bg-[#EA4335] text-white hover:bg-red-600 transition-colors px-10 py-2.5 rounded-[5px] text-[13px] font-bold shadow-sm flex items-center justify-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"></path></svg>
                            Kembali
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-dashboard-layout>
