<x-dashboard-layout header="Panduan Website & Aturan Kerja Praktek" userName="{{ auth()->user()->name }}" roleName="MAHASISWA">
    <x-slot:sidebar>
        @include('mahasiswa.components.sidebar', ['active' => 'panduan'])
    </x-slot>

    <style>
        [x-cloak] { display: none !important; }
        .step-container::before {
            content: '';
            position: absolute;
            left: 24px;
            top: 40px;
            bottom: -40px;
            width: 2px;
            background: #E5E7EB;
            z-index: 0;
        }
        .step-container:last-child::before {
            display: none;
        }
    </style>

    <div class="mt-8 px-4 w-full pb-20" x-data="{ tab: 'alur' }">
        <!-- Banner -->
        <div class="bg-gradient-to-r from-blue-700 to-indigo-800 rounded-[10px] p-8 mb-8 text-white shadow-lg relative overflow-hidden">
            <div class="relative z-10 max-w-3xl">
                <h1 class="text-2xl font-bold mb-2">Pusat Informasi Kerja Praktek Mahasiswa</h1>
                <p class="text-blue-100 leading-relaxed text-[13px]">
                    Sistem ini dirancang untuk mendampingi seluruh proses Kerja Praktek (KP) Anda dari pendaftaran hingga nilai akhir. 
                    Pelajari alur kerja sistem, fungsionalitas setiap menu, serta peraturan dan sanksi (pinalti) yang berlaku selama pelaksanaan KP.
                </p>
            </div>
            <div class="absolute right-0 top-0 bottom-0 opacity-10 pointer-events-none">
                <svg class="w-64 h-full" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2L2 22h20L12 2zm0 4.5l6.5 13h-13L12 6.5z"/></svg>
            </div>
        </div>

        <!-- Navigation Tabs -->
        <div class="flex flex-wrap gap-2 border-b border-gray-200 mb-8">
            <button @click="tab = 'alur'" :class="tab === 'alur' ? 'border-blue-600 text-blue-600 font-bold bg-blue-50/50' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'" class="px-6 py-3 border-b-2 text-[13px] transition-colors rounded-t-lg">
                Alur & Fitur Sistem
            </button>
            <button @click="tab = 'peraturan'" :class="tab === 'peraturan' ? 'border-red-600 text-red-600 font-bold bg-red-50/50' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'" class="px-6 py-3 border-b-2 text-[13px] transition-colors rounded-t-lg">
                Peraturan & Pinalti KP
            </button>
            <button @click="tab = 'faq'" :class="tab === 'faq' ? 'border-green-600 text-green-600 font-bold bg-green-50/50' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'" class="px-6 py-3 border-b-2 text-[13px] transition-colors rounded-t-lg">
                Bantuan & Kontak
            </button>
        </div>

        <!-- TAB: Alur & Fitur Sistem -->
        <div x-show="tab === 'alur'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" class="flex flex-col lg:flex-row gap-8 items-start">
            
            <!-- Left: Timeline Alur KP -->
            <div class="flex-1 bg-white rounded-[10px] border border-gray-200 shadow-sm p-6 w-full">
                <h2 class="text-lg font-bold text-gray-800 border-b border-gray-100 pb-4 mb-6 flex items-center gap-2">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    Penjelasan Fitur Berdasarkan Alur KP
                </h2>

                <div class="space-y-0 relative">
                    <!-- Step 1 -->
                    <div class="relative step-container pb-8">
                        <div class="flex gap-4 relative z-10">
                            <div class="w-10 h-10 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center font-bold text-[15px] shrink-0 border-4 border-white shadow-sm">1</div>
                            <div class="pt-1">
                                <h3 class="font-bold text-gray-800 text-[14px]">Tahap Pra-Pelaksanaan</h3>
                                <div class="bg-gray-50 border border-gray-100 rounded-lg p-5 mt-3 space-y-4">
                                    <div class="border-b border-gray-200 pb-3 last:border-0 last:pb-0">
                                        <div class="mb-2">
                                            <span class="text-blue-700 font-bold text-[12px] bg-blue-100 px-3 py-1 rounded inline-block">Dashboard</span>
                                        </div>
                                        <p class="text-[12px] text-gray-600 leading-relaxed">Beranda utama untuk memantau ringkasan status, pengumuman terbaru dari Koordinator, dan akses cepat ke fitur utama.</p>
                                    </div>
                                    <div class="border-b border-gray-200 pb-3 last:border-0 last:pb-0">
                                        <div class="mb-2">
                                            <span class="text-blue-700 font-bold text-[12px] bg-blue-100 px-3 py-1 rounded inline-block">Mendaftar KP</span>
                                        </div>
                                        <p class="text-[12px] text-gray-600 leading-relaxed">Menu untuk menginput detail perusahaan, melampirkan proposal KP, dan mendaftarkan anggota kelompok (jika kolektif).</p>
                                    </div>
                                    <div class="border-b border-gray-200 pb-3 last:border-0 last:pb-0">
                                        <div class="mb-2">
                                            <span class="text-blue-700 font-bold text-[12px] bg-blue-100 px-3 py-1 rounded inline-block">Status Pendaftaran</span>
                                        </div>
                                        <p class="text-[12px] text-gray-600 leading-relaxed">Mengecek status verifikasi pendaftaran Anda. Jika ditolak, Anda dapat melihat alasan penolakan dan mengajukan ulang pendaftaran. Jika disetujui, Anda akan menunggu plotting Dosen Pembimbing.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Step 2 -->
                    <div class="relative step-container pb-8">
                        <div class="flex gap-4 relative z-10">
                            <div class="w-10 h-10 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center font-bold text-[15px] shrink-0 border-4 border-white shadow-sm">2</div>
                            <div class="pt-1">
                                <h3 class="font-bold text-gray-800 text-[14px]">Tahap Pelaksanaan & Bimbingan</h3>
                                <div class="bg-gray-50 border border-gray-100 rounded-lg p-5 mt-3 space-y-4">
                                    <div class="border-b border-gray-200 pb-3 last:border-0 last:pb-0">
                                        <div class="mb-2">
                                            <span class="text-green-700 font-bold text-[12px] bg-green-100 px-3 py-1 rounded inline-block">Log Bimbingan Dosen</span>
                                        </div>
                                        <p class="text-[12px] text-gray-600 leading-relaxed">Mencatat aktivitas bimbingan yang telah dilakukan dengan Dosen Pembimbing. Mahasiswa <strong>WAJIB</strong> memenuhi kuota minimal bimbingan (umumnya 12 kali) sebagai syarat pendaftaran sidang. Dosen harus memverifikasi log tersebut agar terhitung.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Step 3 -->
                    <div class="relative step-container pb-8">
                        <div class="flex gap-4 relative z-10">
                            <div class="w-10 h-10 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center font-bold text-[15px] shrink-0 border-4 border-white shadow-sm">3</div>
                            <div class="pt-1">
                                <h3 class="font-bold text-gray-800 text-[14px]">Tahap Pra-Sidang</h3>
                                <div class="bg-gray-50 border border-gray-100 rounded-lg p-5 mt-3 space-y-4">
                                    <div class="border-b border-gray-200 pb-3 last:border-0 last:pb-0">
                                        <div class="mb-2">
                                            <span class="text-orange-700 font-bold text-[12px] bg-orange-100 px-3 py-1 rounded inline-block">Persetujuan Sidang KP</span>
                                        </div>
                                        <p class="text-[12px] text-gray-600 leading-relaxed">Menu untuk meminta acc/persetujuan maju sidang kepada Dosen Pembimbing setelah kuota bimbingan terpenuhi dan laporan akhir selesai dikerjakan.</p>
                                    </div>
                                    <div class="border-b border-gray-200 pb-3 last:border-0 last:pb-0">
                                        <div class="mb-2">
                                            <span class="text-orange-700 font-bold text-[12px] bg-orange-100 px-3 py-1 rounded inline-block">Pendaftaran Sidang</span>
                                        </div>
                                        <p class="text-[12px] text-gray-600 leading-relaxed">Setelah mendapat izin dari Pembimbing, unggah dokumen kelengkapan sidang (Laporan, Bukti Logbook, Surat Selesai KP dari instansi, dll) untuk divalidasi oleh Koordinator.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Step 4 -->
                    <div class="relative step-container pb-8">
                        <div class="flex gap-4 relative z-10">
                            <div class="w-10 h-10 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center font-bold text-[15px] shrink-0 border-4 border-white shadow-sm">4</div>
                            <div class="pt-1">
                                <h3 class="font-bold text-gray-800 text-[14px]">Tahap Sidang & Pasca-Sidang</h3>
                                <div class="bg-gray-50 border border-gray-100 rounded-lg p-5 mt-3 space-y-4">
                                    <div class="border-b border-gray-200 pb-3 last:border-0 last:pb-0">
                                        <div class="mb-2">
                                            <span class="text-purple-700 font-bold text-[12px] bg-purple-100 px-3 py-1 rounded inline-block">Jadwal Sidang</span>
                                        </div>
                                        <p class="text-[12px] text-gray-600 leading-relaxed">Melihat detail waktu, lokasi ruangan, dan daftar Dosen Penguji yang telah diplot oleh Koordinator.</p>
                                    </div>
                                    <div class="border-b border-gray-200 pb-3 last:border-0 last:pb-0">
                                        <div class="mb-2">
                                            <span class="text-purple-700 font-bold text-[12px] bg-purple-100 px-3 py-1 rounded inline-block">Revisi</span>
                                        </div>
                                        <p class="text-[12px] text-gray-600 leading-relaxed">Wajib dilakukan jika Dosen Penguji memberikan catatan perbaikan pada saat sidang. Unggah dokumen revisi agar dapat disetujui (ACC) oleh Penguji. <strong>Batas waktu revisi adalah 5 hari setelah sidang</strong>.</p>
                                    </div>
                                    <div class="border-b border-gray-200 pb-3 last:border-0 last:pb-0">
                                        <div class="mb-2">
                                            <span class="text-purple-700 font-bold text-[12px] bg-purple-100 px-3 py-1 rounded inline-block">Nilai Akhir KP</span>
                                        </div>
                                        <p class="text-[12px] text-gray-600 leading-relaxed">Melihat hasil akhir sidang, rincian komponen penilaian (Pembimbing, Penguji 1, Penguji 2), serta mengunduh form Berita Acara Sidang jika dinyatakan Lulus.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right: Supporting Features -->
            <div class="w-full lg:w-[350px] flex flex-col gap-6">
                <div class="bg-white rounded-[10px] border border-gray-200 shadow-sm p-5">
                    <h2 class="text-[14px] font-bold text-gray-800 mb-4 border-b border-gray-100 pb-2">Fitur Pendukung</h2>
                    <ul class="space-y-4">
                        <li class="flex gap-3 items-start">
                            <div class="mt-1 text-gray-400"><svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path d="M10 2a6 6 0 00-6 6v3.586l-1.707 1.707A1 1 0 003 14h14a1 1 0 00.707-1.707L16 11.586V8a6 6 0 00-6-6zM10 18a3 3 0 01-3-3h6a3 3 0 01-3 3z"/></svg></div>
                            <div>
                                <div class="mb-1">
                                    <h4 class="text-[12px] font-bold text-gray-800 inline-block">Notifikasi</h4>
                                </div>
                                <p class="text-[11px] text-gray-500 leading-relaxed">Berisi riwayat pemberitahuan sistem seperti persetujuan dokumen, jadwal sidang baru, atau pesan penting dari Koordinator.</p>
                            </div>
                        </li>
                        <li class="flex gap-3 items-start">
                            <div class="mt-1 text-gray-400"><svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/></svg></div>
                            <div>
                                <div class="mb-1">
                                    <h4 class="text-[12px] font-bold text-gray-800 inline-block">Profil Mahasiswa</h4>
                                </div>
                                <p class="text-[11px] text-gray-500 leading-relaxed">Menu untuk mengelola data pribadi, foto profil, dan yang paling krusial: <strong>Mengunggah/Membuat Tanda Tangan Digital</strong> yang diperlukan dalam dokumen Berita Acara.</p>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- TAB: Peraturan & Pinalti KP -->
        <div x-show="tab === 'peraturan'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" class="space-y-6" style="display:none;">
            
            <div class="bg-red-50 border border-red-200 rounded-[10px] p-6 shadow-sm relative overflow-hidden">
                <div class="absolute right-0 top-0 text-red-100 transform translate-x-4 -translate-y-4">
                    <svg class="w-32 h-32" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/></svg>
                </div>
                <div class="relative z-10">
                    <h2 class="text-xl font-black text-red-700 mb-2 flex items-center gap-2">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                        Sanksi Pinalti & Peraturan Tegas
                    </h2>
                    <p class="text-[13px] text-red-900/80 max-w-4xl leading-relaxed mb-6">
                        Mahasiswa diwajibkan mematuhi tenggat waktu dan ketentuan administratif yang telah ditetapkan Program Studi. Pelanggaran terhadap aturan di bawah ini dapat berakibat fatal pada nilai akhir Anda.
                    </p>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Penalty 1 -->
                        <div class="bg-white border-l-4 border-red-600 rounded shadow-sm p-4">
                            <h3 class="text-[14px] font-bold text-gray-900 mb-1">Keterlambatan Penyerahan Revisi</h3>
                            <p class="text-[12px] text-gray-600 leading-relaxed mb-2">Mahasiswa diberikan batas waktu khusus (maksimal 5 hari setelah pelaksanaan sidang) untuk menyelesaikan revisi paska-sidang dan mendapatkan persetujuan (ACC) dari Dosen Penguji.</p>
                            <div class="bg-red-100 text-red-800 text-[11px] font-bold px-3 py-2 rounded">
                                Sanksi: Penurunan 1 tingkat Grade Penilaian (Contoh: nilai akhir yang seharusnya mendapat Grade A akan diturunkan menjadi Grade B).
                            </div>
                        </div>

                        <!-- Penalty 2 -->
                        <div class="bg-white border-l-4 border-orange-500 rounded shadow-sm p-4">
                            <h3 class="text-[14px] font-bold text-gray-900 mb-1">Kekurangan Kuota Log Bimbingan</h3>
                            <p class="text-[12px] text-gray-600 leading-relaxed mb-2">Tidak mencapai batas minimal log bimbingan (umumnya 12 kali) dengan Dosen Pembimbing hingga batas akhir pendaftaran sidang.</p>
                            <div class="bg-orange-100 text-orange-800 text-[11px] font-bold px-3 py-2 rounded">
                                Sanksi: Penolakan akses pendaftaran sidang dan penundaan penyelesaian KP.
                            </div>
                        </div>

                        <!-- Penalty 3 -->
                        <div class="bg-white border-l-4 border-red-500 rounded shadow-sm p-4">
                            <h3 class="text-[14px] font-bold text-gray-900 mb-1">Ketidakhadiran Sidang</h3>
                            <p class="text-[12px] text-gray-600 leading-relaxed mb-2">Mahasiswa tidak hadir pada jadwal sidang yang telah diploting tanpa konfirmasi atau alasan darurat (sakit dengan surat dokter, musibah, dll).</p>
                            <div class="bg-red-100 text-red-800 text-[11px] font-bold px-3 py-2 rounded">
                                Sanksi: Gugur sidang (Nilai Otomatis 0.0) dan berpotensi mendapat status kelulusan "Lanjut".
                            </div>
                        </div>

                        <!-- Penalty 4 -->
                        <div class="bg-white border-l-4 border-yellow-500 rounded shadow-sm p-4">
                            <h3 class="text-[14px] font-bold text-gray-900 mb-1">Plagiarisme & Pemalsuan Data</h3>
                            <p class="text-[12px] text-gray-600 leading-relaxed mb-2">Terbukti melakukan plagiasi dalam Laporan KP atau memalsukan dokumen, stempel instansi, tanda tangan pembimbing/supervisor, maupun log bimbingan.</p>
                            <div class="bg-yellow-100 text-yellow-800 text-[11px] font-bold px-3 py-2 rounded">
                                Sanksi: Pembatalan seluruh kegiatan KP, mendapatkan Grade terendah, serta sanksi akademik dari Fakultas.
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white border border-gray-200 rounded-[10px] p-6 shadow-sm">
                <h3 class="font-bold text-[15px] text-gray-800 mb-4 border-b border-gray-100 pb-3">Syarat Kelulusan Sidang & Status "Lanjut"</h3>
                <ul class="list-disc pl-5 text-[13px] text-gray-600 space-y-2 leading-relaxed">
                    <li>Nilai total akumulasi (Rata-rata Penguji dan Pembimbing) harus berada di atas batas minimal kelulusan (Umumnya Grade C).</li>
                    <li>Dalam sistem Kerja Praktek ini, <strong>tidak ada status kelulusan "Tidak Lulus"</strong>. Status yang digunakan apabila mahasiswa gagal mencapai standar minimum (mendapat Grade D atau E) adalah status <strong>"Lanjut"</strong>.</li>
                    <li>Bila mendapat status "Lanjut", mahasiswa diwajibkan untuk memproses ulang atau mengambil kembali SKS Kerja Praktek di kemudian hari.</li>
                </ul>
            </div>

        </div>

        <!-- TAB: Bantuan & FAQ -->
        <div x-show="tab === 'faq'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" class="flex flex-col items-center justify-center text-center py-12" style="display:none;">
            <div class="w-20 h-20 bg-blue-50 text-blue-600 rounded-full flex items-center justify-center mb-6 shadow-sm">
                <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
            </div>
            <h2 class="text-xl font-black text-gray-800 mb-3">Pusat Bantuan Kerja Praktek</h2>
            <p class="text-[13px] text-gray-500 max-w-xl mx-auto leading-relaxed mb-8">
                Jika Anda menemukan masalah sistem, ketidaksesuaian data jadwal, atau memiliki pertanyaan mendesak terkait administrasi KP, hubungi Koordinator KP melalui kanal resmi berikut.
            </p>
            <div class="flex gap-4">
                <a href="mailto:koordinator.kp@univ.ac.id" class="px-8 py-3 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-[5px] transition-colors shadow-md flex items-center gap-2 text-[13px]">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                    Email Koordinator KP
                </a>
            </div>
        </div>

    </div>
</x-dashboard-layout>
