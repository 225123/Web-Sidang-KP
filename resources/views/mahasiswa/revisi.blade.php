<x-dashboard-layout header="Revisi Sidang KP" :userName="auth()->user()->name" roleName="MAHASISWA">
    <x-slot:sidebar>
        @include('mahasiswa.components.sidebar', ['active' => 'revisi'])
    </x-slot>

        

    <div class="mt-6 max-w-5xl mx-auto" x-data="{ uploadType: '', fileName: '', linkDrive: '' }">
        @if($sidang && $sidang->status_kelulusan === 'Lulus Dengan Revisi')
        <div class="bg-[#EAEFFF] border border-[#BACDFB] rounded-[10px] p-4 flex items-start gap-4 shadow-sm mb-8">
            <div class="bg-[#7896F8] w-6 h-6 rounded-full flex items-center justify-center text-white shrink-0 shadow-sm font-serif italic text-sm mt-0.5">i</div>
            <p class="text-[14px] text-black font-medium leading-relaxed">
                Anda dinyatakan Lulus dengan Revisi. Unggah Berkas Revisi (Maksimal 5MB) atau sertakan Link Google Drive. Form ini hanya dapat disubmit satu kali.
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
        @if($errors->any())
            <div class="mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative">
                <strong class="font-bold">Terjadi Kesalahan:</strong>
                <ul class="mt-1 list-disc list-inside text-sm">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="bg-[#D9D9D9] rounded-[10px] p-8 shadow-sm mb-12">
            <h3 class="text-[18px] font-bold text-black mb-6">Informasi Mahasiswa & Pengumpulan Berkas</h3>

            <div class="grid grid-cols-[180px_auto] gap-y-3 text-[14px] font-medium text-black">
                <div>Nama</div>
                <div class="sentence-case">: {{ strtolower($sidang->mahasiswa->user->name ?? '-') }}</div>

                <div>NIM</div>
                <div>: {{ $sidang->mahasiswa->nim ?? '-' }}</div>

                <div>Judul KP</div>
                <div>: {{ $sidang->pendaftaranKp->judul_kp ?? '-' }}</div>

                <div>Jadwal Sidang</div>
                <div>: {{ \Carbon\Carbon::parse($sidang->tanggal_sidang)->locale('id')->isoFormat('D MMMM Y') }} ({{ \Carbon\Carbon::parse($sidang->waktu_mulai_sidang)->format('H:i') }} - {{ \Carbon\Carbon::parse($sidang->waktu_selesai_sidang)->format('H:i') }} WIB)</div>

                <div>Ruangan Sidang</div>
                <div>: {{ $sidang->ruang_sidang ?? '-' }}</div>

                <div>Nilai Akhir Sidang</div>
                <div>: {{ $sidang->nilai_akhir_display !== null ? number_format((float)$sidang->nilai_akhir_display, 2) : '-' }} ({{ $sidang->grade_display ?? '-' }})</div>



                @if(!$sidang->status_revisi || !in_array(strtolower($sidang->status_revisi), ['menunggu', 'disahkan', 'diterima']))
                    <div class="pt-6">Upload Berkas Revisi</div>
                    <div class="pt-6 flex flex-col gap-1.5">
                        <div class="flex items-center gap-2">
                            <span class="mr-2">:</span>
                            <form id="formAjukan" action="{{ route('mahasiswa.revisi.store') }}" method="POST" enctype="multipart/form-data" class="flex-1 flex items-center gap-4">
                                @csrf
    
                                <div class="relative flex items-center gap-2" x-show="uploadType === 'file' || uploadType === ''">
                                    <input type="file" name="file_revisi" id="file_revisi" accept=".pdf" class="hidden" 
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
                                    <button type="button" @click="$refs.fileInput.click()" class="bg-[#F0F0F0] border border-gray-300 text-gray-600 text-[13px] px-4 py-1.5 rounded-[20px] flex items-center gap-2 hover:bg-gray-200 transition-colors">
                                        <svg class="w-4 h-4 text-[#8A9CFF]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
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
                                    <input type="url" name="link_revisi" x-model="linkDrive" @input="uploadType = (linkDrive.trim() !== '') ? 'link' : ''; fileName = ''" placeholder="Link GDrive jika > 5MB..." class="w-full bg-white border border-gray-300 text-[13px] px-3 py-1.5 rounded-[5px] focus:ring-1 focus:ring-blue-500 outline-none">
                                </div>
                            </form>
                        </div>
                        <div class="ml-[22px]">
                            <span class="text-[12px] text-red-500 italic font-medium">*Batas akhir: {{ \Carbon\Carbon::parse($sidang->tanggal_sidang)->addDays(5)->locale('id')->isoFormat('D MMMM Y') }}</span>
                        </div>
                    </div>
                @else
                    <div class="pt-6">Upload Berkas Revisi</div>
                    <div class="pt-6 flex flex-col gap-1.5">
                        <div class="flex items-center gap-2">
                            <span class="mr-2">:</span>
                            <div class="bg-white border border-gray-300 text-gray-700 text-[13px] pl-3 pr-2 py-1.5 rounded-[20px] inline-flex items-center gap-2 shadow-sm w-max max-w-full">
                                @if($sidang->file_revisi)
                                    <svg class="w-4 h-4 text-red-500 shrink-0" fill="currentColor" viewBox="0 0 24 24"><path d="M11.362 2c4.156 0 2.638 6 2.638 6s6-1.65 6 2.457v11.543h-16v-20h7.362zm.827-2h-10.189v24h20v-14.386c0-2.391-6.648-9.614-9.811-9.614zm4.811 13h-10v-1h10v1zm0 2h-10v1h10v-1zm0 3h-10v1h10v-1z"/></svg>
                                    <a href="{{ storage_url($sidang->file_revisi) }}" target="_blank" class="hover:underline hover:text-blue-600 font-medium truncate max-w-[200px]" title="{{ basename($sidang->file_revisi) }}">
                                        {{ basename($sidang->file_revisi) }}
                                    </a>
                                @elseif($sidang->link_revisi)
                                    <svg class="w-4 h-4 text-blue-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path></svg>
                                    <a href="{{ $sidang->link_revisi }}" target="_blank" class="hover:underline hover:text-blue-600 font-medium truncate max-w-[200px]" title="{{ $sidang->link_revisi }}">
                                        Link Google Drive
                                    </a>
                                @endif
                                
                                @if(!in_array(strtolower($sidang->status_revisi), ['disetujui', 'verified', 'disahkan', 'diterima']))
                                    @if(!isset($isReadOnly) || !$isReadOnly)
                                    <div class="h-4 border-l border-gray-300 mx-1 shrink-0"></div>
                                    <form id="form-delete-revisi-{{ $sidang->id }}" action="{{ route('mahasiswa.revisi.destroy', $sidang->id) }}" method="POST" class="inline m-0 p-0">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" onclick="window.showGlobalConfirm('Hapus Berkas', 'Apakah Anda yakin ingin menghapus berkas revisi ini dan mengunggah ulang?', () => document.getElementById('form-delete-revisi-{{ $sidang->id }}').submit())" class="text-gray-400 hover:text-red-500 hover:bg-red-50 p-1 rounded-full transition-colors flex items-center justify-center shrink-0" title="Hapus dan unggah ulang">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                        </button>
                                    </form>
                                    @endif
                                @endif
                            </div>
                        </div>
                        <div class="ml-[22px]">
                            <span class="text-[12px] text-gray-500 italic">Tanggal unggah: <span x-text="formatFullDate('{{ $sidang->tanggal_revisi }}')"></span></span>
                        </div>
                    </div>


                @endif
            </div>

            <!-- Catatan Revisi Section -->
            <div class="mt-6 pt-6 border-t border-gray-300">
                <h4 class="text-[14px] font-bold text-gray-800 mb-2 flex items-center gap-2">
                    <svg class="w-4 h-4 text-blue-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                    Catatan Revisi
                </h4>
                <div class="text-[13px] text-gray-700 leading-relaxed font-normal whitespace-pre-line bg-white/60 p-4 rounded-[6px] border border-gray-200 min-h-[60px]">
                    {!! !empty($sidang->catatan_sidang) && $sidang->catatan_sidang !== '-' ? nl2br(e($sidang->catatan_sidang)) : '<span class="text-gray-400 italic">Tidak ada catatan dari dosen penguji.</span>' !!}
                </div>
            </div>
        </div>

        <hr class="border-t-2 border-[#D9D9D9] mb-12">

        <div class="text-center pb-12">
            <h2 class="text-[18px] font-bold text-black mb-4">Status Pemeriksaan Berkas Revisi</h2>

            @if(!$sidang->status_revisi || !in_array(strtolower($sidang->status_revisi), ['menunggu', 'disahkan', 'diterima']))
                <p class="text-[14px] text-gray-700 font-medium mb-8">Klik 'Submit Revisi' untuk mengirimkan berkas revisi Anda ke Dosen Penguji 1.</p>
                @if(auth()->user()->mahasiswa->is_aktif && (!isset($isReadOnly) || !$isReadOnly))
                <button type="submit" form="formAjukan" class="bg-[#008000] hover:bg-green-700 text-white font-bold text-[14px] px-8 py-2.5 rounded-full shadow-md flex items-center justify-center gap-2 mx-auto transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    SUBMIT REVISI
                </button>
                @elseif(isset($isReadOnly) && $isReadOnly)
                <div class="bg-gray-200 text-gray-500 font-bold text-[14px] px-8 py-2.5 rounded-full shadow-sm flex items-center justify-center mx-auto w-max">
                    Read Only - Periode Lampau
                </div>
                @else
                <div class="bg-red-100 text-red-600 font-bold text-[14px] px-8 py-2.5 rounded-full shadow-sm flex items-center justify-center mx-auto w-max">
                    Status Anda Tidak Aktif (Mode Pelihat)
                </div>
                @endif
            @elseif($sidang->status_revisi === 'Menunggu')
                <div class="inline-flex flex-col items-center justify-center p-8 border-2 border-yellow-400 bg-yellow-50 rounded-lg">
                    <svg class="w-12 h-12 text-yellow-500 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <h3 class="text-lg font-bold text-yellow-700 uppercase">SEDANG DIPERIKSA</h3>
                    <p class="text-sm text-yellow-600 mt-2">Berkas Anda sedang dalam tahap pemeriksaan oleh Dosen Penguji 1.</p>
                    <p class="text-[11px] text-yellow-800/60 mt-4 font-medium italic">
                        Berhasil diunggah pada: <span x-text="formatFullDate('{{ $sidang->tanggal_revisi }}')"></span>
                    </p>
                </div>
            @elseif($sidang->status_revisi === 'Disahkan' || $sidang->status_revisi === 'Diterima')
                <div class="inline-flex flex-col items-center justify-center p-8 border-2 border-green-500 bg-green-50 rounded-lg">
                    <svg class="w-12 h-12 text-green-500 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <h3 class="text-lg font-bold text-green-700 uppercase">REVISI DISAHKAN</h3>
                    <p class="text-sm text-green-600 mt-2">Selamat, revisi Anda telah diperiksa dan dinyatakan sah.</p>
                    <p class="text-[11px] text-green-800/60 mt-4 font-medium italic">
                        Berhasil diunggah pada: <span x-text="formatFullDate('{{ $sidang->tanggal_revisi }}')"></span>
                    </p>
                </div>
            @endif
        </div>
        @else
            <!-- Empty state -->
            <div class="bg-white rounded-[10px] p-8 shadow-sm flex flex-col items-center justify-center min-h-[400px] border border-gray-200">
                <svg class="w-20 h-20 text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                <h3 class="text-[18px] font-bold text-gray-900 mb-2">Tidak Ada Revisi Aktif</h3>
                <p class="text-[14px] text-gray-500 text-center max-w-md leading-relaxed">
                    @if(isset($isPastPeriod) && $isPastPeriod)
                        Anda tidak memiliki jadwal revisi pada periode ini. Halaman ini hanya diperuntukkan bagi mahasiswa yang dinyatakan "Lulus Dengan Revisi".
                    @else
                        Anda tidak memiliki jadwal revisi aktif saat ini. Halaman ini hanya diperuntukkan bagi mahasiswa yang dinyatakan "Lulus Dengan Revisi".
                    @endif
                </p>
            </div>
        @endif
    </div>

    <script>
        function formatFullDate(dateString) {
            if (!dateString) return '-';
            const safeDateString = dateString.includes(' ') ? dateString.replace(' ', 'T') : dateString;
            const date = new Date(safeDateString);
            return date.toLocaleDateString('id-ID', { 
                weekday: 'long', 
                day: 'numeric', 
                month: 'long', 
                year: 'numeric' 
            }) + ' ' + date.toLocaleTimeString('id-ID', {
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit'
            }) + ' WIB';
        }

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
