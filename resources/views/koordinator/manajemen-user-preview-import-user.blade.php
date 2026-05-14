<x-dashboard-layout header="Preview Data Import" userName="{{ auth()->user()->name ?? 'Koordinator' }}" roleName="KOORDINATOR KP">
    <x-slot:sidebar>
        @include('koordinator.components.sidebar', ['active' => 'manajemen-akses'])
    </x-slot>

    <style>
        [x-cloak] { display: none !important; }
        .table-input {
            width: 100%; padding: 0.5rem 0.75rem;
            background: transparent; border: none; outline: none;
            font-size: 13px; color: #1f2937;
            transition: background 0.15s;
        }
        .table-input:focus {
            background: #eff6ff;
            box-shadow: inset 0 0 0 2px #3b82f6;
            border-radius: 4px;
        }
        .dup-input:focus { box-shadow: inset 0 0 0 2px #ef4444; background: #fef2f2; border-radius: 4px; }
        .role-select { appearance: none; cursor: pointer; }
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(12px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        .fade-in { animation: fadeInUp 0.35s ease both; }
        .fade-in-2 { animation: fadeInUp 0.35s ease 0.1s both; }
        .fade-in-3 { animation: fadeInUp 0.35s ease 0.2s both; }
        tr.removing { animation: fadeOut 0.25s forwards; }
        @keyframes fadeOut { to { opacity: 0; transform: scaleY(0); height: 0; } }
    </style>

    <div class="w-full max-w-6xl mx-auto px-4 pb-16 font-inter">

        {{-- ── HEADER CARD ─────────────────────────────────────────────── --}}
        <div class="fade-in mt-6 rounded-xl bg-gradient-to-r from-[#1e3a5f] to-[#2563eb] px-6 py-5 shadow-lg flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
            <div>
                <h1 class="text-white font-bold text-[18px] tracking-tight">Preview Data Import</h1>
                <p class="text-blue-200 text-[13px] mt-0.5 max-w-xl leading-relaxed">
                    Periksa dan edit data pengguna dari file Excel sebelum disimpan ke database.
                    Kolom yang aktif bisa langsung diedit di dalam tabel.
                </p>
            </div>
            <div class="flex flex-wrap gap-2 shrink-0">
                {{-- Valid badge --}}
                <div class="flex items-center gap-2 bg-white/15 backdrop-blur border border-white/20 rounded-lg px-3 py-2">
                    <div class="w-2.5 h-2.5 rounded-full bg-emerald-400 shadow-sm shadow-emerald-400"></div>
                    <span class="text-white font-bold text-[13px]">{{ count($validRows) }} Valid</span>
                </div>
                {{-- Duplicate badge --}}
                @if(session('duplicateRows') && count(session('duplicateRows')) > 0)
                <div class="flex items-center gap-2 bg-red-500/20 backdrop-blur border border-red-400/40 rounded-lg px-3 py-2">
                    <div class="w-2.5 h-2.5 rounded-full bg-red-400 shadow-sm shadow-red-400 animate-pulse"></div>
                    <span class="text-red-200 font-bold text-[13px]">{{ count(session('duplicateRows')) }} Duplikat</span>
                </div>
                @else
                <div class="flex items-center gap-2 bg-white/10 border border-white/20 rounded-lg px-3 py-2">
                    <svg class="w-3.5 h-3.5 text-emerald-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                    </svg>
                    <span class="text-emerald-200 font-bold text-[13px]">Tidak ada duplikat</span>
                </div>
                @endif
            </div>
        </div>

        <form action="{{ route('koordinator.user.import.confirm') }}" method="POST" id="confirmForm">
            @csrf

            {{-- ── TABEL VALID ──────────────────────────────────────────── --}}
            <div class="fade-in-2 rounded-xl border border-gray-200 shadow-sm overflow-hidden mb-6">
                {{-- Table Header --}}
                <div class="flex items-center justify-between px-5 py-3.5 bg-white border-b border-gray-200">
                    <div class="flex items-center gap-2">
                        <div class="w-3 h-3 rounded-full bg-emerald-500"></div>
                        <h3 class="text-gray-800 font-bold text-[14px]">Data Valid</h3>
                    </div>
                    <span class="bg-emerald-50 text-emerald-700 border border-emerald-200 text-[12px] font-bold px-2.5 py-1 rounded-full">
                        {{ count($validRows) }} baris siap impor
                    </span>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-50 border-b border-gray-200 text-[12px] uppercase tracking-wide text-gray-500 font-semibold">
                                <th class="w-10 py-3 px-3 text-center border-r border-gray-200">#</th>
                                <th class="py-3 px-4 border-r border-gray-200">Nama Lengkap</th>
                                <th class="py-3 px-4 border-r border-gray-200 w-36">ID (NIM / NIDN)</th>
                                <th class="py-3 px-4 border-r border-gray-200">Email</th>
                                <th class="py-3 px-4 border-r border-gray-200 w-36">Role</th>
                                <th class="py-3 px-4 w-20 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 bg-white">
                            @forelse($validRows as $index => $row)
                            <tr class="hover:bg-blue-50/40 transition-colors duration-100 group">
                                <td class="py-2.5 px-3 text-center text-[13px] text-gray-400 font-medium border-r border-gray-100 bg-gray-50 w-10">
                                    {{ $index + 1 }}
                                </td>
                                <td class="border-r border-gray-100 py-1 px-1">
                                    <input type="text" name="users[{{ $index }}][nama]" value="{{ $row['nama'] }}"
                                           required class="table-input" placeholder="Nama lengkap…">
                                </td>
                                <td class="border-r border-gray-100 py-1 px-1">
                                    <input type="text" name="users[{{ $index }}][id]" value="{{ $row['id'] }}"
                                           required class="table-input text-center font-mono text-[12px]" placeholder="NIM/NIDN…">
                                </td>
                                <td class="border-r border-gray-100 py-1 px-1">
                                    <input type="email" name="users[{{ $index }}][email]" value="{{ $row['email'] }}"
                                           required class="table-input" placeholder="email@contoh.com…">
                                </td>
                                <td class="border-r border-gray-100 py-1 px-2">
                                    <div class="relative">
                                        <select name="users[{{ $index }}][role]" required
                                                class="table-input role-select pr-6 text-[13px]">
                                            <option value="koordinator_kp" {{ str_contains(strtolower($row['role']), 'koordinator') ? 'selected' : '' }}>Koordinator KP</option>
                                            <option value="dosen"          {{ strtolower($row['role']) === 'dosen' ? 'selected' : '' }}>Dosen</option>
                                            <option value="mahasiswa"      {{ strtolower($row['role']) === 'mahasiswa' ? 'selected' : '' }}>Mahasiswa</option>
                                        </select>
                                        <svg class="pointer-events-none absolute right-1 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                        </svg>
                                    </div>
                                </td>
                                <td class="py-2 px-3 text-center">
                                    <button type="button"
                                            onclick="removeRow(this)"
                                            class="inline-flex items-center justify-center w-7 h-7 rounded-md text-gray-400 hover:text-red-600 hover:bg-red-50 transition-colors"
                                            title="Hapus baris">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="py-16 text-center">
                                    <div class="flex flex-col items-center gap-2 text-gray-400">
                                        <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                        </svg>
                                        <p class="text-[14px] font-medium">Tidak ada data valid dari file.</p>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- ── TABEL DUPLIKAT ───────────────────────────────────────── --}}
            @if(session('duplicateRows') && count(session('duplicateRows')) > 0)
            <div class="fade-in-3 rounded-xl border border-red-200 shadow-sm overflow-hidden mb-6">
                {{-- Table Header --}}
                <div class="flex items-start gap-3 px-5 py-4 bg-red-50 border-b border-red-200">
                    <div class="mt-0.5 shrink-0">
                        <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <div class="flex items-center justify-between flex-wrap gap-2">
                            <h3 class="text-red-700 font-bold text-[14px]">Data Duplikat — Perlu Perbaikan</h3>
                            <span class="bg-red-100 text-red-700 border border-red-200 text-[12px] font-bold px-2.5 py-1 rounded-full">
                                {{ count(session('duplicateRows')) }} baris bermasalah
                            </span>
                        </div>
                        <p class="text-red-600 text-[12.5px] mt-1 leading-relaxed">
                            Data di bawah memiliki <strong>ID (NIM/NIDN)</strong> atau <strong>Email</strong> yang sudah terdaftar.
                            Nilai yang konflik ditandai <span class="font-bold text-red-600 underline decoration-dashed">merah bergaris bawah</span>.
                            Perbaiki langsung di tabel atau hapus baris jika tidak ingin diimpor.
                        </p>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-red-50/60 border-b border-red-200 text-[12px] uppercase tracking-wide text-red-500 font-semibold">
                                <th class="w-10 py-3 px-3 text-center border-r border-red-100">#</th>
                                <th class="py-3 px-4 border-r border-red-100">Nama Lengkap</th>
                                <th class="py-3 px-4 border-r border-red-100 w-36">ID (NIM / NIDN)</th>
                                <th class="py-3 px-4 border-r border-red-100">Email</th>
                                <th class="py-3 px-4 border-r border-red-100 w-36">Role</th>
                                <th class="py-3 px-4 w-20 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-red-100 bg-white">
                            @foreach(session('duplicateRows') as $dupIndex => $dup)
                            @php $i = count($validRows) + $dupIndex; @endphp
                            <tr class="hover:bg-red-50/50 transition-colors duration-100">
                                <td class="py-2.5 px-3 text-center text-[13px] text-red-400 font-medium border-r border-red-100 bg-red-50/30 w-10">
                                    {{ $dupIndex + 1 }}
                                </td>
                                <td class="border-r border-red-100 py-1 px-1">
                                    <input type="text" name="users[{{ $i }}][nama]" value="{{ $dup['nama'] }}"
                                           required class="table-input dup-input">
                                </td>
                                <td class="border-r border-red-100 py-1 px-1">
                                    <input type="text" name="users[{{ $i }}][id]" value="{{ $dup['id'] }}"
                                           required class="table-input dup-input text-center font-mono text-[12px]
                                           {{ $dup['is_duplicate_id'] ? 'text-red-600 font-bold underline decoration-dashed underline-offset-4' : '' }}">
                                </td>
                                <td class="border-r border-red-100 py-1 px-1">
                                    <input type="email" name="users[{{ $i }}][email]" value="{{ $dup['email'] }}"
                                           required class="table-input dup-input
                                           {{ $dup['is_duplicate_email'] ? 'text-red-600 font-bold underline decoration-dashed underline-offset-4' : '' }}">
                                </td>
                                <td class="border-r border-red-100 py-1 px-2">
                                    <div class="relative">
                                        <select name="users[{{ $i }}][role]" required
                                                class="table-input dup-input role-select pr-6 text-[13px]">
                                            <option value="koordinator_kp" {{ str_contains(strtolower($dup['role']), 'koordinator') ? 'selected' : '' }}>Koordinator KP</option>
                                            <option value="dosen"          {{ strtolower($dup['role']) === 'dosen' ? 'selected' : '' }}>Dosen</option>
                                            <option value="mahasiswa"      {{ strtolower($dup['role']) === 'mahasiswa' ? 'selected' : '' }}>Mahasiswa</option>
                                        </select>
                                        <svg class="pointer-events-none absolute right-1 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                        </svg>
                                    </div>
                                </td>
                                <td class="py-2 px-3 text-center">
                                    <button type="button"
                                            onclick="removeRow(this)"
                                            class="inline-flex items-center justify-center w-7 h-7 rounded-md text-red-300 hover:text-red-600 hover:bg-red-50 transition-colors"
                                            title="Hapus baris">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @endif

            {{-- ── ACTION BAR ───────────────────────────────────────────── --}}
            <div class="fade-in-3 sticky bottom-4 z-10">
                <div class="flex items-center justify-between gap-4 bg-white border border-gray-200 rounded-xl shadow-lg px-5 py-4">
                    <div class="flex items-center gap-2 text-gray-500 text-[13px]">
                        <svg class="w-4 h-4 text-blue-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span>Klik sel untuk mengedit data secara langsung sebelum menyimpan.</span>
                    </div>
                    <div class="flex items-center gap-3 shrink-0">
                        <a href="{{ route('koordinator.manajemen-akses') }}"
                           class="h-9 px-5 rounded-lg border border-gray-300 text-gray-700 text-[13px] font-semibold hover:bg-gray-50 transition-colors flex items-center gap-1.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                            Batal
                        </a>
                        @if(count($validRows) > 0 || (session('duplicateRows') && count(session('duplicateRows')) > 0))
                        <button type="submit"
                                class="h-9 px-5 rounded-lg bg-[#1e3a5f] hover:bg-[#162d4a] text-white text-[13px] font-bold flex items-center gap-1.5 shadow-sm transition-colors cursor-pointer">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                            </svg>
                            Simpan ke Database
                        </button>
                        @endif
                    </div>
                </div>
            </div>

        </form>
    </div>

    <script>
        function removeRow(btn) {
            const row = btn.closest('tr');
            row.style.transition = 'all 0.2s ease';
            row.style.opacity = '0';
            row.style.transform = 'translateX(8px)';
            setTimeout(() => row.remove(), 200);
        }
    </script>

</x-dashboard-layout>
