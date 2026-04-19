<x-dashboard-layout header="Verifikasi Berkas Sidang KP" userName="{{ auth()->user()->name }}" roleName="KOORDINATOR KP">
    <x-slot:sidebar>
        @include('koordinator.components.sidebar', ['active' => 'verifikasi'])
    </x-slot>

    <style>
        :root {
            --green-main: #40916c;
            --orange-main: #fcc419;
            --red-main: #e03131;
            --light-blue-bg: #e7f0ff;
            --gray-border: #dee2e6;
            --gray-header: #d9d9d9;
        }

        /* Top Header Section */
        .top-banner {
            display: flex;
            gap: 20px;
            margin-bottom: 30px;
            align-items: stretch;
            flex-wrap: wrap;
        }

        .info-box {
            flex: 3;
            min-width: 300px;
            background-color: var(--light-blue-bg);
            border-radius: 12px;
            padding: 20px;
            display: flex;
            align-items: center;
            gap: 15px;
            font-size: 14px;
            font-weight: 500;
            line-height: 1.4;
        }

        .info-icon {
            background-color: #4dabf7;
            color: white;
            width: auto;
            height: auto;
            border-radius: 8px;
            padding: 4px 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            font-size: 14px;
            font-weight: bold;
        }

        .stats-group {
            flex: 1.2;
            min-width: 300px;
            display: flex;
            gap: 10px;
        }

        .stat-card {
            flex: 1;
            border-radius: 10px;
            color: white;
            text-align: center;
            padding: 15px 10px 10px 10px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            position: relative;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }

        .stat-badge {
            position: absolute;
            top: 5px;
            left: 5px;
            background-color: rgba(255,255,255,0.2);
            padding: 2px 6px;
            border-radius: 4px;
            font-size: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .stat-number { font-size: 24px; font-weight: bold; margin: 5px 0; }
        .stat-text { font-size: 10px; line-height: 1.2; }

        .bg-green { background-color: var(--green-main); }
        .bg-orange { background-color: var(--orange-main); color: #000; }
        .bg-red { background-color: var(--red-main); }

        /* Filter & Search Bar */
        .filter-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
            flex-wrap: wrap;
            gap: 10px;
        }

        .search-box {
            position: relative;
            width: 350px;
            max-width: 100%;
        }

        .search-box input {
            width: 100%;
            padding: 8px 10px 8px 35px;
            border: 1px solid #ced4da;
            border-radius: 6px;
            font-size: 13px;
        }

        .search-icon {
            position: absolute;
            left: 10px;
            top: 50%;
            transform: translateY(-50%);
            color: #999;
        }

        .controls {
            display: flex;
            gap: 15px;
            align-items: center;
            font-size: 13px;
            font-weight: bold;
        }

        .controls select {
            padding: 4px 24px 4px 8px;
            border: 1px solid #ced4da;
            border-radius: 4px;
            font-size: 13px;
        }

        /* Table Design */
        .table-container {
            background-color: white;
            border-radius: 8px;
            overflow-x: auto;
            border: 1px solid #bbb;
        }

        .custom-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 11px;
            min-width: 900px;
        }

        .custom-table th {
            background-color: var(--gray-header);
            border: 1px solid #aaa;
            padding: 10px 5px;
            text-align: center;
        }

        .custom-table td {
            border: 1px solid var(--gray-border);
            padding: 8px;
            text-align: center;
            vertical-align: middle;
        }

        .mhs-info { text-align: left; padding-left: 15px !important; }
        .mhs-name { font-weight: bold; display: block; color: #1a1a1a; font-size: 12px;}
        .mhs-nim { color: #666; font-size: 10px; }

        /* Icons & Action Elements */
        .check { color: #2b8a3e; font-size: 16px; font-weight: bold; margin-right: 3px; }
        .cross { color: #e03131; font-size: 16px; font-weight: bold; }

        .btn-view {
            background-color: #212529;
            color: white;
            border: none;
            padding: 3px 8px;
            font-size: 9px;
            border-radius: 10px;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 4px;
            text-decoration: none;
            transition: bg 0.2s;
        }
        .btn-view:hover { background-color: #495057; color: white; }

        .action-flex {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 4px;
        }

        .btn-action {
            border: none;
            padding: 4px 8px;
            border-radius: 4px;
            color: white;
            font-size: 10px;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 3px;
        }

        .btn-tolak { background-color: var(--red-main); }
        .btn-sahkan { background-color: var(--green-main); }

        /* Status Pills */
        .status-pill {
            padding: 5px 12px;
            border-radius: 15px;
            font-weight: bold;
            display: inline-block;
            font-size: 10px;
        }
        .pill-green { background-color: #b2f2bb; color: #2b8a3e; }
        .pill-red { background-color: #ffc9c9; color: #e03131; }

        /* Pagination */
        .pagination-container {
            display: flex;
            justify-content: flex-end;
            align-items: center;
            margin-top: 15px;
            gap: 10px;
            font-size: 12px;
            color: #666;
            margin-bottom: 20px;
        }

        .table-scroll-wrapper {
            width: 100%;
            overflow-x: auto;
        }
    </style>

    <div class="mt-6" x-data="verifikasiTable()">
        
        @if(session('success'))
            <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative shadow-sm text-sm">
                {{ session('success') }}
            </div>
        @endif

        <div class="top-banner">
            <div class="info-box">
                <div class="info-icon">i</div>
                Tinjau dan Sahkan Kelengkapan Berkas Yang dikirimkan Mahasiswa Sebagai Syarat Melakukan Sidang KP
            </div>
            <div class="flex gap-4">
                <div class="bg-[#38913B] rounded-[10px] p-3 flex flex-col justify-center items-center w-[90px] shadow-sm text-white">
                    <div class="flex items-center gap-2">
                        <div class="bg-white/20 p-1 rounded-full"><svg class="w-3 h-3" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5 13l4 4L19 7"></path>
                            </svg></div>
                        <span class="text-xl font-bold" x-text="statDisahkan"></span>
                    </div>
                    <span class="text-[11px] font-medium mt-1">Disahkan</span>
                </div>
                <div class="bg-[#FBC610] rounded-[10px] p-3 flex flex-col justify-center items-center w-[90px] shadow-sm text-black">
                    <div class="flex items-center gap-2">
                        <div class="border border-black p-0.5 rounded-sm"><svg class="w-3 h-3" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z">
                                </path>
                            </svg></div>
                        <span class="text-xl font-bold" x-text="statBelum"></span>
                    </div>
                    <span class="text-[11px] font-medium text-center leading-tight mt-1">Belum<br>Diperiksa</span>
                </div>
                <div class="bg-[#EA3323] rounded-[10px] p-3 flex flex-col justify-center items-center w-[90px] shadow-sm text-white">
                    <div class="flex items-center gap-2">
                        <div class="bg-white/20 p-1 rounded-full"><svg class="w-3 h-3" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12"></path>
                            </svg></div>
                        <span class="text-xl font-bold" x-text="statDitolak"></span>
                    </div>
                    <span class="text-[11px] font-medium mt-1">Ditolak</span>
                </div>
            </div>
        </div>

        <div class="filter-bar bg-white p-3 rounded shadow-sm border border-gray-200">
            <div class="search-box flex-1">
                <span class="search-icon">🔍</span>
                <input type="text" x-model="searchQuery" placeholder="Cari nama atau NIM mahasiswa di tabel utama...">
            </div>
            <div class="controls flex gap-3">
                <div class="relative flex items-center" x-data="{ open: false }" @click.outside="open = false">
                    <select 
                        @click="open = !open"
                        @blur="open = false"
                        @change="$el.blur()" 
                        x-model="filterStatus" 
                        style="background-image: none;"
                        class="min-w-[170px] appearance-none border border-gray-300 rounded pl-3 pr-10 py-1.5 text-sm bg-white focus:outline-none focus:ring-1 focus:ring-blue-500 cursor-pointer">
                        <option value="all">Semua Status</option>
                        <option value="verified">Disahkan</option>
                        <option value="pending">Belum Diperiksa</option>
                    </select>
                    <svg :class="open ? 'rotate-0' : 'rotate-90'" class="w-4 h-4 absolute right-3 pointer-events-none text-gray-800 transition-transform duration-300 transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </div>
                
                <div class="relative flex items-center" x-data="{ open: false }" @click.outside="open = false">
                    <select 
                        @click="open = !open"
                        @blur="open = false"
                        @change="$el.blur()" 
                        x-model="filterKondisiMain" 
                        style="background-image: none;"
                        class="min-w-[170px] appearance-none border border-gray-300 rounded pl-3 pr-10 py-1.5 text-sm bg-white focus:outline-none focus:ring-1 focus:ring-blue-500 cursor-pointer">
                        <option value="all">Semua Kondisi</option>
                        <option value="lengkap">Lengkap</option>
                        <option value="tidak_lengkap">Tidak Lengkap</option>
                    </select>
                    <svg :class="open ? 'rotate-0' : 'rotate-90'" class="w-4 h-4 absolute right-3 pointer-events-none text-gray-800 transition-transform duration-300 transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </div>

                <button @click="clearMainFilter()" class="bg-red-500 hover:bg-red-600 text-white px-3 py-1.5 rounded text-sm font-medium transition-colors">Clear Filter</button>
            </div>
        </div>

        @php
            // Fungsi pembantu mapper sama
            $mapper = function ($item) {
                // Evaluasi kelengkapan awal yang wajib (3 pilar utama: Laporan, Log, Persetujuan Dosen)
                $isComplete = $item->file_laporan && $item->file_log_bimbingan && $item->status_verifikasi === 'verified';
                
                return [
                    'id' => $item->id,
                    'name' => $item->mahasiswa?->user?->name ?? 'N/A',
                    'nim' => $item->mahasiswa?->nim ?? 'N/A',
                    'status' => $item->status_koordinator,
                    'is_complete' => $isComplete,
                    'file_laporan' => $item->file_laporan ? asset('storage/'.$item->file_laporan) : null,
                    'file_log_bimbingan' => $item->file_log_bimbingan ? asset('storage/'.$item->file_log_bimbingan) : null,
                    'file_persetujuan' => $item->status_verifikasi === 'verified' ? route('mahasiswa.persetujuan-sidang.cetak', $item->id) : null,
                    'file_supervisor' => $item->file_nilai_supervisor ? asset('storage/'.$item->file_nilai_supervisor) : null,
                    'file_lainnya' => $item->file_berkas_lainnya ? asset('storage/'.$item->file_berkas_lainnya) : null,
                    'link_drive' => $item->link_drive,
                    'link_github' => $item->link_github,
                    'link_deploy' => $item->link_deploy,
                    'feedback' => $item->koordinator_feedback,
                ];
            };

            $mainRows = $pengajuans->map($mapper)->sortBy('nim')->values();
            $rejectedRows = $ditolaks->map($mapper)->sortBy('nim')->values();

            // Mapper untuk tabel status ringkas bagian bawah
            $sudahUpload = $pengajuans->map(function($i) { return ['nim'=>$i->mahasiswa->nim ?? '-', 'name'=>$i->mahasiswa->user->name ?? '-', 'status'=>'Sudah Mengupload Berkas']; })->values();
            $belumUpload = $belumKumpuls->merge($ditolaks)->map(function($i) { return ['nim'=>$i->mahasiswa->nim ?? '-', 'name'=>$i->mahasiswa->user->name ?? '-', 'status'=> 'Belum Mengupload Berkas']; })->values();

            $semuaMahasiswa = \App\Models\Mahasiswa::with('user')->get();
            $allStatusRows = $semuaMahasiswa->map(function($mhs) use ($pengajuans) {
                // Apakah mahasiswa ini ada di pengajuans (sudah kirim & status pending/verified)
                $isSudah = $pengajuans->contains('mahasiswa_id', $mhs->user_id);
                return [
                    'nim'    => $mhs->nim ?? '-',
                    'name'   => $mhs->user->name ?? '-',
                    'status' => $isSudah ? 'Sudah Mengupload Berkas' : 'Belum Mengupload Berkas'
                ];
            })->sortBy('nim')->values();
        @endphp

        <h3 class="text-md font-bold mb-3 text-black">Tabel Utama: Verifikasi Berkas</h3>

        <div class="table-container table-scroll-wrapper">
            <table class="custom-table" style="min-width: 1400px;">
                <thead>
                    <tr>
                        <th rowspan="2" style="width:40px;">No</th>
                        <th rowspan="2" style="width:160px;">Mahasiswa</th>
                        <th colspan="8">Kelengkapan Berkas & Link Pelengkap</th>
                        <th rowspan="2" style="width:80px;">Kondisi</th>
                        <th rowspan="2" style="width:130px;">Aksi & Status</th>
                    </tr>
                    <tr>
                        <th style="min-width: 80px;">Laporan KP <span style="color:red">*</span></th>
                        <th style="min-width: 80px;">Log Bimbingan <span style="color:red">*</span></th>
                        <th style="min-width: 80px;">Surat Perset. Pembimbing <span style="color:red">*</span></th>
                        <th style="min-width: 80px;">Surat Penilaian Supervisior</th>
                        <th style="min-width: 80px;">Berkas Lainnya</th>
                        <th style="min-width: 80px;">Link Drive</th>
                        <th style="min-width: 80px;">Link Github</th>
                        <th style="min-width: 80px;">Link Deploy</th>
                    </tr>
                </thead>
                <tbody>
                    <template x-for="(row, index) in paginatedRows" :key="row.id">
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td x-text="(currentPage - 1) * itemsPerPage + index + 1"></td>
                            <td class="mhs-info">
                                <span class="mhs-name" x-text="row.name"></span>
                                <span class="mhs-nim" x-text="row.nim"></span>
                                <!-- Tampilkan feedback di bawah nama bila status ditolak -->
                                <template x-if="row.status === 'rejected' && row.feedback">
                                    <div class="mt-1 text-[9px] text-red-600 bg-red-50 p-1 rounded border border-red-100 italic" title="Alasan Tolak">
                                        Note: <span x-text="row.feedback"></span>
                                    </div>
                                </template>
                            </td>
                            
                            <!-- Laporan KP -->
                            <td>
                                <template x-if="row.file_laporan">
                                    <div class="action-flex"><span class="check">✓</span> <a :href="row.file_laporan" target="_blank" class="btn-view">👁️ Lihat</a></div>
                                </template>
                                <template x-if="!row.file_laporan"><span class="cross">✕</span></template>
                            </td>

                            <!-- Laporan Bimbingan KP -->
                            <td>
                                <template x-if="row.file_log_bimbingan">
                                    <div class="action-flex"><span class="check">✓</span> <a :href="row.file_log_bimbingan" target="_blank" class="btn-view">👁️ Lihat</a></div>
                                </template>
                                <template x-if="!row.file_log_bimbingan"><span class="cross">✕</span></template>
                            </td>

                            <!-- Surat Persetujuan Pembimbing -->
                            <td>
                                <template x-if="row.file_persetujuan">
                                    <div class="action-flex"><span class="check">✓</span> <a :href="row.file_persetujuan" target="_blank" class="btn-view">👁️ Lihat</a></div>
                                </template>
                                <template x-if="!row.file_persetujuan"><span class="cross">✕</span></template>
                            </td>

                            <!-- Surat Penilaian Supervisior -->
                            <td>
                                <template x-if="row.file_supervisor">
                                    <div class="action-flex"><span class="check">✓</span> <a :href="row.file_supervisor" target="_blank" class="btn-view">👁️ Lihat</a></div>
                                </template>
                                <template x-if="!row.file_supervisor"><span class="cross">✕</span></template>
                            </td>

                            <!-- Berkas Lainnya -->
                            <td>
                                <template x-if="row.file_lainnya">
                                    <div class="action-flex"><span class="check">✓</span> <a :href="row.file_lainnya" target="_blank" class="btn-view">👁️ Lihat</a></div>
                                </template>
                                <template x-if="!row.file_lainnya"><span class="cross">✕</span></template>
                            </td>

                            <!-- Link Drive -->
                            <td>
                                <template x-if="row.link_drive">
                                    <div class="action-flex">
                                        <span class="check">✓</span> 
                                        <a :href="row.link_drive" target="_blank" class="btn-view" style="background-color:#364fc7;">Pergi ➜</a>
                                    </div>
                                </template>
                                <template x-if="!row.link_drive"><span class="cross">✕</span></template>
                            </td>

                            <!-- Link Github -->
                            <td>
                                <template x-if="row.link_github">
                                    <div class="action-flex">
                                        <span class="check">✓</span> 
                                        <a :href="row.link_github" target="_blank" class="btn-view" style="background-color:#364fc7;">Pergi ➜</a>
                                    </div>
                                </template>
                                <template x-if="!row.link_github"><span class="cross">✕</span></template>
                            </td>

                            <!-- Link Deploy -->
                            <td>
                                <template x-if="row.link_deploy">
                                    <div class="action-flex">
                                        <span class="check">✓</span> 
                                        <a :href="row.link_deploy" target="_blank" class="btn-view" style="background-color:#364fc7;">Pergi ➜</a>
                                    </div>
                                </template>
                                <template x-if="!row.link_deploy"><span class="cross">✕</span></template>
                            </td>

                            <!-- Status Condition Lengkap / Tidak -->
                            <td>
                                <template x-if="row.is_complete">
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-green-100 text-green-700 font-bold text-[10px]">
                                        <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span> Lengkap
                                    </span>
                                </template>
                                <template x-if="!row.is_complete">
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-red-100 text-red-700 font-bold text-[10px]">
                                        <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span> Tidak Lengkap
                                    </span>
                                </template>
                            </td>

                            <!-- Aksi & Status Panel -->
                            <td>
                                <!-- Jika Pending -->
                                <template x-if="row.status === 'pending'">
                                    <div class="action-flex">
                                        <button x-on:click="openTolakModal(row.id)" :disabled="isUpdating" :class="isUpdating ? 'opacity-50 cursor-not-allowed' : ''" class="btn-action btn-tolak cursor-pointer">✖ Tolak</button>
                                        <button type="button" x-on:click.prevent="updateRowStatus(row.id, 'verified')" :disabled="isUpdating" :class="isUpdating ? 'opacity-50 cursor-not-allowed' : ''" class="btn-action btn-sahkan cursor-pointer">
                                            <span x-show="!isUpdating">✓ Sahkan</span>
                                            <span x-show="isUpdating">...</span>
                                        </button>
                                    </div>
                                </template>

                                <!-- Jika Disahkan -->
                                <template x-if="row.status === 'verified'">
                                    <div class="status-pill pill-green">Disahkan</div>
                                </template>

                                <!-- Jika Ditolak -->
                                <template x-if="row.status === 'rejected'">
                                    <div class="status-pill pill-red">Ditolak</div>
                                </template>
                            </td>
                        </tr>
                    </template>
                    <!-- Dummy Data Ketika Data Kosong -->
                    <template x-if="filteredRows.length === 0">
                        <tr class="opacity-60 bg-gray-50 bg-stripes pointer-events-none">
                            <td>1</td>
                            <td class="mhs-info">
                                <span class="mhs-name">Budi Santoso (Data Dummy)</span>
                                <span class="mhs-nim">412023001</span>
                            </td>
                            <td><div class="action-flex"><span class="check">✓</span> <span class="btn-view bg-gray-400">👁️ Lihat</span></div></td>
                            <td><div class="action-flex"><span class="check">✓</span> <span class="btn-view bg-gray-400">👁️ Lihat</span></div></td>
                            <td><div class="action-flex"><span class="check">✓</span> <span class="btn-view bg-gray-400">👁️ Lihat</span></div></td>
                            <td><span class="cross">✕</span></td>
                            <td><span class="cross">✕</span></td>
                            <td><span class="cross">✕</span></td>
                            <td><span class="cross">✕</span></td>
                            <td><span class="cross">✕</span></td>
                            <td><i class="text-red-500">Tidak Lengkap</i></td>
                            <td><div class="status-pill bg-gray-300 text-gray-700">Tidak Tersedia</div></td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="pagination-container" x-show="totalPages > 1">
            <span x-text="`Menampilkan halaman ${currentPage} dari ${totalPages}`"></span>
            
            <button @click="prevPage" :disabled="currentPage === 1" class="px-2 py-1 bg-white border border-gray-300 rounded hover:bg-gray-50 disabled:opacity-50">&lt;</button>
            <div class="flex gap-1">
                <template x-for="page in totalPages" :key="page">
                    <div @click="goToPage(page)" 
                         class="w-6 h-6 flex items-center justify-center border border-gray-300 rounded cursor-pointer text-[11px]"
                         :class="currentPage === page ? 'bg-[#364fc7] text-white border-[#364fc7]' : 'bg-white hover:bg-gray-50'">
                        <span x-text="page"></span>
                    </div>
                </template>
            </div>
            <button @click="nextPage" :disabled="currentPage === totalPages" class="px-2 py-1 bg-white border border-gray-300 rounded hover:bg-gray-50 disabled:opacity-50">&gt;</button>
        </div> <!-- End of verifikasiTable Component -->

        @php
            // Pastikan data Blade sudah disiapkan (Bila perlu di fallback jika kosong)
            $rejectedBladeRows = $rejectedRows ?? [];
        @endphp

        <!-- Tabel Riwayat Penolakan (Alpine JS rendering) -->
        <div class="mt-8">
            <hr class="my-8 border-t-2 border-dashed border-gray-300">
            <h3 class="text-md font-bold mb-3 text-black">Tabel Riwayat Penolakan</h3>

            <div class="filter-bar bg-white p-3 rounded shadow-sm border border-gray-200 mb-4">
                <div class="search-box flex-1">
                    <span class="search-icon">🔍</span>
                    <input type="text" x-model="searchRejected" placeholder="Cari nama atau NIM di riwayat penolakan...">
                </div>
                <div class="controls flex gap-3">
                    <div class="relative flex items-center" x-data="{ open: false }" @click.outside="open = false">
                        <select 
                            @click="open = !open"
                            @blur="open = false"
                            @change="$el.blur()" 
                            x-model="filterKondisiRejected" 
                            style="background-image: none;"
                            class="min-w-[170px] appearance-none border border-gray-300 rounded pl-3 pr-10 py-1.5 text-sm bg-white focus:outline-none focus:ring-1 focus:ring-blue-500 cursor-pointer">
                            <option value="all">Semua Kondisi</option>
                            <option value="lengkap">Lengkap</option>
                            <option value="tidak_lengkap">Tidak Lengkap</option>
                        </select>
                        <svg :class="open ? 'rotate-0' : 'rotate-90'" class="w-4 h-4 absolute right-3 pointer-events-none text-gray-800 transition-transform duration-300 transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </div>
                    <button @click="clearRejectedFilter()" class="bg-red-500 hover:bg-red-600 text-white px-3 py-1.5 rounded text-sm font-medium transition-colors">Clear Filter</button>
                </div>
            </div>

            <div class="table-container table-scroll-wrapper">
                <table class="custom-table" style="min-width: 1400px;">
                    <thead>
                        <tr>
                            <th rowspan="2" style="width:40px;">No</th>
                            <th rowspan="2" style="width:160px;">Mahasiswa</th>
                            <th colspan="8">Kelengkapan Berkas & Link Pelengkap</th>
                            <th rowspan="2" style="width:80px;">Kondisi</th>
                            <th rowspan="2" style="width:200px;">Aksi / Status (Catatan Dosen)</th>
                        </tr>
                        <tr>
                            <th style="min-width: 80px;">Laporan KP <span style="color:red">*</span></th>
                            <th style="min-width: 80px;">Log Bimbingan <span style="color:red">*</span></th>
                            <th style="min-width: 80px;">Surat Perset. Pembimbing <span style="color:red">*</span></th>
                            <th style="min-width: 80px;">Surat Penilaian Supervisior</th>
                            <th style="min-width: 80px;">Berkas Lainnya</th>
                            <th style="min-width: 80px;">Link Drive</th>
                            <th style="min-width: 80px;">Link Github</th>
                            <th style="min-width: 80px;">Link Deploy</th>
                        </tr>
                    </thead>
                    <tbody>
                        <template x-for="(row, index) in filteredRejectedRows" :key="row.id">
                            <tr class="hover:bg-red-50 transition-colors">
                                <td x-text="index + 1"></td>
                                <td class="mhs-info">
                                    <span class="mhs-name" x-text="row.name"></span>
                                    <span class="mhs-nim" x-text="row.nim"></span>
                                </td>
                                <!-- Laporan KP -->
                                <td>
                                    <template x-if="row.file_laporan">
                                        <div class="action-flex"><span class="check">✓</span> <a :href="row.file_laporan" target="_blank" class="btn-view">👁️ Lihat</a></div>
                                    </template>
                                    <template x-if="!row.file_laporan"><span class="cross">✕</span></template>
                                </td>
                                <!-- Log Bimbingan -->
                                <td>
                                    <template x-if="row.file_log_bimbingan">
                                        <div class="action-flex"><span class="check">✓</span> <a :href="row.file_log_bimbingan" target="_blank" class="btn-view">👁️ Lihat</a></div>
                                    </template>
                                    <template x-if="!row.file_log_bimbingan"><span class="cross">✕</span></template>
                                </td>
                                <!-- Persetujuan -->
                                <td>
                                    <template x-if="row.file_persetujuan">
                                        <div class="action-flex"><span class="check">✓</span> <a :href="row.file_persetujuan" target="_blank" class="btn-view">👁️ Lihat</a></div>
                                    </template>
                                    <template x-if="!row.file_persetujuan"><span class="cross">✕</span></template>
                                </td>
                                <!-- Supervisor -->
                                <td>
                                    <template x-if="row.file_supervisor">
                                        <div class="action-flex"><span class="check">✓</span> <a :href="row.file_supervisor" target="_blank" class="btn-view">👁️ Lihat</a></div>
                                    </template>
                                    <template x-if="!row.file_supervisor"><span class="cross">✕</span></template>
                                </td>
                                <!-- Lainnya -->
                                <td>
                                    <template x-if="row.file_lainnya">
                                        <div class="action-flex"><span class="check">✓</span> <a :href="row.file_lainnya" target="_blank" class="btn-view">👁️ Lihat</a></div>
                                    </template>
                                    <template x-if="!row.file_lainnya"><span class="cross">✕</span></template>
                                </td>
                                <!-- Link Drive -->
                                <td>
                                    <template x-if="row.link_drive">
                                        <div class="action-flex"><span class="check">✓</span> <a :href="row.link_drive" target="_blank" class="btn-view" style="background-color:#364fc7;">Pergi ➜</a></div>
                                    </template>
                                    <template x-if="!row.link_drive"><span class="cross">✕</span></template>
                                </td>
                                <!-- Link GitHub -->
                                <td>
                                    <template x-if="row.link_github">
                                        <div class="action-flex"><span class="check">✓</span> <a :href="row.link_github" target="_blank" class="btn-view" style="background-color:#364fc7;">Pergi ➜</a></div>
                                    </template>
                                    <template x-if="!row.link_github"><span class="cross">✕</span></template>
                                </td>
                                <!-- Link Deploy -->
                                <td>
                                    <template x-if="row.link_deploy">
                                        <div class="action-flex"><span class="check">✓</span> <a :href="row.link_deploy" target="_blank" class="btn-view" style="background-color:#364fc7;">Pergi ➜</a></div>
                                    </template>
                                    <template x-if="!row.link_deploy"><span class="cross">✕</span></template>
                                </td>
                                <!-- Kondisi -->
                                <td>
                                    <template x-if="row.is_complete">
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-green-100 text-green-700 font-bold text-[10px]">
                                            <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span> Lengkap
                                        </span>
                                    </template>
                                    <template x-if="!row.is_complete">
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-red-100 text-red-700 font-bold text-[10px]">
                                            <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span> Tidak Lengkap
                                        </span>
                                    </template>
                                </td>
                                <!-- Feedback Dosen/Koordinator -->
                                <td>
                                    <div class="text-[10px] text-red-600 font-medium italic text-left pl-2">
                                        " <span x-text="row.feedback || 'Tidak ada catatan'"></span> "
                                    </div>
                                </td>
                            </tr>
                        </template>
                        <template x-if="filteredRejectedRows.length === 0">
                            <tr>
                                <td colspan="11" class="text-gray-400 italic py-6">Tidak ada hasil ditemukan.</td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Tabel Daftar Mahasiswa Sudah & Belum Upload -->
        <div class="mt-8 mb-12">
            <hr class="my-8 border-t-2 border-dashed border-gray-300">
            <h3 class="text-md font-bold mb-3 text-black">Daftar Status Pengunggahan Berkas Mahasiswa</h3>
            
            <div class="filter-bar bg-white p-3 rounded shadow-sm border border-gray-200 mb-4">
                <div class="search-box flex-1">
                    <span class="search-icon">🔍</span>
                    <input type="text" x-model="searchStatus" placeholder="Cari nama atau NIM di daftar status...">
                </div>
                <div class="controls flex gap-3">
                    <div class="relative flex items-center" x-data="{ open: false }" @click.outside="open = false">
                        <select 
                            @click="open = !open"
                            @blur="open = false"
                            @change="$el.blur()" 
                            x-model="filterStatusUpload" 
                            style="background-image: none;"
                            class="min-w-[190px] appearance-none border border-gray-300 rounded pl-3 pr-10 py-1.5 text-sm bg-white focus:outline-none focus:ring-1 focus:ring-blue-500 cursor-pointer">
                            <option value="all">Semua Status Upload</option>
                            <option value="sudah">Sudah Mengupload</option>
                            <option value="belum">Belum Mengupload</option>
                        </select>
                        <svg :class="open ? 'rotate-0' : 'rotate-90'" class="w-4 h-4 absolute right-3 pointer-events-none text-gray-800 transition-transform duration-300 transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </div>
                    <button @click="clearStatusFilter()" class="bg-red-500 hover:bg-red-600 text-white px-3 py-1.5 rounded text-sm font-medium transition-colors">Clear Filter</button>
                </div>
            </div>

            <div class="table-container p-4">
                <table class="w-full text-left border-collapse font-sans text-sm">
                    <thead>
                        <tr class="bg-gray-100 border-b border-gray-300 border-t border-r border-l">
                            <th class="py-2 px-4 font-bold border-r border-gray-300 w-12 text-center">No</th>
                            <th class="py-2 px-4 font-bold border-r border-gray-300">NIM</th>
                            <th class="py-2 px-4 font-bold border-r border-gray-300">Nama Mahasiswa</th>
                            <th class="py-2 px-4 font-bold">Status Berkas</th>
                        </tr>
                    </thead>
                    <tbody>
                        <template x-for="(stat, index) in filteredStatusRows" :key="stat.nim">
                            <tr class="border-b border-r border-l border-gray-200 hover:bg-gray-50">
                                <td class="py-2 px-4 border-r border-gray-200 text-center text-gray-600 font-medium" x-text="index + 1"></td>
                                <td class="py-2 px-4 border-r border-gray-200" x-text="stat.nim"></td>
                                <td class="py-2 px-4 border-r border-gray-200 font-semibold text-gray-700" x-text="stat.name"></td>
                                <td class="py-2 px-4">
                                    <template x-if="stat.status === 'Sudah Mengupload Berkas'">
                                        <span class="text-gray-800 font-semibold bg-gray-200 px-3 py-1 rounded-full text-xs font-sans tracking-wide">✓ Sudah Mengupload</span>
                                    </template>
                                    <template x-if="stat.status === 'Belum Mengupload Berkas'">
                                        <span class="text-gray-500 font-medium px-3 py-1 text-xs italic">- Belum Mengupload</span>
                                    </template>
                                </td>
                            </tr>
                        </template>
                        <template x-if="filteredStatusRows.length === 0">
                            <tr><td colspan="4" class="py-4 text-center text-gray-500 italic">Tidak ada hasil ditemukan.</td></tr>
                        </template>
                    </tbody>
                </table>
            </div>
            </div>
        </div>

        <!-- Bagian Keterangan / Legend -->
        <div class="mt-8 bg-gray-50 border border-gray-200 rounded-lg p-5">
            <h4 class="text-[14px] font-bold text-black mb-4">Keterangan Status :</h4>
            <div class="flex flex-wrap gap-8">
                <div class="flex items-center gap-3">
                    <div class="bg-[#38913B] p-2 rounded-[5px] flex items-center justify-center text-white">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path>
                        </svg>
                    </div>
                    <span class="text-[14px] font-bold text-black">Disetujui / Disahkan</span>
                </div>
                <div class="flex items-center gap-3">
                    <div class="bg-[#FBC610] p-2 rounded-[5px] flex items-center justify-center text-black shadow-sm">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"></path>
                        </svg>
                    </div>
                    <span class="text-[14px] font-bold text-black">Belum Diperiksa</span>
                </div>
                <div class="flex items-center gap-3">
                    <div class="bg-[#EA3323] p-2 rounded-[5px] flex items-center justify-center text-white shadow-sm">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </div>
                    <span class="text-[14px] font-bold text-black">Ditolak</span>
                </div>
            </div>
            
            <div class="mt-6 flex flex-wrap gap-8 items-center pt-4 border-t border-gray-200">
                <div class="flex items-center gap-2">
                    <span class="check text-[18px]">✓</span> 
                    <span class="text-[14px] font-bold text-black">Sudah Upload / Lengkap</span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="cross text-[18px]">✕</span> 
                    <span class="text-[14px] font-bold text-black">Belum Upload / Tidak Lengkap</span>
                </div>
            </div>
        </div>

        <!-- Modal Tolak Alpine -->
        <div x-show="showTolakModal" style="display:none;" class="fixed inset-0 z-[100] flex items-center justify-center bg-black/50 backdrop-blur-sm p-4">
            <div @click.away="closeTolakModal()" class="bg-white w-full max-w-md rounded-[10px] shadow-xl overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 bg-red-50 flex justify-between items-center">
                    <h2 class="text-lg font-bold text-red-700">Tolak Berkas</h2>
                    <button @click="closeTolakModal()" class="text-gray-400 hover:text-red-500 font-bold">X</button>
                </div>
                <form @submit.prevent="updateRowStatus(selectedId, 'rejected', rejectFeedback)" class="p-6">
                    <input type="hidden" name="status_koordinator" value="rejected">
                    <p class="text-sm text-gray-700 mb-4">Berikan alasan penolakan berkas kepada mahasiswa agar bisa segera direvisi (Opsional).</p>
                    <textarea name="feedback" x-model="rejectFeedback" rows="4" class="w-full border border-gray-300 rounded-[5px] p-3 text-sm focus:ring-1 focus:ring-red-500 outline-none resize-none mb-6" placeholder="Misal: File laporan belum ditandatangan supervisor..."></textarea>
                    <div class="flex justify-end gap-3">
                        <button type="button" x-on:click="closeTolakModal()" :disabled="isUpdating" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-800 font-bold rounded cursor-pointer">Batal</button>
                        <button type="submit" :disabled="isUpdating" class="px-4 py-2 bg-[#EA3323] hover:bg-red-700 text-white font-bold rounded flex items-center gap-2 cursor-pointer">
                            <span x-show="!isUpdating">Kembalikan Berkas</span>
                            <span x-show="isUpdating">Memproses...</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div> <!-- END of dashboard layout wrapper -->

    <script>
        function verifikasiTable() {
            return {
                searchQuery: '',
                filterStatus: 'all',
                filterKondisiMain: 'all',

                searchRejected: '',
                filterKondisiRejected: 'all',

                searchStatus: '',
                filterStatusUpload: 'all',

                rawRows: @json($mainRows),
                rejectedRows: @json($rejectedRows),
                statusRows: @json($allStatusRows),
                
                statDisahkan: {{ $statDisahkan ?? 0 }},
                statBelum: {{ $statBelum ?? 0 }},
                statDitolak: {{ $statDitolak ?? 0 }},

                showTolakModal: false,
                selectedId: null,
                rejectFeedback: '',
                isUpdating: false,

                currentPage: 1,
                itemsPerPage: 9,

                get filteredRows() {
                    let filtered = this.rawRows;
                    if (this.filterStatus !== 'all') {
                        filtered = filtered.filter(r => r.status === this.filterStatus);
                    }
                    if (this.filterKondisiMain !== 'all') {
                        filtered = filtered.filter(r => this.filterKondisiMain === 'lengkap' ? r.is_complete : !r.is_complete);
                    }
                    if (this.searchQuery.trim() !== '') {
                        const term = this.searchQuery.toLowerCase();
                        filtered = filtered.filter(r => 
                            r.name.toLowerCase().includes(term) || 
                            r.nim.toLowerCase().includes(term)
                        );
                    }
                    return filtered;
                },

                get filteredRejectedRows() {
                    let filtered = this.rejectedRows;
                    if (this.filterKondisiRejected !== 'all') {
                        filtered = filtered.filter(r => this.filterKondisiRejected === 'lengkap' ? r.is_complete : !r.is_complete);
                    }
                    if (this.searchRejected.trim() !== '') {
                        const term = this.searchRejected.toLowerCase();
                        filtered = filtered.filter(r => 
                            r.name.toLowerCase().includes(term) || 
                            r.nim.toLowerCase().includes(term)
                        );
                    }
                    return filtered;
                },

                get filteredStatusRows() {
                    let filtered = this.statusRows;
                    if (this.filterStatusUpload !== 'all') {
                        const targetStr = this.filterStatusUpload === 'sudah' ? 'Sudah Mengupload Berkas' : 'Belum Mengupload Berkas';
                        filtered = filtered.filter(r => r.status === targetStr);
                    }
                    if (this.searchStatus.trim() !== '') {
                        const term = this.searchStatus.toLowerCase();
                        filtered = filtered.filter(r => 
                            r.name.toLowerCase().includes(term) || 
                            r.nim.toLowerCase().includes(term)
                        );
                    }
                    return filtered;
                },

                clearMainFilter() {
                    this.searchQuery = '';
                    this.filterStatus = 'all';
                    this.filterKondisiMain = 'all';
                    this.currentPage = 1;
                },

                get totalPages() {
                    return Math.ceil(this.filteredRows.length / this.itemsPerPage) || 1;
                },

                get paginatedRows() {
                    const start = (this.currentPage - 1) * this.itemsPerPage;
                    return this.filteredRows.slice(start, start + this.itemsPerPage);
                },

                init() {
                    this.$watch('searchQuery', () => { this.currentPage = 1; });
                    this.$watch('filterStatus', () => { this.currentPage = 1; });
                    this.$watch('filterKondisiMain', () => { this.currentPage = 1; });
                },

                nextPage() {
                    if (this.currentPage < this.totalPages) this.currentPage++;
                },
                prevPage() {
                    if (this.currentPage > 1) this.currentPage--;
                },
                goToPage(page) {
                    this.currentPage = page;
                },

                openTolakModal(id) {
                    this.selectedId = id;
                    this.showTolakModal = true;
                    this.rejectFeedback = '';
                },
                closeTolakModal() {
                    this.showTolakModal = false;
                    this.selectedId = null;
                },

                async updateRowStatus(id, newStatus, feedback = '') {
                    if (this.isUpdating) return;
                    this.isUpdating = true;

                    try {
                        const response = await fetch(`{{ url('koordinator/verifikasi') }}/${id}/status`, {
                            method: 'PUT',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'X-Requested-With': 'XMLHttpRequest'
                            },
                            body: JSON.stringify({
                                status_koordinator: newStatus,
                                feedback: feedback
                            })
                        });

                        const result = await response.json();

                        if (result.success) {
                            let rowToMove = null;
                            const mainIndex = this.rawRows.findIndex(r => r.id == id);
                            
                            if (mainIndex !== -1) {
                                if (newStatus === 'verified') {
                                    this.rawRows[mainIndex].status = 'verified';
                                } else {
                                    rowToMove = this.rawRows.splice(mainIndex, 1)[0];
                                    rowToMove.status = 'rejected';
                                    rowToMove.feedback = feedback;
                                    this.rejectedRows.push(rowToMove);
                                    this.rejectedRows.sort((a,b) => a.nim.localeCompare(b.nim));
                                }
                            }

                            if (result.stats) {
                                this.statDisahkan = result.stats.disahkan;
                                this.statBelum = result.stats.belum;
                                this.statDitolak = result.stats.ditolak;
                            }

                            if (newStatus === 'rejected') this.closeTolakModal();
                        }
                    } catch (error) {
                        alert('Gagal memperbarui status. Silakan coba lagi.');
                    } finally {
                        this.isUpdating = false;
                    }
                }
            };
        }
    </script>
</x-dashboard-layout>
