<x-dashboard-layout header="Pendaftaran Sidang KP" userName="{{ auth()->user()->name }}" roleName="MAHASISWA">
    <x-slot:sidebar>
        @include('mahasiswa.components.sidebar', ['active' => 'pendaftaran-sidang'])
        </x-slot>

        

        <div class="mt-6">
            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4">
                    {{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4">
                    {{ session('error') }}
                </div>
            @endif
            @if($errors->any())
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4">
                    <ul class="list-disc pl-5">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Kotak info dipindah ke dalam logic view masing-masing agar hanya muncul saat form aktif -->

            @if(!$isVerifiedByDosen)
                <div
                    class="bg-yellow-100 border border-yellow-400 text-yellow-800 px-6 py-4 rounded-xl text-center font-bold mb-6">
                    Anda belum mendapatkan Persetujuan Sidang dari Dosen Pembimbing. Silakan urus Surat Persetujuan terlebih
                    dahulu pada menu "Persetujuan Sidang KP".
                </div>
            @elseif($pengajuan && in_array($pengajuan->status_koordinator, ['pending', 'verified']))
                {{-- Status Card Jika Sudah Submit (Berhasil) --}}
                <div class="flex flex-col items-center justify-center mt-12 w-full text-center">
                    <svg class="w-28 h-28 mb-4 text-[#008000]" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                        <path
                            d="M12 2l2.4 2.6L18 4l.6 3.4 3.4.6L20 10.4 22 14l-2.6 2.4L20 20l-3.4-.6L13.2 22 12 19.6 9.6 22 6.2 21.4 5.6 18 2 17.4 4 14 2 10.4l2.6-2.4L4 4l3.4.6L10.8 2 12 4.4z">
                        </path>
                        <polyline points="8 12 11 15 16 9" stroke-width="2.5"></polyline>
                    </svg>

                    <h3 class="text-[17px] font-bold text-black mb-2">
                        {{ $pengajuan->status_koordinator === 'verified' ? 'Pendaftaran Sidang Disetujui' : 'Kamu Telah Berhasil Mendaftar' }}
                    </h3>
                    <p class="text-[14px] text-[#1A1A1A] font-medium">Informasi selanjutnya akan diumumkan oleh koordinator
                        KP melalui Email atau Notifikasi</p>
                </div>

            @else
                {{-- Form Submit Berkas (Muncul saat Unsubmitted atau Rejected) --}}

                <div
                    class="bg-[#EAEFFF] border border-[#BACDFB] rounded-[10px] p-4 flex items-start gap-4 shadow-sm mb-6 mt-2">
                    <div
                        class="bg-[#7896F8] w-6 h-6 rounded-full flex items-center justify-center text-white shrink-0 shadow-sm font-serif italic text-sm mt-0.5">
                        i</div>
                    <p class="text-[14px] text-black font-medium leading-relaxed">
                        Unggah Berkas yang diperlukan dan klik 'Submit Berkas' untuk mendapatkan verifikasi persetujuan
                        sidang dari Koordinator KP
                    </p>
                </div>

                @if($pengajuan && $pengajuan->status_koordinator == 'rejected')
                    <div
                        class="bg-red-50 border-l-4 border-red-500 text-red-800 px-6 py-5 rounded-r-xl shadow-sm mb-6 flex items-start gap-4">
                        <div class="mt-0.5">
                            <svg class="w-6 h-6 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z">
                                </path>
                            </svg>
                        </div>
                        <div>
                            <h4 class="font-bold text-[15px] mb-1">Berks Pendaftaran Anda Dikembalikan (Ditolak)</h4>
                            <p class="text-[13px] mb-2">Alasan Penolakan:
                                <strong>{{ $pengajuan->koordinator_feedback }}</strong></p>
                            <p class="text-[12px] italic text-red-600">Silakan unggah ulang dokumen yang telah Anda perbaiki
                                melalui formulir di bawah ini.</p>
                        </div>
                    </div>
                @endif

                <form id="formPendaftaranSidang" action="{{ route('mahasiswa.pendaftaran-sidang.store') }}" method="POST" enctype="multipart/form-data"
                    class="bg-[#eeeeee] rounded-xl p-8 mb-6 mt-4">
                    @csrf
                    <h3 class="text-lg font-bold text-gray-800 mt-0 mb-6">Kelengkapan Dokumen Sidang</h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5 mb-8">
                        <!-- Laporan KP -->
                        <div x-data="{ hasFile: false }"
                            class="bg-white rounded-lg p-5 flex flex-col justify-between relative shadow-sm border border-gray-200">
                            <div class="text-[13px] font-bold text-black mb-3">Laporan KP <span
                                    class="text-red-500 ml-1">*</span></div>
                            <div class="flex items-start gap-2">
                                <input type="file" name="file_laporan" required accept=".pdf" x-ref="laporan" 
                                    @change="window.handleFileSelection($event, 5242880, (isValid) => { hasFile = isValid; })"
                                    class="flex-1 text-[11px] text-gray-600 mb-3 cursor-pointer file:mr-4 file:py-1.5 file:px-3 file:rounded-md file:border-0 file:text-[11px] file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                                <button type="button" x-cloak x-show="hasFile" @click="$refs.laporan.value = ''; hasFile = false" 
                                    class="shrink-0 p-1 mt-1 text-gray-400 hover:text-red-500 hover:bg-red-50 rounded-md transition-colors" title="Hapus File">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                </button>
                            </div>
                            <div class="text-[10px] text-gray-400 font-medium">Maks. 5 MB (PDF)</div>
                        </div>

                        <!-- Laporan Bimbingan KP -->
                        <div x-data="{ hasFile: false }"
                            class="bg-white rounded-lg p-5 flex flex-col justify-between relative shadow-sm border border-gray-200">
                            <div class="text-[13px] font-bold text-black mb-3">Laporan Bimbingan KP <span
                                    class="text-red-500 ml-1">*</span></div>
                            <div class="flex items-start gap-2">
                                <input type="file" name="file_log_bimbingan" required accept=".pdf" x-ref="bimbingan" 
                                    @change="window.handleFileSelection($event, 5242880, (isValid) => { hasFile = isValid; })"
                                    class="flex-1 text-[11px] text-gray-600 mb-3 cursor-pointer file:mr-4 file:py-1.5 file:px-3 file:rounded-md file:border-0 file:text-[11px] file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                                <button type="button" x-cloak x-show="hasFile" @click="$refs.bimbingan.value = ''; hasFile = false" 
                                    class="shrink-0 p-1 mt-1 text-gray-400 hover:text-red-500 hover:bg-red-50 rounded-md transition-colors" title="Hapus File">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                </button>
                            </div>
                            <div class="text-[10px] text-gray-400 font-medium">Maks. 5 MB (PDF)</div>
                        </div>


                        <!-- Berkas Lainnya -->
                        <div x-data="{ hasFile: false }"
                            class="bg-white rounded-lg p-5 flex flex-col justify-between relative shadow-sm border border-gray-200">
                            <div class="text-[13px] font-bold text-black mb-3">Berkas Lainnya</div>
                            <div class="flex items-start gap-2">
                                <input type="file" name="file_berkas_lainnya" accept=".pdf" x-ref="lainnya" 
                                    @change="window.handleFileSelection($event, 5242880, (isValid) => { hasFile = isValid; })"
                                    class="flex-1 text-[11px] text-gray-600 mb-3 cursor-pointer file:mr-4 file:py-1.5 file:px-3 file:rounded-md file:border-0 file:text-[11px] file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                                <button type="button" x-cloak x-show="hasFile" @click="$refs.lainnya.value = ''; hasFile = false" 
                                    class="shrink-0 p-1 mt-1 text-gray-400 hover:text-red-500 hover:bg-red-50 rounded-md transition-colors" title="Hapus File">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                </button>
                            </div>
                            <div class="text-[10px] text-gray-400 font-medium">Opsional - Maks. 5 MB (PDF)</div>
                        </div>

                        <!-- Link Drive -->
                        <div
                            class="bg-white rounded-lg p-5 flex flex-col justify-between relative shadow-sm border border-gray-200">
                            <div class="text-[13px] font-bold text-black mb-3">Link Google Drive</div>
                            <input type="url" name="link_drive" placeholder="https://drive.google.com/..."
                                class="border border-gray-300 rounded-[5px] px-3 py-2 text-[12px] focus:outline-none focus:border-blue-500 w-full bg-gray-50 mb-1">
                            <div class="text-[10px] text-gray-400 mt-1">Jika file > 5MB</div>
                        </div>

                        <!-- Link Github -->
                        <div
                            class="bg-white rounded-lg p-5 flex flex-col justify-between relative shadow-sm border border-gray-200">
                            <div class="text-[13px] font-bold text-black mb-3">Link Project (Github) <span class="text-red-500 ml-1">*</span></div>
                            <input type="url" name="link_github" required placeholder="https://github.com/..."
                                class="border border-gray-300 rounded-[5px] px-3 py-2 text-[12px] focus:outline-none focus:border-blue-500 w-full bg-gray-50 mb-1">
                        </div>

                        <!-- Link Deploy -->
                        <div
                            class="bg-white rounded-lg p-5 flex flex-col justify-between relative shadow-sm border border-gray-200 lg:col-start-1 lg:col-end-4 md:col-start-1 md:col-end-3">
                            <div class="text-[13px] font-bold text-black mb-3">Link Deploy / Publish Project <span class="text-red-500 ml-1">*</span></div>
                            <input type="url" name="link_deploy" required placeholder="https://myapp.com/..."
                                class="border border-gray-300 rounded-[5px] px-3 py-2 text-[12px] focus:outline-none focus:border-blue-500 w-full sm:w-[50%] bg-gray-50 mb-1">
                        </div>
                    </div>

                    <div class="flex justify-center mt-6">
                        <button type="submit"
                            class="w-full sm:w-auto bg-[#008000] hover:bg-green-700 text-white font-bold h-[45px] px-10 rounded-full text-[14px] flex items-center justify-center shadow-md gap-3 transition-colors cursor-pointer border-none">
                            <svg class="w-4 h-4 transform -rotate-45 mb-1" fill="currentColor" viewBox="0 0 20 20">
                                <path
                                    d="M10.894 2.553a1 1 0 00-1.788 0l-7 14a1 1 0 001.169 1.409l5-1.429A1 1 0 009 15.571V11a1 1 0 112 0v4.571a1 1 0 00.725.962l5 1.428a1 1 0 001.17-1.408l-7-14z">
                                </path>
                            </svg>
                            SUBMIT BERKAS
                        </button>
                    </div>
                </form>
            @endif

        </div>

        <script>
            window.handleFileSelection = function(event, maxSize, callback) {
                const files = event.target.files;
                if (files && files.length > 0) {
                    if (files[0].size > maxSize) {
                        window.dispatchEvent(new CustomEvent('show-alert', {
                            detail: {
                                title: 'Ukuran File Terlalu Besar',
                                message: 'Maksimal berukuran 5 MB.',
                                type: 'danger'
                            }
                        }));
                        event.target.value = '';
                        callback(false, '');
                    } else {
                        callback(true, files[0].name);
                    }
                } else {
                    callback(false, '');
                }
            };
        </script>
</x-dashboard-layout>