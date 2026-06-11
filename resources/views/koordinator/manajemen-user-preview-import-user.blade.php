<x-dashboard-layout header="Preview Data Import" hidePeriodSelector="true" userName="{{ auth()->user()->name ?? 'Koordinator' }}" roleName="KOORDINATOR KP">
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
        .role-select { appearance: none; cursor: pointer; }
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(12px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        .fade-in { animation: fadeInUp 0.35s ease both; }
        .fade-in-2 { animation: fadeInUp 0.35s ease 0.1s both; }
        .fade-in-3 { animation: fadeInUp 0.35s ease 0.2s both; }
    </style>

    <div class="w-full max-w-6xl mx-auto px-4 pb-16 font-inter" x-data="importPreview()">

        {{-- ── HEADER CARD ─────────────────────────────────────────────── --}}
        <div class="fade-in mt-6 rounded-xl bg-gradient-to-r from-[#1e3a5f] to-[#2563eb] px-6 py-5 shadow-lg flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
            <div>
                <h1 class="text-white font-bold text-[18px] tracking-tight">Preview Data Import</h1>
                <p class="text-blue-200 text-[13px] mt-0.5 max-w-xl leading-relaxed">
                    Periksa dan edit data pengguna dari file Excel sebelum disimpan ke database.
                    Kolom yang aktif bisa langsung diedit. ID yang baru diinput akan divalidasi otomatis.
                </p>
            </div>
            <div class="flex flex-wrap gap-2 shrink-0">
                <div class="flex items-center gap-2 bg-white/15 backdrop-blur border border-white/20 rounded-lg px-3 py-2">
                    <div class="w-2.5 h-2.5 rounded-full bg-emerald-400 shadow-sm shadow-emerald-400"></div>
                    <span class="text-white font-bold text-[13px]" x-text="validRows.length + ' Valid'"></span>
                </div>
                
                <template x-if="duplicateRows.length > 0">
                    <div class="flex items-center gap-2 bg-red-500/20 backdrop-blur border border-red-400/40 rounded-lg px-3 py-2">
                        <div class="w-2.5 h-2.5 rounded-full bg-red-400 shadow-sm shadow-red-400 animate-pulse"></div>
                        <span class="text-red-200 font-bold text-[13px]" x-text="duplicateRows.length + ' Data Duplikat'"></span>
                    </div>
                </template>
                <template x-if="duplicateRows.length === 0">
                    <div class="flex items-center gap-2 bg-white/10 border border-white/20 rounded-lg px-3 py-2">
                        <svg class="w-3.5 h-3.5 text-emerald-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                        </svg>
                        <span class="text-emerald-200 font-bold text-[13px]">Tidak ada duplikat</span>
                    </div>
                </template>
            </div>
        </div>

        <form action="{{ route('koordinator.user.import.confirm') }}" method="POST" id="confirmImportForm">
            @csrf
            
            {{-- ── TABEL DATA VALID ─────────────────────────────────────────── --}}
            <div class="fade-in-2 rounded-xl border border-gray-200 shadow-sm overflow-hidden mb-6">
                <div class="flex items-center justify-between px-5 py-4 bg-gray-50 border-b border-gray-200">
                    <div>
                        <h3 class="text-gray-800 font-bold text-[14px]">Tabel Data Valid</h3>
                        <p class="text-gray-500 text-[12.5px] mt-0.5">Baris data di bawah ini siap untuk diimpor. Edit secara real-time.</p>
                    </div>
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
                            <template x-for="(row, index) in validRows" :key="row.internal_id">
                                <tr class="hover:bg-blue-50/40 transition-colors duration-100 group" :class="{'bg-red-50/20': row.is_invalid}">
                                    <td class="py-2.5 px-3 text-center text-[13px] text-gray-400 font-medium border-r border-gray-100 bg-gray-50 w-10">
                                        <span x-text="index + 1"></span>
                                        <div x-show="row.is_checking" class="mt-1 flex justify-center">
                                            <svg class="animate-spin h-3 w-3 text-blue-500" viewBox="0 0 24 24" fill="none">
                                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                            </svg>
                                        </div>
                                    </td>
                                    <td class="border-r border-gray-100 py-1 px-1">
                                        <input type="text" :name="`users[${index}][nama]`" x-model="row.nama"
                                            :readonly="row.is_update" @keydown.enter.prevent
                                            class="table-input" :class="{'bg-gray-200 text-gray-500 cursor-not-allowed select-none': row.is_update}" required placeholder="Nama lengkap…">
                                    </td>
                                    <td class="border-r border-gray-100 py-1 px-1 relative">
                                        <input type="text" :name="`users[${index}][id]`" x-model="row.id" 
                                            @input.debounce.600ms="checkId(index)"
                                            @keydown.enter.prevent="checkId(index)"
                                            oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                                            class="table-input text-center font-mono text-[12px]" required placeholder="NIM/NIDN…">
                                        
                                        <template x-if="row.is_update">
                                            <div>
                                                <input type="hidden" :name="`users[${index}][is_update]`" value="1">
                                                <input type="hidden" :name="`users[${index}][user_id]`" :value="row.user_id || ''">
                                                <div class="text-[10px] text-amber-600 font-bold text-center mt-1 leading-tight px-1 pb-1">User Lanjut. Akan diperbarui.</div>
                                            </div>
                                        </template>

                                        <template x-if="row.is_invalid">
                                            <div class="text-[10px] text-red-600 font-bold text-center mt-1 leading-tight px-1 pb-1">Duplikat. Periksa tabel di bawah.</div>
                                        </template>
                                    </td>
                                    <td class="border-r border-gray-100 py-1 px-1">
                                        <input type="email" :name="`users[${index}][email]`" x-model="row.email"
                                            :readonly="row.is_update" @keydown.enter.prevent
                                            class="table-input" :class="{'bg-gray-200 text-gray-500 cursor-not-allowed select-none': row.is_update}" required placeholder="email@contoh.com…">
                                    </td>
                                    <td class="border-r border-gray-100 py-1 px-2">
                                        <div class="relative">
                                            <select :name="`users[${index}][role]`" x-model="row.role" required
                                                    class="table-input role-select pr-6 text-[13px]" :class="{'bg-gray-200 text-gray-500 cursor-not-allowed pointer-events-none': row.is_update}">
                                                <option value="koordinator_kp">Koordinator KP</option>
                                                <option value="dosen">Dosen</option>
                                                <option value="mahasiswa">Mahasiswa</option>
                                            </select>
                                            <svg class="pointer-events-none absolute right-1 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                            </svg>
                                        </div>
                                    </td>
                                    <td class="py-2 px-3 text-center">
                                        <button type="button" @click="removeRow(index)"
                                                class="inline-flex items-center justify-center w-7 h-7 rounded-md text-gray-400 hover:text-red-600 hover:bg-red-50 transition-colors"
                                                title="Hapus baris">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                        </button>
                                    </td>
                                </tr>
                            </template>

                            <template x-if="validRows.length === 0">
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
                            </template>
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- ── TABEL CEK DUPLIKAT (COMPARISON) ───────────────────────────────────────── --}}
            <template x-if="duplicateRows.length > 0">
                <div class="fade-in-3 rounded-xl border border-red-200 shadow-sm overflow-hidden mb-6">
                    <div class="flex items-start gap-3 px-5 py-4 bg-red-50 border-b border-red-200">
                        <div class="mt-0.5 shrink-0">
                            <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                            </svg>
                        </div>
                        <div class="flex-1">
                            <div class="flex items-center justify-between flex-wrap gap-2">
                                <h3 class="text-red-700 font-bold text-[14px]">Tabel Cek Duplikat — Perbandingan Data</h3>
                                <span class="bg-red-100 text-red-700 border border-red-200 text-[12px] font-bold px-2.5 py-1 rounded-full" x-text="duplicateRows.length + ' baris ditemukan'">
                                </span>
                            </div>
                            <p class="text-red-600 text-[12.5px] mt-1 leading-relaxed">
                                Berikut adalah perbandingan data dari file Excel dengan data pengguna yang sudah terdaftar di database.
                                Status <strong>Baru/Lanjut</strong> ditentukan secara otomatis berdasarkan histori KP sebelumnya.
                            </p>
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-red-50/60 border-b border-red-200 text-[12px] uppercase tracking-wide text-red-600 font-bold">
                                    <th class="w-10 py-3 px-3 text-center border-r border-red-100">#</th>
                                    <th class="py-3 px-4 border-r border-red-100">ID (NIM/NIDN)</th>
                                    <th class="py-3 px-4 border-r border-red-100 bg-red-100/50 w-[30%]">Data Dari Input</th>
                                    <th class="py-3 px-4 border-r border-red-100 bg-blue-50/50 w-[30%]">Data Di Database</th>
                                    <th class="py-3 px-4">Status & Keterangan</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-red-100 bg-white">
                                <template x-for="(dup, dupIndex) in duplicateRows" :key="dupIndex">
                                    <tr class="hover:bg-red-50/50 transition-colors duration-100">
                                        <td class="py-3 px-3 text-center text-[13px] text-red-400 font-medium border-r border-red-100 bg-red-50/30">
                                            <span x-text="dupIndex + 1"></span>
                                        </td>
                                        <td class="py-3 px-4 border-r border-red-100 font-mono text-[13px] font-bold text-center text-gray-800" x-text="dup.id">
                                        </td>
                                        <td class="py-3 px-4 border-r border-red-100 bg-red-50/10">
                                            <div class="text-[13px] font-bold text-gray-800" x-text="dup.nama"></div>
                                            <div class="text-[12px] text-gray-500" x-text="dup.email"></div>
                                            <div class="text-[11px] font-medium mt-1 uppercase text-gray-400" x-text="dup.role"></div>
                                        </td>
                                        <td class="py-3 px-4 border-r border-red-100 bg-blue-50/10">
                                            <template x-if="dup.existing">
                                                <div>
                                                    <div class="text-[13px] font-bold text-gray-800" x-text="dup.existing.nama || '-'"></div>
                                                    <div class="text-[12px] text-gray-500" x-text="dup.existing.email || '-'"></div>
                                                    <div class="text-[11px] font-medium mt-1 uppercase text-gray-400">Histori: <span class="font-bold text-blue-600" x-text="dup.existing.status || '-'"></span></div>
                                                </div>
                                            </template>
                                            <template x-if="!dup.existing">
                                                <div class="text-[12px] italic text-gray-400">Tidak ditemukan</div>
                                            </template>
                                        </td>
                                        <td class="py-3 px-4">
                                            <template x-if="dup.keterangan.includes('Ditolak')">
                                                <div>
                                                    <span class="inline-flex bg-red-100 text-red-700 border border-red-200 text-[11px] font-bold px-2 py-1 rounded mb-1">
                                                        Ditolak
                                                    </span>
                                                    <div class="text-[12px] text-red-600 leading-tight" x-text="dup.keterangan.replace('Ditolak: ', '')"></div>
                                                </div>
                                            </template>
                                            <template x-if="!dup.keterangan.includes('Ditolak')">
                                                <div>
                                                    <span class="inline-flex bg-amber-100 text-amber-700 border border-amber-200 text-[11px] font-bold px-2 py-1 rounded mb-1">
                                                        Lanjut
                                                    </span>
                                                    <div class="text-[12px] text-amber-600 leading-tight" x-text="dup.keterangan.replace('Diterima: ', '')"></div>
                                                    <div class="text-[11px] text-gray-400 mt-1 italic">*Tercatat di tabel Data Valid</div>
                                                </div>
                                            </template>
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                </div>
            </template>

            {{-- ── ACTION BAR ───────────────────────────────────────────── --}}
            <div class="fade-in-3 sticky bottom-4 z-10">
                <div class="flex items-center justify-between gap-4 bg-white border border-gray-200 rounded-xl shadow-lg px-5 py-4">
                    <div class="flex items-center gap-2 text-gray-500 text-[13px]">
                        <template x-if="duplicateRows.length > 0">
                            <div class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-amber-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                </svg>
                                <span>Data yang ditolak (Invalid) harus dihapus atau diperbaiki sebelum disimpan.</span>
                            </div>
                        </template>
                        <template x-if="duplicateRows.length === 0">
                            <div class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-blue-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <span>Periksa kembali data Anda sebelum menyimpan.</span>
                            </div>
                        </template>
                    </div>
                    <div class="flex items-center gap-3 shrink-0">
                        <a href="{{ route('koordinator.manajemen-akses') }}"
                           class="h-9 px-5 rounded-lg border border-gray-300 text-gray-700 text-[13px] font-semibold hover:bg-gray-50 transition-colors flex items-center gap-1.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                            Batal
                        </a>
                        <template x-if="validRows.length > 0">
                            <button type="submit"
                                    :disabled="hasInvalidRows()"
                                    :class="hasInvalidRows() ? 'bg-gray-400 cursor-not-allowed opacity-50' : 'bg-[#1e3a5f] hover:bg-[#162d4a] cursor-pointer'"
                                    class="h-9 px-5 rounded-lg text-white text-[13px] font-bold flex items-center gap-1.5 shadow-sm transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                </svg>
                                Simpan ke Database
                            </button>
                        </template>
                    </div>
                </div>
            </div>

        </form>
    </div>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('importPreview', () => ({
                validRows: @json($validRows).map(row => ({
                    ...row, 
                    internal_id: Math.random().toString(36).substr(2, 9),
                    is_checking: false,
                    is_invalid: false
                })),
                duplicateRows: @json(session('duplicateRows', [])).map(row => ({
                    ...row,
                    sourceIndex: -1 // Original duplicates from server
                })),

                hasInvalidRows() {
                    return this.validRows.some(r => r.is_invalid);
                },

                removeRow(index) {
                    this.duplicateRows = this.duplicateRows.filter(d => d.sourceIndex !== index);
                    
                    this.duplicateRows.forEach(d => {
                        if (d.sourceIndex > index) d.sourceIndex--;
                    });
                    
                    this.validRows.splice(index, 1);
                },

                async checkId(index) {
                    const row = this.validRows[index];
                    if (!row.id || row.id.length < 3) return;

                    row.is_checking = true;
                    try {
                        const response = await fetch(`{{ route('koordinator.user.check-id') }}?id_user=${row.id}`);
                        const data = await response.json();
                        
                        this.duplicateRows = this.duplicateRows.filter(d => d.sourceIndex !== index);
                        
                        if (data.exists) {
                            if (data.role_type === 'dosen' || data.not_allowed) {
                                row.is_invalid = true;
                                row.is_update = false;
                                
                                this.duplicateRows.push({
                                    sourceIndex: index,
                                    nama: row.nama,
                                    id: row.id,
                                    email: row.email,
                                    role: row.role,
                                    keterangan: 'Ditolak: ' + (data.not_allowed ? 'Mahasiswa sudah Lulus' : 'Duplikat ID/Email'),
                                    existing: {
                                        nama: data.name,
                                        email: data.email,
                                        status: data.role
                                    }
                                });
                            } else {
                                row.is_invalid = false;
                                row.is_update = true;
                                row.user_id = data.user_id;
                                
                                this.duplicateRows.push({
                                    sourceIndex: index,
                                    nama: row.nama,
                                    id: row.id,
                                    email: row.email,
                                    role: row.role,
                                    keterangan: 'Diterima: Lanjut',
                                    existing: {
                                        nama: data.name,
                                        email: data.email,
                                        status: data.role
                                    }
                                });
                            }
                        } else {
                            row.is_invalid = false;
                            row.is_update = false;
                        }
                    } catch (e) {
                        console.error(e);
                    } finally {
                        row.is_checking = false;
                    }
                }
            }));
        });
    </script>
</x-dashboard-layout>
