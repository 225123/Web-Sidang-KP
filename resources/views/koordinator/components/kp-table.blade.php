@props(['pendaftarans', 'title', 'isRejected' => false])

<div class="mb-5 mt-10">
    <h3 class="text-[16px] font-bold text-gray-800 mb-2 border-b border-gray-300 pb-2 flex items-center gap-2">
        @if($isRejected)
            <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
        @else
            <svg class="w-5 h-5 text-[#4285F4]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
        @endif
        {{ $title }}
    </h3>
</div>

<div class="rounded-t-[10px] mb-4 shadow-sm border border-[#CAC0C0]"
    x-data="{
        currentPage: 1,
        perPage: 10,
        totalVisible: {{ count($pendaftarans) }},
        jenisFilter: 'All',
        statusFilter: 'All',
        pengerjaanFilter: 'All',
        init() {
            let prefix = '{{ $isRejected ? "rejected" : "main" }}';
            window.addEventListener('update-jenis-' + prefix, e => { this.jenisFilter = e.detail; this.currentPage = 1; this.updateTable(); });
            window.addEventListener('update-status-' + prefix, e => { this.statusFilter = e.detail; this.currentPage = 1; this.updateTable(); });
            window.addEventListener('update-pengerjaan-' + prefix, e => { this.pengerjaanFilter = e.detail; this.currentPage = 1; this.updateTable(); });
            window.addEventListener('clear-filters-' + prefix, e => {
                this.jenisFilter = 'All'; this.statusFilter = 'All'; this.pengerjaanFilter = 'All';
                this.currentPage = 1; this.updateTable();
            });

            this.$watch('{{ $searchModel ?? "searchQuery" }}', value => {
                this.currentPage = 1;
                this.updateTable();
            });
            this.$nextTick(() => this.updateTable());
        },
        updateTable() {
            let query = (this.{{ $searchModel ?? "searchQuery" }} || '').toLowerCase();
            let groups = Array.from(this.$refs.tableRoot.querySelectorAll('tbody.proposal-group'));
            let visibleGroups = [];
            
            groups.forEach(g => {
                let matchSearch = (query === '' || g.dataset.search.includes(query));
                let matchJenis = (this.jenisFilter === 'All' || g.dataset.jenis.toLowerCase() === this.jenisFilter.toLowerCase());
                
                let checkStatus = this.statusFilter === 'Belum Diperiksa' ? 'pending' : (this.statusFilter === 'Disetujui' ? 'approved' : '');
                let matchStatus = (this.statusFilter === 'All' || g.dataset.status.toLowerCase() === checkStatus);
                
                let checkPengerjaan = this.pengerjaanFilter.toLowerCase();
                let matchPengerjaan = (this.pengerjaanFilter === 'All' || g.dataset.pengerjaan.toLowerCase().includes(checkPengerjaan));

                if (matchSearch && matchJenis && matchStatus && matchPengerjaan) {
                    visibleGroups.push(g);
                } else {
                    g.style.display = 'none';
                }
            });
            
            this.totalVisible = visibleGroups.length;
            let start = (this.currentPage - 1) * this.perPage;
            let end = start + this.perPage;
            
            let studentCounter = 1;
            visibleGroups.forEach((g, idx) => {
                let studentRowsCount = g.querySelectorAll('.row-counter').length;
                
                if (idx >= start && idx < end) {
                    g.style.display = '';
                    let counters = g.querySelectorAll('.row-counter');
                    counters.forEach(c => {
                        c.innerText = studentCounter++;
                    });
                } else {
                    g.style.display = 'none';
                    studentCounter += studentRowsCount;
                }
            });
        },
        get totalPages() { return Math.max(1, Math.ceil(this.totalVisible / this.perPage)); },
        get startItem() { return this.totalVisible === 0 ? 0 : (this.currentPage - 1) * this.perPage + 1; },
        get endItem() { return Math.min(this.currentPage * this.perPage, this.totalVisible); },
        nextPage() { if (this.currentPage < this.totalPages) { this.currentPage++; this.updateTable(); window.scrollTo(0, this.$el.offsetTop - 100); } },
        prevPage() { if (this.currentPage > 1) { this.currentPage--; this.updateTable(); window.scrollTo(0, this.$el.offsetTop - 100); } },
        goToPage(p) { this.currentPage = p; this.updateTable(); window.scrollTo(0, this.$el.offsetTop - 100); }
    }"
>
    <!-- Wrapper with conditional background only for scrolling boundary horizontally -->
    <div class="overflow-x-auto {{ $isRejected ? 'bg-[#FFF5F5]' : 'bg-[#F9F9F9]' }} rounded-t-[10px]">
        <table class="w-full min-w-[1000px] text-left border-collapse text-[12px] text-center" x-ref="tableRoot">
            <thead class="bg-[#E0DFDF] font-bold text-black h-[40px]">
                <tr>
                    <th class="border-b border-[#CAC0C0] px-4 py-2 w-[40px]">No</th>
                    <th class="border-b border-[#CAC0C0] px-4 py-2">Mahasiswa</th>
                    <th class="border-b border-[#CAC0C0] px-4 py-2">Pengerjaan</th>
                    <th class="border-b border-[#CAC0C0] px-4 py-2">Jenis KP</th>
                    <th class="border-b border-[#CAC0C0] px-4 py-2">Nama Instansi</th>
                    <th class="border-b border-[#CAC0C0] px-4 py-2">Supervisor</th>
                    <th class="border-b border-[#CAC0C0] px-4 py-2">Judul KP</th>
                    <th class="border-b border-[#CAC0C0] px-4 py-2 {{ $isRejected ? 'min-w-[150px]' : 'min-w-[200px]' }}">
                        {{ $isRejected ? 'Tanggal Penolakan' : 'Status Approval' }}
                    </th>
                    <th class="border-b border-[#CAC0C0] px-4 py-2 w-[80px]">Detail KP</th>
                </tr>
            </thead>

            @forelse($pendaftarans as $index => $kp)
                @php 
                    $mhsList = $kp->mahasiswaList ?? [['nama' => $kp->user->name ?? 'Unknown', 'nim' => $kp->user->mahasiswa->nim ?? 'Unknown']];
                    $rowspan = count($mhsList);
                    $allNamesNims = '';
                    foreach($mhsList as $member) {
                        $allNamesNims .= strtolower(($member['nama'] ?? '') . ' ' . ($member['nim'] ?? '')) . ' ';
                    }
                    $rowSearchString = $allNamesNims . strtolower(($kp->judul_kp ?? '') . ' ' . ($kp->instansi_nama ?? ''));
                @endphp
                <tbody class="proposal-group transition-colors duration-150" 
                    data-search="{{ $rowSearchString }}" 
                    data-jenis="{{ $kp->jenis_instansi ?? '' }}"
                    data-status="{{ $kp->status_kp ?? '' }}"
                    data-pengerjaan="{{ $kp->pengerjaan_kp ?? 'individu' }}"
                    style="display: none;">
                    @foreach($mhsList as $mIdx => $m)
                    <tr class="bg-white hover:bg-gray-50 font-medium border-b border-[#CAC0C0]">
                        
                        <td class="border-r border-[#CAC0C0] px-4 py-4 text-center align-middle">
                            <span x-show="!isSelectionMode" class="text-gray-700 font-bold row-counter"></span>
                            @if($mIdx === 0)
                                <div x-show="isSelectionMode" style="display: none;">
                                    @if($kp->status_kp === 'pending')
                                        <input type="checkbox" name="selected_ids[]" value="{{ $kp->id }}" class="rounded shadow-sm border-[#CAC0C0] focus:ring-[#4CC098] cursor-pointer w-4 h-4 text-[#4CC098]">
                                    @else
                                        <input type="checkbox" disabled class="rounded shadow-sm border-gray-200 bg-gray-100 cursor-not-allowed opacity-50 w-4 h-4">
                                    @endif
                                </div>
                            @endif
                        </td>

                        <td class="border-r border-[#CAC0C0] px-4 py-2 leading-tight">
                            <div class="flex items-center gap-2 text-left">
                                <div class="w-8 h-8 rounded-full bg-[#E6F0FA] text-[#4285F4] flex items-center justify-center font-bold text-[13px] border border-[#D0E3F5] flex-shrink-0">
                                    {{ strtoupper(substr($m['nama'] ?? 'U', 0, 1)) }}
                                </div>
                                <div>
                                    <div class="font-bold text-[12px] text-gray-800">{{ $m['nama'] }}</div>
                                    <div class="text-[11px] text-gray-500 font-medium mt-0.5">{{ $m['nim'] }}</div>
                                </div>
                            </div>
                        </td>
                        
                        @if($mIdx === 0)
                        <td rowspan="{{ $rowspan }}" class="border-r border-[#CAC0C0] px-4 py-2 text-center align-middle">
                            <div class="inline-flex py-1 px-3 rounded-[5px] text-[12px] font-bold shadow-sm whitespace-nowrap {{ ($kp->pengerjaan_kp ?? '') == 'berkelompok' || ($kp->pengerjaan_kp ?? '') == 'kelompok' ? 'bg-[#FFF3E0] text-[#E65100]' : 'bg-[#E3F2FD] text-[#0D47A1]' }}">
                                {{ ucfirst($kp->pengerjaan_kp ?? 'Individu') }}
                            </div>
                        </td>
                        <td rowspan="{{ $rowspan }}" class="border-r border-[#CAC0C0] px-4 py-2 text-center align-middle">{{ $kp->jenis_instansi }}</td>
                        <td rowspan="{{ $rowspan }}" class="border-r border-[#CAC0C0] px-4 py-2 align-middle text-center">{{ $kp->instansi_nama ?? '-' }}</td>
                        <td rowspan="{{ $rowspan }}" class="border-r border-[#CAC0C0] px-4 py-2 align-middle text-center">{{ $kp->supervisorInstansi->nama_supervisor ?? '-' }}</td>
                        <td rowspan="{{ $rowspan }}" class="border-r border-[#CAC0C0] px-4 py-2 max-w-[200px] break-words leading-tight align-middle text-center" title="{{ $kp->judul_kp ?? '-' }}">
                            {{ Str::limit($kp->judul_kp ?? '-', 50) }}
                        </td>
                        <td rowspan="{{ $rowspan }}" class="border-r border-[#CAC0C0] px-4 py-2 text-center align-middle">
                            @if($isRejected)
                                <div class="font-bold text-[12px] text-gray-700 whitespace-nowrap">{{ $kp->updated_at ? $kp->updated_at->format('d M Y') : '-' }}</div>
                            @else
                                @if($kp->status_kp === 'pending')
                                    <div x-show="!isSelectionMode" class="flex items-center justify-center gap-2">
                                        <form method="POST" action="{{ route('koordinator.pendaftaran-kp.status', $kp->id) }}">
                                            @csrf
                                            @method('PUT')
                                            <input type="hidden" name="status" value="rejected">
                                            <button type="button" @click="openModalCatatan($el.closest('form'), 'Tolak Pendaftaran KP?')" class="bg-[#EA4335] text-white px-3 py-1 rounded-[20px] shadow-sm flex items-center justify-center gap-1 w-[80px] hover:bg-red-600 transition">
                                                <svg class="w-3 h-3 rounded-full border border-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                                Tolak
                                            </button>
                                        </form>
                                        <form method="POST" action="{{ route('koordinator.pendaftaran-kp.status', $kp->id) }}">
                                            @csrf
                                            @method('PUT')
                                            <input type="hidden" name="status" value="approved">
                                            <button type="button" @click="openModalCatatan($el.closest('form'), 'Sahkan Pendaftaran KP?')" class="bg-[#34A853] text-white px-3 py-1 rounded-[20px] shadow-sm flex items-center justify-center gap-1 w-[80px] hover:bg-green-600 transition">
                                                <svg class="w-3 h-3 rounded-full border border-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                                Sahkan
                                            </button>
                                        </form>
                                    </div>
                                    <div x-show="isSelectionMode" style="display: none;" class="flex items-center justify-center">
                                        <div class="inline-flex items-center justify-center bg-[#FDE293] text-[#A67C00] border border-[#FDE293] px-6 py-1.5 rounded-[20px] font-bold w-[120px] shadow-sm text-[11px] cursor-not-allowed">
                                            Menunggu
                                        </div>
                                    </div>
                                @elseif($kp->status_kp === 'approved')
                                    <div x-data="{ open: false }" class="relative flex items-center justify-center w-full">
                                        <button type="button" @click="open = !open" @click.outside="open = false" class="inline-flex items-center justify-center bg-[#A1DFAC] text-[#1D5E2D] px-6 py-1.5 rounded-[20px] font-bold w-[120px] shadow-sm text-[11px] hover:bg-green-300 transition cursor-pointer">
                                            Disetujui <svg class="w-3 h-3 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                        </button>
                                        <div x-show="open" style="display: none;" class="absolute top-full mt-1 left-1/2 -translate-x-1/2 bg-white border border-gray-200 shadow-lg rounded-[8px] overflow-hidden z-20 w-[120px]">
                                            <form method="POST" action="{{ route('koordinator.pendaftaran-kp.status', $kp->id) }}">
                                                @csrf
                                                @method('PUT')
                                                <input type="hidden" name="status" value="rejected">
                                                <button type="button" class="w-full text-left px-4 py-2 hover:bg-gray-100 text-[11px] font-bold text-[#EA4335]" @click="openModalCatatan($el.closest('form'), 'Ubah Status ke Ditolak')">
                                                    Ubah: Tolak
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                @endif
                            @endif
                        </td>
                        <td rowspan="{{ $rowspan }}" class="px-4 py-2 text-center align-middle">
                            <div class="flex justify-center w-full">
                                <a href="{{ route('koordinator.pendaftaran-kp.show', \Str::slug(($kp->user->name ?? 'user') . '-' . ($kp->user->mahasiswa->nim ?? '000'))) }}" class="inline-block bg-[#4285F4] hover:bg-blue-600 text-white px-4 py-1.5 rounded-[20px] shadow-sm text-[11px] font-semibold transition-colors text-center w-[80px]">Detail</a>
                            </div>
                        </td>
                        @endif
                    </tr>
                    @endforeach
                </tbody>
            @empty
                <tbody>
                    <tr>
                        <td colspan="9" class="border border-[#CAC0C0] px-4 py-16 text-center bg-white">
                            <div class="flex flex-col items-center justify-center text-gray-400">
                                <svg class="w-12 h-12 mb-3 opacity-60" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                                <p class="text-[14px] font-medium text-gray-500">Data pendaftaran tidak ditemukan.</p>
                            </div>
                        </td>
                    </tr>
                </tbody>
            @endforelse
            
            <!-- Alpine Empty State for Search -->
            <tbody x-show="totalVisible === 0 && {{ count($pendaftarans) }} > 0" style="display: none;">
                <tr>
                    <td colspan="9" class="border border-[#CAC0C0] px-4 py-16 text-center bg-white">
                        <div class="flex flex-col items-center justify-center text-gray-400">
                            <svg class="w-12 h-12 mb-3 opacity-60" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                            <p class="text-[14px] font-medium text-gray-500">Pencarian tidak membuahkan hasil.</p>
                        </div>
                    </td>
                </tr>
            </tbody>

        </table>
    </div>

    <!-- AlpineJS Dynamic Paginator Footer -->
    <div class="flex flex-col sm:flex-row items-center justify-between bg-white px-4 py-3 border-t-0 rounded-b-[10px] sm:px-6 shadow-sm border border-[#CAC0C0]" x-show="totalVisible > 0">
        <div class="hidden sm:block text-[13px] font-medium text-gray-600">
            Menampilkan <span class="font-bold" x-text="startItem"></span> - <span class="font-bold" x-text="endItem"></span> dari <span class="font-bold" x-text="totalVisible"></span> pendaftaran
        </div>
        <div class="flex flex-1 justify-between sm:justify-end mt-2 sm:mt-0 gap-2">
            <button @click="prevPage" :disabled="currentPage === 1" :class="{'opacity-50 cursor-not-allowed': currentPage === 1, 'hover:bg-gray-50': currentPage > 1}" class="relative inline-flex items-center px-4 py-1.5 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white shadow-sm transition">
                Sebelumnya
            </button>
            
            <div class="hidden md:flex items-center gap-1 mx-2">
                <template x-for="page in totalPages" :key="page">
                    <button type="button" @click="goToPage(page)" 
                        :class="currentPage === page ? 'bg-[#4285F4] text-white border-[#4285F4]' : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-50'"
                        class="relative inline-flex items-center px-3 py-1.5 border text-sm font-medium rounded-md shadow-sm transition"
                        x-text="page" x-show="totalPages <= 7 || page === 1 || page === totalPages || (page >= currentPage - 1 && page <= currentPage + 1)">
                    </button>
                </template>
            </div>

            <button @click="nextPage" :disabled="currentPage === totalPages" :class="{'opacity-50 cursor-not-allowed': currentPage === totalPages, 'hover:bg-gray-50': currentPage < totalPages}" class="relative inline-flex items-center px-4 py-1.5 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white shadow-sm transition">
                Selanjutnya
            </button>
        </div>
    </div>
</div>
