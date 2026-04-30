@props(['pendaftarans', 'isRejected' => false, 'searchModel' => 'searchQuery'])

<div class="mb-4"
    x-data="{
        currentPage: 1,
        perPage: 10,
        totalVisible: {{ count($pendaftarans) }},
        jenisFilter: 'All',
        statusFilter: 'All',
        pengerjaanFilter: 'All',
        baruLanjutFilter: 'All',
        init() {
            this.$watch('{{ $searchModel ?? "searchQuery" }}', value => {
                this.currentPage = 1;
                this.updateTable();
            });
            this.$nextTick(() => this.updateTable());
        },
        updateTable() {
            if (!this.$refs.tableRoot) return;
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

                let checkBaruLanjut = this.baruLanjutFilter.toLowerCase();
                let matchBaruLanjut = (this.baruLanjutFilter === 'All' || g.dataset.barulanjut.toLowerCase() === checkBaruLanjut);

                if (matchSearch && matchJenis && matchStatus && matchPengerjaan && matchBaruLanjut) {
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
    @update-jenis-{{ $isRejected ? 'rejected' : 'main' }}.window="jenisFilter = $event.detail; currentPage = 1; updateTable()"
    @update-status-{{ $isRejected ? 'rejected' : 'main' }}.window="statusFilter = $event.detail; currentPage = 1; updateTable()"
    @update-pengerjaan-{{ $isRejected ? 'rejected' : 'main' }}.window="pengerjaanFilter = $event.detail; currentPage = 1; updateTable()"
    @update-barulanjut-{{ $isRejected ? 'rejected' : 'main' }}.window="baruLanjutFilter = $event.detail; currentPage = 1; updateTable()"
    @clear-filters-{{ $isRejected ? 'rejected' : 'main' }}.window="jenisFilter = 'All'; statusFilter = 'All'; pengerjaanFilter = 'All'; baruLanjutFilter = 'All'; currentPage = 1; updateTable()"
>
    <div class="overflow-x-auto w-full mb-0">
        <table class="w-full min-w-[1000px] border-collapse text-[13px] text-center" x-ref="tableRoot">
            <thead>
                <tr class="bg-[#EBEBEB] text-black">
                    <th class="py-3 px-4 font-bold text-center w-[60px] border-b border-r border-gray-300">No</th>
                    <th class="py-3 px-4 font-bold text-left border-b border-r border-gray-300">Mahasiswa</th>
                    <th class="py-3 px-4 font-bold text-center border-b border-r border-gray-300">Pengerjaan</th>
                    @if($isRejected)
                    <th class="py-3 px-4 font-bold text-center border-b border-r border-gray-300 whitespace-nowrap">Jenis KP</th>
                    @endif
                    <th class="py-3 px-4 font-bold text-left border-b border-r border-gray-300">Nama Instansi</th>
                    <th class="py-3 px-4 font-bold text-left border-b border-r border-gray-300">Supervisor</th>
                    <th class="py-3 px-4 font-bold text-center border-b border-r border-gray-300">Judul KP</th>
                    <th class="py-3 px-4 font-bold text-center border-b border-r border-gray-300 {{ $isRejected ? 'min-w-[150px]' : 'min-w-[160px]' }}">Aksi</th>
                    <th class="py-3 px-4 font-bold text-center border-b border-gray-300 min-w-[80px]">Detail</th>
                </tr>
            </thead>

            @forelse($pendaftarans as $index => $kp)
                @if($kp->is_duplicate ?? false) @continue @endif
                @php 
                    $mhsList = $kp->mahasiswaList ?? [['nama' => $kp->user->name ?? 'Unknown', 'nim' => $kp->user->mahasiswa->nim ?? 'Unknown', 'has_registered' => true]];
                    $rowspan = count($mhsList);
                    $allNamesNims = '';
                    foreach($mhsList as $member) {
                        $allNamesNims .= strtolower(($member['nama'] ?? '') . ' ' . ($member['nim'] ?? '')) . ' ';
                    }
                    $rowSearchString = $allNamesNims . strtolower(($kp->judul_kp ?? '') . ' ' . ($kp->instansi_nama ?? ''));
                    $hasAnggota = $rowspan > 1;
                    $groupSize = $rowspan;
                @endphp
                <tbody class="proposal-group transition-colors duration-150" 
                    data-search="{{ $rowSearchString }}" 
                    data-jenis="{{ $kp->jenis_instansi ?? '' }}"
                    data-status="{{ $kp->status_kp ?? '' }}"
                    data-pengerjaan="{{ $kp->pengerjaan_kp ?? 'individu' }}"
                    data-barulanjut="{{ $kp->is_lanjutan ? 'lanjut' : 'baru' }}"
                    style="display: none;">
                    @foreach($mhsList as $mIdx => $m)
                    <tr class="bg-white hover:bg-gray-50 font-medium border-b border-gray-200">
                        <td class="border-r border-gray-200 px-4 py-2 text-center align-middle">
                            <span class="text-gray-700 font-bold row-counter"></span>
                        </td>

                        <td class="border-r border-gray-200 px-4 py-2 text-left">
                            <div class="font-bold text-[12px] text-gray-800">{{ $m['nama'] }}</div>
                            <div class="text-[11px] text-gray-500 font-medium">{{ $m['nim'] }}</div>
                        </td>
                        
                        @if($mIdx === 0)
                        <td rowspan="{{ $rowspan }}" class="border-r border-gray-200 px-4 py-2 text-center align-middle">
                            <div class="inline-flex py-1 px-3 rounded-[5px] text-[11px] font-bold {{ ($kp->pengerjaan_kp ?? '') == 'berkelompok' ? 'bg-[#FFF3E0] text-[#E65100]' : 'bg-[#E3F2FD] text-[#0D47A1]' }}">
                                {{ ucfirst($kp->pengerjaan_kp ?? 'Individu') }}
                            </div>
                        </td>
                        
                        @if($isRejected)
                        <td rowspan="{{ $rowspan }}" class="border-r border-gray-200 px-4 py-2 text-center align-middle">{{ $kp->jenis_instansi }}</td>
                        @endif
                        @endif <!-- Close the first mIdx === 0 block -->
                        
                        <td class="border-r border-gray-200 px-4 py-2 align-middle text-left">{{ $m['instansi_nama'] ?? $kp->instansi_nama ?? '-' }}</td>
                        
                        @if($mIdx === 0)
                        <td rowspan="{{ $rowspan }}" class="border-r border-gray-200 px-4 py-2 align-middle text-left">{{ $kp->supervisorInstansi->nama_supervisor ?? '-' }}</td>
                        @endif
                        
                        <td class="border-r border-gray-200 px-4 py-2 max-w-[200px] break-words align-middle text-left">
                            @if(isset($m['has_registered']) && !$m['has_registered'])
                                <span class="text-gray-400 font-bold block text-center">-</span>
                            @else
                                {{ Str::limit($m['judul_kp'] ?? $kp->judul_kp ?? '-', 50) }}
                            @endif
                        </td>
                        
                        @if($mIdx === 0)
                        <td rowspan="{{ $rowspan }}" class="px-4 py-2 border-r border-gray-200 text-center align-middle">
                            @if($isRejected)
                                <span class="text-red-500 font-bold text-[11px] bg-red-50 px-2 py-0.5 rounded-[5px]">Ditolak</span>
                                <div class="text-[10px] text-gray-500 italic mt-1">{{ $kp->updated_at ? $kp->updated_at->format('d M Y') : '-' }}</div>
                            @elseif(($kp->status_kp ?? '') === 'approved')
                                <span class="text-green-600 font-bold text-[11px] bg-green-50 px-2 py-0.5 rounded-[5px]">Disetujui</span>
                                <div class="text-[10px] text-gray-500 italic mt-1">{{ $kp->updated_at ? $kp->updated_at->format('d M Y') : '-' }}</div>
                            @else
                                <div class="flex items-center justify-center gap-2">
                                    <form action="{{ route('koordinator.pendaftaran-kp.status', $kp->id) }}" method="POST">
                                        @csrf @method('PUT')
                                        <input type="hidden" name="status" value="approved">
                                        <button type="submit" class="bg-[#38913B] hover:bg-green-700 text-white px-3 py-1.5 rounded shadow-sm text-[10px] font-bold transition-colors whitespace-nowrap">Setujui</button>
                                    </form>
                                    
                                    <button type="button" @click="openModalCatatan($el.nextElementSibling, 'Tolak Pendaftaran KP?')" class="bg-[#EA3323] hover:bg-red-700 text-white px-3 py-1.5 rounded shadow-sm text-[10px] font-bold transition-colors whitespace-nowrap">Tolak</button>
                                    <form class="hidden reject-form" action="{{ route('koordinator.pendaftaran-kp.status', $kp->id) }}" method="POST">
                                        @csrf @method('PUT')
                                        <input type="hidden" name="status" value="rejected">
                                    </form>
                                </div>
                            @endif
                        </td>
                        @endif
                        
                        <td class="px-4 py-2 text-center align-middle border-l border-gray-200">
                            @if(isset($m['has_registered']) && !$m['has_registered'])
                                <span class="text-gray-400 font-bold">-</span>
                            @else
                                <a href="{{ route('koordinator.pendaftaran-kp.show', Str::slug(($m['nama'] ?? '') . '-' . ($m['nim'] ?? ''))) }}" class="bg-[#4285F4] hover:bg-blue-700 text-white px-4 py-1.5 rounded shadow-sm text-[10px] font-bold transition-colors whitespace-nowrap inline-block">Detail</a>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            @empty
                <tbody class="divide-y divide-gray-200 proposal-group" data-search="" data-jenis="" data-status="" data-pengerjaan="" data-barulanjut="">
                    <tr>
                        <td colspan="{{ $isRejected ? 9 : 8 }}" class="py-12 text-center text-gray-400 italic font-medium bg-gray-50 tracking-widest border-b border-gray-300">
                            Tidak Ada Data
                        </td>
                    </tr>
                </tbody>
            @endforelse
            
            <tbody x-show="totalVisible === 0 && {{ count($pendaftarans) }} > 0" style="display: none;">
                <tr>
                    <td colspan="{{ $isRejected ? 9 : 8 }}" class="border border-gray-200 px-4 py-16 text-center bg-white">
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
    <div class="px-6 py-4 bg-white flex items-center justify-between border-t border-gray-200 rounded-b-[10px]" x-show="totalPages > 1">
        <span class="text-[12px] font-medium text-black/50" x-text="(totalVisible === 0 ? 0 : ((currentPage - 1) * perPage + 1)) + ' - ' + Math.min(currentPage * perPage, totalVisible) + ' dari ' + totalVisible + ' baris'"></span>
        <div class="flex items-center gap-2">
            <button @click="prevPage" :disabled="currentPage === 1" class="px-3 py-1 border border-gray-300 rounded text-[12px] hover:bg-gray-50 disabled:opacity-30 disabled:cursor-not-allowed transition-colors">Previous</button>
            <div class="flex items-center gap-1">
                <template x-for="page in totalPages" :key="page">
                    <button type="button" @click="goToPage(page)" 
                        :class="currentPage === page ? 'bg-blue-600 text-white shadow-md' : 'text-black hover:bg-gray-100'"
                        class="w-8 h-8 rounded text-[12px] font-bold transition-all"
                        x-text="page" x-show="totalPages <= 7 || page === 1 || page === totalPages || (page >= currentPage - 1 && page <= currentPage + 1)">
                    </button>
                </template>
            </div>
            <button @click="nextPage" :disabled="currentPage === totalPages" class="px-3 py-1 border border-gray-300 rounded text-[12px] hover:bg-gray-50 disabled:opacity-30 disabled:cursor-not-allowed transition-colors">Next</button>
        </div>
    </div>
</div>
