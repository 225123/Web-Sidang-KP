<x-dashboard-layout header="Daftar Bimbingan Mahasiswa" userName="{{ auth()->user()->name }}" roleName="KOORDINATOR KP">
    <x-slot:sidebar>
        @include('koordinator.components.sidebar', ['active' => 'bimbingan-saya'])
        </x-slot>

        <div class="mt-6">
            <div class="flex flex-col xl:flex-row gap-6 mb-8 items-start xl:items-stretch">
                <div
                    class="flex-1 bg-[#EAEFFF] border border-[#BACDFB] rounded-[10px] p-4 flex items-center gap-4 shadow-sm">
                    <div
                        class="bg-[#7896F8] w-6 h-6 rounded-full flex items-center justify-center text-white shrink-0 shadow-sm font-serif italic text-sm">
                        i</div>
                    <p class="text-[14px] text-black font-medium leading-relaxed">
                        Silahkan meninjau Jumlah Mahasiswa bimbingan Anda dan Lakukan Verifikasi Bimbingan Di dalam Log
                        Bimbingan Tiap Mahasiswa
                    </p>
                </div>

                <div class="flex gap-4">
                    <div
                        class="bg-[#38913B] rounded-[10px] p-3 flex flex-col justify-center items-center w-[100px] shadow-sm text-white">
                        <div class="flex items-center gap-2">
                            <div class="bg-white/20 p-1 rounded-full">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 13l4 4L19 7"></path>
                                </svg>
                            </div>
                            <span class="text-[24px] font-bold">{{ $jumlahSelesai ?? 9 }}</span>
                        </div>
                        <span class="text-[12px] font-medium mt-1">Selesai</span>
                    </div>
                    <div
                        class="bg-[#FBC610] rounded-[10px] p-3 flex flex-col justify-center items-center w-[100px] shadow-sm text-black">
                        <div class="flex items-center gap-2">
                            <div class="border border-black p-1 rounded-sm">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z">
                                    </path>
                                </svg>
                            </div>
                            <span class="text-[24px] font-bold">{{ $jumlahBelumDiperiksa ?? 3 }}</span>
                        </div>
                        <span class="text-[12px] font-medium text-center leading-tight mt-1">Belum<br>diperiksa</span>
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
                <button class="bg-[#EA3323] text-white font-medium text-sm px-4 py-1.5 rounded shadow-sm">
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
                    class="block w-full pl-10 pr-10 py-2.5 border border-gray-300 rounded-lg text-sm text-gray-800 focus:outline-none"
                    placeholder="Cari berdasarkan Nama Mahasiswa, NIM , atau Nama Dosen...">
                <div class="absolute inset-y-0 right-0 pr-3 flex items-center">
                    <span class="text-lg text-gray-400">></span>
                </div>
            </div>

            <div class="bg-white border border-gray-200 rounded-lg overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full border-collapse text-sm">
                        <thead>
                            <tr class="bg-[#EBEBEB] text-gray-700">
                                <th class="py-3 px-4 font-semibold text-center w-[5%]">No</th>
                                <th class="py-3 px-4 font-semibold text-center">Mahasiswa</th>
                                <th class="py-3 px-4 font-semibold text-center">Judul KP</th>
                                <th class="py-3 px-4 font-semibold text-center">Instansi</th>
                                <th class="py-3 px-4 font-semibold text-center">Supervisor</th>
                                <th class="py-3 px-4 font-semibold text-center">Total Bimbingan</th>
                                <th class="py-3 px-4 font-semibold text-center">Status Approval</th>
                                <th class="py-3 px-4 font-semibold text-center">Log Bimbingan</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 text-center">
                            @forelse($pendaftarans ?? [] as $index => $pendaftaran)
                                <tr class="hover:bg-gray-50">
                                    <td class="py-3 px-4">
                                        <input type="checkbox" class="rounded border-gray-300 w-4 h-4">
                                    </td>
                                    <td class="py-3 px-4 text-left">
                                        <div class="font-semibold text-black">
                                            {{ $pendaftaran->display_mahasiswa->user->name ?? 'User' }}</div>
                                        <div class="text-gray-500 text-xs">{{ $pendaftaran->display_mahasiswa->nim ?? '-' }}</div>
                                    </td>
                                    <td class="py-3 px-4 text-gray-700">{{ Str::limit($pendaftaran->display_judul_kp ?? '-', 30) }}</td>
                                    <td class="py-3 px-4 text-gray-700">{{ $pendaftaran->display_instansi ?? '-' }}</td>
                                    <td class="py-3 px-4 text-gray-700">{{ Auth::user()->name ?? '-' }}</td>
                                    <td class="py-3 px-4 font-medium text-gray-700">{{ $pendaftaran->total_log ?? 0 }}/12</td>
                                    <td class="py-3 px-4">
                                        @if($pendaftaran->status_approval_semua == '-')
                                            <span class="text-gray-500 font-bold text-lg">-</span>
                                        @elseif($pendaftaran->status_approval_semua == 'Menunggu pengecekan')
                                            <span
                                                class="bg-[#FDE68A] text-[#92400E] font-semibold px-4 py-1.5 rounded-full text-xs">Menunggu
                                                pengecekan</span>
                                        @else
                                            <span
                                                class="bg-[#86EFAC] text-[#166534] font-semibold px-4 py-1.5 rounded-full text-xs flex items-center justify-center w-max mx-auto gap-1">
                                                <div class="w-2 h-2 rounded-full bg-green-600"></div> Diperiksa
                                            </span>
                                        @endif
                                    </td>
                                    <td class="py-3 px-4">
                                        <a href="{{ route('koordinator.bimbingan-saya.detail', $pendaftaran->id) }}"
                                            class="bg-[#3B5998] hover:bg-blue-800 text-white text-xs font-semibold px-5 py-1.5 rounded-full inline-block">
                                            Detail
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center py-6 text-gray-500">Tidak ada mahasiswa bimbingan
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="flex justify-start gap-4 mt-6 py-2">
                <button
                    class="bg-[#38913B] hover:bg-green-700 text-white px-6 py-2 rounded font-semibold text-sm flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    Sahkan
                </button>
                <button
                    class="bg-[#EA3323] hover:bg-red-600 text-white px-6 py-2 rounded font-semibold text-sm flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                        </path>
                    </svg>
                    Tolak
                </button>
            </div>
        </div>
</x-dashboard-layout>