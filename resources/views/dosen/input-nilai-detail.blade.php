@php
    $roleTitle = match($role) {
        'pembimbing' => 'Pembimbing',
        'supervisior' => 'Supervisior',
        'penguji1' => 'Penguji 1',
        'penguji2' => 'Penguji 2',
        default => 'Sidang'
    };
@endphp
<x-dashboard-layout header="Input Nilai {{ $roleTitle }}" :backUrl="route('dosen.input-nilai.index')" hidePeriodSelector="true" userName="{{ auth()->user()->name }}" roleName="DOSEN">
    <x-slot:sidebar>
        @include('dosen.components.sidebar', ['active' => 'input-nilai'])
    </x-slot>

    <div x-data="inputDetail()" class="p-6 max-w-5xl mx-auto">



        <!-- Section 1: Informasi Mahasiswa -->
        <div class="bg-[#D9D9D9] p-8 rounded-[15px] mb-6 shadow-sm border border-gray-200">
            <h2 class="text-[18px] font-bold text-black mb-6">Informasi Mahasiswa</h2>
            <div class="grid grid-cols-1 md:grid-cols-1 gap-y-4">
                <div class="flex items-start">
                    <span class="w-[180px] text-[14px] text-gray-700 font-bold">Nama</span>
                    <span class="w-[20px] text-[14px] text-gray-700">:</span>
                    <span class="text-[14px] font-bold text-black uppercase">{{ $sidang->mahasiswa->user->name }}</span>
                </div>
                <div class="flex items-start">
                    <span class="w-[180px] text-[14px] text-gray-700 font-bold">NIM</span>
                    <span class="w-[20px] text-[14px] text-gray-700">:</span>
                    <span class="text-[14px] font-medium text-black">{{ $sidang->mahasiswa->nim }}</span>
                </div>
                <div class="flex items-start">
                    <span class="w-[180px] text-[14px] text-gray-700 font-bold">Judul KP</span>
                    <span class="w-[20px] text-[14px] text-gray-700">:</span>
                    <span class="text-[14px] font-medium text-black italic">{{ $sidang->pendaftaranKp->judul_kp }}</span>
                </div>
                @if($role === 'penguji1' || $role === 'penguji2')
                    <div class="flex items-start">
                        <span class="w-[180px] text-[14px] text-gray-700 font-bold">Tanggal Sidang</span>
                        <span class="w-[20px] text-[14px] text-gray-700">:</span>
                        <span class="text-[14px] font-medium text-black">{{ \Carbon\Carbon::parse($sidang->tanggal_sidang)->translatedFormat('l, d F Y') }}</span>
                    </div>
                    <div class="flex items-start">
                        <span class="w-[180px] text-[14px] text-gray-700 font-bold">Tempat</span>
                        <span class="w-[20px] text-[14px] text-gray-700">:</span>
                        <span class="text-[14px] font-medium text-black line-clamp-1 italic italic">{{ $sidang->ruang_sidang ?? '-' }}</span>
                    </div>
                    <div class="flex items-start">
                        <span class="w-[180px] text-[14px] text-gray-700 font-bold">Waktu</span>
                        <span class="w-[20px] text-[14px] text-gray-700">:</span>
                        <span class="text-[14px] font-medium text-black">{{ substr($sidang->waktu_mulai_sidang, 0, 5) }} - {{ substr($sidang->waktu_selesai_sidang, 0, 5) }}</span>
                    </div>
                @endif
            </div>
        </div>

        <!-- Section 2: Penilaian Form -->
        <form action="{{ route('dosen.input-nilai.store', ['id' => $sidang->id, 'role' => $role]) }}" method="POST" @submit.prevent="submitForm($event)">
            @csrf
            <div class="bg-[#D9D9D9] p-8 rounded-[15px] shadow-sm border border-gray-200">
                <h2 class="text-[18px] font-bold text-black mb-6">Penilaian <span x-text="roleDisplay"></span></h2>

                <div class="space-y-6 max-w-2xl">
                    <!-- Dynamic Component Rendering -->
                    <template x-if="role === 'pembimbing'">
                        <div class="space-y-4">
                            <div class="flex items-center gap-4">
                                <label class="w-[240px] text-[14px] text-gray-700 font-bold">Kualitas Laporan</label>
                                <span class="text-gray-700">:</span>
                                <input type="text" name="nb_laporan" x-model="v1" x-on:input="v1 = sanitize($event.target.value)" x-on:blur="v1 = validateRange(v1)" x-on:keydown.enter.prevent="focusNext($event)" :disabled="isLocked" inputmode="decimal" class="w-[90px] h-[35px] border border-gray-300 rounded-[5px] px-2 text-center text-[14px] focus:outline-none focus:border-[#4CC098] disabled:bg-gray-100 disabled:text-gray-500">
                                <span class="text-[14px] text-gray-500 font-medium">40%</span>
                            </div>
                            <div class="flex items-center gap-4">
                                <label class="w-[240px] text-[14px] text-gray-700 font-bold">Kualitas Produk</label>
                                <span class="text-gray-700">:</span>
                                <input type="text" name="nb_produk" x-model="v2" x-on:input="v2 = sanitize($event.target.value)" x-on:blur="v2 = validateRange(v2)" x-on:keydown.enter.prevent="focusNext($event)" :disabled="isLocked" inputmode="decimal" class="w-[90px] h-[35px] border border-gray-300 rounded-[5px] px-2 text-center text-[14px] focus:outline-none focus:border-[#4CC098] disabled:bg-gray-100 disabled:text-gray-500">
                                <span class="text-[14px] text-gray-500 font-medium">40%</span>
                            </div>
                            <div class="flex items-center gap-4">
                                <label class="w-[240px] text-[14px] text-gray-700 font-bold">Sikap Dan Kedisiplinan</label>
                                <span class="text-gray-700">:</span>
                                <input type="text" name="nb_sikap" x-model="v3" x-on:input="v3 = sanitize($event.target.value)" x-on:blur="v3 = validateRange(v3)" x-on:keydown.enter.prevent="focusNext($event)" :disabled="isLocked" inputmode="decimal" class="w-[90px] h-[35px] border border-gray-300 rounded-[5px] px-2 text-center text-[14px] focus:outline-none focus:border-[#4CC098] disabled:bg-gray-100 disabled:text-gray-500">
                                <span class="text-[14px] text-gray-500 font-medium">20%</span>
                            </div>
                        </div>
                    </template>

                    <template x-if="role === 'penguji1' || role === 'penguji2'">
                        <div class="space-y-4">
                            <div class="flex items-center gap-4">
                                <label class="w-[240px] text-[14px] text-gray-700 font-bold">Kualitas Laporan</label>
                                <span class="text-gray-700">:</span>
                                <input type="text" name="n_laporan" x-model="v1" x-on:input="v1 = sanitize($event.target.value)" x-on:blur="v1 = validateRange(v1)" x-on:keydown.enter.prevent="focusNext($event)" :disabled="isLocked" inputmode="decimal" class="w-[90px] h-[35px] border border-gray-300 rounded-[5px] px-2 text-center text-[14px] focus:outline-none focus:border-[#4CC098] disabled:bg-gray-100 disabled:text-gray-500">
                                <span class="text-[14px] text-gray-500 font-medium">40%</span>
                            </div>
                            <div class="flex items-center gap-4">
                                <label class="w-[240px] text-[14px] text-gray-700 font-bold">Kualitas Produk</label>
                                <span class="text-gray-700">:</span>
                                <input type="text" name="n_produk" x-model="v2" x-on:input="v2 = sanitize($event.target.value)" x-on:blur="v2 = validateRange(v2)" x-on:keydown.enter.prevent="focusNext($event)" :disabled="isLocked" inputmode="decimal" class="w-[90px] h-[35px] border border-gray-300 rounded-[5px] px-2 text-center text-[14px] focus:outline-none focus:border-[#4CC098] disabled:bg-gray-100 disabled:text-gray-500">
                                <span class="text-[14px] text-gray-500 font-medium">40%</span>
                            </div>
                            <div class="flex items-center gap-4">
                                <label class="w-[240px] text-[14px] text-gray-700 font-bold">Kemampuan Presentasi</label>
                                <span class="text-gray-700">:</span>
                                <input type="text" name="n_presentasi" x-model="v3" x-on:input="v3 = sanitize($event.target.value)" x-on:blur="v3 = validateRange(v3)" x-on:keydown.enter.prevent="focusNext($event)" :disabled="isLocked" inputmode="decimal" class="w-[90px] h-[35px] border border-gray-300 rounded-[5px] px-2 text-center text-[14px] focus:outline-none focus:border-[#4CC098] disabled:bg-gray-100 disabled:text-gray-500">
                                <span class="text-[14px] text-gray-500 font-medium">20%</span>
                            </div>
                        </div>
                    </template>

                    <template x-if="role === 'supervisior'">
                        <div class="space-y-4">
                            <!-- Helper for external -->
                            @if($sidang->pendaftaranKp->jenis_instansi === 'Eksternal')
                                <div class="bg-blue-50 border-l-4 border-blue-400 p-4 mb-4 rounded shadow-sm">
                                    <div class="flex items-center">
                                        <svg class="h-5 w-5 text-blue-400 mr-2" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/></svg>
                                        <p class="text-[12px] text-blue-700 font-medium italic">
                                            KP Eksternal: Bacalah berkas pendaftaran mahasiswa untuk melihat nilai dari Supervisor Instansi.
                                        </p>
                                    </div>
                                </div>
                            @endif
                            <div class="flex items-center gap-4">
                                <label class="w-[240px] text-[14px] text-gray-700 font-bold">Kemampuan dan Motivasi Kerja</label>
                                <span class="text-gray-700">:</span>
                                <input type="text" name="ns_motivasi" x-model="v1" x-on:input="v1 = sanitize($event.target.value)" x-on:blur="v1 = validateRange(v1)" x-on:keydown.enter.prevent="focusNext($event)" :disabled="isLocked" inputmode="decimal" class="w-[90px] h-[35px] border border-gray-300 rounded-[5px] px-2 text-center text-[14px] focus:outline-none focus:border-[#4CC098] disabled:bg-gray-100 disabled:text-gray-500">
                                <span class="text-[14px] text-gray-500 font-medium">25%</span>
                            </div>
                            <div class="flex items-center gap-4">
                                <label class="w-[240px] text-[14px] text-gray-700 font-bold">Kualitas Kerja</label>
                                <span class="text-gray-700">:</span>
                                <input type="text" name="ns_kualitas" x-model="v2" x-on:input="v2 = sanitize($event.target.value)" x-on:blur="v2 = validateRange(v2)" x-on:keydown.enter.prevent="focusNext($event)" :disabled="isLocked" inputmode="decimal" class="w-[90px] h-[35px] border border-gray-300 rounded-[5px] px-2 text-center text-[14px] focus:outline-none focus:border-[#4CC098] disabled:bg-gray-100 disabled:text-gray-500">
                                <span class="text-[14px] text-gray-500 font-medium">25%</span>
                            </div>
                            <div class="flex items-center gap-4">
                                <label class="w-[240px] text-[14px] text-gray-700 font-bold">Inisiatif dan Kreatifitas</label>
                                <span class="text-gray-700">:</span>
                                <input type="text" name="ns_inisiatif" x-model="v3" x-on:input="v3 = sanitize($event.target.value)" x-on:blur="v3 = validateRange(v3)" x-on:keydown.enter.prevent="focusNext($event)" :disabled="isLocked" inputmode="decimal" class="w-[90px] h-[35px] border border-gray-300 rounded-[5px] px-2 text-center text-[14px] focus:outline-none focus:border-[#4CC098] disabled:bg-gray-100 disabled:text-gray-500">
                                <span class="text-[14px] text-gray-500 font-medium">25%</span>
                            </div>
                            <div class="flex items-center gap-4">
                                <label class="w-[240px] text-[14px] text-gray-700 font-bold">Sikap Dan Kedisiplinan</label>
                                <span class="text-gray-700">:</span>
                                <input type="text" name="ns_sikap" x-model="v4" x-on:input="v4 = sanitize($event.target.value)" x-on:blur="v4 = validateRange(v4)" x-on:keydown.enter.prevent="focusNext($event)" :disabled="isLocked" inputmode="decimal" class="w-[90px] h-[35px] border border-gray-300 rounded-[5px] px-2 text-center text-[14px] focus:outline-none focus:border-[#4CC098] disabled:bg-gray-100 disabled:text-gray-500">
                                <span class="text-[14px] text-gray-500 font-medium">25%</span>
                            </div>
                        </div>
                    </template>

                    @if($role === 'penguji1' || $role === 'penguji2')
                        <div class="pt-6 border-t border-dashed border-gray-400">
                            <label class="block text-[14px] font-bold text-black mb-2 uppercase">Catatan Sidang</label>
                            <textarea name="catatan" rows="3" :disabled="isLocked" class="w-full border border-gray-300 rounded-[5px] p-3 text-[13px] focus:outline-none focus:border-[#4CC098] disabled:bg-gray-100 disabled:text-gray-500" placeholder="Masukkan catatan atau masukan untuk mahasiswa...">{{ $sidang->catatan_sidang ?? '' }}</textarea>
                        </div>
                    @endif

                    <div class="pt-6 border-t border-dashed border-gray-400">
                        <div class="flex items-center gap-4">
                            <span class="w-[240px] text-[16px] font-bold text-black uppercase">Nilai Akhir</span>
                            <span class="text-black font-bold">:</span>
                            <span class="text-[18px] font-bold text-[#4285F4]" x-text="nilaiAkhir"></span>
                        </div>
                    </div>

                    @if($role === 'penguji1')
                    <div class="pt-6 border-t border-dashed border-gray-400">
                        <div class="flex items-center gap-4">
                            <label class="w-[240px] text-[16px] font-bold text-black uppercase">Status Kelulusan</label>
                            <span class="text-black font-bold">:</span>
                            <select name="status_kelulusan" x-model="statusKelulusan" :disabled="isLocked" class="w-[250px] py-1.5 border border-gray-300 rounded-[5px] px-2 text-[14px] focus:outline-none focus:border-[#4CC098] disabled:bg-gray-100 disabled:text-gray-500 font-medium">
                                <option value="" disabled selected>Pilih Status Kelulusan...</option>
                                <option value="Lulus">Lulus</option>
                                <option value="Lulus Dengan Revisi">Lulus Dengan Revisi</option>
                                <option value="Lanjut">Lanjut</option>
                            </select>
                        </div>
                    </div>
                    @endif
                </div>

                <div class="flex justify-end gap-3 mt-12">
                    @if(isset($isReadOnly) && $isReadOnly)
                        <div class="bg-gray-300 text-white px-10 py-2 rounded-[5px] text-[13px] font-bold cursor-not-allowed">
                            Read Only
                        </div>
                    @else
                    <template x-if="isLocked">
                        <button type="button" @click="isLocked = false" class="bg-[#4285F4] hover:bg-[#3367d6] text-white px-10 py-2 rounded-[5px] text-[13px] font-bold transition flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            Edit Penilaian
                        </button>
                    </template>
                    <template x-if="!isLocked">
                        <button type="submit" class="bg-[#34A853] hover:bg-[#2d9247] text-white px-10 py-2 rounded-[5px] text-[13px] font-bold transition flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M0 11l2-2 5 5L18 3l2 2L7 18z"/></svg>
                            Submit
                        </button>
                    </template>
                    @endif
                </div>
            </div>
        </form>
    </div>

    <script>
        function inputDetail() {
            return {
                role: '{{ $role }}',
                v1: '',
                v2: '',
                v3: '',
                v4: '',
                statusKelulusan: '',
                isLocked: false,

                init() {
                    let hasData = false;
                    // Populate with existing data if any
                    @if($role === 'pembimbing')
                        if ({{ $sidang->nb_laporan ?? -1 }} >= 0) {
                            this.v1 = '{{ number_format($sidang->nb_laporan ?? 0, 3, ".", "") }}';
                            this.v2 = '{{ number_format($sidang->nb_produk ?? 0, 3, ".", "") }}';
                            this.v3 = '{{ number_format($sidang->nb_sikap ?? 0, 3, ".", "") }}';
                            hasData = true;
                        }
                    @elseif($role === 'penguji1')
                        if ({{ $sidang->n1_laporan ?? -1 }} >= 0) {
                            this.v1 = '{{ number_format($sidang->n1_laporan ?? 0, 3, ".", "") }}';
                            this.v2 = '{{ number_format($sidang->n1_produk ?? 0, 3, ".", "") }}';
                            this.v3 = '{{ number_format($sidang->n1_presentasi ?? 0, 3, ".", "") }}';
                            this.statusKelulusan = '{!! $sidang->status_kelulusan ?? '' !!}';
                            hasData = true;
                        }
                    @elseif($role === 'penguji2')
                        if ({{ $sidang->n2_laporan ?? -1 }} >= 0) {
                            this.v1 = '{{ number_format($sidang->n2_laporan ?? 0, 3, ".", "") }}';
                            this.v2 = '{{ number_format($sidang->n2_produk ?? 0, 3, ".", "") }}';
                            this.v3 = '{{ number_format($sidang->n2_presentasi ?? 0, 3, ".", "") }}';
                            hasData = true;
                        }
                    @elseif($role === 'supervisior')
                        if ({{ $sidang->ns_motivasi ?? -1 }} >= 0) {
                            this.v1 = '{{ number_format($sidang->ns_motivasi ?? 0, 3, ".", "") }}';
                            this.v2 = '{{ number_format($sidang->ns_kualitas ?? 0, 3, ".", "") }}';
                            this.v3 = '{{ number_format($sidang->ns_inisiatif ?? 0, 3, ".", "") }}';
                            this.v4 = '{{ number_format($sidang->ns_sikap ?? 0, 3, ".", "") }}';
                            hasData = true;
                        }
                    @endif

                    if (hasData) {
                        this.isLocked = true;
                    }
                    @if(isset($isReadOnly) && $isReadOnly)
                        this.isLocked = true;
                    @endif
                },

                get roleDisplay() {
                    if (this.role === 'penguji1') return 'Penguji 1';
                    if (this.role === 'penguji2') return 'Penguji 2';
                    return this.role.charAt(0).toUpperCase() + this.role.slice(1);
                },

                get nilaiAkhir() {
                    let total = 0;
                    let n1 = parseFloat(this.v1) || 0;
                    let n2 = parseFloat(this.v2) || 0;
                    let n3 = parseFloat(this.v3) || 0;
                    let n4 = parseFloat(this.v4) || 0;

                    if (this.role === 'supervisior') {
                        total = (n1 * 0.25) + (n2 * 0.25) + (n3 * 0.25) + (n4 * 0.25);
                    } else {
                        total = (n1 * 0.4) + (n2 * 0.4) + (n3 * 0.2);
                    }
                    return Math.round(total * 1000) / 1000;
                },

                sanitize(val) {
                    // Convert comma to dot
                    let sanitized = val.replace(/,/g, '.');
                    // Allow only digits and one dot
                    sanitized = sanitized.replace(/[^0-9.]/g, '');
                    const parts = sanitized.split('.');
                    if (parts.length > 2) sanitized = parts[0] + '.' + parts.slice(1).join('');
                    // Limit to 3 decimals
                    if (parts.length > 1) sanitized = parts[0] + '.' + parts[1].substring(0, 3);

                    // Auto-desimal: sisipkan titik pada digit ke-3, kecuali '100'
                    const partsNow = sanitized.split('.');
                    if (partsNow.length === 1) {
                        if (sanitized.length === 3 && sanitized !== '100') {
                            sanitized = sanitized.substring(0, 2) + '.' + sanitized.substring(2);
                        } else if (sanitized.length > 3 && sanitized.substring(0, 3) !== '100') {
                            sanitized = sanitized.substring(0, 2) + '.' + sanitized.substring(2, 5);
                        }
                    }
                    
                    // Smart Capping while typing
                    let num = parseFloat(sanitized);
                    if (!isNaN(num) && num > 100) return '100';
                    
                    return sanitized;
                },

                validateRange(val) {
                    if (val === '') return '';
                    let num = parseFloat(val);
                    if (isNaN(num)) return '';
                    if (num < 1) return '1.000';
                    if (num > 100) return '100.000';
                    return num.toFixed(3);
                },

                submitForm(e) {
                    let requiredFields = [this.v1, this.v2, this.v3];
                    if (this.role === 'supervisior') requiredFields.push(this.v4);
                    
                    if (requiredFields.some(v => v === '' || parseFloat(v) === 0)) {
                        alert("Harap lengkapi semua field penilaian (tidak boleh kosong atau 0).");
                        return;
                    }

                    if (this.role === 'penguji1' && !this.statusKelulusan) {
                        alert("Harap pilih Status Kelulusan terlebih dahulu.");
                        return;
                    }
                    
                    const catatan = document.querySelector('textarea[name="catatan"]')?.value || '';
                    if (this.role === 'penguji1' && ['Lulus Dengan Revisi', 'Lanjut'].includes(this.statusKelulusan) && catatan.trim() === '') {
                        alert("Anda wajib meninggalkan Catatan Sidang jika status kelulusan adalah Lulus Dengan Revisi atau Lanjut.");
                        return;
                    }

                    e.target.submit();
                },

                focusNext(e) {
                    const inputs = Array.from(document.querySelectorAll('input:not([disabled]), select:not([disabled]), textarea:not([disabled])'));
                    const index = inputs.indexOf(e.target);
                    if (index > -1 && index < inputs.length - 1) {
                        inputs[index + 1].focus();
                    }
                }
            }
        }
    </script>
</x-dashboard-layout>
