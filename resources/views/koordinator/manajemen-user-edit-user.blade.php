<x-dashboard-layout header="Edit User" userName="{{ auth()->user()->name ?? 'Koordinator' }}" roleName="KOORDINATOR KP">
    <x-slot:sidebar>
        @include('koordinator.components.sidebar', ['active' => 'manajemen-akses'])
    </x-slot>

    <div class="mt-8 px-4 w-full max-w-[900px] mx-auto pb-12 font-inter" x-data="{
        showConfirmModal: false,
        selectedRole: '{{ ucwords(str_replace('_', ' ', $user->role)) }}',
        openRole: false,
        openConfirm() {
            this.showConfirmModal = true;
        },
        executeConfirm() {
            document.getElementById('editForm').submit();
        }
    }">

        <!-- Error/Success alerts -->
        @if(session('error') || $errors->any())
            <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg relative mb-6 shadow-sm">
                <p class="text-sm font-medium">{{ session('error') ?? $errors->first() }}</p>
            </div>
        @endif

        <div class="bg-white border border-gray-200 rounded-[10px] w-full p-8 shadow-sm relative min-h-[350px]">
            <h3 class="text-[16px] font-bold text-gray-800 mb-8 border-b border-gray-100 pb-3">Informasi {{ ucwords(str_replace('_', ' ', $user->role)) }}</h3>
            
            <form action="{{ route('koordinator.user.update', $user->id) }}" method="POST" id="editForm">
                @csrf
                @method('PUT')

                <div class="space-y-5">
                    <div class="flex items-center">
                        <div class="w-[150px] text-[14px] text-gray-600 font-medium">ID</div>
                        <div class="w-[20px] text-[14px] text-gray-600">:</div>
                        <div class="text-[14px] text-gray-900 font-bold bg-gray-50 px-3 py-1.5 rounded border border-gray-200 min-w-[200px]">{{ $detail->nim ?? $detail->nidn ?? '123456789' }}</div>
                    </div>

                    <div class="flex items-center">
                        <div class="w-[150px] text-[14px] text-gray-600 font-medium">Nama</div>
                        <div class="w-[20px] text-[14px] text-gray-600">:</div>
                        <input type="text" name="name" value="{{ $user->name }}" required class="w-[300px] h-[34px] bg-white border border-gray-300 px-3 text-[14px] text-gray-900 outline-none rounded focus:ring-1 focus:ring-blue-500 focus:border-blue-500 transition-all">
                    </div>

                    @if(in_array($user->role, ['dosen', 'koordinator_kp']))
                    <div class="flex items-center">
                        <div class="w-[150px] text-[14px] text-gray-600 font-medium">Status</div>
                        <div class="w-[20px] text-[14px] text-gray-600">:</div>
                        <div class="relative w-[150px]">
                            <select name="status" class="w-full h-[34px] bg-white border border-gray-300 px-3 text-[14px] text-gray-900 outline-none rounded appearance-none cursor-pointer focus:ring-1 focus:ring-blue-500 focus:border-blue-500 transition-all font-medium">
                                <option value="Aktif" {{ ($detail->is_aktif ?? true) ? 'selected' : '' }}>Aktif</option>
                                <option value="Nonaktif" {{ !($detail->is_aktif ?? true) ? 'selected' : '' }}>Tidak Aktif</option>
                            </select>
                            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-gray-500">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                            </div>
                        </div>
                    </div>
                    @endif

                    <div class="flex items-center">
                        <div class="w-[150px] text-[14px] text-gray-600 font-medium">Email</div>
                        <div class="w-[20px] text-[14px] text-gray-600">:</div>
                        <input type="email" name="email" value="{{ $user->email }}" required class="w-[300px] h-[34px] bg-white border border-gray-300 px-3 text-[14px] text-gray-900 outline-none rounded focus:ring-1 focus:ring-blue-500 focus:border-blue-500 transition-all">
                    </div>

                    <div class="flex items-start pb-4">
                        <div class="w-[150px] text-[14px] text-gray-600 font-medium mt-1.5">Role</div>
                        <div class="w-[20px] text-[14px] text-gray-600 mt-1.5">:</div>
                        
                        <div class="relative w-[200px]" x-data="{ openRole: false }">
                            <button @click="openRole = !openRole" @click.away="openRole = false" type="button" class="w-full h-[34px] bg-white border border-gray-300 px-3 text-[14px] text-gray-900 font-medium outline-none rounded flex items-center justify-between cursor-pointer focus:ring-1 focus:ring-blue-500 transition-all">
                                <span x-text="selectedRole"></span>
                                <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                            </button>
                            <div x-show="openRole" class="absolute z-10 w-full mt-1 bg-white border border-gray-300 rounded shadow-lg overflow-hidden" style="display: none;" x-transition>
                                <div @click="selectedRole = 'Dosen'; openRole = false" class="px-3 py-2 text-[13px] cursor-pointer hover:bg-blue-50 hover:text-blue-700 transition-colors">Dosen</div>
                                <div @click="selectedRole = 'Koordinator KP'; openRole = false" class="px-3 py-2 text-[13px] cursor-pointer hover:bg-blue-50 hover:text-blue-700 transition-colors">Koordinator KP</div>
                                <div @click="selectedRole = 'Mahasiswa'; openRole = false" class="px-3 py-2 text-[13px] cursor-pointer hover:bg-blue-50 hover:text-blue-700 transition-colors">Mahasiswa</div>
                            </div>
                            <input type="hidden" name="role" :value="selectedRole === 'Koordinator KP' ? 'koordinator_kp' : selectedRole.toLowerCase()">
                        </div>
                    </div>
                </div>

                <div class="absolute bottom-6 right-6 flex items-center gap-4">
                    <a href="{{ route('koordinator.manajemen-akses') }}" class="h-[28px] px-4 bg-gray-500 hover:bg-gray-600 rounded-[5px] flex items-center justify-center text-white text-[13px] font-bold shadow-sm transition-colors">
                        Kembali
                    </a>
                    <button type="button" @click="openConfirm()" class="h-[28px] px-4 bg-[#3E8B3E] hover:bg-[#347834] rounded-[5px] flex items-center justify-center gap-1.5 text-white text-[13px] font-bold shadow-sm transition-colors">
                        <div class="w-4 h-4 bg-white rounded-full flex items-center justify-center">
                            <svg class="w-3 h-3 text-[#3E8B3E]" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path></svg>
                        </div>
                        Sahkan
                    </button>
                </div>
            </form>
        </div>

        <!-- Custom Global Confirm Modal (2-Step Verification) -->
        <div x-show="showConfirmModal" style="display: none;" class="fixed inset-0 z-[110] flex items-center justify-center bg-black/40 backdrop-blur-sm p-4">
            <div @click.away="showConfirmModal = false" class="bg-white rounded-[10px] w-full max-w-[450px] p-8 shadow-2xl flex flex-col items-center justify-center text-center transform transition-all">
                
                <!-- Icon -->
                <div class="mb-5">
                    <svg class="w-16 h-16 text-[#4CAF50]" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                    </svg>
                </div>

                <!-- Message -->
                <h3 class="text-black font-semibold text-[16px] mb-8">Apakah Anda yakin ingin mengesahkannya?</h3>

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
