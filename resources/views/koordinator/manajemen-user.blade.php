<x-dashboard-layout header="Manajemen User" userName="{{ auth()->user()->name ?? 'Koordinator' }}" roleName="KOORDINATOR KP">
    <x-slot:sidebar>
        @include('koordinator.components.sidebar', ['active' => 'manajemen-akses'])
    </x-slot>

    <!-- Header Actions (Periode Box sama persis Dashboard) -->
    <x-slot:headerActions>
        <div x-data="{ open: false, selected: 'Genap 2025/2026' }" class="relative w-full md:w-[212px]">
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
    <div class="mt-8 px-4 w-full max-w-6xl mx-auto pb-12 font-inter" x-data="userManager()" x-init="init()">
        
        <!-- Turbo-style Progress Bar -->
        <div class="fixed top-0 left-0 w-full z-[1000] pointer-events-none h-[3px]">
            <div 
                class="h-full bg-gradient-to-r from-blue-400 via-blue-600 to-blue-700 shadow-[0_0_10px_rgba(59,130,246,0.8)] transition-all duration-300 ease-out"
                :style="'width: ' + progress + '%'"
                x-show="progress > 0"
                x-transition:leave="transition ease-in duration-500"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
            ></div>
        </div>

        <!-- Header and Tools -->
        <div class="flex flex-col lg:flex-row items-stretch lg:items-center justify-between gap-4 mb-8">
            <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-4">
                <button @click="showAddModal = true" class="w-full sm:w-[104px] h-[36px] bg-[#4CAF50] rounded-[5px] flex items-center justify-center gap-1 text-white text-[14px] font-medium shadow-sm hover:bg-[#45a049] transition-colors flex-shrink-0">
                    <span class="text-[20px] leading-none mb-1">+</span> Tambah
                </button>

                <div class="relative w-full sm:w-[340px] h-[36px]">
                    <svg class="w-4 h-4 absolute left-3 top-2.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    <input type="text" x-model="search" @input.debounce.500ms="fetchData()" placeholder="Cari berdasarkan Nama..." class="w-full h-full pl-10 pr-3 border border-black/50 rounded-[5px] text-[14px] focus:outline-none focus:border-black/70 bg-white">
                </div>
            </div>

            <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-4">
                
                <template x-if="tab === 'dosen'">
                    <div x-data="{ openStatus: false }" class="relative w-full sm:w-[130px] h-[36px]">
                        <button @click="openStatus = !openStatus" @click.outside="openStatus = false" type="button" 
                            class="w-full h-full flex items-center justify-between border border-black/50 bg-white rounded-[5px] text-[14px] text-gray-700 px-3 outline-none focus:border-black/70 cursor-pointer">
                            <span x-text="selectedStatusLabel || 'Status'"></span>
                            <svg :class="openStatus ? '-rotate-90' : 'rotate-0'" class="w-3 h-3 text-black font-bold transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M15 19l-7-7 7-7"></path>
                            </svg>
                        </button>
                        <div x-show="openStatus" x-transition style="display: none;" 
                            class="absolute z-50 w-full mt-1 bg-white border border-black/50 rounded-[5px] shadow-sm overflow-hidden">
                            <ul class="py-1 text-[13px] text-gray-700">
                                <li><button @click="statusFilter = ''; openStatus = false; fetchData()" type="button" class="block w-full text-left px-3 py-1.5 hover:bg-gray-100 transition-colors">Semua Status</button></li>
                                <li><button @click="statusFilter = 'Aktif'; openStatus = false; fetchData()" type="button" class="block w-full text-left px-3 py-1.5 hover:bg-gray-100 transition-colors">Aktif</button></li>
                                <li><button @click="statusFilter = 'Tidak Aktif'; openStatus = false; fetchData()" type="button" class="block w-full text-left px-3 py-1.5 hover:bg-gray-100 transition-colors">Tidak Aktif</button></li>
                            </ul>
                        </div>
                    </div>
                </template>

                <!-- Export PDF Dropdown -->
                <div x-data="{ openExport: false }" class="relative w-full sm:w-[150px] h-[36px]">
                    <button @click="openExport = !openExport" @click.outside="openExport = false" type="button" 
                        class="w-full h-full bg-[#E32727] hover:bg-red-700 transition-colors rounded-full flex items-center justify-center gap-2 text-white text-[14px] shadow-sm font-medium focus:outline-none">
                        <div class="w-[14px] h-[18px] bg-black/30 border border-white flex items-center justify-center">
                            <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" /></svg>
                        </div>
                        Export PDF
                    </button>
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

        <!-- Notification -->
        <div x-show="notification.show" x-transition 
            :class="notification.type === 'success' ? 'bg-green-50 border-green-200 text-green-700' : 'bg-red-50 border-red-200 text-red-700'"
            class="border px-4 py-3 rounded-lg relative mb-6 shadow-sm flex items-center gap-3" style="display: none;">
            <svg x-show="notification.type === 'success'" class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
            <svg x-show="notification.type === 'error'" class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            <span class="text-sm font-medium" x-text="notification.message"></span>
        </div>

        <!-- Tabs & Table -->
        <div :class="loading ? 'opacity-60 pointer-events-none' : ''" class="transition-opacity duration-300">
            <!-- Tabs -->
            <div class="flex items-end h-[36px]">
                <button @click="switchTab('dosen')" 
                   :class="tab === 'dosen' ? 'bg-[#D9D9D9] border border-black/50 border-b-0 h-[36px] z-10' : 'bg-[#E8E8E8] border border-black/50 opacity-70 h-[34px] border-b-black'"
                   class="w-[110px] text-[14px] font-medium rounded-t-[5px] relative flex items-center justify-center text-black hover:opacity-100 transition-all">
                   Dosen
                </button>
                <button @click="switchTab('mahasiswa')" 
                   :class="tab === 'mahasiswa' ? 'bg-[#D9D9D9] border border-black/50 border-b-0 h-[36px] z-10' : 'bg-[#E8E8E8] border border-black/50 opacity-70 h-[34px] border-b-black'"
                   class="w-[110px] text-[14px] font-medium rounded-t-[5px] relative left-[-1px] flex items-center justify-center text-black hover:opacity-100 transition-all">
                   Mahasiswa
                </button>
            </div>

            <!-- Table Container -->
            <div class="bg-white border border-black/50 border-t-0 rounded-b-[5px] rounded-tr-[5px] overflow-x-auto relative top-[-1px]">
                <table class="w-full min-w-[1000px] text-left text-[13px] font-medium text-black border-collapse">
                    <thead class="bg-[#B0AFB5]">
                        <tr class="h-[40px] text-gray-900 border-b border-black/50">
                            <th class="border-r border-black/40 font-medium w-[5%] pl-4">No</th>
                            <th class="border-r border-black/40 font-medium w-[23%] px-4">Nama</th>
                            <th class="border-r border-black/40 font-medium w-[12%] px-4">ID</th>
                            <th class="border-r border-black/40 font-medium w-[15%] px-4">Role</th>
                            <th class="border-r border-black/40 font-medium :class="tab === 'dosen' ? 'w-[20%]' : 'w-[30%]'" px-4 text-left">Email</th>
                            <template x-if="tab === 'dosen'">
                                <th class="border-r border-black/40 font-medium w-[15%] px-4">Status</th>
                            </template>
                            <th class="font-medium :class="tab === 'dosen' ? 'w-[10%]' : 'w-[15%]'" text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-black/40">
                        <template x-for="(user, index) in users" :key="user.id">
                            <tr class="h-[45px] hover:bg-gray-50 transition-colors text-gray-900">
                                <td class="border-r border-black/40 pl-4" x-text="pagination.from + index"></td>
                                <td class="border-r border-black/40 text-left px-4" x-text="user.name"></td>
                                <td class="border-r border-black/40 px-4" x-text="user.identifier_id || '-'"></td>
                                <td class="border-r border-black/40 px-4" x-text="formatRole(user.role)"></td>
                                <td class="border-r border-black/40 px-4 text-left break-all" x-text="user.email"></td>
                                
                                <template x-if="tab === 'dosen'">
                                    <td class="border-r border-black/40 px-2 lg:text-[13px]">
                                        <select @change="updateStatus(user.id, $event.target.value)" 
                                            class="w-full bg-transparent border-none outline-none cursor-pointer text-left focus:ring-0 italic text-[13px] pr-6"
                                            :class="user.is_aktif !== false ? 'text-green-700' : 'text-red-700'">
                                            <option class="text-green-700 italic text-[13px]" value="Aktif" :selected="user.is_aktif !== false">Aktif</option>
                                            <option class="text-red-700 italic text-[13px]" value="Nonaktif" :selected="user.is_aktif === false">Tidak Aktif</option>
                                        </select>
                                    </td>
                                </template>

                                <td class="px-2 text-center">
                                    <div class="flex items-center justify-center gap-3">
                                        <a :href="'/koordinator/manajemen-akses/' + user.id + '/edit'" class="text-gray-500 hover:text-[#456DA7] hover:bg-blue-50 p-1.5 rounded-md transition-colors" title="Edit Data">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                        </a>
                                        <button @click="openDeleteConfirm(user.id)" class="text-gray-500 hover:text-[#E32727] hover:bg-red-50 p-1.5 rounded-md transition-colors" title="Hapus Data">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        </button>
                                        <form :id="'deleteForm-' + user.id" :action="'/koordinator/manajemen-akses/' + user.id" method="POST" class="hidden">
                                            @csrf
                                            @method('DELETE')
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        </template>
                        <template x-if="users.length === 0">
                            <tr>
                                <td :colspan="tab === 'dosen' ? 7 : 6" class="py-12 text-gray-500 text-center font-medium italic">Belum ada data user yang terdaftar atau tidak ditemukan...</td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            <div class="mt-4 flex justify-end" x-show="pagination.last_page > 1">
                <div class="flex items-center gap-1">
                    <button @click="changePage(pagination.current_page - 1)" 
                        :disabled="pagination.current_page === 1"
                        :class="pagination.current_page === 1 ? 'opacity-50 cursor-not-allowed' : 'hover:bg-gray-100'"
                        class="px-2 py-1 border border-black/30 rounded text-[12px] font-medium transition-colors">
                        Sblm
                    </button>
                    
                    <template x-for="p in pagination.last_page" :key="p">
                        <button @click="changePage(p)"
                            :class="pagination.current_page === p ? 'bg-[#456DA7] text-white border-[#456DA7]' : 'border-black/30 text-black hover:bg-gray-100'"
                            class="w-8 h-8 flex items-center justify-center border rounded text-[12px] font-medium transition-colors"
                            x-text="p"></button>
                    </template>

                    <button @click="changePage(pagination.current_page + 1)" 
                        :disabled="pagination.current_page === pagination.last_page"
                        :class="pagination.current_page === pagination.last_page ? 'opacity-50 cursor-not-allowed' : 'hover:bg-gray-100'"
                        class="px-2 py-1 border border-black/30 rounded text-[12px] font-medium transition-colors">
                        Brkt
                    </button>
                </div>
            </div>
        </div>

        <!-- Tambah User Modal -->
        <div x-show="showAddModal" style="display: none;" class="fixed inset-0 z-[150] flex items-center justify-center bg-black/40 backdrop-blur-sm p-4" x-transition>
            <div @click.away="showAddModal = false" class="bg-[#F4F3F3] border border-black/50 rounded-[30px] w-full max-w-[850px] shadow-2xl relative overflow-hidden">
                
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
                            <div class="flex flex-col md:flex-row md:items-center">
                                <label class="w-full md:w-[200px] text-[15px] text-black font-medium mb-1 md:mb-0">Nama Lengkap</label>
                                <span class="hidden md:inline text-black mx-4">:</span>
                                <input type="text" name="name" required class="flex-1 w-full md:max-w-[300px] h-[32px] bg-[#D9D9D9] px-3 font-italic text-[14px] text-black outline-none focus:ring-1 focus:ring-blue-500" placeholder="Ketik nama...">
                            </div>

                            <!-- ID -->
                            <div class="flex flex-col md:flex-row md:items-center">
                                <label class="w-full md:w-[200px] text-[15px] text-black font-medium mb-1 md:mb-0">ID (NIM/NIDN)</label>
                                <span class="hidden md:inline text-black mx-4">:</span>
                                <input type="text" name="id_user" required class="flex-1 w-full md:max-w-[300px] h-[32px] bg-[#D9D9D9] px-3 font-italic text-[14px] text-black outline-none focus:ring-1 focus:ring-blue-500" placeholder="Input ID User">
                            </div>

                            <!-- Email -->
                            <div class="flex flex-col md:flex-row md:items-center">
                                <label class="w-full md:w-[200px] text-[15px] text-black font-medium mb-1 md:mb-0">Email Utama</label>
                                <span class="hidden md:inline text-black mx-4">:</span>
                                <input type="email" name="email" required class="flex-1 w-full md:max-w-[300px] h-[32px] bg-[#D9D9D9] px-3 font-italic text-[14px] text-black outline-none focus:ring-1 focus:ring-blue-500" placeholder="Input Email User">
                            </div>

                            <!-- Role Dropdown -->
                            <div class="flex flex-col md:flex-row md:items-start">
                                <label class="w-full md:w-[200px] text-[15px] text-black md:mt-1 font-medium mb-1 md:mb-0">Role System</label>
                                <span class="hidden md:inline text-black mx-4 mt-1">:</span>
                                <div class="relative flex-1 w-full md:max-w-[300px]">
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
                        <div class="flex flex-col lg:flex-row items-stretch lg:items-center justify-between gap-4 mt-8 pt-4 border-t border-black/10">
                            <div class="flex flex-col sm:flex-row gap-4">
                                <a href="{{ route('koordinator.user.template.download') }}" class="w-full sm:w-[180px] h-[34px] bg-[#6C6F77] hover:bg-gray-600 rounded-[5px] flex items-center justify-center gap-2 text-white font-bold text-[12px] shadow-sm transition-colors cursor-pointer ring-1 ring-black/20 focus:outline-none">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                    Download template
                                </a>
                                
                                <label class="w-full sm:w-[180px] h-[34px] bg-[#3A6FF7] hover:bg-blue-600 rounded-[5px] flex items-center justify-center gap-2 text-white font-bold text-[12px] shadow-sm transition-colors cursor-pointer ring-1 ring-black/20 mb-0">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></path></svg>
                                    Upload File Excel
                                    <input type="file" form="importForm" name="file_import" class="hidden" onchange="document.getElementById('importForm').submit()" accept=".xlsx,.xls">
                                </label>
                            </div>

                            <div class="flex gap-4 lg:pr-6 justify-end">
                                <button @click="showAddModal = false" type="button" class="flex-1 sm:flex-none w-full sm:w-[104px] h-[32px] bg-[#E32727] hover:bg-red-700 text-white font-medium text-[14px] rounded-[5px] transition-colors shadow-sm focus:outline-none">
                                    Keluar
                                </button>
                                <button type="button" @click="openAddConfirm()" class="flex-1 sm:flex-none w-full sm:w-[104px] h-[32px] bg-[#008000] hover:bg-green-700 text-white font-medium text-[14px] rounded-[5px] transition-colors shadow-sm focus:outline-none">
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
        <div x-show="showConfirmModal" style="display: none;" class="fixed inset-0 z-[200] flex items-center justify-center bg-black/40 backdrop-blur-sm p-4" x-transition>
            <div @click.away="showConfirmModal = false" class="bg-white rounded-[10px] w-full max-w-[450px] p-8 shadow-2xl flex flex-col items-center justify-center text-center transform transition-all">
                
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

                <h3 class="text-black font-semibold text-[16px] mb-8" x-text="confirmType === 'add' ? 'Apakah Anda yakin ingin menambahkan user ini?' : 'Apakah Anda yakin ingin menghapus user tersebut secara permanen?'"></h3>

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

    <script>
        function userManager() {
            return {
                users: @json($users->items()),
                tab: '{{ $tab }}',
                search: '{{ request('search') }}',
                statusFilter: '{{ request('status') }}',
                pagination: {
                    current_page: {{ $users->currentPage() }},
                    last_page: {{ $users->lastPage() }},
                    from: {{ $users->firstItem() ?? 0 }},
                    to: {{ $users->lastItem() ?? 0 }},
                    total: {{ $users->total() }}
                },
                loading: false,
                progress: 0,
                progressInterval: null,
                notification: { show: false, message: '', type: 'success' },
                showAddModal: false,
                selectedRole: 'Input Role User',
                openRole: false,
                confirmType: '',
                confirmActionId: null,
                showConfirmModal: false,

                init() {
                    // Sync URL state on initial load
                    const urlParams = new URLSearchParams(window.location.search);
                    this.tab = urlParams.get('tab') || 'dosen';
                    this.search = urlParams.get('search') || '';
                    this.statusFilter = urlParams.get('status') || '';
                    this.pagination.current_page = parseInt(urlParams.get('page')) || 1;
                },

                get selectedStatusLabel() {
                    return this.statusFilter || 'Status';
                },

                startLoading() {
                    this.loading = true;
                    this.progress = 10;
                    if (this.progressInterval) clearInterval(this.progressInterval);
                    
                    this.progressInterval = setInterval(() => {
                        if (this.progress < 90) {
                            this.progress += Math.random() * 5;
                        }
                    }, 200);
                },

                stopLoading() {
                    if (this.progressInterval) clearInterval(this.progressInterval);
                    this.progress = 100;
                    setTimeout(() => {
                        this.loading = false;
                        this.progress = 0;
                    }, 500);
                },

                async fetchData() {
                    this.startLoading();
                    try {
                        const params = new URLSearchParams({
                            tab: this.tab,
                            search: this.search,
                            status: this.statusFilter,
                            page: this.pagination.current_page
                        });
                        
                        const response = await fetch(`${window.location.pathname}?${params.toString()}`, {
                            headers: { 'X-Requested-With': 'XMLHttpRequest' }
                        });
                        
                        const data = await response.json();
                        this.users = data.users;
                        this.pagination = data.pagination;

                        // Update URL without reload
                        const newUrl = `${window.location.pathname}?${params.toString()}`;
                        window.history.pushState({ path: newUrl }, '', newUrl);
                    } catch (error) {
                        this.showNotification('Gagal memuat data.', 'error');
                    } finally {
                        this.stopLoading();
                    }
                },

                switchTab(newTab) {
                    this.tab = newTab;
                    this.pagination.current_page = 1;
                    this.statusFilter = '';
                    this.fetchData();
                },

                changePage(page) {
                    if (page < 1 || page > this.pagination.last_page) return;
                    this.pagination.current_page = page;
                    this.fetchData();
                    window.scrollTo({ top: 0, behavior: 'smooth' });
                },

                async updateStatus(id, newStatus) {
                    this.startLoading();
                    try {
                        const response = await fetch(`/koordinator/manajemen-akses/${id}/status`, {
                            method: 'PUT',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'X-Requested-With': 'XMLHttpRequest'
                            },
                            body: JSON.stringify({ status: newStatus })
                        });
                        
                        const data = await response.json();
                        if (data.success) {
                            this.showNotification(data.message, 'success');
                            // Update local users array
                            const user = this.users.find(u => u.id === id);
                            if (user) {
                                user.is_aktif = (newStatus === 'Aktif');
                            }
                        } else {
                            this.showNotification('Gagal memperbarui status.', 'error');
                            this.fetchData(); // Reset to server state
                        }
                    } catch (error) {
                        this.showNotification('Terjadi kesalahan koneksi.', 'error');
                        this.fetchData();
                    } finally {
                        this.stopLoading();
                    }
                },

                openAddConfirm() {
                    const form = document.getElementById('formManual');
                    if (form.checkValidity()) {
                        this.confirmType = 'add';
                        this.showConfirmModal = true;
                    } else {
                        form.reportValidity();
                    }
                },

                openDeleteConfirm(id) {
                    this.confirmActionId = id;
                    this.confirmType = 'delete';
                    this.showConfirmModal = true;
                },

                executeConfirm() {
                    if (this.confirmType === 'add') {
                        document.getElementById('formManual').submit();
                    } else if (this.confirmType === 'delete') {
                        document.getElementById('deleteForm-' + this.confirmActionId).submit();
                    }
                    this.showConfirmModal = false;
                },

                showNotification(message, type = 'success') {
                    this.notification = { show: true, message, type };
                    setTimeout(() => { this.notification.show = false; }, 3000);
                },

                formatRole(role) {
                    return role.split('_').map(word => word.charAt(0).toUpperCase() + word.slice(1)).join(' ');
                }
            };
        }
    </script>
</x-dashboard-layout>