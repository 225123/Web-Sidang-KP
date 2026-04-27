<x-dashboard-layout header="Nilai Akhir KP" userName="{{ auth()->user()->name }}" roleName="MAHASISWA">
    <x-slot:sidebar>
        @include('mahasiswa.components.sidebar', ['active' => 'nilai-akhir'])
    </x-slot>

    @php
        $mhsName = optional(optional(optional($sidang)->mahasiswa)->user)->name ? strtolower($sidang->mahasiswa->user->name) : '-';
        $mhsNim = optional(optional($sidang)->mahasiswa)->nim ?? '-';
        $tglSidang = optional($sidang)->tanggal_sidang ? \Carbon\Carbon::parse($sidang->tanggal_sidang)->locale('id')->isoFormat('dddd, D MMMM Y') : '-';
        $waktuSidang = optional($sidang)->waktu_mulai_sidang ? \Carbon\Carbon::parse($sidang->waktu_mulai_sidang)->format('H:i') . ' - ' . \Carbon\Carbon::parse($sidang->waktu_selesai_sidang)->format('H:i') . ' WIB' : '-';
        $ruangan = optional($sidang)->ruang_sidang ?? '-';
        $judul = optional(optional($sidang)->pendaftaranKp)->judul_kp ? strtolower($sidang->pendaftaranKp->judul_kp) : '-';
        $pembimbingName = optional(optional(optional($sidang)->pendaftaranKp)->pembimbing)->name ?? '-';
        $penguji1Name = optional(optional($sidang)->penguji1)->name ?? '-';
        $penguji2Name = optional(optional($sidang)->penguji2)->name ?? '-';
        $supervisorName = optional(optional(optional($sidang)->pendaftaranKp)->supervisorInstansi)->nama_supervisor ?? '-';

        $nbLaporan = optional($sidang)->nb_laporan ?? '-';
        $nbProduk = optional($sidang)->nb_produk ?? '-';
        $nbSikap = optional($sidang)->nb_sikap ?? '-';
        $totalPembimbing = optional($sidang)->nilai_pembimbing ?? '-';

        $nsMotivasi = optional($sidang)->ns_motivasi ?? '-';
        $nsKualitas = optional($sidang)->ns_kualitas ?? '-';
        $nsInisiatif = optional($sidang)->ns_inisiatif ?? '-';
        $nsSikap = optional($sidang)->ns_sikap ?? '-';
        $totalSupervisor = optional($sidang)->nilai_supervisor ?? '-';

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
        $isPenalized = optional($sidang)->is_penalized ?? false;
    @endphp

    <div class="mt-6 max-w-6xl mx-auto space-y-6 pb-12">
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

                <div>Dosen Pembimbing</div>
                <div>: {{ $pembimbingName }}</div>

                <div>Dosen Penguji 1</div>
                <div>: {{ $penguji1Name }}</div>

                <div>Dosen Penguji 2</div>
                <div>: {{ $penguji2Name }}</div>

                <div>Supervisor</div>
                <div>: {{ $supervisorName }}</div>
            </div>
        </div>

        <!-- Section 2: Detail Penilaian -->
        <div class="bg-[#EBEBEB] rounded-[10px] p-8 shadow-sm border border-[#CAC0C0]">
            <h3 class="text-[18px] font-bold text-black mb-8 uppercase tracking-tight">Detail Penilaian</h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-16 gap-y-12">
                <!-- Kolom Kiri: Pembimbing & Supervisor -->
                <div class="space-y-12">
                    <!-- Dosen Pembimbing (40%) -->
                    <div>
                        <h4 class="text-[15px] font-bold text-black mb-4 border-b border-gray-400 pb-1">Dosen Pembimbing <span class="font-normal">(40%)</span></h4>
                        <div class="space-y-2 text-[13px] font-medium text-black">
                            <div class="flex justify-between"><span>Kualitas Laporan</span><span>: {{ $nbLaporan }}</span></div>
                            <div class="flex justify-between"><span>Kualitas Produk</span><span>: {{ $nbProduk }}</span></div>
                            <div class="flex justify-between"><span>Sikap Kedisiplinan</span><span>: {{ $nbSikap }}</span></div>
                            <div class="flex justify-between font-bold border-t border-gray-300 pt-1 mt-2">
                                <span>Total</span><span>: {{ $totalPembimbing }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Supervisor (10%) -->
                    <div>
                        <h4 class="text-[15px] font-bold text-black mb-4 border-b border-gray-400 pb-1">Supervisor <span class="font-normal">(10%)</span></h4>
                        <div class="space-y-2 text-[13px] font-medium text-black">
                            <div class="flex justify-between"><span>Kemampuan & Motivasi</span><span>: {{ $nsMotivasi }}</span></div>
                            <div class="flex justify-between"><span>Kualitas Kerja</span><span>: {{ $nsKualitas }}</span></div>
                            <div class="flex justify-between"><span>Insentif & Kreativitas</span><span>: {{ $nsInisiatif }}</span></div>
                            <div class="flex justify-between"><span>Sikap & Kedisiplinan</span><span>: {{ $nsSikap }}</span></div>
                            <div class="flex justify-between font-bold border-t border-gray-300 pt-1 mt-2">
                                <span>Total</span><span>: {{ $totalSupervisor }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Kolom Kanan: Dosen Penguji & Hasil Akhir -->
                <div class="flex flex-col h-full">
                    <h4 class="text-[15px] font-bold text-black mb-4 border-b border-gray-400 pb-1">Dosen Penguji <span class="font-normal">(50%)</span></h4>
                    
                    <div class="grid grid-cols-2 gap-8 mb-6">
                        <!-- Penguji 1 -->
                        <div>
                            <p class="font-bold text-[13px] mb-2">Penguji 1</p>
                            <div class="space-y-2 text-[12px] font-medium text-black">
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
                            <p class="font-bold text-[13px] mb-2">Penguji 2</p>
                            <div class="space-y-2 text-[12px] font-medium text-black">
                                <div class="flex justify-between"><span>Laporan</span><span>: {{ $n2Laporan }}</span></div>
                                <div class="flex justify-between"><span>Produk</span><span>: {{ $n2Produk }}</span></div>
                                <div class="flex justify-between"><span>Presentasi</span><span>: {{ $n2Presentasi }}</span></div>
                                <div class="flex justify-between font-bold border-t border-gray-300 pt-1 mt-2">
                                    <span>Total</span><span>: {{ $totalN2 }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Hasil Akhir Area (Under Penguji) -->
                    <div class="mt-auto pt-6 border-t-2 border-dashed border-gray-400">
                        <div class="flex flex-col items-start gap-2">
                            <!-- Nilai Akhir Row -->
                            <div class="w-full">
                                <div class="flex items-center gap-4">
                                    <span class="text-[13px] font-bold text-black uppercase w-[150px]">Nilai Akhir</span>
                                    <span class="text-[15px] font-bold text-black">
                                        : {{ $nilaiAkhir }}
                                    </span>
                                </div>
                                <!-- Penalty Text Directly Under Nilai Akhir -->
                                @if($isPenalized)
                                    <div class="pl-[165px]">
                                        <span class="text-[10px] text-red-500 font-bold italic">* Grade diturunkan karena revisi belum lengkap</span>
                                    </div>
                                @endif
                            </div>

                            <!-- Status Lulus Row -->
                            <div class="flex items-center gap-4 mt-1">
                                <span class="text-[13px] font-bold text-black uppercase w-[150px]">Status Lulus</span>
                                <span class="text-[13px] font-medium text-black">
                                    : <span class="{{ strtolower($statusLulus) === 'lanjut' ? 'text-red-600' : 'text-black' }}">
                                        {{ $statusLulus }}
                                    </span>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>


            <div class="mt-12 flex justify-end gap-3">
                @if($sidang)
                    <a href="{{ route('mahasiswa.nilai-akhir.download') }}" class="bg-[#EA3323] hover:bg-red-700 text-white font-bold text-[11px] px-6 py-2.5 rounded-[5px] flex items-center gap-2 shadow-sm transition-all uppercase tracking-wide">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        Download Nilai
                    </a>

                    @if($sidang->penguji_1_id && $sidang->penguji_2_id)
                        <a href="{{ route('mahasiswa.nilai-akhir.download-berita-acara') }}" class="bg-[#4285F4] hover:bg-blue-600 text-white font-bold text-[11px] px-6 py-2.5 rounded-[5px] flex items-center gap-2 shadow-sm transition-all uppercase tracking-wide">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                            Download Berita Acara
                        </a>
                    @else
                        <button disabled class="bg-gray-400 text-white font-bold text-[13px] px-8 py-3 rounded-[5px] flex items-center gap-2 shadow-sm uppercase tracking-wide cursor-not-allowed opacity-70" title="Dosen Penguji belum diplot oleh Koordinator">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                            Berita Acara Belum Tersedia
                        </button>
                    @endif
                @else
                    <button disabled class="bg-gray-400 text-white font-bold text-[13px] px-8 py-3 rounded-[5px] flex items-center gap-2 shadow-sm uppercase tracking-wide cursor-not-allowed">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        Download Nilai
                    </button>
                    <button disabled class="bg-gray-400 text-white font-bold text-[13px] px-8 py-3 rounded-[5px] flex items-center gap-2 shadow-sm uppercase tracking-wide cursor-not-allowed">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        Download Berita Acara
                    </button>
                @endif
            </div>
        </div>
    </div>

    <style>
        .sentence-case { text-transform: lowercase; }
        .sentence-case::first-letter { text-transform: uppercase; }
    </style>
</x-dashboard-layout>
