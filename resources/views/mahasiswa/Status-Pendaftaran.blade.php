<x-dashboard-layout header="Status Pendaftaran" userName="{{ auth()->user()->name ?? 'MAHASISWA' }}" roleName="{{ auth()->user()->mahasiswa->nim ?? 'Mahasiswa' }}">
    <x-slot:sidebar>
        @include('mahasiswa.components.sidebar', ['active' => 'status-pendaftaran'])
    </x-slot>

    <style>
        .counter-body { counter-reset: row-number 0; }
        .data-row .row-number-cell::before {
            counter-increment: row-number;
            content: counter(row-number);
        }
    </style>

    <div class="mt-8 px-2 w-full max-w-[1240px] mx-auto pb-12" x-data="{ 
        modalCatatanOpen: false,
        modalFormEl: null,
        modalPesan: '',
        modalCatatanValue: '',
        openModalCatatan(formElement, pesan) {
            this.modalFormEl = formElement;
            this.modalPesan = pesan;
            this.modalCatatanValue = '';
            this.modalCatatanOpen = true;
        },
        submitModalCatatan() {
            let input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'catatan';
            input.value = this.modalCatatanValue;
            this.modalFormEl.appendChild(input);
            this.modalFormEl.submit();
        }
    }">
        
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

        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4 shadow-sm" role="alert">
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
        @endif

        <div class="bg-[#E6F0FA] border border-[#D0E3F5] rounded-[10px] p-4 lg:p-5 mb-6 flex items-start gap-4 shadow-sm">
            <div class="w-6 h-6 rounded-full bg-[#4285F4] text-white flex items-center justify-center font-bold flex-shrink-0 mt-0.5">
                i
            </div>
            <p class="text-[14px] text-[#1A1A1A] font-medium leading-relaxed m-0 mt-0.5">
                Pantau riwayat pendaftaran Kerja Praktik Anda. Gunakan fitur pencarian dan filter di bawah untuk memudahkan pemantauan.
            </p>
        </div>

        @if(isset($unrespondedInvitation) && $unrespondedInvitation)
        <div class="bg-yellow-50 border border-yellow-400 rounded-[10px] p-4 lg:p-5 mb-6 flex items-start gap-4 shadow-sm relative overflow-hidden">
            <div class="w-6 h-6 rounded-full bg-yellow-400 text-yellow-900 flex items-center justify-center font-bold flex-shrink-0 mt-0.5">
                !
            </div>
            <div class="flex-1">
                <h4 class="text-[14px] font-bold text-yellow-900 mb-1">Panggilan Pendaftaran Kelompok</h4>
                <p class="text-[13px] text-yellow-800 font-medium leading-relaxed m-0">
                    Rekan Anda <strong>{{ $unrespondedInvitation->user->name ?? 'Seseorang' }}</strong> telah menunjuk Anda sebagai anggota kelompok untuk judul KP <span class="italic">"{{ $unrespondedInvitation->judul_kp }}"</span>. 
                    Anda harus mengisi Formulir Pendaftaran agar data Anda masuk ke dalam sistem.
                </p>
            </div>
            <a href="{{ route('mahasiswa.pendaftaran-kp.create') }}" class="bg-yellow-400 hover:bg-yellow-500 text-yellow-900 font-bold px-4 py-2 rounded-[5px] text-[12px] shadow-sm whitespace-nowrap transition-colors mt-2 sm:mt-0 flex-shrink-0">
                Lengkapi Sekarang
            </a>
        </div>
        @endif

        <form method="GET" action="{{ url()->current() }}" class="flex flex-col lg:flex-row justify-between items-center gap-4 mb-8">
            
            <div class="relative w-full lg:w-[400px]">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </div>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search ..." 
                    x-on:keydown.enter="$el.closest('form').submit()"
                    class="border border-[#CAC0C0] rounded-[5px] pl-10 pr-3 py-2 w-full text-[13px] focus:outline-none focus:border-black shadow-sm font-medium">
            </div>

            <div class="flex flex-wrap items-center gap-4 w-full lg:w-auto justify-start lg:justify-end">
                
                <div x-data="{ open: false, val: '{{ request('status', '') }}' }" class="relative w-full sm:w-auto sm:min-w-[155px]">
                    <button @click="open = !open" @click.outside="open = false" type="button" class="w-full flex items-center justify-between gap-3 border border-[#CAC0C0] bg-[#FBFBFB] rounded-[5px] shadow-sm text-[13px] font-medium py-2 px-3 focus:outline-none focus:border-[#4CC098] focus:ring-1 focus:ring-[#4CC098] cursor-pointer text-black">
                        <span x-text="val === '' ? 'Semua Status' : val === 'pending' ? 'Menunggu' : val === 'approved' ? 'Disetujui' : 'Ditolak'"></span>
                        <svg :class="open ? '-rotate-90' : 'rotate-0'" class="w-3.5 h-3.5 transition-transform duration-200 text-gray-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                        </svg>
                    </button>
                    <div x-show="open" x-transition style="display: none;" class="absolute z-50 w-full mt-1 bg-[#FBFBFB] border border-[#CAC0C0] rounded-[5px] shadow-lg overflow-hidden">
                        <ul class="py-1 text-[13px] font-medium text-black">
                            <li><label class="block px-3 py-2 hover:bg-[#E8E5E5] cursor-pointer border-b border-[#CAC0C0]"><input type="radio" name="status" value="" class="hidden" x-model="val" @change="$el.closest('form').submit()">Semua Status</label></li>
                            <li><label class="block px-3 py-2 hover:bg-[#E8E5E5] cursor-pointer"><input type="radio" name="status" value="pending" class="hidden" x-model="val" @change="$el.closest('form').submit()">Menunggu</label></li>
                            <li><label class="block px-3 py-2 hover:bg-[#E8E5E5] cursor-pointer"><input type="radio" name="status" value="approved" class="hidden" x-model="val" @change="$el.closest('form').submit()">Disetujui</label></li>
                            <li><label class="block px-3 py-2 hover:bg-[#E8E5E5] cursor-pointer"><input type="radio" name="status" value="rejected" class="hidden" x-model="val" @change="$el.closest('form').submit()">Ditolak</label></li>
                        </ul>
                    </div>
                </div>
                
                <div x-data="{ open: false, val: '{{ request('jenis', '') }}' }" class="relative w-full sm:w-auto sm:min-w-[155px]">
                    <button @click="open = !open" @click.outside="open = false" type="button" class="w-full flex items-center justify-between gap-3 border border-[#CAC0C0] bg-[#FBFBFB] rounded-[5px] shadow-sm text-[13px] font-medium py-2 px-3 focus:outline-none focus:border-[#4CC098] focus:ring-1 focus:ring-[#4CC098] cursor-pointer text-black">
                        <span x-text="val === '' ? 'Semua Jenis KP' : val === 'internal' ? 'Internal' : 'External'"></span>
                        <svg :class="open ? '-rotate-90' : 'rotate-0'" class="w-3.5 h-3.5 transition-transform duration-200 text-gray-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                        </svg>
                    </button>
                    <div x-show="open" x-transition style="display: none;" class="absolute z-50 w-full mt-1 bg-[#FBFBFB] border border-[#CAC0C0] rounded-[5px] shadow-lg overflow-hidden">
                        <ul class="py-1 text-[13px] font-medium text-black">
                            <li><label class="block px-3 py-2 hover:bg-[#E8E5E5] cursor-pointer border-b border-[#CAC0C0]"><input type="radio" name="jenis" value="" class="hidden" x-model="val" @change="$el.closest('form').submit()">Semua Jenis KP</label></li>
                            <li><label class="block px-3 py-2 hover:bg-[#E8E5E5] cursor-pointer"><input type="radio" name="jenis" value="internal" class="hidden" x-model="val" @change="$el.closest('form').submit()">Internal</label></li>
                            <li><label class="block px-3 py-2 hover:bg-[#E8E5E5] cursor-pointer"><input type="radio" name="jenis" value="external" class="hidden" x-model="val" @change="$el.closest('form').submit()">External</label></li>
                        </ul>
                    </div>
                </div>
                
                <div x-data="{ open: false, val: '{{ request('periode', '') }}' }" class="relative w-full sm:w-auto sm:min-w-[155px]">
                    <button @click="open = !open" @click.outside="open = false" type="button" class="w-full flex items-center justify-between gap-3 border border-[#CAC0C0] bg-[#FBFBFB] rounded-[5px] shadow-sm text-[13px] font-medium py-2 px-3 focus:outline-none focus:border-[#4CC098] focus:ring-1 focus:ring-[#4CC098] cursor-pointer text-black">
                        <span x-text="val === '' ? 'Semua Periode' : val === 'ganjil' ? 'Ganjil' : 'Genap'"></span>
                        <svg :class="open ? '-rotate-90' : 'rotate-0'" class="w-3.5 h-3.5 transition-transform duration-200 text-gray-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                        </svg>
                    </button>
                    <div x-show="open" x-transition style="display: none;" class="absolute z-50 w-full mt-1 bg-[#FBFBFB] border border-[#CAC0C0] rounded-[5px] shadow-lg overflow-hidden">
                        <ul class="py-1 text-[13px] font-medium text-black">
                            <li><label class="block px-3 py-2 hover:bg-[#E8E5E5] cursor-pointer border-b border-[#CAC0C0]"><input type="radio" name="periode" value="" class="hidden" x-model="val" @change="$el.closest('form').submit()">Semua Periode</label></li>
                            <li><label class="block px-3 py-2 hover:bg-[#E8E5E5] cursor-pointer"><input type="radio" name="periode" value="ganjil" class="hidden" x-model="val" @change="$el.closest('form').submit()">Ganjil</label></li>
                            <li><label class="block px-3 py-2 hover:bg-[#E8E5E5] cursor-pointer"><input type="radio" name="periode" value="genap" class="hidden" x-model="val" @change="$el.closest('form').submit()">Genap</label></li>
                        </ul>
                    </div>
                </div>

                <a href="{{ url()->current() }}" class="flex-1 sm:flex-none bg-red-50 text-red-600 hover:bg-red-100 border border-red-200 transition-colors px-4 py-1.5 rounded-[5px] text-[12px] font-bold shadow-sm text-center">Clear</a>

                <a href="{{ route('mahasiswa.pendaftaran-kp.create') }}" class="w-full lg:w-auto mt-2 lg:mt-0 bg-[#FBEC04] hover:bg-yellow-400 transition-colors text-black px-6 py-2 rounded-[5px] text-[13px] font-bold shadow-sm flex items-center justify-center gap-2 whitespace-nowrap ml-0 lg:ml-auto">
                    <svg class="w-4 h-4" fill="none" stroke="black" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path></svg>
                    Mendaftar KP
                </a>
            </div>
            <button type="submit" class="hidden">Search</button>
        </form>

        <h3 class="text-black font-bold text-[15px] mb-4">Riwayat Pendaftaran</h3>

        <div class="overflow-x-auto bg-white rounded-[10px] shadow-sm border border-[#EBEBEB]">
            <table class="w-full min-w-[800px] text-center border-collapse">
                <thead class="bg-[#EEEEEE] text-[13px] font-bold text-black uppercase tracking-wide">
                    <tr>
                        <th class="px-4 py-4 w-[5%] border-b border-[#EBEBEB]">No</th>
                        <th class="px-4 py-4 w-[30%] border-b border-[#EBEBEB] border-l">Judul KP</th>
                        <th class="px-4 py-4 w-[15%] border-b border-[#EBEBEB] border-l">Jenis KP</th>
                        <th class="px-4 py-4 w-[15%] border-b border-[#EBEBEB] border-l">Periode</th>
                        <th class="px-4 py-4 w-[15%] border-b border-[#EBEBEB] border-l">Status</th>
                        <th class="px-4 py-4 w-[20%] border-b border-[#EBEBEB] border-l">Catatan Dosen</th>
                    </tr>
                </thead>
                <tbody class="counter-body text-[13px] font-medium text-black">
                    @forelse($riwayatKp as $kp)
                        <tr class="data-row border-b border-[#EBEBEB] hover:bg-gray-50 transition-colors h-[80px]">

                            <td class="px-4 py-2 border-r border-[#EBEBEB] text-center">
                                <span class="row-number-cell text-gray-700 font-bold"></span>
                            </td>

                            <td class="px-4 py-3 text-left">
                                <div class="font-bold text-[14px] leading-tight mb-3 pl-2">{{ $kp->judul_kp }}</div>
                                <div class="flex items-center gap-2 text-gray-400 text-[11px] font-normal pl-2">
                                    <svg class="w-4 h-4 opacity-70" fill="currentColor" viewBox="0 0 24 24"><path d="M19 2H9c-1.1 0-2 .9-2 2v2H5c-1.1 0-2 .9-2 2v14h18V4c0-1.1-.9-2-2-2zM7 10h2v2H7v-2zm0 4h2v2H7v-2zm0 4h2v2H7v-2zm10 0h-2v2h-2v-2h-2v2h-2v-2h-2v-6h12v6zm0-4h-2v2h-2v-2h-2v2h-2v-2h-2V4h10v10z"></path></svg>
                                    {{ $kp->instansi_nama }}
                                </div>
                            </td>

                            <td class="px-4 py-4 border-l border-[#EBEBEB]">
                                {{ $kp->jenis_instansi }}
                            </td>

                            <td class="px-4 py-4 border-l border-[#EBEBEB]">
                                {{ ($kp->created_at->format('n') > 6 ? 'Ganjil' : 'Genap') . '/' . $kp->created_at->format('Y') }}
                            </td>

                            <td class="px-4 py-4 border-l border-[#EBEBEB] align-middle">
                                <div class="flex justify-center items-center">
                                    @if($kp->status_kp === 'approved')
                                        <div class="inline-flex items-center justify-center bg-[#A1DFAC] text-[#1D5E2D] px-4 py-1.5 rounded-[20px] font-bold w-[120px] shadow-sm text-[11px] h-[32px]">
                                            Disetujui
                                        </div>
                                    @elseif($kp->status_kp === 'rejected')
                                        <div class="inline-flex items-center justify-center bg-[#F3A5A1] text-[#711611] px-4 py-1.5 rounded-[20px] font-bold w-[120px] shadow-sm text-[11px] h-[32px]">
                                            Ditolak
                                        </div>
                                    @else
                                        <div class="inline-flex items-center justify-center bg-[#FDE293] text-[#A67C00] px-4 py-1.5 rounded-[20px] font-bold w-[120px] shadow-sm text-[11px] h-[32px]">
                                            Menunggu
                                        </div>
                                    @endif
                                </div>
                            </td>

                            <td class="px-4 py-4 border-l border-[#EBEBEB] text-gray-500 italic max-w-[200px] break-words">
                                {{ $kp->catatan ?: '-' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-16 text-center bg-white text-gray-500 font-medium">
                                <svg class="w-12 h-12 mx-auto mb-3 opacity-30" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                                Data riwayat pendaftaran tidak ditemukan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="flex flex-col lg:flex-row justify-end items-center mb-8 gap-4 mt-4">
            <div class="flex items-center gap-2 text-[12px] font-medium text-gray-600 ml-auto">
                <span class="mr-4 text-[13px]">{{ $riwayatKp->firstItem() ?? 0 }} - {{ $riwayatKp->lastItem() ?? 0 }} dari {{ $riwayatKp->total() }} entri</span>
                <div class="mt-2">
                    {{ $riwayatKp->appends(request()->query())->links() }}
                </div>
            </div>
        </div>
    </div>
</x-dashboard-layout>