<x-dashboard-layout header="Jadwal Sidang & Menguji" userName="{{ auth()->user()->name }}" roleName="KOORDINATOR KP">
    <x-slot:sidebar>
        @include('koordinator.components.sidebar', ['active' => 'jadwal-menguji'])
    </x-slot>

    <style>
        .sentence-case { text-transform: lowercase; }
        .sentence-case::first-letter { text-transform: uppercase; }
    </style>

    <div class="mt-6" x-data="{ 
        searchQuery: '',
        sidangs: {{ $sidangs->map(fn($s) => [
            'id' => $s->id,
            'nama' => $s->mahasiswa->user->name ?? 'User',
            'nim' => $s->mahasiswa->nim ?? '-',
            'judul' => $s->pendaftaranKp->judul_kp ?? '-',
            'tanggal_iso' => $s->tanggal_sidang,
            'tanggal_indo' => \Carbon\Carbon::parse($s->tanggal_sidang)->translatedFormat('l, d F Y'),
            'waktu' => ($s->waktu_mulai_sidang ? substr($s->waktu_mulai_sidang, 0, 5) : '-') . ' - ' . ($s->waktu_selesai_sidang ? substr($s->waktu_selesai_sidang, 0, 5) : '-'),
            'ruang' => $s->ruang_sidang ? strtoupper($s->ruang_sidang) : '-',
            'peran' => ($s->penguji_1_id == auth()->id()) ? 'PENGUJI 1' : (($s->penguji_2_id == auth()->id()) ? 'PENGUJI 2' : 'PEMBIMBING'),
        ])->toJson() }},
        get filteredList() {
            return this.sidangs.filter(s => {
                return !this.searchQuery || 
                    s.nama.toLowerCase().includes(this.searchQuery.toLowerCase()) ||
                    s.nim.toLowerCase().includes(this.searchQuery.toLowerCase()) ||
                    s.judul.toLowerCase().includes(this.searchQuery.toLowerCase());
            });
        }
    }">
        <div class="flex flex-col xl:flex-row gap-6 mb-8 items-start xl:items-stretch">
            <div class="flex-1 bg-[#EAEFFF] border border-[#BACDFB] rounded-[10px] p-4 flex items-center gap-4 shadow-sm">
                <div class="bg-[#7896F8] w-6 h-6 rounded-full flex items-center justify-center text-white shrink-0 shadow-sm font-serif italic text-sm">i</div>
                <p class="text-[14px] text-black font-normal leading-relaxed">
                    Daftar jadwal sidang kerja praktek. Silakan periksa peran dan lokasi ruangan anda.
                </p>
            </div>
        </div>

        <!-- Search Bar -->
        <div class="bg-white p-4 rounded-[10px] border border-gray-200 shadow-sm mb-6">
            <div class="relative flex-1 w-full">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </div>
                <input type="text" x-model="searchQuery"
                    class="block w-full pl-10 pr-4 py-2 border border-gray-300 rounded-[5px] text-sm text-black focus:ring-blue-500 focus:border-blue-500 font-medium"
                    placeholder="Cari Nama Mahasiswa, NIM, atau Judul KP...">
            </div>
        </div>

        <div class="bg-white border border-gray-200 rounded-[10px] overflow-hidden shadow-sm">
            <div class="overflow-x-auto">
                <table class="w-full border-collapse text-[13px]">
                    <thead>
                        <tr class="bg-[#EBEBEB] text-black h-[50px]">
                            <th class="px-4 font-normal text-center w-[50px]">No</th>
                            <th class="px-4 font-normal text-left w-[220px]">Tanggal sidang</th>
                            <th class="px-4 font-normal text-center w-[120px]">Waktu</th>
                            <th class="px-4 font-normal text-center w-[120px]">Ruangan</th>
                            <th class="px-4 font-normal text-center w-[150px]">Peran sidang</th>
                            <th class="px-4 font-normal text-left w-[250px]">Mahasiswa</th>
                            <th class="px-4 font-normal text-left">Judul kp</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        <template x-for="(s, index) in filteredList" :key="s.id">
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="py-5 px-4 text-center text-black font-normal" x-text="index+1"></td>
                                <td class="py-5 px-4 text-left font-normal text-black" x-text="s.tanggal_indo"></td>
                                <td class="py-5 px-4 text-center font-normal text-black" x-text="s.waktu"></td>
                                <td class="py-5 px-4 text-center font-normal text-black" x-text="s.ruang"></td>
                                <td class="py-5 px-4 text-center">
                                    <span class="bg-gray-100 text-black font-normal px-3 py-1 rounded text-[11px] whitespace-nowrap uppercase tracking-wider" x-text="s.peran"></span>
                                </td>
                                <td class="py-5 px-4 text-left font-normal">
                                    <div class="font-normal text-[13px] text-black sentence-case" x-text="s.nama"></div>
                                    <div class="text-gray-500 text-[11px] font-normal" x-text="s.nim"></div>
                                </td>
                                <td class="py-5 px-4 text-left">
                                    <div class="text-[12px] font-normal text-black leading-snug sentence-case" x-text="s.judul"></div>
                                </td>
                            </tr>
                        </template>
                        <template x-if="filteredList.length === 0">
                            <tr>
                                <td colspan="7" class="text-center py-16 text-gray-500 italic bg-gray-50 font-medium tracking-widest uppercase">
                                    Tidak ada jadwal sidang yang ditemukan.
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-dashboard-layout>
