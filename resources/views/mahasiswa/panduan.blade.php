<x-dashboard-layout header="Panduan Website & Aturan Kerja Praktek" userName="{{ auth()->user()->name }}" roleName="MAHASISWA" hidePeriodSelector="true">
    <x-slot:sidebar>
        @include('mahasiswa.components.sidebar', ['active' => 'panduan'])
    </x-slot>

        

    <style>
        [x-cloak] {
            display: none !important;
        }

        .step-container::before {
            content: '';
            position: absolute;
            left: 24px;
            top: 40px;
            bottom: -40px;
            width: 2px;
            background: #F1F5F9;
            z-index: 0;
        }

        .step-container:last-child::before {
            display: none;
        }
    </style>

    <div class="mt-8 px-4 w-full pb-20" x-data="{ tab: 'alur' }">
        <!-- Banner -->
        <div
            class="bg-gradient-to-br from-[#C26700] via-[#F48200] to-[#C26700] rounded-[15px] p-10 mb-8 text-white shadow-xl relative overflow-hidden border border-[#D97300]">
            <div class="relative z-10 max-w-3xl">
                <div
                    class="inline-flex items-center gap-2 bg-white/20 text-white px-3 py-1 rounded-full text-[11px] font-bold uppercase tracking-wider mb-4 border border-white/30">
                    <span class="w-2 h-2 bg-white rounded-full animate-pulse"></span>
                    Panduan Resmi Mahasiswa
                </div>
                <h1 class="text-3xl font-extrabold mb-3 tracking-tight">Pusat Informasi Kerja Praktek</h1>
                <p class="text-white/90 leading-relaxed text-[14px] font-medium">
                    Sistem ini dirancang untuk mendampingi seluruh proses Kerja Praktek (KP) Anda dari pendaftaran hingga
                    nilai akhir. Pelajari alur kerja sistem, fungsionalitas setiap menu, serta peraturan dan sanksi
                    (pinalti) yang berlaku.
                </p>
            </div>
            <div class="absolute right-[-20px] top-[-20px] opacity-[0.05] pointer-events-none">
                <svg class="w-80 h-80" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M12 2L2 22h20L12 2zm0 4.5l6.5 13h-13L12 6.5z" />
                </svg>
            </div>
        </div>

        <!-- Navigation Tabs -->
        <div class="flex flex-wrap gap-2 border-b border-slate-200 mb-8">
            <button @click="tab = 'alur'"
                :class="tab === 'alur' ? 'border-[#F48200] text-[#F48200] font-bold bg-[#F48200]/10' : 'border-transparent text-slate-700 font-medium hover:text-[#F48200] hover:border-slate-300'"
                class="px-8 py-4 border-b-2 text-[13px] transition-all rounded-t-lg">
                <div class="flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 12h16m-7 6h7" />
                    </svg>
                    Fungsi Navigasi & Menu
                </div>
            </button>
            <button @click="tab = 'peraturan'"
                :class="tab === 'peraturan' ? 'border-[#F48200] text-[#F48200] font-bold bg-[#F48200]/10' : 'border-transparent text-slate-700 font-medium hover:text-[#F48200] hover:border-slate-300'"
                class="px-8 py-4 border-b-2 text-[13px] transition-all rounded-t-lg">
                <div class="flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                    Peraturan & Pinalti
                </div>
            </button>
            <button @click="tab = 'faq'"
                :class="tab === 'faq' ? 'border-[#F48200] text-[#F48200] font-bold bg-[#F48200]/10' : 'border-transparent text-slate-700 font-medium hover:text-[#F48200] hover:border-slate-300'"
                class="px-8 py-4 border-b-2 text-[13px] transition-all rounded-t-lg">
                <div class="flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Bantuan & FAQ
                </div>
            </button>
        </div>

        <!-- TAB: Penjelasan Navigasi -->
        <div x-show="tab === 'alur'" x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0"
            class="flex flex-col lg:flex-row gap-8 items-start">

            <!-- Left: Grid Penjelasan Menu -->
            <div class="flex-1 w-full space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Dashboard -->
                    <div
                        class="bg-slate-50 rounded-[12px] border border-slate-200 p-5 shadow-sm hover:border-[#F48200] transition-all group">
                        <div class="flex items-center gap-4 mb-3">
                            <div
                                class="w-10 h-10 bg-[#F48200] text-white rounded-lg flex items-center justify-center transition-colors shadow-md">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                                </svg>
                            </div>
                            <h3 class="font-bold text-slate-900 text-[15px]">Dashboard</h3>
                        </div>
                        <p class="text-[12px] text-slate-800 leading-relaxed font-medium">
                            Beranda utama untuk memantau ringkasan status pendaftaran, pengumuman terbaru dari Koordinator, dan akses cepat ke fitur utama sistem.
                        </p>
                    </div>

                    <!-- Mendaftar KP -->
                    <div
                        class="bg-slate-50 rounded-[12px] border border-slate-200 p-5 shadow-sm hover:border-[#F48200] transition-all group">
                        <div class="flex items-center gap-4 mb-3">
                            <div
                                class="w-10 h-10 bg-[#F48200] text-white rounded-lg flex items-center justify-center transition-colors shadow-md">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 13h6m-3-3v6m5 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                            </div>
                            <h3 class="font-bold text-slate-900 text-[15px]">Mendaftar KP</h3>
                        </div>
                        <p class="text-[12px] text-slate-800 leading-relaxed font-medium">
                            Menginput detail perusahaan, melampirkan proposal KP, dan mendaftarkan anggota kelompok (jika kolektif) untuk diverifikasi oleh Koordinator.
                        </p>
                    </div>

                    <!-- Status Pendaftaran -->
                    <div
                        class="bg-slate-50 rounded-[12px] border border-slate-200 p-5 shadow-sm hover:border-[#F48200] transition-all group">
                        <div class="flex items-center gap-4 mb-3">
                            <div
                                class="w-10 h-10 bg-[#F48200] text-white rounded-lg flex items-center justify-center transition-colors shadow-md">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <h3 class="font-bold text-slate-900 text-[15px]">Status Pendaftaran</h3>
                        </div>
                        <p class="text-[12px] text-slate-800 leading-relaxed font-medium">
                            Mengecek progres verifikasi pendaftaran. Jika ditolak, Anda dapat melihat alasan penolakan dan mengajukan ulang dengan perbaikan.
                        </p>
                    </div>

                    <!-- Log Bimbingan -->
                    <div
                        class="bg-slate-50 rounded-[12px] border border-slate-200 p-5 shadow-sm hover:border-[#F48200] transition-all group">
                        <div class="flex items-center gap-4 mb-3">
                            <div
                                class="w-10 h-10 bg-[#F48200] text-white rounded-lg flex items-center justify-center transition-colors shadow-md">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                </svg>
                            </div>
                            <h3 class="font-bold text-slate-900 text-[15px]">Log Bimbingan Dosen</h3>
                        </div>
                        <p class="text-[12px] text-slate-800 leading-relaxed font-medium">
                            Mencatat progres KP. Anda <strong>WAJIB</strong> memenuhi minimal 12 bimbingan yang telah divalidasi (ACC) oleh Dosen sebagai prasyarat sidang.
                        </p>
                    </div>

                    <!-- Persetujuan Sidang -->
                    <div
                        class="bg-slate-50 rounded-[12px] border border-slate-200 p-5 shadow-sm hover:border-[#F48200] transition-all group">
                        <div class="flex items-center gap-4 mb-3">
                            <div
                                class="w-10 h-10 bg-[#F48200] text-white rounded-lg flex items-center justify-center transition-colors shadow-md">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <h3 class="font-bold text-slate-900 text-[15px]">Persetujuan Sidang</h3>
                        </div>
                        <p class="text-[12px] text-slate-800 leading-relaxed font-medium">
                            Meminta izin maju sidang kepada Dosen Pembimbing setelah laporan akhir selesai dan kuota bimbingan minimal 12 kali terpenuhi.
                        </p>
                    </div>

                    <!-- Jadwal Sidang -->
                    <div
                        class="bg-slate-50 rounded-[12px] border border-slate-200 p-5 shadow-sm hover:border-[#F48200] transition-all group">
                        <div class="flex items-center gap-4 mb-3">
                            <div
                                class="w-10 h-10 bg-[#F48200] text-white rounded-lg flex items-center justify-center transition-colors shadow-md">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                            </div>
                            <h3 class="font-bold text-slate-900 text-[15px]">Jadwal Sidang</h3>
                        </div>
                        <p class="text-[12px] text-slate-800 leading-relaxed font-medium">
                            Informasi detail mengenai waktu pelaksanaan, lokasi ruangan, dan daftar Dosen Penguji yang telah dijadwalkan oleh Koordinator.
                        </p>
                    </div>

                    <!-- Revisi -->
                    <div
                        class="bg-slate-50 rounded-[12px] border border-slate-200 p-5 shadow-sm hover:border-[#F48200] transition-all group">
                        <div class="flex items-center gap-4 mb-3">
                            <div
                                class="w-10 h-10 bg-[#F48200] text-white rounded-lg flex items-center justify-center transition-colors shadow-md">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                </svg>
                            </div>
                            <h3 class="font-bold text-slate-900 text-[15px]">Revisi</h3>
                        </div>
                        <p class="text-[12px] text-slate-800 leading-relaxed font-medium">
                            Mengunggah dokumen perbaikan jika Dosen Penguji memberikan catatan revisi. <strong>Batas waktu revisi adalah 5 hari setelah sidang</strong>.
                        </p>
                    </div>

                    <!-- Nilai Akhir -->
                    <div
                        class="bg-slate-50 rounded-[12px] border border-slate-200 p-5 shadow-sm hover:border-[#F48200] transition-all group">
                        <div class="flex items-center gap-4 mb-3">
                            <div
                                class="w-10 h-10 bg-[#F48200] text-white rounded-lg flex items-center justify-center transition-colors shadow-md">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                </svg>
                            </div>
                            <h3 class="font-bold text-slate-900 text-[15px]">Nilai Akhir KP</h3>
                        </div>
                        <p class="text-[12px] text-slate-800 leading-relaxed font-medium">
                            Melihat hasil akhir sidang, rincian komponen penilaian, serta mengunduh form Berita Acara Sidang jika dinyatakan Lulus.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Right: Support & Profile -->
            <div class="w-full lg:w-[350px] flex flex-col gap-6">
                <div class="bg-slate-50 rounded-[12px] border border-slate-200 p-6 shadow-sm">
                    <h2 class="text-[15px] font-bold text-slate-800 mb-5 flex items-center gap-2">
                        <svg class="w-4 h-4 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z"
                                clip-rule="evenodd" />
                        </svg>
                        Informasi Profil
                    </h2>
                    <div class="space-y-5">
                        <div
                            class="p-4 bg-white border-2 border-dashed border-[#F48200] rounded-lg relative overflow-hidden group hover:bg-[#F48200]/5 transition-all">
                            <div class="flex items-center gap-3 mb-2">
                                <div
                                    class="w-8 h-8 bg-[#F48200] text-white rounded flex items-center justify-center">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                    </svg>
                                </div>
                                <h4 class="text-[13px] font-bold text-slate-900">Tanda Tangan Digital</h4>
                            </div>
                            <p class="text-[11px] text-slate-700 leading-relaxed font-medium">
                                Fitur wajib di menu Profil. Anda wajib <strong>menggambar langsung</strong> tanda tangan digital Anda. Tanda tangan ini digunakan otomatis di seluruh dokumen PDF sistem.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="bg-slate-900 text-slate-300 rounded-[12px] p-6 shadow-xl relative overflow-hidden">
                    <div class="relative z-10">
                        <h3 class="text-[14px] font-bold text-white mb-3 flex items-center gap-2">
                            <svg class="w-4 h-4 text-orange-400" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Sanksi Pinalti
                        </h3>
                        <p class="text-[11px] text-slate-100 leading-relaxed font-medium">
                            Keterlambatan revisi (>5 hari) berakibat pada <strong>penurunan 1 tingkat Grade</strong> penilaian akhir secara otomatis oleh sistem.
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- TAB: Peraturan & Pinalti -->
        <div x-show="tab === 'peraturan'" x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0"
            class="space-y-8" style="display:none;">
            
            <!-- Detailed Workflow Timeline -->
            <div class="bg-white border border-slate-200 rounded-[15px] p-8 shadow-sm">
                <h2 class="text-xl font-bold text-slate-900 mb-8 flex items-center gap-3">
                    <div class="w-10 h-10 bg-[#F48200] rounded-xl flex items-center justify-center text-white shadow-lg shadow-orange-100">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5l7 7-7 7M5 5l7 7-7 7"/></svg>
                    </div>
                    Alur Prosedur & Ketentuan Kerja Praktek
                </h2>

                <div class="space-y-12 relative before:content-[''] before:absolute before:left-[19px] before:top-2 before:bottom-2 before:w-[2px] before:bg-slate-100">
                    <!-- Phase 1 -->
                    <div class="relative pl-12">
                        <div class="absolute left-0 w-10 h-10 bg-white border-2 border-[#F48200] rounded-full flex items-center justify-center z-10">
                            <div class="w-2 h-2 bg-[#F48200] rounded-full"></div>
                        </div>
                        <h3 class="font-bold text-slate-900 text-[15px] mb-2">1. Pra-Pendaftaran & Pengajuan Proposal</h3>
                        <p class="text-[12px] text-slate-700 leading-relaxed font-medium mb-3">
                            Mahasiswa mencari instansi KP secara mandiri. Setelah mendapatkan konfirmasi lisan, mahasiswa wajib menyusun proposal dan mendaftarkannya ke sistem.
                        </p>
                        <ul class="list-disc pl-5 text-[11px] text-slate-600 space-y-1 font-medium italic">
                            <li>Wajib melampirkan proposal dalam format PDF.</li>
                            <li>Pendaftaran anggota kelompok dilakukan oleh ketua kelompok (jika kolektif).</li>
                        </ul>
                    </div>

                    <!-- Phase 2 -->
                    <div class="relative pl-12">
                        <div class="absolute left-0 w-10 h-10 bg-white border-2 border-[#F48200] rounded-full flex items-center justify-center z-10">
                            <div class="w-2 h-2 bg-[#F48200] rounded-full"></div>
                        </div>
                        <h3 class="font-bold text-slate-900 text-[15px] mb-2">2. Verifikasi Berkas & Penugasan Pembimbing</h3>
                        <p class="text-[12px] text-slate-700 leading-relaxed font-medium mb-3">
                            Koordinator memeriksa kelengkapan berkas. Jika sesuai, status pendaftaran akan berubah menjadi <strong>'Disetujui'</strong> dan Dosen Pembimbing akan diploting secara otomatis.
                        </p>
                    </div>

                    <!-- Phase 3 -->
                    <div class="relative pl-12">
                        <div class="absolute left-0 w-10 h-10 bg-white border-2 border-[#F48200] rounded-full flex items-center justify-center z-10">
                            <div class="w-2 h-2 bg-[#F48200] rounded-full"></div>
                        </div>
                        <h3 class="font-bold text-slate-900 text-[15px] mb-2">3. Masa Pelaksanaan & Bimbingan (Logbook)</h3>
                        <p class="text-[12px] text-slate-700 leading-relaxed font-medium mb-3">
                            Mahasiswa melaksanakan KP di instansi (Durasi 1-3 bulan). Selama masa ini, mahasiswa wajib mencatat setiap aktivitas ke menu <strong>'Log Bimbingan'</strong>.
                        </p>
                        <div class="bg-orange-50 border-l-4 border-[#F48200] p-3 text-[11px] text-orange-900 font-bold">
                            KETENTUAN: Minimal 12 kali entri bimbingan harus mendapatkan ACC/Validasi dari Dosen Pembimbing untuk dapat mendaftar sidang.
                        </div>
                    </div>

                    <!-- Phase 4 -->
                    <div class="relative pl-12">
                        <div class="absolute left-0 w-10 h-10 bg-white border-2 border-[#F48200] rounded-full flex items-center justify-center z-10">
                            <div class="w-2 h-2 bg-[#F48200] rounded-full"></div>
                        </div>
                        <h3 class="font-bold text-slate-900 text-[15px] mb-2">4. Izin Sidang & Pendaftaran Sidang</h3>
                        <p class="text-[12px] text-slate-700 leading-relaxed font-medium mb-3">
                            Setelah laporan selesai, ajukan <strong>'Persetujuan Sidang'</strong> ke Pembimbing. Jika diberikan ACC, mahasiswa dapat mengunggah dokumen syarat sidang (Surat Selesai KP, Laporan Final, dll).
                        </p>
                    </div>

                    <!-- Phase 5 -->
                    <div class="relative pl-12">
                        <div class="absolute left-0 w-10 h-10 bg-white border-2 border-[#F48200] rounded-full flex items-center justify-center z-10">
                            <div class="w-2 h-2 bg-[#F48200] rounded-full"></div>
                        </div>
                        <h3 class="font-bold text-slate-900 text-[15px] mb-2">5. Pelaksanaan Sidang & Revisi</h3>
                        <p class="text-[12px] text-slate-700 leading-relaxed font-medium mb-3">
                            Mahasiswa melaksanakan presentasi di hadapan Dosen Penguji. Jika terdapat catatan revisi, mahasiswa wajib memperbaikinya dalam sistem.
                        </p>
                        <div class="bg-red-50 border-l-4 border-red-500 p-3 text-[11px] text-red-900 font-bold">
                            SANKSI: Melewati batas revisi 5 hari kerja berakibat pada penurunan Grade Nilai otomatis.
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-red-50 border border-red-200 rounded-[15px] p-8 shadow-sm">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-10">
                    <div class="relative bg-white p-6 rounded-xl shadow-sm border border-red-100">
                        <h3 class="font-bold text-red-700 text-[16px] mb-3 flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            Tenggat Waktu Revisi
                        </h3>
                        <p class="text-[12px] text-slate-800 leading-relaxed font-medium mb-4">
                            Mahasiswa diberikan waktu maksimal 5 hari kerja setelah sidang untuk menyelesaikan semua catatan perbaikan dari penguji.
                        </p>
                        <div class="bg-red-50 text-red-700 text-[11px] font-bold px-3 py-2 rounded border border-red-100">
                            Sanksi: Penurunan 1 tingkat Grade (Misal A menjadi B).
                        </div>
                    </div>
                    <div class="relative bg-white p-6 rounded-xl shadow-sm border border-red-100">
                        <h3 class="font-bold text-red-700 text-[16px] mb-3 flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            Minimal Bimbingan
                        </h3>
                        <p class="text-[12px] text-slate-800 leading-relaxed font-medium mb-4">
                            Setiap mahasiswa wajib memiliki minimal 12 entri log bimbingan yang telah disetujui (ACC) oleh Dosen Pembimbing.
                        </p>
                        <div class="bg-red-50 text-red-700 text-[11px] font-bold px-3 py-2 rounded border border-red-100">
                            Sanksi: Tidak diperbolehkan mendaftar sidang KP.
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- TAB: FAQ & Bantuan -->
        <div x-show="tab === 'faq'" x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0"
            class="space-y-6" style="display:none;">
            <div class="max-w-4xl mx-auto">
                <div class="text-center mb-10">
                    <h2 class="text-2xl font-bold text-slate-900 mb-2">Pertanyaan Sering Diajukan (FAQ)</h2>
                    <p class="text-slate-600 text-[13px]">Temukan jawaban cepat untuk kendala yang sering dialami oleh Mahasiswa.</p>
                </div>

                <div class="space-y-3" x-data="{ activeFaq: null }">
                    <!-- FAQ 1 -->
                    <div
                        class="bg-white border border-slate-200 rounded-xl overflow-hidden transition-all shadow-sm">
                        <button @click="activeFaq = activeFaq === 1 ? null : 1"
                            class="w-full px-6 py-4 text-left flex items-center justify-between hover:bg-slate-50 transition-colors">
                            <span class="font-bold text-[14px] text-slate-900">Kapan saya bisa mendaftar sidang KP?</span>
                            <svg :class="activeFaq === 1 ? 'rotate-180' : ''"
                                class="w-5 h-5 text-[#F48200] transition-transform duration-200" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>
                        <div x-show="activeFaq === 1" x-collapse>
                            <div class="px-6 pb-5 pt-2">
                                <div
                                    class="bg-[#F48200]/5 border-l-4 border-[#F48200] p-4 rounded-r-lg text-[13px] text-slate-800 leading-relaxed font-medium">
                                    Anda dapat mendaftar sidang setelah kuota bimbingan (minimal 12 kali) terpenuhi, diverifikasi oleh pembimbing, dan telah mendapatkan <strong>'ACC Sidang'</strong> secara digital dari Dosen Pembimbing.
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- FAQ 2 -->
                    <div
                        class="bg-white border border-slate-200 rounded-xl overflow-hidden transition-all shadow-sm">
                        <button @click="activeFaq = activeFaq === 2 ? null : 2"
                            class="w-full px-6 py-4 text-left flex items-center justify-between hover:bg-slate-50 transition-colors">
                            <span class="font-bold text-[14px] text-slate-900">Mengapa status pendaftaran saya masih 'Pending'?</span>
                            <svg :class="activeFaq === 2 ? 'rotate-180' : ''"
                                class="w-5 h-5 text-[#F48200] transition-transform duration-200" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>
                        <div x-show="activeFaq === 2" x-collapse>
                            <div class="px-6 pb-5 pt-2">
                                <div
                                    class="bg-[#F48200]/5 border-l-4 border-[#F48200] p-4 rounded-r-lg text-[13px] text-slate-800 leading-relaxed font-medium">
                                    Koordinator melakukan verifikasi dokumen secara berkala. Pastikan proposal dan dokumen pendukung lainnya sudah lengkap dan sesuai format agar proses verifikasi lebih cepat.
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- FAQ 3 -->
                    <div
                        class="bg-white border border-slate-200 rounded-xl overflow-hidden transition-all shadow-sm">
                        <button @click="activeFaq = activeFaq === 3 ? null : 3"
                            class="w-full px-6 py-4 text-left flex items-center justify-between hover:bg-slate-50 transition-colors">
                            <span class="font-bold text-[14px] text-slate-900">Berapa lama batas waktu pengerjaan revisi?</span>
                            <svg :class="activeFaq === 3 ? 'rotate-180' : ''"
                                class="w-5 h-5 text-[#F48200] transition-transform duration-200" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>
                        <div x-show="activeFaq === 3" x-collapse>
                            <div class="px-6 pb-5 pt-2">
                                <div
                                    class="bg-[#F48200]/5 border-l-4 border-[#F48200] p-4 rounded-r-lg text-[13px] text-slate-800 leading-relaxed font-medium">
                                    Sesuai peraturan, Anda memiliki waktu maksimal <strong>5 hari kerja</strong> untuk menyelesaikan revisi. Keterlambatan akan mengakibatkan penurunan grade nilai akhir secara otomatis oleh sistem.
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- FAQ 4 -->
                    <div
                        class="bg-white border border-slate-200 rounded-xl overflow-hidden transition-all shadow-sm">
                        <button @click="activeFaq = activeFaq === 4 ? null : 4"
                            class="w-full px-6 py-4 text-left flex items-center justify-between hover:bg-slate-50 transition-colors">
                            <span class="font-bold text-[14px] text-slate-900">Tanda tangan saya tidak muncul di Berita Acara?</span>
                            <svg :class="activeFaq === 4 ? 'rotate-180' : ''"
                                class="w-5 h-5 text-[#F48200] transition-transform duration-200" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>
                        <div x-show="activeFaq === 4" x-collapse>
                            <div class="px-6 pb-5 pt-2">
                                <div
                                    class="bg-[#F48200]/5 border-l-4 border-[#F48200] p-4 rounded-r-lg text-[13px] text-slate-800 leading-relaxed font-medium">
                                    Pastikan Anda sudah <strong>menggambar langsung</strong> tanda tangan digital di menu <strong>'Profil'</strong>. Tanda tangan ini wajib ada agar dokumen PDF dapat dihasilkan dengan lengkap oleh sistem.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-dashboard-layout>
