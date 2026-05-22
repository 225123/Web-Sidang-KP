<x-dashboard-layout header="DASHBOARD" :userName="auth()->user()->name" roleName="MAHASISWA">
    <x-slot:sidebar>
        @include('mahasiswa.components.sidebar', ['active' => 'dashboard'])
        </x-slot>

        <!-- Header Actions passed to Layout to render nicely next to DASHBOARD text -->
                

        <style>
            /* Custom Native Scrollbar for Webkit */
            .custom-scrollbar::-webkit-scrollbar {
                width: 8px;
            }

            .custom-scrollbar::-webkit-scrollbar-track {
                background: #FFFFFF;
                border: 1px solid #D9D9D9;
                border-radius: 10px;
            }

            .custom-scrollbar::-webkit-scrollbar-thumb {
                background-color: #666666;
                border-radius: 10px;
            }
        </style>

        <!-- Responsive Dashboard Content Layout -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 lg:gap-12 mt-6" x-data="dashboardMhs()">
            <!-- Left Column -->
            <div class="flex flex-col gap-8 lg:gap-10 w-full">
                <!-- Status Kerja Praktik Card -->
                <div class="bg-[#ECECEC] rounded-[30px] p-8 shadow-sm min-h-[436px]">
                    <div class="flex items-center gap-2 mb-6">
                        <h3 class="font-bold text-black text-[17px]">Status Kerja Praktik</h3>
                        <span class="font-medium text-black text-[17px] ml-1">:
                            <span
                                class="{{ $kp['status_raw'] === 'approved' ? 'text-green-600' : ($kp['status_raw'] === 'pending' ? 'text-[#BFA512]' : 'text-red-600') }}">
                                {{ $kp['status_teks'] }}
                            </span>
                        </span>
                    </div>

                    <div class="flex flex-col gap-3.5 text-[13px] font-medium text-black">
                        @if($kp['is_lanjutan'])
                        <div class="flex items-center">
                            <div class="w-[180px]">Status KP</div>
                            <div class="flex-1">: Lanjut <span class="text-[11px] text-black/50 italic">(melanjutkan dari periode sebelumnya)</span></div>
                        </div>
                        @endif
                        <div class="flex">
                            <div class="w-[180px]">Judul Projek KP</div>
                            <div class="flex-1">: {{ $kp['judul'] }}</div>
                        </div>
                        <div class="flex">
                            <div class="w-[180px]">Sumber Instansi</div>
                            <div class="flex-1">: {{ $kp['instansi'] }} ({{ $kp['jenis_instansi'] }})</div>
                        </div>
                        <div class="flex">
                            <div class="w-[180px]">Supervisor</div>
                            <div class="flex-1">: {{ $kp['supervisor'] }}</div>
                        </div>
                        <div class="flex">
                            <div class="w-[180px]">Dosen Pembimbing</div>
                            <div class="flex-1">: {{ $kp['pembimbing'] }}</div>
                        </div>
                        <div class="flex">
                            <div class="w-[180px]">Jumlah Bimbingan</div>
                            <div>: {{ $bimbinganDosen['current'] }} / {{ $bimbinganDosen['target'] }}</div>
                        </div>

                        <div class="flex">
                            <div class="w-[180px]">Progress KP</div>
                            <div>: {{ $progress }} %</div>
                        </div>
                    </div>

                    <div class="mt-8 flex gap-12 items-center justify-center relative left-[-20px]">
                        <!-- Dynamic Progress Chart -->
                        <div class="relative rounded-full overflow-hidden flex-shrink-0"
                            :style="'width: 125px; height: 125px; background: conic-gradient(black 0% ' + currentProgress + '%, #D1C6C6 ' + currentProgress + '% 100%); transition: background 1.5s ease-out;'">
                            <div class="absolute inset-0 z-20 flex flex-col justify-center items-center">
                                <span class="text-[16px] text-white font-bold" x-show="currentProgress > 50"
                                    x-text="currentProgress + '%'"></span>
                                <span class="text-[16px] text-black font-bold" x-show="currentProgress <= 50"
                                    x-text="currentProgress + '%'"></span>
                            </div>
                            <div class="absolute inset-0 m-auto bg-[#ECECEC] rounded-full"
                                style="width: 80px; height: 80px;"></div>
                        </div>

                        <div class="space-y-4">
                            <div class="flex items-center gap-3 group">
                                <div class="w-[14px] h-[14px] bg-black group-hover:scale-110 transition-transform">
                                </div>
                                <span class="text-[13px] text-black font-medium">Tuntas</span>
                            </div>
                            <div class="flex items-center gap-3 group">
                                <div class="w-[14px] h-[14px] bg-[#D1C6C6] group-hover:scale-110 transition-transform">
                                </div>
                                <span class="text-[13px] text-black font-medium">Belum Tuntas</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Notifikasi (Dynamic) -->
                <div
                    class="bg-[#ECECEC] rounded-[10px] pt-4 pb-4 pl-6 pr-2 shadow-sm border border-[#D9D9D9] h-[252px] flex flex-col">
                    <h3 class="font-semibold text-black text-[18px] mb-4 pr-4 uppercase tracking-tight">Notifikasi ({{ $notifikasiCount }})
                    </h3>

                    <div class="space-y-4 w-full flex-1 overflow-y-auto pr-4 custom-scrollbar">
                        @forelse($notifikasi as $notif)
                        <a href="{{ route('mahasiswa.notifikasi.show', $notif->id) }}"
                            class="border-b border-[#D9D9D9] pb-3 transform hover:translate-x-1 transition-transform cursor-pointer block">
                            <h4 class="font-bold text-[14px] text-[#1A1A1A]">{{ $notif->judul }}</h4>
                            <p class="text-[11px] text-[#666666] mt-1 line-clamp-2">{{ $notif->pesan }}</p>
                        </a>
                        @empty
                        <div class="flex flex-col items-center justify-center h-full text-gray-400 italic text-[13px] pr-4">
                            Tidak ada notifikasi baru.
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Right Column -->
            <div class="flex flex-col gap-8 lg:gap-10 w-full">
                <!-- Timeline Terdekat -->
                <div
                    class="bg-[#ECECEC] rounded-[10px] p-6 shadow-sm border border-[#D9D9D9] h-[132px] flex flex-col justify-center transition-all hover:shadow-md">
                    <h3 class="font-bold text-[#1A1A1A] text-[15px] mb-3 uppercase tracking-tight">Timeline Terdekat
                    </h3>
                    @if($timeline)
                        <p class="font-semibold text-[#1A1A1A] text-[14px]">
                            {{ $timeline->nama_kegiatan }} : <span
                                class="font-normal">{{ \Carbon\Carbon::parse($timeline->tanggal)->format('d/m/Y') }}</span>
                        </p>
                    @else
                        <p class="text-[13px] text-black/60 italic font-medium">Belum ada agenda terdekat...</p>
                    @endif
                </div>

                <!-- Status Sidang Kerja Praktik -->
                <div
                    class="bg-[#ECECEC] rounded-[30px] p-8 shadow-sm h-[487px] border border-[#D9D9D9] flex flex-col justify-between">
                    <div>
                        <h3 class="font-bold text-black text-[17px] mb-8">
                            Status Sidang Kerja Praktik :
                            <span class="font-medium {{ $sidang ? 'text-green-600' : 'text-[#0E0E0B]' }}">
                                {{ $sidang->status_jadwal ?? 'Belum Mendaftar' }}
                            </span>
                        </h3>

                        <div class="flex flex-col gap-5 text-[13px] font-medium text-black">
                            <div class="flex">
                                <div class="w-[140px]">Hari / Tanggal</div>
                                <div class="flex-1">:
                                    {{ $sidang && $sidang->tanggal_sidang ? \Carbon\Carbon::parse($sidang->tanggal_sidang)->translatedFormat('l, d F Y') : '-' }}
                                </div>
                            </div>
                            <div class="flex">
                                <div class="w-[140px]">Waktu</div>
                                <div class="flex-1">:
                                    {{ $sidang && $sidang->waktu_mulai_sidang ? \Carbon\Carbon::parse($sidang->waktu_mulai_sidang)->format('H:i') . ' - ' . \Carbon\Carbon::parse($sidang->waktu_selesai_sidang)->format('H:i') : '-' }}
                                </div>
                            </div>
                            <div class="flex">
                                <div class="w-[140px]">Ruangan</div>
                                <div class="flex-1">: {{ $sidang->ruang_sidang ?? '-' }}</div>
                            </div>
                            <div class="flex mt-3">
                                <div class="w-[140px]">Dosen Penguji 1</div>
                                <div class="flex-1">:
                                    {{ ($sidang && $sidang->penguji1) ? $sidang->penguji1->name : '-' }}
                                </div>
                            </div>
                            <div class="flex">
                                <div class="w-[140px]">Dosen Penguji 2</div>
                                <div class="flex-1">:
                                    {{ ($sidang && $sidang->penguji2) ? $sidang->penguji2->name : '-' }}
                                </div>
                            </div>
                            <div class="flex">
                                <div class="w-[140px]">Nilai Akhir / Grade</div>
                                <div class="flex-1">:
                                    {{ ($sidang && $sidang->nilai_akhir) ? $sidang->nilai_akhir : '-' }} /
                                    {{ ($sidang && $sidang->grade) ? $sidang->grade : '-' }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-8 flex justify-end">
                        @if(!auth()->user()->mahasiswa->is_aktif)
                            <button disabled class="bg-gray-200 text-gray-500 font-bold text-[13px] px-6 h-[36px] rounded-[20px] flex items-center justify-center gap-2 cursor-not-allowed">
                                Mode Pelihat
                            </button>
                        @elseif(!$sidang || !in_array($sidang->status_koordinator, ['pending', 'verified']))
                            <a href="{{ route('mahasiswa.pendaftaran-sidang.index') }}"
                                class="bg-[#FFFF1A] hover:bg-yellow-400 text-black font-bold text-[13px] w-[184px] h-[36px] rounded-[20px] flex items-center justify-center gap-2 transform hover:-translate-y-0.5 transition-all shadow-md">
                                <svg class="w-3.5 h-3.5 transform -rotate-45" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                                </svg>
                                Mendaftar Sidang
                            </a>
                        @else
                            <button disabled
                                class="bg-gray-200 text-gray-500 font-bold text-[13px] w-[184px] h-[36px] rounded-[20px] flex items-center justify-center gap-2 cursor-not-allowed">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 13l4 4L19 7"></path>
                                </svg>
                                Sudah Mendaftar
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <script>
            function dashboardMhs() {
                return {
                    currentProgress: 0,
                    targetProgress: @json($progress),
                    init() {
                        setTimeout(() => {
                            this.currentProgress = this.targetProgress;
                        }, 500);
                    }
                }
            }
        </script>
</x-dashboard-layout>