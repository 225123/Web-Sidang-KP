<x-dashboard-layout header="Detail Penilaian KP" :backUrl="route('koordinator.finalisasi-nilai.index')" hidePeriodSelector="true"
    userName="{{ auth()->user()->name }}" roleName="KOORDINATOR KP">
    <x-slot:sidebar>
        @include('koordinator.components.sidebar', ['active' => 'finalisasi-nilai'])
        </x-slot>

        <div class="mt-6 max-w-6xl mx-auto space-y-6">
            <!-- Section 1: Informasi Mahasiswa -->
            <div class="bg-[#EBEBEB] rounded-[10px] p-8 shadow-sm border border-[#CAC0C0]">
                <h3 class="text-[18px] font-bold text-black mb-6 uppercase tracking-tight">Informasi Mahasiswa</h3>

                <div class="grid grid-cols-[200px_auto] gap-y-3 text-[14px] font-medium text-black">
                    <div>Nama</div>
                    <div class="sentence-case">: {{ strtolower($sidang->mahasiswa->user->name) }}</div>

                    <div>NIM</div>
                    <div>: {{ $sidang->mahasiswa->nim }}</div>

                    <div>Tanggal Sidang</div>
                    <div>:
                        {{ \Carbon\Carbon::parse($sidang->tanggal_sidang)->locale('id')->isoFormat('dddd, D MMMM Y') }}
                    </div>

                    <div>Waktu Sidang</div>
                    <div>: {{ \Carbon\Carbon::parse($sidang->waktu_mulai_sidang)->format('H:i') }} -
                        {{ \Carbon\Carbon::parse($sidang->waktu_selesai_sidang)->format('H:i') }} WIB
                    </div>

                    <div>Ruangan</div>
                    <div>: {{ $sidang->ruang_sidang }}</div>

                    <div>Judul KP</div>
                    <div class="sentence-case">: {{ strtolower($sidang->judul_kp_display ?? '-') }}</div>

                    <div>Dosen Pembimbing</div>
                    <div>: {{ $sidang->pendaftaranKp->pembimbing->name ?? '-' }}</div>

                    <div>Dosen Penguji 1</div>
                    <div>: {{ $sidang->penguji1->name ?? '-' }}</div>

                    <div>Dosen Penguji 2</div>
                    <div>: {{ $sidang->penguji2->name ?? '-' }}</div>

                    <div>Supervisor</div>
                    <div>: {{ $sidang->pendaftaranKp->supervisorInstansi->nama_supervisor ?? '-' }}</div>
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
                            <h4 class="text-[15px] font-bold text-black mb-4 border-b border-gray-400 pb-1">Dosen
                                Pembimbing <span class="font-normal">(40%)</span></h4>
                            <div class="space-y-2 text-[13px] font-medium text-black">
                                <div class="flex justify-between"><span>Kualitas Laporan</span><span>:
                                        {{ $sidang->nb_laporan ?? 0 }}</span></div>
                                <div class="flex justify-between"><span>Kualitas Produk</span><span>:
                                        {{ $sidang->nb_produk ?? 0 }}</span></div>
                                <div class="flex justify-between"><span>Sikap Kedisiplinan</span><span>:
                                        {{ $sidang->nb_sikap ?? 0 }}</span></div>
                                <div class="flex justify-between font-bold border-t border-gray-300 pt-1 mt-2">
                                    <span>Total</span><span>: {{ $sidang->nilai_pembimbing ?? 0 }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Supervisor (10%) -->
                        <div>
                            <h4 class="text-[15px] font-bold text-black mb-4 border-b border-gray-400 pb-1">Supervisor
                                <span class="font-normal">(10%)</span>
                            </h4>
                            <div class="space-y-2 text-[13px] font-medium text-black">
                                <div class="flex justify-between"><span>Kemampuan & Motivasi</span><span>:
                                        {{ $sidang->ns_motivasi ?? 0 }}</span></div>
                                <div class="flex justify-between"><span>Kualitas Kerja</span><span>:
                                        {{ $sidang->ns_kualitas ?? 0 }}</span></div>
                                <div class="flex justify-between"><span>Insentif & Kreativitas</span><span>:
                                        {{ $sidang->ns_inisiatif ?? 0 }}</span></div>
                                <div class="flex justify-between"><span>Sikap & Kedisiplinan</span><span>:
                                        {{ $sidang->ns_sikap ?? 0 }}</span></div>
                                <div class="flex justify-between font-bold border-t border-gray-300 pt-1 mt-2">
                                    <span>Total</span><span>: {{ $sidang->nilai_supervisor ?? 0 }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Kolom Kanan: Dosen Penguji & Hasil Akhir -->
                    <div class="flex flex-col h-full">
                        <h4 class="text-[15px] font-bold text-black mb-4 border-b border-gray-400 pb-1">Dosen Penguji
                            <span class="font-normal">(50%)</span>
                        </h4>

                        <div class="grid grid-cols-2 gap-8 mb-6">
                            <!-- Penguji 1 -->
                            <div>
                                <p class="font-bold text-[13px] mb-2">Penguji 1 (20%)</p>
                                <div class="space-y-2 text-[12px] font-medium text-black">
                                    <div class="flex justify-between"><span>Laporan</span><span>:
                                            {{ $sidang->n1_laporan ?? 0 }}</span></div>
                                    <div class="flex justify-between"><span>Produk</span><span>:
                                            {{ $sidang->n1_produk ?? 0 }}</span></div>
                                    <div class="flex justify-between"><span>Presentasi</span><span>:
                                            {{ $sidang->n1_presentasi ?? 0 }}</span></div>
                                    <div class="flex justify-between font-bold border-t border-gray-300 pt-1 mt-2">
                                        <span>Total</span><span>: {{ $sidang->nilai_penguji_1 ?? 0 }}
                                            @if($sidang->original_nilai_penguji_1 !== null && $sidang->nilai_penguji_1 > $sidang->original_nilai_penguji_1)
                                                <span class="text-[10px] text-green-600 ml-1 italic">(dinaikkan)</span>
                                            @elseif($sidang->original_nilai_penguji_1 !== null && $sidang->nilai_penguji_1 < $sidang->original_nilai_penguji_1)
                                                <span class="text-[10px] text-red-600 ml-1 italic">(diturunkan)</span>
                                            @endif
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <!-- Penguji 2 -->
                            <div>
                                <p class="font-bold text-[13px] mb-2">Penguji 2 (25%)</p>
                                <div class="space-y-2 text-[12px] font-medium text-black">
                                    <div class="flex justify-between"><span>Laporan</span><span>:
                                            {{ $sidang->n2_laporan ?? 0 }}</span></div>
                                    <div class="flex justify-between"><span>Produk</span><span>:
                                            {{ $sidang->n2_produk ?? 0 }}</span></div>
                                    <div class="flex justify-between"><span>Presentasi</span><span>:
                                            {{ $sidang->n2_presentasi ?? 0 }}</span></div>
                                    <div class="flex justify-between font-bold border-t border-gray-300 pt-1 mt-2">
                                        <span>Total</span><span>: {{ $sidang->nilai_penguji_2 ?? 0 }}</span>
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
                                        <span class="text-[13px] font-bold text-black uppercase w-[150px]">Nilai
                                            Sidang</span>
                                        <span class="text-[15px] font-bold text-black">
                                            : {{ number_format($sidang->nilai_akhir_display, 2) }}
                                        </span>
                                    </div>
                                    <!-- Penalty Text Directly Under Nilai Akhir -->
                                    @if($sidang->is_penalized)
                                        <div class="pl-[165px]">
                                            <span class="text-[10px] text-red-500 font-bold italic">* Grade diturunkan
                                                karena revisi belum lengkap</span>
                                        </div>
                                    @endif
                                </div>

                                <!-- Status Lulus Row -->
                                <div class="flex items-center gap-4 mt-1">
                                    <span class="text-[13px] font-bold text-black uppercase w-[150px]">Status
                                        Lulus</span>
                                    <span class="text-[13px] font-medium text-black">
                                        : <span
                                            class="{{ $sidang->status_kelulusan === 'Tidak Lulus' ? 'text-red-600' : 'text-black' }}">
                                            {{ $sidang->status_kelulusan }}
                                        </span>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-12 flex justify-end gap-3">
                    <a href="{{ route('koordinator.finalisasi-nilai.download', $sidang->id) }}"
                        class="bg-[#EA3323] hover:bg-red-700 text-white font-bold text-[11px] px-6 py-2.5 rounded-[5px] flex items-center gap-2 shadow-sm transition-all uppercase tracking-wide">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                            </path>
                        </svg>
                        Download Nilai
                    </a>

                    @if($sidang->pelaksanaan === 'Selesai')
                        <a href="{{ route('koordinator.finalisasi-nilai.download-berita-acara', $sidang->id) }}"
                            class="bg-[#4285F4] hover:bg-blue-600 text-white font-bold text-[11px] px-6 py-2.5 rounded-[5px] flex items-center gap-2 shadow-sm transition-all uppercase tracking-wide">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                </path>
                            </svg>
                            Download Berita Acara
                        </a>
                    @else
                        <button disabled
                            class="bg-gray-400 text-white font-bold text-[11px] px-6 py-2.5 rounded-[5px] flex items-center gap-2 shadow-sm uppercase tracking-wide cursor-not-allowed opacity-70"
                            title="Sidang belum dilaksanakan atau belum selesai">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                </path>
                            </svg>
                            Berita Acara Belum Tersedia
                        </button>
                    @endif
                </div>
            </div>
        </div>

        <style>
            .sentence-case {
                text-transform: lowercase;
            }

            .sentence-case::first-letter {
                text-transform: uppercase;
            }
        </style>
</x-dashboard-layout>