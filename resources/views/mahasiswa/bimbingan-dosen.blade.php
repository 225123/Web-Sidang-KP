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

            <x-slot:headerActions>
        <div x-data="{ open: false, selected: 'Genap 2025/2026' }" class="relative w-[212px] mt-2 md:mt-0">
            <button @click="open = !open" @click.outside="open = false" type="button"
                class="w-full flex items-center justify-between border border-[#CAC0C0] bg-[#FBFBFB] rounded-[5px] shadow-sm text-[13px] font-medium py-1.5 px-3 focus:outline-none focus:border-[#F48200] focus:ring-[#F48200] focus:ring-1 cursor-pointer text-black h-[32px]">

                <span x-text="selected"></span>

                <svg :class="open ? 'rotate-0' : 'rotate-90'"
                    class="w-3.5 h-3.5 text-gray-500 transition-transform duration-200 flex-shrink-0" fill="none"
                    stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                </svg>
            </button>

            <div x-show="open" x-transition style="display: none;"
                class="absolute z-50 w-full mt-1 bg-[#FBFBFB] border border-[#CAC0C0] rounded-[5px] shadow-lg overflow-hidden">
                <ul class="py-1 text-[13px] font-medium text-black">
                    <li>
                        <button @click="selected = 'Genap 2025/2026'; open = false" type="button"
                            class="block w-full text-left px-3 py-2 hover:bg-[#E8E5E5] transition-colors cursor-pointer">
                            Genap 2025/2026
                        </button>
                    </li>
                    <li>
                        <button @click="selected = 'Ganjil 2025/2026'; open = false" type="button"
                            class="block w-full text-left px-3 py-2 hover:bg-[#E8E5E5] transition-colors cursor-pointer">
                            Ganjil 2025/2026
                        </button>
                    </li>
                </ul>
            </div>
            <input type="hidden" name="periode" :value="selected">
        </div>
    </x-slot:headerActions>

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
                <button @click="isModalOpen = true"
                    class="bg-[#FFFF1A] hover:bg-yellow-400 text-black font-bold text-[11px] px-6 py-2.5 rounded-full shadow-md flex items-center gap-2 transition-all uppercase tracking-wide whitespace-nowrap">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Tambah Bimbingan
                </button>
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
                                            @click="previewImage = '{{ asset('storage/' . $log->file_progress) }}'">
                                            <img src="{{ asset('storage/' . $log->file_progress) }}" class="w-full h-full object-cover">
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
        <div x-cloak x-show="previewImage" style="display:none;"
            class="fixed inset-0 z-[100] flex items-center justify-center bg-black/60 transition-opacity backdrop-blur-sm p-4">
            <div @click.away="previewImage = null" class="relative max-w-4xl max-h-screen">
                <button @click="previewImage = null" class="absolute -top-3 -right-3 bg-white text-black rounded-full w-8 h-8 flex items-center justify-center font-bold shadow-lg hover:bg-gray-200 z-10 transition-colors">✕</button>
                <img :src="previewImage" class="max-w-full max-h-[90vh] object-contain rounded-lg border-4 border-white shadow-2xl">
            </div>
        </div>

        <!-- Add Bimbingan Modal -->
        <div x-cloak x-show="isModalOpen" style="display:none;"
            class="fixed inset-0 z-[100] flex items-center justify-center bg-black/40 backdrop-blur-[2px] p-4 transition-all overflow-y-auto">
            <div @click.away="isModalOpen = false" class="bg-white w-full max-w-2xl rounded-[20px] shadow-2xl relative my-8 overflow-hidden transform transition-all scale-100">
                
                <div class="bg-gray-50 border-b border-gray-100 px-8 py-5 flex justify-between items-center">
                    <h2 class="text-xl font-bold text-black uppercase tracking-tight">Tambah Bimbingan Dosen</h2>
                    <button @click="isModalOpen = false" class="text-black/40 hover:text-black transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path></svg>
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
                                <div class="w-16 h-16 bg-[#F5F5F5] border border-dashed border-gray-300 rounded-[12px] overflow-hidden flex items-center justify-center shrink-0 cursor-pointer hover:bg-gray-100 transition-colors" @click="document.getElementById('fileUpload').click()">
                                    <template x-if="newImagePreview">
                                        <img :src="newImagePreview" class="w-full h-full object-cover">
                                    </template>
                                    <template x-if="!newImagePreview">
                                        <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                    </template>
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
        </div>
    </div>

    <!-- Alpine Script Handled via x-data inline -->
</x-dashboard-layout>