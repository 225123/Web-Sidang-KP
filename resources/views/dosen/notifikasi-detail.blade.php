<x-dashboard-layout header="" userName="{{ auth()->user()->name }}" roleName="DOSEN">
    <x-slot:sidebar>
        @include('dosen.components.sidebar', ['active' => 'notifikasi'])
    </x-slot>

    <div class="mt-8 px-4 w-full">
        <div class="max-w-5xl mx-auto">
            <!-- Custom Original Position Title with Back Button -->
            <div class="flex items-center gap-4 mb-6">
                <a href="{{ route('dosen.notifikasi') }}" class="w-8 h-8 flex items-center justify-center rounded-full bg-gray-200 hover:bg-gray-300 transition-colors text-gray-700">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"></path></svg>
                </a>
                <h2 class="text-[22px] font-bold text-black border-none m-0">Detail Notifikasi</h2>
            </div>

            <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden mb-12">
                <div class="px-8 py-10">
                    
                    <!-- 1. Sender Info Row -->
                    <div class="flex items-start justify-between mb-3 border-b border-gray-50 pb-3">
                        <div class="flex items-center gap-4">
                            <!-- Avatar -->
                            <div class="w-12 h-12 rounded-full bg-blue-600 flex items-center justify-center text-white font-bold text-[18px] shadow-sm">
                                {{ strtoupper(substr($notifikasi->sender->name ?? 'S', 0, 1)) }}
                            </div>
                            <div class="flex flex-col">
                                <div class="flex items-center gap-2">
                                    <span class="font-bold text-[15px] text-[#202124]">{{ $notifikasi->sender->name ?? 'Sistem' }}</span>
                                    <span class="text-[13px] text-[#5f6368]">&lt;{{ $notifikasi->sender->email ?? 'noreply@sistem.com' }}&gt;</span>
                                </div>
                                <div class="flex items-center gap-1 text-[13px] text-[#5f6368] mt-0.5">
                                    <span>kepada : <span class="text-[#5f6368] mt-0.5">saya</span></span>
                                </div>
                            </div>
                        </div>
                        <div class="text-[13px] text-[#5f6368] font-medium">
                            {{ $notifikasi->created_at->isoFormat('DD MMM YYYY, HH:mm') }} ({{ $notifikasi->created_at->diffForHumans() }})
                        </div>
                    </div>

                    <!-- 2. Subject -->
                    <div class="mb-8">
                        <h1 class="text-[18px] font-bold text-[#202124] leading-tight">
                            {{ $notifikasi->judul }}
                        </h1>
                    </div>

                    <!-- 3. Content Area -->
                    <div class="text-[15px] text-black leading-relaxed whitespace-pre-wrap min-h-[150px] font-sans">{{ $notifikasi->pesan }}</div>

                    <!-- Attachments Section (Gmail Style) -->
                    @if($notifikasi->file_path)
                    <div class="mt-6 pt-4 border-t border-gray-100">
                        <p class="text-[14px] font-bold text-[#202124] mb-5 flex items-center gap-2 uppercase tracking-wide">
                            <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path></svg>
                            1 Lampiran
                        </p>
                        
                        <!-- Attachment Card -->
                        <div class="group relative w-[240px] bg-[#f8f9fa] rounded-lg border border-gray-200 overflow-hidden hover:shadow-lg transition-all duration-300">
                            <!-- Thumbnail Area -->
                            <div class="h-[140px] bg-white flex items-center justify-center border-b border-gray-100 overflow-hidden">
                                @php
                                    $ext = pathinfo($notifikasi->file_path, PATHINFO_EXTENSION);
                                    $isImage = in_array(strtolower($ext), ['jpg', 'jpeg', 'png', 'gif', 'webp']);
                                @endphp
                                
                                @if($isImage)
                                    <img src="{{ asset('storage/'.$notifikasi->file_path) }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                                @else
                                    <svg class="w-16 h-16 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                                @endif

                                <!-- Hover Overlay -->
                                <div class="absolute inset-0 bg-black/50 opacity-0 group-hover:opacity-100 flex items-center justify-center gap-6 transition-opacity duration-300">
                                    <a href="{{ asset('storage/'.$notifikasi->file_path) }}" target="_blank" class="p-3 bg-white/20 hover:bg-white/40 rounded-full text-white transition-all transform hover:scale-110" title="Lihat">
                                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                    </a>
                                    <a href="{{ asset('storage/'.$notifikasi->file_path) }}" download class="p-3 bg-white/20 hover:bg-white/40 rounded-full text-white transition-all transform hover:scale-110" title="Download">
                                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                                    </a>
                                </div>
                            </div>
                            <!-- Filename Area -->
                            <div class="px-4 py-3 flex items-center gap-3 bg-white">
                                <div class="p-1.5 bg-red-500 rounded text-white flex-shrink-0">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                                </div>
                                <span class="text-[12px] font-bold text-[#202124] truncate" title="{{ basename($notifikasi->file_path) }}">
                                    {{ basename($notifikasi->file_path) }}
                                </span>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
        <div class="h-20"></div>
    </div>
</x-dashboard-layout>
