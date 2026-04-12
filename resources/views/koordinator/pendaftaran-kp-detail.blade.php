<x-dashboard-layout header="" userName="{{ auth()->user()->name ?? 'KOORDINATOR KP' }}" roleName="KOORDINATOR KP">
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
            
            <div class="grid grid-cols-[140px_10px_1fr] md:grid-cols-[200px_10px_1fr] gap-y-4 text-[14px] text-[#1A1A1A] font-medium leading-[24px]">
                
                <!-- Nama -->
                <div>Nama</div>
                <div>:</div>
                <div class="uppercase">{{ $kp->user->name ?? '-' }}</div>
                
                <!-- NIM -->
                <div>NIM</div>
                <div>:</div>
                <div>{{ $kp->user->mahasiswa->nim ?? '-' }}</div>
                
                <!-- Jenis KP -->
                <div>Jenis KP</div>
                <div>:</div>
                <div>{{ $kp->jenis_instansi }}</div>
                
                <!-- Nama Instansi -->
                <div>Nama Instansi</div>
                <div>:</div>
                <div>{{ $kp->instansi_nama }}</div>
                
                <!-- Supervisor -->
                <div>Supervisor</div>
                <div>:</div>
                <div>{{ $kp->supervisorInstansi->nama_supervisor ?? '-' }}</div>
                
                <!-- Judul KP -->
                <div>Judul KP</div>
                <div>:</div>
                <div>{{ $kp->judul_kp }}</div>
                
                <!-- Status KP (Baru/Lanjut) -->
                <div>Status KP</div>
                <div>:</div>
                <div>Baru /Lanjut</div> 
                
                <!-- Detail KP -->
                <div class="align-top pt-2">Detail KP</div>
                <div class="align-top pt-2">:</div>
                <div class="pt-2">
                    <div class="text-[13px] leading-[1.8]">
                        @if($kp->jenis_proyek)
                            {!! nl2br(e($kp->jenis_proyek)) !!}
                        @else
                            <span class="text-gray-400 italic">Tidak ada deskripsi detail KP</span>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Buttons at the bottom right inside the gray box -->
            <div class="flex justify-end gap-3 mt-10" x-data="{ editMode: false }">
                @if($kp->status_kp === 'pending')
                    <form method="POST" action="{{ route('koordinator.pendaftaran-kp.status', $kp->id) }}" class="inline-block">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="status" value="rejected">
                        <button type="button" class="bg-[#EA4335] hover:bg-red-600 transition-colors text-white px-6 py-2 rounded-[5px] text-[13px] font-bold shadow-sm flex items-center justify-center gap-2 w-[110px]" 
                            @click="openModalCatatan($el.closest('form'), 'Tolak Pendaftaran KP?')">
                            <svg class="w-4 h-4 rounded-full border border-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                            Tolak
                        </button>
                    </form>

                    <form method="POST" action="{{ route('koordinator.pendaftaran-kp.status', $kp->id) }}" class="inline-block">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="status" value="approved">
                        <button type="button" class="bg-[#34A853] hover:bg-green-600 transition-colors text-white px-6 py-2 rounded-[5px] text-[13px] font-bold shadow-sm flex items-center justify-center gap-2 w-[110px]" 
                            @click="openModalCatatan($el.closest('form'), 'Sahkan Pendaftaran KP?')">
                            <svg class="w-4 h-4 rounded-full border border-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            Sahkan
                        </button>
                    </form>
                @else
                    <div x-show="!editMode" class="flex items-center gap-3">
                        <button @click="editMode = true" type="button" class="text-gray-500 hover:text-blue-600 transition underline text-[13px] font-semibold mr-2 cursor-pointer">Ubah Status</button>
                        <div class="inline-flex items-center justify-center {{ $kp->status_kp === 'approved' ? 'bg-[#A1DFAC] text-[#1D5E2D]' : 'bg-[#F3A5A1] text-[#711611]' }} px-8 py-2.5 rounded-[5px] font-bold shadow-sm text-[13px]">
                            @if($kp->status_kp === 'approved')
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                                Telah Disahkan
                            @else
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path></svg>
                                Telah Ditolak
                            @endif
                        </div>
                    </div>

                    <div x-show="editMode" style="display: none;" class="flex items-center gap-3">
                        <button @click="editMode = false" type="button" class="text-gray-500 hover:text-gray-800 transition underline text-[13px] font-semibold mr-2 cursor-pointer">Batal</button>
                        <form method="POST" action="{{ route('koordinator.pendaftaran-kp.status', $kp->id) }}" class="inline-block">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="status" value="rejected">
                            <button type="button" class="bg-[#EA4335] hover:bg-red-600 transition-colors text-white px-6 py-2.5 rounded-[5px] text-[13px] font-bold shadow-sm flex items-center justify-center gap-2 w-[110px]" 
                                @click="openModalCatatan($el.closest('form'), 'Ubah Status ke Ditolak?')">
                                <svg class="w-4 h-4 rounded-full border border-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                Tolak
                            </button>
                        </form>
                        <form method="POST" action="{{ route('koordinator.pendaftaran-kp.status', $kp->id) }}" class="inline-block">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="status" value="approved">
                            <button type="button" class="bg-[#34A853] hover:bg-green-600 transition-colors text-white px-6 py-2.5 rounded-[5px] text-[13px] font-bold shadow-sm flex items-center justify-center gap-2 w-[110px]" 
                                @click="openModalCatatan($el.closest('form'), 'Ubah Status ke Disahkan?')">
                                <svg class="w-4 h-4 rounded-full border border-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                Sahkan
                            </button>
                        </form>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-dashboard-layout>
