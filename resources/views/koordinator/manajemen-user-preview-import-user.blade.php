<x-dashboard-layout header="Preview Data Import" userName="{{ auth()->user()->name ?? 'Koordinator' }}" roleName="KOORDINATOR KP">
    <x-slot:sidebar>
        @include('koordinator.components.sidebar', ['active' => 'manajemen-akses'])
    </x-slot>

    <x-slot:headerActions>
        <div x-data="{ open: false, selected: 'Genap 2025/2026' }" class="relative w-[212px]">
            <button @click="open = !open" @click.outside="open = false" type="button"
                class="w-full flex items-center justify-between border border-[#CAC0C0] bg-[#FBFBFB] rounded-[5px] shadow-sm text-[13px] font-medium py-1.5 px-3 focus:outline-none focus:border-[#4CC098] focus:ring-1 focus:ring-[#4CC098] cursor-pointer text-black h-[32px]">

                <span x-text="selected"></span>

                <svg :class="open ? 'rotate-0' : 'rotate-90'"
                    class="w-3.5 h-3.5 text-gray-500 transition-transform duration-200 flex-shrink-0" fill="none"
                    stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                </svg>
            </button>

            <div x-show="open" x-transition style="display: none;"
                class="absolute z-50 w-full mt-1 bg-[#FBFBFB] border border-[#CAC0C0] rounded-[5px] shadow-lg overflow-hidden">
                <ul class="py-1 text-[13px] font-medium text-black">
                    <li>
                        <button @click="selected = 'Genap 2025/2026'; open = false" type="button"
                            class="block w-full text-left px-3 py-2 hover:bg-[#E8E5E5] transition-colors cursor-pointer">
                            Genap 2025/2026
                        </button>
                    </li>
                    <li>
                        <button @click="selected = 'Ganjil 2025/2026'; open = false" type="button"
                            class="block w-full text-left px-3 py-2 hover:bg-[#E8E5E5] transition-colors cursor-pointer">
                            Ganjil 2025/2026
                        </button>
                    </li>
                </ul>
            </div>
            <input type="hidden" name="periode" :value="selected">
        </div>
    </x-slot:headerActions>

    <div class="mt-8 px-4 w-full max-w-6xl mx-auto pb-12 font-inter">

        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-6">
            <p class="text-[13px] text-gray-500 font-medium">Silakan periksa dan edit daftar user dari file Excel yang Anda unggah sebelum menyimpannya ke database.</p>
            
            <div class="flex items-center gap-2">
                <span class="inline-flex items-center px-3 py-1.5 rounded-full text-[13px] font-bold bg-[#E8E8E8] text-gray-700 border border-gray-300">
                    Total Valid: {{ count($validRows) }} User
                </span>
                @if(session('duplicateRows') && count(session('duplicateRows')) > 0)
                <span class="inline-flex items-center px-3 py-1.5 rounded-full text-[13px] font-bold bg-[#FDE8E8] text-[#E32727] border border-red-200">
                    Terdapat Data Duplikat: {{ count(session('duplicateRows')) }} User
                </span>
                @else
                <span class="inline-flex items-center px-3 py-1.5 rounded-full text-[13px] font-bold bg-[#DEF7EC] text-[#008000] border border-green-200">
                    Tidak ada data duplikat
                </span>
                @endif
            </div>
        </div>

        <form action="{{ route('koordinator.user.import.confirm') }}" method="POST" id="confirmForm">
            @csrf
            
            <!-- Tabel Data Valid -->
            <div class="bg-white border border-black/50 rounded-[5px] overflow-hidden shadow-sm mb-8">
                <div class="bg-gray-50 px-4 py-3 border-b border-black/40">
                    <h3 class="text-gray-800 font-bold text-[14px]">Tabel Preview Data Valid</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left font-medium text-black border-collapse">
                        <thead class="bg-[#B0AFB5]">
                            <tr class="h-[40px] text-center text-[13px]">
                                <th class="border-r border-black/40 font-medium w-[5%]">No</th>
                                <th class="border-r border-black/40 font-medium w-[25%] px-2">Nama</th>
                                <th class="border-r border-black/40 font-medium w-[15%] px-2">ID (NIM/NIDN)</th>
                                <th class="border-r border-black/40 font-medium w-[25%] px-2">Email</th>
                                <th class="border-r border-black/40 font-medium w-[20%] px-2">Role</th>
                                <th class="font-medium w-[10%] px-2">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-black/40">
                            @forelse($validRows as $index => $row)
                            <tr class="h-[45px] hover:bg-yellow-50 transition-colors group">
                                <td class="border-r border-black/40 text-[13px] text-center text-gray-600 bg-gray-50">{{ $index + 1 }}</td>
                                
                                <td class="border-r border-black/40">
                                    <input type="text" name="users[{{ $index }}][nama]" value="{{ $row['nama'] }}" required class="w-full h-full px-3 py-2.5 bg-transparent outline-none focus:bg-white focus:ring-1 focus:ring-blue-500 font-normal text-[13px] text-gray-900 placeholder-gray-400">
                                </td>
                                
                                <td class="border-r border-black/40">
                                    <input type="text" name="users[{{ $index }}][id]" value="{{ $row['id'] }}" required class="w-full h-full px-3 py-2.5 bg-transparent border-none outline-none focus:bg-white focus:ring-1 focus:ring-blue-500 text-gray-700 font-normal text-[13px] placeholder-gray-400 text-center">
                                </td>
                                
                                <td class="border-r border-black/40">
                                    <input type="email" name="users[{{ $index }}][email]" value="{{ $row['email'] }}" required class="w-full h-full px-3 py-2.5 bg-transparent border-none outline-none focus:bg-white focus:ring-1 focus:ring-blue-500 text-gray-700 font-normal text-[13px] placeholder-gray-400">
                                </td>
                                
                                <td class="border-r border-black/40">
                                    <select name="users[{{ $index }}][role]" required class="w-full h-full px-3 py-2.5 bg-transparent border-none outline-none focus:bg-white focus:ring-1 focus:ring-blue-500 text-gray-900 font-normal text-[13px] cursor-pointer appearance-none">
                                        <option value="koordinator_kp" {{ str_contains(strtolower($row['role']), 'koordinator') ? 'selected' : '' }}>Koordinator KP</option>
                                        <option value="dosen" {{ strtolower($row['role']) === 'dosen' ? 'selected' : '' }}>Dosen</option>
                                        <option value="mahasiswa" {{ strtolower($row['role']) === 'mahasiswa' ? 'selected' : '' }}>Mahasiswa</option>
                                    </select>
                                </td>

                                <td class="text-center">
                                    <button type="button" onclick="this.closest('tr').remove()" class="text-red-500 hover:text-red-700 font-bold text-[13px] hover:underline" title="Hapus Baris">Hapus</button>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="py-12 bg-gray-50 text-center text-gray-500 font-medium">
                                    <span class="block mb-2 text-3xl">⚠️</span>
                                    Tidak ada data valid.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Tabel Data Duplikat -->
            @if(session('duplicateRows') && count(session('duplicateRows')) > 0)
            <div class="mb-8 bg-[#FBFBFB] border border-[#E32727]/80 rounded-[5px] overflow-hidden shadow-sm relative">
                <div class="bg-[#FDE8E8] px-4 py-4 border-b border-[#E32727]/30 flex flex-col gap-1">
                    <h3 class="text-[#E32727] font-bold text-[14px] flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                        Tabel Data Duplikat (Menunggu Perbaikan)
                    </h3>
                    <p class="text-red-700 text-[13px] font-medium leading-relaxed">Data di bawah ini memiliki <strong class="font-bold">ID (NIM/NIDN)</strong> atau <strong class="font-bold">Email</strong> yang bentrok dengan sistem. Input yang bermasalah ditandai dengan teks berwarna merah. Harap perbaiki isi inputnya secara langsung atau tekan tombol <strong class="font-bold border px-1 bg-white ml-0.5 rounded text-xs">Hapus</strong> jika batal diimpor.</p>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left font-medium text-black border-collapse">
                        <thead class="bg-red-100">
                            <tr class="h-[40px] text-center text-red-800 text-[13px]">
                                <th class="border-r border-red-200 font-medium w-[5%]">No</th>
                                <th class="border-r border-red-200 font-medium w-[25%] px-2">Nama</th>
                                <th class="border-r border-red-200 font-medium w-[15%] px-2">ID (NIM/NIDN)</th>
                                <th class="border-r border-red-200 font-medium w-[25%] px-2">Email</th>
                                <th class="border-r border-red-200 font-medium w-[20%] px-2">Role</th>
                                <th class="font-medium w-[10%] px-2">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-red-200">
                            @foreach(session('duplicateRows') as $dupIndex => $dup)
                            @php
                                $totalRows = count($validRows);
                                $i = $totalRows + $dupIndex;
                            @endphp
                            <tr class="h-[45px] hover:bg-red-50 transition-colors group">
                                <td class="border-r border-red-200 text-[13px] text-center text-red-900 bg-white">{{ $dupIndex + 1 }}</td>
                                
                                <td class="border-r border-red-200 bg-white">
                                    <input type="text" name="users[{{ $i }}][nama]" value="{{ $dup['nama'] }}" required class="w-full h-full px-3 py-2.5 bg-transparent outline-none focus:bg-white focus:ring-1 focus:ring-red-500 font-normal text-[13px] text-gray-900 placeholder-gray-400">
                                </td>
                                
                                <td class="border-r border-red-200 bg-white relative">
                                    <input type="text" name="users[{{ $i }}][id]" value="{{ $dup['id'] }}" required class="w-full h-full px-3 py-2.5 bg-transparent border-none outline-none focus:bg-white focus:ring-1 focus:ring-red-500 text-center font-normal text-[13px] {{ $dup['is_duplicate_id'] ? 'text-[#E32727] font-bold decoration-[#E32727] underline decoration-dashed underline-offset-4' : 'text-gray-700' }}" placeholder-gray-400">
                                </td>
                                
                                <td class="border-r border-red-200 bg-white relative">
                                    <input type="email" name="users[{{ $i }}][email]" value="{{ $dup['email'] }}" required class="w-full h-full px-3 py-2.5 bg-transparent border-none outline-none focus:bg-white focus:ring-1 focus:ring-red-500 font-normal text-[13px] {{ $dup['is_duplicate_email'] ? 'text-[#E32727] font-bold decoration-[#E32727] underline decoration-dashed underline-offset-4' : 'text-gray-700' }}" placeholder-gray-400">
                                </td>
                                
                                <td class="border-r border-red-200 bg-white">
                                    <select name="users[{{ $i }}][role]" required class="w-full h-full px-3 py-2.5 bg-transparent border-none outline-none focus:bg-white focus:ring-1 focus:ring-red-500 text-gray-900 font-normal text-[13px] cursor-pointer appearance-none">
                                        <option value="koordinator_kp" {{ str_contains(strtolower($dup['role']), 'koordinator') ? 'selected' : '' }}>Koordinator KP</option>
                                        <option value="dosen" {{ strtolower($dup['role']) === 'dosen' ? 'selected' : '' }}>Dosen</option>
                                        <option value="mahasiswa" {{ strtolower($dup['role']) === 'mahasiswa' ? 'selected' : '' }}>Mahasiswa</option>
                                    </select>
                                </td>

                                <td class="text-center bg-white">
                                    <button type="button" onclick="this.closest('tr').remove()" class="text-red-500 hover:text-red-700 font-bold text-[13px] hover:underline hover:bg-red-50 px-2 py-1 rounded" title="Hapus Baris">Hapus</button>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @endif

            <!-- Form Actions -->
            <div class="flex items-center justify-between p-6 bg-gray-50 border border-black/50 rounded-[5px] mt-2 mb-8">
                <a href="{{ route('koordinator.manajemen-akses') }}" class="w-[120px] h-[36px] bg-[#E32727] hover:bg-red-700 text-white rounded-[5px] text-[14px] font-bold flex items-center justify-center transition-colors shadow-sm">
                    Batal
                </a>
                
                @if(count($validRows) > 0 || (session('duplicateRows') && count(session('duplicateRows')) > 0))
                <button type="submit" class="w-[180px] h-[36px] bg-[#008000] hover:bg-green-700 text-white rounded-[5px] text-[14px] font-bold flex items-center justify-center gap-2 shadow-sm transition-colors cursor-pointer ring-1 ring-green-900/50">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                    Simpan Perubahan
                </button>
                @endif
            </div>

        </form>

    </div>
</x-dashboard-layout>
