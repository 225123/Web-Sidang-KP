<x-dashboard-layout header="DASHBOARD" userName="{{ auth()->user()->name ?? 'Koordinator' }}"
    roleName="KOORDINATOR KP">
    <x-slot:sidebar>
        @include('koordinator.components.sidebar', ['active' => 'dashboard'])
    </x-slot:sidebar>

    

    <div class="flex flex-col gap-6 mt-4 w-full" x-data="dashboardData()">
        <!-- Statistics Cards -->
        <div class="flex flex-wrap gap-4 items-center">
            <div class="flex gap-4">
                <div class="w-[188px] h-[71px] bg-[#3B82F6] rounded-[5px] relative overflow-hidden flex flex-col justify-center items-center shadow-sm transform hover:scale-105 transition-transform duration-300">
                    <svg class="w-5 h-5 absolute left-3 top-3 text-white opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                    <span class="text-[#E8F5E9] text-[28px] font-bold font-inter leading-none" x-text="animateValue(stats.total_mahasiswa)">0</span>
                    <span class="text-[#E8F5E9] text-[12px] font-medium font-inter mt-1">Peserta KP</span>
                </div>
                <div class="w-[188px] h-[71px] bg-[#E57835] rounded-[5px] relative overflow-hidden flex flex-col justify-center items-center shadow-sm transform hover:scale-105 transition-transform duration-300">
                    <svg class="w-5 h-5 absolute left-3 top-3 text-white opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span class="text-[#E8F5E9] text-[28px] font-bold font-inter leading-none" x-text="animateValue(stats.kp_berjalan)">0</span>
                    <span class="text-[#E8F5E9] text-[12px] font-medium font-inter mt-1">KP Berjalan</span>
                </div>
                <div class="w-[188px] h-[71px] bg-[#4CAF50] rounded-[5px] relative overflow-hidden flex flex-col justify-center items-center shadow-sm transform hover:scale-105 transition-transform duration-300">
                    <svg class="w-5 h-5 absolute left-3 top-3 text-white opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span class="text-[#E8F5E9] text-[28px] font-bold font-inter leading-none" x-text="animateValue(stats.kp_selesai)">0</span>
                    <span class="text-[#E8F5E9] text-[12px] font-medium font-inter mt-1">KP Selesai</span>
                </div>
                <div class="w-[188px] h-[71px] bg-[#F4B400] rounded-[5px] relative overflow-hidden flex flex-col justify-center items-center shadow-sm transform hover:scale-105 transition-transform duration-300">
                    <svg class="w-5 h-5 absolute left-3 top-3 text-black opacity-30" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    <span class="text-black text-[28px] font-bold font-inter leading-none" x-text="animateValue(stats.sidang_terjadwal)">0</span>
                    <span class="text-black text-[12px] font-medium font-inter mt-1">Sidang Terjadwal</span>
                </div>
            </div>

            <div class="flex gap-4 ml-6">
                <div class="w-[131px] h-[49px] bg-[#3B82F6] rounded-[3.5px] flex flex-col justify-center items-center shadow-sm relative top-[-10px] transform hover:scale-105 transition-transform">
                    <span class="text-[#E8F5E9] text-[19.6px] font-bold font-inter leading-none" x-text="animateValue(stats.sudah_berkas)">0</span>
                    <span class="text-[#E8F5E9] text-[8.4px] font-medium font-inter mt-1">Sudah kumpul Berkas</span>
                </div>
                <div class="w-[131px] h-[49px] bg-[#E57835] rounded-[3.5px] flex flex-col justify-center items-center shadow-sm relative top-[-10px] transform hover:scale-105 transition-transform">
                    <span class="text-[#E8F5E9] text-[19.6px] font-bold font-inter leading-none" x-text="animateValue(stats.belum_berkas)">0</span>
                    <span class="text-[#E8F5E9] text-[8.4px] font-medium font-inter mt-1">Belum kumpul berkas</span>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 lg:gap-8">
            <!-- Weekly Chart -->
            <div class="lg:col-span-2 bg-[#FEFEFF] rounded-[10px] border border-[#D9D9D9] p-6 h-[320px] relative shadow-sm flex flex-col">
                <div class="flex justify-between items-start mb-6 w-full">
                    <h3 class="font-bold text-[#1A1A1A] text-[15px] font-inter uppercase tracking-tight">Statistik Jadwal Sidang</h3>
                    <div class="flex items-center gap-2">
                        <button @click="prevWeek()" :disabled="currentWeekIndex === 0" class="p-1 rounded bg-[#EDEBEB] border border-[#CAC0C0] hover:bg-gray-200 disabled:opacity-50 transition-colors">
                            <svg class="w-4 h-4 text-black" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                        </button>
                        <div class="bg-[#EDEBEB] border border-[#CAC0C0] rounded-[5px] px-3 py-1 text-center min-w-[120px]">
                            <span class="text-[12px] text-[#1A1A1A] font-medium" x-text="currentWeekLabel"></span>
                        </div>
                        <button @click="nextWeek()" :disabled="currentWeekIndex === chartWeeks.length - 1" class="p-1 rounded bg-[#EDEBEB] border border-[#CAC0C0] hover:bg-gray-200 disabled:opacity-50 transition-colors">
                            <svg class="w-4 h-4 text-black" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                        </button>
                    </div>
                </div>

                <div class="flex-1 relative border-l border-b border-[#B5A6A6] mt-4 ml-8 pb-4">
                    <!-- Grid Lines and Y-Axis -->
                    <template x-for="val in [100, 75, 50, 25, 0]">
                        <div class="absolute left-0 w-full border-t border-[#B5A6A6] opacity-20" :style="'bottom: ' + val + '%'">
                            <span class="absolute -left-10 -top-2.5 text-[#B5A6A6] text-[12px] font-bold w-8 text-right" x-text="Math.round((val/100) * maxWeeklyValue)"></span>
                        </div>
                    </template>

                    <!-- Bars -->
                    <div class="w-full h-full flex justify-around items-end px-4">
                        <template x-for="(count, index) in currentWeeklyStats">
                            <div class="flex flex-col items-center justify-end w-full h-full group">
                                <div class="w-6 bg-gradient-to-t from-[#3B82F6] to-[#60A5FA] rounded-t-sm transition-all duration-1000 ease-out shadow-sm relative"
                                     :style="'height: ' + (maxWeeklyValue > 0 ? (count / maxWeeklyValue * 100) : 0) + '%'">
                                    <!-- Tooltip -->
                                    <div class="absolute -top-8 left-1/2 transform -translate-x-1/2 bg-black text-white text-[10px] py-1 px-2 rounded opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap z-20 pointer-events-none">
                                        <span x-text="count"></span> Sidang
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>

                <div class="flex justify-around items-center w-[calc(100%-2rem)] ml-8 mt-2 text-[#B5A6A6] text-[11px] font-bold uppercase tracking-wider">
                    <span>Sun</span><span>Mon</span><span>Tue</span><span>Wed</span><span>Thu</span><span>Fri</span><span>Sat</span>
                </div>
            </div>

            <!-- Timeline Card -->
            <div class="bg-[#9F9F9F] rounded-[10px] border border-[#D9D9D9] p-6 h-[135px] shadow-sm flex flex-col justify-center transition-all duration-300 hover:shadow-md">
                <h3 class="font-bold text-[#1A1A1A] text-[15px] mb-4 uppercase tracking-tight">Timeline Terdekat</h3>
                @if($timeline)
                    <div class="grid grid-cols-[1fr_auto] gap-2 items-center">
                        <span class="text-[14px] font-bold text-[#1A1A1A] truncate">{{ $timeline->nama_kegiatan }}</span>
                        <span class="text-[14px] font-normal text-[#1A1A1A] whitespace-nowrap">: {{ \Carbon\Carbon::parse($timeline->tanggal)->format('d/m/Y') }}</span>
                    </div>
                @else
                    <p class="text-[11px] text-black/60 italic font-medium">Belum ada agenda terdekat...</p>
                @endif
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 lg:gap-8 min-h-[302px]">
            <!-- Progress Chart -->
            <div class="lg:col-span-2 bg-[#FEFEFF] rounded-[10px] border border-[#D9D9D9] p-8 shadow-sm flex flex-col justify-center">
                <h3 class="font-bold text-[#1A1A1A] text-[15px] mb-8 uppercase tracking-tight">Progress Pelaksanaan Sidang</h3>
                <div class="flex items-center gap-12 w-full h-[180px]">
                    <!-- Animated Circular Progress -->
                    <div class="relative flex-shrink-0" style="width: 140px; height: 140px;">
                        <svg class="w-full h-full transform -rotate-90" viewBox="0 0 100 100">
                            <!-- Background Circle -->
                            <circle cx="50" cy="50" r="45" fill="none" stroke="#E7DDDD" stroke-width="10" />
                            <!-- Progress Circle -->
                            <circle cx="50" cy="50" r="45" fill="none" stroke="#3B82F6" stroke-width="10" 
                                    stroke-dasharray="282.7" 
                                    :stroke-dashoffset="282.7 - (282.7 * progressSidang.sudah / 100)"
                                    class="transition-all duration-[2000ms] ease-out" />
                        </svg>
                        <div class="absolute inset-0 flex flex-col items-center justify-center">
                            <span class="text-[20px] font-bold text-black" x-text="progressSidang.sudah + '%'"></span>
                        </div>
                    </div>

                    <div class="flex flex-col gap-6 ml-8 w-full relative">
                        <div class="flex items-center gap-4 group">
                            <span class="text-[16px] font-bold font-inter text-black w-[45px]" x-text="progressSidang.sudah + '%'"></span>
                            <span class="text-[11px] font-semibold text-[#666666]">Mahasiswa sudah melakukan Sidang (<span x-text="sudahSidangCount"></span>)</span>
                        </div>
                        <div class="flex items-center gap-4 group">
                            <span class="text-[16px] font-bold font-inter text-black w-[45px]" x-text="progressSidang.belum + '%'"></span>
                            <span class="text-[11px] font-semibold text-[#666666]">Mahasiswa belum melakukan Sidang (<span x-text="belumSidangCount"></span>)</span>
                        </div>
                        <div class="flex gap-8 mt-4 ml-2">
                            <div class="flex items-center gap-2">
                                <div class="w-2.5 h-2.5 bg-[#3B82F6]"></div>
                                <span class="text-[11px] text-[#666666]">Sudah Sidang</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <div class="w-2.5 h-2.5 bg-[#E7DDDD]"></div>
                                <span class="text-[11px] text-[#666666]">Belum Sidang</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Notifications (Dynamic) -->
            <div class="bg-[#FEFEFF] rounded-[10px] border border-[#D9D9D9] p-6 shadow-sm flex flex-col h-full max-h-[302px]">
                <h3 class="font-bold text-[#1A1A1A] text-[15px] mb-4 uppercase tracking-tight">Notifikasi ({{ $notifikasiCount }})</h3>
                <div class="overflow-y-auto flex-1 pr-2 custom-scrollbar">
                    <div class="flex flex-col divide-y divide-[#D9D9D9]">
                        @forelse($notifikasi as $notif)
                            <a href="{{ route('koordinator.notifikasi.show', $notif->id) }}" class="py-3 transform hover:translate-x-1 transition-transform cursor-pointer block">
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

    <script>
        function dashboardData() {
            return {
                stats: @json($stats),
                chartWeeks: @json($chartWeeks),
                currentWeekIndex: 0,
                progressSidang: { sudah: 0, belum: 0 },
                targetProgress: @json($progressSidang),
                sudahSidangCount: @json($sudahSidangCount),
                belumSidangCount: @json($belumSidangCount),
                
                get currentWeeklyStats() {
                    return this.chartWeeks[this.currentWeekIndex].stats;
                },
                get currentWeekLabel() {
                    return this.chartWeeks[this.currentWeekIndex].label;
                },
                get maxWeeklyValue() {
                    const statsArray = Array.from(this.chartWeeks[this.currentWeekIndex].stats || []);
                    let max = 5;
                    for (let i = 0; i < statsArray.length; i++) {
                        if (statsArray[i] > max) max = statsArray[i];
                    }
                    return max;
                },
                
                init() {
                    // Trigger animations
                    setTimeout(() => {
                        this.progressSidang.sudah = this.targetProgress.sudah;
                        this.progressSidang.belum = this.targetProgress.belum;
                    }, 500);
                },
                
                nextWeek() {
                    if (this.currentWeekIndex < this.chartWeeks.length - 1) {
                        this.currentWeekIndex++;
                    }
                },
                
                prevWeek() {
                    if (this.currentWeekIndex > 0) {
                        this.currentWeekIndex--;
                    }
                },

                animateValue(val) {
                    return val;
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
</x-dashboard-layout>