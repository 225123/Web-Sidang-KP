<x-dashboard-layout header="Bimbingan Dosen" userName="{{ auth()->user()->name }}" roleName="MAHASISWA">
    <x-slot:sidebar>
        @include('mahasiswa.components.sidebar', ['active' => 'bimbingan-dosen'])
    </x-slot>

    <style>
        [x-cloak] { display: none !important; }
        .custom-scrollbar::-webkit-scrollbar { width: 5px; height: 5px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: #f1f1f1; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #ccc; border-radius: 10px; }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #999; }
    </style>

            

    <div x-data="{
        isModalOpen: false,
        previewImage: null,
        newImagePreview: null,
        handleFileUpload(event) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = (e) => {
                    this.newImagePreview = e.target.result;
                };
                reader.readAsDataURL(file);
            }
        },
        removeFile() {
            this.newImagePreview = null;
            document.getElementById('fileUpload').value = '';
        }
    }" class="mt-6">
        <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4 mb-8">
            <div class="flex-1 bg-[#EAEFFF] border border-[#BACDFB] rounded-[10px] p-4 flex items-center gap-4 shadow-sm w-full lg:w-auto">
                <div class="bg-[#7896F8] w-6 h-6 rounded-full flex items-center justify-center text-white shrink-0 shadow-sm font-serif italic text-sm">i</div>
                <p class="text-[13px] text-black font-medium leading-relaxed">
                    Pastikan Anda mencatat setiap kegiatan bimbingan dengan Dosen Pembimbing Anda. Minimal bimbingan yang diterima adalah 12 kali.
                </p>
            </div>
            
            <div class="flex items-center gap-3 shrink-0 w-full lg:w-auto overflow-x-auto pb-2 lg:pb-0">
                <a href="{{ route('mahasiswa.bimbingan-dosen.export-pdf') }}" target="_blank"
                    class="bg-[#EA3323] hover:bg-red-700 text-white font-bold text-[11px] px-6 py-2.5 rounded-full shadow-md flex items-center gap-2 transition-all uppercase tracking-wide whitespace-nowrap">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    Export PDF
                </a>
                @if(auth()->user()->mahasiswa->is_aktif)
                <button @click="isModalOpen = true"
                    class="bg-[#FFFF1A] hover:bg-yellow-400 text-black font-bold text-[11px] px-6 py-2.5 rounded-full shadow-md flex items-center gap-2 transition-all uppercase tracking-wide whitespace-nowrap">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Tambah Bimbingan
                </button>
                @else
                <button disabled
                    class="bg-gray-200 text-gray-500 font-bold text-[11px] px-6 py-2.5 rounded-full shadow-md flex items-center gap-2 uppercase tracking-wide whitespace-nowrap cursor-not-allowed">
                    Mode Pelihat
                </button>
                @endif
            </div>
        </div>

        <div class="bg-white rounded-[15px] border border-gray-200 shadow-sm overflow-hidden p-6 mb-12">
            <h3 class="font-bold text-black text-[15px] mb-6 uppercase tracking-tight">Riwayat Bimbingan :</h3>

            @if(session('success'))
                <div class="mb-6 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-[10px] flex items-center gap-3 shadow-sm animate-pulse">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <span class="text-sm font-bold">{{ session('success') }}</span>
                </div>
            @endif

            <div class="overflow-x-auto custom-scrollbar border border-gray-100 rounded-[10px]">
                <table class="w-full text-left border-collapse min-w-[900px]">
                    <thead>
                        <tr class="bg-[#EBEBEB] text-black text-[13px] border-b border-gray-300">
                            <th class="py-4 px-4 font-bold text-center w-[120px]">Tanggal</th>
                            <th class="py-4 px-4 font-bold text-center w-[180px]">Waktu & Tempat</th>
                            <th class="py-4 px-4 font-bold text-left">Detail Pembahasan</th>
                            <th class="py-4 px-4 font-bold text-center w-[120px]">Bukti</th>
                            <th class="py-4 px-4 font-bold text-center w-[180px]">Status</th>
                        </tr>
                    </thead>
                    <tbody class="text-[13px] text-black divide-y divide-gray-100">
                        @forelse($logs ?? [] as $log)
                            @php 
                                /** @var \App\Models\LogBimbingan $log */
                                $materi = [];
                                if ($log && $log->materi_bahasan) {
                                    $materi = json_decode($log->materi_bahasan, true) ?? [];
                                }
                            @endphp
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="py-4 px-4 text-center font-bold">
                                    {{ $log && $log->tanggal ? \Carbon\Carbon::parse($log->tanggal)->format('d/m/Y') : '-' }}
                                </td>
                                <td class="py-4 px-4 text-center">
                                    <div class="text-blue-700 font-bold tracking-tight">{{ $materi['waktuMulai'] ?? '00:00' }} - {{ $materi['waktuSelesai'] ?? '00:00' }}</div>
                                    <div class="text-[11px] text-black/50 italic">{{ $materi['tempat'] ?? '-' }}</div>
                                </td>
                                <td class="py-4 px-4 text-left">
                                    <div class="font-bold text-black mb-1 uppercase text-[12px] tracking-tight">{{ $materi['topik'] ?? '-' }}</div>
                                    <div class="text-black/70 leading-relaxed text-[12px]">{{ Str::limit($materi['detail'] ?? '-', 120) }}</div>
                                </td>
                                <td class="py-4 px-4 text-center">
                                    @if($log && $log->file_progress)
                                        <div class="w-14 h-10 mx-auto rounded-lg border border-gray-200 overflow-hidden shadow-sm hover:ring-2 hover:ring-blue-400 transition-all cursor-pointer bg-gray-50"
                                            @click="previewImage = '{{ storage_url($log->file_progress) }}'">
                                            <img src="{{ storage_url($log->file_progress) }}" class="w-full h-full object-cover">
                                        </div>
                                    @else
                                        <div class="w-12 h-8 mx-auto border border-dashed border-gray-300 rounded flex items-center justify-center text-[9px] text-black/30 font-bold uppercase text-center leading-tight">No<br>Img</div>
                                    @endif
                                </td>
                                <td class="py-4 px-4 text-center">
                                    @if($log && $log->status_approval == 'pending')
                                        <div class="inline-flex items-center gap-1.5 bg-[#FDE68A] text-[#92400E] px-4 py-1.5 rounded-full font-bold text-[10px] uppercase shadow-sm whitespace-nowrap">
                                            <div class="w-1.5 h-1.5 rounded-full bg-[#D4A017] animate-pulse"></div>
                                            Menunggu pengecekan
                                        </div>
                                    @elseif($log && $log->status_approval == 'approved')
                                        <div class="inline-flex items-center gap-1.5 bg-green-100 text-green-700 px-4 py-1.5 rounded-full font-bold text-[10px] uppercase shadow-sm">
                                            <div class="w-1.5 h-1.5 rounded-full bg-green-600"></div> Diterima
                                        </div>
                                    @elseif($log && $log->status_approval == 'rejected')
                                        <div class="inline-flex items-center gap-1.5 bg-red-100 text-red-700 px-4 py-1.5 rounded-full font-bold text-[10px] uppercase shadow-sm">
                                            <div class="w-1.5 h-1.5 rounded-full bg-red-600"></div> Ditolak
                                        </div>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-16 text-center text-gray-400 italic bg-gray-50 uppercase tracking-widest font-medium">Belum ada riwayat bimbingan</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>


        </div>

        <!-- Image Preview Modal -->
        <template x-teleport="body">
            <div x-cloak x-show="previewImage" 
                class="fixed inset-0 z-[9999] flex items-center justify-center bg-black/80 backdrop-blur-sm p-4 transition-all"
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100">
                <div @click.away="previewImage = null" class="relative max-w-4xl max-h-screen transform transition-all"
                     x-transition:enter="transition ease-out duration-300"
                     x-transition:enter-start="scale-95 opacity-0"
                     x-transition:enter-end="scale-100 opacity-100">
                    <button @click="previewImage = null" class="absolute -top-4 -right-4 bg-white text-black rounded-full w-10 h-10 flex items-center justify-center font-bold shadow-2xl hover:bg-gray-200 z-10 transition-colors border-2 border-black/10">✕</button>
                    <img :src="previewImage" class="max-w-full max-h-[90vh] object-contain rounded-lg shadow-2xl">
                </div>
            </div>
        </template>

        <!-- Add Bimbingan Modal -->
        <template x-teleport="body">
            <div x-cloak x-show="isModalOpen" 
                class="fixed inset-0 z-[9998] flex items-center justify-center bg-black/50 backdrop-blur-[3px] p-4 transition-all overflow-y-auto custom-scrollbar">
                <div @click.away="isModalOpen = false" 
                     class="bg-white w-full max-w-2xl rounded-[24px] shadow-[0_20px_70px_-10px_rgba(0,0,0,0.5)] relative my-auto overflow-hidden transform transition-all"
                     x-transition:enter="transition ease-out duration-300"
                     x-transition:enter-start="opacity-0 translate-y-8 scale-95"
                     x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                     x-transition:leave="transition ease-in duration-200"
                     x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                     x-transition:leave-end="opacity-0 translate-y-8 scale-95">
                    
                    <div class="bg-gray-50 border-b border-gray-100 px-8 py-6 flex justify-between items-center">
                        <h2 class="text-xl font-bold text-black uppercase tracking-tight">Tambah Bimbingan Dosen</h2>
                        <button @click="isModalOpen = false" class="p-2 hover:bg-gray-200 rounded-full transition-colors">
                            <svg class="w-6 h-6 text-black/40" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </button>
                    </div>

                <div class="p-8">
                    <form action="{{ route('mahasiswa.bimbingan-dosen.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                        @csrf
                        <div class="grid grid-cols-1 md:grid-cols-[160px_auto] gap-x-4 gap-y-5 items-center">
                            <label class="text-[13px] font-bold text-black uppercase">Hari / Tanggal</label>
                            <input type="date" name="tanggal" required class="w-full bg-[#F5F5F5] border-gray-200 text-black text-sm rounded-[10px] py-3 px-4 focus:ring-blue-500 focus:bg-white transition-all">

                            <label class="text-[13px] font-bold text-black uppercase">Waktu</label>
                            <div class="flex gap-2 items-center">
                                <input type="time" name="waktuMulai" required class="flex-1 bg-[#F5F5F5] border-gray-200 text-black text-sm rounded-[10px] py-3 px-4 focus:ring-blue-500 focus:bg-white transition-all text-center">
                                <span class="text-black font-bold">-</span>
                                <input type="time" name="waktuSelesai" required class="flex-1 bg-[#F5F5F5] border-gray-200 text-black text-sm rounded-[10px] py-3 px-4 focus:ring-blue-500 focus:bg-white transition-all text-center">
                            </div>

                            <label class="text-[13px] font-bold text-black uppercase">Tempat</label>
                            <input type="text" name="tempat" placeholder="Contoh: Lab Komputer 1 / Zoom Meeting" required class="w-full bg-[#F5F5F5] border-gray-200 text-black text-sm rounded-[10px] py-3 px-4 focus:ring-blue-500 focus:bg-white transition-all">

                            <label class="text-[13px] font-bold text-black uppercase">Topik Pembahasan</label>
                            <input type="text" name="topik" placeholder="Contoh: Revisi Bab 1" required class="w-full bg-[#F5F5F5] border-gray-200 text-black text-sm rounded-[10px] py-3 px-4 focus:ring-blue-500 focus:bg-white transition-all">

                            <label class="text-[13px] font-bold text-black uppercase self-start pt-3">Detail Isi</label>
                            <div class="relative">
                                <textarea name="detail" required rows="6" class="w-full bg-[#F5F5F5] border-gray-200 text-black text-sm rounded-[15px] py-3 px-4 resize-none focus:ring-blue-500 focus:bg-white transition-all placeholder:text-gray-400" placeholder="Jelaskan detail apa saja yang dibahas saat bimbingan..."></textarea>
                                <span class="absolute bottom-3 right-4 text-[10px] text-gray-400 font-bold uppercase tracking-wider bg-white/50 px-2 py-1 rounded">Max 500 kata</span>
                            </div>
                        </div>

                        <div class="mt-8 flex flex-col md:flex-row items-stretch md:items-end justify-between gap-6 pt-6 border-t border-gray-100">
                            <div class="flex items-center gap-4">
                                <div class="relative shrink-0">
                                    <div class="w-16 h-16 bg-[#F5F5F5] border border-dashed border-gray-300 rounded-[12px] flex items-center justify-center cursor-pointer hover:bg-gray-100 transition-colors group" @click="if(!newImagePreview) document.getElementById('fileUpload').click()">
                                        <template x-if="newImagePreview">
                                            <div class="w-full h-full relative rounded-[12px] overflow-hidden">
                                                <img :src="newImagePreview" class="w-full h-full object-cover">
                                                <!-- Modern Remove Button -->
                                                <button type="button" @click.stop="removeFile()" class="absolute inset-0 m-auto w-7 h-7 bg-black/60 hover:bg-red-500 text-white rounded-full flex items-center justify-center opacity-0 group-hover:opacity-100 transition-all duration-200 transform scale-75 group-hover:scale-100 shadow-md">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12"></path></svg>
                                                </button>
                                            </div>
                                        </template>
                                        <template x-if="!newImagePreview">
                                            <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                        </template>
                                    </div>
                                </div>
                                <div class="flex flex-col gap-1">
                                    <button type="button" @click="document.getElementById('fileUpload').click()" class="bg-white border border-gray-300 hover:bg-gray-50 text-black text-[11px] font-bold px-4 py-2 rounded-full shadow-sm flex items-center gap-2 transition-all uppercase tracking-wide">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                                        Pilih Bukti
                                    </button>
                                    <span class="text-[10px] text-gray-400 font-medium italic">Format: JPG, PNG (Max 2MB)</span>
                                </div>
                                <input type="file" id="fileUpload" name="bukti" @change="handleFileUpload" accept="image/*" class="hidden" required>
                            </div>

                            <button type="submit" class="bg-[#2B8130] hover:bg-green-700 text-white font-black text-[13px] px-10 py-3 rounded-full shadow-lg flex items-center justify-center gap-2 transition-all uppercase tracking-widest transform hover:scale-105 active:scale-95">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M10.894 2.553a1 1 0 00-1.788 0l-7 14a1 1 0 001.169 1.409l5-1.429A1 1 0 009 15.571V11a1 1 0 112 0v4.571a1 1 0 00.725.962l5 1.428a1 1 0 001.17-1.408l-7-14z"></path></svg>
                                Submit Logbook
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </template>
    </div>

    <!-- Alpine Script Handled via x-data inline -->
</x-dashboard-layout>