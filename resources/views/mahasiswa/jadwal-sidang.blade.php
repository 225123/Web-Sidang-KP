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
                <p class="text-gray-600">Anda belum mendaftar/mengupload berkas sidang, atau status berkas Anda belum disahkan oleh Koordinator KP.</p>
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
                            
                            <div class="font-medium text-gray-700 mt-2">Daftar Kelompok</div>
                            <div class="mt-2">:</div>
                            <div class="mt-2 text-gray-700">
                                @if($sidang->pendaftaranKp->anggota_kelompok_ids && count($sidang->pendaftaranKp->anggota_kelompok_ids) > 0)
                                    <ul class="list-disc list-inside">
                                        @foreach($sidang->pendaftaranKp->anggota_kelompok_ids as $anggotaId)
                                            @php
                                                $anggotaObj = \App\Models\Mahasiswa::with('user')->find($anggotaId);
                                            @endphp
                                            <li>{{ $anggotaObj->user->name ?? 'Unknown' }} - {{ $anggotaObj->nim ?? '-' }}</li>
                                        @endforeach
                                    </ul>
                                @else
                                    <span class="text-gray-500 italic">Individu</span>
                                @endif
                            </div>

                        </div>

                        <!-- Tombol Kalender -->
                        <button class="absolute bottom-6 right-6 bg-[#d8d8d8] hover:bg-[#cccccc] text-gray-800 border-none rounded-md px-4 py-2.5 text-[13px] font-medium cursor-pointer flex items-center gap-2 transition-colors shadow-sm">
                            <i class="fas fa-calendar-alt text-gray-600"></i> Tambahkan ke Kalender
                        </button>

                    </div>
                @endif

            </div> <!-- End Container Utama -->
        @endif

    </div>
</x-dashboard-layout>
