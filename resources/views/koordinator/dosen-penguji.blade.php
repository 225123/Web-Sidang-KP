<x-dashboard-layout header="Manajemen Dosen Penguji" userName="{{ auth()->user()->name }}" roleName="KOORDINATOR KP">
    <x-slot:sidebar>
        @include('koordinator.components.sidebar', ['active' => 'dosen-penguji'])
    </x-slot>

    

    <style>
        .filter-bar { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; flex-wrap: wrap; gap: 10px; }
        .search-box { position: relative; width: 300px; max-width: 100%; }
        .search-box input { width: 100%; padding: 8px 10px 8px 35px; border: 1px solid #ced4da; border-radius: 6px; font-size: 13px; }
        .search-icon { position: absolute; left: 10px; top: 50%; transform: translateY(-50%); color: #999; }
        .sentence-case { text-transform: lowercase; }
        .sentence-case::first-letter { text-transform: uppercase; }
        [x-cloak] { display: none !important; }
    </style>

    <script>
        window.pengujiManager = function() {
            return {
                dosenList: @json($dosenList),
                sidangList: @json($terjadwal),
                tungguList: @json($daftarTunggu),
                terjadwal: [],
                
                searchQuery: '',
                searchTunggu: '',
                searchBeban: '',
                filterPenguji: null,
                currentPage: 1,
                itemsPerPage: 10,
                selectedIds: [],
                isSelectingMode: false,
                isLoadingAuto: false,
                warnings: @json($warnings),
                totalMahasiswa: {{ $totalMahasiswa }},
                alert: { show: false, type: 'success', title: '', message: '' },
                confirmDialog: { show: false, title: '', message: '', type: 'danger', confirmText: 'Iya, Lanjutkan', callback: null },
                
                form: {
                    mode: 'create', // create | edit
                    isLoading: false,
                    id: '',
                    name: '',
                    nim: '',
                    displayName: '--- Pilih dari Daftar Tunggu ---',
                    pembimbingId: null,
                    pembimbingName: '',
                    tanggal: '',
                    mulai: '',
                    selesai: '',
                    ruang: '',
                    p1: null,
                    p2: null,
                },

                init() {
                    this.terjadwal = this.sidangList.map(s => ({
                        id: s.id,
                        name: s.name,
                        nim: s.nim,
                        judul: s.judul,
                        tanggal: s.tanggal,
                        mulai: s.mulai,
                        selesai: s.selesai,
                        penguji_1_id: s.penguji_1_id,
                        penguji_2_id: s.penguji_2_id,
                        status_jadwal: s.status_jadwal,
                        pembimbing_id: s.pembimbing_id,
                        pembimbing_name: s.pembimbing_name,
                        ruang: s.ruang
                    }));
                },

                triggerConfirm(options) {
                    this.confirmDialog = {
                        show: true,
                        title: options.title || 'Konfirmasi Aksi',
                        message: options.message || 'Apakah Anda yakin ingin melanjutkan?',
                        type: options.type || 'danger',
                        confirmText: options.confirmText || 'Iya, Lanjutkan',
                        callback: options.callback || null
                    };
                },

                executeConfirm() {
                    if (this.confirmDialog.callback) {
                        this.confirmDialog.callback();
                    }
                    this.confirmDialog.show = false;
                },

                get filteredDaftarTunggu() {
                    const q = this.searchTunggu.toLowerCase();
                    return this.tungguList.filter(m => m.name.toLowerCase().includes(q) || m.nim.toLowerCase().includes(q));
                },

                get filteredBebanDosen() {
                    const q = this.searchBeban.toLowerCase();
                    return this.dosenList.filter(d => d.nama.toLowerCase().includes(q));
                },

                get filteredTerjadwal() {
                    let filtered = this.terjadwal;
                    if (this.searchQuery) {
                        const q = this.searchQuery.toLowerCase();
                        filtered = filtered.filter(m => m.name.toLowerCase().includes(q) || m.nim.toLowerCase().includes(q) || m.judul.toLowerCase().includes(q));
                    }
                    if (this.filterPenguji) {
                        filtered = filtered.filter(m => m.penguji_1_id == this.filterPenguji || m.penguji_2_id == this.filterPenguji);
                    }
                    return filtered;
                },

                get paginatedTerjadwal() {
                    const start = (this.currentPage - 1) * this.itemsPerPage;
                    return this.filteredTerjadwal.slice(start, start + this.itemsPerPage);
                },

                get totalPages() {
                   return Math.ceil(this.filteredTerjadwal.length / this.itemsPerPage) || 1;
                },

                getDosenName(id) {
                    if (!id) return '-';
                    let d = this.dosenList.find(x => x.id == id);
                    return d ? d.nama : '-';
                },

                isPembimbing(dosenId) {
                    return this.form.pembimbingId == dosenId;
                },

                hasWarning(mhs) {
                    return this.warnings.some(w => w.includes(mhs.name));
                },

                formatDate(dateString) {
                    if (!dateString) return '-';
                    const parts = dateString.split('-');
                    const dateObj = new Date(parts[0], parts[1] - 1, parts[2]);
                    const formatter = new Intl.DateTimeFormat('id-ID', { year: 'numeric', month: 'short', day: '2-digit' });
                    return formatter.format(dateObj);
                },

                showAlert(type, title, message) {
                    this.alert = { show: true, type, title, message };
                    setTimeout(() => { this.alert.show = false; }, 5000);
                },

                selectStudent(mhs, isEdit = false) {
                    this.form.mode = isEdit ? 'edit' : 'create';
                    this.form.id = mhs.id;
                    this.form.name = mhs.name;
                    this.form.nim = mhs.nim;
                    this.form.displayName = mhs.nim + ' - ' + mhs.name;
                    this.form.pembimbingId = mhs.pembimbing_id;
                    this.form.pembimbingName = mhs.pembimbing_name;
                    this.form.tanggal = mhs.tanggal || '';
                    this.form.mulai = mhs.mulai || '';
                    this.form.selesai = mhs.selesai || '';
                    this.form.ruang = mhs.ruang || '';
                    this.form.p1 = mhs.penguji_1_id || null;
                    this.form.p2 = mhs.penguji_2_id || null;
                    
                    document.getElementById('form-container')?.scrollIntoView({ behavior: 'smooth', block: 'center' });
                },

                resetForm() {
                    this.form.mode = 'create';
                    this.form.id = '';
                    this.form.name = '';
                    this.form.nim = '';
                    this.form.displayName = '--- Pilih dari Daftar Tunggu ---';
                    this.form.pembimbingId = null;
                    this.form.pembimbingName = '';
                    this.form.tanggal = '';
                    this.form.mulai = '';
                    this.form.selesai = '';
                    this.form.ruang = '';
                    this.form.p1 = null;
                    this.form.p2 = null;
                    this.selectedIds = [];
                },

                toggleSelectingMode() {
                    this.isSelectingMode = !this.isSelectingMode;
                    this.selectedIds = [];
                },

                toggleSelect(id) {
                    const index = this.selectedIds.indexOf(id);
                    if (index > -1) {
                        this.selectedIds.splice(index, 1);
                    } else {
                        this.selectedIds.push(id);
                    }
                },

                get hasOnlySubmitted() {
                    return this.terjadwal.some(t => t.status_jadwal === 'submitted');
                },

                get hasDraftChanges() {
                    return this.terjadwal.some(t => t.status_jadwal === 'draft');
                },

                clearFilters() {
                    this.searchQuery = '';
                    this.filterPenguji = null;
                    this.currentPage = 1;
                },

                async autoPlot() {
                    this.triggerConfirm({
                        title: 'Otomatiskan Penugasan',
                        message: 'Sistem akan membagikan penguji secara adil ke seluruh mahasiswa di daftar tunggu. Lanjutkan?',
                        type: 'info',
                        confirmText: 'Lanjutkan',
                        callback: async () => {
                            this.isLoadingAuto = true;
                            try {
                                const res = await fetch("{{ route('koordinator.dosen-penguji.auto') }}", {
                                    method: "POST", headers: { "X-CSRF-TOKEN": "{{ csrf_token() }}", "Accept": "application/json" }
                                });
                                const result = await res.json();
                                if (result.success) {
                                    this.showAlert('success', 'Berhasil', result.message);
                                    setTimeout(() => location.reload(), 1500);
                                } else {
                                    this.showAlert('error', 'Gagal', result.message);
                                }
                            } catch (e) { this.showAlert('error', 'Error', 'Terjadi kesalahan sistem.'); }
                            finally { this.isLoadingAuto = false; }
                        }
                    });
                },

                async submitForm() {
                    if (!this.form.p1 || !this.form.p2) return;
                    this.form.isLoading = true;
                    try {
                        const response = await fetch("{{ route('koordinator.dosen-penguji.store') }}", {
                            method: "POST",
                            headers: { "Content-Type": "application/json", "X-CSRF-TOKEN": "{{ csrf_token() }}" },
                            body: JSON.stringify({ id: this.form.id, penguji_1_id: this.form.p1, penguji_2_id: this.form.p2 })
                        });
                        const result = await response.json();
                        if (result.success) {
                            this.showAlert('success', 'Berhasil', result.message);
                            setTimeout(() => location.reload(), 1000);
                        } else { this.showAlert('error', 'Gagal', result.message); }
                    } catch (e) { this.showAlert('error', 'Error', 'Gagal memproses data.'); }
                    finally { this.form.isLoading = false; }
                },

                async cancelAssignment(id) {
                     this.triggerConfirm({
                        title: 'Batalkan Penugasan',
                        message: 'Apakah Anda yakin ingin membatalkan penugasan penguji untuk mahasiswa ini? Mahasiswa akan kembali ke daftar tunggu.',
                        type: 'danger',
                        confirmText: 'Iya, Batalkan',
                        callback: async () => {
                            try {
                                const res = await fetch(`{{ url('koordinator/dosen-penguji') }}/${id}/cancel`, {
                                    method: "DELETE", headers: { "X-CSRF-TOKEN": "{{ csrf_token() }}" }
                                });
                                const data = await res.json();
                                if(data.success) location.reload();
                             } catch(e) { this.showAlert('error', 'Error', 'Gagal membatalkan penugasan.'); }
                        }
                     });
                },

                async bulkDestroy() {
                    this.triggerConfirm({
                        title: 'Hapus Massal',
                        message: `Apakah Anda yakin ingin membatalkan penugasan penguji untuk ${this.selectedIds.length} mahasiswa terpilih?`,
                        type: 'danger',
                        confirmText: 'Iya, Hapus Semua',
                        callback: async () => {
                            try {
                                const res = await fetch("{{ route('koordinator.dosen-penguji.bulk-destroy') }}", {
                                    method: "POST",
                                    headers: { "Content-Type": "application/json", "X-CSRF-TOKEN": "{{ csrf_token() }}", "Accept": "application/json" },
                                    body: JSON.stringify({ ids: this.selectedIds })
                                });
                                const data = await res.json();
                                if(data.success) location.reload();
                            } catch (e) { this.showAlert('error', 'Error', 'Terjadi kesalahan sistem.'); }
                        }
                    });
                },

                selectAll() {
                    if (this.selectedIds.length === this.filteredTerjadwal.length && this.filteredTerjadwal.length > 0) {
                        this.selectedIds = [];
                    } else {
                        this.selectedIds = this.filteredTerjadwal.map(r => r.id);
                    }
                }
            };
        };
    </script>

    <div class="w-full flex-1 pb-10" x-data="pengujiManager()">
        <style>
            .filter-bar { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; flex-wrap: wrap; gap: 10px; }
            .search-box { position: relative; width: 300px; max-width: 100%; }
            .search-box input { width: 100%; padding: 8px 10px 8px 35px; border: 1px solid #ced4da; border-radius: 6px; font-size: 13px; }
            .search-icon { position: absolute; left: 10px; top: 50%; transform: translateY(-50%); color: #999; }
            .custom-scrollbar::-webkit-scrollbar { width: 6px; }
            .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
            .custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
            [x-cloak] { display: none !important; }
            .box-search { padding: 6px 10px 6px 30px; font-size: 11px; border-radius: 4px; border: 1px solid rgba(0,0,0,0.1); width: 100%; margin-bottom: 10px; outline: none; transition: all 0.2s; }
            .box-search:focus { border-color: #4285F4; box-shadow: 0 0 0 2px rgba(66, 133, 244, 0.1); }
            .checkbox-custom { width: 16px; height: 16px; cursor: pointer; accent-color: #4285F4; }
        </style>

        <!-- Assignment Info Header -->

        <!-- Info Header -->
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6 mb-8">
            <div class="flex-1 bg-[#E6F0FA] border border-[#D0E3F5] rounded-[10px] p-4 lg:p-5 flex items-start gap-4 shadow-sm w-full">
                <div class="w-6 h-6 rounded-full bg-[#4285F4] text-white flex items-center justify-center font-bold flex-shrink-0 mt-0.5">i</div>
                <div class="flex-1">
                    <p class="text-[14px] text-[#1A1A1A] font-medium leading-relaxed m-0 mt-0.5">
                        Penentuan Dosen Penguji Sidang KP.<br>
                        Pilih mahasiswa dari Daftar Tunggu, atau gunakan Auto Plot untuk pembagian beban menguji yang adil.
                    </p>
                </div>
            </div>
            <div class="flex gap-3">
                <button @click="autoPlot()" :disabled="isLoadingAuto" class="bg-[#4285F4] hover:bg-blue-700 text-white px-6 py-3 rounded-[5px] text-[13px] font-bold flex items-center gap-2 shadow-md transition-all whitespace-nowrap disabled:opacity-50">
                    <svg x-show="!isLoadingAuto" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" /></svg>
                    <div x-show="isLoadingAuto" class="animate-spin rounded-full h-4 w-4 border-b-2 border-white"></div>
                    <span x-text="isLoadingAuto ? 'Memproses...' : 'Auto'"></span>
                </button>
            </div>
        </div>

        <!-- Alert Box -->
        <div x-cloak x-show="alert.show" class="mb-6">
            <div class="border rounded-[5px] px-4 py-3 relative shadow-sm w-full flex items-start gap-3 animate-bounce"
                :class="alert.type === 'success' ? 'bg-green-100 border-green-400 text-green-700' : 'bg-red-100 border-red-400 text-red-700'">
                <div class="flex-1">
                    <span class="block font-bold text-[13px]" x-text="alert.title"></span>
                    <span class="block text-[12px] mt-0.5" x-text="alert.message"></span>
                </div>
                <button @click="alert.show = false" type="button" class="text-gray-500 hover:text-gray-700"><svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg></button>
            </div>
        </div>

        <!-- Main Work Area -->
        <div class="flex flex-col lg:flex-row gap-8 mb-12">
            <!-- Left: List Area -->
            <div class="w-full lg:w-[40%] flex flex-col gap-6">
                <!-- Daftar Tunggu Box -->
                <div class="bg-[#E6E6E6] rounded-[5px] p-5 flex flex-col h-auto border border-[#CAC0C0] shadow-sm">
                    <div class="flex justify-between items-center border-b border-gray-300 pb-2 mb-3">
                        <h3 class="font-bold text-[12px] text-black tracking-tight">Daftar Tunggu Penguji</h3>
                        <div class="font-bold text-[12px] text-black">
                            <span x-text="filteredDaftarTunggu.length"></span> / <span x-text="totalMahasiswa"></span>
                        </div>
                    </div>
                    <!-- Box Search -->
                    <div class="relative">
                        <span class="absolute left-3 top-2 text-gray-400"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg></span>
                        <input type="text" x-model="searchTunggu" placeholder="Cari NIM atau Nama..." class="box-search">
                    </div>
                    <div class="text-[12px] font-medium text-black pr-2 max-h-[300px] overflow-y-auto custom-scrollbar">
                        <template x-for="(mhs, index) in filteredDaftarTunggu" :key="mhs.id">
                            <div @click="selectStudent(mhs)" class="flex items-center justify-between border-b border-gray-300 py-3 hover:bg-gray-200 cursor-pointer transition-colors group px-2 rounded">
                                <div class="flex items-center gap-3 truncate">
                                    <span class="font-bold text-gray-400 w-5 flex-shrink-0" x-text="(index+1) + '.'"></span>
                                    <div class="truncate">
                                        <div class="font-normal text-black sentence-case" x-text="mhs.nim"></div>
                                        <div class="font-normal text-gray-700 truncate sentence-case" x-text="mhs.name"></div>
                                    </div>
                                </div>
                                <div class="flex-shrink-0 ml-4 group-hover:translate-x-1 transition-transform">
                                    <svg class="w-5 h-5 text-black" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                                </div>
                            </div>
                        </template>
                        <template x-if="filteredDaftarTunggu.length === 0">
                            <div class="text-center py-10 text-gray-500 italic font-medium">Data Tidak Ditemukan</div>
                        </template>
                    </div>
                </div>

                <!-- Beban Dosen Box -->
                <div class="bg-[#E6E6E6] rounded-[5px] p-5 flex flex-col h-auto border border-[#CAC0C0] shadow-sm">
                    <div class="flex justify-between items-center border-b border-gray-300 pb-2 mb-3">
                        <h3 class="font-bold text-[12px] text-black tracking-tight">Beban Menguji Dosen</h3>
                    </div>
                    <!-- Box Search -->
                    <div class="relative">
                        <span class="absolute left-3 top-2 text-gray-400"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg></span>
                        <input type="text" x-model="searchBeban" placeholder="Cari Nama Dosen..." class="box-search">
                    </div>
                    <div class="text-[12px] font-medium text-black pr-2 max-h-[250px] overflow-y-auto custom-scrollbar">
                        <template x-for="(dosen, index) in filteredBebanDosen" :key="dosen.id">
                            <div class="flex items-center justify-between border-b border-gray-300 py-3 px-2 rounded">
                                <div class="flex items-center gap-3 truncate">
                                    <span class="font-bold text-gray-400 w-5 flex-shrink-0" x-text="(index+1) + '.'"></span>
                                    <span class="font-bold text-black sentence-case truncate" x-text="dosen.nama"></span>
                                </div>
                                <div class="font-bold text-[#4285F4] whitespace-nowrap ml-4 bg-blue-100 px-2 py-1 rounded shadow-sm">
                                    <span x-text="dosen.beban"></span> MHS
                                </div>
                            </div>
                        </template>
                        <template x-if="filteredBebanDosen.length === 0">
                            <div class="text-center py-10 text-gray-500 italic font-medium">Dosen Tidak Ditemukan</div>
                        </template>
                    </div>
                </div>
            </div>

            <!-- Right: Form Area -->
            <div id="form-container" class="w-full lg:w-[60%]">
                <div class="bg-white rounded-[5px] p-6 flex flex-col h-full border border-[#CAC0C0] shadow-sm relative">
                    <div x-show="form.isLoading" class="absolute inset-0 bg-white/60 backdrop-blur-[2px] z-20 flex items-center justify-center">
                        <div class="animate-spin rounded-full h-10 w-10 border-b-2 border-blue-600"></div>
                    </div>

                    <h3 class="font-bold text-[12px] text-black mb-6 border-b border-gray-100 pb-3 flex items-center gap-2 tracking-tight">
                        <svg class="w-5 h-5 text-gray-800" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                        <span x-text="form.mode === 'edit' ? 'Ubah Penugasan Penguji' : 'Penugasan Penguji'"></span>
                    </h3>

                    <form @submit.prevent="submitForm" class="flex flex-col gap-6 flex-1">
                        <!-- Mahasiswa Info -->
                        <div class="grid grid-cols-1 md:grid-cols-[140px_1fr] gap-4 items-center text-[13px]">
                            <label class="font-bold text-black tracking-wide text-[12px]">Mahasiswa</label>
                            <div class="bg-gray-100 border border-[#CAC0C0] rounded-[5px] px-4 py-2.5 font-bold text-black shadow-inner flex justify-between items-center">
                                <span x-text="form.displayName"></span>
                                <template x-if="form.pembimbingName">
                                    <span class="text-[11px] text-blue-600 font-bold" x-text="'(Pembimbing: ' + form.pembimbingName + ')'"></span>
                                </template>
                            </div>
                        </div>

                        <!-- Schedule Row (LOCKED - Read Only) -->
                        <div class="grid grid-cols-1 md:grid-cols-[140px_1fr] gap-4 items-center text-[13px]">
                            <label class="font-bold text-black tracking-wide text-[12px]">Tanggal Sidang</label>
                            <div class="bg-gray-100 border border-[#CAC0C0] rounded-[5px] px-4 py-2 font-bold text-black shadow-inner min-h-[38px] flex items-center" x-text="formatDate(form.tanggal) || '-'"></div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-[140px_1fr] gap-4 items-center text-[13px]">
                            <label class="font-bold text-black tracking-wide text-[12px]">Waktu Sidang</label>
                            <div class="flex items-center gap-3">
                                <div class="flex-1 bg-gray-100 border border-[#CAC0C0] rounded-[5px] px-4 py-2 font-bold text-black shadow-inner min-h-[38px] flex items-center" x-text="form.mulai ? form.mulai.substring(0,5) : '-'"></div>
                                <span class="font-bold">-</span>
                                <div class="flex-1 bg-gray-100 border border-[#CAC0C0] rounded-[5px] px-4 py-2 font-bold text-black shadow-inner min-h-[38px] flex items-center" x-text="form.selesai ? form.selesai.substring(0,5) : '-'"></div>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-[140px_1fr] gap-4 items-center text-[13px]">
                            <label class="font-bold text-black tracking-wide text-[12px]">Ruangan</label>
                            <div class="bg-gray-100 border border-[#CAC0C0] rounded-[5px] px-4 py-2 font-bold text-black shadow-inner min-h-[38px] flex items-center uppercase" x-text="form.ruang || '-'"></div>
                        </div>

                        <hr class="border-gray-200">

                        <!-- Penguji 1 Dropdown -->
                        <div class="grid grid-cols-1 md:grid-cols-[140px_1fr] gap-4 items-center text-[13px]">
                            <label class="font-bold text-black tracking-wide text-[12px]">Penguji 1</label>
                            <div class="relative" x-data="{ open: false, search: '' }" @click.outside="open = false">
                                <button type="button" @click="open = !open && form.id" 
                                    class="w-full h-[40px] flex items-center justify-between px-4 border border-[#CAC0C0] rounded-[5px] bg-white transition-all hover:border-[#4CC098] shadow-sm disabled:bg-gray-100 disabled:cursor-not-allowed"
                                    :class="form.p1 ? 'text-black font-bold' : 'text-gray-400 font-medium'" :disabled="!form.id">
                                    <span class="truncate pr-2" x-text="getDosenName(form.p1) || '--- Pilih Dosen Penguji 1 ---'"></span>
                                    <svg class="w-4 h-4 text-gray-500 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                </button>
                                <div x-cloak x-show="open" class="absolute z-50 mt-1 w-full bg-white border border-[#CAC0C0] rounded-[5px] shadow-xl overflow-hidden min-w-[280px]">
                                    <div class="p-2 border-b border-gray-100 bg-gray-50">
                                        <input type="text" x-model="search" @click.stop class="w-full px-3 py-2 text-[12px] border border-gray-200 rounded-[5px] outline-none focus:border-[#4CC098]" placeholder="Cari Nama Dosen...">
                                    </div>
                                    <ul class="max-h-[220px] overflow-y-auto custom-scrollbar bg-white">
                                        <li @click="form.p1 = null; open = false" class="px-4 py-2 text-[12px] text-red-600 font-bold hover:bg-red-50 cursor-pointer border-b border-gray-100">--- Batalkan Pilihan ---</li>
                                        <template x-for="d in dosenList" :key="d.id">
                                            <li x-show="d.nama.toLowerCase().includes(search.toLowerCase())"
                                                @click="!isPembimbing(d.id) && form.p2 != d.id && (form.p1 = d.id, open = false)"
                                                class="px-4 py-2.5 text-[12px] flex justify-between items-center transition-colors border-b border-gray-100/50"
                                                :class="[
                                                    isPembimbing(d.id) ? 'bg-red-50 text-red-500 cursor-not-allowed opacity-80' : (form.p2 == d.id ? 'bg-gray-100 text-gray-400 cursor-not-allowed' : 'hover:bg-blue-50 cursor-pointer text-black font-bold'),
                                                    form.p1 == d.id ? 'bg-[#E6F0FA] font-bold' : ''
                                                ]">
                                                <div class="flex items-center gap-2">
                                                    <span x-text="d.nama" class="font-bold"></span>
                                                    <template x-if="isPembimbing(d.id)">
                                                        <span class="text-[9px] bg-red-100 px-1 rounded uppercase font-bold">PEMBIMBING</span>
                                                    </template>
                                                </div>
                                                <span class="font-bold text-[#4285F4]" x-text="d.beban"></span>
                                            </li>
                                        </template>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <!-- Penguji 2 Dropdown -->
                        <div class="grid grid-cols-1 md:grid-cols-[140px_1fr] gap-4 items-center text-[13px]">
                            <label class="font-bold text-black tracking-wide text-[12px]">Penguji 2</label>
                            <div class="relative" x-data="{ open: false, search: '' }" @click.outside="open = false">
                                <button type="button" @click="open = !open && form.id" 
                                    class="w-full h-[40px] flex items-center justify-between px-4 border border-[#CAC0C0] rounded-[5px] bg-white transition-all hover:border-[#4CC098] shadow-sm disabled:bg-gray-100 disabled:cursor-not-allowed"
                                    :class="form.p2 ? 'text-black font-bold' : 'text-gray-400 font-medium'" :disabled="!form.id">
                                    <span class="truncate pr-2" x-text="getDosenName(form.p2) || '--- Pilih Dosen Penguji 2 ---'"></span>
                                    <svg class="w-4 h-4 text-gray-500 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                </button>
                                <div x-cloak x-show="open" class="absolute z-50 mt-1 w-full bg-white border border-[#CAC0C0] rounded-[5px] shadow-xl overflow-hidden min-w-[280px]">
                                    <div class="p-2 border-b border-gray-100 bg-gray-50">
                                        <input type="text" x-model="search" @click.stop class="w-full px-3 py-2 text-[12px] border border-gray-200 rounded-[5px] outline-none focus:border-[#4CC098]" placeholder="Cari Nama Dosen...">
                                    </div>
                                    <ul class="max-h-[220px] overflow-y-auto custom-scrollbar bg-white">
                                        <li @click="form.p2 = null; open = false" class="px-4 py-2 text-[12px] text-red-600 font-bold hover:bg-red-50 cursor-pointer border-b border-gray-100">--- Batalkan Pilihan ---</li>
                                        <template x-for="d in dosenList" :key="d.id">
                                            <li x-show="d.nama.toLowerCase().includes(search.toLowerCase())"
                                                @click="!isPembimbing(d.id) && form.p1 != d.id && (form.p2 = d.id, open = false)"
                                                class="px-4 py-2.5 text-[12px] flex justify-between items-center transition-colors border-b border-gray-100/50"
                                                :class="[
                                                    isPembimbing(d.id) ? 'bg-red-50 text-red-500 cursor-not-allowed opacity-80' : (form.p1 == d.id ? 'bg-gray-100 text-gray-400 cursor-not-allowed' : 'hover:bg-blue-50 cursor-pointer text-black font-bold'),
                                                    form.p2 == d.id ? 'bg-[#E6F0FA] font-bold' : ''
                                                ]">
                                                <div class="flex items-center gap-2">
                                                    <span x-text="d.nama" class="font-bold"></span>
                                                    <template x-if="isPembimbing(d.id)">
                                                        <span class="text-[9px] bg-red-100 px-1 rounded uppercase font-bold">PEMBIMBING</span>
                                                    </template>
                                                </div>
                                                <span class="font-bold text-[#4285F4]" x-text="d.beban"></span>
                                            </li>
                                        </template>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <!-- Form Actions -->
                        <div class="flex justify-center gap-4 mt-auto pt-6 border-t border-gray-100">
                            <button type="button" @click="resetForm()" class="bg-gray-100 border border-[#CAC0C0] hover:bg-gray-200 text-black px-10 py-2 rounded-[25px] text-[13px] font-bold flex items-center justify-center gap-2 transition-all shadow-sm">
                                Batal
                            </button>
                            <button type="submit" :disabled="!form.id || !form.p1 || !form.p2" class="bg-[#34A853] hover:bg-green-700 text-white px-10 py-2 rounded-[25px] text-[13px] font-bold flex items-center justify-center gap-2 transition-all shadow-md disabled:opacity-50 disabled:cursor-not-allowed">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                                Tambahkan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-[15px] border border-gray-200 shadow-sm overflow-hidden p-6 mb-8">
            <div class="mb-6 flex flex-col sm:flex-row justify-between items-start gap-4">
                <div>
                    <h3 class="text-[18px] font-bold text-black tracking-tight">Daftar Mahasiswa Terjadwal</h3>
                    <p class="text-[12px] text-black/60 font-medium mt-1">Daftar mahasiswa yang telah mendapatkan plot dosen penguji.</p>
                </div>
                <div class="flex items-center gap-3 shrink-0">
                    <form action="{{ route('koordinator.dosen-penguji.submit') }}" method="POST">
                        @csrf
                        <button type="submit"
                            class="text-black font-bold rounded-[5px] px-6 py-2 text-[12px] flex items-center gap-2 shadow-sm hover:shadow transition-colors"
                            :class="hasDraftChanges ? 'bg-[#4285F4] hover:bg-blue-600 text-white' : (hasOnlySubmitted ? 'bg-[#FDE293] hover:bg-yellow-400 text-[#A67C00]' : 'bg-gray-300 cursor-not-allowed text-gray-500')"
                            :disabled="terjadwal.length === 0">
                            <svg class="w-3.5 h-3.5 fill-current" viewBox="0 0 20 20">
                                <path d="M0 11l2-2 5 5L18 3l2 2L7 18z" />
                            </svg>
                            <span
                                x-text="hasDraftChanges ? (hasOnlySubmitted ? 'Submit Pembaruan' : 'Submit') : (hasOnlySubmitted ? 'Telah Disubmit' : 'Kosong')"></span>
                        </button>
                    </form>
                </div>
            </div>

            <!-- Filters -->
            <div class="flex flex-col sm:flex-row justify-between items-center gap-6 mb-6">
                <div class="flex flex-wrap items-center gap-4 w-full sm:w-auto">
                    <!-- Search Input -->
                    <div class="relative flex-1 sm:w-[300px]">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        </div>
                        <input type="text" x-model="searchQuery" class="block w-full pl-9 pr-4 py-2 border border-gray-300 rounded-[5px] text-[12px] text-black focus:ring-[#4285F4]" placeholder="Cari Nama, NIM, atau Judul KP...">
                    </div>

                    <!-- Dosen Dropdown Filter -->
                    <div x-data="{ openDosen: false, dSearch: '' }" class="relative w-full sm:w-[180px] z-[60]" @click.outside="openDosen = false">
                        <button type="button" @click="openDosen = !openDosen" class="w-full text-[12px] border border-gray-300 rounded-[5px] py-2 px-3 bg-white text-black font-medium focus:ring-[#4285F4] flex justify-between items-center text-left shadow-sm">
                            <span class="truncate" x-text="filterPenguji === null ? 'Semua Penguji' : (getDosenName(filterPenguji))"></span>
                            <svg :class="openDosen ? 'rotate-0' : 'rotate-90'" class="w-3.5 h-3.5 transition-all duration-200 text-gray-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <div x-show="openDosen" x-transition x-cloak class="absolute w-full mt-1 bg-white border border-gray-300 rounded-[5px] shadow-lg overflow-hidden py-1 z-50">
                            <div class="p-2 border-b bg-gray-50">
                                <input type="text" x-model="dSearch" class="w-full text-xs p-1.5 border border-gray-300 rounded-[3px] outline-none focus:border-[#4285F4]" placeholder="Cari dosen...">
                            </div>
                            <ul class="max-h-[200px] overflow-y-auto custom-scrollbar">
                                <li class="block px-3 py-2 text-[12px] hover:bg-gray-100 cursor-pointer text-black">
                                    <input type="radio" :value="null" x-model="filterPenguji" class="hidden" @change="openDosen = false">Semua Penguji
                                </li>
                                <template x-for="d in dosenList" :key="d.id">
                                    <li x-show="d.nama.toLowerCase().includes(dSearch.toLowerCase())" class="block px-3 py-2 text-[12px] hover:bg-gray-100 cursor-pointer text-black" :class="filterPenguji == d.id ? 'bg-blue-50 text-blue-700 font-bold' : ''">
                                        <input type="radio" :value="d.id" x-model="filterPenguji" class="hidden" @change="openDosen = false">
                                        <span x-text="d.nama"></span>
                                    </li>
                                </template>
                            </ul>
                        </div>
                    </div>

                    <button x-show="isSelectingMode" x-cloak @click="bulkDestroy()" type="button"
                        class="bg-red-500 hover:bg-red-600 text-white px-3 py-1.5 rounded-[5px] text-[12px] font-bold shadow-sm transition-colors disabled:opacity-50 h-[35px]"
                        :disabled="selectedIds.length === 0">
                        Hapus Terpilih (<span x-text="selectedIds.length"></span>)
                    </button>

                    <button x-show="isSelectingMode" x-cloak @click="selectAll()" type="button"
                        class="bg-gray-200 hover:bg-gray-300 text-black px-3 py-1.5 rounded-[5px] text-[12px] font-bold shadow-sm transition-colors border border-gray-300 h-[35px]">
                        <span x-text="selectedIds.length === filteredTerjadwal.length && filteredTerjadwal.length > 0 ? 'Batal Pilih Semua' : 'Pilih Semua'"></span>
                    </button>

                    <button @click="toggleSelectingMode()" type="button"
                        class="px-3 py-1.5 rounded-[5px] text-[12px] font-bold shadow-sm transition-colors h-[35px]"
                        :class="isSelectingMode ? 'bg-[#1A1A1A] text-white hover:bg-black' : 'bg-white border border-gray-300 text-black hover:bg-gray-100'">
                        <span x-text="isSelectingMode ? 'Batal Pilih' : 'Pilih'"></span>
                    </button>
                </div>
                
                <div class="flex gap-2 w-full sm:w-auto">
                    <button type="button" @click="clearFilters()" class="flex-1 sm:flex-none border border-[#EA4335] bg-[#EA4335] text-white hover:bg-red-600 transition-colors px-4 py-1.5 rounded-[5px] text-[12px] font-bold shadow-sm flex items-center justify-center">
                        Clear Filter
                    </button>
                </div>
            </div>
            
            <div class="border border-gray-200 rounded-[10px] overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse text-[12px] text-center min-w-[1000px]">
                <thead class="bg-[#E0DFDF] font-bold text-black border-b border-[#CAC0C0] h-[45px]">
                    <tr>
                        <th class="border-r border-[#CAC0C0] px-4 py-2 w-[50px]">No</th>
                        <th class="border-r border-[#CAC0C0] px-4 py-2 text-left w-[240px]">Mahasiswa</th>
                        <th class="border-r border-[#CAC0C0] px-4 py-2 text-left">Judul kp</th>
                        <th class="border-r border-[#CAC0C0] px-4 py-2 w-[160px]">Jadwal sidang</th>
                        <th class="border-r border-[#CAC0C0] px-4 py-2 w-[180px]">Penguji 1</th>
                        <th class="border-r border-[#CAC0C0] px-4 py-2 w-[180px]">Penguji 2</th>
                        <th class="px-4 py-2 w-[100px]" x-show="!isSelectingMode">Aksi</th>
                    </tr>
                </thead>
                <tbody class="align-middle bg-white">
                    <template x-for="(mhs, index) in paginatedTerjadwal" :key="mhs.id">
                        <tr class="hover:bg-blue-50 border-b border-[#CAC0C0] transition-colors" :class="hasWarning(mhs) ? 'bg-red-50' : (selectedIds.includes(mhs.id) ? 'bg-gray-100' : '')">
                            <td class="border-r border-[#CAC0C0] px-4 py-4 font-normal text-black text-center">
                                <span x-show="!isSelectingMode" x-text="((currentPage - 1) * itemsPerPage) + index + 1"></span>
                                <div x-show="isSelectingMode" class="flex justify-center cursor-pointer" @click="toggleSelect(mhs.id)">
                                    <div class="w-4 h-4 border border-gray-400 rounded transition-colors"
                                        :class="selectedIds.includes(mhs.id) ? 'bg-gray-700 border-gray-700 shadow-inner' : 'bg-white hover:bg-gray-100'">
                                    </div>
                                </div>
                            </td>
                            <td class="border-r border-[#CAC0C0] px-4 py-4 text-left" @click="isSelectingMode ? toggleSelect(mhs.id) : null" :class="isSelectingMode ? 'cursor-pointer' : ''">
                                <div class="font-normal text-[13px] text-black sentence-case" x-text="mhs.name"></div>
                                <div class="text-[11px] text-gray-500 font-normal" x-text="mhs.nim"></div>
                                <template x-if="hasWarning(mhs)">
                                    <div class="text-[9px] font-normal text-red-600 bg-red-100 px-1.5 py-0.5 rounded mt-1">Konflik terdeteksi</div>
                                </template>
                            </td>
                            <td class="border-r border-[#CAC0C0] px-4 py-4 text-left font-normal text-black" @click="isSelectingMode ? toggleSelect(mhs.id) : null" :class="isSelectingMode ? 'cursor-pointer' : ''">
                                <div class="line-clamp-2 leading-snug sentence-case" x-text="mhs.judul"></div>
                            </td>
                            <td class="border-r border-[#CAC0C0] px-4 py-4 font-normal text-black text-center" @click="isSelectingMode ? toggleSelect(mhs.id) : null" :class="isSelectingMode ? 'cursor-pointer' : ''">
                                <div x-text="formatDate(mhs.tanggal)"></div>
                                <div class="text-[11px] mt-0.5" x-text="mhs.mulai ? mhs.mulai.substring(0,5) + ' - ' + mhs.selesai.substring(0,5) : '-'"></div>
                            </td>
                            <td class="border-r border-[#CAC0C0] px-4 py-4 font-normal text-black text-center" @click="isSelectingMode ? toggleSelect(mhs.id) : null" :class="isSelectingMode ? 'cursor-pointer' : ''">
                                <span class="sentence-case" x-text="getDosenName(mhs.penguji_1_id)"></span>
                            </td>
                            <td class="border-r border-[#CAC0C0] px-4 py-4 font-normal text-black text-center" @click="isSelectingMode ? toggleSelect(mhs.id) : null" :class="isSelectingMode ? 'cursor-pointer' : ''">
                                <span class="sentence-case" x-text="getDosenName(mhs.penguji_2_id)"></span>
                            </td>
                            <td class="px-4 py-4" x-show="!isSelectingMode">
                                <div class="flex justify-center gap-2">
                                    <button @click="selectStudent(mhs, true)" class="p-1.5 bg-blue-50 border border-blue-200 rounded-md text-blue-600 hover:bg-blue-100 transition-colors shadow-sm">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                    </button>
                                    <button @click="cancelAssignment(mhs.id)" class="p-1.5 bg-red-50 border border-red-200 rounded-md text-red-600 hover:bg-red-100 transition-colors shadow-sm">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </template>
                    <template x-if="paginatedTerjadwal.length === 0">
                        <tr>
                            <td colspan="8" class="py-16 text-center text-gray-400 italic bg-gray-50 font-normal">Data terjadwal tidak ditemukan</td>
                        </tr>
                    </template>
                </tbody>
            </table>
            </div>

            <!-- Pagination -->
            <div class="px-6 py-4 border-t border-gray-200 flex items-center justify-between mt-4" x-show="totalPages > 1">
                <span class="text-[12px] font-medium text-black/50" x-text="(filteredTerjadwal.length === 0 ? 0 : ((currentPage - 1) * itemsPerPage + 1)) + ' - ' + Math.min(currentPage * itemsPerPage, filteredTerjadwal.length) + ' dari ' + filteredTerjadwal.length + ' baris'"></span>
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

        <!-- Custom Global Confirm Modal -->
        <div x-cloak x-show="confirmDialog.show" style="display: none;" class="fixed inset-0 z-[1000] flex items-center justify-center bg-black/40 backdrop-blur-sm p-4" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95">
            <div @click.away="confirmDialog.show = false" class="bg-white rounded-[15px] w-full max-w-[420px] p-8 shadow-2xl flex flex-col items-center text-center relative overflow-hidden border border-gray-100">
                
                <!-- Icon Header Based on Type -->
                <div class="mb-6">
                    <template x-if="confirmDialog.type === 'danger'">
                        <div class="w-20 h-20 bg-red-50 rounded-full flex items-center justify-center">
                            <svg class="w-12 h-12 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                        </div>
                    </template>
                    <template x-if="confirmDialog.type === 'info'">
                        <div class="w-20 h-20 bg-blue-50 rounded-full flex items-center justify-center">
                            <svg class="w-12 h-12 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                    </template>
                    <template x-if="confirmDialog.type === 'warning'">
                        <div class="w-20 h-20 bg-yellow-50 rounded-full flex items-center justify-center">
                            <svg class="w-12 h-12 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                        </div>
                    </template>
                </div>

                <h3 class="text-[18px] font-bold text-gray-900 mb-3" x-text="confirmDialog.title"></h3>
                <p class="text-[14px] text-gray-500 mb-8 leading-relaxed px-2" x-text="confirmDialog.message"></p>

                <div class="flex gap-4 w-full">
                    <button @click="confirmDialog.show = false" type="button" class="flex-1 h-[45px] bg-gray-50 hover:bg-gray-100 text-gray-700 rounded-[10px] text-[14px] font-bold transition-all border border-gray-200">
                        Batal
                    </button>
                    <button @click="executeConfirm()" type="button" 
                        class="flex-1 h-[45px] text-white rounded-[10px] text-[14px] font-bold transition-all shadow-md active:transform active:scale-95"
                        :class="[
                            confirmDialog.type === 'danger' ? 'bg-[#E53935] hover:bg-red-700' : '',
                            confirmDialog.type === 'info' ? 'bg-[#4285F4] hover:bg-blue-700' : '',
                            confirmDialog.type === 'warning' ? 'bg-[#FBC02D] hover:bg-yellow-700' : ''
                        ]"
                        x-text="confirmDialog.confirmText">
                    </button>
                </div>
            </div>
        </div>
    </div>

</x-dashboard-layout>
