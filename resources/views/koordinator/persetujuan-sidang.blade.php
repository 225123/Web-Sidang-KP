<x-dashboard-layout header="Persetujuan Sidang KP" userName="{{ auth()->user()->name }}" roleName="KOORDINATOR KP">
    <x-slot:sidebar>
        @include('koordinator.components.sidebar', ['active' => 'persetujuan-sidang'])
        </x-slot>

    

        <div class="mt-6" x-data="{ 
            showTolakModal: false, 
            selectedId: null,
            searchQuery: '',
            statusFilter: 'all',
            pengajuans: {{ \Illuminate\Support\Js::from($pengajuans->map(fn($p) => [
                'id' => $p->id,
                'nama' => $p->mahasiswa->user->name ?? 'User',
                'nim' => $p->mahasiswa->nim ?? '-',
                'judul' => \App\Models\PendaftaranKp::where('mahasiswa_id', $p->mahasiswa_id)->latest()->value('judul_kp') ?? $p->pendaftaranKp->judul_kp ?? '-',
                'file_laporan' => $p->file_laporan ? storage_url($p->file_laporan) : null,
                'link_drive' => $p->link_drive ?? null,
                'total_bimbingan' => $p->total_bimbingan_count ?? 0,
                'status' => $p->status_verifikasi,
                'feedback' => $p->dosen_feedback ?? 'Tidak ada catatan.',
                'detail_url' => '#' 
            ])) }},
            rejectedList: {{ \Illuminate\Support\Js::from($riwayatPenolakan->map(fn($r) => [
                'id' => $r->id,
                'nama' => $r->pendaftaranSidang->mahasiswa->user->name ?? 'User',
                'nim' => $r->pendaftaranSidang->mahasiswa->nim ?? '-',
                'tanggal_upload' => $r->pendaftaranSidang->created_at ? $r->pendaftaranSidang->created_at->format('d M Y, H:i') : '-',
                'feedback' => $r->alasan_penolakan ?? 'Tidak ada catatan.',
                'ditolak_oleh' => $r->ditolak_oleh
            ])) }},
            get filteredList() {
                return this.pengajuans.filter(p => {
                    if (p.status === 'rejected') return false;
                    
                    const matchesSearch = !this.searchQuery || 
                        p.nama.toLowerCase().includes(this.searchQuery.toLowerCase()) ||
                        p.nim.toLowerCase().includes(this.searchQuery.toLowerCase()) ||
                        p.judul.toLowerCase().includes(this.searchQuery.toLowerCase());
                    
                    const matchesStatus = this.statusFilter === 'all' || p.status === this.statusFilter;
                        
                    return matchesSearch && matchesStatus;
                });
            }
        }">
            <!-- Unified Table Container -->
            <div class="bg-white rounded-[15px] border border-gray-200 shadow-sm overflow-hidden p-6 mb-8">
                <!-- Header Section -->
                <div class="border-b border-gray-200 pb-6 mb-6">
                    <!-- Title & Description Row with Stats -->
                    <div class="mb-8 flex flex-col sm:flex-row justify-between items-start gap-4">
                        <div>
                            <h3 class="text-[18px] font-bold text-black tracking-tight uppercase">Tabel Persetujuan Sidang KP</h3>
                            <p class="text-[12px] text-black/60 font-medium mt-1">Berikut adalah daftar mahasiswa bimbingan Anda yang sedang mengajukan permohonan persetujuan untuk mendaftar Sidang KP.</p>
                        </div>
                        <div class="flex flex-wrap gap-2 shrink-0">
                            <div class="bg-[#38913B] text-white rounded-[5px] px-3 py-1.5 flex items-center gap-2 shadow-sm border border-green-600/20">
                                <span class="text-[16px] font-bold leading-none">{{ $jumlahDisetujui ?? 0 }}</span>
                                <span class="text-[11px] font-medium uppercase tracking-wider">Disetujui</span>
                            </div>
                            <div class="bg-[#FBC610] text-black rounded-[5px] px-3 py-1.5 flex items-center gap-2 shadow-sm border border-yellow-500/20">
                                <span class="text-[16px] font-bold leading-none">{{ $jumlahBelum ?? 0 }}</span>
                                <span class="text-[11px] font-medium uppercase tracking-wider">Belum Disetujui</span>
                            </div>
                            <div class="bg-[#EA3323] text-white rounded-[5px] px-3 py-1.5 flex items-center gap-2 shadow-sm border border-red-600/20">
                                <span class="text-[16px] font-bold leading-none">{{ $jumlahDitolak ?? 0 }}</span>
                                <span class="text-[11px] font-medium uppercase tracking-wider">Ditolak</span>
                            </div>
                        </div>
                    </div>

                    <!-- Search & Filters Row -->
                    <div class="flex flex-col xl:flex-row gap-4">
                        <!-- Search Bar -->
                        <div class="relative w-full xl:w-[350px] shrink-0">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                            </div>
                            <input type="text" x-model="searchQuery" 
                                class="w-full h-[36px] pl-10 pr-4 text-[13px] bg-white border border-[#CAC0C0] rounded-[5px] text-black focus:outline-none focus:border-gray-400 focus:ring-0 transition-colors shadow-sm placeholder:text-gray-400" 
                                placeholder="Cari nama, NIM, atau judul KP...">
                        </div>

                        <!-- Dropdown Filters -->
                        <div class="flex flex-wrap items-center gap-3">
                            <!-- Filter Status -->
                            <div x-data="{ open: false }" class="relative w-full sm:w-[220px]">
                                <button @click="open = !open" @click.outside="open = false" type="button" 
                                    class="w-full h-[36px] flex items-center justify-between border border-[#CAC0C0] bg-white rounded-[5px] px-3 text-[13px] text-black hover:bg-gray-50 transition-colors shadow-sm focus:outline-none focus:border-gray-400 focus:ring-0">
                                    <span class="font-medium truncate" x-text="statusFilter === 'all' ? 'Status Approval' : (statusFilter === 'pending' ? 'Belum Disetujui' : 'Selesai/Disetujui')"></span>
                                    <svg :class="open ? 'rotate-0' : 'rotate-90'" class="w-3.5 h-3.5 text-gray-500 transition-transform duration-200 shrink-0 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                </button>
                                <div x-show="open" x-transition.opacity.duration.200ms style="display: none;" class="absolute z-50 w-full mt-1 bg-white border border-[#CAC0C0] rounded-[5px] shadow-lg py-1">
                                    <label class="block px-3 py-2 text-[12px] hover:bg-gray-100 cursor-pointer text-black">
                                        <input type="radio" value="all" x-model="statusFilter" class="hidden" @change="open = false">Semua Status
                                    </label>
                                    <label class="block px-3 py-2 text-[12px] hover:bg-gray-100 cursor-pointer text-black">
                                        <input type="radio" value="pending" x-model="statusFilter" class="hidden" @change="open = false">Belum Disetujui
                                    </label>
                                    <label class="block px-3 py-2 text-[12px] hover:bg-gray-100 cursor-pointer text-black">
                                        <input type="radio" value="verified" x-model="statusFilter" class="hidden" @change="open = false">Selesai/Disetujui
                                    </label>
                                </div>
                            </div>

                            <button @click="searchQuery = ''; statusFilter = 'all'" class="h-[36px] bg-[#EA3323] hover:bg-red-700 text-white font-bold text-[12px] px-4 rounded-[5px] shadow-sm transition-colors uppercase whitespace-nowrap">
                                Clear Filter
                            </button>
                        </div>
                    </div>
                </div>

            @if(session('success'))
                <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-[5px] relative shadow-sm flex items-center gap-3">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    <span class="block sm:inline font-medium text-sm">{{ session('success') }}</span>
                </div>
            @endif

                <div class="border border-gray-200 rounded-[10px] overflow-hidden">
                    <div class="overflow-x-auto custom-scrollbar">
                        <table class="w-full text-center border-collapse text-[13px] min-w-[1250px]">
                            <thead class="bg-[#EBEBEB] text-black">
                                <tr>
                                    <th class="py-3.5 px-4 font-bold text-center w-[5%]">No</th>
                                    <th class="py-3.5 px-4 font-bold text-left w-[200px]">Mahasiswa</th>
                                    <th class="py-3.5 px-4 font-bold text-center">Judul KP</th>
                                    <th class="py-3.5 px-4 font-bold text-center w-[150px]">Laporan KP</th>
                                    <th class="py-3.5 px-4 font-bold text-center w-[150px]">Total Bimbingan</th>
                                    <th class="py-3.5 px-4 font-bold text-center w-[180px]">Status Approval</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 bg-white">
                                <template x-for="(p, index) in filteredList" :key="p.id">
                                    <tr class="hover:bg-gray-50 transition-colors">
                                        <td class="py-4 px-4 text-center text-black font-normal" x-text="index+1"></td>
                                        <td class="py-4 px-4 text-left">
                                            <div class="font-bold text-black" x-text="p.nama"></div>
                                            <div class="text-black/60 text-[11px]" x-text="p.nim"></div>
                                        </td>
                                        <td class="py-4 px-4 text-center text-black font-normal" x-text="p.judul"></td>
                                        <td class="py-4 px-4 text-center">
                                            <template x-if="p.file_laporan">
                                                <a :href="p.file_laporan" target="_blank" class="text-blue-600 hover:underline font-bold italic">Lihat Laporan</a>
                                            </template>
                                            <template x-if="p.link_drive && !p.file_laporan">
                                                <a :href="p.link_drive" target="_blank" class="text-blue-600 hover:underline font-bold italic">Link GDrive</a>
                                            </template>
                                        </td>
                                        <td class="py-4 px-4 text-center text-black font-bold">
                                            <span x-text="p.total_bimbingan"></span>/12
                                        </td>
                                        <td class="py-4 px-4 text-center">
                                            <template x-if="p.status === 'pending'">
                                                <div class="flex items-center justify-center gap-2">
                                                    @if(!isset($isReadOnly) || !$isReadOnly)
                                                    <form :action="'{{ url('koordinator/persetujuan-sidang') }}/' + p.id + '/update'" method="POST" class="w-full">
                                                        @csrf @method('PUT')
                                                        <input type="hidden" name="status" value="verified">
                                                        <button type="submit" class="w-full bg-[#38913B] hover:bg-green-700 text-white font-bold text-[11px] px-3 py-1.5 rounded flex items-center justify-center shadow-sm uppercase transition-colors">
                                                            Terima
                                                        </button>
                                                    </form>
                                                    <button type="button" @click="showTolakModal = true; selectedId = p.id" class="w-full bg-[#EA3323] hover:bg-red-700 text-white font-bold text-[11px] px-3 py-1.5 rounded flex items-center justify-center shadow-sm uppercase transition-colors">
                                                        Tolak
                                                    </button>
                                                    @else
                                                    <span class="text-[10px] text-red-500 font-bold uppercase tracking-wide">Read Only</span>
                                                    @endif
                                                </div>
                                            </template>
                                            <template x-if="p.status === 'verified'">
                                                <span class="bg-[#86EFAC] text-[#166534] font-bold px-4 py-1.5 rounded-full text-[11px] uppercase flex items-center justify-center w-max mx-auto gap-1.5 shadow-sm">
                                                    Diterima
                                                </span>
                                            </template>
                                        </td>
                                    </tr>
                                </template>
                                <template x-if="filteredList.length === 0">
                                    <tr>
                                        <td colspan="7" class="text-center py-12 text-gray-500 italic bg-gray-50 font-medium">
                                            Tidak ada data pengajuan yang sesuai dengan filter.
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Tabel Riwayat Penolakan -->
            <div class="bg-white rounded-[15px] border border-gray-200 shadow-sm overflow-hidden p-6 mb-8">
                <div class="mb-6 flex flex-col sm:flex-row justify-between items-start gap-4">
                    <div>
                        <h3 class="text-[18px] font-bold text-black tracking-tight uppercase">Riwayat Penolakan</h3>
                        <p class="text-[12px] text-black/60 font-medium mt-1">Daftar pengajuan persetujuan sidang yang telah ditolak dan dikembalikan ke mahasiswa untuk direvisi.</p>
                    </div>
                </div>

                <div class="border border-gray-200 rounded-[10px] overflow-hidden">
                    <div class="overflow-x-auto custom-scrollbar">
                        <table class="w-full text-center border-collapse text-[13px] min-w-[1070px]">
                            <thead class="bg-[#EBEBEB] text-black">
                                <tr>
                                    <th class="py-3.5 px-4 font-bold text-center w-[5%]">No</th>
                                    <th class="py-3.5 px-4 font-bold text-left w-[200px]">Mahasiswa</th>
                                    <th class="py-3.5 px-4 font-bold text-center">Tanggal Upload</th>
                                    <th class="py-3.5 px-4 font-bold text-center">Alasan penolakan</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 bg-white">
                                <template x-for="(p, index) in rejectedList" :key="'rej'+p.id">
                                    <tr class="hover:bg-gray-50 transition-colors">
                                        <td class="py-4 px-4 text-center text-black font-normal" x-text="index+1"></td>
                                        <td class="py-4 px-4 text-left">
                                            <div class="font-bold text-black" x-text="p.nama"></div>
                                            <div class="text-black/60 text-[11px]" x-text="p.nim"></div>
                                        </td>
                                        <td class="py-4 px-4 text-center text-black font-normal" x-text="p.tanggal_upload"></td>
                                        <td class="py-4 px-4 text-center">
                                            <div class="flex flex-col items-center gap-1">
                                                <span class="text-[12px] text-red-600 font-medium max-w-[250px] text-center" x-text="p.feedback"></span>
                                                <span class="text-[9px] text-gray-500 max-w-[200px] text-center mt-1" x-text="'Oleh: ' + p.ditolak_oleh"></span>
                                            </div>
                                        </td>
                                    </tr>
                                </template>
                                <template x-if="rejectedList.length === 0">
                                    <tr>
                                        <td colspan="6" class="text-center py-12 text-gray-500 italic bg-gray-50 font-medium">
                                            Belum ada riwayat penolakan persetujuan sidang.
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <template x-teleport="body">
                <div x-cloak x-show="showTolakModal" 
                    class="fixed inset-0 z-[9999] flex items-center justify-center bg-black/50 backdrop-blur-sm p-4">
                    <div @click.away="showTolakModal = false"
                        class="bg-white w-full max-w-md rounded-[12px] shadow-2xl overflow-hidden transform transition-all scale-100"
                        x-transition:enter="transition ease-out duration-300"
                        x-transition:enter-start="opacity-0 translate-y-4 scale-95"
                        x-transition:enter-end="opacity-100 translate-y-0 scale-100">
                    <div class="px-6 py-4 border-b border-gray-200 bg-red-50 flex justify-between items-center">
                        <h2 class="text-lg font-bold text-red-700 flex items-center gap-2 uppercase tracking-tight">
                            <svg class="w-5 h-5 font-bold" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z">
                                </path>
                            </svg>
                            Tolak Pengajuan
                        </h2>
                        <button @click="showTolakModal = false" class="text-gray-400 hover:text-red-500 transition-colors"><svg
                                class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                    d="M6 18L18 6M6 6l12 12"></path>
                            </svg></button>
                    </div>

                    <form :action="'{{ url('koordinator/persetujuan-sidang') }}/' + selectedId + '/tolak'" method="POST"
                        class="p-6">
                        @csrf
                        @method('DELETE')
                        <p class="text-[13px] text-gray-700 mb-4 leading-relaxed font-medium">Berikan alasan penolakan agar mahasiswa dapat memperbaiki
                            laporannya. Data akan disimpan dalam riwayat penolakan.</p>

                        <textarea name="feedback" required rows="4"
                            class="w-full border border-gray-300 rounded-[5px] p-4 text-[13px] text-black font-normal focus:ring-1 focus:ring-red-500 outline-none resize-none mb-6 shadow-sm"
                            placeholder="Misal: Bab 3 masih kurang lengkap, harap lengkapi logbook..."></textarea>

                        <div class="flex justify-end gap-3">
                            <button type="button" @click="showTolakModal = false"
                                class="px-5 py-2 bg-gray-100 hover:bg-gray-200 text-gray-800 text-[12px] font-bold rounded-[5px] uppercase transition-colors">Batal</button>
                            <button type="submit"
                                class="px-5 py-2 bg-[#EA3323] hover:bg-red-700 text-white text-[12px] font-bold rounded-[5px] shadow-md uppercase transition-all">Kirim
                                Penolakan</button>
                        </div>
                    </form>
                </div>
            </div>
            </template>
        </div>

</x-dashboard-layout>