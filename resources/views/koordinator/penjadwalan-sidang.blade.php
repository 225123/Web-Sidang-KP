<x-dashboard-layout header="Penjadwalan Sidang" userName="{{ auth()->user()->name }}" roleName="KOORDINATOR KP">
    <x-slot:sidebar>
        @include('koordinator.components.sidebar', ['active' => 'penjadwalan'])
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

        <style>
            .filter-bar { display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px; flex-wrap: wrap; gap: 10px; }
            .search-box { position: relative; width: 350px; max-width: 100%; }
            .search-box input { width: 100%; padding: 8px 10px 8px 35px; border: 1px solid #ced4da; border-radius: 6px; font-size: 13px; }
            .search-icon { position: absolute; left: 10px; top: 50%; transform: translateY(-50%); color: #999; }
            .controls { display: flex; gap: 15px; align-items: center; font-size: 13px; font-weight: normal; }
            .sentence-case { text-transform: lowercase; }
            .sentence-case::first-letter { text-transform: uppercase; }
            .custom-scrollbar::-webkit-scrollbar { width: 4px; }
            .custom-scrollbar::-webkit-scrollbar-track { background: #f1f1f1; }
            .custom-scrollbar::-webkit-scrollbar-thumb { background: #ccc; border-radius: 10px; }
            .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #bbb; }
        </style>

        @php
            $parsedTunggu = $daftarTunggu;
            $parsedTerjadwal = $terjadwal;
        @endphp

        <script>
            window.penjadwalanManager = function() {
                return {
                    daftarTunggu: @json($parsedTunggu),
                    terjadwal: @json($parsedTerjadwal),
                    searchQuery: '',
                    filterTanggal: 'all',
                    filterWaktu: 'all',
                    isSelectingMode: false,
                    selectedIds: [],
                    form: { mode: 'create', isLoading: false, id: '', tanggal: '', mulai: '', selesai: '', ruang: '' },
                    filterRuang: 'all',
                    currentPage: 1,
                    itemsPerPage: 10,
                    alert: { show: false, type: 'success', title: '', message: '' },
                    confirmDialog: { show: false, message: '', actionType: '', data: null, callback: null },
                    topLoading: false,

                    async refreshState() {
                        this.topLoading = true;
                        try {
                            const response = await fetch("{{ route('koordinator.penjadwalan.index') }}", {
                                headers: { "Accept": "application/json", "X-Requested-With": "XMLHttpRequest" }
                            });
                            const data = await response.json();
                            this.daftarTunggu = data.daftarTunggu;
                            this.terjadwal = data.terjadwal;
                        } catch (e) {
                            this.showAlert('error', 'Error', 'Gagal memuat ulang data.');
                        } finally {
                            setTimeout(() => { this.topLoading = false; }, 500);
                        }
                    },

                    showConfirm(msg, callbackOrFn) { 
                        this.confirmDialog.message = msg; 
                        this.confirmDialog.callback = callbackOrFn; 
                        this.confirmDialog.show = true; 
                    },

                    executeConfirm() {
                        if (this.confirmDialog.callback && typeof this.confirmDialog.callback === 'function') { 
                            this.confirmDialog.callback(); 
                        } else if (this.confirmDialog.actionType === 'delete') {
                            this.executeDeleteSchedule(this.confirmDialog.data);
                        }
                        this.confirmDialog.show = false;
                    },

                    uniqueTanggals() {
                        const dates = this.terjadwal.map(t => t.tanggal).filter(t => t);
                        return [...new Set(dates)].sort();
                    },

                    uniqueWaktus() {
                        const times = this.terjadwal.map(t => t.mulai).filter(t => t);
                        return [...new Set(times)].sort();
                    },

                    uniqueRuangs() {
                        const rooms = this.terjadwal.map(t => t.ruang).filter(t => t);
                        return [...new Set(rooms)].sort();
                    },

                    filteredTerjadwal() {
                        let result = this.terjadwal;
                        
                        if (this.filterTanggal !== 'all') {
                            result = result.filter(r => r.tanggal === this.filterTanggal);
                        }
                        if (this.filterWaktu !== 'all') {
                            result = result.filter(r => r.mulai === this.filterWaktu);
                        }
                        if (this.filterRuang !== 'all') {
                            result = result.filter(r => r.ruang === this.filterRuang);
                        }
                        if (this.searchQuery) {
                            const q = this.searchQuery.toLowerCase();
                            result = result.filter(r => 
                                (r.name && r.name.toLowerCase().includes(q)) || 
                                (r.nim && r.nim.toLowerCase().includes(q)) || 
                                (r.judul && r.judul.toLowerCase().includes(q)) ||
                                (r.ruang && r.ruang.toLowerCase().includes(q))
                            );
                        }
                        return result;
                    },

                    paginatedTerjadwal() { 
                        const start = (this.currentPage - 1) * this.itemsPerPage; 
                        return this.filteredTerjadwal().slice(start, start + this.itemsPerPage); 
                    },

                    totalPages() { 
                        return Math.ceil(this.filteredTerjadwal().length / this.itemsPerPage) || 1; 
                    },

                    formatTime(timeStr) {
                        return timeStr ? timeStr.substring(0, 5) : '-';
                    },

                    formatDate(dateString) { 
                        if (!dateString) return '-'; 
                        const parts = dateString.split('-'); 
                        if (parts.length < 3) return dateString;
                        const dateObj = new Date(parts[0], parts[1]-1, parts[2]); 
                        return new Intl.DateTimeFormat('id-ID', { year: 'numeric', month: 'short', day: '2-digit' }).format(dateObj); 
                    },

                    formatMonthYear(m, y) {
                        try {
                            return new Intl.DateTimeFormat('id-ID', { month: 'long', year: 'numeric' }).format(new Date(y, m));
                        } catch(e) { return ''; }
                    },

                    autoFillSelesai() { 
                        if (this.form.mulai) { 
                            let parts = this.form.mulai.split(':'); 
                            if (parts.length >= 2) { 
                                let hours = (parseInt(parts[0]) + 1) % 24; 
                                this.form.selesai = hours.toString().padStart(2, '0') + ':' + parts[1]; 
                            } 
                        } 
                    },

                    autoPlotModal: { 
                        show: false, 
                        dates: [], 
                        isLoading: false,
                        error: '',
                        month: new Date().getMonth(),
                        year: new Date().getFullYear(),
                        days: []
                    },

                    resetForm() { 
                        this.form.mode = 'create'; 
                        this.form.id = ''; 
                        this.form.tanggal = ''; 
                        this.form.mulai = ''; 
                        this.form.selesai = ''; 
                        this.form.ruang = ''; 
                    },

                    init() {
                        // General init if needed
                    },

                    initCalendar() {
                        if (!this.autoPlotModal) return;
                        // Reset days array
                        let days = [];
                        const firstDayOfMonth = new Date(this.autoPlotModal.year, this.autoPlotModal.month, 1).getDay();
                        const daysInMonth = new Date(this.autoPlotModal.year, this.autoPlotModal.month + 1, 0).getDate();
                        const daysInPrevMonth = new Date(this.autoPlotModal.year, this.autoPlotModal.month, 0).getDate();

                        // Padding from previous month
                        for (let i = firstDayOfMonth - 1; i >= 0; i--) {
                            const d = daysInPrevMonth - i;
                            days.push({
                                day: d,
                                date: this.formatCalendarDate(d, this.autoPlotModal.month - 1, this.autoPlotModal.year),
                                isCurrentMonth: false
                            });
                        }

                        // Days of current month
                        for (let i = 1; i <= daysInMonth; i++) {
                            days.push({
                                day: i,
                                date: this.formatCalendarDate(i, this.autoPlotModal.month, this.autoPlotModal.year),
                                isCurrentMonth: true
                            });
                        }

                        // Padding for next month to complete 6 rows (42 cells)
                        let nextDay = 1;
                        while (days.length < 42) {
                            days.push({
                                day: nextDay,
                                date: this.formatCalendarDate(nextDay, this.autoPlotModal.month + 1, this.autoPlotModal.year),
                                isCurrentMonth: false
                            });
                            nextDay++;
                        }
                        this.autoPlotModal.days = days;
                    },

                    formatCalendarDate(d, m, y) {
                        const date = new Date(y, m, d);
                        const year = date.getFullYear();
                        const month = (date.getMonth() + 1).toString().padStart(2, '0');
                        const day = date.getDate().toString().padStart(2, '0');
                        return `${year}-${month}-${day}`;
                    },

                    changeMonth(offset) {
                        this.autoPlotModal.month += offset;
                        if (this.autoPlotModal.month > 11) {
                            this.autoPlotModal.month = 0;
                            this.autoPlotModal.year++;
                        } else if (this.autoPlotModal.month < 0) {
                            this.autoPlotModal.month = 11;
                            this.autoPlotModal.year--;
                        }
                        this.initCalendar();
                    },

                    toggleAutoDate(dateStr) {
                        if (new Date(dateStr) < new Date().setHours(0,0,0,0)) return;

                        const index = this.autoPlotModal.dates.indexOf(dateStr);
                        if (index > -1) {
                            this.autoPlotModal.dates.splice(index, 1);
                        } else {
                            this.autoPlotModal.dates.push(dateStr);
                        }
                        this.autoPlotModal.dates.sort();
                        this.autoPlotModal.error = '';
                    },
                    
                    minDaysRequired() { 
                        return Math.ceil(this.daftarTunggu.length / 18) || 1; 
                    },
                    
                    targetPerDay() {
                        if (!this.autoPlotModal || this.autoPlotModal.dates.length === 0) return 18;
                        return Math.ceil(this.daftarTunggu.length / this.autoPlotModal.dates.length);
                    },

                    totalCapacity() { 
                        if (!this.autoPlotModal) return 0;
                        return this.autoPlotModal.dates.length * 18; 
                    },

                    async executeAutoPlot() {
                        this.autoPlotModal.error = '';
                        
                        if (this.autoPlotModal.dates.length === 0) {
                            this.autoPlotModal.error = 'Silakan pilih setidaknya satu tanggal pada kalender.';
                            return;
                        }

                        if (this.autoPlotModal.dates.length < this.minDaysRequired()) {
                            this.autoPlotModal.error = `Minimal dibutuhkan ${this.minDaysRequired()} hari untuk ${this.daftarTunggu.length} mahasiswa (kapasitas maks 18/hari).`;
                            return;
                        }

                        this.autoPlotModal.isLoading = true;
                        try {
                            const response = await fetch("{{ route('koordinator.penjadwalan.auto') }}", {
                                method: "POST",
                                headers: { "Content-Type": "application/json", "X-CSRF-TOKEN": "{{ csrf_token() }}" },
                                body: JSON.stringify({ dates: this.autoPlotModal.dates })
                            });
                            const result = await response.json();
                            if (result.success) {
                                this.showAlert('success', 'Berhasil', result.message);
                                this.autoPlotModal.show = false;
                                this.refreshState();
                            } else {
                                this.autoPlotModal.error = result.message;
                            }
                        } catch (e) {
                            this.autoPlotModal.error = 'Terjadi kesalahan sistem saat memproses plotting.';
                        } finally {
                            this.autoPlotModal.isLoading = false;
                        }
                    },

                    showAlert(type, title, message) { 
                        this.alert.type = type; 
                        this.alert.title = title; 
                        this.alert.message = message; 
                        this.alert.show = true; 
                        setTimeout(() => { if (this.alert.show) this.alert.show = false; }, 5000); 
                    },

                    selectForSchedule(id) { 
                        this.resetForm(); 
                        this.form.id = id; 
                        document.getElementById('form-box').scrollIntoView({ behavior: 'smooth' }); 
                    },

                    getStudentDisplayName(id) {
                        const mhs = this.terjadwal.find(t => t.id == id) || this.daftarTunggu.find(d => d.id == id);
                        return mhs ? `${mhs.nim} - ${mhs.name}` : '-';
                    },

                    editSchedule(id) {
                        const s = this.terjadwal.find(t => t.id == id);
                        if (!s) return;
                        this.form.mode = 'edit';
                        this.form.id = s.id;
                        this.form.tanggal = s.tanggal;
                        this.form.mulai = this.formatTime(s.mulai);
                        this.form.selesai = this.formatTime(s.selesai);
                        this.form.ruang = s.ruang;
                        document.getElementById('form-box').scrollIntoView({ behavior: 'smooth' });
                    },

                    promptDeleteSchedule(id) {
                        this.confirmDialog.actionType = 'delete';
                        this.confirmDialog.data = id;
                        this.showConfirm('Kembalikan mahasiswa ini ke daftar tunggu? Jadwal akan dihapus.');
                    },

                    async executeDeleteSchedule(id) {
                        try {
                            const res = await fetch(`{{ url('koordinator/penjadwalan') }}/${id}`, {
                                method: 'DELETE',
                                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
                            });
                            const result = await res.json();
                            if (result.success) { this.refreshState(); }
                            else { this.showAlert('error', 'Gagal', result.message); }
                        } catch (e) { this.showAlert('error', 'Error', 'Gagal menghapus jadwal.'); }
                    },

                    toggleSelectingMode() { this.isSelectingMode = !this.isSelectingMode; this.selectedIds = []; },

                    toggleSelect(id) {
                        const index = this.selectedIds.indexOf(id);
                        if (index > -1) this.selectedIds.splice(index, 1);
                        else this.selectedIds.push(id);
                    },

                    selectAll() {
                        if (this.selectedIds.length === this.filteredTerjadwal().length) {
                            this.selectedIds = [];
                        } else {
                            this.selectedIds = this.filteredTerjadwal().map(r => r.id);
                        }
                    },

                    promptBulkDelete() {
                        if (this.selectedIds.length === 0) return;
                        this.showConfirm(`Kembalikan ${this.selectedIds.length} mahasiswa terpilih ke daftar tunggu?`, async () => {
                            try {
                                const res = await fetch("{{ route('koordinator.penjadwalan.bulk-destroy') }}", {
                                    method: "POST",
                                    headers: { "Content-Type": "application/json", "X-CSRF-TOKEN": "{{ csrf_token() }}" },
                                    body: JSON.stringify({ ids: this.selectedIds })
                                });
                                const result = await res.json();
                                if (result.success) { this.refreshState(); }
                                else { this.showAlert('error', 'Gagal', result.message); }
                            } catch (e) { this.showAlert('error', 'Error', 'Terjadi kesalahan.'); }
                        });
                    },

                    clearFilters() {
                        this.searchQuery = '';
                        this.filterTanggal = 'all';
                        this.filterWaktu = 'all';
                        this.filterRuang = 'all';
                        this.currentPage = 1;
                    },

                    async saveSchedule() {
                        if (!this.form.id || !this.form.tanggal || !this.form.mulai || !this.form.selesai || !this.form.ruang) {
                            this.showAlert('error', 'Form Tidak Lengkap', 'Lengkapi data penjadwalan.');
                            return;
                        }
                        this.form.isLoading = true;
                        try {
                            const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                            const response = await fetch("{{ route('koordinator.penjadwalan.store') }}", {
                                method: "POST",
                                headers: {
                                    "Content-Type": "application/json",
                                    "X-CSRF-TOKEN": token,
                                    "Accept": "application/json"
                                },
                                body: JSON.stringify({
                                    id: this.form.id,
                                    tanggal_sidang: this.form.tanggal,
                                    waktu_mulai_sidang: this.form.mulai,
                                    waktu_selesai_sidang: this.form.selesai,
                                    ruang_sidang: this.form.ruang
                                })
                            });
                            const result = await response.json();
                            if (result.success) {
                                this.resetForm();
                                this.refreshState();
                            } else {
                                this.showAlert('error', 'Gagal', result.message);
                            }
                        } catch (e) {
                            this.showAlert('error', 'Gagal', 'Terjadi kesalahan sistem.');
                        } finally {
                            this.form.isLoading = false;
                        }
                    }
                };
            };
        </script>

        <div class="w-full flex-1 pb-10 relative" x-data="penjadwalanManager()" x-init="init()">
            <!-- Top Progress Bar (SPA Style) -->
            <div x-cloak x-show="topLoading" x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 w-0" x-transition:enter-end="opacity-100 w-full"
                x-transition:leave="transition ease-in duration-300" x-transition:leave-start="opacity-100 w-full"
                x-transition:leave-end="opacity-0 w-full" class="fixed top-0 left-0 right-0 z-[110] pointer-events-none">
                <div class="h-1 bg-[#4285F4] shadow-[0_0_10px_rgba(66,133,244,0.8)] animate-pulse"></div>
            </div>

            <style>
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

                /* Calendar Styles */
                .calendar-grid { display: grid; grid-template-columns: repeat(7, 1fr); gap: 4px; }
                .calendar-day { 
                    aspect-ratio: 1; 
                    display: flex; 
                    align-items: center; 
                    justify-content: center; 
                    font-size: 13px; 
                    font-weight: 500; 
                    cursor: pointer; 
                    border-radius: 8px; 
                    transition: all 0.2s;
                }
                .calendar-day.other-month { color: #ccc; }
                .calendar-day.selected { background-color: #4285F4; color: white; font-weight: bold; box-shadow: 0 2px 4px rgba(66, 133, 244, 0.3); }
                .calendar-day.today { border: 2px solid #4285F4; color: #4285F4; }
                .calendar-day:hover:not(.selected) { background-color: #f3f4f6; }
                .calendar-header-day { font-size: 11px; font-weight: 800; color: #9ca3af; text-align: center; padding-bottom: 8px; text-transform: uppercase; }
            </style>
            <!-- Alert Box (Success/Error from AJAX) -->
            <div x-cloak x-show="alert.show" class="mb-4 px-2 xl:px-0">
                <div class="border rounded-[5px] px-4 py-3 relative shadow-sm w-full flex items-start gap-3"
                    :class="alert.type === 'success' ? 'bg-green-100 border-green-400 text-green-700' : 'bg-red-100 border-red-400 text-red-700'">
                    <svg x-show="alert.type === 'success'" class="w-5 h-5 flex-shrink-0 mt-0.5" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    <svg x-show="alert.type === 'error'" class="w-5 h-5 flex-shrink-0 mt-0.5" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z">
                        </path>
                    </svg>
                    <div class="flex-1">
                        <span class="block font-bold text-[13px]" x-text="alert.title"></span>
                        <span class="block text-[12px] mt-0.5" x-text="alert.message"></span>
                    </div>
                    <button @click="alert.show = false" type="button"
                        class="text-gray-500 hover:text-gray-700 focus:outline-none">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Info & Sync Buttons -->
            <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6 mb-6">
                <div
                    class="flex-1 bg-[#E6F0FA] border border-[#D0E3F5] rounded-[10px] p-4 lg:p-5 flex items-start gap-4 shadow-sm w-full">
                    <div
                        class="w-6 h-6 rounded-full bg-[#4285F4] text-white flex items-center justify-center font-bold flex-shrink-0 mt-0.5">
                        i</div>
                    <p class="text-[14px] text-black font-normal leading-relaxed m-0 mt-0.5">
                        Penjadwalan sidang mahasiswa kp. <br>
                        Jadwalkan satu persatu, lalu tekan 'submit' untuk mempublikasikan revisi secara langsung ke
                        mahasiswa.
                    </p>
                </div>

                <div class="flex items-center shrink-0 ml-auto w-full lg:w-auto">
                    <button type="button"
                        @click="autoPlotModal.show = true; initCalendar()"
                        class="bg-[#4285F4] hover:bg-blue-600 font-bold text-white rounded-[5px] px-6 py-2 text-[13px] flex items-center justify-center gap-2 shadow-sm w-full lg:w-[120px] transition-colors"
                        title="Atur Auto-plotting">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                        </svg>
                        Auto
                    </button>
                </div>
            </div>

            <!-- Enhanced Auto Plotting (Top-Aligned Integration) -->
            <div x-cloak x-show="autoPlotModal.show" style="display: none;"
                class="absolute inset-x-0 top-0 z-[150] flex justify-center items-start bg-transparent p-4 min-h-full"
                x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 scale-95"
                x-transition:enter-end="opacity-100 scale-100" x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95">
                <div @click.outside="if(!autoPlotModal.isLoading) autoPlotModal.show = false"
                    class="bg-white rounded-[15px] w-full max-w-[500px] shadow-2xl overflow-hidden border border-gray-100">
                    
                    <!-- Header -->
                    <div class="bg-gray-50 px-8 py-6 border-b border-gray-100 flex justify-between items-center">
                        <div>
                            <h3 class="text-[18px] font-bold text-gray-900">Konfigurasi Auto-Plotting</h3>
                            <p class="text-[12px] text-gray-500 mt-1">Atur tanggal pelaksanaan sidang secara massal</p>
                        </div>
                        <button @click="autoPlotModal.show = false" class="text-gray-400 hover:text-gray-600 transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </button>
                    </div>

                    <div class="p-8">
                        <!-- Advanced Stats Summary -->
                        <div class="grid grid-cols-3 gap-3 mb-6">
                            <div class="bg-gray-50 border border-gray-100 p-3 rounded-[10px] text-center">
                                <div class="text-[10px] font-bold text-gray-500 uppercase tracking-widest mb-1">Daftar Tunggu</div>
                                <div class="text-[16px] font-bold text-gray-900" x-text="daftarTunggu ? daftarTunggu.length : 0"></div>
                            </div>
                            <div class="bg-blue-50 border border-blue-100 p-3 rounded-[10px] text-center">
                                <div class="text-[10px] font-bold text-blue-600 uppercase tracking-widest mb-1">Target Sidang/Hari</div>
                                <div class="text-[16px] font-bold text-blue-900" x-text="targetPerDay()"></div>
                            </div>
                            <div class="bg-purple-50 border border-purple-100 p-3 rounded-[10px] text-center">
                                <div class="text-[10px] font-bold text-purple-600 uppercase tracking-widest mb-1">Min. Hari</div>
                                <div class="text-[16px] font-bold text-purple-900" x-text="minDaysRequired()"></div>
                            </div>
                        </div>

                        <!-- Info Alert -->
                        <div class="bg-blue-50 border-l-4 border-blue-400 p-4 mb-6 flex items-start gap-3">
                            <svg class="w-5 h-5 text-blue-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            <p class="text-[12px] text-blue-700 leading-relaxed font-medium">
                                Klik langsung pada tanggal di kalender untuk menjadwalkan. Pastikan total kapasitas mencukupi jumlah antrean.
                            </p>
                        </div>

                        <!-- Custom Calendar UI -->
                        <div class="border border-gray-100 rounded-[12px] p-4 bg-white shadow-sm mb-6">
                            <!-- Calendar Month Header -->
                            <div class="flex items-center justify-between mb-6">
                                <h4 class="text-[15px] font-bold text-gray-800" x-text="formatMonthYear(autoPlotModal.month, autoPlotModal.year)"></h4>
                                <div class="flex gap-1">
                                    <button @click="changeMonth(-1)" type="button" class="p-2 hover:bg-gray-50 rounded-lg text-gray-500"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg></button>
                                    <button @click="changeMonth(1)" type="button" class="p-2 hover:bg-gray-50 rounded-lg text-gray-500"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg></button>
                                </div>
                            </div>

                            <!-- Calendar Grid -->
                            <div class="calendar-grid">
                                <div class="calendar-header-day">Min</div>
                                <div class="calendar-header-day">Sen</div>
                                <div class="calendar-header-day">Sel</div>
                                <div class="calendar-header-day">Rab</div>
                                <div class="calendar-header-day">Kam</div>
                                <div class="calendar-header-day">Jum</div>
                                <div class="calendar-header-day">Sab</div>

                                <template x-for="dayObj in autoPlotModal.days" :key="dayObj.date">
                                    <div @click="toggleAutoDate(dayObj.date)" 
                                        class="calendar-day" 
                                        :class="[
                                            !dayObj.isCurrentMonth ? 'other-month' : '',
                                            autoPlotModal.dates.includes(dayObj.date) ? 'selected' : '',
                                            dayObj.date === new Date().toISOString().split('T')[0] ? 'today' : ''
                                        ]"
                                        x-text="dayObj.day">
                                    </div>
                                </template>
                            </div>
                        </div>

                        <!-- Capacity Warning (Dynamic Error Alert) -->
                        <div x-cloak x-show="autoPlotModal.error" 
                            x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 -translate-y-2" x-transition:enter-end="opacity-100 translate-y-0"
                            class="mb-6 bg-red-50 border border-red-100 p-4 rounded-[10px] flex items-start gap-3">
                            <svg class="w-5 h-5 text-red-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                            <div>
                                <h5 class="text-[13px] font-bold text-red-800 mb-1">Plotting Diblokir</h5>
                                <p class="text-[12px] text-red-600 leading-relaxed font-medium" x-text="autoPlotModal.error"></p>
                            </div>
                        </div>

                        <!-- Selected Summary -->
                        <div class="flex items-center justify-between mb-8 px-1">
                            <span class="text-[12px] font-bold text-gray-500 uppercase tracking-widest">Terpilih: <span class="text-blue-600" x-text="autoPlotModal ? autoPlotModal.dates.length : 0"></span> Hari</span>
                            <span class="text-[13px] font-bold" :class="autoPlotModal.dates.length >= minDaysRequired() ? 'text-green-600' : 'text-orange-600'">
                                Distribusi: <span x-text="targetPerDay()"></span> / hari (Maks 18)
                            </span>
                        </div>

                        <!-- Actions -->
                        <div class="flex gap-4 pt-4 border-t border-gray-50">
                            <button @click="autoPlotModal.show = false" :disabled="autoPlotModal.isLoading" type="button" 
                                class="flex-1 h-[48px] bg-gray-50 hover:bg-gray-100 text-gray-700 rounded-[10px] text-[14px] font-bold transition-all border border-gray-200 disabled:opacity-50">
                                Batal
                            </button>
                            <button @click="executeAutoPlot()" :disabled="autoPlotModal.isLoading" type="button" 
                                class="flex-1 h-[48px] bg-[#4285F4] hover:bg-blue-700 text-white rounded-[10px] text-[14px] font-bold transition-all shadow-md active:transform active:scale-95 disabled:opacity-50 flex items-center justify-center gap-2">
                                <span x-show="!autoPlotModal.isLoading">Mulai Plotting</span>
                                <div x-show="autoPlotModal.isLoading" class="animate-spin rounded-full h-4 w-4 border-b-2 border-white"></div>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Custom Confirmation Modal -->
            <div x-cloak x-show="confirmDialog.show"
                class="fixed inset-0 z-[100] flex items-center justify-center bg-black/40 backdrop-blur-sm transition-opacity">
                <div @click.outside="confirmDialog.show = false"
                    class="bg-white rounded-[10px] p-6 max-w-sm w-full mx-4 shadow-xl transform transition-all scale-100">
                    <div class="flex items-center justify-center w-12 h-12 mx-auto bg-blue-100 rounded-full mb-4">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <h3 class="text-center font-bold text-gray-900 text-[16px] mb-2">Konfirmasi Aksi</h3>
                    <p class="text-center text-gray-500 text-[13px] mb-6 leading-relaxed"
                        x-text="confirmDialog.message"></p>
                    <div class="flex justify-center gap-3">
                        <button @click="confirmDialog.show = false" type="button"
                            class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold rounded-[5px] text-[13px] transition w-full">Batal</button>
                        <button @click="executeConfirm()" type="button"
                            class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-[5px] text-[13px] transition shadow-sm w-full">Lanjutkan</button>
                    </div>
                </div>
            </div>

            <!-- Dashboard Content -->
            <div class="flex flex-col lg:flex-row gap-6 mb-8 mt-4">

                <!-- Kiri: Daftar Tunggu Box -->
                <div class="bg-white rounded-[15px] p-6 w-full lg:w-[45%] h-auto flex flex-col justify-start border border-gray-200 shadow-sm overflow-hidden">
                    <div class="flex justify-between items-center border-b border-gray-200 pb-4 mb-4">
                        <h3 class="font-bold text-[16px] text-black tracking-tight">Daftar Tunggu </h3>
                        <div class="font-bold text-[12px] text-black/60 bg-gray-100 px-3 py-1 rounded-full">
                            <span x-text="daftarTunggu.length"></span> / {{ $totalMahasiswa }}
                        </div>
                    </div>
                    <div class="text-[12px] font-medium text-black pr-2 max-h-[300px] overflow-y-auto custom-scrollbar">
                        <table class="w-full border-collapse">
                            <tbody>
                                <template x-for="(mhs, index) in daftarTunggu" :key="mhs.id">
                                    <tr class="border-b border-gray-100 hover:bg-gray-50 cursor-pointer transition-colors"
                                        @click="selectForSchedule(mhs.id)">
                                        <td class="py-2.5 pr-2 whitespace-nowrap align-middle text-left text-gray-500 w-[20px] font-normal"
                                            x-text="(index + 1) + '.'"></td>
                                        <td class="py-2.5 px-2 align-middle text-left text-black font-normal">
                                            <span class="font-bold" x-text="mhs.nim"></span> - <span class="sentence-case text-gray-600" x-text="mhs.name"></span>
                                        </td>
                                        <td class="py-2.5 pl-2 align-middle text-right">
                                            <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                                            </svg>
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                        <template x-if="daftarTunggu.length === 0">
                            <div class="text-center py-8 text-gray-400 italic font-medium uppercase tracking-widest text-[11px]">Semua Mahasiswa Telah Terjadwal</div>
                        </template>
                    </div>
                </div>

                <!-- Kanan: Form -->
                <div id="form-box" class="bg-white rounded-[15px] p-6 w-full lg:w-[55%] flex flex-col h-auto border border-gray-200 shadow-sm overflow-hidden relative">
                    <div x-show="form.isLoading"
                        class="absolute inset-0 bg-white/70 backdrop-blur-sm z-10 flex items-center justify-center rounded-[15px]">
                        <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
                    </div>

                    <h3 class="font-bold text-[16px] text-black mb-4 border-b border-gray-200 pb-4 flex items-center gap-2 tracking-tight">
                        <svg class="w-4 h-4 text-[#4285F4]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        <span x-text="form.mode === 'edit' ? 'Edit Penjadwalan' : 'Penjadwalan'"></span>
                    </h3>

                    <form @submit.prevent="saveSchedule()" class="flex flex-col flex-1">
                        <input type="hidden" x-model="form.id">

                        <div class="grid grid-cols-1 md:grid-cols-[100px_1fr] gap-3 items-center mb-4 text-[12px]">
                            <label class="font-bold text-gray-700">Mahasiswa</label>
                            <select x-show="form.mode === 'create'" x-model="form.id"
                                :class="form.id ? 'text-black' : 'text-gray-400'"
                                class="border border-gray-300 rounded-[5px] px-3 py-2 focus:border-[#4285F4] focus:ring-1 focus:ring-[#4285F4] outline-none font-medium shadow-sm"
                                :required="form.mode === 'create'">
                                <option value="" class="text-gray-400">-- Pilih dari Daftar Tunggu --</option>
                                <template x-for="mhs in daftarTunggu" :key="mhs.id">
                                    <option :value="mhs.id" x-text="mhs.nim + ' - ' + mhs.name" class="text-black"></option>
                                </template>
                            </select>
                            <div x-show="form.mode === 'edit'"
                                class="bg-gray-50 border border-gray-300 rounded-[5px] px-3 py-2 font-medium text-black flex items-center shadow-sm">
                                <span x-text="getStudentDisplayName(form.id)"></span>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-[100px_1fr] gap-3 items-center mb-4 text-[12px]">
                            <label class="font-bold text-gray-700">Tanggal</label>
                            <input type="date" x-model="form.tanggal" required
                                :class="form.tanggal ? 'text-black' : 'text-gray-400'"
                                class="border border-gray-300 rounded-[5px] px-3 py-2 focus:border-[#4285F4] focus:ring-1 focus:ring-[#4285F4] outline-none w-full font-medium placeholder:text-gray-400 shadow-sm min-h-[36px]">
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-[100px_1fr] gap-3 items-center mb-4 text-[12px]">
                            <label class="font-bold text-gray-700">Waktu</label>
                            <div class="flex items-center gap-2">
                                <input type="time" x-model="form.mulai" @change="autoFillSelesai()" required
                                    :class="form.mulai ? 'text-black' : 'text-gray-400'"
                                    class="flex-1 border border-gray-300 rounded-[5px] px-3 py-2 focus:border-[#4285F4] focus:ring-1 focus:ring-[#4285F4] outline-none font-medium shadow-sm min-h-[36px]">
                                <span class="font-bold text-gray-400">-</span>
                                <input type="time" x-model="form.selesai" required
                                    :class="form.selesai ? 'text-black' : 'text-gray-400'"
                                    class="flex-1 border border-gray-300 rounded-[5px] px-3 py-2 focus:border-[#4285F4] focus:ring-1 focus:ring-[#4285F4] outline-none font-medium shadow-sm min-h-[36px]">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-[100px_1fr] gap-3 items-center mb-6 text-[12px]">
                            <label class="font-bold text-gray-700">Ruangan</label>
                            <input type="text" x-model="form.ruang" placeholder="Misal: E306" required
                                class="border border-gray-300 rounded-[5px] px-3 py-2 focus:border-[#4285F4] focus:ring-1 focus:ring-[#4285F4] outline-none w-full text-black font-medium placeholder:text-gray-400 uppercase shadow-sm">
                        </div>

                        <div class="flex justify-end gap-3 mt-auto pt-4 border-t border-gray-200">
                            <button type="button" @click="resetForm()"
                                class="bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 px-5 py-2 rounded-[5px] text-[12px] font-bold flex items-center gap-2 transition-colors shadow-sm focus:outline-none">
                                Batal
                            </button>
                            <button type="submit"
                                class="bg-[#34A853] hover:bg-[#2c8d46] text-white px-5 py-2 rounded-[5px] text-[12px] font-bold flex items-center gap-1.5 transition-colors shadow-sm focus:outline-none">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                <span x-text="form.mode === 'edit' ? 'Update & Simpan' : 'Tambahkan'"></span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="bg-white rounded-[15px] border border-gray-200 shadow-sm overflow-hidden p-6 mb-8">
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center border-b border-gray-200 pb-4 mb-6">
                    <div>
                        <h2 class="text-[18px] font-bold text-black tracking-tight m-0">Daftar Terjadwal</h2>
                        <p class="text-[12px] text-black/60 font-medium mt-1">Jadwal sidang mahasiswa yang telah ditetapkan.</p>
                    </div>
                </div>

                <!-- Filter & Search Bar -->
                <div class="flex flex-wrap items-center gap-3 mb-6">
                    <!-- Search Box -->
                    <div class="relative flex-1 min-w-[200px]">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </div>
                        <input type="text" x-model="searchQuery" @input="currentPage = 1" placeholder="Cari nama, NIM, judul KP, atau ruangan..." class="block w-full pl-9 pr-4 py-2 border border-gray-300 rounded-[5px] text-[12px] text-black focus:ring-[#4285F4] shadow-sm">
                    </div>

                    <div class="flex flex-wrap items-center gap-2 w-full sm:w-auto">
                        <button x-show="isSelectingMode" x-cloak @click="selectAll()" type="button"
                            class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-3 py-2 rounded-[5px] text-[12px] font-medium transition-colors border border-gray-300 shadow-sm whitespace-nowrap">
                            <span x-text="selectedIds.length === filteredTerjadwal().length && filteredTerjadwal().length > 0 ? 'Batal Pilih Semua' : 'Pilih Semua'"></span>
                        </button>

                        <button x-show="isSelectingMode" x-cloak @click="promptBulkDelete()" type="button"
                            class="bg-[#EA3323] hover:bg-red-700 text-white px-3 py-2 rounded-[5px] text-[12px] font-bold transition-colors shadow-sm disabled:opacity-50 whitespace-nowrap"
                            :disabled="selectedIds.length === 0">
                            Hapus Terpilih (<span x-text="selectedIds.length"></span>)
                        </button>

                        <button @click="toggleSelectingMode()" type="button"
                            class="px-3 py-2 rounded-[5px] text-[12px] font-medium transition-colors shadow-sm border whitespace-nowrap"
                            :class="isSelectingMode ? 'bg-gray-800 text-white border-gray-800 hover:bg-gray-900' : 'bg-white border-gray-300 text-gray-700 hover:bg-gray-50'">
                            <span x-text="isSelectingMode ? 'Batal Pilih' : 'Pilih'"></span>
                        </button>

                        <div class="hidden md:block w-px h-6 bg-gray-300 mx-1"></div>

                        <!-- Tanggal Filter Dropdown -->
                        <div x-data="{ openTanggal: false }" class="relative w-full sm:w-[150px] z-[60]" @click.outside="openTanggal = false">
                            <button type="button" @click="openTanggal = !openTanggal" class="w-full text-[12px] border border-gray-300 rounded-[5px] py-2 px-3 bg-white text-black font-medium focus:ring-[#4285F4] flex justify-between items-center text-left shadow-sm">
                                <span class="truncate" x-text="filterTanggal === 'all' ? 'Semua Tanggal' : formatDate(filterTanggal)"></span>
                                <svg :class="openTanggal ? 'rotate-0' : 'rotate-90'" class="w-3.5 h-3.5 transition-all duration-200 text-gray-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                            </button>
                            <div x-show="openTanggal" x-transition x-cloak class="absolute w-full mt-1 bg-white border border-gray-300 rounded-[5px] shadow-lg overflow-hidden py-1 z-50">
                                <label class="block px-3 py-2 text-[12px] hover:bg-gray-100 cursor-pointer text-black"><input type="radio" value="all" x-model="filterTanggal" class="hidden" @change="openTanggal = false; currentPage = 1">Semua Tanggal</label>
                                <template x-for="t in uniqueTanggals()" :key="t">
                                    <label class="block px-3 py-2 text-[12px] hover:bg-gray-100 cursor-pointer text-black"><input type="radio" :value="t" x-model="filterTanggal" class="hidden" @change="openTanggal = false; currentPage = 1"><span x-text="formatDate(t)"></span></label>
                                </template>
                            </div>
                        </div>

                        <!-- Waktu Filter Dropdown -->
                        <div x-data="{ openWaktu: false }" class="relative w-full sm:w-[130px] z-[50]" @click.outside="openWaktu = false">
                            <button type="button" @click="openWaktu = !openWaktu" class="w-full text-[12px] border border-gray-300 rounded-[5px] py-2 px-3 bg-white text-black font-medium focus:ring-[#4285F4] flex justify-between items-center text-left shadow-sm">
                                <span class="truncate" x-text="filterWaktu === 'all' ? 'Semua Waktu' : filterWaktu.substring(0,5) + ' WIB'"></span>
                                <svg :class="openWaktu ? 'rotate-0' : 'rotate-90'" class="w-3.5 h-3.5 transition-all duration-200 text-gray-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                            </button>
                            <div x-show="openWaktu" x-transition x-cloak class="absolute w-full mt-1 bg-white border border-gray-300 rounded-[5px] shadow-lg overflow-hidden py-1 z-50">
                                <label class="block px-3 py-2 text-[12px] hover:bg-gray-100 cursor-pointer text-black"><input type="radio" value="all" x-model="filterWaktu" class="hidden" @change="openWaktu = false; currentPage = 1">Semua Waktu</label>
                                <template x-for="w in uniqueWaktus()" :key="w">
                                    <label class="block px-3 py-2 text-[12px] hover:bg-gray-100 cursor-pointer text-black"><input type="radio" :value="w" x-model="filterWaktu" class="hidden" @change="openWaktu = false; currentPage = 1"><span x-text="w.substring(0,5) + ' WIB'"></span></label>
                                </template>
                            </div>
                        </div>

                        <!-- Ruangan Filter Dropdown -->
                        <div x-data="{ openRuang: false }" class="relative w-full sm:w-[130px] z-[40]" @click.outside="openRuang = false">
                            <button type="button" @click="openRuang = !openRuang" class="w-full text-[12px] border border-gray-300 rounded-[5px] py-2 px-3 bg-white text-black font-medium focus:ring-[#4285F4] flex justify-between items-center text-left shadow-sm">
                                <span class="truncate uppercase" x-text="filterRuang === 'all' ? 'Semua Ruangan' : filterRuang"></span>
                                <svg :class="openRuang ? 'rotate-0' : 'rotate-90'" class="w-3.5 h-3.5 transition-all duration-200 text-gray-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                            </button>
                            <div x-show="openRuang" x-transition x-cloak class="absolute w-full mt-1 bg-white border border-gray-300 rounded-[5px] shadow-lg overflow-hidden py-1 z-50 max-h-[200px] overflow-y-auto">
                                <label class="block px-3 py-2 text-[12px] hover:bg-gray-100 cursor-pointer text-black"><input type="radio" value="all" x-model="filterRuang" class="hidden" @change="openRuang = false; currentPage = 1">Semua Ruangan</label>
                                <template x-for="r in uniqueRuangs()" :key="r">
                                    <label class="block px-3 py-2 text-[12px] hover:bg-gray-100 cursor-pointer text-black uppercase"><input type="radio" :value="r" x-model="filterRuang" class="hidden" @change="openRuang = false; currentPage = 1"><span x-text="r"></span></label>
                                </template>
                            </div>
                        </div>

                        <button @click="clearFilters()" type="button"
                            class="bg-[#EA3323] hover:bg-red-700 text-white px-4 py-2 rounded-[5px] text-[12px] font-bold transition-colors shadow-sm shrink-0 whitespace-nowrap flex justify-center items-center">
                            Clear Filter
                        </button>
                    </div>
                </div>

                <!-- Tabel Data -->
                <div class="border border-gray-200 rounded-[10px] overflow-hidden">
                    <div class="overflow-x-auto custom-scrollbar">
                        <table class="w-full text-left text-[12px] font-medium text-black border-collapse min-w-[800px]">
                            <thead class="bg-[#EBEBEB]">
                                <tr class="h-[45px] text-black text-center">
                                    <th class="border-b border-r border-gray-300 font-bold px-4 w-[50px]">No</th>
                                    <th class="border-b border-r border-gray-300 font-bold px-4 text-left w-[200px]">Mahasiswa</th>
                                    <th class="border-b border-r border-gray-300 font-bold px-4 text-left">Judul KP</th>
                                    <th class="border-b border-r border-gray-300 font-bold px-4">Jadwal (WIB)</th>
                                    <th class="border-b border-r border-gray-300 font-bold px-4">Tempat</th>
                                    <th class="border-b border-r border-gray-300 font-bold px-4">Status</th>
                                    <th class="border-b border-gray-300 font-bold px-4 w-[90px]" x-show="!isSelectingMode">Aksi</th>
                                </tr>
                            </thead>
                    <tbody class="align-middle bg-white">
                        <template x-for="(row, index) in paginatedTerjadwal()" :key="row.id">
                            <tr class="hover:bg-blue-50 border-b border-[#CAC0C0] transition-colors"
                                :class="selectedIds.includes(row.id) ? 'bg-gray-100' : ''">
                                <td class="border-r border-[#CAC0C0] px-4 py-3 font-normal text-black text-center">
                                    <span x-show="!isSelectingMode"
                                        x-text="(currentPage - 1) * itemsPerPage + index + 1"
                                        class="text-black inline-block w-full"></span>

                                    <div x-show="isSelectingMode" class="flex justify-center cursor-pointer"
                                        @click="toggleSelect(row.id)">
                                        <div class="w-4 h-4 border border-gray-400 rounded transition-colors"
                                            :class="selectedIds.includes(row.id) ? 'bg-gray-700 border-gray-700 shadow-inner' : 'bg-white hover:bg-gray-100'">
                                        </div>
                                    </div>
                                </td>
                                <td class="border-r border-[#CAC0C0] px-4 py-3 text-left"
                                    @click="isSelectingMode ? toggleSelect(row.id) : null"
                                    :class="isSelectingMode ? 'cursor-pointer' : ''">
                                    <div class="font-bold text-[13px] text-black" x-text="row.nim"></div>
                                    <div class="text-[11px] text-gray-500 sentence-case leading-tight" x-text="row.name"></div>
                                </td>
                                <td @click="isSelectingMode ? toggleSelect(row.id) : null"
                                    :class="isSelectingMode ? 'cursor-pointer' : ''"
                                    class="border-r border-[#CAC0C0] px-4 py-3 text-left text-black break-words max-w-[200px] font-normal">
                                    <div class="line-clamp-2 leading-snug sentence-case" x-text="row.judul" :title="row.judul"></div>
                                </td>
                                <td class="border-r border-[#CAC0C0] px-4 py-3"
                                    @click="isSelectingMode ? toggleSelect(row.id) : null"
                                    :class="isSelectingMode ? 'cursor-pointer' : ''">
                                    <div class="font-normal text-black" x-text="formatDate(row.tanggal)"></div>
                                    <div class="font-normal text-[11px] text-black mt-0.5"><span
                                            x-text="row.mulai.substring(0,5)"></span> - <span
                                            x-text="row.selesai.substring(0,5)"></span></div>
                                </td>
                                <td class="border-r border-[#CAC0C0] px-4 py-3 font-normal text-black text-center"
                                    @click="isSelectingMode ? toggleSelect(row.id) : null"
                                    :class="isSelectingMode ? 'cursor-pointer' : ''">
                                    <span class="sentence-case inline-block" x-text="row.ruang || '-'"></span>
                                </td>
                                <td class="border-r border-[#CAC0C0] px-4 py-3 text-center"
                                    @click="isSelectingMode ? toggleSelect(row.id) : null"
                                    :class="isSelectingMode ? 'cursor-pointer' : ''">
                                    <div x-show="row.status === 'submitted'"
                                        class="inline-flex items-center justify-center gap-1.5 bg-[#A1DFAC] text-[#1D5E2D] px-3 py-1 rounded-[20px] shadow-sm font-normal w-[90px] text-[10px]">
                                        <div class="w-1.5 h-1.5 rounded-full bg-[#1D5E2D]"></div>
                                        Publish
                                    </div>
                                    <div x-show="row.status === 'draft'"
                                        class="inline-flex items-center justify-center gap-1.5 bg-[#FDE293] text-[#A67C00] px-3 py-1 rounded-[20px] shadow-sm font-normal w-[90px] text-[10px]"
                                        style="display: none;">
                                        <div class="w-1.5 h-1.5 rounded-full bg-[#A67C00]"></div>
                                        Draft
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-center" x-show="!isSelectingMode">
                                    <div class="flex justify-center gap-2">
                                        <button type="button" @click="editSchedule(row.id)" title="Edit"
                                            class="text-blue-600 hover:text-blue-800 bg-blue-50 hover:bg-blue-100 p-1.5 rounded shrink-0 transition-colors">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z">
                                                </path>
                                            </svg>
                                        </button>
                                        <button type="button" @click="promptDeleteSchedule(row.id)"
                                            title="Hapus (Kembali ke Antrean)"
                                            class="text-red-500 hover:text-red-700 bg-red-50 hover:bg-red-100 p-1.5 rounded shrink-0 transition-colors">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                                </path>
                                            </svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </template>
                        <template x-if="filteredTerjadwal().length === 0">
                            <tr>
                                <td :colspan="isSelectingMode ? 6 : 7"
                                    class="border-t border-gray-200 px-4 py-16 text-center bg-gray-50 border-b-0">
                                    <div class="flex flex-col items-center justify-center text-gray-400">
                                        <svg class="w-10 h-10 mb-2 opacity-60" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        <p class="text-[12px] font-bold text-gray-400 tracking-widest uppercase"
                                            x-text="terjadwal.length === 0 ? 'Belum ada Tabel Terjadwal' : 'Tidak ada hasil yang sesuai filter'">
                                        </p>
                                    </div>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
                </div>
            </div>

            <!-- Standardized Pagination -->
            <div class="pt-4 border-t border-gray-200 flex flex-col sm:flex-row items-center justify-between gap-4 mt-6">
                <span class="text-[12px] font-medium text-black/50" x-text="(filteredTerjadwal().length > 0 ? ((currentPage - 1) * itemsPerPage + 1) : 0) + ' - ' + Math.min(currentPage * itemsPerPage, filteredTerjadwal().length) + ' dari ' + filteredTerjadwal().length + ' baris'"></span>

                <div class="flex items-center gap-2" x-show="totalPages() > 1">
                    <button @click="if(currentPage > 1) currentPage--"
                        :disabled="currentPage === 1"
                        class="px-3 py-1 border border-gray-300 rounded text-[12px] hover:bg-gray-50 disabled:opacity-30 disabled:cursor-not-allowed transition-colors">
                        Previous
                    </button>
                    
                    <div class="flex items-center gap-1">
                        <template x-for="page in totalPages()" :key="page">
                            <button @click="currentPage = page"
                                :class="currentPage === page ? 'bg-blue-600 text-white shadow-md' : 'text-black hover:bg-gray-100'"
                                class="w-8 h-8 rounded text-[12px] font-bold transition-all"
                                x-text="page">
                            </button>
                        </template>
                    </div>

                    <button @click="if(currentPage < totalPages()) currentPage++"
                        :disabled="currentPage === totalPages()"
                        class="px-3 py-1 border border-gray-300 rounded text-[12px] hover:bg-gray-50 disabled:opacity-30 disabled:cursor-not-allowed transition-colors">
                        Next
                    </button>
                </div>
            </div>
        </div>

        </div> <!--/ x-data -->

</x-dashboard-layout>