<x-dashboard-layout header="Persetujuan Sidang KP" userName="{{ auth()->user()->name }}" roleName="KOORDINATOR KP">
    <x-slot:sidebar>
        @include('koordinator.components.sidebar', ['active' => 'persetujuan-sidang'])
        </x-slot>

        <div class="mt-6" x-data="{ showTolakModal: false, selectedId: null }">
            <div class="flex flex-col xl:flex-row gap-6 mb-8 items-start xl:items-stretch">
                <div
                    class="flex-1 bg-[#EAEFFF] border border-[#BACDFB] rounded-[10px] p-4 flex items-center gap-4 shadow-sm">
                    <div
                        class="bg-[#7896F8] w-6 h-6 rounded-full flex items-center justify-center text-white shrink-0 shadow-sm font-serif italic text-sm">
                        i</div>
                    <p class="text-[14px] text-black font-medium leading-relaxed">
                        Berikut adalah daftar mahasiswa bimbingan Anda yang sedang mengajukan permohonan persetujuan
                        untuk mendaftar Sidang KP
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
                            <span class="text-xl font-bold">{{ $jumlahDisetujui ?? 8 }}</span>
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
                            <span class="text-xl font-bold">{{ $jumlahDitolak ?? 4 }}</span>
                        </div>
                        <span class="text-[11px] font-medium mt-1">Ditolak</span>
                    </div>
                </div>
            </div>

            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-6">
                <div class="flex items-center gap-6">
                    <div class="flex items-center gap-2">
                        <label class="text-sm font-bold text-black">Tanggal :</label>
                        <select class="w-[130px] text-sm border border-gray-300 rounded py-1.5 px-2 bg-white">
                            <option>DD/MM/YY</option>
                        </select>
                    </div>
                    <div class="flex items-center gap-2">
                        <label class="text-sm font-bold text-black">Aksi :</label>
                        <select class="w-[130px] text-sm border border-gray-300 rounded py-1.5 px-2 bg-white">
                            <option>All</option>
                        </select>
                    </div>
                </div>
                <button
                    class="bg-[#EA3323] text-white font-medium text-sm px-4 py-1.5 rounded shadow-sm hover:bg-red-700">
                    Clear Filter
                </button>
            </div>

            <div class="relative mb-6">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </div>
                <input type="text"
                    class="block w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg text-sm text-gray-800 focus:outline-none"
                    placeholder="Cari berdasarkan Nama Mahasiswa, NIM, atau Nama Dosen ..">
            </div>

            @if(session('success'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif

            <div class="bg-white border border-gray-200 rounded-lg overflow-hidden shadow-sm">
                <div class="overflow-x-auto">
                    <table class="w-full border-collapse text-sm">
                        <thead>
                            <tr class="bg-[#EBEBEB] text-gray-700 border-b border-gray-300">
                                <th class="py-3 px-4 font-semibold text-center w-[5%] border-r border-gray-300">No</th>
                                <th class="py-3 px-4 font-semibold text-center border-r border-gray-300">Mahasiswa</th>
                                <th class="py-3 px-4 font-semibold text-center border-r border-gray-300">Judul KP</th>
                                <th class="py-3 px-4 font-semibold text-center border-r border-gray-300 w-[25%]">Laporan
                                    KP</th>
                                <th class="py-3 px-4 font-semibold text-center border-r border-gray-300">Total Bimbingan
                                </th>
                                <th class="py-3 px-4 font-semibold text-center border-r border-gray-300 w-[15%]">Status
                                    Approval</th>
                                <th class="py-3 px-4 font-semibold text-center">Log Bimbingan</th>
                            </tr>
                        </thead>
                        <tbody class="text-center text-gray-800">
                            @forelse($pengajuans as $index => $pengajuan)
                                <tr class="border-b border-gray-200 hover:bg-gray-50 h-[45px]">
                                    <td class="py-3 px-4 border-r border-gray-200">
                                        <input type="checkbox" class="rounded border-gray-300 w-4 h-4">
                                    </td>
                                    <td class="py-3 px-4 border-r border-gray-200 text-left">
                                        <div class="font-bold">
                                            {{ $pengajuan->mahasiswa?->user?->name ?? 'User' }}</div>
                                        <div class="text-gray-500 text-[11px]">
                                            {{ $pengajuan->mahasiswa?->nim ?? '-' }}</div>
                                    </td>
                                    <td class="py-3 px-4 border-r border-gray-200 text-[13px]">
                                        {{ $pengajuan->pendaftaranKp?->judul_kp ?? '-' }}</td>
                                    <td class="py-3 px-4 border-r border-gray-200 text-[12px] text-blue-600">
                                        @if($pengajuan->file_laporan)
                                            <a href="{{ asset('storage/' . $pengajuan->file_laporan) }}" target="_blank"
                                                class="hover:underline font-medium italic">Lihat Dokumen Laporan</a>
                                        @elseif($pengajuan->link_github)
                                            <a href="{{ $pengajuan->link_github }}" target="_blank"
                                                class="hover:underline font-medium italic">Link Drive / GDrive</a>
                                        @endif
                                    </td>
                                    <td class="py-3 px-4 border-r border-gray-200 text-[13px]">
                                        {{ $pengajuan->pendaftaranKp?->logBimbingans?->count() ?? 0 }}/12
                                    </td>
                                    <td class="py-3 px-4 border-r border-gray-200">
                                        @if($pengajuan->status_verifikasi == 'pending')
                                            <div class="flex items-center justify-center gap-2">
                                                <form action="{{ route('koordinator.persetujuan-sidang.update', $pengajuan->id) }}"
                                                    method="POST">
                                                    @csrf @method('PUT')
                                                    <button type="submit"
                                                        class="bg-[#38913B] text-white text-[11px] font-bold px-3 py-1.5 rounded flex items-center gap-1">
                                                        <svg class="w-3 h-3" fill="none" stroke="currentColor"
                                                            viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                        </svg>
                                                        Sahkan
                                                    </button>
                                                </form>
                                                <button type="button"
                                                    @click="showTolakModal = true; selectedId = {{ $pengajuan->id }}"
                                                    class="bg-[#EA3323] text-white text-[11px] font-bold px-3 py-1.5 rounded flex items-center gap-1">
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M6 18L18 6M6 6l12 12"></path>
                                                    </svg>
                                                    Tolak
                                                </button>
                                            </div>
                                        @elseif($pengajuan->status_verifikasi == 'verified')
                                            <div
                                                class="bg-[#A7F3D0] text-[#065F46] font-bold text-[12px] px-4 py-1.5 rounded-full inline-flex items-center gap-1.5">
                                                <div class="w-2 h-2 rounded-full bg-green-600"></div> Selesai
                                            </div>
                                        @endif
                                    </td>
                                    <td class="py-3 px-4">
                                        <a href="#"
                                            class="bg-[#3B5998] text-white text-[11px] font-semibold px-4 py-1.5 rounded-full inline-block">Detail</a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="py-10 text-gray-400 italic">Belum ada pengajuan yang masuk untuk
                                        mahasiswa bimbingan Anda.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="p-4 flex justify-between items-center bg-white border-t border-gray-200">
                    <span class="text-xs text-gray-500 font-medium">1 - {{ count($pengajuans ?? []) }} of
                        {{ count($pengajuans ?? []) }} entries</span>
                    <div class="flex gap-1 border border-gray-200 rounded p-0.5 bg-white shadow-sm text-sm">
                        <button
                            class="px-2 py-1 text-gray-400 bg-transparent disabled:opacity-50 hover:bg-gray-50 rounded"
                            disabled>
                            << /button>
                                <button class="px-2.5 py-1 text-white bg-[#3B5998] rounded">1</button>
                                <button
                                    class="px-2.5 py-1 text-gray-600 bg-transparent hover:bg-gray-50 rounded">2</button>
                                <button
                                    class="px-2.5 py-1 text-gray-600 bg-transparent hover:bg-gray-50 rounded">3</button>
                                <button
                                    class="px-2 py-1 text-gray-600 bg-transparent hover:bg-gray-50 rounded">...</button>
                                <button
                                    class="px-2 py-1 text-gray-600 bg-transparent hover:bg-gray-50 rounded">></button>
                    </div>
                </div>
            </div>

            <div x-show="showTolakModal" style="display:none;"
                class="fixed inset-0 z-[100] flex items-center justify-center bg-black/50 backdrop-blur-sm p-4">
                <div @click.away="showTolakModal = false"
                    class="bg-white w-full max-w-md rounded-[10px] shadow-xl overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200 bg-red-50 flex justify-between items-center">
                        <h2 class="text-lg font-bold text-red-700 flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z">
                                </path>
                            </svg>
                            Tolak Pengajuan
                        </h2>
                        <button @click="showTolakModal = false" class="text-gray-400 hover:text-red-500"><svg
                                class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12"></path>
                            </svg></button>
                    </div>

                    <form :action="'{{ url('koordinator/persetujuan-sidang/tolak') }}/' + selectedId" method="POST"
                        class="p-6">
                        @csrf
                        @method('DELETE')
                        <p class="text-sm text-gray-700 mb-4">Berikan alasan penolakan agar mahasiswa dapat memperbaiki
                            laporannya. Data pengajuan ini akan dihapus dari antrean agar mahasiswa bisa mengunggah
                            ulang.</p>

                        <textarea name="feedback" required rows="4"
                            class="w-full border border-gray-300 rounded-[5px] p-3 text-sm focus:ring-1 focus:ring-red-500 outline-none resize-none mb-6"
                            placeholder="Misal: Bab 3 masih kurang lengkap, harap lengkapi logbook..."></textarea>

                        <div class="flex justify-end gap-3">
                            <button type="button" @click="showTolakModal = false"
                                class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-800 text-sm font-bold rounded">Batal</button>
                            <button type="submit"
                                class="px-4 py-2 bg-[#EA3323] hover:bg-red-700 text-white text-sm font-bold rounded shadow-sm">Kirim
                                Penolakan & Hapus</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
</x-dashboard-layout>