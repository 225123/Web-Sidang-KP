<x-dashboard-layout userName="{{ auth()->user()->name }}" roleName="KOORDINATOR">
    <x-slot name="sidebar">
        @include('koordinator.components.sidebar', ['active' => 'periode-kp'])
    </x-slot>

    <x-slot name="header">Manajemen Periode KP</x-slot>

    <div class="max-w-6xl mx-auto space-y-6">

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
                    <button type="button" id="btnBukaPeriode"
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
                                                <form action="{{ route('koordinator.periode-kp.aktif', $periode->id) }}" method="POST" class="form-confirm"
                                                    data-msg="Aktifkan kembali periode {{ $periode->label_tahun_ajaran }}?">
                                                    @csrf @method('PUT')
                                                    <button type="submit" class="bg-blue-50 hover:bg-blue-100 text-blue-600 border border-blue-200 text-[11px] font-bold px-3 py-1.5 rounded-[5px] transition-colors shadow-sm">
                                                        Jadikan Aktif
                                                    </button>
                                                </form>
                                                @if(($stats[$periode->id] ?? 0) === 0)
                                                    <form action="{{ route('koordinator.periode-kp.destroy', $periode->id) }}" method="POST" class="form-confirm"
                                                        data-msg="Hapus periode {{ $periode->label_tahun_ajaran }}? Tindakan ini tidak bisa dibatalkan.">
                                                        @csrf @method('DELETE')
                                                        <button type="submit" class="bg-red-50 hover:bg-red-100 text-red-600 border border-red-200 text-[11px] font-bold px-3 py-1.5 rounded-[5px] transition-colors shadow-sm">
                                                            Hapus
                                                        </button>
                                                    </form>
                                                @endif
                                            </div>
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

    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Confirm before opening new period
            document.getElementById('btnBukaPeriode').addEventListener('click', function () {
                var msg = 'Buka periode {{ $nextPeriod["label"] }}? Periode aktif saat ini akan ditutup secara otomatis.';
                if (confirm(msg)) {
                    document.getElementById('formBukaPeriode').submit();
                }
            });

            // Confirm on all form-confirm forms
            document.querySelectorAll('.form-confirm').forEach(function (form) {
                form.addEventListener('submit', function (e) {
                    var msg = form.getAttribute('data-msg') || 'Apakah Anda yakin?';
                    if (!confirm(msg)) e.preventDefault();
                });
            });
        });
    </script>

</x-dashboard-layout>
