<x-dashboard-layout header="Manajemen Penyimpanan & Arsip" userName="{{ auth()->user()->name }}" roleName="KOORDINATOR KP" :hidePeriodSelector="true">
    <x-slot:sidebar>
        @include('koordinator.components.sidebar', ['active' => 'backup'])
    </x-slot>
    <div class="sm:flex sm:items-center sm:justify-between mb-8">
        <!-- Title has been handled by layout header -->
    </div>

    @if(session('success'))
        <div class="mb-6 p-4 bg-green-50 border-l-4 border-green-500 rounded-md">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-[12px] font-medium text-green-800">{{ session('success') }}</p>
                </div>
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 rounded-md">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-[12px] font-medium text-red-800">{{ session('error') }}</p>
                </div>
            </div>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Panel Kiri: Kapasitas Penyimpanan -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h2 class="text-lg font-bold text-gray-900 mb-4 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4"></path></svg>
                    Kapasitas Database
                </h2>
                
                <div class="mb-4">
                    <div class="flex justify-between text-[12px] mb-1">
                        <span class="text-gray-500 font-medium">PostgreSQL (Neon)</span>
                        <span class="text-gray-900 font-bold">{{ $dbSize }} / {{ $neonMax }}</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-blue-600 h-2 rounded-full" style="width: 2%"></div>
                    </div>
                </div>

                <div class="mb-6">
                    <div class="flex justify-between text-[12px] mb-1">
                        <span class="text-gray-500 font-medium">{{ $cloudStorageName }}</span>
                        <span class="text-gray-900 font-bold">~{{ $cloudFilesCount }} Berkas Terunggah</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-emerald-500 h-2 rounded-full" style="width: {{ min(100, max(1, ($cloudFilesCount / 1000) * 100)) }}%"></div>
                    </div>
                    <p class="text-[12px] text-gray-400 mt-1">Batas Kuota Gratis: {{ $cloudMax }}</p>
                </div>

                <div class="bg-blue-50 rounded-lg p-4 text-[12px] text-blue-800">
                    <p class="font-bold mb-1">Pencadangan Otomatis</p>
                    <p>Infrastruktur Cloud Neon mencadangkan database Anda secara otomatis melalui sistem <em>Point-in-Time Recovery</em> (PITR). Anda tidak perlu mengunduh file .sql secara manual lagi.</p>
                </div>
            </div>
        </div>

        <!-- Panel Kanan: Sistem Arsip & Pembersihan -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6" x-data="{ 
                isDownloading: false, 
                showPurgeModal: false, 
                selectedPeriode: '', 
                konfirmasiText: '',
                submitDownload() {
                    if(!this.selectedPeriode) return alert('Silakan pilih periode terlebih dahulu!');
                    this.isDownloading = true;
                    // Auto reset loading after 15 seconds as a fallback
                    setTimeout(() => { this.isDownloading = false; }, 15000);
                    return true;
                }
            }">
                <h2 class="text-lg font-bold text-gray-900 mb-2 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"></path></svg>
                    Pengarsipan & Pembersihan Data
                </h2>
                <p class="text-[12px] text-gray-600 mb-6">Pilih periode akademik untuk mengunduh seluruh data (Teks & Lampiran PDF) menjadi satu file ZIP, lalu bersihkan datanya untuk melegakan kapasitas penyimpanan.</p>

                <div class="bg-gray-50 border border-gray-200 rounded-lg p-5">
                    <label class="block text-[12px] font-medium text-gray-700 mb-2">Pilih Tahun Ajaran (Periode)</label>
                    <select x-model="selectedPeriode" class="w-full border-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-md shadow-sm mb-6">
                        <option value="">-- Pilih Periode Akademik --</option>
                        @foreach($periodes as $p)
                            <option value="{{ $p->id }}">{{ $p->label_tahun_ajaran }}</option>
                        @endforeach
                    </select>

                    <div class="flex flex-col sm:flex-row gap-4">
                        <!-- Form Download ZIP -->
                        <form action="{{ route('koordinator.backup.download') }}" method="POST" @submit="submitDownload" data-turbo="false" class="flex-1">
                            @csrf
                            <input type="hidden" name="periode_id" x-bind:value="selectedPeriode">
                            <button type="submit" x-bind:disabled="!selectedPeriode || isDownloading" class="w-full flex justify-center items-center px-4 py-2 bg-emerald-600 border border-transparent rounded-md font-semibold text-white hover:bg-emerald-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors">
                                <span x-show="!isDownloading">
                                    <svg class="w-4 h-4 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                                    Unduh Arsip Lengkap (.ZIP)
                                </span>
                                <span x-show="isDownloading" class="flex items-center">
                                    <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    Sedang Memproses File...
                                </span>
                            </button>
                        </form>

                        <!-- Tombol Buka Modal Purge -->
                        <button type="button" @click="showPurgeModal = true" x-bind:disabled="!selectedPeriode" class="flex-1 flex justify-center items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-white hover:bg-red-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors">
                            <svg class="w-4 h-4 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                            Hapus Permanen Periode
                        </button>
                    </div>
                </div>

                <!-- Modal Konfirmasi Hapus -->
                <div x-show="showPurgeModal" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
                    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                        <div class="fixed inset-0 transition-opacity" aria-hidden="true">
                            <div class="absolute inset-0 bg-gray-500 opacity-75" @click="showPurgeModal = false"></div>
                        </div>

                        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                            <form action="{{ route('koordinator.backup.purge') }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <input type="hidden" name="periode_id" x-bind:value="selectedPeriode">
                                
                                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                                    <div class="sm:flex sm:items-start">
                                        <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                                            <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                                        </div>
                                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                                            <h3 class="text-lg leading-6 font-bold text-gray-900">Hapus Permanen Data & File Cloud</h3>
                                            <div class="mt-2 text-[12px] text-gray-500">
                                                <p class="mb-2">Tindakan ini akan memusnahkan <strong>SELURUH</strong> data mahasiswa, pendaftaran KP, nilai, dan juga menghapus semua file PDF lampirannya dari Cloud Storage ({{ $cloudStorageName }}) untuk periode ini.</p>
                                                <p class="font-bold text-red-600 mb-4">Pastikan Anda telah sukses mengunduh Backup (.ZIP) terlebih dahulu!</p>
                                                
                                                <label class="block text-gray-700 font-medium mb-1">Ketik "HAPUS" untuk konfirmasi:</label>
                                                <input type="text" name="konfirmasi" x-model="konfirmasiText" class="w-full border-gray-300 focus:border-red-500 focus:ring-red-500 rounded-md" placeholder="HAPUS" required>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                                    <button type="submit" x-bind:disabled="konfirmasiText !== 'HAPUS'" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-[12px] disabled:opacity-50 disabled:cursor-not-allowed">
                                        Musnahkan Data
                                    </button>
                                    <button type="button" @click="showPurgeModal = false; konfirmasiText = ''" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-[12px]">
                                        Batal
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabel Riwayat Pengunduhan Arsip -->
    <div class="mt-8 bg-white shadow-sm sm:rounded-lg overflow-hidden border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50 flex justify-between items-center">
            <h2 class="text-lg font-semibold text-gray-900 flex items-center gap-2">
                <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                Riwayat Pengunduhan Arsip
            </h2>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-[12px] font-medium text-gray-500 uppercase tracking-wider">Tanggal & Waktu</th>
                        <th scope="col" class="px-6 py-3 text-left text-[12px] font-medium text-gray-500 uppercase tracking-wider">Periode Akademik</th>
                        <th scope="col" class="px-6 py-3 text-left text-[12px] font-medium text-gray-500 uppercase tracking-wider">Nama File Output</th>
                        <th scope="col" class="px-6 py-3 text-left text-[12px] font-medium text-gray-500 uppercase tracking-wider">Diunduh Oleh</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($histories as $history)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap text-[12px] text-gray-500">
                                {{ $history->created_at->translatedFormat('d F Y, H:i') }} WIB
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-[12px] text-gray-900 font-medium">
                                {{ $history->periode_name ?? ($history->tahunAjaran->label_tahun_ajaran ?? 'Tidak diketahui') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-[12px] font-mono text-gray-600">
                                {{ $history->file_name }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-[12px] text-gray-500 flex items-center gap-2">
                                <div class="w-6 h-6 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center font-bold text-[12px]">
                                    {{ substr($history->koordinator->name ?? 'A', 0, 1) }}
                                </div>
                                {{ $history->koordinator->name ?? 'Sistem' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-12 text-center text-gray-500 text-[12px]">
                                <svg class="w-12 h-12 mx-auto text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 002-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                                </svg>
                                Belum ada riwayat pengunduhan arsip.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-dashboard-layout>
