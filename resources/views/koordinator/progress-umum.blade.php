<x-dashboard-layout header="Progress Umum Bimbingan Mahasiswa" :userName="auth()->user()->name"
    roleName="KOORDINATOR KP">
    <x-slot:sidebar>
        @include('koordinator.components.sidebar', ['active' => 'progress-umum'])
        </x-slot>

        <style>
            [x-cloak] {
                display: none !important;
            }

            .custom-scrollbar::-webkit-scrollbar {
                width: 5px;
                height: 5px;
            }

            .custom-scrollbar::-webkit-scrollbar-track {
                background: #f1f1f1;
            }

            .custom-scrollbar::-webkit-scrollbar-thumb {
                background: #ccc;
                border-radius: 10px;
            }

            .custom-scrollbar::-webkit-scrollbar-thumb:hover {
                background: #999;
            }

            .analytics-card {
                transition: all 0.8s cubic-bezier(0.16, 1, 0.3, 1);
                transform: translateY(20px);
                opacity: 0;
            }

            .analytics-card.visible {
                transform: translateY(0);
                opacity: 1;
            }
        </style>

        <div class="mt-8 px-4 w-full" x-data="{ 
        searchQuery: '',
        pembimbingFilter: 'all',
        sortOrder: 'none',
        currentPage: 1,
        itemsPerPage: 15,
        pendaftarans: {{ \Illuminate\Support\Js::from($pendaftarans->map(fn($p) => [
    'id' => $p['id'],
    'mhs_id' => $p['display_mahasiswa']->user_id,
    'nama' => $p['display_mahasiswa']->user->name ?? ($p['display_mahasiswa']->nama ?? '-'),
    'nim' => $p['display_mahasiswa']->nim ?? '-',
    'judul' => $p['display_judul_kp'] ?? '-',
    'instansi' => $p['display_instansi'] ?? '-',
    'supervisor' => $p['display_supervisor'] ?? '-',
    'pembimbing' => $p['display_pembimbing'] ?? '-',
    'total_log' => (int) ($p['total_log'] ?? 0),
])) }},

        get filteredList() {
            let list = this.pendaftarans.filter(p => {
                const term = (this.searchQuery || '').toLowerCase();
                const matchesSearch = !this.searchQuery || 
                    (p.nama || '').toLowerCase().includes(term) ||
                    (p.nim || '').toLowerCase().includes(term) ||
                    (p.judul || '').toLowerCase().includes(term) ||
                    (p.instansi || '').toLowerCase().includes(term) ||
                    (p.supervisor || '').toLowerCase().includes(term);
                
                const matchesPembimbing = this.pembimbingFilter === 'all' || p.pembimbing === this.pembimbingFilter;
                    
                return matchesSearch && matchesPembimbing;
            });

            if (this.sortOrder === 'high') {
                list.sort((a, b) => b.total_log - a.total_log);
            } else if (this.sortOrder === 'low') {
                list.sort((a, b) => a.total_log - b.total_log);
            }

            return list;
        },

        get totalPages() {
            return Math.ceil(this.filteredList.length / this.itemsPerPage) || 1;
        },

        get paginatedList() {
            const start = (this.currentPage - 1) * this.itemsPerPage;
            return this.filteredList.slice(start, start + this.itemsPerPage);
        },

        nextPage() { if (this.currentPage < this.totalPages) this.currentPage++; },
        prevPage() { if (this.currentPage > 1) this.currentPage--; },
        goToPage(page) { this.currentPage = page; },

        resetPagination() { this.currentPage = 1; }
    }">

            <!-- Page Description Box -->
            <div
                class="bg-[#E6F0FA] border border-[#D0E3F5] rounded-[10px] p-4 lg:p-5 mb-8 flex items-start gap-4 shadow-sm">
                <div
                    class="w-6 h-6 rounded-full bg-[#4285F4] text-white flex items-center justify-center font-bold flex-shrink-0 mt-0.5">
                    i
                </div>
                <p class="text-[14px] text-[#1A1A1A] font-medium leading-relaxed m-0 mt-0.5">
                    Pantau progres bimbingan seluruh mahasiswa secara individu. Gunakan fitur pencarian dan filter untuk
                    menyaring data berdasarkan dosen pembimbing atau jumlah bimbingan yang telah disahkan.
                </p>
            </div>

            <!-- Table Box -->
            <div class="bg-white border border-gray-300 rounded-[10px] overflow-hidden shadow-sm mb-12">
                <div class="bg-[#EBEBEB] border-b border-gray-300 p-4 lg:p-6">
                    <div class="flex flex-wrap items-center gap-4">
                        <div class="relative flex-1 min-w-[300px]">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                            </div>
                            <input type="text" x-model="searchQuery" @input="resetPagination()"
                                class="block w-full pl-9 pr-4 py-2 border border-gray-300 rounded-[5px] text-[13px] text-black focus:ring-1 focus:ring-blue-500 shadow-sm"
                                placeholder="Cari Nama, NIM, Judul, Instansi...">
                        </div>

                        <div class="flex items-center gap-2">
                            <label class="text-[12px] font-bold text-black uppercase whitespace-nowrap">Pembimbing
                                :</label>
                            <select x-model="pembimbingFilter" @change="resetPagination()"
                                class="w-[200px] text-[12px] border border-gray-300 rounded-[5px] py-2 px-3 bg-white text-black font-medium focus:outline-none focus:ring-1 focus:ring-blue-500 shadow-sm">
                                <option value="all">Semua Dosen</option>
                                @foreach($dosens as $dosen)
                                    <option value="{{ $dosen->name }}">{{ $dosen->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="flex items-center gap-2">
                            <label class="text-[12px] font-bold text-black uppercase whitespace-nowrap">Urutkan
                                :</label>
                            <select x-model="sortOrder" @change="resetPagination()"
                                class="w-[180px] text-[12px] border border-gray-300 rounded-[5px] py-2 px-3 bg-white text-black font-medium focus:outline-none focus:ring-1 focus:ring-blue-500 shadow-sm">
                                <option value="none">Default</option>
                                <option value="high">Bimbingan Terbanyak</option>
                                <option value="low">Bimbingan Terendah</option>
                            </select>
                        </div>

                        <button
                            @click="searchQuery = ''; pembimbingFilter = 'all'; sortOrder = 'none'; resetPagination()"
                            class="bg-[#EA3323] hover:bg-red-700 text-white font-bold text-[11px] px-5 py-2.5 rounded-[5px] shadow-md transition-all uppercase whitespace-nowrap flex items-center gap-2">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                    d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                            Clear Filter
                        </button>
                    </div>
                </div>

                <div class="overflow-x-auto custom-scrollbar">
                    <table class="w-full border-collapse text-[11px]">
                        <thead>
                            <tr class="bg-[#EBEBEB] text-black text-center">
                                <th
                                    class="py-3 px-4 font-bold w-[50px] border-b border-gray-300 border-r border-gray-300">
                                    No</th>
                                <th
                                    class="py-3 px-4 font-bold text-left border-b border-gray-300 border-r border-gray-300">
                                    Mahasiswa</th>
                                <th class="py-3 px-4 font-bold border-b border-gray-300 border-r border-gray-300">Judul
                                    KP</th>
                                <th class="py-3 px-4 font-bold border-b border-gray-300 border-r border-gray-300">
                                    Instansi</th>
                                <th
                                    class="py-3 px-4 font-bold border-b border-gray-300 border-r border-gray-300 text-blue-700">
                                    Pembimbing</th>
                                <th class="py-3 px-4 font-bold border-b border-gray-300">Total Bimbingan</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            <template x-for="(p, index) in paginatedList" :key="p.mhs_id">
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="py-3 px-4 text-center text-black/60 border-r border-gray-200"
                                        x-text="(currentPage - 1) * itemsPerPage + index + 1"></td>
                                    <td class="py-3 px-4 text-left border-r border-gray-200">
                                        <div class="font-bold text-black text-[12px]" x-text="p.nama"></div>
                                        <div class="text-black/60 text-[10px]" x-text="p.nim"></div>
                                    </td>
                                    <td class="py-3 px-4 text-center text-black/80 font-medium leading-relaxed border-r border-gray-200 text-[11px]"
                                        x-text="p.judul"></td>
                                    <td class="py-3 px-4 text-center text-black/70 italic font-medium border-r border-gray-200 text-[11px]"
                                        x-text="p.instansi"></td>
                                    <td class="py-3 px-4 text-center text-blue-800 font-bold text-[10px] border-r border-gray-200"
                                        x-text="p.pembimbing"></td>
                                    <td class="py-3 px-4 text-center">
                                        <span
                                            class="bg-blue-50 text-blue-800 border border-blue-200 px-3 py-1 rounded-full font-bold text-[11px] shadow-sm">
                                            <span x-text="p.total_log"></span> <span
                                                class="text-[9px] font-bold text-blue-400">/ 12</span>
                                        </span>
                                    </td>
                                </tr>
                            </template>
                            <template x-if="filteredList.length === 0">
                                <tr>
                                    <td colspan="6"
                                        class="text-center py-20 text-gray-400 italic bg-gray-50 uppercase tracking-widest font-medium">
                                        Tidak ada data bimbingan mahasiswa yang ditemukan
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>

                <div class="px-6 py-4 bg-white border-t border-gray-200 flex items-center justify-between"
                    x-show="totalPages > 1">
                    <span class="text-[12px] font-medium text-black/50"
                        x-text="`Halaman ${currentPage} dari ${totalPages}`"></span>
                    <div class="flex items-center gap-2">
                        <button @click="prevPage" :disabled="currentPage === 1"
                            class="px-3 py-1 border border-gray-300 rounded text-[12px] hover:bg-gray-50 disabled:opacity-30 disabled:cursor-not-allowed transition-colors">Previous</button>
                        <div class="flex items-center gap-1">
                            <template x-for="p in totalPages" :key="p">
                                <button @click="goToPage(p)"
                                    class="w-8 h-8 rounded text-[12px] font-bold transition-all"
                                    :class="currentPage === p ? 'bg-blue-600 text-white shadow-md' : 'text-black hover:bg-gray-100'"
                                    x-text="p"></button>
                            </template>
                        </div>
                        <button @click="nextPage" :disabled="currentPage === totalPages"
                            class="px-3 py-1 border border-gray-300 rounded text-[12px] hover:bg-gray-50 disabled:opacity-30 disabled:cursor-not-allowed transition-colors">Next</button>
                    </div>
                </div>
            </div>

            @php
                $totalMhs = count($pendaftarans);

                // Rumus baru: epsilon (jumlah bimbingan / 12, max 1.0) / total_mahasiswa
                $sumRatios = 0;
                foreach ($pendaftarans as $p) {
                    $ratio = $p['total_log'] / 12;
                    if ($ratio > 1)
                        $ratio = 1;
                    $sumRatios += $ratio;
                }

                $overallPercent = $totalMhs > 0 ? ($sumRatios / $totalMhs) * 100 : 0;
                // Gunakan format 1 desimal agar progres kecil tidak terlihat 0
                $displayPercent = number_format($overallPercent, 1);

                $countBelum = $pendaftarans->where('total_log', 0)->count();
                $countDimulai = $pendaftarans->whereBetween('total_log', [1, 11])->count();
                $countMemenuhi = $pendaftarans->where('total_log', 12)->count();
            @endphp

            <!-- Simplified Analytics Section -->
            <div class="analytics-card bg-white rounded-[15px] border border-gray-200 shadow-lg p-6 lg:p-8 mb-16 max-w-4xl mx-auto"
                id="analyticsSection">
                <div class="flex flex-col md:flex-row items-center gap-12">

                    <!-- Left: Status Stats -->
                    <div class="flex-1 w-full space-y-4">
                        <div class="flex justify-between items-center pb-2 border-b border-gray-100">
                            <h3 class="text-[16px] font-bold text-black uppercase tracking-tight">Rekap Bimbingan</h3>

                        </div>

                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg border border-gray-100">
                            <div class="flex items-center gap-3">
                                <div class="w-2 h-2 rounded-full bg-gray-400"></div>
                                <span class="text-[13px] font-medium text-gray-600">Belum Mulai</span>
                            </div>
                            <span class="text-[16px] font-black text-gray-700">{{ $countBelum }}</span>
                        </div>

                        <div class="flex items-center justify-between p-3 bg-blue-50 rounded-lg border border-blue-100">
                            <div class="flex items-center gap-3">
                                <div class="w-2 h-2 rounded-full bg-blue-500 animate-pulse"></div>
                                <span class="text-[13px] font-medium text-blue-700">Dalam Proses (1-11)</span>
                            </div>
                            <span class="text-[16px] font-black text-blue-800">{{ $countDimulai }}</span>
                        </div>

                        <div
                            class="flex items-center justify-between p-3 bg-green-50 rounded-lg border border-green-100">
                            <div class="flex items-center gap-3">
                                <div class="w-2 h-2 rounded-full bg-green-500"></div>
                                <span class="text-[13px] font-medium text-green-700">Memenuhi (12)</span>
                            </div>
                            <span class="text-[16px] font-black text-green-800">{{ $countMemenuhi }}</span>
                        </div>
                    </div>

                    <!-- Right: Circular Progress -->
                    <div class="shrink-0 flex flex-col items-center">
                        <div class="relative w-40 h-40">
                            <canvas id="overallProgressChart"></canvas>
                            <div class="absolute inset-0 flex flex-col items-center justify-center">
                                <span
                                    class="text-[28px] font-black text-black leading-none">{{ $displayPercent }}%</span>
                                <span class="text-[10px] font-bold text-gray-400 uppercase mt-1">Total Progres</span>
                            </div>
                        </div>
                        <p
                            class="text-[11px] text-center text-gray-400 mt-4 leading-tight font-medium uppercase tracking-widest">
                            Rangkuman Seluruh<br>Bimbingan Mahasiswa
                        </p>
                    </div>

                </div>
            </div>

            <div class="h-10"></div>
        </div>

        <!-- Scripts for Chart -->
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            function initChart() {
                const analyticsSection = document.getElementById('analyticsSection');
                if (!analyticsSection) return;

                const chartCanvas = document.getElementById('overallProgressChart');
                if (!chartCanvas) return;

                const ctx = chartCanvas.getContext('2d');

                const config = {
                    type: 'doughnut',
                    data: {
                        datasets: [{
                            data: [{{ $overallPercent }}, {{ 100 - $overallPercent }}],
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

                let chartInstance = null;

                const observer = new IntersectionObserver((entries) => {
                    entries.forEach(entry => {
                        if (entry.isIntersecting) {
                            entry.target.classList.add('visible');
                            if (!chartInstance && typeof Chart !== 'undefined') {
                                chartInstance = new Chart(ctx, config);
                            }
                            observer.unobserve(entry.target);
                        }
                    });
                }, { threshold: 0.1 });

                observer.observe(analyticsSection);
            }

            // Listen for both DOMContentLoaded (fresh load) and turbo:load (Turbo navigation)
            document.addEventListener('DOMContentLoaded', initChart);
            document.addEventListener('turbo:load', initChart);
        </script>
</x-dashboard-layout>