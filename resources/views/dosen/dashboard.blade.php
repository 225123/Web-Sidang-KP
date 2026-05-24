<x-dashboard-layout header="Dashboard" userName="{{ auth()->user()->name }}" roleName="DOSEN">
    <x-slot:sidebar>
        @include('dosen.components.sidebar', ['active' => 'dashboard'])
        </x-slot>

                

        <div class="flex flex-col gap-6 mt-4 w-full" x-data="dashboardDosen()">
            <div class="flex flex-wrap gap-4 items-center mb-2 w-full">
                <div class="flex flex-wrap gap-4 w-full xl:w-auto">
                    <div
                        class="w-full sm:w-[calc(50%-0.5rem)] xl:w-[188px] h-[71px] bg-[#3B82F6] rounded-[5px] relative overflow-hidden flex flex-col justify-center items-center shadow-sm">
                        <svg class="w-5 h-5 absolute left-3 top-3 text-white" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                        <span class="text-[#E8F5E9] text-[28px] font-bold font-inter leading-none">{{ $stats['bimbingan'] }}</span>
                        <span class="text-[#E8F5E9] text-[12px] font-medium font-inter mt-1">Mahasiswa Bimbingan</span>
                    </div>

                    <div
                        class="w-full sm:w-[calc(50%-0.5rem)] xl:w-[188px] h-[71px] bg-[#E57835] rounded-[5px] relative overflow-hidden flex flex-col justify-center items-center shadow-sm">
                        <svg class="w-5 h-5 absolute left-3 top-3 text-white" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span class="text-[#E8F5E9] text-[28px] font-bold font-inter leading-none">{{ $stats['belum_sidang'] }}</span>
                        <span class="text-[#E8F5E9] text-[12px] font-medium font-inter mt-1">Belum Sidang</span>
                    </div>

                    <div
                        class="w-full sm:w-[calc(50%-0.5rem)] xl:w-[188px] h-[71px] bg-[#4CAF50] rounded-[5px] relative overflow-hidden flex flex-col justify-center items-center shadow-sm">
                        <svg class="w-5 h-5 absolute left-3 top-3 text-white" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span class="text-[#E8F5E9] text-[28px] font-bold font-inter leading-none">{{ $stats['telah_sidang'] }}</span>
                        <span class="text-[#E8F5E9] text-[12px] font-medium font-inter mt-1">Telah Sidang</span>
                    </div>

                    <div
                        class="w-full sm:w-[calc(50%-0.5rem)] xl:w-[188px] h-[71px] bg-[#F4B400] rounded-[5px] relative overflow-hidden flex flex-col justify-center items-center shadow-sm">
                        <svg class="w-5 h-5 absolute left-3 top-3 text-black" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        <span class="text-black text-[28px] font-bold font-inter leading-none">{{ $stats['sidang_terjadwal'] }}</span>
                        <span class="text-black text-[12px] font-medium font-inter mt-1">Sidang Terjadwal</span>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 lg:gap-8">
                <div class="lg:col-span-2 space-y-6">
                    <!-- Statistik Bimbingan Mingguan -->
                    <div
                        class="bg-[#FEFEFF] rounded-[10px] border border-[#D9D9D9] p-6 shadow-sm overflow-hidden flex flex-col min-h-[302px]">
                        <div class="flex justify-between items-start mb-6">
                            <h3 class="font-semibold text-[#1A1A1A] text-[18px] font-inter tracking-tight"> Progress Bimbingan</h3>
                        </div>
                        
                        <div class="flex flex-col md:flex-row items-center gap-8 flex-1">
                            <!-- Circular Progress Chart -->
                            <div class="relative flex-shrink-0">
                                <svg class="w-40 h-40 transform -rotate-90">
                                    <circle
                                        cx="80"
                                        cy="80"
                                        r="70"
                                        stroke="#F3F4F6"
                                        stroke-width="12"
                                        fill="transparent"
                                    />
                                    <circle
                                        cx="80"
                                        cy="80"
                                        r="70"
                                        stroke="#3B82F6"
                                        stroke-width="12"
                                        fill="transparent"
                                        stroke-dasharray="440"
                                        stroke-dashoffset="{{ 440 - (440 * $progressBimbingan) / 100 }}"
                                        stroke-linecap="round"
                                        class="transition-all duration-1000 ease-out"
                                    />
                                </svg>
                                <div class="absolute inset-0 flex flex-col items-center justify-center">
                                    <span class="text-[28px] font-bold text-black font-inter">{{ $progressBimbingan }}%</span>
                                    <span class="text-[11px] font-medium text-gray-500 uppercase tracking-wider">Selesai</span>
                                </div>
                            </div>

                            <!-- Student List -->
                            <div class="flex-1 w-full overflow-y-auto custom-scrollbar max-h-[220px] pr-2">
                                <div class="space-y-3">
                                    @forelse($listBimbinganMahasiswa as $mhs)
                                    <div class="flex items-center justify-between p-3 bg-[#F9FAFB] rounded-[8px] hover:bg-[#F3F4F6] transition-colors border border-transparent hover:border-[#D1D5DB]">
                                        <div class="flex flex-col">
                                            <span class="text-[13px] font-bold text-[#1A1A1A] leading-tight">{{ $mhs->nama }}</span>
                                            <span class="text-[11px] text-gray-500 mt-0.5">{{ $mhs->nim }}</span>
                                        </div>
                                        <div class="flex flex-col items-end">
                                            <span class="text-[12px] font-bold {{ $mhs->count >= 12 ? 'text-[#059669]' : 'text-[#3B82F6]' }}">
                                                {{ $mhs->count }}/12
                                            </span>
                                            <div class="w-20 bg-gray-200 rounded-full h-1 mt-1.5">
                                                <div class="bg-current h-1 rounded-full" 
                                                     style="width: {{ min(($mhs->count / 12) * 100, 100) }}%; color: {{ $mhs->count >= 12 ? '#059669' : '#3B82F6' }}"></div>
                                            </div>
                                        </div>
                                    </div>
                                    @empty
                                    <div class="flex flex-col items-center justify-center h-full py-10 text-gray-400 italic text-[13px]">
                                        Belum ada mahasiswa bimbingan...
                                    </div>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Persetujuan Menunggu -->
                    <div
                        class="bg-[#FEFEFF] rounded-[10px] border border-[#D9D9D9] p-6 shadow-sm overflow-hidden min-h-[220px]">
                        <div class="mb-4">
                            <h3 class="font-semibold text-[#1A1A1A] text-[18px] font-inter tracking-tight">Persetujuan Menunggu</h3>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="w-full min-w-[500px] text-left">
                                <thead class="bg-[#F9F9F9] text-[13px] text-gray-500 font-bold border-b border-[#D9D9D9]">
                                <tr>
                                    <th class="px-4 py-2 w-[40px]">No</th>
                                    <th class="px-4 py-2">Mahasiswa</th>
                                    <th class="px-4 py-2 text-center">Jenis Berkas</th>
                                    <th class="px-4 py-2 text-center w-[100px]">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-[#D9D9D9] text-[13px] font-medium text-black">
                                @forelse($menungguPersetujuan as $index => $item)
                                <tr class="hover:bg-gray-50 transition-colors h-[48px]">
                                    <td class="px-4 py-2">{{ $index + 1 }}</td>
                                    <td class="px-4 py-2">{{ $item->mahasiswa }}</td>
                                    <td class="px-4 py-2 text-center">
                                        <span class="px-3 py-1 rounded-[5px] text-[11px] font-bold border {{ $item->color }}">
                                            {{ $item->jenis }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-2 text-center">
                                        <a href="{{ $item->route }}"
                                            class="text-white bg-[#4285F4] hover:bg-blue-600 px-3 py-1.5 font-bold text-[11px] rounded-[5px] shadow-sm transition-colors inline-block">Review</a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="px-4 py-8 text-center text-gray-400 italic">Tidak ada persetujuan tertunda.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                        </div>
                    </div>
                </div>

                <div class="flex flex-col gap-6">
                    <!-- Timeline Terdekat (New) -->
                    <div class="bg-[#ECECEC] rounded-[10px] p-6 shadow-sm border border-[#D9D9D9] h-[132px] flex flex-col justify-center transition-all hover:shadow-md">
                        <h3 class="font-bold text-[#1A1A1A] text-[15px] mb-3 tracking-tight">Timeline Terdekat</h3>
                        @if($timeline)
                            <p class="font-semibold text-[#1A1A1A] text-[14px]">
                                {{ $timeline->nama_kegiatan }} : <span class="font-normal">{{ \Carbon\Carbon::parse($timeline->tanggal)->format('d/m/Y') }}</span>
                            </p>
                        @else
                            <p class="text-[13px] text-black/60 italic font-medium">Belum ada agenda terdekat...</p>
                        @endif
                    </div>

                    <!-- Jadwal Sidang Terdekat -->
                    <div class="bg-[#FEFEFF] rounded-[10px] border border-[#D9D9D9] p-6 shadow-sm flex flex-col h-[302px]">
                        <h3 class="font-semibold text-[#1A1A1A] text-[18px] mb-4 font-inter tracking-tight">Jadwal Sidang Terdekat</h3>

                        <div class="flex flex-col gap-4 overflow-y-auto custom-scrollbar pr-2">
                            @forelse($jadwalTerdekat as $jadwal)
                            <div class="flex flex-col border-l-4 border-[#3B82F6] pl-3 py-1 bg-gray-50/50 rounded-r-md">
                                <span class="text-[11px] font-bold text-gray-500 mb-0.5 font-inter">
                                    {{ \Carbon\Carbon::parse($jadwal->tanggal_sidang)->format('d M Y') }} • 
                                    {{ \Carbon\Carbon::parse($jadwal->waktu_mulai_sidang)->format('H:i') }} - {{ \Carbon\Carbon::parse($jadwal->waktu_selesai_sidang)->format('H:i') }}
                                </span>
                                <span class="text-[14px] font-bold text-[#1A1A1A] font-inter">{{ $jadwal->mahasiswa->user->name }}</span>
                                <div class="flex justify-between items-end mt-1 font-inter">
                                    <span class="text-[11px] text-[#666666] font-medium">{{ $jadwal->mahasiswa->nim }}</span>
                                    <span class="text-[10px] font-bold px-2 py-0.5 bg-[#E8F5E9] text-[#1B5E20] rounded border border-[#4CAF50]">
                                        {{ $jadwal->ruang_sidang }}
                                    </span>
                                </div>
                            </div>
                            @empty
                            <div class="flex flex-col items-center justify-center h-full text-gray-400 italic text-[13px]">
                                Belum ada jadwal sidang menguji...
                            </div>
                            @endforelse
                        </div>
                    </div>

                    <!-- Notifikasi Dynamic -->
                    <div class="bg-[#FEFEFF] rounded-[10px] border border-[#D9D9D9] p-6 shadow-sm flex flex-col h-[252px]">
                        <h3 class="font-bold text-[#1A1A1A] text-[15px] mb-4 tracking-tight">Notifikasi ({{ $notifikasiCount }})</h3>
                        <div class="overflow-y-auto flex-1 pr-2 custom-scrollbar">
                            <div class="flex flex-col divide-y divide-[#D9D9D9]">
                                @forelse($notifikasi as $notif)
                                <a href="{{ route('dosen.notifikasi.show', $notif->id) }}" class="py-3 transform hover:translate-x-1 transition-transform cursor-pointer block">
                                    <h4 class="text-[14px] font-bold text-[#1A1A1A]">{{ $notif->judul }}</h4>
                                    <p class="text-[11px] text-[#666666] mt-1 line-clamp-1">{{ $notif->pesan }}</p>
                                </a>
                                @empty
                                <div class="flex flex-col items-center justify-center h-full text-gray-400 italic text-[13px]">
                                    Tidak ada notifikasi baru.
                                </div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <script>
            function dashboardDosen() {
                return {
                    init() {
                        console.log('Dosen Dashboard Initialized');
                    }
                }
            }
        </script>

        <style>
            .custom-scrollbar::-webkit-scrollbar { width: 5px; }
            .custom-scrollbar::-webkit-scrollbar-track { background: #f1f1f1; }
            .custom-scrollbar::-webkit-scrollbar-thumb { background: #888; border-radius: 10px; }
            .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #555; }
        </style>
    </div>
</x-dashboard-layout>