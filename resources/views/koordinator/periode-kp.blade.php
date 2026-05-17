<x-dashboard-layout userName="{{ auth()->user()->name }}" roleName="KOORDINATOR KP" hidePeriodSelector="true">
    <x-slot name="sidebar">
        @include('koordinator.components.sidebar', ['active' => 'periode-kp'])
    </x-slot>

    <x-slot name="header">Manajemen Periode KP</x-slot>

    <div class="max-w-6xl mx-auto space-y-6" x-data="periodeController()">
        @if(session('success'))
            <div class="bg-green-50 border border-green-200 text-green-800 px-5 py-4 rounded-xl flex items-center gap-3">
                <svg class="w-5 h-5 text-green-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span class="font-medium text-sm">{{ session('success') }}</span>
            </div>
        @endif
        @if(session('error'))
            <div class="bg-red-50 border border-red-200 text-red-800 px-5 py-4 rounded-xl flex items-center gap-3">
                <svg class="w-5 h-5 text-red-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span class="font-medium text-sm">{{ session('error') }}</span>
            </div>
        @endif

        {{-- Top Row --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            {{-- Active Period Card --}}
            <div class="lg:col-span-2 bg-gradient-to-br from-[#4CC098] to-[#2ea87a] rounded-2xl p-6 text-white shadow-lg">
                <p class="text-white/70 text-xs font-bold uppercase tracking-widest mb-2">Periode Aktif Saat Ini</p>
                <h2 class="text-4xl font-black tracking-tight mb-5">
                    @if($aktif)
                        {{ $aktif->label_tahun_ajaran }}
                    @else
                        Belum Ada Periode Aktif
                    @endif
                </h2>
                <div class="border-t border-white/20 pt-5 grid grid-cols-3 gap-4 text-center">
                    <div>
                        <p class="text-2xl font-black">{{ $aktifStats['mahasiswa'] }}</p>
                        <p class="text-white/70 text-xs mt-1 uppercase tracking-wide">Mahasiswa KP</p>
                    </div>
                    <div class="border-x border-white/20">
                        <p class="text-2xl font-black">{{ $aktifStats['dosen'] }}</p>
                        <p class="text-white/70 text-xs mt-1 uppercase tracking-wide">Dosen Pembimbing</p>
                    </div>
                    <div>
                        <p class="text-2xl font-black">{{ $aktifStats['total'] }}</p>
                        <p class="text-white/70 text-xs mt-1 uppercase tracking-wide">Total User</p>
                    </div>
                </div>
            </div>

            {{-- Open New Period --}}
            <div class="bg-white rounded-[15px] border border-gray-200 shadow-sm p-6 flex flex-col justify-between">
                <div>
                    <h3 class="font-bold text-gray-800 text-[16px] mb-1 tracking-tight">Buka Periode Baru</h3>
                    <p class="text-[12px] text-gray-500 mb-4 font-medium">
                        Periode berikutnya: <span class="font-bold text-[#4285F4]">{{ $nextPeriod['label'] }}</span>
                    </p>
                    <p class="text-[12px] text-gray-500 italic mb-5 leading-relaxed">
                        Membuka periode baru akan otomatis menutup periode yang sedang aktif.
                    </p>
                </div>
                <form action="{{ route('koordinator.periode-kp.store') }}" method="POST" id="formBukaPeriode" class="space-y-3">
                    @csrf
                    <input type="hidden" name="semester" value="{{ $nextPeriod['semester'] }}">
                    <input type="hidden" name="tahun" value="{{ $nextPeriod['tahun'] }}">
                    <div class="bg-gray-50 border border-gray-200 rounded-[10px] px-4 py-3 text-center">
                        <p class="text-[12px] text-gray-500 font-medium mb-1">Periode yang akan dibuka</p>
                        <p class="text-[18px] font-black text-gray-800">{{ $nextPeriod['label'] }}</p>
                    </div>
                    <button type="button" @click="bukaPeriode()"
                        class="w-full bg-[#4285F4] hover:bg-blue-600 text-white font-bold text-[13px] py-2.5 rounded-[10px] transition-colors shadow-sm flex items-center justify-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        Buka Periode KP Baru
                    </button>
                </form>
            </div>
        </div>

        {{-- History Table --}}
        <div class="bg-white rounded-[15px] border border-gray-200 shadow-sm overflow-hidden p-6 mb-8">
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between border-b border-gray-200 pb-4 mb-6">
                <div>
                    <h3 class="font-bold text-[16px] text-black tracking-tight">Riwayat Periode KP</h3>
                    <p class="text-[12px] text-black/60 font-medium mt-1">Periode berakhir otomatis saat periode baru dibuka.</p>
                </div>
                <span class="text-[12px] text-black/60 font-bold bg-gray-100 px-3 py-1 rounded-full mt-3 sm:mt-0">{{ $periodes->count() }} Periode</span>
            </div>
            
            <div class="border border-gray-200 rounded-[10px] overflow-hidden">
                <div class="overflow-x-auto custom-scrollbar">
                    <table class="w-full text-left text-[12px] font-medium text-black border-collapse">
                        <thead class="bg-[#EBEBEB]">
                            <tr class="h-[45px] text-black text-center">
                                <th class="border-b border-r border-gray-300 font-bold px-4 text-left">Periode</th>
                                <th class="border-b border-r border-gray-300 font-bold px-4">Mahasiswa Terdaftar</th>
                                <th class="border-b border-r border-gray-300 font-bold px-4">Status</th>
                                <th class="border-b border-gray-300 font-bold px-4">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="align-middle bg-white">
                            @forelse($periodes as $periode)
                                <tr class="hover:bg-gray-50 border-b border-gray-200 transition-colors @if($periode->is_active) bg-blue-50/30 @endif">
                                    <td class="px-4 py-3 border-r border-gray-200">
                                        <div class="flex items-center gap-2">
                                            <div class="w-1.5 h-1.5 rounded-full flex-shrink-0 @if($periode->is_active) bg-blue-500 animate-pulse @else bg-gray-300 @endif"></div>
                                            <span class="font-bold text-black text-[13px]">{{ $periode->label_tahun_ajaran }}</span>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3 border-r border-gray-200 text-center">
                                        <span class="font-bold text-black">{{ $stats[$periode->id] ?? 0 }}</span>
                                        <span class="text-gray-500"> mahasiswa</span>
                                    </td>
                                    <td class="px-4 py-3 border-r border-gray-200 text-center">
                                        @if($periode->is_active)
                                            <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-[#E6F0FA] text-[#4285F4] rounded-[20px] text-[10px] font-bold uppercase shadow-sm">
                                                <span class="w-1.5 h-1.5 bg-[#4285F4] rounded-full animate-pulse"></span>
                                                Aktif
                                            </span>
                                        @else
                                            <span class="px-3 py-1 bg-gray-100 text-gray-500 rounded-[20px] text-[10px] font-bold uppercase shadow-sm border border-gray-200">Selesai</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        @if(!$periode->is_active)
                                            <div class="flex items-center justify-center gap-2">
                                                <button type="button" @click="konfirmasiAksi('aktif', '{{ $periode->id }}', 'Aktifkan kembali periode {{ $periode->label_tahun_ajaran }}?')"
                                                    class="bg-blue-50 hover:bg-blue-100 text-blue-600 border border-blue-200 text-[11px] font-bold px-3 py-1.5 rounded-[5px] transition-colors shadow-sm">
                                                    Jadikan Aktif
                                                </button>
                                                
                                                @if(($stats[$periode->id] ?? 0) === 0)
                                                    <button type="button" @click="konfirmasiAksi('hapus', '{{ $periode->id }}', 'Hapus periode {{ $periode->label_tahun_ajaran }}? Tindakan ini tidak bisa dibatalkan.')"
                                                        class="bg-red-50 hover:bg-red-100 text-red-600 border border-red-200 text-[11px] font-bold px-3 py-1.5 rounded-[5px] transition-colors shadow-sm">
                                                        Hapus
                                                    </button>
                                                @endif
                                            </div>

                                            <form :id="'form-aktif-' + '{{ $periode->id }}'" action="{{ route('koordinator.periode-kp.aktif', $periode->id) }}" method="POST" class="hidden">
                                                @csrf @method('PUT')
                                            </form>

                                            <form :id="'form-hapus-' + '{{ $periode->id }}'" action="{{ route('koordinator.periode-kp.destroy', $periode->id) }}" method="POST" class="hidden">
                                                @csrf @method('DELETE')
                                            </form>
                                        @else
                                            <span class="text-[11px] text-gray-400 italic font-medium">Periode berjalan</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-4 py-16 text-center text-gray-400 bg-gray-50">
                                        <div class="flex flex-col items-center gap-2">
                                            <svg class="w-10 h-10 opacity-40 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                            </svg>
                                            <p class="font-bold text-[12px] tracking-widest uppercase">Belum ada periode KP.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Custom Global Confirm Modal -->
        <div x-cloak x-show="confirmDialog.show" style="display: none;" class="fixed inset-0 z-[1000] flex items-center justify-center bg-black/40 backdrop-blur-sm p-4" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95">
            <div @click.away="confirmDialog.show = false" class="bg-white rounded-[15px] w-full max-w-[420px] p-8 shadow-2xl flex flex-col items-center text-center relative overflow-hidden border border-gray-100">
                
                <!-- Icon Header Based on Type -->
                <div class="mb-6">
                    <template x-if="confirmDialog.type === 'danger'">
                        <div class="w-20 h-20 bg-red-50 rounded-full flex items-center justify-center">
                            <svg class="w-12 h-12 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                        </div>
                    </template>
                    <template x-if="confirmDialog.type === 'info'">
                        <div class="w-20 h-20 bg-blue-50 rounded-full flex items-center justify-center">
                            <svg class="w-12 h-12 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                    </template>
                </div>

                <h3 class="text-[18px] font-bold text-gray-900 mb-3" x-text="confirmDialog.title"></h3>
                <p class="text-[14px] text-gray-500 mb-8 leading-relaxed px-2" x-text="confirmDialog.message"></p>

                <div class="flex gap-4 w-full">
                    <button @click="confirmDialog.show = false" type="button" class="flex-1 h-[45px] bg-gray-50 hover:bg-gray-100 text-gray-700 rounded-[10px] text-[14px] font-bold transition-all border border-gray-200">
                        Batal
                    </button>
                    <button @click="executeConfirm()" type="button" 
                        class="flex-1 h-[45px] text-white rounded-[10px] text-[14px] font-bold transition-all shadow-md active:transform active:scale-95"
                        :class="[
                            confirmDialog.type === 'danger' ? 'bg-[#E53935] hover:bg-red-700' : '',
                            confirmDialog.type === 'info' ? 'bg-[#4285F4] hover:bg-blue-700' : ''
                        ]"
                        x-text="confirmDialog.confirmText">
                    </button>
                </div>
            </div>
        </div>

    </div>

    <script>
        function periodeController() {
            return {
                confirmDialog: { show: false, title: '', message: '', type: 'info', confirmText: 'Iya, Lanjutkan', callback: null },

                triggerConfirm(options) {
                    this.confirmDialog = {
                        show: true,
                        title: options.title || 'Konfirmasi Aksi',
                        message: options.message || 'Apakah Anda yakin ingin melanjutkan?',
                        type: options.type || 'info',
                        confirmText: options.confirmText || 'Iya, Lanjutkan',
                        callback: options.callback || null
                    };
                },

                executeConfirm() {
                    if (this.confirmDialog.callback) {
                        this.confirmDialog.callback();
                    }
                    this.confirmDialog.show = false;
                },

                bukaPeriode() {
                    this.triggerConfirm({
                        title: 'Buka Periode Baru',
                        message: 'Buka periode {{ $nextPeriod["label"] }}? Periode aktif saat ini akan ditutup secara otomatis.',
                        type: 'info',
                        confirmText: 'Buka Periode',
                        callback: () => {
                            document.getElementById('formBukaPeriode').submit();
                        }
                    });
                },

                konfirmasiAksi(tipe, id, pesan) {
                    this.triggerConfirm({
                        title: tipe === 'hapus' ? 'Hapus Periode' : 'Aktifkan Periode',
                        message: pesan,
                        type: tipe === 'hapus' ? 'danger' : 'info',
                        confirmText: tipe === 'hapus' ? 'Ya, Hapus' : 'Ya, Aktifkan',
                        callback: () => {
                            document.getElementById('form-' + tipe + '-' + id).submit();
                        }
                    });
                }
            };
        }
    </script>

</x-dashboard-layout>
