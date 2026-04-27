<x-dashboard-layout header="Panduan Website & Prosedur Dosen" userName="{{ auth()->user()->name }}" roleName="DOSEN">
    <x-slot:sidebar>
        @include('dosen.components.sidebar', ['active' => 'panduan'])
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
                class="bg-gradient-to-br from-[#8B6E3C] via-[#CDA057] to-[#8B6E3C] rounded-[15px] p-10 mb-8 text-white shadow-xl relative overflow-hidden border border-[#B88A4A]">
                <div class="relative z-10 max-w-3xl">
                    <div
                        class="inline-flex items-center gap-2 bg-white/20 text-white px-3 py-1 rounded-full text-[11px] font-bold uppercase tracking-wider mb-4 border border-white/30">
                        <span class="w-2 h-2 bg-white rounded-full animate-pulse"></span>
                        Panduan Resmi Dosen
                    </div>
                    <h1 class="text-3xl font-extrabold mb-3 tracking-tight">Pusat Informasi Kerja Praktek</h1>
                    <p class="text-white/90 leading-relaxed text-[14px] font-medium">
                        Sistem ini mengintegrasikan seluruh tahapan Kerja Praktek secara digital. Mulai dari pemantauan
                        bimbingan,
                        validasi kelayakan sidang, penginputan nilai ujian, hingga pengesahan dokumen Berita Acara
                        melalui Tanda Tangan Digital.
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
                    :class="tab === 'alur' ? 'border-[#CDA057] text-[#CDA057] font-bold bg-[#CDA057]/10' : 'border-transparent text-slate-700 font-medium hover:text-[#CDA057] hover:border-slate-300'"
                    class="px-8 py-4 border-b-2 text-[13px] transition-all rounded-t-lg">
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 6h16M4 12h16m-7 6h7" />
                        </svg>
                        Fungsi Navigasi & Menu
                    </div>
                </button>
                <button @click="tab = 'prosedur'"
                    :class="tab === 'prosedur' ? 'border-[#CDA057] text-[#CDA057] font-bold bg-[#CDA057]/10' : 'border-transparent text-slate-700 font-medium hover:text-[#CDA057] hover:border-slate-300'"
                    class="px-8 py-4 border-b-2 text-[13px] transition-all rounded-t-lg">
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                        </svg>
                        Prosedur & Ketentuan
                    </div>
                </button>
                <button @click="tab = 'faq'"
                    :class="tab === 'faq' ? 'border-[#CDA057] text-[#CDA057] font-bold bg-[#CDA057]/10' : 'border-transparent text-slate-700 font-medium hover:text-[#CDA057] hover:border-slate-300'"
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
                            class="bg-slate-50 rounded-[12px] border border-slate-200 p-5 shadow-sm hover:border-[#CDA057] transition-all group">
                            <div class="flex items-center gap-4 mb-3">
                                <div
                                    class="w-10 h-10 bg-[#CDA057] text-white rounded-lg flex items-center justify-center transition-colors shadow-md">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                                    </svg>
                                </div>
                                <h3 class="font-bold text-slate-900 text-[15px]">Dashboard</h3>
                            </div>
                            <p class="text-[12px] text-slate-800 leading-relaxed font-medium">
                                Pusat ringkasan informasi. Anda dapat melihat beban bimbingan aktif, jumlah mahasiswa
                                yang menunggu persetujuan sidang, dan daftar agenda sidang terdekat hari ini.
                            </p>
                        </div>

                        <!-- Daftar Mahasiswa -->
                        <div
                            class="bg-slate-50 rounded-[12px] border border-slate-200 p-5 shadow-sm hover:border-[#CDA057] transition-all group">
                            <div class="flex items-center gap-4 mb-3">
                                <div
                                    class="w-10 h-10 bg-[#CDA057] text-white rounded-lg flex items-center justify-center transition-colors shadow-md">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                    </svg>
                                </div>
                                <h3 class="font-bold text-slate-900 text-[15px]">Daftar Mahasiswa</h3>
                            </div>
                            <p class="text-[12px] text-slate-800 leading-relaxed font-medium">
                                Mengelola mahasiswa bimbingan. Di halaman ini Anda wajib memvalidasi (ACC) setiap entri
                                logbook bimbingan mahasiswa sebagai prasyarat utama mereka untuk mendaftar sidang.
                            </p>
                        </div>

                        <!-- Persetujuan Sidang -->
                        <div
                            class="bg-slate-50 rounded-[12px] border border-slate-200 p-5 shadow-sm hover:border-[#CDA057] transition-all group">
                            <div class="flex items-center gap-4 mb-3">
                                <div
                                    class="w-10 h-10 bg-[#CDA057] text-white rounded-lg flex items-center justify-center transition-colors shadow-md">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <h3 class="font-bold text-slate-900 text-[15px]">Persetujuan Sidang</h3>
                            </div>
                            <p class="text-[12px] text-slate-800 leading-relaxed font-medium">
                                Validasi kelayakan maju sidang. Anda dapat memeriksa draft laporan akhir dan memberikan
                                ACC digital agar mahasiswa dapat memproses pendaftaran sidang ke Koordinator.
                            </p>
                        </div>

                        <!-- Jadwal Sidang -->
                        <div
                            class="bg-slate-50 rounded-[12px] border border-slate-200 p-5 shadow-sm hover:border-[#CDA057] transition-all group">
                            <div class="flex items-center gap-4 mb-3">
                                <div
                                    class="w-10 h-10 bg-[#CDA057] text-white rounded-lg flex items-center justify-center transition-colors shadow-md">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                </div>
                                <h3 class="font-bold text-slate-900 text-[15px]">Jadwal Sidang</h3>
                            </div>
                            <p class="text-[12px] text-slate-800 leading-relaxed font-medium">
                                Agenda tugas menguji. Menampilkan informasi detail mengenai waktu, lokasi ruangan
                                (offline/online), dan daftar mahasiswa yang harus Anda uji sebagai Penguji 1 atau
                                Penguji 2.
                            </p>
                        </div>

                        <!-- Input Nilai -->
                        <div
                            class="bg-slate-50 rounded-[12px] border border-slate-200 p-5 shadow-sm hover:border-[#CDA057] transition-all group">
                            <div class="flex items-center gap-4 mb-3">
                                <div
                                    class="w-10 h-10 bg-[#CDA057] text-white rounded-lg flex items-center justify-center transition-colors shadow-md">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                    </svg>
                                </div>
                                <h3 class="font-bold text-slate-900 text-[15px]">Input Nilai</h3>
                            </div>
                            <p class="text-[12px] text-slate-800 leading-relaxed font-medium">
                                Pengisian komponen nilai sidang. Anda dapat menginput nilai berdasarkan kriteria
                                (Presentasi, Laporan, dll) dan mengunduh formulir penilaian resmi yang sudah terisi
                                otomatis.
                            </p>
                        </div>

                        <!-- Berita Acara -->
                        <div
                            class="bg-slate-50 rounded-[12px] border border-slate-200 p-5 shadow-sm hover:border-[#CDA057] transition-all group">
                            <div class="flex items-center gap-4 mb-3">
                                <div
                                    class="w-10 h-10 bg-[#CDA057] text-white rounded-lg flex items-center justify-center transition-colors shadow-md">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9.5a2.5 2.5 0 00-2.5-2.5H15" />
                                    </svg>
                                </div>
                                <h3 class="font-bold text-slate-900 text-[15px]">Berita Acara</h3>
                            </div>
                            <p class="text-[12px] text-slate-800 leading-relaxed font-medium">
                                Rekap hasil sidang kelompok. Menampilkan status akhir kelulusan mahasiswa dan Berita
                                Acara yang telah divalidasi oleh seluruh tim penguji dan koordinator.
                            </p>
                        </div>

                        <!-- Revisi -->
                        <div
                            class="bg-slate-50 rounded-[12px] border border-slate-200 p-5 shadow-sm hover:border-[#CDA057] transition-all group">
                            <div class="flex items-center gap-4 mb-3">
                                <div
                                    class="w-10 h-10 bg-[#CDA057] text-white rounded-lg flex items-center justify-center transition-colors shadow-md">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                </div>
                                <h3 class="font-bold text-slate-900 text-[15px]">Revisi</h3>
                            </div>
                            <p class="text-[12px] text-slate-800 leading-relaxed font-medium">
                                Validasi perbaikan laporan. Khusus bagi Dosen yang memberikan catatan revisi, halaman
                                ini digunakan untuk menyetujui dokumen final yang telah diperbaiki mahasiswa.
                            </p>
                        </div>

                        <!-- Notifikasi -->
                        <div
                            class="bg-slate-50 rounded-[12px] border border-slate-200 p-5 shadow-sm hover:border-[#CDA057] transition-all group">
                            <div class="flex items-center gap-4 mb-3">
                                <div
                                    class="w-10 h-10 bg-slate-600 text-white rounded-lg flex items-center justify-center transition-colors shadow-md">
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                        <path
                                            d="M10 2a6 6 0 00-6 6v3.586l-1.707 1.707A1 1 0 003 14h14a1 1 0 00.707-1.707L16 11.586V8a6 6 0 00-6-6zM10 18a3 3 0 01-3-3h6a3 3 0 01-3 3z" />
                                    </svg>
                                </div>
                                <h3 class="font-bold text-slate-900 text-[15px]">Notifikasi</h3>
                            </div>
                            <p class="text-[12px] text-slate-800 leading-relaxed font-medium">
                                Pemberitahuan real-time. Informasi mengenai jadwal menguji baru, pengajuan ACC sidang
                                dari mahasiswa bimbingan, atau pesan penting dari Koordinator.
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
                                class="p-4 bg-white border-2 border-dashed border-[#CDA057] rounded-lg relative overflow-hidden group hover:bg-[#CDA057]/5 transition-all">
                                <div class="flex items-center gap-3 mb-2">
                                    <div
                                        class="w-8 h-8 bg-[#CDA057] text-white rounded flex items-center justify-center">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                        </svg>
                                    </div>
                                    <h4 class="text-[13px] font-bold text-slate-900">Tanda Tangan Digital</h4>
                                </div>
                                <p class="text-[11px] text-slate-700 leading-relaxed font-medium">
                                    Fitur terpenting di menu Profil. Anda wajib <strong>menggambar langsung</strong>
                                    tanda tangan digital Anda pada canvas yang disediakan. Tanda tangan ini akan
                                    digunakan di seluruh dokumen PDF sistem.
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-slate-900 text-slate-300 rounded-[12px] p-6 shadow-xl relative overflow-hidden">
                        <div class="relative z-10">
                            <h3 class="text-[14px] font-bold text-white mb-3 flex items-center gap-2">
                                <svg class="w-4 h-4 text-yellow-400" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                Ketentuan Kuota
                            </h3>
                            <p class="text-[11px] text-slate-100 leading-relaxed font-medium">
                                Sesuai standar operasional, mahasiswa hanya dapat meminta Persetujuan Sidang jika
                                minimal <strong>12 bimbingan</strong> telah Anda verifikasi (ACC) di menu Daftar
                                Mahasiswa.
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- TAB: Prosedur & Ketentuan -->
            <div x-show="tab === 'prosedur'" x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0"
                class="space-y-8" style="display:none;">

                <div class="bg-white border border-slate-200 rounded-[15px] p-8 shadow-sm">
                    <h2 class="text-xl font-bold text-slate-900 mb-8 flex items-center gap-3">
                        <div
                            class="w-10 h-10 bg-[#CDA057] rounded-xl flex items-center justify-center text-white shadow-lg shadow-orange-100">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                            </svg>
                        </div>
                        Alur Kerja & Kewajiban Dosen
                    </h2>

                    <div
                        class="space-y-12 relative before:content-[''] before:absolute before:left-[19px] before:top-2 before:bottom-2 before:w-[2px] before:bg-slate-100">
                        <!-- Step 1 -->
                        <div class="relative pl-12">
                            <div
                                class="absolute left-0 w-10 h-10 bg-white border-2 border-[#CDA057] rounded-full flex items-center justify-center z-10">
                                <div class="w-2 h-2 bg-[#CDA057] rounded-full"></div>
                            </div>
                            <h3 class="font-bold text-slate-900 text-[15px] mb-2">1. Penugasan Mahasiswa Bimbingan</h3>
                            <p class="text-[12px] text-slate-700 leading-relaxed font-medium">
                                Dosen menerima notifikasi melalui dashboard saat Koordinator menetapkan Anda sebagai
                                Pembimbing. Segera cek menu <strong>'Daftar Mahasiswa'</strong> untuk memulai proses
                                pemantauan.
                            </p>
                        </div>

                        <!-- Step 2 -->
                        <div class="relative pl-12">
                            <div
                                class="absolute left-0 w-10 h-10 bg-white border-2 border-[#CDA057] rounded-full flex items-center justify-center z-10">
                                <div class="w-2 h-2 bg-[#CDA057] rounded-full"></div>
                            </div>
                            <h3 class="font-bold text-slate-900 text-[15px] mb-2">2. Monitoring Progres & Validasi
                                Logbook</h3>
                            <p class="text-[12px] text-slate-700 leading-relaxed font-medium mb-3">
                                Dosen wajib meninjau logbook yang diinput mahasiswa secara berkala. Berikan
                                <strong>ACC</strong> pada setiap entri yang valid.
                            </p>
                            <div
                                class="bg-amber-50 border-l-4 border-[#CDA057] p-3 text-[11px] text-amber-900 font-bold">
                                KETENTUAN: Mahasiswa tidak dapat mendaftar sidang jika Anda belum memvalidasi minimal 12
                                entri bimbingan.
                            </div>
                        </div>

                        <!-- Step 3 -->
                        <div class="relative pl-12">
                            <div
                                class="absolute left-0 w-10 h-10 bg-white border-2 border-[#CDA057] rounded-full flex items-center justify-center z-10">
                                <div class="w-2 h-2 bg-[#CDA057] rounded-full"></div>
                            </div>
                            <h3 class="font-bold text-slate-900 text-[15px] mb-2">3. Verifikasi Laporan & Izin Sidang
                            </h3>
                            <p class="text-[12px] text-slate-700 leading-relaxed font-medium mb-3">
                                Memeriksa draft laporan akhir mahasiswa. Jika sudah layak, berikan <strong>ACC
                                    Sidang</strong> pada menu Persetujuan Sidang agar mahasiswa dapat mendaftar ke
                                Koordinator.
                            </p>
                        </div>

                        <!-- Step 4 -->
                        <div class="relative pl-12">
                            <div
                                class="absolute left-0 w-10 h-10 bg-white border-2 border-[#CDA057] rounded-full flex items-center justify-center z-10">
                                <div class="w-2 h-2 bg-[#CDA057] rounded-full"></div>
                            </div>
                            <h3 class="font-bold text-slate-900 text-[15px] mb-2">4. Pengujian Sidang & Input Nilai</h3>
                            <p class="text-[12px] text-slate-700 leading-relaxed font-medium mb-3">
                                Melaksanakan sidang sesuai jadwal. Setelah sidang berakhir, Dosen wajib menginput nilai
                                pada menu <strong>'Input Nilai'</strong> sebelum Berita Acara dapat digenerate.
                            </p>
                        </div>

                        <!-- Step 5 -->
                        <div class="relative pl-12">
                            <div
                                class="absolute left-0 w-10 h-10 bg-white border-2 border-[#CDA057] rounded-full flex items-center justify-center z-10">
                                <div class="w-2 h-2 bg-[#CDA057] rounded-full"></div>
                            </div>
                            <h3 class="font-bold text-slate-900 text-[15px] mb-2">5. Validasi Revisi & Tanda Tangan
                                Berita Acara</h3>
                            <p class="text-[12px] text-slate-700 leading-relaxed font-medium mb-3">
                                Jika ada revisi, periksa perbaikan mahasiswa di menu Revisi. Setelah semua beres, Berita
                                Acara akan divalidasi menggunakan <strong>Tanda Tangan Digital</strong> Anda.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Penalty Info for Dosen -->
                <div class="bg-slate-900 text-white rounded-[15px] p-8 shadow-xl relative overflow-hidden">
                    <div class="relative z-10">
                        <h3 class="text-lg font-bold mb-4 flex items-center gap-2 text-yellow-400">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                            Penting: Kelengkapan Berita Acara
                        </h3>
                        <p class="text-[13px] text-slate-300 leading-relaxed font-medium">
                            Sistem tidak akan mengizinkan pengunduhan Berita Acara oleh mahasiswa jika:
                        </p>
                        <ul class="mt-4 space-y-2 text-[12px] text-slate-400 font-medium">
                            <li class="flex items-start gap-2">
                                <span class="text-yellow-500 mt-1">●</span>
                                <span>Nilai dari seluruh Penguji (Penguji 1 & 2) belum lengkap diinput.</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <span class="text-yellow-500 mt-1">●</span>
                                <span>Dosen belum memiliki data <strong>Tanda Tangan Digital</strong> di menu
                                    Profil.</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <span class="text-yellow-500 mt-1">●</span>
                                <span>Status revisi (jika ada) belum divalidasi menjadi 'Disetujui'.</span>
                            </li>
                        </ul>
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
                        <p class="text-slate-600 text-[13px]">Temukan jawaban cepat untuk kendala yang sering dialami
                            oleh para Dosen.</p>
                    </div>

                    <div class="space-y-3" x-data="{ activeFaq: null }">
                        <!-- FAQ 1 -->
                        <div
                            class="bg-white border border-slate-200 rounded-xl overflow-hidden transition-all shadow-sm">
                            <button @click="activeFaq = activeFaq === 1 ? null : 1"
                                class="w-full px-6 py-4 text-left flex items-center justify-between hover:bg-slate-50 transition-colors">
                                <span class="font-bold text-[14px] text-slate-900">Bagaimana cara melakukan ACC
                                    Bimbingan mahasiswa?</span>
                                <svg :class="activeFaq === 1 ? 'rotate-180' : ''"
                                    class="w-5 h-5 text-[#CDA057] transition-transform duration-200" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>
                            <div x-show="activeFaq === 1" x-collapse>
                                <div class="px-6 pb-5 pt-2">
                                    <div
                                        class="bg-[#CDA057]/5 border-l-4 border-[#CDA057] p-4 rounded-r-lg text-[13px] text-slate-800 leading-relaxed font-medium">
                                        Buka menu <strong>'Daftar Mahasiswa'</strong>, pilih mahasiswa yang
                                        bersangkutan, klik <strong>'Detail'</strong>, kemudian klik tombol
                                        <strong>'ACC'</strong> pada setiap entri bimbingan yang valid.
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- FAQ 2 -->
                        <div
                            class="bg-white border border-slate-200 rounded-xl overflow-hidden transition-all shadow-sm">
                            <button @click="activeFaq = activeFaq === 2 ? null : 2"
                                class="w-full px-6 py-4 text-left flex items-center justify-between hover:bg-slate-50 transition-colors">
                                <span class="font-bold text-[14px] text-slate-900">Mengapa saya tidak bisa memberikan
                                    ACC Sidang?</span>
                                <svg :class="activeFaq === 2 ? 'rotate-180' : ''"
                                    class="w-5 h-5 text-[#CDA057] transition-transform duration-200" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>
                            <div x-show="activeFaq === 2" x-collapse>
                                <div class="px-6 pb-5 pt-2">
                                    <div
                                        class="bg-[#CDA057]/5 border-l-4 border-[#CDA057] p-4 rounded-r-lg text-[13px] text-slate-800 leading-relaxed font-medium">
                                        Mahasiswa hanya dapat diberikan <strong>ACC Sidang</strong> jika kuota minimal
                                        bimbingan (12 kali) telah terpenuhi dan seluruhnya telah Anda verifikasi (ACC)
                                        di sistem.
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- FAQ 3 -->
                        <div
                            class="bg-white border border-slate-200 rounded-xl overflow-hidden transition-all shadow-sm">
                            <button @click="activeFaq = activeFaq === 3 ? null : 3"
                                class="w-full px-6 py-4 text-left flex items-center justify-between hover:bg-slate-50 transition-colors">
                                <span class="font-bold text-[14px] text-slate-900">Bagaimana cara menginput nilai sidang
                                    mahasiswa?</span>
                                <svg :class="activeFaq === 3 ? 'rotate-180' : ''"
                                    class="w-5 h-5 text-[#CDA057] transition-transform duration-200" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>
                            <div x-show="activeFaq === 3" x-collapse>
                                <div class="px-6 pb-5 pt-2">
                                    <div
                                        class="bg-[#CDA057]/5 border-l-4 border-[#CDA057] p-4 rounded-r-lg text-[13px] text-slate-800 leading-relaxed font-medium">
                                        Masuk ke menu <strong>'Input Nilai'</strong>, pilih mahasiswa, dan masukkan
                                        angka 0-100 pada setiap komponen. Sistem akan otomatis menghitung Grade akhir
                                        berdasarkan bobot yang berlaku.
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- FAQ 4 -->
                        <div
                            class="bg-white border border-slate-200 rounded-xl overflow-hidden transition-all shadow-sm">
                            <button @click="activeFaq = activeFaq === 4 ? null : 4"
                                class="w-full px-6 py-4 text-left flex items-center justify-between hover:bg-slate-50 transition-colors">
                                <span class="font-bold text-[14px] text-slate-900">Tanda tangan saya tidak muncul di
                                    Berita Acara?</span>
                                <svg :class="activeFaq === 4 ? 'rotate-180' : ''"
                                    class="w-5 h-5 text-[#CDA057] transition-transform duration-200" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>
                            <div x-show="activeFaq === 4" x-collapse>
                                <div class="px-6 pb-5 pt-2">
                                    <div
                                        class="bg-[#CDA057]/5 border-l-4 border-[#CDA057] p-4 rounded-r-lg text-[13px] text-slate-800 leading-relaxed font-medium">
                                        Pastikan Anda telah <strong>menggambar langsung</strong> tanda tangan digital
                                        Anda di menu <strong>'Profil'</strong>. Tanpa data tanda tangan ini, dokumen PDF
                                        (Berita Acara & Nilai) tidak dapat dihasilkan oleh sistem.
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

        </div>
</x-dashboard-layout>