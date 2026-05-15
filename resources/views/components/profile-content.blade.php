<div class="w-full max-w-4xl mx-auto" x-data="profileManager()">
    <h1 class="text-2xl font-bold mb-6 text-black hidden">Profil</h1>

    @if(session('error'))
    <div class="mb-6 bg-red-50 border-l-4 border-red-500 p-4 rounded-r-md shadow-sm flex items-start gap-3">
        <svg class="w-6 h-6 text-red-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
        <div>
            <h3 class="font-bold text-red-800 text-sm">Perhatian</h3>
            <p class="text-red-700 text-sm mt-1">{{ session('error') }}</p>
        </div>
    </div>
    @endif

    @if ($errors->any())
    <div class="mb-6 bg-red-50 border-l-4 border-red-500 p-4 rounded-r-md shadow-sm flex items-start gap-3">
        <svg class="w-6 h-6 text-red-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
        <div>
            <h3 class="font-bold text-red-800 text-sm">Gagal Mengupdate Profil</h3>
            <ul class="text-red-700 text-sm mt-1 list-disc list-inside">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    </div>
    @endif
    
    @if(session('success'))
    <div class="mb-6 bg-green-50 border-l-4 border-green-500 p-4 rounded-r-md shadow-sm flex items-start gap-3">
        <svg class="w-6 h-6 text-green-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
        <div>
            <h3 class="font-bold text-green-800 text-sm">Berhasil</h3>
            <p class="text-green-700 text-sm mt-1">{{ session('success') }}</p>
        </div>
    </div>
    @endif

    <!-- Personal Information Card -->
    <div class="bg-white border border-gray-200 rounded-[10px] p-6 md:p-8 flex flex-col md:flex-row justify-between items-center md:items-start mb-6 shadow-sm relative gap-8 md:gap-0">
        <div class="flex flex-col gap-6 w-full md:w-[65%]">
            <h2 class="font-bold text-[16px] text-black border-b border-gray-100 pb-3 flex items-center gap-2 uppercase tracking-tight">
                <svg class="w-5 h-5 text-gray-800" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                Informasi Pribadi
            </h2>
            
            <form action="{{ route('profil.updateInfo') }}" method="POST" id="profile-form" class="flex flex-col gap-5">
                @csrf
                <div class="grid grid-cols-1 sm:grid-cols-[120px_1fr] gap-2 sm:gap-4 items-center">
                    <span class="font-bold text-gray-600 text-[13px] uppercase">Nama</span>
                    <div class="bg-gray-50 border border-gray-200 rounded-[5px] px-4 py-2 font-bold text-black uppercase shadow-inner text-[13px]">
                        {{ $profileData['name'] }}
                    </div>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-[120px_1fr] gap-2 sm:gap-4 items-center">
                    <span class="font-bold text-gray-600 text-[13px] uppercase">{{ $profileData['id_label'] }}</span>
                    <div class="bg-gray-50 border border-gray-200 rounded-[5px] px-4 py-2 font-bold text-black shadow-inner text-[13px]">
                        {{ $profileData['id_value'] }}
                    </div>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-[120px_1fr] gap-2 sm:gap-4 items-center">
                    <span class="font-bold text-gray-600 text-[13px] uppercase">Email</span>
                    <div class="relative w-full">
                        <div x-show="!editMode" class="bg-gray-50 border border-gray-200 rounded-[5px] px-4 py-2 text-black shadow-inner break-all text-[13px]">
                            {{ $profileData['email'] }}
                        </div>
                        <input x-cloak x-show="editMode" type="email" name="email" value="{{ $profileData['email'] }}" class="w-full border border-gray-300 rounded-[5px] px-4 py-2 text-[13px] outline-none focus:border-[#4285F4] focus:ring-1 focus:ring-[#4285F4] transition-all">
                    </div>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-[120px_1fr] gap-2 sm:gap-4 items-center">
                    <span class="font-bold text-gray-600 text-[13px] uppercase">No HP</span>
                    <div class="relative w-full">
                        <div x-show="!editMode" class="bg-gray-50 border border-gray-200 rounded-[5px] px-4 py-2 text-black shadow-inner text-[13px]">
                            {{ $profileData['no_hp'] }}
                        </div>
                        <input x-cloak x-show="editMode" type="text" name="no_hp" value="{{ $profileData['no_hp'] == '-' ? '' : $profileData['no_hp'] }}" class="w-full border border-gray-300 rounded-[5px] px-4 py-2 text-[13px] outline-none focus:border-[#4285F4] focus:ring-1 focus:ring-[#4285F4] transition-all">
                    </div>
                </div>

                <div class="mt-2 flex gap-3">
                    <button type="button" x-show="!editMode" @click="editMode = true" class="bg-white border border-gray-300 shadow-sm text-black px-6 py-2 rounded-[5px] text-[13px] font-bold hover:bg-gray-50 flex items-center gap-2 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                        <span>Edit Kontak</span>
                    </button>
                    <button type="submit" x-cloak x-show="editMode" class="bg-[#34A853] shadow-sm text-white px-6 py-2 rounded-[5px] text-[13px] font-bold hover:bg-green-700 flex items-center gap-2 transition-colors">Simpan Perubahan</button>
                    <button type="button" x-cloak x-show="editMode" @click="editMode = false" class="bg-white border border-gray-300 shadow-sm text-black px-6 py-2 rounded-[5px] text-[13px] font-bold hover:bg-gray-50 flex items-center gap-2 transition-colors">Batal</button>
                </div>
            </form>
        </div>

        <div class="flex flex-col items-center w-full md:w-[30%] relative group">
            <div class="w-[140px] h-[140px] md:w-[160px] md:h-[160px] rounded-full bg-[#E6F0FA] border-4 border-white flex justify-center items-center text-gray-400 overflow-hidden shadow-lg relative">
                @if($user->avatar)
                    <img src="{{ asset('storage/' . $user->avatar) }}" alt="Avatar" class="w-full h-full object-cover">
                @else
                    <svg class="w-[80px] h-[80px]" viewBox="0 0 24 24" fill="currentColor"><path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/></svg>
                @endif
                
                <!-- Hover Upload Overlay -->
                <label for="avatar-upload" class="absolute inset-0 bg-black/50 hidden group-hover:flex items-center justify-center cursor-pointer text-[13px] text-white font-bold transition-opacity">
                    <div class="flex flex-col items-center gap-1">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 16V8a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 11v6m-3-3h6"></path></svg>
                        Ubah Foto
                    </div>
                </label>
            </div>
            <form action="{{ route('profil.updateAvatar') }}" method="POST" enctype="multipart/form-data" id="avatar-form" class="hidden">
                @csrf
                <input type="file" id="avatar-upload" name="avatar" accept="image/*" onchange="document.getElementById('avatar-form').submit()">
            </form>
        </div>
    </div>

    <!-- Tanda Tangan Digital Section -->
    <div class="bg-white border border-gray-200 rounded-[10px] p-6 md:p-8 flex flex-col items-center mb-8 shadow-sm relative">
        <h2 class="font-bold text-[16px] text-black border-b border-gray-100 w-full text-center pb-3 mb-6 uppercase tracking-tight flex items-center justify-center gap-2">
            <svg class="w-5 h-5 text-gray-800" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
            Tanda Tangan Digital
        </h2>
        
        <div class="bg-gray-50 w-full max-w-[400px] h-[160px] border border-gray-300 border-dashed rounded-[10px] flex justify-center items-center mb-5 overflow-hidden relative shadow-inner">
            @if($user->signature_path)
                @php
                    $isBase64 = str_starts_with($user->signature_path, 'data:');
                    $sigSrc = $isBase64 ? $user->signature_path : asset('storage/' . $user->signature_path);
                @endphp
                <img src="{{ $sigSrc }}" alt="Tanda Tangan" class="max-w-[80%] max-h-[80%] object-contain">
            @else
                <div class="flex flex-col items-center text-gray-400 gap-2">
                    <svg class="w-8 h-8 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                    <span class="text-[13px] italic">Belum ada tanda tangan.</span>
                </div>
            @endif
        </div>

        <div class="flex justify-center w-full">
            <button @click="showSigModal = true" type="button" class="bg-[#4285F4] hover:bg-blue-600 text-white shadow-sm px-8 py-2.5 rounded-[5px] font-bold text-[13px] flex items-center gap-2 transition-colors">
                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20h9"></path><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"></path></svg>
                Buat / Ubah Tanda Tangan
            </button>
        </div>
    </div>

    <!-- Actions Section -->
    <div class="flex flex-col sm:flex-row justify-center items-center gap-4 pt-6 border-t border-gray-200 mt-2 mb-4">
        <button type="button" onclick="alert('Fitur Reset Password (Dummy)')" class="w-full sm:w-auto bg-white text-black border border-[#CAC0C0] hover:bg-gray-100 rounded-[25px] px-10 py-2.5 font-bold text-[13px] shadow-sm transition-colors flex items-center justify-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"></path></svg>
            Reset Password
        </button>
        <form action="{{ route('logout') }}" method="POST" class="w-full sm:w-auto m-0">
            @csrf
            <button type="submit" class="w-full bg-[#EA4335] hover:bg-red-700 text-white rounded-[25px] px-10 py-2.5 font-bold text-[13px] shadow-md transition-colors flex items-center justify-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                Sign Out
            </button>
        </form>
    </div>

    <!-- Canvas Modal Overlay -->
    <div x-cloak x-show="showSigModal" class="fixed inset-0 z-[100] flex items-center justify-center bg-black/60 backdrop-blur-sm p-4">
        <div @click.away="showSigModal = false" class="bg-white w-full max-w-2xl rounded-xl shadow-2xl overflow-hidden flex flex-col">
            <div class="px-6 py-4 border-b border-gray-200 bg-gray-50 flex justify-between items-center">
                <h2 class="text-lg font-bold text-gray-800">Buat Tanda Tangan</h2>
                <button @click="showSigModal = false" class="text-gray-400 hover:text-red-500">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
            <div class="p-6 flex flex-col items-center">
                <p class="text-sm text-gray-500 mb-4 text-center">Goreskan tanda tangan Anda pada area di bawah ini menggunakan mouse atau sentuhan layar.</p>
                
                <div class="w-full flex items-center justify-between mb-3 bg-white p-2 rounded border border-gray-200 shadow-sm">
                    <label class="text-sm font-semibold text-gray-700 flex items-center gap-2">
                        <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                        Tebal Garis:
                    </label>
                    <input type="range" id="lineWidth" min="1" max="10" value="3" class="w-[60%] cursor-pointer accent-[#140EBF]">
                </div>

                <div class="border-2 border-gray-300 rounded-lg bg-gray-50 w-full overflow-hidden" style="cursor: crosshair; user-select: none;">
                    <canvas id="signaturePadCanvas" width="600" height="300" class="w-full h-[300px] touch-none"
                        @mousedown="startDraw($event)"
                        @mousemove="draw($event)"
                        @mouseup.window="stopDraw($event)"
                        @touchstart.prevent="startDraw($event)"
                        @touchmove.prevent="draw($event)"
                        @touchend.window="stopDraw($event)"
                        @touchcancel.window="stopDraw($event)">
                    </canvas>
                </div>
            </div>
            <div class="px-6 py-4 border-t border-gray-200 bg-gray-50 flex flex-col gap-3">
                <div x-show="sigErrorMsg" x-transition style="display: none;" class="w-full bg-red-50 border border-red-200 text-red-600 rounded p-2 text-sm text-center font-medium shadow-sm flex items-center justify-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                    <span x-text="sigErrorMsg"></span>
                </div>
                <div class="flex justify-between items-center w-full">
                    <button type="button" @click="clearCanvas()" class="text-red-600 hover:text-red-800 font-semibold text-sm">Hapus / Ulang</button>
                    <div class="flex gap-3">
                        <button type="button" @click="showSigModal = false" class="px-4 py-2 bg-white border border-gray-300 rounded text-sm font-semibold hover:bg-gray-50">Batal</button>
                        <form action="{{ route('profil.updateSignatureDraw') }}" method="POST" id="draw-form">
                            @csrf
                            <input type="hidden" name="signature_base64" id="signature_base64">
                            <button type="button" @click="saveCanvas()" class="px-4 py-2 bg-blue-600 text-white rounded text-sm font-semibold hover:bg-blue-700 shadow-sm transition-colors">Simpan</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Simple Canvas Drawing Script -->
<script>
window.profileManager = function() {
    return {
        editMode: false,
        showSigModal: false,
        sigErrorMsg: '',
        isDrawing: false,
        lastX: 0,
        lastY: 0,
        ctx: null,

        init() {
            this.$watch('showSigModal', value => {
                if(value) {
                    setTimeout(() => {
                        this.setupCanvas();
                    }, 50);
                } else {
                    this.isDrawing = false;
                }
            });
        },

        setupCanvas() {
            const canvas = document.getElementById('signaturePadCanvas');
            if(!canvas) return;
            this.ctx = canvas.getContext('2d');
        },

        getCursorPos(e) {
            const canvas = document.getElementById('signaturePadCanvas');
            const rect = canvas.getBoundingClientRect();
            const scaleX = canvas.width / rect.width;
            const scaleY = canvas.height / rect.height;
            
            let clientX = e.clientX;
            let clientY = e.clientY;

            if (e.touches && e.touches.length > 0) {
                clientX = e.touches[0].clientX;
                clientY = e.touches[0].clientY;
            } else if(e.changedTouches && e.changedTouches.length > 0) {
                clientX = e.changedTouches[0].clientX;
                clientY = e.changedTouches[0].clientY;
            }

            return {
                x: (clientX - rect.left) * scaleX,
                y: (clientY - rect.top) * scaleY
            };
        },

        startDraw(e) {
            if(!this.ctx) this.setupCanvas();
            this.isDrawing = true;
            this.ctx.lineCap = 'round';
            this.ctx.lineJoin = 'round';
            this.ctx.strokeStyle = '#000000';
            const lwInput = document.getElementById('lineWidth');
            this.ctx.lineWidth = lwInput ? parseInt(lwInput.value) : 3;

            const pos = this.getCursorPos(e);
            this.lastX = pos.x;
            this.lastY = pos.y;
        },

        draw(e) {
            if (!this.isDrawing || !this.ctx) return;
            const pos = this.getCursorPos(e);
            
            this.ctx.beginPath();
            this.ctx.moveTo(this.lastX, this.lastY);
            this.ctx.lineTo(pos.x, pos.y);
            this.ctx.stroke();
            
            this.lastX = pos.x;
            this.lastY = pos.y;
        },

        stopDraw(e) {
            this.isDrawing = false;
        },

        clearCanvas() {
            const canvas = document.getElementById('signaturePadCanvas');
            if(canvas && this.ctx) {
                this.ctx.clearRect(0, 0, canvas.width, canvas.height);
            }
        },

        saveCanvas() {
            const canvas = document.getElementById('signaturePadCanvas');
            if(!canvas) return;

            const dataURL = canvas.toDataURL('image/png');
            
            const blank = document.createElement('canvas');
            blank.width = canvas.width;
            blank.height = canvas.height;
            
            if(dataURL === blank.toDataURL('image/png')) {
                this.sigErrorMsg = 'Kanvas tanda tangan Anda masih kosong! Silakan isi terlebih dahulu.';
                setTimeout(() => { this.sigErrorMsg = ''; }, 3000);
                return;
            }
            
            document.getElementById('signature_base64').value = dataURL;
            document.getElementById('draw-form').submit();
        }
    }
}
</script>
