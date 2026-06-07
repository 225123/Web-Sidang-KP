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
                    <div class="flex items-center gap-2" x-show="chartWeeks.length > 0" style="display: none;" x-cloak>
                        <button x-show="chartWeeks.length > 1" @click="prevWeek()" :disabled="currentWeekIndex === 0" class="p-1 rounded bg-[#EDEBEB] border border-[#CAC0C0] hover:bg-gray-200 disabled:opacity-50 transition-colors">
                            <svg class="w-4 h-4 text-black" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                        </button>
                        <div class="bg-[#EDEBEB] border border-[#CAC0C0] rounded-[5px] px-3 py-1 text-center min-w-[120px]">
                            <span class="text-[12px] text-[#1A1A1A] font-medium" x-text="currentWeekLabel"></span>
                        </div>
                        <button x-show="chartWeeks.length > 1" @click="nextWeek()" :disabled="currentWeekIndex === chartWeeks.length - 1" class="p-1 rounded bg-[#EDEBEB] border border-[#CAC0C0] hover:bg-gray-200 disabled:opacity-50 transition-colors">
                            <svg class="w-4 h-4 text-black" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                        </button>
                    </div>

                    <!-- Empty State Button -->
                    <div x-show="chartWeeks.length === 0" style="display: none;" x-cloak>
                        <a href="{{ route('koordinator.penjadwalan.index') }}" class="inline-flex items-center gap-1.5 bg-[#EDEBEB] border border-[#CAC0C0] hover:bg-[#DFDFDF] text-[#1A1A1A] text-[12px] font-medium px-4 py-1 rounded-[5px] transition-colors">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                            Buat Jadwal
                        </a>
                    </div>
                </div>

                <!-- Chart Wrapper -->
                <div class="flex-1 relative mt-4 ml-8" :class="chartWeeks.length > 0 ? 'border-l border-b border-[#B5A6A6]' : 'border-l border-b border-[#B5A6A6] flex items-center justify-center'">
                    <!-- Empty State -->
                    <div x-show="chartWeeks.length === 0" style="display: none;" class="text-[#B5A6A6] text-[14px] italic font-medium">
                        Belum ada jadwal sidang
                    </div>

                    <!-- Chart Content -->
                    <div x-show="chartWeeks.length > 0" class="w-full h-full relative" style="display: none;">
                    <!-- Grid Lines and Y-Axis -->
                    <template x-for="val in [100, 75, 50, 25, 0]">
                        <div class="absolute left-0 w-full border-t border-[#B5A6A6]/20" :style="'bottom: ' + val + '%'">
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
                </div>

                <div class="flex justify-around items-center w-[calc(100%-2rem)] ml-8 px-4 mt-2 text-[#B5A6A6] text-[11px] font-bold uppercase tracking-wider">
                    <div class="w-full text-center">Sun</div>
                    <div class="w-full text-center">Mon</div>
                    <div class="w-full text-center">Tue</div>
                    <div class="w-full text-center">Wed</div>
                    <div class="w-full text-center">Thu</div>
                    <div class="w-full text-center">Fri</div>
                    <div class="w-full text-center">Sat</div>
                </div>
            </div>

            <!-- Timeline Card -->
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
        </div>

        <!-- NEW ROW 3: Progress Bimbingan Saya (col-1) & Progress Bimbingan Umum (col-2) -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 lg:gap-8">
<!-- Progress Bimbingan -->
            <div class="bg-[#FEFEFF] rounded-[10px] border border-[#D9D9D9] p-6 shadow-sm overflow-hidden flex flex-col min-h-[302px]">
                <div class="flex justify-between items-start mb-6 shrink-0">
                    <h3 class="font-semibold text-[#1A1A1A] text-[18px] font-inter tracking-tight"> Progress Bimbingan Saya</h3>
                </div>
                
                <div class="flex flex-col items-center gap-6">
                    <!-- Circular Progress Chart -->
                    <div class="relative flex-shrink-0">
                        <svg class="w-40 h-40 transform -rotate-90">
                            <circle cx="80" cy="80" r="70" stroke="#F3F4F6" stroke-width="12" fill="transparent" />
                            <circle cx="80" cy="80" r="70" stroke="#3B82F6" stroke-width="12" fill="transparent"
                                stroke-dasharray="440"
                                :stroke-dashoffset="440 - (440 * progressBimbinganKoord) / 100"
                                stroke-linecap="round"
                                class="transition-all duration-1000 ease-out" />
                        </svg>
                        <div class="absolute inset-0 flex flex-col items-center justify-center">
                            <span class="text-[28px] font-bold text-black font-inter" x-text="progressBimbinganKoord + '%'"></span>
                            <span class="text-[11px] font-medium text-gray-500 uppercase tracking-wider">Selesai</span>
                        </div>
                    </div>

                    <!-- Student List -->
                    <div class="w-full overflow-y-auto custom-scrollbar pr-2 max-h-[135px]">
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
            <!-- Progress Bimbingan Umum -->
            <div class="lg:col-span-2 bg-[#FEFEFF] rounded-[10px] border border-[#D9D9D9] p-6 shadow-sm overflow-hidden flex flex-col min-h-[302px]" id="analyticsSection">
                <div class="flex justify-between items-start mb-6">
                    <h3 class="font-semibold text-[#1A1A1A] text-[18px] font-inter tracking-tight"> Progress Bimbingan Umum</h3>
                </div>

                <div class="flex flex-col md:flex-row items-center gap-12 flex-1 w-full max-w-2xl mx-auto">
                    <!-- Left: Status Stats -->
                    <div class="flex-1 w-full space-y-4">
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-[8px] border border-gray-100 shadow-sm">
                            <div class="flex items-center gap-3">
                                <div class="w-2 h-2 rounded-full bg-gray-400"></div>
                                <span class="text-[13px] font-medium text-gray-600">Belum Mulai</span>
                            </div>
                            <span class="text-[16px] font-black text-gray-700">{{ $countBelumUmum }}</span>
                        </div>

                        <div class="flex items-center justify-between p-3 bg-blue-50 rounded-[8px] border border-blue-100 shadow-sm">
                            <div class="flex items-center gap-3">
                                <div class="w-2 h-2 rounded-full bg-blue-500 animate-pulse"></div>
                                <span class="text-[13px] font-medium text-blue-700">Dalam Proses (1-11)</span>
                            </div>
                            <span class="text-[16px] font-black text-blue-800">{{ $countDimulaiUmum }}</span>
                        </div>

                        <div class="flex items-center justify-between p-3 bg-green-50 rounded-[8px] border border-green-100 shadow-sm">
                            <div class="flex items-center gap-3">
                                <div class="w-2 h-2 rounded-full bg-green-500"></div>
                                <span class="text-[13px] font-medium text-green-700">Memenuhi (12)</span>
                            </div>
                            <span class="text-[16px] font-black text-green-800">{{ $countMemenuhiUmum }}</span>
                        </div>
                    </div>

                    <!-- Right: Circular Progress -->
                    <div class="shrink-0 flex flex-col items-center">
                        <div class="relative w-36 h-36">
                            <canvas id="overallProgressChart"></canvas>
                            <div class="absolute inset-0 flex flex-col items-center justify-center">
                                <span class="text-[28px] font-black text-black leading-none">{{ $displayPercentUmum }}%</span>
                                <span class="text-[10px] font-bold text-gray-400 uppercase mt-1">Total Progres</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- NEW ROW 4: Persetujuan Menunggu (col-2) & Notifikasi (col-1) -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 lg:gap-8">
<!-- Persetujuan Menunggu -->
            <div class="lg:col-span-2 bg-[#FEFEFF] rounded-[10px] border border-[#D9D9D9] p-6 shadow-sm overflow-hidden min-h-[302px]">
                <div class="mb-6">
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
<!-- Notifications (Dynamic) -->
            <div class="lg:col-span-1 w-full bg-[#FEFEFF] rounded-[10px] border border-[#D9D9D9] p-6 shadow-sm flex flex-col h-[252px]">
                <h3 class="font-bold text-[#1A1A1A] text-[15px] mb-4 tracking-tight">Notifikasi ({{ $notifikasiCount }})</h3>
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

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        function initChartUmum() {
            const chartCanvas = document.getElementById('overallProgressChart');
            if (!chartCanvas) return;

            const ctx = chartCanvas.getContext('2d');
            const overallPercent = {{ $overallPercentUmum }};

            const config = {
                type: 'doughnut',
                data: {
                    datasets: [{
                        data: [overallPercent, 100 - overallPercent],
                        backgroundColor: ['#2563eb', '#f1f5f9'],
                        borderWidth: 0,
                        hoverOffset: 0,
                        cutout: '85%',
                        borderRadius: 20
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: { enabled: false }
                    },
                    animation: {
                        duration: 2500,
                        easing: 'easeOutQuart'
                    }
                }
            };

            if (window.umumChartInstance) {
                window.umumChartInstance.destroy();
            }
            window.umumChartInstance = new Chart(ctx, config);
        }

        document.addEventListener('DOMContentLoaded', initChartUmum);
        document.addEventListener('turbo:load', initChartUmum);
    </script>
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
                progressBimbinganKoord: 0,
                targetProgressBimbinganKoord: @json($progressBimbinganKoordinator),
                
                get currentWeeklyStats() {
                    if (this.chartWeeks.length === 0) return [0,0,0,0,0,0,0];
                    return this.chartWeeks[this.currentWeekIndex].stats;
                },
                get currentWeekLabel() {
                    if (this.chartWeeks.length === 0) return '';
                    return this.chartWeeks[this.currentWeekIndex].label;
                },
                get maxWeeklyValue() {
                    if (this.chartWeeks.length === 0) return 5;
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
                        this.progressBimbinganKoord = this.targetProgressBimbinganKoord;
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