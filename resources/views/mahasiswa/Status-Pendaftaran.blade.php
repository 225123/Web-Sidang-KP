<x-dashboard-layout header="Status Pendaftaran" userName="{{ auth()->user()->name }}" roleName="MAHASISWA">
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

    <div class="mt-8 px-2 w-full max-w-[1240px] mx-auto pb-12">
        
        <!-- Info Banner -->
        <div class="bg-[#E6F0FA] border border-[#D0E3F5] rounded-[10px] p-4 lg:p-5 mb-8 flex items-start gap-4 shadow-sm">
            <div class="w-6 h-6 rounded-full bg-[#4285F4] text-white flex items-center justify-center font-bold flex-shrink-0 mt-0.5">
                i
            </div>
            <p class="text-[14px] text-[#1A1A1A] font-medium leading-relaxed m-0 mt-0.5">
                Pantau riwayat pendaftaran Kerja Praktik Anda secara berkala untuk melihat status verifikasi oleh Koordinator.
            </p>
        </div>

        @if(isset($unrespondedInvitation) && $unrespondedInvitation)
        <div class="bg-yellow-50 border border-yellow-400 rounded-[10px] p-4 lg:p-5 mb-8 flex items-start gap-4 shadow-sm relative overflow-hidden">
            <div class="w-6 h-6 rounded-full bg-yellow-400 text-yellow-900 flex items-center justify-center font-bold flex-shrink-0 mt-0.5">
                !
            </div>
            <div class="flex-1">
                <h4 class="text-[14px] font-bold text-yellow-900 mb-1">Panggilan Pendaftaran Kelompok</h4>
                <p class="text-[13px] text-yellow-800 font-medium leading-relaxed m-0">
                    Rekan Anda <strong>{{ $unrespondedInvitation->user->name ?? 'Seseorang' }}</strong> telah menunjuk Anda sebagai anggota kelompok untuk judul KP <span class="italic">"{{ $unrespondedInvitation->judul_kp }}"</span>. 
                </p>
            </div>
            <a href="{{ route('mahasiswa.pendaftaran-kp.create') }}" class="bg-yellow-400 hover:bg-yellow-500 text-yellow-900 font-bold px-4 py-2 rounded-[5px] text-[12px] shadow-sm whitespace-nowrap transition-colors mt-2 sm:mt-0 flex-shrink-0">
                Lengkapi Sekarang
            </a>
        </div>
        @endif

        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4 shadow-sm" role="alert">
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
        @endif

        <!-- Main Card (Koordinator Style) -->
        <div class="bg-white rounded-[15px] border border-gray-200 shadow-sm overflow-hidden p-6 mb-8">
            <!-- Header section matching Koordinator -->
            <div class="flex flex-col sm:flex-row justify-between items-start gap-4 mb-8">
                <div>
                    <h3 class="text-[18px] font-bold text-black uppercase tracking-tight">Status Pendaftaran Mahasiswa</h3>
                    <p class="text-[12px] text-black/60 font-medium mt-1">Rekapitulasi riwayat pendaftaran KP pribadi Anda</p>
                </div>
                <div class="flex gap-2 shrink-0">
                    @php
                        $latest = $riwayatKp->first();
                    @endphp
                    @if(!$latest || $latest->status_kp === 'rejected')
                        <a href="{{ route('mahasiswa.pendaftaran-kp.create') }}" class="bg-[#FBEC04] hover:bg-yellow-400 text-black px-4 py-2 rounded-[5px] text-[12px] font-bold flex items-center justify-center sm:justify-start w-full sm:w-auto shadow-sm transition-colors gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path></svg>
                            Mendaftar KP Baru
                        </a>
                    @else
                        @php
                            $statusClass = 'bg-gray-100 text-gray-700';
                            $statusLabel = 'Belum Mendaftar';
                            
                            if ($latest) {
                                if ($latest->status_kp === 'approved') {
                                    $statusClass = 'bg-[#34A853] text-white';
                                    $statusLabel = 'Sudah Mendaftar (Disetujui)';
                                } else {
                                    $statusClass = 'bg-[#FBBC05] text-black';
                                    $statusLabel = 'Menunggu Verifikasi';
                                }
                            }
                        @endphp
                        <div class="{{ $statusClass }} rounded-[5px] px-3 py-1.5 flex items-center gap-2 shadow-sm border border-black/5">
                            <span class="text-[11px] font-bold uppercase tracking-wider">{{ $statusLabel }}</span>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Filter & Action Section -->
            <form method="GET" action="{{ url()->current() }}" class="flex flex-col lg:flex-row justify-between items-center gap-6 mb-6">
                <div class="flex flex-wrap items-center gap-4 w-full sm:w-auto">
                    <div class="relative flex-1 sm:w-[300px]">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        </div>
                        <input type="text" name="search" value="{{ request('search') }}" class="block w-full pl-9 pr-4 py-2 border border-gray-300 rounded-[5px] text-[12px] text-black focus:ring-[#4285F4]" placeholder="Cari berdasarkan judul atau instansi...">
                    </div>

                    <div x-data="{ openFilter: false, val: '{{ request('status', '') }}' }" class="relative w-full sm:w-[180px] z-[60]">
                        <button type="button" @click="openFilter = !openFilter" @click.outside="openFilter = false" class="w-full text-[12px] border border-gray-300 rounded-[5px] py-2 px-3 bg-white text-black font-medium focus:ring-[#4285F4] flex justify-between items-center text-left shadow-sm">
                            <span x-text="val === '' ? 'Status Pendaftaran' : (val === 'pending' ? 'Menunggu' : (val === 'approved' ? 'Disetujui' : 'Ditolak'))"></span>
                            <svg :class="openFilter ? 'rotate-0' : 'rotate-90'" class="w-3.5 h-3.5 transition-all duration-200 text-gray-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <div x-show="openFilter" x-transition x-cloak class="absolute w-full mt-1 bg-white border border-gray-300 rounded-[5px] shadow-lg overflow-hidden py-1 z-50">
                            <label class="block px-3 py-2 text-[12px] hover:bg-gray-100 cursor-pointer text-black"><input type="radio" name="status" value="" class="hidden" x-model="val" @change="$el.closest('form').submit()">Semua Status</label>
                            <label class="block px-3 py-2 text-[12px] hover:bg-gray-100 cursor-pointer text-black"><input type="radio" name="status" value="pending" class="hidden" x-model="val" @change="$el.closest('form').submit()">Menunggu</label>
                            <label class="block px-3 py-2 text-[12px] hover:bg-gray-100 cursor-pointer text-black"><input type="radio" name="status" value="approved" class="hidden" x-model="val" @change="$el.closest('form').submit()">Disetujui</label>
                            <label class="block px-3 py-2 text-[12px] hover:bg-gray-100 cursor-pointer text-black"><input type="radio" name="status" value="rejected" class="hidden" x-model="val" @change="$el.closest('form').submit()">Ditolak</label>
                        </div>
                    </div>

                    <div class="flex gap-2 w-full sm:w-auto">
                        <a href="{{ url()->current() }}" class="flex-1 sm:flex-none border border-[#EA4335] bg-[#EA4335] text-white hover:bg-red-600 transition-colors px-4 py-1.5 rounded-[5px] text-[12px] font-bold shadow-sm flex items-center justify-center">
                            Clear Filter
                        </a>
                    </div>
                </div>
            </form>

            <!-- Table with Koordinator aesthetics -->
            <div class="border border-gray-200 rounded-[10px] overflow-hidden">
                <table class="w-full border-collapse text-[13px]">
                    <thead>
                        <tr class="bg-[#EBEBEB] text-black">
                            <th class="py-3 px-4 font-bold text-center w-[60px] border-b border-r border-gray-300 uppercase tracking-wider text-[11px]">No</th>
                            <th class="py-3 px-4 font-bold text-left border-b border-r border-gray-300 uppercase tracking-wider text-[11px]">Proyek & Instansi</th>
                            <th class="py-3 px-4 font-bold text-center border-b border-r border-gray-300 uppercase tracking-wider text-[11px] w-[150px]">Jenis KP</th>

                            <th class="py-3 px-4 font-bold text-center border-b border-gray-300 uppercase tracking-wider text-[11px] w-[180px]">Status Approval</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 counter-body">
                        @forelse($riwayatKp as $kp)
                            <tr class="hover:bg-gray-50 transition-colors data-row">
                                <td class="py-3 px-4 text-center text-black/60 border-r border-gray-200 row-number-cell font-bold"></td>
                                <td class="py-4 px-4 text-left border-r border-gray-200">
                                    <div class="font-bold text-[13px] text-gray-900 mb-1">{{ $kp->judul_kp }}</div>
                                    <div class="flex items-center gap-1.5 text-gray-500 text-[11px] font-medium">
                                        <svg class="w-3.5 h-3.5 opacity-60" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                                        {{ $kp->instansi_nama }}
                                    </div>
                                    @if($kp->catatan)
                                        <div class="mt-2 bg-red-50 text-red-600 p-2 rounded text-[10px] italic border border-red-100">
                                            <strong>Catatan:</strong> {{ $kp->catatan }}
                                        </div>
                                    @endif
                                </td>
                                <td class="py-3 px-4 text-center border-r border-gray-200 font-medium">{{ $kp->jenis_instansi }}</td>

                                <td class="py-3 px-4 text-center">
                                    @if($kp->status_kp === 'approved')
                                        <span class="inline-flex items-center gap-1.5 bg-green-100 text-green-700 px-4 py-1 rounded-full font-bold text-[10px] uppercase">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg> Disetujui
                                        </span>
                                    @elseif($kp->status_kp === 'rejected')
                                        <span class="inline-flex items-center gap-1.5 bg-red-100 text-red-700 px-4 py-1 rounded-full font-bold text-[10px] uppercase">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12"></path></svg> Ditolak
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 bg-yellow-100 text-yellow-700 px-4 py-1 rounded-full font-bold text-[10px] uppercase">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg> Menunggu
                                        </span>
                                    @endif
                                    <div class="text-[10px] text-gray-400 mt-1.5 font-medium italic">Diperbarui: {{ $kp->updated_at->format('d M Y') }}</div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="py-12 text-center text-gray-400 italic font-medium bg-gray-50 tracking-widest">
                                    Riwayat pendaftaran Anda belum tersedia.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination Footer -->
            @if($riwayatKp->total() > $riwayatKp->perPage())
            <div class="px-6 py-4 bg-white flex items-center justify-between border-t border-gray-200">
                <span class="text-[12px] font-medium text-black/50">{{ $riwayatKp->firstItem() }} - {{ $riwayatKp->lastItem() }} dari {{ $riwayatKp->total() }} entri</span>
                <div class="mt-2 text-[12px]">
                    {{ $riwayatKp->appends(request()->query())->links() }}
                </div>
            </div>
            @endif
        </div>
    </div>
</x-dashboard-layout>