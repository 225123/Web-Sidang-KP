<x-dashboard-layout header="Pengumuman" userName="{{ auth()->user()->name }}" roleName="KOORDINATOR KP">
    <x-slot:sidebar>
        @include('koordinator.components.sidebar', ['active' => 'pengumuman'])
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

    <style>
        [x-cloak] { display: none !important; }
        .custom-scrollbar::-webkit-scrollbar { width: 5px; height: 5px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: #f1f1f1; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #ccc; border-radius: 10px; }
        
        .underline-input {
            border: none;
            border-bottom: 1px solid #E5E7EB;
            border-radius: 0;
            padding-left: 0;
            padding-right: 0;
            background: transparent !important;
        }
        .underline-input:focus {
            box-shadow: none;
            border-bottom-color: #3B82F6;
        }
    </style>

    <div class="mt-8 px-4 w-full" x-data="{ 
        targetSearch: '',
        showDropdown: false,
        selectedTarget: { id: '', name: '', role: '' },
        searchTable: '',
        sortOrder: 'desc',
        currentPage: 1,
        itemsPerPage: 15,
        selectedFile: null,
        
        targets: [
            { id: 'semua', name: 'Semua User', avatar: null, role: 'Role Global', identifier: '' },
            { id: 'mahasiswa', name: 'Semua Mahasiswa', avatar: null, role: 'Role', identifier: '' },
            { id: 'dosen', name: 'Semua Dosen', avatar: null, role: 'Role', identifier: '' },
            @foreach($users as $user)
            @php
                $identifier = $user->mahasiswa->nim ?? ($user->dosen->nidn ?? '');
                $displayName = $user->name . ($identifier ? " ($identifier)" : "");
            @endphp
            { 
                id: '{{ $user->id }}', 
                name: '{{ addslashes($displayName) }}', 
                avatar: '{{ $user->avatar ? asset('storage/'.$user->avatar) : 'https://ui-avatars.com/api/?name='.urlencode($user->name).'&background=random' }}',
                role: '{{ strtoupper($user->role) }}',
                identifier: '{{ $identifier }}'
            },
            @endforeach
        ],
        logs: {{ \Illuminate\Support\Js::from($logs->getCollection()->map(fn($l) => [
            'id' => $l->id,
            'penerima' => match($l->target_role) {
                'group_semua', 'semua' => 'Semua User',
                'group_mahasiswa', 'mahasiswa' => 'Semua Mahasiswa',
                'group_dosen', 'dosen' => 'Semua Dosen',
                default => ($l->receiver->name ?? 'User')
            },
            'penerima_id' => !$l->target_role && $l->receiver ? ($l->receiver->nim ?? $l->receiver->dosen->nidn ?? '') : '',
            'judul' => $l->judul,
            'pesan' => $l->pesan,
            'file_path' => $l->file_path ? asset('storage/'.$l->file_path) : null,
            'hari' => $l->created_at->isoFormat('dddd,'),
            'tanggal' => $l->created_at->isoFormat('DD MMMM YYYY'),
            'timestamp' => $l->created_at->timestamp,
            'url' => route('koordinator.pengumuman.show', $l->id)
        ])) }},

        get filteredTargets() {
            if (!this.targetSearch) return this.targets;
            const term = this.targetSearch.toLowerCase();
            return this.targets.filter(t => 
                t.name.toLowerCase().includes(term) || 
                t.identifier.toLowerCase().includes(term)
            );
        },
        selectTarget(t) {
            this.selectedTarget = t;
            this.showDropdown = false;
            this.targetSearch = '';
        },

        get filteredLogs() {
            let list = this.logs.filter(l => {
                const term = this.searchTable.toLowerCase();
                return l.penerima.toLowerCase().includes(term) || 
                       l.penerima_id.toLowerCase().includes(term) ||
                       l.judul.toLowerCase().includes(term) || 
                       l.pesan.toLowerCase().includes(term);
            });

            if (this.sortOrder === 'desc') {
                list.sort((a, b) => b.timestamp - a.timestamp);
            } else {
                list.sort((a, b) => a.timestamp - b.timestamp);
            }

            return list;
        },

        get totalPages() {
            return Math.ceil(this.filteredLogs.length / this.itemsPerPage) || 1;
        },

        get paginatedLogs() {
            const start = (this.currentPage - 1) * this.itemsPerPage;
            return this.filteredLogs.slice(start, start + this.itemsPerPage);
        },

        nextPage() { if (this.currentPage < this.totalPages) this.currentPage++; },
        prevPage() { if (this.currentPage > 1) this.currentPage--; },
        resetPagination() { this.currentPage = 1; },

        handleFileChange(e) {
            const file = e.target.files[0];
            if (file) {
                this.selectedFile = file.name;
            }
        }
    }">
        
        <!-- Form Section - Half Width -->
        <div class="max-w-2xl bg-white rounded-[5px] border border-gray-300 shadow-sm overflow-hidden mb-12">
            <form action="{{ route('koordinator.pengumuman.store') }}" method="POST" enctype="multipart/form-data" class="p-6 space-y-5">
                @csrf
                
                <!-- Kepada -->
                <div class="flex items-start gap-4">
                    <label class="text-[12px] font-bold text-gray-700 min-w-[70px] mt-2 uppercase">Kepada :</label>
                    <div class="flex-1 relative">
                        <input type="hidden" name="target" :value="selectedTarget.id" required>
                        <div @click="showDropdown = !showDropdown" class="w-full underline-input h-8 flex items-center justify-between cursor-pointer border-b border-gray-200">
                            <template x-if="selectedTarget.name">
                                <span class="text-[12px] font-bold text-black" x-text="selectedTarget.name"></span>
                            </template>
                            <template x-if="!selectedTarget.name">
                                <span class="text-[12px] text-gray-400 font-normal">Penerima ..</span>
                            </template>
                            <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </div>
                        
                        <!-- Searchable Dropdown List -->
                        <div x-show="showDropdown" @click.outside="showDropdown = false" x-cloak x-transition class="absolute z-50 left-0 right-0 top-9 bg-white border border-gray-200 rounded-md shadow-xl max-h-[200px] overflow-hidden flex flex-col">
                            <div class="p-2 border-b border-gray-100 bg-gray-50">
                                <input type="text" x-model="targetSearch" @click.stop class="w-full h-7 px-2 text-[11px] border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-blue-500" placeholder="Cari nama...">
                            </div>
                            <div class="overflow-y-auto custom-scrollbar">
                                <template x-for="t in filteredTargets" :key="t.id">
                                    <div @click="selectTarget(t)" class="px-3 py-1.5 hover:bg-blue-50 cursor-pointer text-[11px] border-b border-gray-50 flex items-center gap-2">
                                        <template x-if="t.avatar">
                                            <img :src="t.avatar" class="w-4 h-4 rounded-full object-cover">
                                        </template>
                                        <template x-if="!t.avatar">
                                            <div class="w-4 h-4 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 text-[8px] font-bold">
                                                <span x-text="t.name ? t.name.charAt(0) : '?'"></span>
                                            </div>
                                        </template>
                                        <span x-text="t.name"></span>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Subject -->
                <div class="flex items-center gap-4">
                    <label class="text-[12px] font-bold text-gray-700 min-w-[70px] uppercase">Subject :</label>
                    <input type="text" name="judul" required class="flex-1 underline-input h-8 text-[12px] font-normal" placeholder="">
                </div>

                <!-- Message Area -->
                <textarea name="pesan" required rows="6" class="w-full border border-gray-200 rounded-md p-3 text-[12px] text-black focus:outline-none focus:ring-1 focus:ring-blue-500 resize-none bg-gray-50/20" placeholder="Tuliskan pesan anda disini..."></textarea>

                <!-- Bottom Form Bar -->
                <div class="flex items-center justify-between pt-4 border-t border-gray-100">
                    <div class="flex flex-col gap-1">
                        <button type="button" @click="$refs.fileInput.click()" class="bg-[#CCCCCC] hover:bg-gray-300 text-gray-700 font-bold text-[11px] px-4 py-1.5 rounded-full flex items-center gap-2 transition-colors">
                            <span class="text-sm font-bold">+</span> Tambah File
                        </button>
                        <input type="file" name="attachment" x-ref="fileInput" class="hidden" @change="handleFileChange">
                        <span x-show="selectedFile" x-text="selectedFile" class="text-[10px] text-blue-600 truncate max-w-[150px] italic"></span>
                    </div>
                    
                    <button type="submit" class="bg-[#008000] hover:bg-green-700 text-white font-bold text-[11px] px-6 py-2 rounded-full flex items-center gap-2 shadow-sm transition-all uppercase tracking-wider">
                        <svg class="w-3.5 h-3.5 transform -rotate-45" fill="currentColor" viewBox="0 0 24 24"><path d="M2.01 21L23 12 2.01 3 2 10l15 2-15 2z"/></svg>
                        Kirim
                    </button>
                </div>
            </form>
        </div>

        <div class="bg-white rounded-[15px] border border-gray-200 shadow-sm overflow-hidden p-6 mb-8">
            <div class="mb-6 flex flex-col sm:flex-row justify-between items-start gap-4">
                <div>
                    <h3 class="text-[18px] font-bold text-black tracking-tight">Riwayat Pengumuman</h3>
                    <p class="text-[12px] text-black/60 font-medium mt-1">Daftar riwayat pengumuman yang pernah dibuat.</p>
                </div>
            </div>

            <div class="flex flex-col sm:flex-row justify-between items-center gap-6 mb-6">
                <div class="flex flex-wrap items-center gap-4 w-full sm:w-auto">
                    <!-- Search Input -->
                    <div class="relative flex-1 sm:w-[300px]">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        </div>
                        <input type="text" x-model="searchTable" @input="resetPagination()" class="block w-full pl-9 pr-4 py-2 border border-gray-300 rounded-[5px] text-[12px] text-black focus:ring-[#4285F4]" placeholder="Cari berdasarkan nama...">
                    </div>

                    <!-- Tanggal (Sort) Dropdown Filter -->
                    <div x-data="{ openSort: false }" class="relative w-full sm:w-[150px] z-[50]" @click.outside="openSort = false">
                        <button type="button" @click="openSort = !openSort" class="w-full text-[12px] border border-gray-300 rounded-[5px] py-2 px-3 bg-white text-black font-medium focus:ring-[#4285F4] flex justify-between items-center text-left shadow-sm">
                            <span class="truncate" x-text="sortOrder === 'desc' ? 'Terbaru' : 'Terlama'"></span>
                            <svg :class="openSort ? 'rotate-0' : 'rotate-90'" class="w-3.5 h-3.5 transition-all duration-200 text-gray-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <div x-show="openSort" x-transition x-cloak class="absolute w-full mt-1 bg-white border border-gray-300 rounded-[5px] shadow-lg overflow-hidden py-1 z-50">
                            <label class="block px-3 py-2 text-[12px] hover:bg-gray-100 cursor-pointer text-black"><input type="radio" value="desc" x-model="sortOrder" class="hidden" @change="openSort = false; resetPagination()">Terbaru</label>
                            <label class="block px-3 py-2 text-[12px] hover:bg-gray-100 cursor-pointer text-black"><input type="radio" value="asc" x-model="sortOrder" class="hidden" @change="openSort = false; resetPagination()">Terlama</label>
                        </div>
                    </div>
                </div>

                <div class="flex gap-2 w-full sm:w-auto">
                    <button type="button" @click="searchTable = ''; sortOrder = 'desc'; resetPagination()" class="flex-1 sm:flex-none border border-[#EA4335] bg-[#EA4335] text-white hover:bg-red-600 transition-colors px-4 py-1.5 rounded-[5px] text-[12px] font-bold shadow-sm flex items-center justify-center">
                        Clear Filter
                    </button>
                </div>
            </div>

            <div class="border border-gray-200 rounded-[10px] overflow-hidden">
                <div class="overflow-x-auto custom-scrollbar">
                    <table class="w-full border-collapse text-[11px] min-w-[800px]">
                    <thead>
                        <tr class="bg-[#EBEBEB] text-black text-center">
                            <th class="py-3 px-4 border-b border-r border-gray-300 w-[40px]">
                                <input type="checkbox" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                            </th>
                            <th class="py-3 px-4 font-bold text-center border-b border-r border-gray-300 w-[200px]">Kepada</th>
                            <th class="py-3 px-4 font-bold text-center border-b border-r border-gray-300">Pesan</th>
                            <th class="py-3 px-4 font-bold text-center border-b border-gray-300 w-[150px]">Tanggal</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        <template x-for="(log, index) in paginatedLogs" :key="log.id">
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="py-3 px-4 border-r border-gray-200 text-center">
                                    <input type="checkbox" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                </td>
                                <td class="py-3 px-4 border-r border-gray-200">
                                    <a :href="log.url" class="block font-bold text-black text-[12px] truncate hover:text-blue-600">
                                        <span x-text="log.penerima"></span>
                                        <template x-if="log.penerima_id">
                                            <span class="text-black/50 font-normal" x-text="'('+log.penerima_id+')'"></span>
                                        </template>
                                    </a>
                                </td>
                                <td class="py-3 px-4 border-r border-gray-200 text-left">
                                    <a :href="log.url" class="flex flex-col gap-0.5 max-w-[500px] group">
                                        <div class="font-bold text-black text-[12px] truncate flex items-center gap-2 group-hover:text-blue-600">
                                            <template x-if="log.file_path">
                                                <svg class="w-3 h-3 text-blue-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8 4a3 3 0 00-3 3v4a5 5 0 0010 0V7a1 1 0 112 0v4a7 7 0 11-14 0V7a5 5 0 0110 0v4a3 3 0 11-6 0V7a1 1 0 012 0v4a1 1 0 102 0V7a3 3 0 00-3-3z" clip-rule="evenodd"></path></svg>
                                            </template>
                                            <span x-text="log.judul"></span>
                                        </div>
                                        <div class="text-black/60 text-[11px] truncate" x-text="log.pesan"></div>
                                    </a>
                                </td>
                                <td class="py-3 px-4 text-center text-black/70 font-medium leading-snug">
                                    <span x-text="log.hari"></span><br>
                                    <span x-text="log.tanggal"></span>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
                </div>
            </div>

            <div class="px-6 py-4 border-t border-gray-200 flex items-center justify-between mt-4" x-show="totalPages > 1">
                <span class="text-[12px] font-medium text-black/50" x-text="(filteredLogs.length === 0 ? 0 : ((currentPage - 1) * itemsPerPage + 1)) + ' - ' + Math.min(currentPage * itemsPerPage, filteredLogs.length) + ' dari ' + filteredLogs.length + ' baris'"></span>
                <div class="flex items-center gap-2">
                    <button @click="prevPage" :disabled="currentPage === 1" 
                        class="px-3 py-1 border border-gray-300 rounded text-[12px] hover:bg-gray-50 disabled:opacity-30 disabled:cursor-not-allowed transition-colors">Previous</button>
                    <div class="flex items-center gap-1">
                        <template x-for="p in totalPages" :key="p">
                            <button @click="currentPage = p" 
                                class="w-8 h-8 rounded text-[12px] font-bold transition-all"
                                :class="currentPage === p ? 'bg-blue-600 text-white shadow-md' : 'text-black hover:bg-gray-100'"
                                x-text="p"></button>
                        </template>
                    </div>
                    <button @click="nextPage" :disabled="currentPage === totalPages" 
                        class="px-3 py-1 border border-gray-300 rounded text-[12px] hover:bg-gray-50 disabled:opacity-30 disabled:cursor-not-allowed transition-colors">Next</button>
                </div>
            </div>
        </div>

        <div class="h-20"></div>
    </div>
</x-dashboard-layout>
