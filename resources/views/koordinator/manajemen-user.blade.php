<x-dashboard-layout header="Manajemen User" userName="{{ auth()->user()->name ?? 'Koordinator' }}" roleName="KOORDINATOR KP">
    <x-slot:sidebar>
        @include('koordinator.components.sidebar', ['active' => 'manajemen-akses'])
    </x-slot>

    <!-- Header Actions (Periode Box sama persis Dashboard) -->
    <x-slot:headerActions>
        <div x-data="{ open: false, selected: 'Genap 2025/2026' }" class="relative w-[212px]">
            <button @click="open = !open" @click.outside="open = false" type="button" 
                class="w-full flex items-center justify-between border border-[#CAC0C0] bg-[#FBFBFB] rounded-[5px] shadow-sm text-[13px] font-medium py-1.5 px-3 focus:outline-none focus:border-[#4CC098] focus:ring-1 focus:ring-[#4CC098] cursor-pointer text-black h-[32px]">
                
                <span x-text="selected"></span>
                
                <svg :class="open ? '-rotate-90' : 'rotate-0'" class="w-3.5 h-3.5 text-gray-500 transition-transform duration-200 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"></path>
                </svg>
            </button>

            <div x-show="open" x-transition style="display: none;" 
                class="absolute z-50 w-full mt-1 bg-[#FBFBFB] border border-[#CAC0C0] rounded-[5px] shadow-lg overflow-hidden">
                <ul class="py-1 text-[13px] font-medium text-black">
                    <li><button @click="selected = 'Genap 2025/2026'; open = false; document.getElementById('filterForm').submit()" type="button" class="block w-full text-left px-3 py-2 hover:bg-[#E8E5E5] transition-colors cursor-pointer">Genap 2025/2026</button></li>
                    <li><button @click="selected = 'Ganjil 2025/2026'; open = false; document.getElementById('filterForm').submit()" type="button" class="block w-full text-left px-3 py-2 hover:bg-[#E8E5E5] transition-colors cursor-pointer">Ganjil 2025/2026</button></li>
                </ul>
            </div>
        </div>
    </x-slot:headerActions>

    <!-- Shared Alpine State -->
    <div class="mt-8 px-4 w-full max-w-6xl mx-auto pb-12 font-inter" x-data="{ 
        tab: '{{ $tab }}',
        showAddModal: {{ $errors->has('file_import') ? 'true' : 'false' }}, 
        selectedRole: 'Input Role User', 
        openRole: false,
        confirmType: '',
        confirmActionUrl: '',
        showConfirmModal: false,
        
        openAddConfirm() {
            const form = document.getElementById('formManual');
            if (form.checkValidity()) {
                this.confirmType = 'add';
                this.showConfirmModal = true;
            } else {
                form.reportValidity();
            }
        },

        openDeleteConfirm(url) {
            this.confirmActionUrl = url;
            this.confirmType = 'delete';
            this.showConfirmModal = true;
        },

        executeConfirm() {
            if (this.confirmType === 'add') {
                document.getElementById('formManual').submit();
            } else if (this.confirmType === 'delete') {
                document.getElementById('deleteForm-' + this.confirmActionUrl).submit();
            }
            this.showConfirmModal = false;
        }
    }">

        <!-- Header and Tools -->
        <div class="flex flex-wrap items-center justify-between gap-4 mb-8">
            <div class="flex items-center gap-4">
                <button @click="showAddModal = true" class="w-[104px] h-[36px] bg-[#4CAF50] rounded-[5px] flex items-center justify-center gap-1 text-white text-[14px] font-medium shadow-sm hover:bg-[#45a049] transition-colors">
                    <span class="text-[20px] leading-none mb-1">+</span> Tambah
                </button>

                <form id="filterForm" action="{{ url()->current() }}" method="GET" class="relative w-[340px] h-[36px]">
                    <input type="hidden" name="tab" :value="tab">
                    <input type="hidden" name="status" id="statusFilter" value="{{ request('status') }}">
                    <svg class="w-4 h-4 absolute left-3 top-2.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari berdasarkan Nama..." class="w-full h-full pl-10 pr-3 border border-black/50 rounded-[5px] text-[14px] focus:outline-none focus:border-black/70 bg-white" onkeydown="if(event.key === 'Enter') this.form.submit()">
                </form>
            </div>

            <div class="flex items-center gap-4">
                
                @if($tab === 'dosen')
                <div x-data="{ openStatus: false, selectedStatus: '{{ request('status') ?: 'Status' }}' }" class="relative w-[130px] h-[36px]">
                    <button @click="openStatus = !openStatus" @click.outside="openStatus = false" type="button" 
                        class="w-full h-full flex items-center justify-between border border-black/50 bg-white rounded-[5px] text-[14px] text-gray-700 px-3 outline-none focus:border-black/70 cursor-pointer">
                        <span x-text="selectedStatus"></span>
                        <svg :class="openStatus ? '-rotate-90' : 'rotate-0'" class="w-3 h-3 text-black font-bold transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M15 19l-7-7 7-7"></path>
                        </svg>
                    </button>
                    <!-- Custom Dropdown Menu for Status -->
                    <div x-show="openStatus" x-transition style="display: none;" 
                        class="absolute z-50 w-full mt-1 bg-white border border-black/50 rounded-[5px] shadow-sm overflow-hidden">
                        <ul class="py-1 text-[13px] text-gray-700">
                            <li><button @click="selectedStatus = 'Status'; openStatus = false; document.getElementById('statusFilter').value = ''; document.getElementById('filterForm').submit()" type="button" class="block w-full text-left px-3 py-1.5 hover:bg-gray-100 transition-colors">Semua Status</button></li>
                            <li><button @click="selectedStatus = 'Aktif'; openStatus = false; document.getElementById('statusFilter').value = 'Aktif'; document.getElementById('filterForm').submit()" type="button" class="block w-full text-left px-3 py-1.5 hover:bg-gray-100 transition-colors">Aktif</button></li>
                            <li><button @click="selectedStatus = 'Tidak Aktif'; openStatus = false; document.getElementById('statusFilter').value = 'Tidak Aktif'; document.getElementById('filterForm').submit()" type="button" class="block w-full text-left px-3 py-1.5 hover:bg-gray-100 transition-colors">Tidak Aktif</button></li>
                        </ul>
                    </div>
                </div>
                @endif

                <!-- Export PDF Dropdown -->
                <div x-data="{ openExport: false }" class="relative w-[150px] h-[36px]">
                    <button @click="openExport = !openExport" @click.outside="openExport = false" type="button" 
                        class="w-full h-full bg-[#E32727] hover:bg-red-700 transition-colors rounded-full flex items-center justify-center gap-2 text-white text-[14px] shadow-sm font-medium focus:outline-none">
                        <div class="w-[14px] h-[18px] bg-black/30 border border-white flex items-center justify-center">
                            <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" /></svg>
                        </div>
                        Export PDF
                    </button>
                    <!-- Dropdown Menu -->
                    <div x-show="openExport" x-transition style="display: none;" 
                        class="absolute z-50 right-0 w-48 mt-2 bg-white border border-gray-200 rounded-[5px] shadow-lg overflow-hidden">
                        <ul class="py-1 text-[13px] text-gray-700">
                            <li><a href="{{ route('koordinator.user.export-pdf', ['type' => 'semua']) }}" target="_blank" class="block w-full text-left px-4 py-2 hover:bg-gray-100 transition-colors font-medium">Export Semua User</a></li>
                            <li><a href="{{ route('koordinator.user.export-pdf', ['type' => 'dosen']) }}" target="_blank" class="block w-full text-left px-4 py-2 hover:bg-gray-100 transition-colors font-medium">Export Data Dosen</a></li>
                            <li><a href="{{ route('koordinator.user.export-pdf', ['type' => 'mahasiswa']) }}" target="_blank" class="block w-full text-left px-4 py-2 hover:bg-gray-100 transition-colors font-medium">Export Data Mahasiswa</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- Global Session Messages (Selain Import/File) -->
        @if(session('success'))
            <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg relative mb-6 shadow-sm flex items-center gap-3">
                <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                <span class="text-sm font-medium">{{ session('success') }}</span>
            </div>
        @endif
        @if(session('error') && !$errors->has('file_import'))
            <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg relative mb-6 shadow-sm">
                <p class="text-sm font-medium">{{ session('error') }}</p>
            </div>
        @endif

        <!-- Tabs & Table -->
        <div>
            <!-- Tabs -->
            <div class="flex items-end h-[36px]">
                <a href="{{ url()->current() }}?tab=dosen" 
                   :class="tab === 'dosen' ? 'bg-[#D9D9D9] border border-black/50 border-b-0 h-[36px] z-10' : 'bg-[#E8E8E8] border border-black/50 opacity-70 h-[34px] border-b-black'"
                   class="w-[110px] text-[14px] font-medium rounded-t-[5px] relative flex items-center justify-center text-black hover:opacity-100 transition-all">
                   Dosen
                </a>
                <a href="{{ url()->current() }}?tab=mahasiswa" 
                   :class="tab === 'mahasiswa' ? 'bg-[#D9D9D9] border border-black/50 border-b-0 h-[36px] z-10' : 'bg-[#E8E8E8] border border-black/50 opacity-70 h-[34px] border-b-black'"
                   class="w-[110px] text-[14px] font-medium rounded-t-[5px] relative left-[-1px] flex items-center justify-center text-black hover:opacity-100 transition-all">
                   Mahasiswa
                </a>
            </div>

            <!-- Table Container -->
            <div class="bg-white border border-black/50 border-t-0 rounded-b-[5px] rounded-tr-[5px] overflow-x-auto relative top-[-1px]">
                <table class="w-full text-left text-[13px] font-medium text-black border-collapse">
                    <thead class="bg-[#B0AFB5]">
                        <tr class="h-[40px] text-gray-900 border-b border-black/50">
                            <th class="border-r border-black/40 font-medium w-[5%] pl-4">No</th>
                            <th class="border-r border-black/40 font-medium w-[23%] px-4">Nama</th>
                            <th class="border-r border-black/40 font-medium w-[12%] px-4">ID</th>
                            <th class="border-r border-black/40 font-medium w-[15%] px-4">Role</th>
                            <th class="border-r border-black/40 font-medium {{ $tab === 'dosen' ? 'w-[20%]' : 'w-[30%]' }} px-4 text-left">Email</th>
                            @if($tab === 'dosen')
                            <th class="border-r border-black/40 font-medium w-[15%] px-4">Status</th>
                            @endif
                            <th class="font-medium {{ $tab === 'dosen' ? 'w-[10%]' : 'w-[15%]' }} text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-black/40">
                        @forelse($users as $index => $user)
                        <tr class="h-[45px] hover:bg-gray-50 transition-colors text-gray-900">
                            <td class="border-r border-black/40 pl-4">{{ $users->firstItem() + $index }}</td>
                            <td class="border-r border-black/40 text-left px-4">{{ $user->name }}</td>
                            <td class="border-r border-black/40 px-4">{{ $user->identifier_id ?? '-' }}</td>
                            <td class="border-r border-black/40 px-4">
                                {{ ucwords(str_replace('_', ' ', $user->role)) }}
                            </td>
                            <td class="border-r border-black/40 px-4 text-left break-all" title="{{ $user->email }}">{{ $user->email }}</td>
                            @if($tab === 'dosen')
                            <td class="border-r border-black/40 px-2 lg:text-[13px]">
                                <form action="{{ route('koordinator.user.update-status', $user->id) }}" method="POST" class="m-0">
                                    @csrf
                                    @method('PUT')
                                    <select name="status" onchange="this.form.submit()" class="w-full bg-transparent border-none outline-none cursor-pointer text-left focus:ring-0 italic text-[13px] pr-6 {{ $user->is_aktif !== false ? 'text-green-700' : 'text-red-700' }}">
                                        <option class="text-green-700 italic text-[13px]" value="Aktif" {{ $user->is_aktif !== false ? 'selected' : '' }}>Aktif</option>
                                        <option class="text-red-700 italic text-[13px]" value="Nonaktif" {{ $user->is_aktif === false ? 'selected' : '' }}>Tidak Aktif</option>
                                    </select>
                                </form>
                            </td>
                            @endif
                            <td class="px-2 text-center">
                                <div class="flex items-center justify-center gap-3">
                                    <a href="{{ route('koordinator.user.edit', $user->id) }}" class="text-gray-500 hover:text-[#456DA7] hover:bg-blue-50 p-1.5 rounded-md transition-colors" title="Edit Data">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                    </a>
                                    <form id="deleteForm-{{ $user->id }}" action="{{ route('koordinator.user.destroy', $user->id) }}" method="POST" class="m-0 p-0 inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" @click="openDeleteConfirm('{{ $user->id }}')" class="text-gray-500 hover:text-[#E32727] hover:bg-red-50 p-1.5 rounded-md transition-colors" title="Hapus Data">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="{{ $tab === 'dosen' ? 7 : 6 }}" class="py-8 text-gray-500 text-center">Belum ada data user yang terdaftar.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <div class="mt-4 flex justify-end">
                {{ $users->appends(request()->query())->links() }}
            </div>
        </div>

        <!-- Tambah User Modal -->
        <div x-show="showAddModal" style="display: none;" class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm p-4">
            <div @click.away="showAddModal = false" class="bg-[#F4F3F3] border border-black/50 rounded-[30px] w-full max-w-[850px] shadow-2xl relative overflow-hidden" x-transition>
                
                <div class="px-10 pt-8 pb-4">
                    <h2 class="text-[24px] font-bold text-black font-inter mb-4">Tambah User</h2>

                    <!-- Instructions -->
                    <div class="bg-blue-50/80 border border-blue-200 rounded-lg p-3 mb-6">
                        <p class="text-sm font-medium text-blue-800 flex items-start gap-2">
                            <svg class="w-5 h-5 text-blue-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            <span><strong>Tips Tambah Sekaligus:</strong> Jika Anda memiliki data masif berukuran besar, silakan klik tombol <strong>Download template</strong> di bawah, isi datanya (Kolom Role tersedia dropdown), lalu <strong>Upload File</strong> Excel tersebut kembali dalam form Bahasa Indonesia.</span>
                        </p>
                    </div>

                    <!-- Upload Error Warning Alerts (Indonesian) -->
                    @if($errors->has('file_import'))
                    <div class="bg-red-50 border border-red-300 rounded-lg p-3 mb-6 flex items-start gap-2">
                        <svg class="w-5 h-5 text-red-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <p class="text-sm font-bold text-red-700">{{ $errors->first('file_import') }}</p>
                    </div>
                    @endif
                    
                    <form action="{{ route('koordinator.user.store') }}" method="POST" id="formManual">
                        @csrf
                        <div class="space-y-4 max-w-[600px] mb-12">
                            <!-- Nama -->
                            <div class="flex items-center">
                                <label class="w-[200px] text-[15px] text-black font-medium">Nama Lengkap</label>
                                <span class="text-black mx-4">:</span>
                                <input type="text" name="name" required class="flex-1 max-w-[300px] h-[32px] bg-[#D9D9D9] px-3 font-italic text-[14px] text-black outline-none focus:ring-1 focus:ring-blue-500" placeholder="Ketik nama...">
                            </div>

                            <!-- ID -->
                            <div class="flex items-center">
                                <label class="w-[200px] text-[15px] text-black font-medium">ID (NIM/NIDN)</label>
                                <span class="text-black mx-4">:</span>
                                <input type="text" name="id_user" required class="flex-1 max-w-[300px] h-[32px] bg-[#D9D9D9] px-3 font-italic text-[14px] text-black outline-none focus:ring-1 focus:ring-blue-500" placeholder="Input ID User">
                            </div>

                            <!-- Email -->
                            <div class="flex items-center">
                                <label class="w-[200px] text-[15px] text-black font-medium">Email Utama</label>
                                <span class="text-black mx-4">:</span>
                                <input type="email" name="email" required class="flex-1 max-w-[300px] h-[32px] bg-[#D9D9D9] px-3 font-italic text-[14px] text-black outline-none focus:ring-1 focus:ring-blue-500" placeholder="Input Email User">
                            </div>

                            <!-- Role Dropdown -->
                            <div class="flex items-start">
                                <label class="w-[200px] text-[15px] text-black mt-1 font-medium">Role System</label>
                                <span class="text-black mx-4 mt-1">:</span>
                                <div class="relative flex-1 max-w-[300px]">
                                    <button @click="openRole = !openRole" type="button" class="w-full h-[32px] bg-[#D9D9D9] px-3 flex items-center justify-between outline-none focus:ring-1 focus:ring-blue-500">
                                        <span x-text="selectedRole" :class="selectedRole === 'Input Role User' ? 'text-black/50 italic text-[14px]' : 'text-black text-[14px] font-medium'"></span>
                                        <span class="text-black/70 font-bold transform rotate-90" :class="openRole ? '-rotate-90' : ''">&gt;</span>
                                    </button>

                                    <!-- Dropdown Menu -->
                                    <div x-show="openRole" @click.away="openRole = false" class="absolute z-10 w-full mt-1 bg-[#E4E3E3] shadow-md border border-gray-300">
                                        <div @click="selectedRole = 'Koordinator KP'; openRole = false" class="px-4 py-2 text-[14px] font-medium cursor-pointer hover:bg-[#456DA7] hover:text-[#EBDFDF] transition-colors" :class="selectedRole === 'Koordinator KP' ? 'bg-[#456DA7] text-[#EBDFDF]' : 'text-[#333]'">Koordinator KP</div>
                                        <div @click="selectedRole = 'Dosen'; openRole = false" class="px-4 py-2 text-[14px] font-medium cursor-pointer hover:bg-[#456DA7] hover:text-[#EBDFDF] transition-colors" :class="selectedRole === 'Dosen' ? 'bg-[#456DA7] text-[#EBDFDF]' : 'text-[#333]'">Dosen</div>
                                        <div @click="selectedRole = 'Mahasiswa'; openRole = false" class="px-4 py-2 text-[14px] font-medium cursor-pointer hover:bg-[#456DA7] hover:text-[#EBDFDF] transition-colors" :class="selectedRole === 'Mahasiswa' ? 'bg-[#456DA7] text-[#EBDFDF]' : 'text-[#333]'">Mahasiswa</div>
                                    </div>
                                    <input type="hidden" name="role" required :value="selectedRole === 'Koordinator KP' ? 'koordinator_kp' : (selectedRole === 'Input Role User' ? '' : selectedRole.toLowerCase())">
                                </div>
                            </div>
                        </div>

                        <!-- Footer Actions -->
                        <div class="flex items-center justify-between mt-8 pt-4 border-t border-black/10">
                            <!-- Left: File actions -->
                            <div class="flex gap-4">
                                <a href="{{ route('koordinator.user.template.download') }}" class="w-[180px] h-[34px] bg-[#6C6F77] hover:bg-gray-600 rounded-[5px] flex items-center justify-center gap-2 text-white font-bold text-[12px] shadow-sm transition-colors cursor-pointer ring-1 ring-black/20">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                    Download template
                                </a>
                                
                                <label class="w-[180px] h-[34px] bg-[#3A6FF7] hover:bg-blue-600 rounded-[5px] flex items-center justify-center gap-2 text-white font-bold text-[12px] shadow-sm transition-colors cursor-pointer ring-1 ring-black/20">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></path></svg>
                                    Upload File Excel
                                    <input type="file" form="importForm" name="file_import" class="hidden" onchange="document.getElementById('importForm').submit()" accept=".xlsx,.xls">
                                </label>
                            </div>

                            <!-- Right: Submit actions -->
                            <div class="flex gap-6 pr-6">
                                <button @click="showAddModal = false" type="button" class="w-[104px] h-[32px] bg-[#E32727] hover:bg-red-700 text-white font-medium text-[14px] rounded-[5px] transition-colors shadow-sm focus:outline-none">
                                    Keluar
                                </button>
                                <button type="button" @click="openAddConfirm()" class="w-[104px] h-[32px] bg-[#008000] hover:bg-green-700 text-white font-medium text-[14px] rounded-[5px] transition-colors shadow-sm focus:outline-none">
                                    Kirim
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <form id="importForm" action="{{ route('koordinator.user.import') }}" method="POST" enctype="multipart/form-data" class="hidden">
            @csrf
        </form>

        <!-- Custom Global Confirm Modal -->
        <div x-show="showConfirmModal" style="display: none;" class="fixed inset-0 z-[110] flex items-center justify-center bg-black/40 backdrop-blur-sm p-4">
            <div @click.away="showConfirmModal = false" class="bg-white rounded-[10px] w-full max-w-[450px] p-8 shadow-2xl flex flex-col items-center justify-center text-center transform transition-all">
                
                <!-- Icon -->
                <div x-show="confirmType === 'add'" class="mb-5">
                    <svg class="w-16 h-16 text-[#4CAF50]" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                    </svg>
                </div>
                <div x-show="confirmType === 'delete'" class="mb-5">
                    <svg class="w-16 h-16 text-[#E53935]" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M1 21h22L12 2 1 21zm12-3h-2v-2h2v2zm0-4h-2v-4h2v4z"/>
                    </svg>
                </div>

                <!-- Message -->
                <h3 class="text-black font-semibold text-[16px] mb-8" x-text="confirmType === 'add' ? 'Apakah Anda yakin ingin menambahkan user ini?' : 'Apakah Anda yakin ingin menghapus user tersebut secara permanen?'"></h3>

                <!-- Buttons -->
                <div class="flex gap-4 w-full justify-center">
                    <button @click="showConfirmModal = false" type="button" class="w-[100px] h-[34px] bg-[#E32727] hover:bg-red-700 text-white rounded-[5px] text-[14px] font-medium transition-colors shadow-sm">
                        Batal
                    </button>
                    <button @click="executeConfirm()" type="button" class="w-[100px] h-[34px] bg-[#456DA7] hover:bg-blue-700 text-white rounded-[5px] text-[14px] font-medium transition-colors shadow-sm">
                        Iya
                    </button>
                </div>
            </div>
        </div>

    </div>
</x-dashboard-layout>