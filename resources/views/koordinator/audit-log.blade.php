<x-dashboard-layout header="Audit Log Sistem" userName="{{ auth()->user()->name }}" roleName="KOORDINATOR">
    <x-slot:sidebar>
        @include('koordinator.components.sidebar', ['active' => 'audit-log'])
    </x-slot>

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
        <div class="bg-white rounded-[10px] p-4 sm:p-8 shadow-sm border border-[#CAC0C0]">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
                <div>
                    <h3 class="text-[18px] font-bold text-black uppercase tracking-tight">Tabel Aktivitas User</h3>
                    <p class="text-[12px] text-gray-500 mt-1 font-medium italic">Data diperbarui otomatis setiap 5 detik tanpa refresh</p>
                </div>
            </div>

            <!-- Filters & Search (Responsive Grid) -->
            <form id="filterForm" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3 mb-6">
                <div class="sm:col-span-2 relative">
                    <input type="text" name="search" id="searchInput" value="{{ request('search') }}" placeholder="Cari Nama, NIM, Modul, atau Aksi.." class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-[5px] text-[13px] focus:ring-[#4CC098] focus:border-[#4CC098]">
                    <svg class="w-4 h-4 text-gray-400 absolute left-3 top-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </div>
                
                <select name="role" id="roleFilter" class="border border-gray-300 rounded-[5px] text-[13px] px-4 py-2.5 focus:ring-[#4CC098] focus:border-[#4CC098] bg-white">
                    <option value="">Semua Role</option>
                    <option value="Mahasiswa" {{ request('role') == 'Mahasiswa' ? 'selected' : '' }}>Mahasiswa</option>
                    <option value="Dosen" {{ request('role') == 'Dosen' ? 'selected' : '' }}>Dosen</option>
                    <option value="Koordinator KP" {{ request('role') == 'Koordinator KP' ? 'selected' : '' }}>Koordinator KP</option>
                    <option value="Kaprodi" {{ request('role') == 'Kaprodi' ? 'selected' : '' }}>Kaprodi</option>
                </select>

                <select name="module" id="moduleFilter" class="border border-gray-300 rounded-[5px] text-[13px] px-4 py-2.5 focus:ring-[#4CC098] focus:border-[#4CC098] bg-white">
                    <option value="">Semua Modul</option>
                    <option value="Autentikasi" {{ request('module') == 'Autentikasi' ? 'selected' : '' }}>Autentikasi</option>
                    <option value="User/Profile" {{ request('module') == 'User/Profile' ? 'selected' : '' }}>User/Profile</option>
                    <option value="Pendaftaran KP" {{ request('module') == 'Pendaftaran KP' ? 'selected' : '' }}>Pendaftaran KP</option>
                    <option value="Bimbingan" {{ request('module') == 'Bimbingan' ? 'selected' : '' }}>Bimbingan</option>
                    <option value="Sidang" {{ request('module') == 'Sidang' ? 'selected' : '' }}>Sidang</option>
                    <option value="Penilaian" {{ request('module') == 'Penilaian' ? 'selected' : '' }}>Penilaian</option>
                    <option value="Manajemen Timeline" {{ request('module') == 'Manajemen Timeline' ? 'selected' : '' }}>Timeline</option>
                    <option value="Pengumuman" {{ request('module') == 'Pengumuman' ? 'selected' : '' }}>Pengumuman</option>
                    <option value="Audit Log" {{ request('module') == 'Audit Log' ? 'selected' : '' }}>Audit Log</option>
                </select>

                <div class="flex gap-2">
                    <button type="submit" class="flex-1 bg-[#4CC098] text-white font-bold text-[12px] rounded-[5px] uppercase tracking-wide hover:bg-[#3da884] transition-all shadow-md active:scale-95">Filter</button>
                    <button type="button" id="resetFilters" class="px-4 flex items-center justify-center bg-gray-200 text-gray-700 rounded-[5px] hover:bg-gray-300 transition-all active:scale-95">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                    </button>
                </div>
            </form>

            <!-- Table Container with Loading Overlay -->
            <div class="relative">
                <div id="tableLoading" class="absolute inset-0 bg-white/50 backdrop-blur-[1px] z-10 flex items-center justify-center opacity-0 pointer-events-none transition-opacity">
                    <div class="w-8 h-8 border-4 border-[#4CC098] border-t-transparent rounded-full animate-spin"></div>
                </div>

                <div class="overflow-x-auto rounded-t-[5px] border-x border-t border-gray-300 shadow-sm">
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
            <div class="mt-6 flex justify-end" id="paginationContainer">
                {{ $logs->appends(request()->query())->links() }}
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
