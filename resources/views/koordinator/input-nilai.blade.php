<x-dashboard-layout header="Input Nilai Sidang" userName="{{ auth()->user()->name }}" roleName="KOORDINATOR KP">
    <x-slot:sidebar>
        @include('koordinator.components.sidebar', ['active' => 'input-nilai'])
        </x-slot>

        <div x-data="inputNilaiPage()" class="p-6">
            <!-- Dashboard Header Info -->
            <div class="mb-6">
                <div
                    class="bg-[#E6F0FA] border border-[#D0E3F5] rounded-[10px] p-4 lg:p-5 flex items-start gap-4 shadow-sm w-full">
                    <div
                        class="w-6 h-6 rounded-full bg-[#4285F4] text-white flex items-center justify-center font-bold flex-shrink-0 mt-0.5">
                        i</div>
                    <p class="text-[14px] text-black font-normal leading-relaxed m-0 mt-0.5">
                        Halaman pemantauan dan pengisian nilai sidang. <br>
                        Data dipisahkan berdasarkan peran Anda sebagai dosen. Koordinator hanya mengelola status jika
                        menjabat sebagai Penguji 1.
                    </p>
                </div>
            </div>

            <!-- Filter & Search Bar -->
            <div
                class="bg-white p-3 rounded-[5px] shadow-sm border border-[#CAC0C0] mb-8 flex flex-wrap gap-3 items-center">
                <div class="relative flex-1 min-w-[300px]">
                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </span>
                    <input type="text" x-model="search" placeholder="Cari nama, NIM, atau judul KP..."
                        class="w-full pl-10 pr-4 py-2 border border-[#CAC0C0] rounded-[5px] text-[13px] focus:outline-none focus:border-[#4CC098]">
                </div>

                <div class="flex gap-2 items-center">
                    <span class="text-[13px] font-bold text-black">Urutkan:</span>
                    <select x-model="sortBy"
                        class="border border-[#CAC0C0] rounded-[5px] px-3 py-2 text-[13px] focus:outline-none focus:border-[#4CC098] bg-white cursor-pointer font-medium">
                        <option value="date_near">Tanggal (Terdekat)</option>
                        <option value="date_far">Tanggal (Terjauh)</option>
                        <option value="name_asc">Nama (A-Z)</option>
                        <option value="name_desc">Nama (Z-A)</option>
                        <option value="nim_asc">NIM (Terkecil)</option>
                        <option value="nim_desc">NIM (Terbesar)</option>
                        <option value="time_asc">Jam (Terkecil)</option>
                        <option value="time_desc">Jam (Terbesar)</option>
                    </select>
                </div>
            </div>

            <!-- SECTION 1: INPUT NILAI SIDANG (PENGUJI) -->
            <div class="mb-10">
                <h2 class="text-[16px] font-bold text-black mb-4 flex items-center gap-2">
                    <span class="w-1.5 h-6 bg-[#4285F4] rounded-full"></span>
                    Input Nilai Sidang (Penguji)
                </h2>
                <div class="bg-[#F9F9F9] border border-[#CAC0C0] rounded-t-[10px] overflow-hidden shadow-sm">
                    <div class="overflow-x-auto">
                        <table class="w-full text-center border-collapse text-[12px] min-w-[1000px]">
                            <thead class="bg-[#E0DFDF] font-bold text-black border-b border-[#CAC0C0] h-[45px]">
                                <tr>
                                    <th class="border-r border-[#CAC0C0] px-3 py-2 w-[50px]">No</th>
                                    <th class="border-r border-[#CAC0C0] px-4 py-2 text-left w-[180px]">Jadwal</th>
                                    <th class="border-r border-[#CAC0C0] px-4 py-2 text-left w-[150px]">Peran Sidang
                                    </th>
                                    <th class="border-r border-[#CAC0C0] px-4 py-2 text-left w-[180px]">Mahasiswa</th>
                                    <th class="border-r border-[#CAC0C0] px-4 py-2 text-left">Judul KP</th>
                                    <th class="border-r border-[#CAC0C0] px-4 py-2 w-[140px]">Pelaksanaan</th>
                                    <th class="px-4 py-2 w-[150px]">Penilaian</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white">
                                <template x-for="(sidang, index) in filteredPenguji" :key="'p-' + sidang.id">
                                    <tr class="border-b border-[#CAC0C0] hover:bg-gray-50 transition-colors">
                                        <td class="border-r border-[#CAC0C0] px-3 py-4 text-gray-700"
                                            x-text="index + 1"></td>
                                        <td class="border-r border-[#CAC0C0] px-4 py-4 text-left">
                                            <div class="font-bold text-black uppercase"
                                                x-text="formatDate(sidang.tanggal_sidang)"></div>
                                            <div class="text-gray-600 mt-1"
                                                x-text="formatTime(sidang.waktu_mulai_sidang) + ' - ' + formatTime(sidang.waktu_selesai_sidang) + ' WIB'">
                                            </div>
                                            <div class="text-gray-400 italic mt-1" x-text="sidang.ruang_sidang || '-'">
                                            </div>
                                        </td>
                                        <td class="border-r border-[#CAC0C0] px-4 py-4 text-left">
                                            <div class="flex flex-col gap-1">
                                                <template
                                                    x-for="role in getSpecificRoles(sidang, ['PENGUJI 1', 'PENGUJI 2'])">
                                                    <span
                                                        class="text-[10px] font-bold bg-[#F0F0F0] text-gray-600 px-2 py-0.5 rounded-[3px] border border-gray-200"
                                                        x-text="role"></span>
                                                </template>
                                            </div>
                                        </td>
                                        <td class="border-r border-[#CAC0C0] px-4 py-4 text-left">
                                            <div class="font-bold text-black uppercase"
                                                x-text="sidang.mahasiswa.user.name"></div>
                                            <div class="text-gray-500 font-mono text-[11px]"
                                                x-text="sidang.mahasiswa.nim"></div>
                                        </td>
                                        <td class="border-r border-[#CAC0C0] px-4 py-4 text-left">
                                            <p class="sentence-case leading-snug line-clamp-2 text-black font-normal"
                                                x-text="sidang.pendaftaran_kp.judul_kp"
                                                :title="sidang.pendaftaran_kp.judul_kp"></p>
                                        </td>
                                        <td class="border-r border-[#CAC0C0] px-4 py-4">
                                            <div class="flex flex-col items-center gap-2">
                                                <div class="text-[10px] font-bold px-2 py-1 rounded-[20px] shadow-sm flex items-center gap-1.5 min-w-[90px] justify-center"
                                                    :class="getStatusClass(sidang)">
                                                    <div class="w-1.5 h-1.5 rounded-full"
                                                        :class="getStatusDotClass(sidang)"></div>
                                                    <span x-text="getExecutionStatus(sidang)"></span>
                                                </div>
                                                <template x-if="sidang.is_penguji_1">
                                                    <select @change="updateStatus(sidang.id, $event.target.value)"
                                                        class="border border-[#CAC0C0] rounded-[5px] px-1.5 py-1 text-[11px] focus:outline-none focus:border-[#4CC098] w-full mt-1 bg-white cursor-pointer shadow-none">
                                                        <option value="Menunggu"
                                                            :selected="sidang.pelaksanaan === 'Menunggu'">Atur Menunggu
                                                        </option>
                                                        <option value="Berjalan"
                                                            :selected="sidang.pelaksanaan === 'Berjalan'">Atur Berjalan
                                                        </option>
                                                        <option value="Selesai"
                                                            :selected="sidang.pelaksanaan === 'Selesai'">Set Selesai
                                                        </option>
                                                        <option value="Dibatalkan"
                                                            :selected="sidang.pelaksanaan === 'Dibatalkan'">Set Batal
                                                        </option>
                                                    </select>
                                                </template>
                                                <template x-if="!sidang.is_penguji_1">
                                                    <div
                                                        class="text-[10px] text-gray-400 italic font-medium mt-1 text-center">
                                                        Otoritas Penguji 1</div>
                                                </template>
                                            </div>
                                        </td>
                                        <td class="px-4 py-4">
                                            <div class="flex flex-col gap-2">
                                                <template
                                                    x-for="role in getSpecificRoles(sidang, ['PENGUJI 1', 'PENGUJI 2'])">
                                                    <a :href="'{{ url('koordinator/input-nilai') }}/' + sidang.id + '/' + role.toLowerCase().replace(' ', '')"
                                                        class="w-full text-center bg-[#4CC098] hover:bg-[#3da681] text-white py-1.5 rounded-[4px] text-[10px] font-bold transition-all shadow-sm flex items-center justify-center gap-1">
                                                        INPUT <span x-text="role"></span>
                                                    </a>
                                                </template>
                                            </div>
                                        </td>
                                    </tr>
                                </template>
                                <template x-if="filteredPenguji.length === 0">
                                    <tr>
                                        <td colspan="7" class="py-12 text-center text-gray-500 italic text-[13px]">
                                            Tidak ada data penguji yang sesuai.
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- SECTION 2: INPUT NILAI PEMBIMBING -->
            <div class="mb-10">
                <h2 class="text-[16px] font-bold text-black mb-4 flex items-center gap-2">
                    <span class="w-1.5 h-6 bg-[#34A853] rounded-full"></span>
                    Input Nilai Pembimbing
                </h2>
                <div class="bg-[#F9F9F9] border border-[#CAC0C0] rounded-t-[10px] overflow-hidden shadow-sm">
                    <div class="overflow-x-auto">
                        <table class="w-full text-center border-collapse text-[12px] min-w-[800px]">
                            <thead class="bg-[#E0DFDF] font-bold text-black border-b border-[#CAC0C0] h-[45px]">
                                <tr>
                                    <th class="border-r border-[#CAC0C0] px-3 py-2 w-[50px]">No</th>
                                    <th class="border-r border-[#CAC0C0] px-4 py-2 text-left w-[250px]">Mahasiswa</th>
                                    <th class="border-r border-[#CAC0C0] px-4 py-2 text-left">Judul KP</th>
                                    <th class="px-4 py-2 w-[200px]">Penilaian</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white">
                                <template x-for="(sidang, index) in filteredPembimbing" :key="'pb-' + sidang.id">
                                    <tr class="border-b border-[#CAC0C0] hover:bg-gray-50 transition-colors">
                                        <td class="border-r border-[#CAC0C0] px-3 py-4 text-gray-700"
                                            x-text="index + 1"></td>
                                        <td class="border-r border-[#CAC0C0] px-4 py-4 text-left">
                                            <div class="font-bold text-black uppercase"
                                                x-text="sidang.mahasiswa.user.name"></div>
                                            <div class="text-gray-500 font-mono text-[11px]"
                                                x-text="sidang.mahasiswa.nim"></div>
                                        </td>
                                        <td class="border-r border-[#CAC0C0] px-4 py-4 text-left">
                                            <p class="sentence-case leading-snug line-clamp-2 text-black font-normal"
                                                x-text="sidang.pendaftaran_kp.judul_kp"
                                                :title="sidang.pendaftaran_kp.judul_kp"></p>
                                        </td>
                                        <td class="px-4 py-4">
                                            <a :href="'{{ url('koordinator/input-nilai') }}/' + sidang.id + '/pembimbing'"
                                                class="w-full text-center bg-[#4CC098] hover:bg-[#3da681] text-white py-2 rounded-[4px] text-[11px] font-bold transition-all shadow-sm flex items-center justify-center gap-1 uppercase">
                                                Input Nilai Pembimbing
                                            </a>
                                        </td>
                                    </tr>
                                </template>
                                <template x-if="filteredPembimbing.length === 0">
                                    <tr>
                                        <td colspan="4" class="py-12 text-center text-gray-500 italic text-[13px]">
                                            Tidak ada data pembimbing yang sesuai.
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- SECTION 3: INPUT NILAI SUPERVISOR -->
            <div class="mb-10">
                <h2 class="text-[16px] font-bold text-black mb-4 flex items-center gap-2">
                    <span class="w-1.5 h-6 bg-[#FBBC05] rounded-full"></span>
                    Input Nilai Supervisior
                </h2>
                <div class="bg-[#F9F9F9] border border-[#CAC0C0] rounded-t-[10px] overflow-hidden shadow-sm">
                    <div class="overflow-x-auto">
                        <table class="w-full text-center border-collapse text-[12px] min-w-[800px]">
                            <thead class="bg-[#E0DFDF] font-bold text-black border-b border-[#CAC0C0] h-[45px]">
                                <tr>
                                    <th class="border-r border-[#CAC0C0] px-3 py-2 w-[50px]">No</th>
                                    <th class="border-r border-[#CAC0C0] px-4 py-2 text-left w-[250px]">Mahasiswa</th>
                                    <th class="border-r border-[#CAC0C0] px-4 py-2 text-left">Judul KP</th>
                                    <th class="px-4 py-2 w-[200px]">Penilaian</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white">
                                <template x-for="(sidang, index) in filteredSupervisior" :key="'sv-' + sidang.id">
                                    <tr class="border-b border-[#CAC0C0] hover:bg-gray-50 transition-colors">
                                        <td class="border-r border-[#CAC0C0] px-3 py-4 text-gray-700"
                                            x-text="index + 1"></td>
                                        <td class="border-r border-[#CAC0C0] px-4 py-4 text-left">
                                            <div class="font-bold text-black uppercase"
                                                x-text="sidang.mahasiswa.user.name"></div>
                                            <div class="text-gray-500 font-mono text-[11px]"
                                                x-text="sidang.mahasiswa.nim"></div>
                                        </td>
                                        <td class="border-r border-[#CAC0C0] px-4 py-4 text-left">
                                            <p class="sentence-case leading-snug line-clamp-2 text-black font-normal"
                                                x-text="sidang.pendaftaran_kp.judul_kp"
                                                :title="sidang.pendaftaran_kp.judul_kp"></p>
                                        </td>
                                        <td class="px-4 py-4">
                                            <a :href="'{{ url('koordinator/input-nilai') }}/' + sidang.id + '/supervisior'"
                                                class="w-full text-center bg-[#4CC098] hover:bg-[#3da681] text-white py-2 rounded-[4px] text-[11px] font-bold transition-all shadow-sm flex items-center justify-center gap-1 uppercase">
                                                Input Nilai Supervisior
                                            </a>
                                        </td>
                                    </tr>
                                </template>
                                <template x-if="filteredSupervisior.length === 0">
                                    <tr>
                                        <td colspan="4" class="py-12 text-center text-gray-500 italic text-[13px]">
                                            Tidak ada data supervisior yang sesuai.
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <script>
            function inputNilaiPage() {
                return {
                    sidangs: @json($sidangs),
                    search: '',
                    sortBy: 'date_near',
                    now: new Date(),

                    init() {
                        setInterval(() => {
                            this.now = new Date();
                        }, 60000);
                    },

                    get baseFiltered() {
                        let result = [...this.sidangs];
                        if (this.search) {
                            const q = this.search.toLowerCase();
                            result = result.filter(s =>
                                s.mahasiswa.user.name.toLowerCase().includes(q) ||
                                s.mahasiswa.nim.includes(q) ||
                                (s.pendaftaran_kp.judul_kp && s.pendaftaran_kp.judul_kp.toLowerCase().includes(q))
                            );
                        }
                        result.sort((a, b) => {
                            switch (this.sortBy) {
                                case 'date_near': return new Date(a.tanggal_sidang) - new Date(b.tanggal_sidang);
                                case 'date_far': return new Date(b.tanggal_sidang) - new Date(a.tanggal_sidang);
                                case 'name_asc': return a.mahasiswa.user.name.localeCompare(b.mahasiswa.user.name);
                                case 'name_desc': return b.mahasiswa.user.name.localeCompare(a.mahasiswa.user.name);
                                case 'nim_asc': return a.mahasiswa.nim.localeCompare(b.mahasiswa.nim);
                                case 'nim_desc': return b.mahasiswa.nim.localeCompare(a.mahasiswa.nim);
                                case 'time_asc': return a.waktu_mulai_sidang.localeCompare(b.waktu_mulai_sidang);
                                case 'time_desc': return b.waktu_mulai_sidang.localeCompare(a.waktu_mulai_sidang);
                                default: return 0;
                            }
                        });
                        return result;
                    },

                    get filteredPenguji() {
                        return this.baseFiltered.filter(s =>
                            s.user_roles.includes('PENGUJI 1') || s.user_roles.includes('PENGUJI 2')
                        );
                    },

                    get filteredPembimbing() {
                        return this.baseFiltered.filter(s => s.user_roles.includes('PEMBIMBING'));
                    },

                    get filteredSupervisior() {
                        return this.baseFiltered.filter(s => s.user_roles.includes('SUPERVISIOR'));
                    },

                    getSpecificRoles(sidang, rolesToMatch) {
                        return sidang.user_roles.filter(r => rolesToMatch.includes(r));
                    },

                    getExecutionStatus(s) {
                        if (s.pelaksanaan === 'Selesai') return 'Selesai';
                        if (s.pelaksanaan === 'Dibatalkan') return 'Dibatalkan';
                        const start = new Date(`${s.tanggal_sidang}T${s.waktu_mulai_sidang}`);
                        const end = new Date(`${s.tanggal_sidang}T${s.waktu_selesai_sidang}`);
                        if (this.now < start) return 'Menunggu';
                        if (this.now >= start && this.now <= end) return 'Berjalan';
                        return s.pelaksanaan;
                    },

                    getStatusClass(s) {
                        const status = this.getExecutionStatus(s);
                        if (status === 'Menunggu') return 'bg-[#F9F9F9] text-gray-500 border border-gray-300';
                        if (status === 'Berjalan') return 'bg-[#DEF1FF] text-[#1D4ED8] border border-[#BFDBFE]';
                        if (status === 'Selesai') return 'bg-[#A1DFAC] text-[#1D5E2D] border border-[#BBF7D0]';
                        if (status === 'Dibatalkan') return 'bg-[#FFD3D3] text-[#B91C1C] border border-[#FECACA]';
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

                    formatDate(dateString) {
                        if (!dateString) return '-';
                        return new Date(dateString).toLocaleDateString('id-ID', { weekday: 'long', day: 'numeric', month: 'short', year: 'numeric' });
                    },

                    formatTime(timeString) {
                        if (!timeString) return '-';
                        return timeString.substring(0, 5);
                    },

                    async updateStatus(id, newStatus) {
                        try {
                            const response = await fetch(`{{ url('koordinator/input-nilai') }}/${id}/status`, {
                                method: 'POST',
                                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                                body: JSON.stringify({ pelaksanaan: newStatus })
                            });
                            const res = await response.json();
                            if (res.success) {
                                const idx = this.sidangs.findIndex(s => s.id === id);
                                if (idx !== -1) {
                                    this.sidangs[idx].pelaksanaan = newStatus;
                                    this.now = new Date();
                                }
                            } else { alert(res.message); }
                        } catch (e) { alert('Gagal memperbarui status.'); }
                    }
                }
            }
        </script>

        <style>
            .sentence-case {
                text-transform: lowercase;
            }

            .sentence-case::first-letter {
                text-transform: uppercase;
            }

            [x-cloak] {
                display: none !important;
            }
        </style>
</x-dashboard-layout>