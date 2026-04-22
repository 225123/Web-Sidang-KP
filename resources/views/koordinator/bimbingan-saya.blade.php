<x-dashboard-layout header="Daftar Bimbingan Mahasiswa" userName="{{ auth()->user()->name }}" roleName="KOORDINATOR KP">
    <x-slot:sidebar>
        @include('koordinator.components.sidebar', ['active' => 'bimbingan-saya'])
    </x-slot>

    <style>
        [x-cloak] { display: none !important; }
        .custom-scrollbar::-webkit-scrollbar { width: 5px; height: 5px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: #f1f1f1; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #ccc; border-radius: 10px; }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #999; }
    </style>

    <div class="mt-6" x-data="{ 
        searchQuery: '',
        statusFilter: 'all',
        pendaftarans: {{ $pendaftarans->map(fn($p) => [
            'id' => $p->id,
            'nama' => $p->display_mahasiswa->user->name ?? '',
            'nim' => $p->display_mahasiswa->nim ?? '',
            'judul' => $p->display_judul_kp ?? '',
            'instansi' => $p->display_instansi ?? '',
            'supervisor' => $p->display_supervisor ?? '-',
            'total_log' => $p->total_log ?? 0,
            'status_label' => $p->status_approval_semua ?? '-',
            'detail_url' => $p->id ? route('koordinator.bimbingan-saya.detail', $p->id) : '#'
        ])->toJson() }},
        get filteredList() {
            return this.pendaftarans.filter(p => {
                const term = this.searchQuery.toLowerCase();
                const matchesSearch = !this.searchQuery || 
                    p.nama.toLowerCase().includes(term) ||
                    p.nim.toLowerCase().includes(term) ||
                    p.judul.toLowerCase().includes(term) ||
                    p.instansi.toLowerCase().includes(term) ||
                    p.supervisor.toLowerCase().includes(term);
                
                const matchesStatus = this.statusFilter === 'all' || 
                    (this.statusFilter === 'diperiksa' && p.status_label === 'Diperiksa') ||
                    (this.statusFilter === 'pending' && p.status_label === 'Menunggu pengecekan');
                    
                return matchesSearch && matchesStatus;
            });
        }
    }">
        <div class="flex flex-col xl:flex-row gap-6 mb-8 items-start xl:items-stretch">
            <div class="flex-1 bg-[#EAEFFF] border border-[#BACDFB] rounded-[10px] p-4 flex items-center gap-4 shadow-sm">
                <div class="bg-[#7896F8] w-6 h-6 rounded-full flex items-center justify-center text-white shrink-0 shadow-sm font-serif italic text-sm">i</div>
                <p class="text-[14px] text-black font-medium leading-relaxed">
                    Silahkan meninjau jumlah Mahasiswa bimbingan Anda dan lakukan verifikasi bimbingan di dalam Log Bimbingan tiap mahasiswa.
                </p>
            </div>

            <div class="flex gap-4">
                <div class="bg-[#38913B] rounded-[10px] p-3 flex flex-col justify-center items-center w-[100px] shadow-sm text-white">
                    <div class="flex items-center gap-2">
                        <div class="bg-white/20 p-1 rounded-full">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        </div>
                        <span class="text-[24px] font-bold">{{ $jumlahSelesai ?? 0 }}</span>
                    </div>
                    <span class="text-[12px] font-medium mt-1">Selesai</span>
                </div>
                <div class="bg-[#FBC610] rounded-[10px] p-3 flex flex-col justify-center items-center w-[100px] shadow-sm text-black">
                    <div class="flex items-center gap-2">
                        <div class="border border-black p-1 rounded-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"></path></svg>
                        </div>
                        <span class="text-[24px] font-bold">{{ $jumlahBelumDiperiksa ?? 0 }}</span>
                    </div>
                    <span class="text-[12px] font-medium text-center leading-tight mt-1">Belum<br>diperiksa</span>
                </div>
            </div>
        </div>

        <!-- Modern Search & Filter Bar -->
        <div class="bg-white p-4 rounded-[10px] border border-gray-200 shadow-sm mb-6">
            <div class="flex flex-col lg:flex-row items-center gap-4">
                <div class="relative flex-1 w-full">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </div>
                    <input type="text" x-model="searchQuery" class="block w-full pl-10 pr-4 py-2 border border-gray-300 rounded-[5px] text-sm text-black focus:ring-blue-500 focus:border-blue-500" placeholder="Cari Nama, NIM, Judul KP, Instansi, atau Supervisor...">
                </div>

                <div class="flex items-center gap-2 shrink-0">
                    <label class="text-[13px] font-bold text-black whitespace-nowrap uppercase">Status Approval :</label>
                    <select x-model="statusFilter" class="w-[180px] text-[13px] border border-gray-300 rounded-[5px] py-2 px-3 bg-white text-black font-medium focus:outline-none focus:ring-1 focus:ring-blue-500">
                        <option value="all">Semua Status</option>
                        <option value="diperiksa">Diperiksa</option>
                        <option value="pending">Menunggu pengecekan</option>
                    </select>
                </div>

                <button @click="searchQuery = ''; statusFilter = 'all'" class="bg-[#EA3323] hover:bg-red-700 text-white font-bold text-[12px] px-6 py-2.5 rounded-[5px] shadow-sm transition-colors uppercase whitespace-nowrap">
                    Clear Filter
                </button>
            </div>
        </div>

        <div class="bg-white border border-gray-200 rounded-[10px] overflow-hidden shadow-sm mb-12">
            <div class="overflow-x-auto custom-scrollbar">
                <table class="w-full border-collapse text-[13px]">
                    <thead>
                        <tr class="bg-[#EBEBEB] text-black">
                            <th class="py-4 px-4 font-bold text-center w-[60px] border-b border-gray-300 uppercase tracking-wider">No</th>
                            <th class="py-4 px-4 font-bold text-left border-b border-gray-300 uppercase tracking-wider">Mahasiswa</th>
                            <th class="py-4 px-4 font-bold text-center border-b border-gray-300 uppercase tracking-wider">Judul KP</th>
                            <th class="py-4 px-4 font-bold text-center border-b border-gray-300 uppercase tracking-wider">Instansi</th>
                            <th class="py-4 px-4 font-bold text-center border-b border-gray-300 uppercase tracking-wider">Supervisor</th>
                            <th class="py-4 px-4 font-bold text-center border-b border-gray-300 uppercase tracking-wider">Bimbingan</th>
                            <th class="py-4 px-4 font-bold text-center border-b border-gray-300 uppercase tracking-wider">Status</th>
                            <th class="py-4 px-4 font-bold text-center border-b border-gray-300 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        <template x-for="(p, index) in filteredList" :key="p.id">
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="py-4 px-4 text-center text-black/60" x-text="index + 1"></td>
                                <td class="py-4 px-4 text-left">
                                    <div class="font-bold text-black" x-text="p.nama"></div>
                                    <div class="text-black/60 text-[11px]" x-text="p.nim"></div>
                                </td>
                                <td class="py-4 px-4 text-center text-black/80 font-medium leading-relaxed" x-text="p.judul"></td>
                                <td class="py-4 px-4 text-center text-black/70 italic" x-text="p.instansi"></td>
                                <td class="py-4 px-4 text-center text-black font-bold uppercase text-[11px]" x-text="p.supervisor"></td>
                                <td class="py-4 px-4 text-center">
                                    <span class="bg-gray-100 text-black px-3 py-1 rounded-full font-bold text-[12px]">
                                        <span x-text="p.total_log"></span>/12
                                    </span>
                                </td>
                                <td class="py-4 px-4 text-center">
                                    <template x-if="p.status_label === '-'">
                                        <span class="text-black/40 font-bold">-</span>
                                    </template>
                                    <template x-if="p.status_label === 'Menunggu pengecekan'">
                                        <span class="bg-[#FDE68A] text-[#92400E] font-bold px-4 py-1.5 rounded-full text-[10px] uppercase whitespace-nowrap shadow-sm">
                                            Menunggu
                                        </span>
                                    </template>
                                    <template x-if="p.status_label === 'Diperiksa'">
                                        <span class="bg-[#86EFAC] text-[#166534] font-bold px-4 py-1.5 rounded-full text-[10px] uppercase flex items-center justify-center w-max mx-auto gap-1.5 shadow-sm">
                                            <div class="w-1.5 h-1.5 rounded-full bg-green-600"></div> Selesai
                                        </span>
                                    </template>
                                </td>
                                <td class="py-4 px-4 text-center">
                                    <template x-if="p.id">
                                        <a :href="p.detail_url" class="bg-[#3B5998] hover:bg-blue-800 text-white text-[11px] font-bold px-6 py-2 rounded-full inline-block shadow-md transition-all uppercase tracking-wide">
                                            Detail
                                        </a>
                                    </template>
                                    <template x-if="!p.id">
                                        <span class="text-gray-400 italic text-[10px] uppercase font-bold tracking-tight">Belum Daftar</span>
                                    </template>
                                </td>
                            </tr>
                        </template>
                        <template x-if="filteredList.length === 0">
                            <tr>
                                <td colspan="8" class="text-center py-16 text-gray-400 italic bg-gray-50 uppercase tracking-widest font-medium">
                                    Tidak ada data mahasiswa bimbingan
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="flex justify-start gap-4 mt-8 py-2">
            <button class="bg-[#38913B] hover:bg-green-700 text-white px-8 py-2.5 rounded-[5px] font-bold text-[13px] flex items-center gap-2 shadow-md transition-all uppercase">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                Sahkan Terpilih
            </button>
            <button class="bg-[#EA3323] hover:bg-red-600 text-white px-8 py-2.5 rounded-[5px] font-bold text-[13px] flex items-center gap-2 shadow-md transition-all uppercase">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path></svg>
                Tolak Terpilih
            </button>
        </div>
    </div>
</x-dashboard-layout>