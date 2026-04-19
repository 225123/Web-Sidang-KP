<x-dashboard-layout header="Bimbingan Dosen" userName="{{ auth()->user()->name }}" roleName="MAHASISWA">
    <x-slot:sidebar>
        @include('mahasiswa.components.sidebar', ['active' => 'bimbingan-dosen'])
        </x-slot>

        <x-slot:headerActions>
            <div x-data="{ open: false, selected: 'Genap 2025/2026' }" class="relative w-[212px]">
                <button @click="open = !open" @click.outside="open = false" type="button"
                    class="w-full flex items-center justify-between border border-[#CAC0C0] bg-[#FBFBFB] rounded-[5px] shadow-sm text-[13px] font-medium py-2 px-3 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 cursor-pointer text-black">
                    <span x-text="selected"></span>
                    <svg :class="open ? 'rotate-90' : 'rotate-180'"
                        class="w-3.5 h-3.5 text-gray-500 transition-transform duration-200 flex-shrink-0" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </button>
                <div x-show="open" x-transition style="display: none;"
                    class="absolute z-50 w-full bg-[#FBFBFB] border border-[#CAC0C0] rounded-[5px] shadow-lg overflow-hidden">
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
        </x-slot:headerActions>

        <div x-data="bimbinganState()" class="mt-6">
            <div class="flex justify-end items-center gap-3 mb-6 relative">
                <a href="{{ route('mahasiswa.bimbingan-dosen.export-pdf') }}" target="_blank"
                    class="bg-[#EA3323] hover:bg-red-600 text-white font-medium text-[13px] px-4 py-2 rounded-[20px] shadow flex items-center gap-2 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                        </path>
                    </svg>
                    Export PDF
                </a>
                <div x-data="{ filterOpen: false }" class="relative">
                    <button @click="filterOpen = !filterOpen" @click.outside="filterOpen = false"
                        class="bg-transparent hover:bg-gray-100 text-[#666666] font-medium text-[13px] px-3 py-2 rounded shadow-sm border border-[#D9D9D9] flex items-center gap-2 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z">
                            </path>
                        </svg>
                        Filter
                    </button>
                    <div x-show="filterOpen" style="display:none;"
                        class="absolute right-0 mt-1 w-32 bg-white border border-[#D9D9D9] rounded shadow-lg z-10">
                        <ul class="py-1 text-[13px] text-gray-700">
                            <li><a href="#" class="block px-4 py-2 hover:bg-gray-100">Nama</a></li>
                            <li><a href="#" class="block px-4 py-2 hover:bg-gray-100">Tanggal</a></li>
                        </ul>
                    </div>
                </div>
                <button @click="isModalOpen = true"
                    class="bg-[#FFFF1A] hover:bg-yellow-400 text-black font-medium text-[13px] px-4 py-2 rounded shadow flex items-center gap-2 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Tambah Bimbingan
                </button>
            </div>

            <div class="bg-[#ECECEC] rounded-[15px] p-5 shadow-sm border border-[#D9D9D9]">
                <h3 class="font-bold text-black text-[15px] mb-4">Riwayat Bimbingan :</h3>

                @if(session('success'))
                    <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative"
                        role="alert">
                        <span class="block sm:inline">{{ session('success') }}</span>
                    </div>
                @endif
                @if($errors->any())
                    <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                        <ul class="list-disc list-inside">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="overflow-x-auto custom-scrollbar">
                    <table class="w-full text-left border-collapse min-w-[900px]">
                        <thead>
                            <tr class="border-b border-[#D9D9D9] text-[13px] text-black text-center">
                                <th class="py-3 px-2 font-semibold">Tanggal</th>
                                <th class="py-3 px-2 font-semibold">Waktu dan Tempat</th>
                                <th class="py-3 px-2 font-semibold w-[40%]">Logbook Pembahasan</th>
                                <th class="py-3 px-2 font-semibold">Bukti - gambar</th>
                                <th class="py-3 px-2 font-semibold">Status</th>
                            </tr>
                        </thead>
                        <tbody class="text-[12px] text-black">
                            @forelse($logs ?? [] as $log)
                                @php $materi = json_decode($log->materi_bahasan, true); @endphp
                                <tr class="border-b border-[#D9D9D9] hover:bg-[#E2E2E2] transition-colors">
                                    <td class="py-3 px-2 text-center">
                                        {{ \Carbon\Carbon::parse($log->tanggal)->format('d/m/Y') }}</td>
                                    <td class="py-3 px-2 text-center">
                                        {{ $materi['waktuMulai'] ?? '00:00' }}-{{ $materi['waktuSelesai'] ?? '00:00' }}
                                        ({{ $materi['tempat'] ?? '-' }})
                                    </td>
                                    <td class="py-3 px-2">
                                        <strong>{{ $materi['topik'] ?? '-' }}</strong><br>
                                        {{ Str::limit($materi['detail'] ?? '-', 100) }}
                                    </td>
                                    <td class="py-3 px-2 text-center flex justify-center">
                                        @if($log->file_progress)
                                            <div class="w-8 h-8 rounded bg-gray-200 overflow-hidden cursor-pointer shadow-sm hover:opacity-80 transition-opacity"
                                                @click="previewImage = '{{ asset('storage/' . $log->file_progress) }}'">
                                                <img src="{{ asset('storage/' . $log->file_progress) }}"
                                                    class="w-full h-full object-cover">
                                            </div>
                                        @else
                                            <div
                                                class="w-8 h-8 rounded bg-[#8B9BED] shadow-sm flex items-center justify-center text-white text-[10px]">
                                                No<br>Img</div>
                                        @endif
                                    </td>
                                    <td class="py-3 px-2 text-center">
                                        @if($log->status_approval == 'pending')
                                            <div
                                                class="inline-flex items-center gap-2 bg-[#FBD76F] px-3 py-1 rounded-full w-full max-w-[140px] justify-center shadow-sm">
                                                <div class="w-2.5 h-2.5 rounded-full bg-[#D4A017]"></div>
                                                <span
                                                    class="font-medium text-[#7E651D] text-[11px] leading-tight flex-1">Menunggu<br>pengecekan</span>
                                            </div>
                                        @elseif($log->status_approval == 'approved')
                                            <div
                                                class="inline-flex items-center gap-2 bg-[#9BF48F] px-4 py-1.5 rounded-full w-full max-w-[140px] justify-center shadow-sm">
                                                <div class="w-2.5 h-2.5 rounded-full bg-[#46A43B]"></div>
                                                <span class="font-medium text-[#2E6B27]">Diterima</span>
                                            </div>
                                        @elseif($log->status_approval == 'rejected')
                                            <div
                                                class="inline-flex items-center gap-2 bg-[#F17E7E] px-4 py-1.5 rounded-full w-full max-w-[140px] justify-center shadow-sm">
                                                <div class="w-2.5 h-2.5 rounded-full bg-[#C12E2E]"></div>
                                                <span class="font-medium text-[#7D1E1E]">Ditolak</span>
                                            </div>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="py-6 text-center text-gray-500">Belum ada riwayat bimbingan.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-6 flex flex-col items-end text-[12px] text-gray-600 gap-2">
                    <span>Total Bimbingan : {{ count($logs ?? []) }}</span>
                </div>
            </div>

            <div x-show="previewImage" style="display:none;"
                class="fixed inset-0 z-[100] flex items-center justify-center bg-black bg-opacity-70 transition-opacity backdrop-blur-sm p-4">
                <div @click.away="previewImage = null" class="relative max-w-4xl max-h-screen">
                    <button @click="previewImage = null"
                        class="absolute -top-4 -right-4 bg-white text-black rounded-full w-8 h-8 flex items-center justify-center font-bold shadow-lg hover:bg-gray-200">X</button>
                    <img :src="previewImage" class="max-w-full max-h-[90vh] object-contain rounded-md shadow-2xl">
                </div>
            </div>

            <div x-show="isModalOpen" style="display:none;"
                class="fixed inset-0 z-[100] flex items-center justify-center overflow-y-auto w-full h-full bg-black/40 p-4">
                <div @click.away="isModalOpen = false"
                    class="bg-white w-full max-w-2xl rounded-2xl shadow-xl relative my-8 overflow-hidden">

                    <div class="p-8">
                        <h2 class="text-xl font-bold text-black mb-6">Tambah Bimbingan Dosen</h2>

                        <form action="{{ route('mahasiswa.bimbingan-dosen.store') }}" method="POST"
                            enctype="multipart/form-data">
                            @csrf

                            <div class="space-y-4 text-sm font-medium text-black">
                                <div class="flex flex-col sm:flex-row sm:items-center gap-2 sm:gap-0">
                                    <label class="w-full sm:w-[160px]">Hari / Tanggal</label>
                                    <span class="hidden sm:inline mr-4">:</span>
                                    <input type="date" name="tanggal" required
                                        class="flex-1 bg-[#E8E8E8] border-none text-black text-sm rounded py-2 px-3 focus:outline-none focus:ring-1 focus:ring-gray-400">
                                </div>

                                <div class="flex flex-col sm:flex-row sm:items-center gap-2 sm:gap-0">
                                    <label class="w-full sm:w-[160px]">Waktu</label>
                                    <span class="hidden sm:inline mr-4">:</span>
                                    <div class="flex-1 flex gap-2 items-center">
                                        <input type="time" name="waktuMulai" required
                                            class="w-1/2 bg-[#E8E8E8] border-none text-black text-sm rounded py-2 px-3 focus:outline-none focus:ring-1 focus:ring-gray-400">
                                        <span class="flex items-center text-black font-bold">-</span>
                                        <input type="time" name="waktuSelesai" required
                                            class="w-1/2 bg-[#E8E8E8] border-none text-black text-sm rounded py-2 px-3 focus:outline-none focus:ring-1 focus:ring-gray-400">
                                    </div>
                                </div>

                                <div class="flex flex-col sm:flex-row sm:items-center gap-2 sm:gap-0">
                                    <label class="w-full sm:w-[160px]">Tempat</label>
                                    <span class="hidden sm:inline mr-4">:</span>
                                    <input type="text" name="tempat" placeholder="Contoh E302" required
                                        class="flex-1 bg-[#E8E8E8] border-none text-black text-sm rounded py-2 px-3 focus:outline-none focus:ring-1 focus:ring-gray-400">
                                </div>

                                <div class="flex flex-col sm:flex-row sm:items-center gap-2 sm:gap-0">
                                    <label class="w-full sm:w-[160px]">Topik Pembahasan</label>
                                    <span class="hidden sm:inline mr-4">:</span>
                                    <input type="text" name="topik" placeholder="Masukan Judul Topik" required
                                        class="flex-1 bg-[#E8E8E8] border-none text-black text-sm rounded py-2 px-3 focus:outline-none focus:ring-1 focus:ring-gray-400">
                                </div>

                                <div class="flex flex-col sm:flex-row items-start gap-2 sm:gap-0">
                                    <label class="w-full sm:w-[160px] pt-2">Detail Pembahasan</label>
                                    <span class="hidden sm:inline mr-4 pt-2">:</span>
                                    <div class="flex-1 w-full relative">
                                        <textarea name="detail" required rows="6"
                                            class="w-full bg-[#E8E8E8] border-none text-black text-sm rounded py-3 px-3 resize-none focus:outline-none focus:ring-1 focus:ring-gray-400"
                                            placeholder="Masukan isi pembahasan..."></textarea>
                                        <span class="absolute bottom-2 right-3 text-xs text-gray-500">500 kata</span>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-8 flex flex-col sm:flex-row items-start sm:items-end justify-between gap-6">
                                <div class="flex items-center gap-4 relative w-full sm:max-w-[250px]">
                                    <button type="button"
                                        class="bg-white border border-gray-300 hover:bg-gray-50 text-black text-sm font-semibold py-1.5 px-4 rounded shadow-sm flex items-center gap-2 transition-colors"
                                        onclick="document.getElementById('fileUpload').click()">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12">
                                            </path>
                                        </svg>
                                        Upload Bukti
                                    </button>
                                    <input type="file" id="fileUpload" name="bukti" @change="handleFileUpload"
                                        accept="image/*" class="hidden" required>

                                    <div class="w-16 h-16 bg-[#F5F5F5] border border-dashed border-gray-300 rounded overflow-hidden flex items-center justify-center shrink-0"
                                        onclick="document.getElementById('fileUpload').click()" style="cursor:pointer;"
                                        title="Klik untuk mengubah gambar">
                                        <template x-if="newImagePreview">
                                            <img :src="newImagePreview" class="w-full h-full object-cover">
                                        </template>
                                        <template x-if="!newImagePreview">
                                            <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z">
                                                </path>
                                            </svg>
                                        </template>
                                    </div>
                                </div>

                                <button type="submit"
                                    class="w-full sm:w-auto bg-[#2B8130] hover:bg-green-700 text-white font-bold text-sm py-2.5 px-8 rounded-full shadow flex items-center justify-center gap-2 transition-colors">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path
                                            d="M10.894 2.553a1 1 0 00-1.788 0l-7 14a1 1 0 001.169 1.409l5-1.429A1 1 0 009 15.571V11a1 1 0 112 0v4.571a1 1 0 00.725.962l5 1.428a1 1 0 001.17-1.408l-7-14z">
                                        </path>
                                    </svg>
                                    SUBMIT
                                </button>
                            </div>

                        </form>
                    </div>
                </div>
            </div>
        </div>

        <script>
            document.addEventListener('alpine:init', () => {
                Alpine.data('bimbinganState', () => ({
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
                }))
            })
        </script>
</x-dashboard-layout>