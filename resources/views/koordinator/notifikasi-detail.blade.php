<x-dashboard-layout header="Detail Notifikasi" userName="{{ auth()->user()->name }}" roleName="KOORDINATOR KP" hidePeriodSelector="true">
    <x-slot:sidebar>
        @include('koordinator.components.sidebar', ['active' => 'notifikasi'])
    </x-slot>

    <div class="mt-8 px-4 w-full">
        <div class="bg-white border border-gray-300 rounded-[5px] shadow-sm overflow-hidden">
            <!-- Header Nav -->
            <div class="flex items-center px-4 py-2 border-b border-gray-200 bg-gray-50/50">
                <a href="{{ route('koordinator.notifikasi') }}" class="p-2 hover:bg-gray-200 rounded-full transition-colors group" title="Kembali">
                    <svg class="w-5 h-5 text-gray-600 group-hover:text-black" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                </a>
            </div>

            <!-- Content Area -->
            <div class="p-8">
                <!-- Row 1: Sender & Date -->
                <div class="flex items-center justify-between mb-3">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 font-bold text-lg">
                            {{ strtoupper(substr($notifikasi->sender->name ?? 'Sistem', 0, 1)) }}
                        </div>
                        <div class="flex flex-col">
                            <span class="text-[14px] font-bold text-black">{{ $notifikasi->sender->name ?? 'Sistem KP' }}</span>
                            <div class="flex items-center gap-1 text-[13px] text-[#5f6368] mt-0.5">
                                <span>kepada :
                                    <span class="text-[#5f6368] mt-0.5">
                                        {{ $notifikasi->target_role ? 'Semua ' . ucfirst($notifikasi->target_role) : ($notifikasi->receiver->name ?? 'Koordinator') }}
                                    </span>
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="text-[12px] text-[#5f6368]">
                        {{ $notifikasi->created_at->isoFormat('dddd, DD MMMM YYYY') }} 
                        ({{ $notifikasi->created_at->format('H:i') }})
                    </div>
                </div>

                <!-- Row 2: Subject -->
                <div class="mb-8 ml-[52px]">
                    <h1 class="text-[18px] font-bold text-black leading-tight">{{ $notifikasi->judul }}</h1>
                </div>

                <!-- Row 3: Message Body -->
                <div class="ml-[52px] text-[15px] text-black leading-relaxed whitespace-pre-wrap font-sans">{{ $notifikasi->pesan }}</div>

                <!-- Guidance Button for System Notifications -->
                @if($notifikasi->target_url)
                <div class="ml-[52px] mt-10">
                    <a href="{{ route('koordinator.notifikasi.redirect', $notifikasi->id) }}" class="inline-flex items-center gap-2 bg-[#4285F4] hover:bg-blue-700 text-white font-bold text-[13px] px-6 py-3 rounded-[5px] shadow-sm transition-all">
                        <span>Lihat Detail Kegiatan</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                        </svg>
                    </a>
                    <p class="text-[11px] text-gray-500 mt-2 font-medium italic">* Anda akan diarahkan ke halaman terkait untuk memproses kegiatan ini.</p>
                </div>
                @endif

                <!-- Attachment (if exists) -->
                @if($notifikasi->file_path)
                <div class="ml-[52px] mt-10 pt-6 border-t border-gray-100">
                    <div class="text-[13px] font-bold text-black mb-3 flex items-center gap-2">
                        <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" /></svg>
                        Lampiran
                    </div>
                    <a href="{{ asset('storage/' . $notifikasi->file_path) }}" target="_blank" class="inline-flex items-center p-3 border border-gray-200 rounded-[5px] hover:bg-gray-50 transition-colors group max-w-sm">
                        <div class="w-8 h-8 bg-red-50 flex items-center justify-center text-red-600 rounded mr-3">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" /></svg>
                        </div>
                        <div class="flex flex-col">
                            <span class="text-[12px] font-bold text-black truncate">{{ basename($notifikasi->file_path) }}</span>
                            <span class="text-[10px] text-gray-500 uppercase">Klik untuk melihat</span>
                        </div>
                    </a>
                </div>
                @endif
            </div>
        </div>

        <div class="h-20"></div>
    </div>
</x-dashboard-layout>
