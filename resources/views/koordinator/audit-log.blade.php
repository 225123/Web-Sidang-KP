<x-dashboard-layout header="Audit Log Sistem" userName="{{ auth()->user()->name }}" roleName="KOORDINATOR">
    <x-slot:sidebar>
        @include('koordinator.components.sidebar', ['active' => 'audit-log'])
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

    <div class="mt-6 max-w-7xl mx-auto space-y-6 px-4 sm:px-6 lg:px-8">
        <!-- Top Charts Section -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Line Chart: System Activity -->
            <div class="lg:col-span-2 bg-white rounded-[10px] p-6 shadow-sm border border-[#CAC0C0] flex flex-col">
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
                    <div>
                        <h3 class="text-[16px] font-bold text-black uppercase tracking-tight">Grafik Aktivitas Sistem</h3>
                        <p class="text-[11px] text-gray-500">Visualisasi beban kerja sistem secara real-time (Gunakan mouse untuk menggeser/zoom)</p>
                    </div>
                    <div class="flex items-center gap-2 w-full sm:w-auto">
                        <select id="timeframeSelector" class="w-full sm:w-auto text-[11px] font-bold border-gray-300 rounded-[5px] py-1.5 px-3 focus:ring-[#4CC098] focus:border-[#4CC098]">
                            <option value="minute" {{ $timeframe == 'minute' ? 'selected' : '' }}>Menit</option>
                            <option value="hour" {{ $timeframe == 'hour' ? 'selected' : '' }}>Jam</option>
                            <option value="day" {{ $timeframe == 'day' ? 'selected' : '' }}>Hari</option>
                            <option value="month" {{ $timeframe == 'month' ? 'selected' : '' }}>Bulan</option>
                        </select>
                        <div class="bg-red-500 w-2 h-2 rounded-full animate-pulse ml-2" title="Live Updating"></div>
                    </div>
                </div>
                <div class="h-[300px] w-full cursor-grab active:cursor-grabbing" id="activityChart">
                    <!-- ApexCharts will render here -->
                </div>
            </div>

            <!-- Donut Chart: Average Activity -->
            <div class="bg-white rounded-[10px] p-6 shadow-sm border border-[#CAC0C0] flex flex-col items-center">
                <h3 class="text-[14px] font-bold text-black uppercase tracking-tight mb-4 self-start">Statistik Keaktifan User</h3>
                
                <!-- Main Donut -->
                <div class="h-[180px] w-full relative flex items-center justify-center mb-6">
                    <canvas id="donutChart"></canvas>
                    <div class="absolute inset-0 flex flex-col items-center justify-center pt-2">
                        <span class="text-[28px] font-black text-[#4285F4]" id="donutPercent">{{ $donutData['active_percent'] }}%</span>
                        <span class="text-[10px] text-gray-400 font-bold uppercase">Aktif</span>
                    </div>
                </div>

                <!-- Stats Details (Filling the space) -->
                <div class="w-full grid grid-cols-2 gap-4 border-t border-gray-100 pt-6">
                    <div class="text-center">
                        <p class="text-[10px] text-gray-400 font-bold uppercase mb-1">Total User</p>
                        <p class="text-[18px] font-black text-black" id="totalCountDisplay">{{ $donutData['total_count'] }}</p>
                    </div>
                    <div class="text-center border-l border-gray-100">
                        <p class="text-[10px] text-gray-400 font-bold uppercase mb-1">User Aktif</p>
                        <p class="text-[18px] font-black text-[#4285F4]" id="activeCountDisplay">{{ $donutData['active_count'] }}</p>
                    </div>
                </div>

                <div class="mt-6 p-4 bg-blue-50 rounded-[8px] w-full">
                    <p class="text-[11px] text-[#4285F4] font-medium text-center italic" id="donutLabel">
                        {{ $donutData['label'] }}
                    </p>
                </div>
            </div>
        </div>

        <!-- Table Section -->
        <div class="bg-white rounded-[15px] border border-gray-200 shadow-sm overflow-hidden p-6 mb-8">
            <div class="mb-6 flex flex-col sm:flex-row justify-between items-start gap-4">
                <div>
                    <h3 class="text-[18px] font-bold text-black tracking-tight">Tabel Aktivitas User</h3>
                    <p class="text-[12px] text-black/60 font-medium mt-1">Data diperbarui otomatis setiap 5 detik tanpa refresh.</p>
                </div>
            </div>

            <!-- Filters & Search (Responsive Grid) -->
            <form id="filterForm" class="flex flex-col sm:flex-row justify-between items-center gap-6 mb-6" x-data="{ role: '{{ request('role') }}', module: '{{ request('module') }}' }">
                <input type="hidden" id="roleFilter" name="role" :value="role">
                <input type="hidden" id="moduleFilter" name="module" :value="module">
                
                <div class="flex flex-wrap items-center gap-4 w-full sm:w-auto">
                    <!-- Search Input -->
                    <div class="relative flex-1 sm:w-[300px]">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        </div>
                        <input type="text" name="search" id="searchInput" value="{{ request('search') }}" class="block w-full pl-9 pr-4 py-2 border border-gray-300 rounded-[5px] text-[12px] text-black focus:ring-[#4285F4]" placeholder="Cari Nama, NIM, Modul, atau Aksi...">
                    </div>

                    <!-- Role Filter Dropdown -->
                    <div x-data="{ openRole: false }" class="relative w-full sm:w-[180px] z-[60]">
                        <button type="button" @click="openRole = !openRole" @click.outside="openRole = false" class="w-full text-[12px] border border-gray-300 rounded-[5px] py-2 px-3 bg-white text-black font-medium focus:ring-[#4285F4] flex justify-between items-center text-left shadow-sm">
                            <span x-text="role === '' ? 'Semua Role' : role"></span>
                            <svg :class="openRole ? 'rotate-0' : 'rotate-90'" class="w-3.5 h-3.5 transition-all duration-200 text-gray-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <div x-show="openRole" x-transition x-cloak class="absolute w-full mt-1 bg-white border border-gray-300 rounded-[5px] shadow-lg overflow-hidden py-1 z-50">
                            <label class="block px-3 py-2 text-[12px] hover:bg-gray-100 cursor-pointer text-black"><input type="radio" value="" x-model="role" class="hidden" @change="openRole = false">Semua Role</label>
                            <label class="block px-3 py-2 text-[12px] hover:bg-gray-100 cursor-pointer text-black"><input type="radio" value="Mahasiswa" x-model="role" class="hidden" @change="openRole = false">Mahasiswa</label>
                            <label class="block px-3 py-2 text-[12px] hover:bg-gray-100 cursor-pointer text-black"><input type="radio" value="Dosen" x-model="role" class="hidden" @change="openRole = false">Dosen</label>
                            <label class="block px-3 py-2 text-[12px] hover:bg-gray-100 cursor-pointer text-black"><input type="radio" value="Koordinator KP" x-model="role" class="hidden" @change="openRole = false">Koordinator KP</label>
                            <label class="block px-3 py-2 text-[12px] hover:bg-gray-100 cursor-pointer text-black"><input type="radio" value="Kaprodi" x-model="role" class="hidden" @change="openRole = false">Kaprodi</label>
                        </div>
                    </div>

                    <!-- Module Filter Dropdown -->
                    <div x-data="{ openModule: false }" class="relative w-full sm:w-[180px] z-[50]">
                        <button type="button" @click="openModule = !openModule" @click.outside="openModule = false" class="w-full text-[12px] border border-gray-300 rounded-[5px] py-2 px-3 bg-white text-black font-medium focus:ring-[#4285F4] flex justify-between items-center text-left shadow-sm">
                            <span x-text="module === '' ? 'Semua Modul' : module"></span>
                            <svg :class="openModule ? 'rotate-0' : 'rotate-90'" class="w-3.5 h-3.5 transition-all duration-200 text-gray-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <div x-show="openModule" x-transition x-cloak class="absolute w-full mt-1 bg-white border border-gray-300 rounded-[5px] shadow-lg overflow-hidden py-1 z-50">
                            <label class="block px-3 py-2 text-[12px] hover:bg-gray-100 cursor-pointer text-black"><input type="radio" value="" x-model="module" class="hidden" @change="openModule = false">Semua Modul</label>
                            <label class="block px-3 py-2 text-[12px] hover:bg-gray-100 cursor-pointer text-black"><input type="radio" value="Autentikasi" x-model="module" class="hidden" @change="openModule = false">Autentikasi</label>
                            <label class="block px-3 py-2 text-[12px] hover:bg-gray-100 cursor-pointer text-black"><input type="radio" value="User/Profile" x-model="module" class="hidden" @change="openModule = false">User/Profile</label>
                            <label class="block px-3 py-2 text-[12px] hover:bg-gray-100 cursor-pointer text-black"><input type="radio" value="Pendaftaran KP" x-model="module" class="hidden" @change="openModule = false">Pendaftaran KP</label>
                            <label class="block px-3 py-2 text-[12px] hover:bg-gray-100 cursor-pointer text-black"><input type="radio" value="Bimbingan" x-model="module" class="hidden" @change="openModule = false">Bimbingan</label>
                            <label class="block px-3 py-2 text-[12px] hover:bg-gray-100 cursor-pointer text-black"><input type="radio" value="Sidang" x-model="module" class="hidden" @change="openModule = false">Sidang</label>
                            <label class="block px-3 py-2 text-[12px] hover:bg-gray-100 cursor-pointer text-black"><input type="radio" value="Penilaian" x-model="module" class="hidden" @change="openModule = false">Penilaian</label>
                            <label class="block px-3 py-2 text-[12px] hover:bg-gray-100 cursor-pointer text-black"><input type="radio" value="Manajemen Timeline" x-model="module" class="hidden" @change="openModule = false">Timeline</label>
                            <label class="block px-3 py-2 text-[12px] hover:bg-gray-100 cursor-pointer text-black"><input type="radio" value="Pengumuman" x-model="module" class="hidden" @change="openModule = false">Pengumuman</label>
                            <label class="block px-3 py-2 text-[12px] hover:bg-gray-100 cursor-pointer text-black"><input type="radio" value="Audit Log" x-model="module" class="hidden" @change="openModule = false">Audit Log</label>
                        </div>
                    </div>

                    <div class="flex gap-2 w-full sm:w-auto">
                        <button type="submit" class="flex-1 sm:flex-none border border-[#34A853] bg-[#34A853] text-white hover:bg-green-700 transition-colors px-4 py-1.5 rounded-[5px] text-[12px] font-bold shadow-sm flex items-center justify-center">
                            Filter
                        </button>
                        <button type="button" @click="role = ''; module = ''; document.getElementById('searchInput').value = ''; document.getElementById('filterForm').dispatchEvent(new Event('submit', { cancelable: true }))" id="resetFilters" class="flex-1 sm:flex-none border border-[#EA4335] bg-[#EA4335] text-white hover:bg-red-600 transition-colors px-4 py-1.5 rounded-[5px] text-[12px] font-bold shadow-sm flex items-center justify-center">
                            Clear Filter
                        </button>
                    </div>
                </div>
            </form>

            <!-- Table Container with Loading Overlay -->
            <div class="relative border border-gray-200 rounded-[10px] overflow-hidden">
                <div id="tableLoading" class="absolute inset-0 bg-white/50 backdrop-blur-[1px] z-10 flex items-center justify-center opacity-0 pointer-events-none transition-opacity">
                    <div class="w-8 h-8 border-4 border-[#4CC098] border-t-transparent rounded-full animate-spin"></div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-center border-collapse">
                        <thead>
                            <tr class="bg-[#BBB8B8] text-black text-[13px] font-bold uppercase tracking-wide">
                                <th class="py-4 px-6 border-r border-gray-300 min-w-[150px]">User (ID)</th>
                                <th class="py-4 px-6 border-r border-gray-300 min-w-[120px]">Role</th>
                                <th class="py-4 px-6 border-r border-gray-300 min-w-[120px]">Modul</th>
                                <th class="py-4 px-6 border-r border-gray-300 min-w-[150px]">Action</th>
                                <th class="py-4 px-6 min-w-[180px]">Waktu</th>
                            </tr>
                        </thead>
                        <tbody id="auditLogTableBody" class="text-[13px] font-medium text-black bg-white divide-y divide-gray-300 border-b border-gray-300">
                            @include('koordinator.components.audit-log-table-rows', ['logs' => $logs])
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Custom Pagination Container -->
            <div class="mt-6" id="paginationContainer">
                {{ $logs->appends(request()->query())->links('vendor.pagination.custom') }}
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script> <!-- Keep for donut if needed, or switch both -->

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            let activityChart;
            let donutChart;
            let currentTimeframe = '{{ $timeframe }}';

            // 1. ApexCharts for System Activity
            const activityElement = document.querySelector("#activityChart");
            if (activityElement) {
                const options = {
                    series: [{
                        name: 'Aktivitas',
                        data: {!! json_encode($chartData['data'] ?? []) !!}
                    }],
                    chart: {
                        type: 'area',
                        height: 300,
                        toolbar: { 
                            show: true,
                            tools: {
                                download: true,
                                selection: false,
                                zoom: false,
                                zoomin: false,
                                zoomout: false,
                                pan: false,
                                reset: false
                            }
                        },
                        animations: {
                            enabled: true,
                            easing: 'linear',
                            dynamicAnimation: { speed: 1000 }
                        },
                        zoom: { enabled: true },
                        pan: { enabled: true }
                    },
                    dataLabels: { enabled: false },
                    stroke: { curve: 'smooth', width: 3 },
                    fill: {
                        type: 'gradient',
                        gradient: {
                            shadeIntensity: 1,
                            opacityFrom: 0.7,
                            opacityTo: 0.2,
                            stops: [0, 90, 100]
                        }
                    },
                    colors: ['#4285F4'],
                    xaxis: {
                        categories: {!! json_encode($chartData['labels'] ?? []) !!},
                        tickAmount: currentTimeframe === 'minute' ? 10 : (currentTimeframe === 'hour' ? 12 : 10),
                        labels: { 
                            show: true,
                            rotate: -45,
                            style: { fontSize: '10px', fontWeight: 600 } 
                        }
                    },
                    yaxis: {
                        labels: { style: { fontSize: '10px', fontWeight: 600 } }
                    },
                    tooltip: { theme: 'dark', x: { show: true } },
                    grid: { borderColor: '#f1f1f1' }
                };

                activityChart = new ApexCharts(activityElement, options);
                activityChart.render();
            }

            // 2. Chart.js for Donut
            const donutElement = document.getElementById('donutChart');
            if (donutElement) {
                const ctxDonut = donutElement.getContext('2d');
                donutChart = new Chart(ctxDonut, {
                    type: 'doughnut',
                    data: {
                        datasets: [{
                            data: [{{ $donutData['active_percent'] ?? 0 }}, {{ 100 - ($donutData['active_percent'] ?? 0) }}],
                            backgroundColor: ['#4285F4', '#f1f5f9'],
                            borderWidth: 0,
                            cutout: '82%'
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: { legend: { display: false }, tooltip: { enabled: true } }
                    }
                });
            }

            // Handle Pagination Click
            document.getElementById('paginationContainer').addEventListener('click', function(e) {
                const link = e.target.closest('a');
                if (link && link.href) {
                    e.preventDefault();
                    refreshData(link.href);
                }
            });

            // Refresh Function
            function refreshData(url = null) {
                const formData = new FormData(document.getElementById('filterForm'));
                const searchParams = new URLSearchParams(formData);
                searchParams.append('timeframe', currentTimeframe);

                const fetchUrl = url || `{{ route('koordinator.audit-log.index') }}?${searchParams.toString()}`;

                fetch(fetchUrl, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                })
                .then(response => response.json())
                .then(data => {
                    // Update Table
                    const tableBody = document.getElementById('auditLogTableBody');
                    if (tableBody) tableBody.innerHTML = data.table;
                    
                    const pagContainer = document.getElementById('paginationContainer');
                    if (pagContainer) pagContainer.innerHTML = data.pagination;

                    // Update ApexChart (Simultaneous Update)
                    if (activityChart) {
                        activityChart.updateOptions({
                            xaxis: { 
                                categories: data.chartData.labels,
                                tickAmount: currentTimeframe === 'minute' ? 10 : (currentTimeframe === 'hour' ? 12 : 10)
                            },
                            series: [{
                                data: data.chartData.data
                            }]
                        }, false, true);
                    }

                    // Update Donut
                    if (donutChart) {
                        donutChart.data.datasets[0].data = [data.donutData.active_percent, 100 - data.donutData.active_percent];
                        donutChart.update();
                    }

                    // Update Stats
                    const percentEl = document.getElementById('donutPercent');
                    if (percentEl) percentEl.textContent = data.donutData.active_percent + '%';
                    
                    const activeCountEl = document.getElementById('activeCountDisplay');
                    if (activeCountEl) activeCountEl.textContent = data.donutData.active_count;
                    
                    const totalCountEl = document.getElementById('totalCountDisplay');
                    if (totalCountEl) totalCountEl.textContent = data.donutData.total_count;
                    
                    const labelEl = document.getElementById('donutLabel');
                    if (labelEl) labelEl.textContent = data.donutData.label;
                })
                .catch(err => console.warn('Polling suspended due to network or server error.'));
            }

            document.getElementById('filterForm').addEventListener('submit', (e) => { e.preventDefault(); refreshData(); });
            document.getElementById('resetFilters').addEventListener('click', () => { document.getElementById('filterForm').reset(); refreshData(); });
            document.getElementById('timeframeSelector').addEventListener('change', function() { currentTimeframe = this.value; refreshData(); });

            setInterval(() => refreshData(), 5000);
        });
    </script>

    <style>
        .sentence-case { text-transform: lowercase; }
        .sentence-case::first-letter { text-transform: uppercase; }
        #activityChart { width: 100% !important; min-height: 300px; }
    </style>
</x-dashboard-layout>
