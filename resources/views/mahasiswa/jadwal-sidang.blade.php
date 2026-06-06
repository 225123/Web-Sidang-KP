<x-dashboard-layout header="Jadwal Sidang" userName="{{ auth()->user()->name }}" roleName="MAHASISWA">
    <x-slot:sidebar>
        @include('mahasiswa.components.sidebar', ['active' => 'jadwal-sidang'])
    </x-slot>

        

    <div class="bg-gray-50 flex items-center justify-center p-6 w-full">

        @if(!$sidang)
            <!-- Jika sama sekali belum mengupload berkas, tampilkan UI dummy kosong -->
            <div class="bg-white rounded-[10px] shadow-sm p-8 text-center max-w-2xl w-full border border-gray-200">
                <i class="fas fa-calendar-times text-4xl text-gray-400 mb-4"></i>
                <h2 class="text-xl font-bold text-gray-800 mb-2">Jadwal Belum Tersedia</h2>
                @if(isset($isPastPeriod) && $isPastPeriod)
                    <p class="text-gray-600">Anda tidak terdaftar untuk mengikuti Sidang KP pada periode tersebut, sehingga jadwal sidang tidak tersedia.</p>
                @else
                    <p class="text-gray-600">Anda belum mendaftar/mengupload berkas sidang, atau status berkas Anda belum disahkan oleh Koordinator KP.</p>
                @endif
            </div>
        @else
            <!-- Container Utama -->
            <div class="w-full max-w-3xl">

                @if($sidang->status_jadwal !== 'submitted' || !$sidang->tanggal_sidang)
                    <div class="bg-white rounded-[10px] shadow-sm p-8 text-center border border-gray-200">
                        <i class="fas fa-clock text-4xl text-yellow-500 mb-4"></i>
                        <h2 class="text-xl font-bold text-gray-800 mb-2">Jadwal Sedang Disusun</h2>
                        <p class="text-gray-600">Koordinator sedang melakukan plotting jadwal sidang Anda. Silakan cek kembali halaman ini secara berkala nanti.</p>
                    </div>
                @else
                    <!-- Jika sudah di plotting & disubmit -->
                    <!-- --- Bagian Header --- -->
                    <div class="bg-[#eeeeee] rounded-[10px] flex items-center p-6 mb-4 shadow-sm">
                        <!-- Date Circle -->
                        <div class="bg-[#eef200] w-[80px] h-[80px] rounded-full flex flex-col justify-center items-center mr-6 shadow-sm shrink-0">
                            <span class="text-[13px] text-gray-800 mb-0.5 font-medium">{{ \Carbon\Carbon::parse($sidang->tanggal_sidang)->locale('id')->isoFormat('dddd') }}</span>
                            <span class="text-[26px] font-bold text-black leading-none">{{ \Carbon\Carbon::parse($sidang->tanggal_sidang)->format('d') }}</span>
                        </div>
                        
                        <!-- Title Section -->
                        <div>
                            <h2 class="m-0 mb-2 text-[22px] font-bold text-[#111]">{{ $sidang->pendaftaranKp->judul_kp ?? 'Sidang Kerja Praktik' }}</h2>
                            <div class="text-[#777] text-[15px] flex items-center gap-3">
                                <i class="fas fa-user text-[14px]"></i> 
                                <span>{{ $user->name }} - {{ $user->mahasiswa->nim }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- --- Bagian Body / Detail --- -->
                    <div class="bg-[#eeeeee] rounded-[10px] px-8 pt-8 pb-[80px] relative shadow-sm">
                        
                        <div class="grid grid-cols-[180px_15px_1fr] gap-y-5 text-[14px] text-[#111] items-center">
                            
                            <div class="font-medium text-gray-700">Tanggal Sidang</div>
                            <div>:</div>
                            <div class="font-bold">{{ \Carbon\Carbon::parse($sidang->tanggal_sidang)->locale('id')->isoFormat('dddd, D MMMM Y') }}</div>

                            <div class="font-medium text-gray-700">Ruangan</div>
                            <div>:</div>
                            <div class="font-bold">{{ $sidang->ruang_sidang ?? '-' }}</div>

                            <div class="font-medium text-gray-700">Waktu Sidang</div>
                            <div>:</div>
                            <div class="font-bold">
                                {{ \Carbon\Carbon::parse($sidang->waktu_mulai_sidang)->format('H:i') }} - 
                                {{ \Carbon\Carbon::parse($sidang->waktu_selesai_sidang)->format('H:i') }} WIB
                            </div>

                            <div class="font-medium text-gray-700">Dosen Penguji 1</div>
                            <div>:</div>
                            <div class="font-bold">{{ $sidang->penguji1->name ?? '-' }}</div>

                            <div class="font-medium text-gray-700">Dosen Penguji 2</div>
                            <div>:</div>
                            <div class="font-bold">{{ $sidang->penguji2->name ?? '-' }}</div>

                            <div class="font-medium text-gray-700">Status</div>
                            <div>:</div>
                            <div>
                                <span class="bg-[#eaf06e] text-black px-4 py-1.5 rounded-full text-[13px] font-bold inline-block border border-[#d6de47]">Menunggu Terlaksana</span>
                            </div>
                            


                        </div>

                        <!-- Tombol Kalender -->
                        @php
                            $startDateTime = \Carbon\Carbon::parse($sidang->tanggal_sidang . ' ' . $sidang->waktu_mulai_sidang, 'Asia/Jakarta')->setTimezone('UTC')->format('Ymd\THis\Z');
                            $endDateTime = \Carbon\Carbon::parse($sidang->tanggal_sidang . ' ' . $sidang->waktu_selesai_sidang, 'Asia/Jakarta')->setTimezone('UTC')->format('Ymd\THis\Z');
                        @endphp

                        <div x-data="{ 
                                clicked: localStorage.getItem('cal_email_sent_{{ $sidang->id }}') === 'true',
                                loading: false,
                                async sendEmail() {
                                    if (this.clicked || this.loading) return;
                                    
                                    this.loading = true;
                                    try {
                                        let response = await fetch('{{ route('mahasiswa.jadwal-sidang.kirim-kalender') }}', {
                                            method: 'POST',
                                            headers: {
                                                'Content-Type': 'application/json',
                                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                            }
                                        });
                                        let result = await response.json();
                                        if (result.success) {
                                            this.clicked = true;
                                            localStorage.setItem('cal_email_sent_{{ $sidang->id }}', 'true');
                                        } else {
                                            alert('Gagal mengirim email: ' + result.message);
                                        }
                                    } catch (e) {
                                        alert('Terjadi kesalahan pada server saat mengirim email.');
                                    } finally {
                                        this.loading = false;
                                    }
                                }
                             }" class="absolute bottom-6 right-6">
                            <button type="button" @click="sendEmail()" 
                               :class="clicked ? 'bg-[#34A853] text-white hover:bg-green-700' : 'bg-[#d8d8d8] text-gray-800 hover:bg-[#cccccc]'"
                               class="border-none rounded-md px-4 py-2.5 text-[13px] font-medium cursor-pointer flex items-center gap-2 transition-colors shadow-sm disabled:opacity-50"
                               :disabled="loading">
                                <i x-show="loading" class="fas fa-spinner fa-spin text-gray-600"></i>
                                <i x-show="!loading" :class="clicked ? 'fas fa-check text-white' : 'fas fa-envelope text-gray-600'"></i> 
                                <span x-text="loading ? 'Mengirim...' : (clicked ? 'Email Terkirim' : 'Kirim Kalender via Email')"></span>
                            </button>
                        </div>

                    </div>
                @endif

            </div> <!-- End Container Utama -->
        @endif

    </div>
</x-dashboard-layout>
