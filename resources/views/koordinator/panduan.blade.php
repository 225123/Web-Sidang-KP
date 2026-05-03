<x-dashboard-layout header="Panduan Website & Prosedur Koordinator" userName="{{ auth()->user()->name }}" roleName="KOORDINATOR KP" hidePeriodSelector="true">
    <x-slot:sidebar>
        @include('koordinator.components.sidebar', ['active' => 'panduan'])
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
            style="background: linear-gradient(135deg, #3A9475 0%, #4CC098 50%, #3A9475 100%);"
            class="rounded-[15px] p-10 mb-8 text-white shadow-xl relative overflow-hidden border border-[#44AF8A]">
            <div class="relative z-10 max-w-3xl">
                <div
                    class="inline-flex items-center gap-2 bg-white/20 text-white px-3 py-1 rounded-full text-[11px] font-bold uppercase tracking-wider mb-4 border border-white/30">
                    <span class="w-2 h-2 bg-white rounded-full animate-pulse"></span>
                    Panduan Resmi Koordinator
                </div>
                <h1 class="text-3xl font-extrabold mb-3 tracking-tight">Pusat Kendali Kerja Praktek</h1>
                <p class="text-white/90 leading-relaxed text-[14px] font-medium">
                    Sebagai Koordinator, Anda memiliki otoritas penuh untuk mengelola siklus hidup Kerja Praktek (KP). 
                    Mulai dari manajemen periode, verifikasi berkas, ploting pembimbing dan penguji, hingga finalisasi nilai akhir dan pengarsipan laporan.
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
                :class="tab === 'alur' ? 'border-[#4CC098] text-[#4CC098] font-bold bg-[#4CC098]/10' : 'border-transparent text-slate-700 font-medium hover:text-[#4CC098] hover:border-slate-300'"
                class="px-8 py-4 border-b-2 text-[13px] transition-all rounded-t-lg">
                <div class="flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 12h16m-7 6h7" />
                    </svg>
                    Fungsi Manajemen & Menu
                </div>
            </button>
            <button @click="tab = 'prosedur'"
                :class="tab === 'prosedur' ? 'border-[#4CC098] text-[#4CC098] font-bold bg-[#4CC098]/10' : 'border-transparent text-slate-700 font-medium hover:text-[#4CC098] hover:border-slate-300'"
                class="px-8 py-4 border-b-2 text-[13px] transition-all rounded-t-lg">
                <div class="flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                    Prosedur Pengelolaan
                </div>
            </button>
            <button @click="tab = 'faq'"
                :class="tab === 'faq' ? 'border-[#4CC098] text-[#4CC098] font-bold bg-[#4CC098]/10' : 'border-transparent text-slate-700 font-medium hover:text-[#4CC098] hover:border-slate-300'"
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

        <!-- TAB: Penjelasan Manajemen -->
        <div x-show="tab === 'alur'" x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0"
            class="flex flex-col lg:flex-row gap-8 items-start">

            <!-- Left: Grid Penjelasan Menu -->
            <div class="flex-1 w-full space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Manajemen Periode -->
                    <div
                        class="bg-slate-50 rounded-[12px] border border-slate-200 p-5 shadow-sm hover:border-[#4CC098] transition-all group">
                        <div class="flex items-center gap-4 mb-3">
                            <div
                                class="w-10 h-10 bg-[#4CC098] text-white rounded-lg flex items-center justify-center transition-colors shadow-md">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                            </div>
                            <h3 class="font-bold text-slate-900 text-[15px]">Periode KP</h3>
                        </div>
                        <p class="text-[12px] text-slate-800 leading-relaxed font-medium">
                            Membuka dan menutup periode pendaftaran KP. Menentukan semester aktif dan batas waktu pendaftaran bagi mahasiswa.
                        </p>
                    </div>

                    <!-- Verifikasi Berkas -->
                    <div
                        class="bg-slate-50 rounded-[12px] border border-slate-200 p-5 shadow-sm hover:border-[#4CC098] transition-all group">
                        <div class="flex items-center gap-4 mb-3">
                            <div
                                class="w-10 h-10 bg-[#4CC098] text-white rounded-lg flex items-center justify-center transition-colors shadow-md">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                            </div>
                            <h3 class="font-bold text-slate-900 text-[15px]">Verifikasi Pendaftaran</h3>
                        </div>
                        <p class="text-[12px] text-slate-800 leading-relaxed font-medium">
                            Memeriksa proposal dan syarat administrasi mahasiswa. Memberikan persetujuan atau penolakan dengan catatan perbaikan.
                        </p>
                    </div>

                    <!-- Penugasan Pembimbing -->
                    <div
                        class="bg-slate-50 rounded-[12px] border border-slate-200 p-5 shadow-sm hover:border-[#4CC098] transition-all group">
                        <div class="flex items-center gap-4 mb-3">
                            <div
                                class="w-10 h-10 bg-[#4CC098] text-white rounded-lg flex items-center justify-center transition-colors shadow-md">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                            </div>
                            <h3 class="font-bold text-slate-900 text-[15px]">Penugasan Pembimbing</h3>
                        </div>
                        <p class="text-[12px] text-slate-800 leading-relaxed font-medium">
                            Menetapkan Dosen Pembimbing bagi mahasiswa yang telah disetujui pendaftarannya, baik secara manual maupun otomatis (auto-assign).
                        </p>
                    </div>

                    <!-- Penjadwalan Sidang -->
                    <div
                        class="bg-slate-50 rounded-[12px] border border-slate-200 p-5 shadow-sm hover:border-[#4CC098] transition-all group">
                        <div class="flex items-center gap-4 mb-3">
                            <div
                                class="w-10 h-10 bg-[#4CC098] text-white rounded-lg flex items-center justify-center transition-colors shadow-md">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                            </div>
                            <h3 class="font-bold text-slate-900 text-[15px]">Penjadwalan Sidang</h3>
                        </div>
                        <p class="text-[12px] text-slate-800 leading-relaxed font-medium">
                            Mengatur waktu, lokasi, dan memploting Dosen Penguji 1 & 2 untuk setiap kelompok mahasiswa yang telah siap sidang.
                        </p>
                    </div>

                    <!-- Finalisasi Nilai -->
                    <div
                        class="bg-slate-50 rounded-[12px] border border-slate-200 p-5 shadow-sm hover:border-[#4CC098] transition-all group">
                        <div class="flex items-center gap-4 mb-3">
                            <div
                                class="w-10 h-10 bg-[#4CC098] text-white rounded-lg flex items-center justify-center transition-colors shadow-md">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" />
                                </svg>
                            </div>
                            <h3 class="font-bold text-slate-900 text-[15px]">Finalisasi Nilai</h3>
                        </div>
                        <p class="text-[12px] text-slate-800 leading-relaxed font-medium">
                            Mengesahkan nilai akhir (Grade) setelah seluruh penguji menginput nilai dan mahasiswa menyelesaikan revisi (jika ada).
                        </p>
                    </div>

                    <!-- Berita Acara -->
                    <div
                        class="bg-slate-50 rounded-[12px] border border-slate-200 p-5 shadow-sm hover:border-[#4CC098] transition-all group">
                        <div class="flex items-center gap-4 mb-3">
                            <div
                                class="w-10 h-10 bg-[#4CC098] text-white rounded-lg flex items-center justify-center transition-colors shadow-md">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9.5a2.5 2.5 0 00-2.5-2.5H15" />
                                </svg>
                            </div>
                            <h3 class="font-bold text-slate-900 text-[15px]">Berita Acara</h3>
                        </div>
                        <p class="text-[12px] text-slate-800 leading-relaxed font-medium">
                            Menghasilkan dan mengesahkan dokumen Berita Acara Sidang kolektif yang telah ditandatangani secara digital oleh tim penguji.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Right: System Info & Profile -->
            <div class="w-full lg:w-[350px] flex flex-col gap-6">
                <div class="bg-slate-50 rounded-[12px] border border-slate-200 p-6 shadow-sm">
                    <h2 class="text-[15px] font-bold text-slate-800 mb-5 flex items-center gap-2">
                        <svg class="w-4 h-4 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z"
                                clip-rule="evenodd" />
                        </svg>
                        Otoritas Koordinator
                    </h2>
                    <div class="space-y-5">
                        <div
                            class="p-4 bg-white border-2 border-dashed border-[#4CC098] rounded-lg relative overflow-hidden group hover:bg-[#4CC098]/5 transition-all">
                            <div class="flex items-center gap-3 mb-2">
                                <div
                                    class="w-8 h-8 bg-[#4CC098] text-white rounded flex items-center justify-center">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                    </svg>
                                </div>
                                <h4 class="text-[13px] font-bold text-slate-900">Tanda Tangan Digital</h4>
                            </div>
                            <p class="text-[11px] text-slate-700 leading-relaxed font-medium">
                                Wajib diatur di menu Profil. Digunakan untuk validasi akhir Berita Acara dan dokumen pengesahan lainnya.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="bg-slate-900 text-slate-300 rounded-[12px] p-6 shadow-xl relative overflow-hidden">
                    <div class="relative z-10">
                        <h3 class="text-[14px] font-bold text-white mb-3 flex items-center gap-2">
                            <svg class="w-4 h-4 text-[#4CC098]" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Manajemen User
                        </h3>
                        <p class="text-[11px] text-slate-100 leading-relaxed font-medium">
                            Anda memiliki akses untuk menambah, mengedit, atau menonaktifkan akun Dosen dan Mahasiswa melalui menu <strong>Manajemen User</strong>.
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- TAB: Prosedur Pengelolaan -->
        <div x-show="tab === 'prosedur'" x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0"
            class="space-y-8" style="display:none;">
            
            <div class="bg-white border border-slate-200 rounded-[15px] p-8 shadow-sm">
                <h2 class="text-xl font-bold text-slate-900 mb-8 flex items-center gap-3">
                    <div class="w-10 h-10 bg-[#4CC098] rounded-xl flex items-center justify-center text-white shadow-lg shadow-green-100">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                    </div>
                    Siklus Kerja Koordinator KP
                </h2>

                <div class="space-y-12 relative before:content-[''] before:absolute before:left-[19px] before:top-2 before:bottom-2 before:w-[2px] before:bg-slate-100">
                    <!-- Step 1 -->
                    <div class="relative pl-12">
                        <div class="absolute left-0 w-10 h-10 bg-white border-2 border-[#4CC098] rounded-full flex items-center justify-center z-10">
                            <div class="w-2 h-2 bg-[#4CC098] rounded-full"></div>
                        </div>
                        <h3 class="font-bold text-slate-900 text-[15px] mb-2">1. Pembukaan Periode & Verifikasi Awal</h3>
                        <p class="text-[12px] text-slate-700 leading-relaxed font-medium">
                            Koordinator membuka periode semester aktif. Mahasiswa mendaftar dan Koordinator memvalidasi kelayakan berkas (Proposal & Admin).
                        </p>
                    </div>

                    <!-- Step 2 -->
                    <div class="relative pl-12">
                        <div class="absolute left-0 w-10 h-10 bg-white border-2 border-[#4CC098] rounded-full flex items-center justify-center z-10">
                            <div class="w-2 h-2 bg-[#4CC098] rounded-full"></div>
                        </div>
                        <h3 class="font-bold text-slate-900 text-[15px] mb-2">2. Ploting Pembimbing & Monitoring</h3>
                        <p class="text-[12px] text-slate-700 leading-relaxed font-medium">
                            Menetapkan pembimbing. Koordinator dapat memantau progres bimbingan seluruh mahasiswa melalui menu <strong>'Progress Umum'</strong>.
                        </p>
                    </div>

                    <!-- Step 3 -->
                    <div class="relative pl-12">
                        <div class="absolute left-0 w-10 h-10 bg-white border-2 border-[#4CC098] rounded-full flex items-center justify-center z-10">
                            <div class="w-2 h-2 bg-[#4CC098] rounded-full"></div>
                        </div>
                        <h3 class="font-bold text-slate-900 text-[15px] mb-2">3. Manajemen Sidang (Jadwal & Penguji)</h3>
                        <p class="text-[12px] text-slate-700 leading-relaxed font-medium">
                            Menyusun jadwal sidang kolektif dan menetapkan tim penguji. Pastikan tidak ada bentrok jadwal antar dosen penguji.
                        </p>
                    </div>

                    <!-- Step 4 -->
                    <div class="relative pl-12">
                        <div class="absolute left-0 w-10 h-10 bg-white border-2 border-[#4CC098] rounded-full flex items-center justify-center z-10">
                            <div class="w-2 h-2 bg-[#4CC098] rounded-full"></div>
                        </div>
                        <h3 class="font-bold text-slate-900 text-[15px] mb-2">4. Validasi Akhir & Pengesahan</h3>
                        <p class="text-[12px] text-slate-700 leading-relaxed font-medium">
                            Memeriksa kelengkapan nilai dari seluruh penguji, memverifikasi revisi, dan melakukan <strong>'Sahkan Nilai'</strong> untuk menerbitkan Grade akhir.
                        </p>
                    </div>
                </div>
            </div>

            <div class="bg-blue-50 border border-blue-200 rounded-[15px] p-8 shadow-sm">
                <div class="flex items-start gap-4">
                    <div class="w-12 h-12 bg-blue-600 text-white rounded-full flex items-center justify-center shrink-0">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                    </div>
                    <div>
                        <h3 class="font-bold text-blue-900 text-[16px] mb-2">Pencadangan Data (Backup)</h3>
                        <p class="text-[12px] text-blue-800 leading-relaxed font-medium mb-4">
                            Sebagai administrator, Anda wajib melakukan backup database secara berkala (minimal sebulan sekali) melalui menu <strong>Backup Database</strong> untuk mencegah kehilangan data.
                        </p>
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
                    <h2 class="text-2xl font-bold text-slate-900 mb-2">FAQ Koordinator</h2>
                    <p class="text-slate-600 text-[13px]">Jawaban teknis untuk tugas harian Koordinator KP.</p>
                </div>

                <div class="space-y-3" x-data="{ activeFaq: null }">
                    <!-- FAQ 1 -->
                    <div class="bg-white border border-slate-200 rounded-xl overflow-hidden shadow-sm">
                        <button @click="activeFaq = activeFaq === 1 ? null : 1"
                            class="w-full px-6 py-4 text-left flex items-center justify-between hover:bg-slate-50 transition-colors">
                            <span class="font-bold text-[14px] text-slate-900">Bagaimana cara mengubah Dosen Pembimbing yang sudah diplot?</span>
                            <svg :class="activeFaq === 1 ? 'rotate-180' : ''" class="w-5 h-5 text-[#4CC098] transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <div x-show="activeFaq === 1" x-collapse>
                            <div class="px-6 pb-5 pt-2">
                                <div class="bg-[#4CC098]/5 border-l-4 border-[#4CC098] p-4 rounded-r-lg text-[13px] text-slate-800 leading-relaxed font-medium">
                                    Anda dapat melakukan ploting ulang di menu <strong>Penugasan Pembimbing</strong>. Gunakan fitur 'Reset Plotting' atau pilih mahasiswa secara spesifik untuk mengubah dosen pembimbingnya.
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- FAQ 2 -->
                    <div class="bg-white border border-slate-200 rounded-xl overflow-hidden shadow-sm">
                        <button @click="activeFaq = activeFaq === 2 ? null : 2"
                            class="w-full px-6 py-4 text-left flex items-center justify-between hover:bg-slate-50 transition-colors">
                            <span class="font-bold text-[14px] text-slate-900">Mengapa Grade nilai mahasiswa tidak muncul otomatis?</span>
                            <svg :class="activeFaq === 2 ? 'rotate-180' : ''" class="w-5 h-5 text-[#4CC098] transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <div x-show="activeFaq === 2" x-collapse>
                            <div class="px-6 pb-5 pt-2">
                                <div class="bg-[#4CC098]/5 border-l-4 border-[#4CC098] p-4 rounded-r-lg text-[13px] text-slate-800 leading-relaxed font-medium">
                                    Grade hanya akan dihitung setelah Koordinator menekan tombol <strong>'Sahkan Nilai'</strong> di menu Finalisasi Nilai. Pastikan seluruh penguji (1, 2, dan supervisor) sudah mengisi nilai.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-dashboard-layout>
