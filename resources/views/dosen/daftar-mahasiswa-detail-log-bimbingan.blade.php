<x-dashboard-layout userName="{{ auth()->user()->name }}" roleName="DOSEN">
    <x-slot:sidebar>
        @include('dosen.components.sidebar', ['active' => 'daftar-mahasiswa'])
    </x-slot>

    <x-slot:header>
        <div class="flex items-center gap-4">
            <a href="{{ route('dosen.daftar-mahasiswa') }}" class="p-2 hover:bg-gray-100 rounded-full transition-all group shadow-sm border border-gray-200 bg-white">
                <svg class="w-5 h-5 text-black group-hover:-translate-x-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7" />
                </svg>
            </a>
            <h2 class="text-2xl font-bold text-black tracking-tight uppercase">Log Bimbingan</h2>
        </div>
    </x-slot:header>

    <div class="mt-6" x-data="{ previewImage: null }">
        <!-- Info Cards -->
        <div class="flex flex-col xl:flex-row gap-6 mb-8 items-start xl:items-stretch">
            <div class="flex-1 bg-[#EAEFFF] border border-[#BACDFB] rounded-[10px] p-5 flex items-center gap-4 shadow-sm">
                <div class="bg-[#7896F8] w-7 h-7 rounded-full flex items-center justify-center text-white shrink-0 shadow-sm font-serif italic text-sm">i</div>
                <p class="text-[14px] text-black font-medium leading-relaxed">
                    Tinjau riwayat bimbingan mahasiswa di bawah ini. Anda dapat memberikan persetujuan atau penolakan pada tiap log bimbingan.
                </p>
            </div>

            <div class="flex gap-4">
                <div class="bg-[#38913B] rounded-[10px] p-3 flex flex-col justify-center items-center w-[95px] shadow-sm text-white">
                    <div class="flex items-center gap-2">
                        <div class="bg-white/20 p-1 rounded-full"><svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg></div>
                        <span class="text-xl font-bold">{{ $jumlahDiterima ?? 0 }}</span>
                    </div>
                    <span class="text-[11px] font-medium mt-1">Diterima</span>
                </div>
                <div class="bg-[#FBC610] rounded-[10px] p-3 flex flex-col justify-center items-center w-[95px] shadow-sm text-black">
                    <div class="flex items-center gap-2">
                        <div class="border border-black p-0.5 rounded-sm"><svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"></path></svg></div>
                        <span class="text-xl font-bold">{{ $jumlahBelumDiperiksa ?? 0 }}</span>
                    </div>
                    <span class="text-[11px] font-medium text-center leading-tight mt-1">Belum<br>Diperiksa</span>
                </div>
                <div class="bg-[#EA3323] rounded-[10px] p-3 flex flex-col justify-center items-center w-[95px] shadow-sm text-white">
                    <div class="flex items-center gap-2">
                        <div class="bg-white/20 p-1 rounded-full"><svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg></div>
                        <span class="text-xl font-bold">{{ $jumlahDitolak ?? 0 }}</span>
                    </div>
                    <span class="text-[11px] font-medium mt-1">Ditolak</span>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-[15px] border border-gray-200 shadow-sm overflow-hidden p-6">
            <!-- Student Profile Header -->
            <div class="flex flex-col md:flex-row justify-between items-start gap-6 mb-8 border-b border-gray-100 pb-6">
                <div>
                    <h3 class="text-[16px] font-bold text-black uppercase tracking-tight mb-2">Profil Mahasiswa Bimbingan :</h3>
                    <div class="space-y-1">
                        <div class="flex items-center gap-2 text-[14px]">
                            <span class="text-black/60 font-medium w-28">Nama/NIM</span>
                            <span class="text-black font-bold">: {{ $pendaftaran->display_mahasiswa->user->name ?? 'User' }} ({{ $pendaftaran->display_mahasiswa->nim ?? '-' }})</span>
                        </div>
                        <div class="flex items-start gap-2 text-[14px]">
                            <span class="text-black/60 font-medium w-28">Judul KP</span>
                            <span class="text-black font-medium">: {{ $pendaftaran->display_judul_kp ?? '-' }}</span>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 rounded-[10px] border border-gray-100 flex flex-col items-center justify-center min-w-[120px]">
                    <span class="text-[10px] text-black/40 font-bold uppercase tracking-wider mb-1">Status Selesai</span>
                    <span class="text-[18px] font-black text-black">{{ $jumlahDiterima }}/12</span>
                    <div class="w-full h-1 bg-gray-200 rounded-full mt-2 overflow-hidden">
                        <div class="h-full bg-green-500 rounded-full" style="width: {{ min(100, ($jumlahDiterima / 12) * 100) }}%"></div>
                    </div>
                </div>
            </div>

            <!-- Table -->
            <div class="border border-gray-200 rounded-[10px] overflow-hidden">
                <table class="w-full border-collapse text-[13px]">
                    <thead>
                        <tr class="bg-[#EBEBEB] text-black">
                            <th class="py-4 px-4 font-bold text-center w-[100px] border-b border-gray-300">Tanggal</th>
                            <th class="py-4 px-4 font-bold text-center w-[150px] border-b border-gray-300">Waktu & Tempat</th>
                            <th class="py-4 px-4 font-bold text-left border-b border-gray-300">Detail Pembahasan</th>
                            <th class="py-4 px-4 font-bold text-center w-[120px] border-b border-gray-300">Lampiran</th>
                            <th class="py-4 px-4 font-bold text-center w-[180px] border-b border-gray-300">Status & Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($pendaftaran->logBimbingans as $log)
                            @php $materi = json_decode($log->materi_bahasan, true); @endphp
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="py-4 px-4 text-center text-black font-bold">
                                    {{ \Carbon\Carbon::parse($log->tanggal)->format('d/m/Y') }}
                                </td>
                                <td class="py-4 px-4 text-center">
                                    <div class="text-blue-700 font-bold">{{ $materi['waktuMulai'] ?? '00:00' }} - {{ $materi['waktuSelesai'] ?? '00:00' }}</div>
                                    <div class="text-[11px] text-black/50 italic">{{ $materi['tempat'] ?? '-' }}</div>
                                </td>
                                <td class="py-4 px-4 text-left">
                                    <div class="font-bold text-black mb-1 uppercase text-[12px] tracking-tight">{{ $materi['topik'] ?? 'Topik' }}</div>
                                    <div class="text-black/70 leading-relaxed text-[12px]">{{ $materi['detail'] ?? '-' }}</div>
                                </td>
                                <td class="py-4 px-4 text-center">
                                    @if($log->file_progress)
                                        <div class="w-16 h-12 mx-auto rounded-lg border border-gray-200 overflow-hidden bg-gray-50 cursor-pointer shadow-sm hover:ring-2 hover:ring-blue-500 transition-all"
                                            @click="previewImage = '{{ asset('storage/' . $log->file_progress) }}'">
                                            <img src="{{ asset('storage/' . $log->file_progress) }}" class="w-full h-full object-cover">
                                        </div>
                                    @else
                                        <div class="w-14 h-10 mx-auto rounded border border-dashed border-gray-300 flex items-center justify-center text-[9px] text-black/30 font-bold uppercase text-center leading-tight">No<br>File</div>
                                    @endif
                                </td>
                                <td class="py-4 px-4 text-center">
                                    @if($log->status_approval == 'pending')
                                        <div class="flex items-center justify-center gap-2">
                                            <form action="{{ route('dosen.daftar-mahasiswa.updateStatus', $log->id) }}" method="POST">
                                                @csrf @method('PUT')
                                                <input type="hidden" name="status_approval" value="approved">
                                                <button type="submit" class="bg-[#38913B] hover:bg-green-700 text-white text-[10px] font-bold px-4 py-1.5 rounded shadow-sm transition-colors uppercase">Terima</button>
                                            </form>
                                            <form action="{{ route('dosen.daftar-mahasiswa.updateStatus', $log->id) }}" method="POST">
                                                @csrf @method('PUT')
                                                <input type="hidden" name="status_approval" value="rejected">
                                                <button type="submit" class="bg-[#EA3323] hover:bg-red-700 text-white text-[10px] font-bold px-4 py-1.5 rounded shadow-sm transition-colors uppercase">Tolak</button>
                                            </form>
                                        </div>
                                    @elseif($log->status_approval == 'approved')
                                        <span class="inline-flex items-center gap-1.5 bg-green-100 text-green-700 px-4 py-1.5 rounded-full font-bold text-[10px] uppercase shadow-sm">
                                            <div class="w-1.5 h-1.5 rounded-full bg-green-500"></div> Selesai
                                        </span>
                                    @elseif($log->status_approval == 'rejected')
                                        <span class="inline-flex items-center gap-1.5 bg-red-100 text-red-700 px-4 py-1.5 rounded-full font-bold text-[10px] uppercase shadow-sm">
                                            <div class="w-1.5 h-1.5 rounded-full bg-red-500"></div> Ditolak
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-16 text-center text-gray-400 italic bg-gray-50 uppercase tracking-widest font-medium">Belum ada riwayat bimbingan</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>


        </div>

        <!-- Image Preview Modal -->
        <div x-cloak x-show="previewImage" style="display:none;"
            class="fixed inset-0 z-[100] flex items-center justify-center bg-black/60 transition-opacity backdrop-blur-sm p-4">
            <div @click.away="previewImage = null" class="relative max-w-4xl max-h-screen transform transition-all scale-100 shadow-2xl">
                <button @click="previewImage = null" class="absolute -top-3 -right-3 bg-white text-black rounded-full w-8 h-8 flex items-center justify-center font-bold shadow-lg hover:bg-gray-100 transition-colors z-10">✕</button>
                <img :src="previewImage" class="max-w-full max-h-[90vh] object-contain rounded-lg border-4 border-white">
            </div>
        </div>
    </div>
</x-dashboard-layout>