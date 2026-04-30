<x-dashboard-layout header="Kalender Sidang KP" userName="{{ auth()->user()->name }}" roleName="KOORDINATOR KP">
    <x-slot:sidebar>
        @include('koordinator.components.sidebar', ['active' => 'kalender-sidang'])
    </x-slot>

    <x-slot:headerActions>
        <div x-data="{ open: false, selected: 'Genap 2025/2026' }" class="relative w-[212px]">
            <button @click="open = !open" @click.outside="open = false" type="button"
                class="w-full flex items-center justify-between border border-[#CAC0C0] bg-[#FBFBFB] rounded-[5px] shadow-sm text-[13px] font-medium py-1.5 px-3 focus:outline-none focus:border-[#4CC098] focus:ring-1 focus:ring-[#4CC098] cursor-pointer text-black h-[32px]">

                <span x-text="selected"></span>

                <svg :class="open ? 'rotate-0' : 'rotate-90'"
                    class="w-3.5 h-3.5 text-gray-500 transition-transform duration-200 flex-shrink-0" fill="none"
                    stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                </svg>
            </button>

            <div x-show="open" x-transition style="display: none;"
                class="absolute z-50 w-full mt-1 bg-[#FBFBFB] border border-[#CAC0C0] rounded-[5px] shadow-lg overflow-hidden">
                <ul class="py-1 text-[13px] font-medium text-black">
                    <li>
                        <button @click="selected = 'Genap 2025/2026'; open = false" type="button"
                            class="block w-full text-left px-3 py-2 hover:bg-[#E8E5E5] transition-colors cursor-pointer">
                            Genap 2025/2026
                        </button>
                    </li>
                    <li>
                        <button @click="selected = 'Ganjil 2025/2026'; open = false" type="button"
                            class="block w-full text-left px-3 py-2 hover:bg-[#E8E5E5] transition-colors cursor-pointer">
                            Ganjil 2025/2026
                        </button>
                    </li>
                </ul>
            </div>
            <input type="hidden" name="periode" :value="selected">
        </div>
    </x-slot:headerActions>

    <div class="w-full flex-1 pb-10" x-data="calendarManager()" x-init="init()">
        <style>
            [x-cloak] { display: none !important; }
            .calendar-grid-header { background-color: #E2E8F0; }
            .calendar-cell { min-height: 75px; transition: all 0.2s; position: relative; border-color: #CAC0C0 !important; }
            .calendar-cell:hover:not(.bg-\[\#FDE68A\]) { background-color: #F8FAFC; }
            .day-number { font-weight: 700; font-size: 14px; position: absolute; right: 8px; top: 6px; }
            .session-indicator { background-color: #FFD700; color: #000; }
            .today-highlight { background-color: #BFDBFE !important; }
            .holiday-highlight { background-color: #FECACA !important; }
            
            /* Custom Table Style */
            .detail-table th { background-color: #E8E5E5; color: #1a1a1a; font-weight: 700; text-transform: uppercase; font-size: 10px; padding: 10px 6px; border: 1px solid #CAC0C0; }
            .detail-table td { border: 1px solid #CAC0C0; padding: 8px 6px; vertical-align: middle; font-size: 12px; color: #1a1a1a; }
            
            /* Select Styling */
            .filter-select { border: 1px solid #CAC0C0; background-color: #FBFBFB; border-radius: 5px; font-size: 12px; font-weight: 600; padding: 6px 30px 6px 10px; appearance: none; background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%236B7280'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M19 9l-7 7-7-7'%3E%3C/path%3E%3C/svg%3E"); background-repeat: no-repeat; background-position: right 8px center; background-size: 12px; text-align: center; text-align-last: center; }
        </style>


        <div class="flex flex-col gap-8">
            <!-- Top Side: Calendar -->
            <div class="bg-white rounded-[5px] border border-[#CAC0C0] overflow-hidden flex flex-col shadow-sm">
                <!-- Calendar Header -->
                <div class="p-4 flex items-center justify-between border-b bg-gray-50/50">
                    <h2 class="text-[20px] font-black text-black uppercase" x-text="monthName + ' ' + currentYear"></h2>
                    <div class="flex items-center gap-2">
                        <button @click="prevMonth()" class="p-2 border border-[#CAC0C0] rounded bg-white hover:bg-gray-100 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"></path></svg>
                        </button>
                        <button @click="nextMonth()" class="p-2 border border-[#CAC0C0] rounded bg-white hover:bg-gray-100 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"></path></svg>
                        </button>
                    </div>
                </div>

                <!-- Legend -->
                <div class="px-4 py-3 border-b flex flex-wrap items-center gap-6 bg-white shrink-0">
                    <div class="text-[12px] font-bold text-gray-600">Status Kalender :</div>
                    <div class="flex items-center gap-2">
                        <div class="w-3 h-3 rounded-full bg-[#FDE68A] border border-[#F59E0B]"></div>
                        <span class="text-[11px] font-bold text-black uppercase">Tanggal Sidang</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <div class="w-3 h-3 rounded-full bg-[#BFDBFE]"></div>
                        <span class="text-[11px] font-bold text-black uppercase">Hari Ini</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <div class="w-3 h-3 rounded-full bg-[#FECACA]"></div>
                        <span class="text-[11px] font-bold text-black uppercase">Libur</span>
                    </div>
                </div>

                <!-- Calendar Grid -->
                <div class="flex-1 overflow-x-auto">
                    <div class="min-w-[500px]">
                        <!-- Week Header -->
                        <div class="grid grid-cols-7 border-b border-[#CAC0C0]">
                            <template x-for="day in ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat']">
                                <div class="p-3 text-center text-[13px] font-black text-black border-r border-[#CAC0C0] bg-gray-100/50 last:border-r-0" x-text="day"></div>
                            </template>
                        </div>
                        <!-- Days -->
                        <div class="grid grid-cols-7">
                                    <template x-for="(day, index) in calendarDays" :key="index">
                                        <div @click="day.isCurrentMonth ? selectDate(day.dateString) : null" 
                                            class="calendar-cell p-1 border-r border-b border-[#CAC0C0] overflow-hidden last:border-r-0"
                                            :class="{
                                                'cursor-pointer': day.isCurrentMonth,
                                                'bg-gray-50/50': !day.isCurrentMonth,
                                                'today-highlight': day.isToday && day.isCurrentMonth,
                                                'holiday-highlight': day.isHoliday && day.isCurrentMonth,
                                                'bg-[#FFD700]': day.sessionCount > 0 && day.isCurrentMonth && !day.isToday,
                                                'ring-4 ring-blue-500 ring-inset z-10': selectedDate === day.dateString && day.isCurrentMonth
                                            }">
                                            
                                            <span class="day-number" x-text="day.day" 
                                                :class="{
                                                    'text-red-600': day.isHoliday && day.isCurrentMonth,
                                                    'text-gray-600': !day.isHoliday && day.isCurrentMonth,
                                                    'text-gray-400 font-normal text-[12px]': !day.isCurrentMonth,
                                                    'font-black': day.isCurrentMonth
                                                }"></span>
                                            
                                            <!-- Session Count: Only Text -->
                                            <template x-if="day.sessionCount > 0 && day.isCurrentMonth">
                                                <div class="mt-8 flex flex-col items-center justify-center text-center">
                                                    <span class="text-[16px] font-black leading-none text-black" x-text="day.sessionCount"></span>
                                                    <span class="text-[9px] font-bold uppercase tracking-tight text-gray-700">Sidang</span>
                                                </div>
                                            </template>
                                        </div>
                                    </template>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Bottom Side: Details -->
            <!-- Bottom Side: Details -->
            <div class="bg-white rounded-[15px] border border-gray-200 shadow-sm overflow-hidden p-6 mt-8 mb-8">
                <div class="mb-6 flex flex-col sm:flex-row justify-between items-start gap-4">
                    <div>
                        <h3 class="text-[18px] font-bold text-black tracking-tight">
                            Detail Sidang: <span class="text-blue-600 font-bold" x-text="selectedDate ? formatReadableDate(selectedDate) : ''"></span>
                        </h3>
                        <p class="text-[12px] text-black/60 font-medium mt-1">Daftar jadwal sidang mahasiswa pada tanggal yang dipilih.</p>
                    </div>
                </div>

                <div class="flex flex-col sm:flex-row justify-between items-center gap-6 mb-6">
                    <!-- Filter Bar Integrated -->
                    <div class="flex flex-wrap items-center gap-4 w-full sm:w-auto">
                        <!-- Waktu Dropdown -->
                        <div x-data="{ openWaktu: false }" class="relative w-full sm:w-[160px] z-[60]">
                            <button type="button" @click="openWaktu = !openWaktu" @click.outside="openWaktu = false" class="w-full text-[12px] border border-gray-300 rounded-[5px] py-2 px-3 bg-white text-black font-medium focus:ring-[#4285F4] flex justify-between items-center text-left shadow-sm">
                                <span class="truncate" x-text="filters.waktu === '' ? 'Semua Waktu' : (filters.waktu === 'pagi' ? 'Pagi (08:00-12:00)' : 'Siang (13:00-17:00)')"></span>
                                <svg :class="openWaktu ? 'rotate-0' : 'rotate-90'" class="w-3.5 h-3.5 transition-all duration-200 text-gray-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                            </button>
                            <div x-show="openWaktu" x-transition x-cloak class="absolute w-full mt-1 bg-white border border-gray-300 rounded-[5px] shadow-lg overflow-hidden py-1 z-50">
                                <label class="block px-3 py-2 text-[12px] hover:bg-gray-100 cursor-pointer text-black"><input type="radio" value="" x-model="filters.waktu" class="hidden" @change="openWaktu = false; currentPage = 1">Semua Waktu</label>
                                <label class="block px-3 py-2 text-[12px] hover:bg-gray-100 cursor-pointer text-black"><input type="radio" value="pagi" x-model="filters.waktu" class="hidden" @change="openWaktu = false; currentPage = 1">Pagi (08:00-12:00)</label>
                                <label class="block px-3 py-2 text-[12px] hover:bg-gray-100 cursor-pointer text-black"><input type="radio" value="siang" x-model="filters.waktu" class="hidden" @change="openWaktu = false; currentPage = 1">Siang (13:00-17:00)</label>
                            </div>
                        </div>

                        <!-- Ruangan Dropdown -->
                        <div x-data="{ openRuangan: false }" class="relative w-full sm:w-[150px] z-[50]">
                            <button type="button" @click="openRuangan = !openRuangan" @click.outside="openRuangan = false" class="w-full text-[12px] border border-gray-300 rounded-[5px] py-2 px-3 bg-white text-black font-medium focus:ring-[#4285F4] flex justify-between items-center text-left shadow-sm">
                                <span class="truncate" x-text="filters.ruangan === '' ? 'Semua Ruangan' : filters.ruangan"></span>
                                <svg :class="openRuangan ? 'rotate-0' : 'rotate-90'" class="w-3.5 h-3.5 transition-all duration-200 text-gray-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                            </button>
                            <div x-show="openRuangan" x-transition x-cloak class="absolute w-full mt-1 bg-white border border-gray-300 rounded-[5px] shadow-lg overflow-y-auto max-h-[200px] py-1 z-50 custom-scrollbar">
                                <label class="block px-3 py-2 text-[12px] hover:bg-gray-100 cursor-pointer text-black"><input type="radio" value="" x-model="filters.ruangan" class="hidden" @change="openRuangan = false; currentPage = 1">Semua Ruangan</label>
                                <template x-for="r in allRooms" :key="r">
                                    <label class="block px-3 py-2 text-[12px] hover:bg-gray-100 cursor-pointer text-black"><input type="radio" :value="r" x-model="filters.ruangan" class="hidden" @change="openRuangan = false; currentPage = 1"><span x-text="r"></span></label>
                                </template>
                            </div>
                        </div>

                        <!-- Penguji Dropdown -->
                        <div x-data="{ openPenguji: false, search: '' }" class="relative w-full sm:w-[180px] z-[40]">
                            <button type="button" @click="openPenguji = !openPenguji" @click.outside="openPenguji = false" class="w-full text-[12px] border border-gray-300 rounded-[5px] py-2 px-3 bg-white text-black font-medium focus:ring-[#4285F4] flex justify-between items-center text-left shadow-sm">
                                <span class="truncate" x-text="filters.penguji === '' ? 'Semua Penguji' : filters.penguji"></span>
                                <svg :class="openPenguji ? 'rotate-0' : 'rotate-90'" class="w-3.5 h-3.5 transition-all duration-200 text-gray-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                            </button>
                            <div x-show="openPenguji" x-transition x-cloak class="absolute w-full mt-1 bg-white border border-gray-300 rounded-[5px] shadow-lg overflow-hidden z-50">
                                <div class="p-2 border-b border-gray-200 bg-gray-50">
                                    <input type="text" x-model="search" class="w-full text-[11px] p-1.5 border border-gray-300 rounded-[3px] outline-none focus:border-[#4285F4]" placeholder="Cari dosen...">
                                </div>
                                <div class="max-h-[200px] overflow-y-auto custom-scrollbar py-1">
                                    <label class="block px-3 py-2 text-[12px] hover:bg-gray-100 cursor-pointer text-black"><input type="radio" value="" x-model="filters.penguji" class="hidden" @change="openPenguji = false; currentPage = 1">Semua Penguji</label>
                                    <template x-for="p in allExaminers" :key="p">
                                        <label x-show="p.toLowerCase().includes(search.toLowerCase())" class="block px-3 py-2 text-[12px] hover:bg-gray-100 cursor-pointer text-black"><input type="radio" :value="p" x-model="filters.penguji" class="hidden" @change="openPenguji = false; currentPage = 1"><span x-text="p"></span></label>
                                    </template>
                                </div>
                            </div>
                        </div>

                        <!-- Status Dropdown -->
                        <div x-data="{ openStatus: false }" class="relative w-full sm:w-[130px] z-[30]">
                            <button type="button" @click="openStatus = !openStatus" @click.outside="openStatus = false" class="w-full text-[12px] border border-gray-300 rounded-[5px] py-2 px-3 bg-white text-black font-medium focus:ring-[#4285F4] flex justify-between items-center text-left shadow-sm">
                                <span x-text="filters.status === '' ? 'Semua Status' : filters.status"></span>
                                <svg :class="openStatus ? 'rotate-0' : 'rotate-90'" class="w-3.5 h-3.5 transition-all duration-200 text-gray-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                            </button>
                            <div x-show="openStatus" x-transition x-cloak class="absolute w-full mt-1 bg-white border border-gray-300 rounded-[5px] shadow-lg overflow-hidden py-1 z-50">
                                <label class="block px-3 py-2 text-[12px] hover:bg-gray-100 cursor-pointer text-black"><input type="radio" value="" x-model="filters.status" class="hidden" @change="openStatus = false; currentPage = 1">Semua Status</label>
                                <label class="block px-3 py-2 text-[12px] hover:bg-gray-100 cursor-pointer text-black"><input type="radio" value="Selesai" x-model="filters.status" class="hidden" @change="openStatus = false; currentPage = 1">Selesai</label>
                                <label class="block px-3 py-2 text-[12px] hover:bg-gray-100 cursor-pointer text-black"><input type="radio" value="Terjadwal" x-model="filters.status" class="hidden" @change="openStatus = false; currentPage = 1">Terjadwal</label>
                            </div>
                        </div>

                        <div class="flex gap-2 w-full sm:w-auto">
                            <button @click="resetFilters()" class="flex-1 sm:flex-none border border-[#EA4335] bg-[#EA4335] text-white hover:bg-red-600 transition-colors px-4 py-1.5 rounded-[5px] text-[12px] font-bold shadow-sm flex items-center justify-center">
                                Clear Filter
                            </button>
                        </div>
                    </div>
                </div>
                
                <div class="border border-gray-200 rounded-[10px] overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full detail-table border-collapse text-[12px] min-w-[800px]">
                        <thead>
                            <tr>
                                <th class="w-12 text-center">No</th>
                                <th class="text-left min-w-[200px]">Nama</th>
                                <th class="text-left min-w-[180px]">Dosen Penguji</th>
                                <th class="text-left min-w-[150px]">Jadwal</th>
                                <th class="text-center w-24">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <template x-for="(session, index) in paginatedSessions" :key="session.id">
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="text-center font-bold" x-text="((currentPage - 1) * itemsPerPage) + index + 1"></td>
                                    <td>
                                        <div class="flex flex-col">
                                            <span class="font-black text-black leading-tight" x-text="session.nama"></span>
                                            <span class="text-[11px] font-bold text-gray-500" x-text="session.nim"></span>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="flex flex-col gap-1">
                                            <template x-for="p in session.penguji" :key="p">
                                                <div class="text-[10px] font-bold text-gray-700 bg-gray-100 px-2 py-0.5 rounded border border-gray-200" x-text="p"></div>
                                            </template>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="flex flex-col">
                                            <span class="font-black text-black leading-tight" x-text="session.jadwal.tanggal"></span>
                                            <span class="text-[10px] font-bold text-blue-600" x-text="session.jadwal.waktu"></span>
                                            <span class="text-[10px] font-bold text-red-600 uppercase" x-text="session.jadwal.ruang"></span>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <div class="flex justify-center">
                                            <div class="text-[10px] font-bold px-3 py-1.5 rounded-[20px] shadow-sm flex items-center justify-center gap-1.5 min-w-[100px] border"
                                                :class="getStatusClass(session)">
                                                <div class="w-1.5 h-1.5 rounded-full" :class="getStatusDotClass(session)"></div>
                                                <span x-text="getExecutionStatus(session)"></span>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            </template>
                            <template x-if="filteredSessions.length === 0">
                                <tr>
                                    <td colspan="5" class="py-12 text-center text-gray-500 italic font-medium">Tidak ada data sidang yang sesuai...</td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination Matching Standard Style -->
                <div class="px-6 py-4 border-t border-gray-200 flex items-center justify-between mt-4" x-show="totalPages > 1">
                    <span class="text-[12px] font-medium text-black/50" x-text="(filteredSessions.length === 0 ? 0 : ((currentPage - 1) * itemsPerPage + 1)) + ' - ' + Math.min(currentPage * itemsPerPage, filteredSessions.length) + ' dari ' + filteredSessions.length + ' baris'"></span>
                    <div class="flex items-center gap-2">
                        <button @click="if(currentPage > 1) currentPage--" :disabled="currentPage === 1" 
                            class="px-3 py-1 border border-gray-300 rounded text-[12px] hover:bg-gray-50 disabled:opacity-30 disabled:cursor-not-allowed transition-colors">Previous</button>
                        <div class="flex items-center gap-1">
                            <template x-for="p in totalPages" :key="p">
                                <button @click="currentPage = p" 
                                    class="w-8 h-8 rounded text-[12px] font-bold transition-all"
                                    :class="currentPage === p ? 'bg-blue-600 text-white shadow-md' : 'text-black hover:bg-gray-100'"
                                    x-text="p"></button>
                            </template>
                        </div>
                        <button @click="if(currentPage < totalPages) currentPage++" :disabled="currentPage === totalPages" 
                            class="px-3 py-1 border border-gray-300 rounded text-[12px] hover:bg-gray-50 disabled:opacity-30 disabled:cursor-not-allowed transition-colors">Next</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Progress Chart Section: Aesthetic, Interactive, Modern -->
        <div class="mt-8 bg-white rounded-[5px] border border-[#CAC0C0] p-8 shadow-sm">
            <div class="flex flex-col md:flex-row items-center gap-12">
                <!-- Pie Chart Column -->
                <div class="w-full md:w-auto flex flex-col items-center">
                    <div class="relative w-[220px] h-[220px]">
                        <canvas id="progressChart"></canvas>
                        <div class="absolute inset-0 flex flex-col items-center justify-center pointer-events-none translate-y-2">
                            <span class="text-[32px] font-black text-black leading-none" x-text="percentage + '%'"></span>
                            <span class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mt-1">Terlaksana</span>
                        </div>
                    </div>
                </div>

                <!-- Info Column -->
                <div class="flex-1 flex flex-col gap-6">
                    <div class="pb-4 border-b border-gray-100">
                        <h3 class="text-[18px] font-black text-black uppercase leading-tight">Progres Pelaksanaan Sidang</h3>
                        <p class="text-[12px] font-medium text-gray-500 mt-1 uppercase tracking-wider">Statistik Akumulasi Berdasarkan Seluruh Data Mahasiswa</p>
                    </div>
                    
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                        <div class="flex flex-col p-4 bg-gray-50 rounded-lg border border-gray-100 transition-all hover:shadow-md">
                            <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Total Mahasiswa</span>
                            <div class="flex items-end gap-2">
                                <span class="text-[28px] font-black text-black leading-none" x-text="allSessions.length"></span>
                                <span class="text-[12px] font-bold text-gray-500 pb-1">Orang</span>
                            </div>
                        </div>

                        <div class="flex flex-col p-4 bg-green-50 rounded-lg border border-green-100 transition-all hover:shadow-md">
                            <span class="text-[10px] font-black text-green-600/50 uppercase tracking-widest mb-1">Sudah Sidang</span>
                            <div class="flex items-end gap-2">
                                <span class="text-[28px] font-black text-green-700 leading-none" x-text="finishedCount"></span>
                                <span class="text-[12px] font-bold text-green-600 pb-1">Selesai</span>
                            </div>
                        </div>

                        <div class="flex flex-col p-4 bg-blue-50 rounded-lg border border-blue-100 transition-all hover:shadow-md">
                            <span class="text-[10px] font-black text-blue-600/50 uppercase tracking-widest mb-1">Antrian Sidang</span>
                            <div class="flex items-end gap-2">
                                <span class="text-[28px] font-black text-blue-700 leading-none" x-text="allSessions.length - finishedCount"></span>
                                <span class="text-[12px] font-bold text-blue-600 pb-1">Terjadwal</span>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center gap-4 mt-2">
                        <div class="flex items-center gap-2">
                            <div class="w-3 h-3 rounded-full bg-[#10B981]"></div>
                            <span class="text-[11px] font-bold text-gray-600 uppercase">Selesai</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <div class="w-3 h-3 rounded-full bg-[#E5E7EB]"></div>
                            <span class="text-[11px] font-bold text-gray-600 uppercase">Sisa Antrian</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        function calendarManager() {
            return {
                allSessions: @json($events),
                currentMonth: new Date().getMonth(),
                currentYear: new Date().getFullYear(),
                now: new Date(),
                monthName: '',
                calendarDays: [],
                selectedDate: null,
                currentPage: 1,
                itemsPerPage: 10,
                chart: null,
                filters: {
                    waktu: '',
                    ruangan: '',
                    penguji: '',
                    status: ''
                },
                allRooms: [],
                allExaminers: [],

                init() {
                    // Extract unique rooms and examiners for filters
                    this.allRooms = [...new Set(this.allSessions.map(s => s.ruangan))].filter(Boolean).sort();
                    this.allExaminers = [...new Set(this.allSessions.flatMap(s => s.penguji))].filter(p => p !== 'BELUM DIPLOT').sort();
                    
                    this.updateCalendar();
                    this.$nextTick(() => this.initChart());

                    setInterval(() => {
                        this.now = new Date();
                    }, 60000);

                    // Watch for filter changes to reset page
                    this.$watch('filters', () => { this.currentPage = 1; this.updateChart(); }, { deep: true });
                    this.$watch('selectedDate', () => { this.currentPage = 1; });
                },

                getExecutionStatus(s) {
                    if (s.pelaksanaan === 'Selesai') return 'Selesai';
                    if (s.pelaksanaan === 'Dibatalkan') return 'Dibatalkan';
                    const start = new Date(`${s.tanggal_sidang}T${s.waktu_mulai_sidang}`);
                    const end = new Date(`${s.tanggal_sidang}T${s.waktu_selesai_sidang}`);
                    if (this.now < start) return 'Menunggu';
                    if (this.now >= start && this.now <= end) return 'Berjalan';
                    return s.pelaksanaan || '-';
                },

                getStatusClass(s) {
                    const status = this.getExecutionStatus(s);
                    if (status === 'Menunggu') return 'bg-[#F9F9F9] text-gray-500 border-gray-300';
                    if (status === 'Berjalan') return 'bg-[#DEF1FF] text-[#1D4ED8] border-[#BFDBFE]';
                    if (status === 'Selesai') return 'bg-[#A1DFAC] text-[#1D5E2D] border-[#BBF7D0]';
                    if (status === 'Dibatalkan') return 'bg-[#FFD3D3] text-[#B91C1C] border-[#FECACA]';
                    return '';
                },

                getStatusDotClass(s) {
                    const status = this.getExecutionStatus(s);
                    if (status === 'Menunggu') return 'bg-gray-400';
                    if (status === 'Berjalan') return 'bg-[#1D4ED8]';
                    if (status === 'Selesai') return 'bg-[#1D5E2D]';
                    if (status === 'Dibatalkan') return 'bg-[#B91C1C]';
                    return '';
                },

                initChart() {
                    const canvas = document.getElementById('progressChart');
                    if (!canvas) return;
                    const ctx = canvas.getContext('2d');
                    this.chart = new Chart(ctx, {
                        type: 'doughnut',
                        data: {
                            labels: ['Selesai', 'Belum'],
                            datasets: [{
                                data: [this.finishedCount, this.allSessions.length - this.finishedCount],
                                backgroundColor: ['#10B981', '#F3F4F6'],
                                borderWidth: 0,
                                hoverOffset: 4,
                                borderRadius: 10,
                                cutout: '85%'
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: { display: false },
                                tooltip: { enabled: true }
                            },
                        }
                    });
                },

                updateChart() {
                    if (this.chart) {
                        this.chart.data.datasets[0].data = [this.finishedCount, this.allSessions.length - this.finishedCount];
                        this.chart.update();
                    }
                },

                get finishedCount() {
                    return this.allSessions.filter(s => s.status === 'Selesai').length;
                },

                get percentage() {
                    if (this.allSessions.length === 0) return 0;
                    return Math.round((this.finishedCount / this.allSessions.length) * 100);
                },

                get totalPages() {
                    return Math.ceil(this.filteredSessions.length / this.itemsPerPage);
                },

                get paginatedSessions() {
                    const start = (this.currentPage - 1) * this.itemsPerPage;
                    return this.filteredSessions.slice(start, start + this.itemsPerPage);
                },

                updateCalendar() {
                    const months = ['JANUARI', 'FEBRUARI', 'MARET', 'APRIL', 'MEI', 'JUNI', 'JULI', 'AGUSTUS', 'SEPTEMBER', 'OKTOBER', 'NOVEMBER', 'DESEMBER'];
                    this.monthName = months[this.currentMonth];
                    
                    const firstDayOfMonth = new Date(this.currentYear, this.currentMonth, 1).getDay();
                    const daysInMonth = new Date(this.currentYear, this.currentMonth + 1, 0).getDate();
                    const daysInLastMonth = new Date(this.currentYear, this.currentMonth, 0).getDate();
                    
                    const days = [];
                    const today = new Date();
                    today.setHours(0,0,0,0);

                    // Prev month padding
                    for (let i = firstDayOfMonth - 1; i >= 0; i--) {
                        days.push({
                            day: daysInLastMonth - i,
                            isCurrentMonth: false,
                            isHoliday: false,
                            isToday: false,
                            sessionCount: 0
                        });
                    }
                    
                    // Current month
                    for (let i = 1; i <= daysInMonth; i++) {
                        const date = new Date(this.currentYear, this.currentMonth, i);
                        const dateString = date.getFullYear() + '-' + String(date.getMonth() + 1).padStart(2, '0') + '-' + String(date.getDate()).padStart(2, '0');
                        const sessionCount = this.allSessions.filter(s => s.tanggal === dateString).length;
                        
                        days.push({
                            day: i,
                            dateString: dateString,
                            isCurrentMonth: true,
                            isToday: date.getTime() === today.getTime(),
                            isHoliday: date.getDay() === 0,
                            sessionCount: sessionCount
                        });
                    }

                    // Next month padding to fill ONLY the remaining slots in the current row (Saturday)
                    const remainingCells = (days.length % 7 === 0) ? 0 : (7 - (days.length % 7));
                    for (let i = 1; i <= remainingCells; i++) {
                        days.push({
                            day: i,
                            isCurrentMonth: false,
                            isHoliday: false,
                            isToday: false,
                            sessionCount: 0
                        });
                    }

                    this.calendarDays = days;
                },

                prevMonth() {
                    if (this.currentMonth === 0) {
                        this.currentMonth = 11;
                        this.currentYear--;
                    } else {
                        this.currentMonth--;
                    }
                    this.updateCalendar();
                },

                nextMonth() {
                    if (this.currentMonth === 11) {
                        this.currentMonth = 0;
                        this.currentYear++;
                    } else {
                        this.currentMonth++;
                    }
                    this.updateCalendar();
                },

                selectDate(dateString) {
                    this.selectedDate = (this.selectedDate === dateString) ? null : dateString;
                },

                resetFilters() {
                    this.filters = { waktu: '', ruangan: '', penguji: '', status: '' };
                    this.selectedDate = null;
                },

                formatReadableDate(ds) {
                    const d = new Date(ds);
                    return d.toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' });
                },

                get filteredSessions() {
                    return this.allSessions.filter(s => {
                        const dateMatch = this.selectedDate ? s.tanggal === this.selectedDate : true;
                        
                        let timeMatch = true;
                        if (this.filters.waktu === 'pagi') {
                            const hour = parseInt(s.waktu_mulai.split(':')[0]);
                            timeMatch = hour < 12;
                        } else if (this.filters.waktu === 'siang') {
                            const hour = parseInt(s.waktu_mulai.split(':')[0]);
                            timeMatch = hour >= 12;
                        }

                        const roomMatch = this.filters.ruangan ? s.ruangan === this.filters.ruangan : true;
                        const pengujiMatch = this.filters.penguji ? s.penguji.includes(this.filters.penguji) : true;
                        const statusMatch = this.filters.status ? s.status === this.filters.status : true;

                        return dateMatch && timeMatch && roomMatch && pengujiMatch && statusMatch;
                    });
                }
            };
        }
    </script>
</x-dashboard-layout>
