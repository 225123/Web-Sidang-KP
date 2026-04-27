<x-dashboard-layout header="Detail Data Mahasiswa KP" :userName="auth()->user()->name" roleName="KOORDINATOR KP">
    <x-slot:sidebar>
        @include('koordinator.components.sidebar', ['active' => 'data-mahasiswa'])
    </x-slot>

    <div class="mt-6 max-w-6xl mx-auto space-y-8 pb-12">
        <!-- Header Actions -->
        <div class="flex items-center justify-between">
            <a href="{{ route('koordinator.data-mahasiswa.index') }}" class="inline-flex items-center gap-2 text-gray-600 hover:text-black font-bold text-sm transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"></path></svg>
                Kembali ke Daftar Mahasiswa
            </a>
            <div class="flex gap-3">
                <div class="bg-green-50 border border-green-100 rounded-xl p-3 px-5 text-center shadow-sm">
                    <div class="text-2xl font-black text-green-700">{{ $jumlahDiterima }}</div>
                    <div class="text-[10px] font-bold text-green-600 uppercase mt-1">Bimbingan Selesai</div>
                </div>
                <div class="bg-yellow-50 border border-yellow-100 rounded-xl p-3 px-5 text-center shadow-sm">
                    <div class="text-2xl font-black text-yellow-700">{{ $jumlahBelumDiperiksa }}</div>
                    <div class="text-[10px] font-bold text-yellow-600 uppercase mt-1">Bimbingan Pending</div>
                </div>
            </div>
        </div>

        <!-- Section 1: Profil Mahasiswa & KP -->
        <div class="bg-white rounded-[15px] border border-gray-200 shadow-sm p-8">
            <h3 class="text-[17px] font-bold text-black mb-8 uppercase tracking-tight flex items-center gap-2">
                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                Profil Akademik & Pendaftaran KP
            </h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-12 gap-y-6">
                <div class="space-y-4">
                    <div class="grid grid-cols-[140px_auto] gap-2 text-[14px]">
                        <span class="text-gray-500 font-medium">Nama Lengkap</span>
                        <span class="text-black font-bold">: {{ $pendaftaran->mahasiswa->user->name ?? '-' }}</span>
                    </div>
                    <div class="grid grid-cols-[140px_auto] gap-2 text-[14px]">
                        <span class="text-gray-500 font-medium">NIM</span>
                        <span class="text-black font-bold">: {{ $pendaftaran->mahasiswa->nim ?? '-' }}</span>
                    </div>
                    <div class="grid grid-cols-[140px_auto] gap-2 text-[14px]">
                        <span class="text-gray-500 font-medium">Jenis Pengerjaan</span>
                        <span class="text-black font-bold">: {{ ucfirst($pendaftaran->pengerjaan_kp) }}</span>
                    </div>
                    <div class="grid grid-cols-[140px_auto] gap-2 text-[14px]">
                        <span class="text-gray-500 font-medium">Dosen Pembimbing</span>
                        <span class="text-blue-700 font-bold">: {{ $pendaftaran->pembimbing->name ?? '-' }}</span>
                    </div>
                </div>

                <div class="space-y-4">
                    <div class="grid grid-cols-[140px_auto] gap-2 text-[14px]">
                        <span class="text-gray-500 font-medium">Instansi</span>
                        <span class="text-black font-bold">: {{ $pendaftaran->instansi_nama }}</span>
                    </div>
                    <div class="grid grid-cols-[140px_auto] gap-2 text-[14px]">
                        <span class="text-gray-500 font-medium">Supervisor</span>
                        <span class="text-black font-bold">: {{ $pendaftaran->supervisorInstansi->nama_supervisor ?? '-' }}</span>
                    </div>
                    <div class="grid grid-cols-[140px_auto] gap-2 text-[14px]">
                        <span class="text-gray-500 font-medium">Judul KP</span>
                        <span class="text-black font-bold leading-relaxed">: {{ $pendaftaran->judul_kp }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Section 2: Log Bimbingan -->
        <div class="bg-white rounded-[15px] border border-gray-200 shadow-sm overflow-hidden">
            <div class="p-6 border-b border-gray-100 bg-gray-50/50">
                <h3 class="font-bold text-black flex items-center gap-2">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                    Riwayat Log Bimbingan (Pemantauan)
                </h3>
            </div>

            <div class="divide-y divide-gray-100">
                @forelse($pendaftaran->logBimbingans->sortByDesc('tanggal') as $log)
                    <div class="p-6 hover:bg-gray-50/80 transition-colors">
                        <div class="flex flex-col lg:flex-row gap-6">
                            <div class="lg:w-1/4 shrink-0">
                                <div class="flex items-center gap-3 mb-2">
                                    <div class="bg-blue-600 text-white font-bold text-xs px-3 py-1 rounded-md shadow-sm">Minggu Ke-{{ $log->minggu_ke }}</div>
                                    <div class="text-gray-400 font-bold text-[11px] uppercase tracking-tighter">{{ \Carbon\Carbon::parse($log->tanggal)->translatedFormat('d F Y') }}</div>
                                </div>
                                <div class="mt-4">
                                    @if($log->status_approval == 'approved')
                                        <div class="inline-flex items-center gap-1.5 text-green-600 font-bold text-xs uppercase tracking-wide">
                                            <div class="w-2 h-2 rounded-full bg-green-600"></div> Disetujui
                                        </div>
                                    @elseif($log->status_approval == 'rejected')
                                        <div class="inline-flex items-center gap-1.5 text-red-600 font-bold text-xs uppercase tracking-wide">
                                            <div class="w-2 h-2 rounded-full bg-red-600"></div> Ditolak
                                        </div>
                                    @else
                                        <div class="inline-flex items-center gap-1.5 text-yellow-600 font-bold text-xs uppercase tracking-wide">
                                            <div class="w-2 h-2 rounded-full bg-yellow-500 animate-pulse"></div> Belum Diperiksa
                                        </div>
                                    @endif
                                </div>
                            </div>
                            <div class="flex-1">
                                <div class="bg-gray-50 rounded-xl p-4 border border-gray-100">
                                    <label class="text-[10px] font-bold text-gray-400 uppercase tracking-widest block mb-2">Uraian Kegiatan</label>
                                    <p class="text-gray-800 text-sm leading-relaxed whitespace-pre-line">{{ $log->kegiatan }}</p>
                                </div>
                                @if($log->bukti_bimbingan)
                                    <div class="mt-4 flex items-center gap-3">
                                        <label class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Lampiran Bukti:</label>
                                        <a href="{{ asset('storage/' . $log->bukti_bimbingan) }}" target="_blank" class="inline-flex items-center gap-1.5 text-blue-600 hover:text-blue-800 font-bold text-xs underline decoration-2 underline-offset-4">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path></svg>
                                            Lihat Dokumen
                                        </a>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="p-20 text-center">
                        <div class="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-4 border border-dashed border-gray-300">
                            <svg class="w-10 h-10 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        </div>
                        <p class="text-gray-400 font-bold uppercase tracking-widest text-sm">Belum ada log bimbingan yang tercatat</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</x-dashboard-layout>
