<x-dashboard-layout header="Hasil Sidang KP" :userName="auth()->user()->name" roleName="MAHASISWA">
    <x-slot:sidebar>
        @include('mahasiswa.components.sidebar', ['active' => 'hasil-sidang'])
    </x-slot>

        

    @php
        $mhsName = optional(optional(optional($sidang)->mahasiswa)->user)->name ? strtolower($sidang->mahasiswa->user->name) : '-';
        $mhsNim = optional(optional($sidang)->mahasiswa)->nim ?? '-';
        $tglSidang = optional($sidang)->tanggal_sidang ? \Carbon\Carbon::parse($sidang->tanggal_sidang)->locale('id')->isoFormat('dddd, D MMMM Y') : '-';
        $waktuSidang = optional($sidang)->waktu_mulai_sidang ? \Carbon\Carbon::parse($sidang->waktu_mulai_sidang)->format('H:i') . ' - ' . \Carbon\Carbon::parse($sidang->waktu_selesai_sidang)->format('H:i') . ' WIB' : '-';
        $ruangan = optional($sidang)->ruang_sidang ?? '-';
        $judul = optional(optional($sidang)->pendaftaranKp)->judul_kp ? strtolower($sidang->pendaftaranKp->judul_kp) : '-';
        $penguji1Name = optional(optional($sidang)->penguji1)->name ?? '-';
        $penguji2Name = optional(optional($sidang)->penguji2)->name ?? '-';

        $n1Laporan = optional($sidang)->n1_laporan ?? '-';
        $n1Produk = optional($sidang)->n1_produk ?? '-';
        $n1Presentasi = optional($sidang)->n1_presentasi ?? '-';
        $totalN1 = optional($sidang)->nilai_penguji_1 ?? '-';

        $n2Laporan = optional($sidang)->n2_laporan ?? '-';
        $n2Produk = optional($sidang)->n2_produk ?? '-';
        $n2Presentasi = optional($sidang)->n2_presentasi ?? '-';
        $totalN2 = optional($sidang)->nilai_penguji_2 ?? '-';

        $nilaiAkhir = (optional($sidang)->nilai_akhir_display !== null) ? number_format($sidang->nilai_akhir_display, 2) . ' (' . $sidang->grade_display . ')' : '-';
        $statusLulus = optional($sidang)->status_kelulusan ?? '-';
        $catatanSidang = optional($sidang)->catatan_sidang ?? '-';

        // Pengecekan apakah nilai sudah mulai diinput oleh salah satu penguji (Penguji 1 atau Penguji 2)
        $isNilaiMasuk = (
            optional($sidang)->nilai_penguji_1 !== null || 
            optional($sidang)->nilai_penguji_2 !== null
        );
    @endphp

    <div class="mt-6 max-w-6xl mx-auto space-y-6 pb-12">
        @if($sidang && $isNilaiMasuk)
            <!-- Section 1: Informasi Mahasiswa -->
        <div class="bg-[#EBEBEB] rounded-[10px] p-8 shadow-sm border border-[#CAC0C0]">
            <h3 class="text-[18px] font-bold text-black mb-6 uppercase tracking-tight">Informasi Mahasiswa</h3>
            
            <div class="grid grid-cols-[200px_auto] gap-y-3 text-[14px] font-medium text-black">
                <div>Nama</div>
                <div class="sentence-case">: {{ $mhsName }}</div>

                <div>NIM</div>
                <div>: {{ $mhsNim }}</div>

                <div>Tanggal Sidang</div>
                <div>: {{ $tglSidang }}</div>

                <div>Waktu Sidang</div>
                <div>: {{ $waktuSidang }}</div>

                <div>Ruangan</div>
                <div>: {{ $ruangan }}</div>

                <div>Judul KP</div>
                <div class="sentence-case">: {{ $judul }}</div>

                <div>Dosen Penguji 1</div>
                <div>: {{ $penguji1Name }}</div>

                <div>Dosen Penguji 2</div>
                <div>: {{ $penguji2Name }}</div>
            </div>
        </div>

        <!-- Section 2: Detail Penilaian Penguji -->
        <div class="bg-[#EBEBEB] rounded-[10px] p-8 shadow-sm border border-[#CAC0C0]">
            <h3 class="text-[18px] font-bold text-black mb-8 uppercase tracking-tight">Detail Penilaian Penguji</h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-16 gap-y-12">
                <!-- Penguji 1 -->
                <div>
                    <h4 class="text-[15px] font-bold text-black mb-4 border-b border-gray-400 pb-1">Dosen Penguji 1 <span class="font-normal">(25%)</span></h4>
                    <div class="space-y-2 text-[13px] font-medium text-black">
                        <div class="flex justify-between"><span>Laporan</span><span>: {{ $n1Laporan }}</span></div>
                        <div class="flex justify-between"><span>Produk</span><span>: {{ $n1Produk }}</span></div>
                        <div class="flex justify-between"><span>Presentasi</span><span>: {{ $n1Presentasi }}</span></div>
                        <div class="flex justify-between font-bold border-t border-gray-300 pt-1 mt-2">
                            <span>Total</span><span>: {{ $totalN1 }}</span>
                        </div>
                    </div>
                </div>

                <!-- Penguji 2 -->
                <div>
                    <h4 class="text-[15px] font-bold text-black mb-4 border-b border-gray-400 pb-1">Dosen Penguji 2 <span class="font-normal">(25%)</span></h4>
                    <div class="space-y-2 text-[13px] font-medium text-black">
                        <div class="flex justify-between"><span>Laporan</span><span>: {{ $n2Laporan }}</span></div>
                        <div class="flex justify-between"><span>Produk</span><span>: {{ $n2Produk }}</span></div>
                        <div class="flex justify-between"><span>Presentasi</span><span>: {{ $n2Presentasi }}</span></div>
                        <div class="flex justify-between font-bold border-t border-gray-300 pt-1 mt-2">
                            <span>Total</span><span>: {{ $totalN2 }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Catatan Sidang -->
            <div class="mt-8 p-6 bg-white rounded-[10px] border border-gray-200 shadow-sm">
                <h4 class="text-[15px] font-bold text-gray-900 mb-3 flex items-center gap-2">
                    <svg class="w-5 h-5 text-blue-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                    Catatan & Masukan Penguji
                </h4>
                <div class="text-[13px] text-gray-700 leading-relaxed font-normal whitespace-pre-line bg-gray-50 p-4 rounded-[6px] border border-gray-200 min-h-[60px]">
                    {!! !empty($catatanSidang) && $catatanSidang !== '-' ? nl2br(e($catatanSidang)) : '<span class="text-gray-400 italic">Tidak ada catatan dari dosen penguji.</span>' !!}
                </div>
                <p class="text-[11px] text-gray-500 mt-3 font-medium">* Gunakan catatan ini sebagai acuan utama dalam proses revisi laporan KP Anda.</p>
            </div>

            <!-- Hasil Akhir Area -->
            <div class="mt-12 pt-8 border-t-2 border-dashed border-gray-400">
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
                    <div class="flex flex-col gap-3">
                        <!-- Nilai Akhir Row -->
                        <div class="flex items-center gap-4">
                            <span class="text-[15px] font-bold text-black uppercase w-[150px]">Nilai Akhir</span>
                            <span class="text-[20px] font-black text-black">
                                : {{ $nilaiAkhir }}
                            </span>
                        </div>

                        <!-- Status Lulus Row -->
                        <div class="flex items-center gap-4">
                            <span class="text-[15px] font-bold text-black uppercase w-[150px]">Status Lulus</span>
                            <span class="text-[16px] font-bold">
                                : <span class="{{ strtolower($statusLulus) === 'lanjut' ? 'text-red-600' : 'text-blue-700' }}">
                                    {{ $statusLulus }}
                                </span>
                            </span>
                        </div>
                    </div>

                    <!-- Action Link to Revision if needed -->
                    @if(strtolower($statusLulus) === 'lulus dengan revisi')
                    <a href="{{ route('mahasiswa.revisi.index') }}" class="bg-blue-600 hover:bg-blue-700 text-white font-bold text-[13px] px-8 py-3 rounded-full flex items-center gap-2 shadow-lg transition-all transform hover:-translate-y-1 uppercase tracking-wide">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                        Lanjut ke Halaman Revisi
                    </a>
                    @endif
                </div>
            </div>
        </div>
            </div>
        </div>
        @elseif(!$sidang)
            <div class="mt-6 max-w-6xl mx-auto flex items-center justify-center min-h-[400px]">
                <div class="bg-white rounded-[15px] p-10 shadow-sm border border-gray-200 text-center max-w-lg">
                    <div class="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-6">
                        <svg class="w-10 h-10 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </div>
                    <h3 class="text-[20px] font-bold text-gray-900 mb-3 uppercase tracking-tight">Belum Mendaftar Sidang</h3>
                    <p class="text-[14px] text-gray-500 font-medium leading-relaxed">
                        @if(isset($isPastPeriod) && $isPastPeriod)
                            Anda tidak terdaftar untuk mengikuti Sidang KP pada periode tersebut, sehingga Anda tidak memiliki Hasil Sidang KP.
                        @else
                            Anda belum mendaftar sidang KP. Silakan lakukan pendaftaran sidang pada menu Pendaftaran Sidang, atau hubungi Koordinator KP jika ada kesalahan.
                        @endif
                    </p>
                </div>
            </div>
        @else
            <!-- Empty state -->
            <div class="mt-6 max-w-6xl mx-auto flex items-center justify-center min-h-[400px]">
                <div class="bg-white rounded-[15px] p-10 shadow-sm border border-gray-200 text-center max-w-lg">
                    <div class="w-20 h-20 bg-yellow-50 rounded-full flex items-center justify-center mx-auto mb-6">
                        <svg class="w-10 h-10 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <h3 class="text-[20px] font-bold text-gray-900 mb-3 uppercase tracking-tight">Hasil Sidang Belum Tersedia</h3>
                    <p class="text-[14px] text-gray-500 font-medium leading-relaxed">
                        Nilai sidang Anda sedang dalam proses penginputan oleh tim penguji. Silakan cek kembali halaman ini setelah proses penilaian selesai.
                    </p>
                </div>
            </div>
        @endif
    </div>

    <style>
        .sentence-case { text-transform: lowercase; }
        .sentence-case::first-letter { text-transform: uppercase; }
    </style>
</x-dashboard-layout>
