<x-dashboard-layout header="Pemulihan Data" userName="{{ auth()->user()->name }}" roleName="KOORDINATOR KP">
    <x-slot:sidebar>
        @include('koordinator.components.sidebar', ['active' => 'sistem'])
    </x-slot>

    <div class="mt-8 px-2 w-full max-w-[1240px] mx-auto pb-12">
        <div class="bg-[#E6F0FA] border border-[#D0E3F5] rounded-[10px] p-4 lg:p-5 mb-8 flex items-start gap-4 shadow-sm">
            <div class="w-6 h-6 rounded-full bg-[#4285F4] text-white flex items-center justify-center font-bold flex-shrink-0 mt-0.5">i</div>
            <p class="text-[14px] text-[#1A1A1A] font-medium leading-relaxed m-0 mt-0.5">
                Fitur ini digunakan untuk <strong>memulihkan data (Data Recovery)</strong>. Jika ada pendaftaran KP atau Sidang yang hilang dari sistem karena akun mahasiswanya pernah dihapus, datanya akan terdaftar sebagai "Yatim Piatu" (Orphaned). Anda bisa menyambungkan kembali data tersebut ke akun mahasiswa yang valid di sini.
            </p>
        </div>

        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4 shadow-sm" role="alert">
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
        @endif

        @if(session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4 shadow-sm" role="alert">
                <span class="block sm:inline">{{ session('error') }}</span>
            </div>
        @endif

        <div class="bg-white rounded-[15px] border border-gray-200 shadow-sm overflow-hidden p-6 mb-8">
            <h3 class="text-[18px] font-bold text-black uppercase tracking-tight mb-6">Data Pendaftaran KP Yatim Piatu (Orphaned)</h3>
            
            <div class="overflow-x-auto">
                <table class="w-full border-collapse text-[13px]">
                    <thead>
                        <tr class="bg-[#EBEBEB] text-black">
                            <th class="py-3 px-4 font-bold text-center w-[60px] border-b border-r border-gray-300">ID</th>
                            <th class="py-3 px-4 font-bold text-left border-b border-r border-gray-300">Judul KP</th>
                            <th class="py-3 px-4 font-bold text-left border-b border-r border-gray-300">ID User Lama (Deleted)</th>
                            <th class="py-3 px-4 font-bold text-center border-b border-gray-300 w-[300px]">Aksi Pemulihan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($orphanedKps as $kp)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="py-3 px-4 text-center font-bold text-gray-500 border-r border-gray-200">{{ $kp->id }}</td>
                                <td class="py-3 px-4 text-left font-medium border-r border-gray-200">
                                    <div class="font-bold text-[13px]">{{ $kp->judul_kp }}</div>
                                    <div class="text-[11px] text-gray-500">{{ $kp->instansi_nama }}</div>
                                    @php
                                        $sidang = $orphanedSidangs->where('pendaftaran_kp_id', $kp->id)->first();
                                    @endphp
                                    @if($sidang)
                                        <div class="mt-1 text-[11px] font-bold text-green-600 uppercase">Ada Data Sidang (Nilai: {{ (float)$sidang->nilai_akhir }})</div>
                                    @else
                                        <div class="mt-1 text-[11px] font-medium text-red-500 italic">Tidak ada data sidang</div>
                                    @endif
                                </td>
                                <td class="py-3 px-4 text-left font-medium border-r border-gray-200">
                                    <span class="px-2 py-1 bg-red-100 text-red-600 rounded text-[11px] font-mono">{{ $kp->mahasiswa_id }}</span>
                                </td>
                                <td class="py-3 px-4 text-center">
                                    <form action="{{ route('koordinator.pemulihan-data.recover-kp') }}" method="POST" class="flex gap-2 items-center" onsubmit="return confirm('Yakin ingin menyambungkan data ini?')">
                                        @csrf
                                        <input type="hidden" name="kp_id" value="{{ $kp->id }}">
                                        <select name="new_user_id" class="border border-gray-300 rounded-[5px] text-[12px] p-1.5 flex-1 focus:ring-blue-500" required>
                                            <option value="">-- Pilih Mahasiswa Tujuan --</option>
                                            @foreach($activeMahasiswas as $mhs)
                                                <option value="{{ $mhs->id }}">{{ $mhs->nim }} - {{ $mhs->name }}</option>
                                            @endforeach
                                        </select>
                                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1.5 rounded-[5px] font-bold text-[11px] uppercase tracking-wider">Pulihkan</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="py-12 text-center text-gray-400 italic font-medium bg-gray-50">
                                    Tidak ada data KP yang yatim piatu. Database dalam keadaan sehat.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-dashboard-layout>
