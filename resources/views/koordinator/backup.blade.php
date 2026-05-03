<x-dashboard-layout userName="{{ auth()->user()->name }}" roleName="KOORDINATOR" hidePeriodSelector="true">
    <x-slot name="sidebar">
        @include('koordinator.components.sidebar', ['active' => 'backup'])
    </x-slot>

    <x-slot name="header">
        {{ __('Backup Database') }}
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            <!-- Header & Action -->
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div class="flex-1">
                    <div class="bg-blue-50 border-2 border-blue-200 rounded-xl p-4 flex items-start gap-4">
                        <div class="bg-blue-500 p-2 rounded-lg text-white">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-blue-800 font-medium leading-relaxed">
                                Lakukan Backup Database untuk menjaga integritas data dan mengurangi resiko kehilangan data secara berkala.
                            </p>
                        </div>
                    </div>
                </div>
                <form action="{{ route('koordinator.backup.store') }}" method="POST" class="shrink-0">
                    @csrf
                    <button type="submit" class="w-full md:w-auto bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-6 rounded-xl flex items-center justify-center gap-2 transition-all shadow-lg hover:shadow-blue-200">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Buat Backup Baru
                    </button>
                </form>
            </div>

            <!-- Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Last Backup -->
                <div class="bg-green-50 border-2 border-green-100 rounded-2xl p-6 flex flex-col justify-center items-center text-center">
                    <h3 class="text-green-800 font-bold text-lg mb-2">Backup Terakhir</h3>
                    <p class="text-green-600 font-semibold text-xl">{{ $lastBackup }}</p>
                </div>

                <!-- Storage Capacity -->
                <div class="bg-red-50 border-2 border-red-100 rounded-2xl p-6 relative overflow-hidden">
                    <div class="relative z-10">
                        <h3 class="text-red-800 font-bold text-lg mb-1">Kapasitas Tersedia</h3>
                        <p class="text-red-600 font-medium mb-4">Kapasitas Penyimpanan mencapai : {{ $capacityInfo['used'] }}/{{ $capacityInfo['total'] }}</p>
                        
                        <!-- Progress Bar -->
                        <div class="w-full bg-red-200 rounded-full h-2.5">
                            <div class="bg-red-600 h-2.5 rounded-full" style="width: {{ $capacityInfo['percent'] }}%"></div>
                        </div>
                    </div>
                    <!-- Decorative Icon -->
                    <div class="absolute -right-4 -bottom-4 text-red-100 opacity-50 transform rotate-12">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-24 w-24" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4" />
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Table -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="p-6 border-b border-gray-100 flex items-center justify-between bg-gray-50/50">
                    <h3 class="font-bold text-gray-800 text-lg">Riwayat Backup Database</h3>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left">
                        <thead class="bg-gray-100/80 text-gray-700 uppercase font-bold border-b border-gray-200">
                            <tr>
                                <th class="px-6 py-4 border-r border-gray-200">Tanggal</th>
                                <th class="px-6 py-4 border-r border-gray-200">Nama Backup</th>
                                <th class="px-6 py-4 border-r border-gray-200">Ukuran</th>
                                <th class="px-6 py-4 border-r border-gray-200 text-center">Status</th>
                                <th class="px-6 py-4 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @forelse($backups as $backup)
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-6 py-4 border-r border-gray-100 font-medium text-gray-600">
                                        {{ $backup['date'] }}
                                    </td>
                                    <td class="px-6 py-4 border-r border-gray-100 text-gray-700">
                                        {{ $backup['name'] }}
                                    </td>
                                    <td class="px-6 py-4 border-r border-gray-100 text-gray-600">
                                        {{ $backup['size'] }}
                                    </td>
                                    <td class="px-6 py-4 border-r border-gray-100 text-center">
                                        <span class="px-3 py-1 bg-green-100 text-green-700 rounded-full text-xs font-bold uppercase tracking-wider">
                                            {{ $backup['status'] }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex items-center justify-center gap-3">
                                            <!-- Download -->
                                            <a href="{{ route('koordinator.backup.download', $backup['name']) }}" 
                                               class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center gap-2 font-bold transition-all shadow-sm">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a2 2 0 002 2h12a2 2 0 002-2v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                                </svg>
                                                Download
                                            </a>
                                            
                                            <!-- Delete -->
                                            <form action="{{ route('koordinator.backup.destroy', $backup['name']) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus backup ini?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg flex items-center gap-2 font-bold transition-all shadow-sm">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                    </svg>
                                                    Hapus
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-12 text-center text-gray-400">
                                        <div class="flex flex-col items-center gap-2">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 opacity-20" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4" />
                                            </svg>
                                            <p>Belum ada riwayat backup database.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</x-dashboard-layout>
