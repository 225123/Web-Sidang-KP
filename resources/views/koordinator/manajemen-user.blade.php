<x-dashboard-layout header="Manajemen User" userName="{{ auth()->user()->name ?? 'Koordinator' }}" roleName="KOORDINATOR KP">
    <x-slot:sidebar>
        @include('koordinator.components.sidebar', ['active' => 'manajemen-akses'])
    </x-slot>

    <!-- Header Actions (Periode Box sama persis Dashboard) -->
        

    <!-- Shared Alpine State -->
    <div class="mt-6" x-data="userManager()" x-init="init()">
        
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

        <!-- Notification -->
        <div x-show="notification.show" x-transition 
            :class="notification.type === 'success' ? 'bg-green-50 border-green-200 text-green-700' : 'bg-red-50 border-red-200 text-red-700'"
            class="border px-4 py-3 rounded-lg relative mb-6 shadow-sm flex items-center gap-3" style="display: none;">
            <svg x-show="notification.type === 'success'" class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
            <svg x-show="notification.type === 'error'" class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            <span class="text-sm font-medium" x-text="notification.message"></span>
        </div>

        <div class="bg-white rounded-[15px] border border-gray-200 shadow-sm overflow-hidden p-6 mb-8">
            <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-6 mb-6">
                <div>
                    <h3 class="text-[18px] font-bold text-black tracking-tight">Daftar Pengguna</h3>
                    <p class="text-[12px] text-black/60 font-medium mt-1">Kelola data dosen dan mahasiswa dalam sistem.</p>
                </div>
                
                <div class="flex flex-wrap items-center gap-3 w-full lg:w-auto">
                    <button @click="showAddModal = true" class="w-full sm:w-auto bg-[#4CAF50] hover:bg-[#45a049] text-white font-bold text-[12px] px-4 py-2.5 rounded-[5px] shadow-sm transition-colors whitespace-nowrap shrink-0 flex items-center justify-center gap-1.5">
                        <span class="text-[14px] font-bold leading-none">+</span> Tambah
                    </button>

                    <div class="relative flex-1 w-full sm:w-[250px]">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        </div>
                        <input type="text" x-model="search" @input.debounce.500ms="fetchData()" class="block w-full pl-9 pr-4 py-2 border border-gray-300 rounded-[5px] text-[12px] text-black focus:ring-[#4285F4] shadow-sm" placeholder="Cari Nama...">
                    </div>

                    <template x-if="tab === 'dosen'">
                        <div x-data="{ openStatus: false }" class="relative w-full sm:w-[150px] z-[60]" @click.outside="openStatus = false">
                            <button type="button" @click="openStatus = !openStatus" class="w-full text-[12px] border border-gray-300 rounded-[5px] py-2 px-3 bg-white text-black font-medium focus:ring-[#4285F4] flex justify-between items-center text-left shadow-sm">
                                <span class="truncate" x-text="selectedStatusLabel"></span>
                                <svg :class="openStatus ? 'rotate-0' : 'rotate-90'" class="w-3.5 h-3.5 transition-all duration-200 text-gray-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                            </button>
                            <div x-show="openStatus" x-transition x-cloak class="absolute w-full mt-1 bg-white border border-gray-300 rounded-[5px] shadow-lg overflow-hidden py-1 z-50">
                                <label class="block px-3 py-2 text-[12px] hover:bg-gray-100 cursor-pointer text-black"><input type="radio" value="" x-model="statusFilter" class="hidden" @change="openStatus = false; fetchData()">Semua Status</label>
                                <label class="block px-3 py-2 text-[12px] hover:bg-gray-100 cursor-pointer text-black"><input type="radio" value="Aktif" x-model="statusFilter" class="hidden" @change="openStatus = false; fetchData()">Aktif</label>
                                <label class="block px-3 py-2 text-[12px] hover:bg-gray-100 cursor-pointer text-black"><input type="radio" value="Tidak Aktif" x-model="statusFilter" class="hidden" @change="openStatus = false; fetchData()">Tidak Aktif</label>
                            </div>
                        </div>
                    </template>

                    <template x-if="tab === 'mahasiswa'">
                        <div x-data="{ openStatusMhs: false }" class="relative w-full sm:w-[150px] z-[60]" @click.outside="openStatusMhs = false">
                            <button type="button" @click="openStatusMhs = !openStatusMhs" class="w-full text-[12px] border border-gray-300 rounded-[5px] py-2 px-3 bg-white text-black font-medium focus:ring-[#4285F4] flex justify-between items-center text-left shadow-sm">
                                <span class="truncate" x-text="selectedStatusMahasiswaLabel"></span>
                                <svg :class="openStatusMhs ? 'rotate-0' : 'rotate-90'" class="w-3.5 h-3.5 transition-all duration-200 text-gray-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                            </button>
                            <div x-show="openStatusMhs" x-transition x-cloak class="absolute w-full mt-1 bg-white border border-gray-300 rounded-[5px] shadow-lg overflow-hidden py-1 z-50">
                                <label class="block px-3 py-2 text-[12px] hover:bg-gray-100 cursor-pointer text-black"><input type="radio" value="" x-model="statusMahasiswaFilter" class="hidden" @change="openStatusMhs = false; fetchData()">Semua Status</label>
                                <label class="block px-3 py-2 text-[12px] hover:bg-gray-100 cursor-pointer text-black"><input type="radio" value="baru" x-model="statusMahasiswaFilter" class="hidden" @change="openStatusMhs = false; fetchData()">Baru</label>
                                <label class="block px-3 py-2 text-[12px] hover:bg-gray-100 cursor-pointer text-black"><input type="radio" value="lanjut" x-model="statusMahasiswaFilter" class="hidden" @change="openStatusMhs = false; fetchData()">Lanjut</label>
                            </div>
                        </div>
                    </template>

                    <!-- Export PDF Dropdown -->
                    <div x-data="{ openExport: false }" class="relative w-full sm:w-[150px] z-[50]" @click.outside="openExport = false">
                        <button type="button" @click="openExport = !openExport" class="w-full text-[12px] border border-red-600 bg-[#E32727] text-white rounded-[5px] py-2 px-3 font-medium flex justify-between items-center text-left shadow-sm hover:bg-red-700 transition-colors">
                            <span class="flex items-center gap-1.5"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg> Export PDF</span>
                            <svg :class="openExport ? 'rotate-0' : 'rotate-90'" class="w-3.5 h-3.5 transition-all duration-200 text-white flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <div x-show="openExport" x-transition x-cloak class="absolute w-full mt-1 bg-white border border-gray-300 rounded-[5px] shadow-lg overflow-hidden py-1 z-50">
                            <a href="{{ route('koordinator.user.export-pdf', ['type' => 'semua']) }}" target="_blank" class="block px-3 py-2 text-[12px] hover:bg-gray-100 text-black">Export Semua User</a>
                            <a href="{{ route('koordinator.user.export-pdf', ['type' => 'dosen']) }}" target="_blank" class="block px-3 py-2 text-[12px] hover:bg-gray-100 text-black">Export Data Dosen</a>
                            <a href="{{ route('koordinator.user.export-pdf', ['type' => 'mahasiswa']) }}" target="_blank" class="block px-3 py-2 text-[12px] hover:bg-gray-100 text-black">Export Data Mahasiswa</a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modern Tabs -->
            <div class="flex items-center gap-4 border-b border-gray-200 mb-6">
                <button @click="switchTab('dosen')"
                        class="px-4 py-3 text-[14px] font-bold transition-all border-b-2"
                        :class="tab === 'dosen' ? 'border-[#4285F4] text-[#4285F4]' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'">
                    Dosen
                </button>
                <button @click="switchTab('mahasiswa')"
                        class="px-4 py-3 text-[14px] font-bold transition-all border-b-2"
                        :class="tab === 'mahasiswa' ? 'border-[#4285F4] text-[#4285F4]' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'">
                    Mahasiswa
                </button>
            </div>

            <!-- Table Container -->
            <div :class="loading ? 'opacity-60 pointer-events-none' : ''" class="transition-opacity duration-300">
                <div class="border border-gray-200 rounded-[10px] overflow-hidden">
                    <div class="overflow-x-auto custom-scrollbar">
                        <table class="w-full text-left text-[12px] font-medium text-black border-collapse min-w-[800px]">
                            <thead class="bg-[#EBEBEB]">
                                <tr class="h-[45px] text-black">
                                    <th class="border-b border-r border-gray-300 font-bold px-4 text-center w-[50px]">No</th>
                                    <th class="border-b border-r border-gray-300 font-bold px-4">Nama</th>
                                    <th class="border-b border-r border-gray-300 font-bold px-4">ID</th>
                                    <template x-if="tab === 'dosen'">
                                        <th class="border-b border-r border-gray-300 font-bold px-4">Role</th>
                                    </template>
                                    <th class="border-b border-r border-gray-300 font-bold px-4 text-left">Email</th>
                                    <template x-if="tab === 'dosen'">
                                        <th class="border-b border-r border-gray-300 font-bold px-4 text-center w-[120px]">Status</th>
                                    </template>
                                    <template x-if="tab === 'mahasiswa'">
                                        <th class="border-b border-r border-gray-300 font-bold px-4 text-center w-[120px]">Status</th>
                                    </template>
                                    <template x-if="tab === 'mahasiswa'">
                                        <th class="border-b border-r border-gray-300 font-bold px-4 text-center w-[100px]">Keterangan</th>
                                    </template>
                                    <th class="border-b border-gray-300 font-bold px-4 text-center w-[100px]">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 bg-white">
                                <template x-for="(user, index) in users" :key="user.id">
                                    <tr class="h-[45px] hover:bg-gray-50 transition-colors">
                                        <td class="border-r border-gray-200 text-center text-gray-500" x-text="pagination.from + index"></td>
                                        <td class="border-r border-gray-200 text-left px-4 font-bold" x-text="user.name"></td>
                                        <td class="border-r border-gray-200 px-4" x-text="user.identifier_id || '-'"></td>
                                        <template x-if="tab === 'dosen'">
                                            <td class="border-r border-gray-200 px-4" x-text="formatRole(user.role)"></td>
                                        </template>
                                        <td class="border-r border-gray-200 px-4 text-left break-all text-gray-600" x-text="user.email"></td>
                                        
                                        <template x-if="tab === 'dosen'">
                                            <td class="border-r border-gray-200 px-4 text-center">
                                                <select @change="updateStatus(user.id, $event.target.value)" 
                                                    class="w-full bg-transparent border-none outline-none cursor-pointer text-center focus:ring-0 font-bold text-[11px] uppercase tracking-wide"
                                                    :class="(user.is_aktif == 1 || user.is_aktif === true) ? 'text-green-600' : 'text-red-500'">
                                                    <option class="text-green-600 font-normal" value="Aktif" :selected="user.is_aktif == 1 || user.is_aktif === true">Aktif</option>
                                                    <option class="text-red-500 font-normal" value="Nonaktif" :selected="user.is_aktif == 0 || user.is_aktif === false">Tidak Aktif</option>
                                                </select>
                                            </td>
                                        </template>

                                        <template x-if="tab === 'mahasiswa'">
                                            <td class="border-r border-gray-200 px-4 text-center">
                                                <select @change="updateStatus(user.id, $event.target.value)" 
                                                    class="w-full bg-transparent border-none outline-none cursor-pointer text-center focus:ring-0 font-bold text-[11px] uppercase tracking-wide"
                                                    :class="(user.is_aktif == 1 || user.is_aktif === true) ? 'text-green-600' : 'text-red-500'">
                                                    <option class="text-green-600 font-normal" value="Aktif" :selected="user.is_aktif == 1 || user.is_aktif === true">Aktif</option>
                                                    <option class="text-red-500 font-normal" value="Nonaktif" :selected="user.is_aktif == 0 || user.is_aktif === false">Tidak Aktif</option>
                                                </select>
                                            </td>
                                        </template>

                                        <template x-if="tab === 'mahasiswa'">
                                            <td class="border-r border-gray-200 px-4 text-left">
                                                <span
                                                    class="text-[12px] font-medium text-black"
                                                    x-text="user.status_mahasiswa === 'lanjut' ? 'Lanjut' : 'Baru'"
                                                ></span>
                                            </td>
                                        </template>

                                        <td class="px-4 text-center">
                                            <div class="flex items-center justify-center gap-3">
                                                <a :href="'/koordinator/manajemen-akses/' + user.id + '/edit'" class="text-gray-500 hover:text-[#4285F4] hover:bg-blue-50 p-1.5 rounded transition-colors" title="Edit Data">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                </template>
                                <template x-if="users.length === 0">
                                    <tr>
                                        <td :colspan="tab === 'dosen' ? 7 : 7" class="py-12 text-gray-400 text-center font-medium italic tracking-widest uppercase bg-gray-50">Tidak ada data ditemukan</td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                </div>
                
                <!-- Pagination -->
                <div class="pt-4 border-t border-gray-200 flex flex-col sm:flex-row items-center justify-between gap-4 mt-6" x-show="pagination.last_page > 1">
                    <span class="text-[12px] font-medium text-black/50" x-text="pagination.from + ' - ' + pagination.to + ' dari ' + pagination.total + ' baris'"></span>
                    <div class="flex items-center gap-2">
                        <button @click="changePage(pagination.current_page - 1)" 
                            :disabled="pagination.current_page === 1"
                            class="px-3 py-1 border border-gray-300 rounded text-[12px] hover:bg-gray-50 disabled:opacity-30 disabled:cursor-not-allowed transition-colors">
                            Previous
                        </button>
                        
                        <div class="flex items-center gap-1">
                            <template x-for="p in pagination.last_page" :key="p">
                                <button @click="changePage(p)"
                                    :class="pagination.current_page === p ? 'bg-blue-600 text-white shadow-md' : 'text-black hover:bg-gray-100'"
                                    class="w-8 h-8 rounded text-[12px] font-bold transition-all"
                                    x-text="p"></button>
                            </template>
                        </div>

                        <button @click="changePage(pagination.current_page + 1)" 
                            :disabled="pagination.current_page === pagination.last_page"
                            class="px-3 py-1 border border-gray-300 rounded text-[12px] hover:bg-gray-50 disabled:opacity-30 disabled:cursor-not-allowed transition-colors">
                            Next
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tambah User Modal -->
        <template x-teleport="body">
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

                    <!-- Validation Error Alerts (Indonesian) -->
                    @if($errors->any())
                    <div class="bg-red-50 border border-red-300 rounded-lg p-3 mb-6">
                        <div class="flex items-start gap-2">
                            <svg class="w-5 h-5 text-red-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            <p class="text-sm font-bold text-red-700">Terdapat kesalahan pada isian form:</p>
                        </div>
                        <ul class="list-disc ml-8 mt-1 text-xs text-red-600">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif
                    
                    <form action="{{ route('koordinator.user.store') }}" method="POST" id="formManual">
                        @csrf
                        <div class="space-y-4 max-w-[600px] mb-12">
                            <!-- ID -->
                            <div class="flex flex-col md:flex-row md:items-center">
                                <label class="w-full md:w-[200px] text-[15px] text-black font-medium mb-1 md:mb-0">ID (NIM/NIDN/NIDK)</label>
                                <span class="hidden md:inline text-black mx-4">:</span>
                                <input type="text" name="id_user" x-model="formData.id_user" @blur="checkIdUser" required class="flex-1 w-full md:max-w-[300px] h-[32px] bg-[#D9D9D9] px-3 font-italic text-[14px] text-black outline-none focus:ring-1 focus:ring-blue-500" placeholder="Input ID User">
                            </div>
                            <div x-show="isDosenDuplicate" class="text-red-600 text-[13px] md:ml-[230px] font-bold mt-1">ID ini sudah terdaftar sebagai Dosen/Koordinator. Duplikasi ditolak.</div>
                            <div x-show="isCheckingId" class="text-blue-500 text-[12px] md:ml-[230px] mt-1">Mengecek ID...</div>

                            <!-- Banner: User ditemukan dari periode sebelumnya (Layak) -->
                            <div x-show="isExistingUser && !isDosenDuplicate && !isNotAllowed" x-cloak
                                 class="md:ml-[230px] flex items-start gap-2 bg-amber-50 border border-amber-300 rounded-lg px-3 py-2.5 text-[12.5px] text-amber-800">
                                <svg class="w-4 h-4 text-amber-500 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m0-6v2m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                </svg>
                                <span><strong>User ditemukan dari periode sebelumnya (Mengulang).</strong> Data nama, email, dan role diisi otomatis. Status mahasiswa akan dialihkan menjadi "Lanjut".</span>
                            </div>

                            <!-- Banner: User Ditolak (Sudah Lulus) -->
                            <div x-show="isExistingUser && !isDosenDuplicate && isNotAllowed" x-cloak
                                 class="md:ml-[230px] flex items-start gap-2 bg-red-50 border border-red-300 rounded-lg px-3 py-2.5 text-[12.5px] text-red-800 mt-2">
                                <svg class="w-4 h-4 text-red-500 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <span><strong x-text="notAllowedMessage"></strong> Penambahan pengguna diblokir.</span>
                            </div>

                            <!-- Nama -->
                            <div class="flex flex-col md:flex-row md:items-center">
                                <label class="w-full md:w-[200px] text-[15px] text-black font-medium mb-1 md:mb-0">Nama Lengkap</label>
                                <span class="hidden md:inline text-black mx-4">:</span>
                                <div class="relative flex-1 w-full md:max-w-[300px]">
                                    <input type="text" name="name" x-model="formData.name" required
                                           :readonly="isExistingUser"
                                           :class="isExistingUser ? 'bg-gray-200 text-gray-500 cursor-not-allowed select-none' : 'bg-[#D9D9D9] focus:ring-1 focus:ring-blue-500'"
                                           class="w-full h-[32px] px-3 font-italic text-[14px] text-black outline-none pr-8"
                                           placeholder="Ketik nama...">
                                    <svg x-show="isExistingUser" class="absolute right-2 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                    </svg>
                                </div>
                            </div>

                            <!-- Email -->
                            <div class="flex flex-col md:flex-row md:items-center">
                                <label class="w-full md:w-[200px] text-[15px] text-black font-medium mb-1 md:mb-0">Email Utama</label>
                                <span class="hidden md:inline text-black mx-4">:</span>
                                <div class="relative flex-1 w-full md:max-w-[300px]">
                                    <input type="email" name="email" x-model="formData.email" required
                                           :readonly="isExistingUser"
                                           :class="isExistingUser ? 'bg-gray-200 text-gray-500 cursor-not-allowed select-none' : 'bg-[#D9D9D9] focus:ring-1 focus:ring-blue-500'"
                                           class="w-full h-[32px] px-3 font-italic text-[14px] text-black outline-none pr-8"
                                           placeholder="Input Email User">
                                    <svg x-show="isExistingUser" class="absolute right-2 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                    </svg>
                                </div>
                            </div>

                            <!-- Role Dropdown -->
                            <div class="flex flex-col md:flex-row md:items-start">
                                <label class="w-full md:w-[200px] text-[15px] text-black md:mt-1 font-medium mb-1 md:mb-0">Role System</label>
                                <span class="hidden md:inline text-black mx-4 mt-1">:</span>
                                <div class="relative flex-1 w-full md:max-w-[300px]">
                                    <!-- Locked display (readonly state) -->
                                    <template x-if="isExistingUser">
                                        <div class="w-full h-[32px] bg-gray-200 px-3 pr-8 flex items-center justify-between cursor-not-allowed">
                                            <span x-text="selectedRole" class="text-[14px] text-gray-500 font-medium"></span>
                                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                            </svg>
                                        </div>
                                    </template>
                                    <!-- Editable dropdown -->
                                    <template x-if="!isExistingUser">
                                        <button @click="openRole = !openRole" type="button" class="w-full h-[32px] bg-[#D9D9D9] px-3 flex items-center justify-between outline-none focus:ring-1 focus:ring-blue-500">
                                            <span x-text="selectedRole" :class="selectedRole === 'Input Role User' ? 'text-black/50 italic text-[14px]' : 'text-black text-[14px] font-medium'"></span>
                                            <span class="text-black/70 font-bold transform rotate-90" :class="openRole ? '-rotate-90' : ''">&gt;</span>
                                        </button>
                                    </template>

                                    <!-- Dropdown Menu -->
                                    <div x-show="openRole && !isExistingUser" @click.away="openRole = false" class="absolute z-10 w-full mt-1 bg-[#E4E3E3] shadow-md border border-gray-300">
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
                                <button type="button" @click="openAddConfirm()" :disabled="isNotAllowed || isDosenDuplicate" :class="(isNotAllowed || isDosenDuplicate) ? 'opacity-50 cursor-not-allowed' : 'hover:bg-green-700'" class="flex-1 sm:flex-none w-full sm:w-[104px] h-[32px] bg-[#008000] text-white font-medium text-[14px] rounded-[5px] transition-colors shadow-sm focus:outline-none">
                                    Kirim
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </template>

        <form id="importForm" action="{{ route('koordinator.user.import') }}" method="POST" enctype="multipart/form-data" class="hidden">
            @csrf
        </form>

        <!-- Custom Global Confirm Modal -->
        <template x-teleport="body">
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
                    <button @click="executeConfirm()" :disabled="confirmType === 'add' && (isDosenDuplicate || isNotAllowed)" type="button" :class="confirmType === 'add' && (isDosenDuplicate || isNotAllowed) ? 'opacity-50 cursor-not-allowed bg-gray-400' : 'bg-[#456DA7] hover:bg-blue-700 shadow-sm'" class="w-[100px] h-[34px] text-white rounded-[5px] text-[14px] font-medium transition-colors">
                        Iya
                    </button>
                </div>
            </div>
        </div>
        </template>

    </div>

    <script>
        function userManager() {
            return {
                users: @json($users->items()),
                tab: '{{ $tab }}',
                search: '{{ request('search') }}',
                statusFilter: '{{ request('status') }}',
                statusMahasiswaFilter: '{{ request('status_mahasiswa') }}',
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
                showAddModal: {{ $errors->any() ? 'true' : 'false' }},
                selectedRole: 'Input Role User',
                openRole: false,
                confirmType: '',
                confirmActionId: null,
                showConfirmModal: false,
                formData: {
                    id_user: '{{ old("id_user") }}',
                    name: '{{ old("name") }}',
                    email: '{{ old("email") }}',
                    role: '{{ old("role") ? (old("role") == "koordinator_kp" ? "Koordinator KP" : ucfirst(old("role"))) : "Input Role User" }}'
                },
                isCheckingId: false,
                isDosenDuplicate: false,
                isExistingUser: false,
                isNotAllowed: false,
                notAllowedMessage: '',

                async checkIdUser() {
                    if (!this.formData.id_user) {
                        this.isDosenDuplicate = false;
                        this.isExistingUser = false;
                        this.isNotAllowed = false;
                        return;
                    }
                    
                    this.isCheckingId = true;
                    try {
                        const response = await fetch(`{{ route('koordinator.user.check-id') }}?id_user=${this.formData.id_user}`);
                        const data = await response.json();
                        
                        if (data.exists) {
                            // Auto-fill fields from server data
                            this.formData.name  = data.name;
                            this.formData.email = data.email;
                            this.selectedRole   = data.role;
                            
                            if (data.role_type === 'dosen') {
                                // Dosen/Koordinator: duplikat ditolak, tapi field tetap bisa dibaca
                                this.isDosenDuplicate = true;
                                this.isExistingUser   = false;
                                this.isNotAllowed     = false;
                            } else {
                                // Mahasiswa dari periode sebelumnya: field dikunci
                                this.isDosenDuplicate = false;
                                this.isExistingUser   = true;
                                this.isNotAllowed     = data.not_allowed || false;
                                this.notAllowedMessage = data.not_allowed_message || '';
                            }
                        } else {
                            // ID tidak ditemukan: semua state direset, field bisa diisi bebas
                            this.isDosenDuplicate = false;
                            this.isExistingUser   = false;
                            this.isNotAllowed     = false;
                            this.notAllowedMessage = '';
                        }
                    } catch (error) {
                        console.error('Error checking ID:', error);
                    } finally {
                        this.isCheckingId = false;
                    }
                },

                init() {
                    // Sync URL state on initial load
                    const urlParams = new URLSearchParams(window.location.search);
                    this.tab = urlParams.get('tab') || 'dosen';
                    this.search = urlParams.get('search') || '';
                    this.statusFilter = urlParams.get('status') || '';
                    this.statusMahasiswaFilter = urlParams.get('status_mahasiswa') || '';
                    this.pagination.current_page = parseInt(urlParams.get('page')) || 1;

                    // Handle session messages
                    @if(session('success'))
                    this.notification = { show: true, message: '{{ session('success') }}', type: 'success' };
                    setTimeout(() => this.notification.show = false, 3000);
                    @endif

                    @if(session('error'))
                    this.notification = { show: true, message: '{{ session('error') }}', type: 'error' };
                    setTimeout(() => this.notification.show = false, 5000);
                    @endif

                    @if($errors->any())
                    this.notification = { show: true, message: 'Gagal menambahkan user! Silakan periksa kembali form untuk melihat detail error.', type: 'error' };
                    setTimeout(() => this.notification.show = false, 6000);
                    @endif
                },

                get selectedStatusLabel() {
                    return this.statusFilter || 'Status';
                },

                get selectedStatusMahasiswaLabel() {
                    if (this.statusMahasiswaFilter === 'baru') return 'Baru';
                    if (this.statusMahasiswaFilter === 'lanjut') return 'Lanjut';
                    return 'Status';
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
                            status_mahasiswa: this.statusMahasiswaFilter,
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
                    this.statusMahasiswaFilter = '';
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

                async executeConfirm() {
                    if (this.confirmType === 'add') {
                        document.getElementById('formManual').submit();
                    } else if (this.confirmType === 'delete') {
                        this.startLoading();
                        try {
                            const response = await fetch(`/koordinator/manajemen-akses/${this.confirmActionId}/destroy`, {
                                method: 'DELETE',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                    'X-Requested-With': 'XMLHttpRequest'
                                }
                            });
                            
                            const data = await response.json();
                            if (data.success) {
                                this.showNotification(data.message, 'success');
                                this.fetchData();
                            } else {
                                this.showNotification(data.message || 'Gagal menghapus user.', 'error');
                            }
                        } catch (error) {
                            this.showNotification('Terjadi kesalahan koneksi.', 'error');
                        } finally {
                            this.stopLoading();
                        }
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