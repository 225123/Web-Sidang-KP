<x-dashboard-layout header="Log Bimbingan" userName="{{ auth()->user()->name }}" roleName="KOORDINATOR KP">
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
                        class="bg-[#38913B] rounded-[10px] p-3 flex flex-col justify-center items-center w-[90px] shadow-sm text-white">
                        <div class="flex items-center gap-2">
                            <div class="bg-white/20 p-1 rounded-full"><svg class="w-3 h-3" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 13l4 4L19 7"></path>
                                </svg></div>
                            <span class="text-xl font-bold">{{ $jumlahDiterima ?? 6 }}</span>
                        </div>
                        <span class="text-[11px] font-medium mt-1">Diterima</span>
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
                            <span class="text-xl font-bold">{{ $jumlahBelumDiperiksa ?? 1 }}</span>
                        </div>
                        <span class="text-[11px] font-medium text-center leading-tight mt-1">Belum<br>diperiksa</span>
                    </div>
                    <div
                        class="bg-[#EA3323] rounded-[10px] p-3 flex flex-col justify-center items-center w-[90px] shadow-sm text-white">
                        <div class="flex items-center gap-2">
                            <div class="bg-white/20 p-1 rounded-full"><svg class="w-3 h-3" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12"></path>
                                </svg></div>
                            <span class="text-xl font-bold">{{ $jumlahDitolak ?? 1 }}</span>
                        </div>
                        <span class="text-[11px] font-medium mt-1">Ditolak</span>
                    </div>
                </div>
            </div>

            <div class="flex items-center gap-6 mb-6">
                <div class="flex items-center gap-2">
                    <label class="text-sm font-bold text-black">Tanggal :</label>
                    <select class="w-[120px] text-sm border border-gray-300 rounded py-1 px-2 bg-white">
                        <option>All</option>
                    </select>
                </div>
                <div class="flex items-center gap-2">
                    <label class="text-sm font-bold text-black">Status :</label>
                    <select class="w-[120px] text-sm border border-gray-300 rounded py-1 px-2 bg-white">
                        <option>All</option>
                    </select>
                </div>
                <button class="bg-[#EA3323] text-white font-medium text-xs px-4 py-1.5 rounded">Clear Filter</button>
            </div>

            <div class="flex justify-between items-center mb-2">
                <h3 class="font-bold text-black text-[15px]">Riwayat Bimbingan :</h3>
                <a href="{{ route('koordinator.bimbingan-saya') }}" class="text-black font-bold">></a>
            </div>

            <div x-data="{ previewImage: null }" class="bg-[#EBEBEB] rounded-2xl p-6 shadow-sm border border-gray-200">
                <div class="grid grid-cols-[130px_auto] gap-y-1 mb-6 text-sm font-medium text-black">
                    <div>Nama Mahasiswa</div>
                    <div>: {{ $pendaftaran->display_mahasiswa->user->name ?? 'User' }} -
                        {{ $pendaftaran->display_mahasiswa->nim ?? '-' }}
                    </div>
                    <div>Judul KP</div>
                    <div>: {{ $pendaftaran->display_judul_kp ?? '-' }}</div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-center border-collapse">
                        <thead>
                            <tr class="border-y border-gray-300 text-sm text-black">
                                <th class="py-3 px-2 font-semibold">Tanggal</th>
                                <th class="py-3 px-2 font-semibold">Waktu dan Tempat</th>
                                <th class="py-3 px-2 font-semibold text-left w-[40%]">Logbook Pembahasan</th>
                                <th class="py-3 px-2 font-semibold">Bukti - gambar</th>
                                <th class="py-3 px-2 font-semibold">Status</th>
                            </tr>
                        </thead>
                        <tbody class="text-sm text-black divide-y divide-gray-300">
                            @forelse($pendaftaran->logBimbingans as $log)
                                @php $materi = json_decode($log->materi_bahasan, true); @endphp
                                <tr class="hover:bg-gray-100 transition-colors">
                                    <td class="py-4 px-2">{{ \Carbon\Carbon::parse($log->tanggal)->format('d/m/Y') }}</td>
                                    <td class="py-4 px-2">
                                        {{ $materi['waktuMulai'] ?? '07' }}-{{ $materi['waktuSelesai'] ?? '08' }} <br>
                                        ({{ $materi['tempat'] ?? '-' }})
                                    </td>
                                    <td class="py-4 px-2 text-left">
                                        {{ Str::limit($materi['detail'] ?? '-', 120) }}
                                    </td>
                                    <td class="py-4 px-2">
                                        @if($log->file_progress)
                                            <div class="w-12 h-8 mx-auto rounded overflow-hidden bg-[#8A9CFF] cursor-pointer shadow-sm hover:opacity-80 transition-opacity"
                                                @click="previewImage = '{{ asset('storage/' . $log->file_progress) }}'">
                                                <img src="{{ asset('storage/' . $log->file_progress) }}"
                                                    class="w-full h-full object-cover">
                                            </div>
                                        @else
                                            <div class="w-12 h-8 mx-auto rounded bg-[#8A9CFF] flex items-center justify-center text-[8px] text-white">No Img</div>
                                        @endif
                                    </td>
                                    <td class="py-4 px-2">
                                        @if($log->status_approval == 'pending')
                                            <div class="flex items-center justify-center gap-2">
                                                <form action="{{ route('koordinator.bimbingan-saya.updateStatus', $log->id) }}"
                                                    method="POST" class="inline">
                                                    @csrf @method('PUT')
                                                    <input type="hidden" name="status_approval" value="rejected">
                                                    <button type="submit"
                                                        class="bg-[#EA3323] hover:bg-red-700 text-white text-xs font-semibold px-4 py-1.5 rounded">Tolak</button>
                                                </form>
                                                <form action="{{ route('koordinator.bimbingan-saya.updateStatus', $log->id) }}"
                                                    method="POST" class="inline">
                                                    @csrf @method('PUT')
                                                    <input type="hidden" name="status_approval" value="approved">
                                                    <button type="submit"
                                                        class="bg-[#38913B] hover:bg-green-700 text-white text-xs font-semibold px-4 py-1.5 rounded">Terima</button>
                                                </form>
                                            </div>
                                        @elseif($log->status_approval == 'approved')
                                            <div
                                                class="flex items-center justify-center gap-2 text-[#38913B] font-semibold text-sm">
                                                <div class="w-3 h-3 rounded-full bg-[#86EFAC]"></div> Diterima
                                            </div>
                                        @elseif($log->status_approval == 'rejected')
                                            <div
                                                class="flex items-center justify-center gap-2 text-[#EA3323] font-semibold text-sm">
                                                <div class="w-3 h-3 rounded-full bg-[#FCA5A5]"></div> Ditolak
                                            </div>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="py-6 text-center text-gray-500">Belum ada riwayat bimbingan.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="mt-4 flex justify-end text-sm text-black font-semibold">
                    Total Bimbingan : {{ $pendaftaran->logBimbingans->count() }}
                </div>

                <div x-show="previewImage" style="display:none;"
                    class="fixed inset-0 z-[100] flex items-center justify-center bg-black bg-opacity-70 transition-opacity backdrop-blur-sm p-4">
                    <div @click.away="previewImage = null" class="relative max-w-4xl max-h-screen">
                        <button @click="previewImage = null"
                            class="absolute -top-4 -right-4 bg-white text-black rounded-full w-8 h-8 flex items-center justify-center font-bold shadow-lg hover:bg-gray-200">X</button>
                        <img :src="previewImage" class="max-w-full max-h-[90vh] object-contain rounded-md shadow-2xl">
                    </div>
                </div>

            </div>
        </div>
</x-dashboard-layout>