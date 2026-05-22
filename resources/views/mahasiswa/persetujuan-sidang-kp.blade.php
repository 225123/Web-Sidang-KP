<x-dashboard-layout header="Persetujuan Sidang KP" :userName="auth()->user()->name" roleName="MAHASISWA">
    <x-slot:sidebar>
        @include('mahasiswa.components.sidebar', ['active' => 'persetujuan-sidang'])
        </x-slot>

        

        <div class="mt-6 max-w-5xl mx-auto" x-data="{ uploadType: '', fileName: '', linkDrive: '' }">

            <div class="bg-[#EAEFFF] border border-[#BACDFB] rounded-[10px] p-4 flex items-start gap-4 shadow-sm mb-8">
                <div
                    class="bg-[#7896F8] w-6 h-6 rounded-full flex items-center justify-center text-white shrink-0 shadow-sm font-serif italic text-sm mt-0.5">
                    i</div>
                <p class="text-[14px] text-black font-medium leading-relaxed">
                    Unggah Laporan KP dan klik 'Ajukan' untuk mendapatkan verifikasi persetujuan sidang dari Dosen
                    Pembimbing.
                </p>
            </div>

            @if(session('success'))
                <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif
            @if(session('error'))
                <div class="mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative">
                    <span class="block sm:inline">{{ session('error') }}</span>
                </div>
            @endif

            @if ($errors->any())
                <div class="mb-6 bg-[#FFEAEA] border border-red-400 rounded-[10px] p-4 shadow-sm">
                    <div class="flex items-center gap-3 mb-2">
                        <div class="bg-red-500 w-6 h-6 rounded-full flex items-center justify-center text-white shrink-0">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                        </div>
                        <h4 class="font-bold text-red-800 text-[14px]">Terjadi Kesalahan:</h4>
                    </div>
                    <ul class="list-disc pl-11 text-[13px] text-red-700 font-medium">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if($persetujuan && $persetujuan->status_verifikasi == 'rejected')
                <div class="mb-6 bg-[#FFEAEA] border border-red-400 rounded-[10px] p-4 flex gap-4 shadow-sm items-start">
                    <div class="bg-red-500 w-6 h-6 rounded-full flex items-center justify-center text-white shrink-0 mt-0.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                        </svg>
                    </div>
                    <div>
                        <h4 class="font-bold text-red-800 text-[14px]">Pengajuan Ditolak</h4>
                        <p class="text-[13px] text-red-700 font-medium mt-1 mb-2">Catatan Penolakan:</p>
                        <p class="text-[14px] text-red-900 border-l-2 border-red-500 pl-3 italic">"{{ $persetujuan->dosen_feedback }}"</p>
                        <p class="text-[12px] text-red-600 mt-3 font-semibold">* Silakan unggah laporan yang telah direvisi pada form di bawah ini dan klik Ajukan kembali.</p>
                    </div>
                </div>
            @endif

            <div class="bg-[#D9D9D9] rounded-[10px] p-8 shadow-sm mb-12">
                <h3 class="text-[18px] font-bold text-black mb-6">Informasi Mahasiswa</h3>

                <div class="grid grid-cols-[180px_auto] gap-y-3 text-[14px] font-medium text-black">
                    <div>Nama</div>
                    <div>: {{ $mahasiswaData->user->name ?? 'GEOVANO YANSEN JAS' }}</div>

                    <div>NIM</div>
                    <div>: {{ $mahasiswaData->nim ?? '412023024' }}</div>

                    <div>Judul KP</div>
                    <div>: {{ $ownKp->judul_kp ?? ($pendaftaran->judul_kp ?? 'Website Sidang KP') }}</div>

                    <div>Dosen Pembimbing</div>
                    <div>: {{ $pendaftaran->pembimbing->name ?? 'Belum ada pembimbing' }}</div>

                    <div>Bimbingan Disahkan</div>
                    <div>: {{ $totalBimbingan ?? '12' }}/12</div>



                    @if(!$persetujuan || in_array($persetujuan->status_verifikasi, ['Ditolak', 'rejected']))
                        <div class="pt-2">Upload Laporan KP</div>
                        <div class="pt-2 flex items-center gap-2">
                            <span class="mr-2">:</span>
                            <form id="formAjukan" action="{{ route('mahasiswa.persetujuan-sidang.store') }}" method="POST"
                                enctype="multipart/form-data" class="flex-1 flex items-center gap-4">
                                @csrf

                                <div class="relative flex items-center gap-2" x-show="uploadType === 'file' || uploadType === ''">
                                    <input type="file" name="file_laporan" id="file_laporan" accept=".pdf" class="hidden"
                                        @change="window.handleFileSelection($event, 5242880, (isValid, name) => { 
                                            if(isValid && name) { 
                                                fileName = name; 
                                                uploadType = 'file'; 
                                            } else { 
                                                fileName = ''; 
                                                uploadType = ''; 
                                            } 
                                        })"
                                        x-ref="fileInput">

                                    <button type="button" @click="$refs.fileInput.click()"
                                        class="bg-[#F0F0F0] border border-gray-300 text-gray-600 text-[13px] px-4 py-1.5 rounded-[20px] flex items-center gap-2 hover:bg-gray-200 transition-colors">
                                        <svg class="w-4 h-4 text-[#8A9CFF]" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12">
                                            </path>
                                        </svg>
                                        <span x-text="fileName === '' ? 'Pilih file (Max 5MB)' : fileName"></span>
                                    </button>
                                    <button type="button" x-cloak x-show="fileName !== ''" @click="fileName = ''; uploadType = ''; $refs.fileInput.value = ''" 
                                        class="shrink-0 p-1 text-gray-400 hover:text-red-500 hover:bg-red-50 rounded-md transition-colors" title="Hapus File">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                    </button>
                                </div>

                                <span class="text-sm font-medium" x-show="uploadType === ''">atau</span>

                                <div class="flex-1" x-show="uploadType === 'link' || uploadType === ''">
                                    <input type="url" name="link_drive" x-model="linkDrive" @input="uploadType = (linkDrive.trim() !== '') ? 'link' : ''; fileName = ''" placeholder="Link GDrive jika > 5MB..."
                                        class="w-full bg-white border border-gray-300 text-[13px] px-3 py-1.5 rounded-[5px] focus:ring-1 focus:ring-blue-500 outline-none">
                                </div>
                            </form>
                        </div>
                    @else
                        <div class="pt-2">Upload Laporan KP</div>
                        <div class="pt-2 flex items-center gap-2">
                            <span class="mr-2">:</span>
                            <div
                                class="bg-[#F0F0F0] border border-gray-300 text-gray-500 text-[13px] px-4 py-1.5 rounded-[20px] flex items-center gap-2 opacity-70 cursor-not-allowed">
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12">
                                    </path>
                                </svg>
                                Terkunci (Sedang/Telah Diproses)
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <hr class="border-t-2 border-[#D9D9D9] mb-12">

            <div class="text-center pb-12">
                @if(!$persetujuan || in_array($persetujuan->status_verifikasi, ['Ditolak', 'rejected']))
                    <h2 class="text-[18px] font-bold text-black mb-4">Ajukan Sidang KP</h2>
                    <p class="text-[14px] text-black font-medium mb-8">Klik 'Ajukan' untuk mendapatkan Surat Persetujuan
                        Dosen Pembimbing sebagai syarat pendaftaran sidang KP</p>
                    <button type="button" onclick="document.getElementById('formAjukan').submit()"
                        class="bg-[#008000] hover:bg-green-700 text-white font-bold text-[14px] px-8 py-2.5 rounded-full shadow-md flex items-center justify-center gap-2 mx-auto transition-colors">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path
                                d="M10.894 2.553a1 1 0 00-1.788 0l-7 14a1 1 0 001.169 1.409l5-1.429A1 1 0 009 15.571V11a1 1 0 112 0v4.571a1 1 0 00.725.962l5 1.428a1 1 0 001.17-1.408l-7-14z">
                            </path>
                        </svg>
                        AJUKAN
                    </button>

                @elseif($persetujuan->status_verifikasi == 'pending' || $persetujuan->status_verifikasi == 'Menunggu')
                    <h2 class="text-[18px] font-bold text-black mb-4">Ajukan Sidang KP</h2>
                    <p class="text-[14px] text-black font-medium mb-8">Klik 'Ajukan' untuk mendapatkan Surat Persetujuan
                        Dosen Pembimbing sebagai syarat pendaftaran sidang KP</p>
                    <button disabled
                        class="bg-[#A7F3D0] text-[#065F46] font-bold text-[14px] px-8 py-2.5 rounded-full flex items-center justify-center gap-2 mx-auto cursor-not-allowed">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                        </svg>
                        Telah Mengajukan
                    </button>

                @elseif($persetujuan->status_verifikasi == 'Disetujui' || $persetujuan->status_verifikasi == 'verified')
                    <h2 class="text-[18px] font-bold text-black mb-6">Surat Persetujuan Sidang KP</h2>

                    <div class="flex flex-col items-center justify-center gap-6">

                        <div
                            class="w-full max-w-[650px] h-[600px] border border-gray-300 shadow-md rounded overflow-hidden bg-gray-50 flex items-center justify-center relative">
                            {{--
                            Pastikan route 'mahasiswa.persetujuan-sidang.cetak' di controller mengembalikan
                            response()->stream() atau return $pdf->stream()
                            bukan return $pdf->download(), agar PDF bisa di-render oleh iframe browser.
                            --}}
                            <iframe src="{{ route('mahasiswa.persetujuan-sidang.cetak', $persetujuan->id) }}"
                                type="application/pdf" class="w-full h-full" title="Preview Surat Persetujuan">
                            </iframe>
                        </div>

                        <a href="{{ route('mahasiswa.persetujuan-sidang.cetak', ['id' => $persetujuan->id, 'download' => 'true']) }}"
                            class="bg-[#D9D9D9] hover:bg-gray-400 text-black font-bold text-[14px] px-8 py-2.5 rounded-[5px] flex items-center gap-2 transition-colors shadow-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                            </svg>
                            Unduh File
                        </a>
                    </div>
                @endif
            </div>
        </div>

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