<x-dashboard-layout header="Persetujuan Sidang KP" userName="{{ auth()->user()->name }}" roleName="KOORDINATOR KP">
    <x-slot:sidebar>
        @include('koordinator.components.sidebar', ['active' => 'persetujuan-sidang'])
        </x-slot>

        <div class="mt-6" x-data="{ 
            showTolakModal: false, 
            selectedId: null,
            searchQuery: '',
            statusFilter: 'all',
            pengajuans: {{ $pengajuans->map(fn($p) => [
                'id' => $p->id,
                'nama' => $p->mahasiswa->user->name ?? 'User',
                'nim' => $p->mahasiswa->nim ?? '-',
                'judul' => $p->pendaftaranKp->judul_kp ?? '-',
                'file_laporan' => $p->file_laporan ? asset('storage/' . $p->file_laporan) : null,
                'link_github' => $p->link_github ?? null,
                'total_bimbingan' => $p->total_bimbingan_count ?? 0,
                'status' => $p->status_verifikasi,
                'feedback' => $p->dosen_feedback ?? 'Tidak ada catatan.',
                'detail_url' => '#' 
            ])->toJson() }},
            get filteredList() {
                return this.pengajuans.filter(p => {
                    const matchesSearch = !this.searchQuery || 
                        p.nama.toLowerCase().includes(this.searchQuery.toLowerCase()) ||
                        p.nim.toLowerCase().includes(this.searchQuery.toLowerCase()) ||
                        p.judul.toLowerCase().includes(this.searchQuery.toLowerCase());
                    
                    const matchesStatus = this.statusFilter === 'all' || p.status === this.statusFilter;
                        
                    return matchesSearch && matchesStatus;
                });
            }
        }">
            <div class="flex flex-col xl:flex-row gap-6 mb-8 items-start xl:items-stretch">
                <div
                    class="flex-1 bg-[#EAEFFF] border border-[#BACDFB] rounded-[10px] p-4 flex items-center gap-4 shadow-sm">
                    <div
                        class="bg-[#7896F8] w-6 h-6 rounded-full flex items-center justify-center text-white shrink-0 shadow-sm font-serif italic text-sm">
                        i</div>
                    <p class="text-[14px] text-black font-medium leading-relaxed">
                        Berikut adalah daftar mahasiswa bimbingan Anda yang sedang mengajukan permohonan persetujuan
                        untuk mendaftar Sidang KP.
                    </p>
                </div>

                <div class="flex gap-4">
                    <div
                        class="bg-[#38913B] rounded-[10px] p-3 flex flex-col justify-center items-center w-[90px] shadow-sm text-white">
                        <div class="flex items-center gap-2">
                            <div class="bg-white/20 p-1 rounded-full"><svg class="w-3 h-3" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 13l4 4L19 7"></path>
                                </svg></div>
                            <span class="text-xl font-bold">{{ $jumlahDisetujui ?? 0 }}</span>
                        </div>
                        <span class="text-[11px] font-medium mt-1">Disetujui</span>
                    </div>
                    <div
                        class="bg-[#FBC610] rounded-[10px] p-3 flex flex-col justify-center items-center w-[90px] shadow-sm text-black">
                        <div class="flex items-center gap-2">
                            <div class="border border-black p-0.5 rounded-sm"><svg class="w-3 h-3" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z">
                                    </path>
                                </svg></div>
                            <span class="text-xl font-bold">{{ $jumlahBelum ?? 0 }}</span>
                        </div>
                        <span class="text-[11px] font-medium text-center leading-tight mt-1">Belum<br>Disetujui</span>
                    </div>
                    <div
                        class="bg-[#EA3323] rounded-[10px] p-3 flex flex-col justify-center items-center w-[90px] shadow-sm text-white">
                        <div class="flex items-center gap-2">
                            <div class="bg-white/20 p-1 rounded-full"><svg class="w-3 h-3" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12"></path>
                                </svg></div>
                            <span class="text-xl font-bold">{{ $jumlahDitolak ?? 0 }}</span>
                        </div>
                        <span class="text-[11px] font-medium mt-1">Ditolak</span>
                    </div>
                </div>
            </div>

            <!-- Modern Search & Filter Bar -->
            <div class="bg-white p-4 rounded-[10px] border border-gray-200 shadow-sm mb-6">
                <div class="flex flex-col lg:flex-row items-center gap-4">
                    <div class="relative flex-1 w-full">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </div>
                        <input type="text" x-model="searchQuery"
                            class="block w-full pl-10 pr-4 py-2 border border-gray-300 rounded-[5px] text-sm text-black focus:ring-blue-500 focus:border-blue-500"
                            placeholder="Cari Nama Mahasiswa, NIM, atau Judul KP...">
                    </div>

                    <div class="flex items-center gap-2 shrink-0">
                        <label class="text-[13px] font-bold text-black whitespace-nowrap uppercase">Status :</label>
                        <select x-model="statusFilter"
                            class="w-[180px] text-[13px] border border-gray-300 rounded-[5px] py-2 px-3 bg-white text-black font-medium focus:outline-none focus:ring-1 focus:ring-blue-500">
                            <option value="all">Semua Status</option>
                            <option value="pending">Belum Disetujui</option>
                            <option value="verified">Selesai/Disetujui</option>
                            <option value="rejected">Ditolak</option>
                        </select>
                    </div>

                    <button @click="searchQuery = ''; statusFilter = 'all'"
                        class="bg-[#EA3323] hover:bg-red-700 text-white font-bold text-[12px] px-6 py-2.5 rounded-[5px] shadow-sm transition-colors uppercase whitespace-nowrap">
                        Clear Filter
                    </button>
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

            <div class="bg-white border border-gray-200 rounded-[10px] overflow-hidden shadow-sm">
                <div class="overflow-x-auto">
                    <table class="w-full border-collapse text-[13px]">
                        <thead>
                            <tr class="bg-[#EBEBEB] text-black">
                                <th class="py-3.5 px-4 font-bold text-center w-[5%]">No</th>
                                <th class="py-3.5 px-4 font-bold text-left">Mahasiswa</th>
                                <th class="py-3.5 px-4 font-bold text-center">Judul KP</th>
                                <th class="py-3.5 px-4 font-bold text-center">Laporan KP</th>
                                <th class="py-3.5 px-4 font-bold text-center whitespace-nowrap">Total Bimbingan</th>
                                <th class="py-3.5 px-4 font-bold text-center">Status Approval</th>
                                <th class="py-3.5 px-4 font-bold text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
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
                                        <template x-if="p.link_github && !p.file_laporan">
                                            <a :href="p.link_github" target="_blank" class="text-blue-600 hover:underline font-bold italic">Link GDrive</a>
                                        </template>
                                    </td>
                                    <td class="py-4 px-4 text-center text-black font-bold">
                                        <span x-text="p.total_bimbingan"></span>/12
                                    </td>
                                    <td class="py-4 px-4 text-center">
                                        <template x-if="p.status === 'pending'">
                                            <div class="flex items-center justify-center gap-2">
                                                <form :action="'{{ url('koordinator/persetujuan-sidang') }}/' + p.id + '/update'" method="POST">
                                                    @csrf @method('PUT')
                                                    <button type="submit" class="bg-[#38913B] hover:bg-green-700 text-white text-[11px] font-bold px-3 py-1.5 rounded flex items-center gap-1 shadow-sm uppercase transition-colors">
                                                        Sahkan
                                                    </button>
                                                </form>
                                                <button type="button" @click="showTolakModal = true; selectedId = p.id" class="bg-[#EA3323] hover:bg-red-700 text-white text-[11px] font-bold px-3 py-1.5 rounded flex items-center gap-1 shadow-sm uppercase transition-colors">
                                                    Tolak
                                                </button>
                                            </div>
                                        </template>
                                        <template x-if="p.status === 'verified'">
                                            <span class="bg-[#86EFAC] text-[#166534] font-bold px-4 py-1.5 rounded-full text-[11px] uppercase flex items-center justify-center w-max mx-auto gap-1.5 shadow-sm">
                                                <div class="w-2 h-2 rounded-full bg-green-600"></div> Selesai
                                            </span>
                                        </template>
                                        <template x-if="p.status === 'rejected'">
                                            <div class="flex flex-col items-center gap-1">
                                                <span class="bg-red-100 text-red-700 font-bold px-4 py-1.5 rounded-full text-[11px] uppercase shadow-sm">
                                                    Ditolak
                                                </span>
                                                <span class="text-[10px] text-red-500 italic max-w-[150px] truncate" :title="p.feedback" x-text="'Feedback: ' + p.feedback"></span>
                                            </div>
                                        </template>
                                    </td>
                                    <td class="py-4 px-4 text-center">
                                        <a href="#" class="bg-[#3B5998] hover:bg-blue-800 text-white text-[11px] font-bold px-5 py-2 rounded-full inline-block shadow-sm transition-all uppercase shadow-blue-900/10">
                                            Detail
                                        </a>
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

            <!-- Rejection Modal -->
            <div x-cloak x-show="showTolakModal" style="display:none;"
                class="fixed inset-0 z-[100] flex items-center justify-center bg-black/50 backdrop-blur-sm p-4">
                <div @click.away="showTolakModal = false"
                    class="bg-white w-full max-w-md rounded-[10px] shadow-xl overflow-hidden transform transition-all scale-100">
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
        </div>

</x-dashboard-layout>