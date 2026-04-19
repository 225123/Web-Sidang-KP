<div class="bg-white rounded-lg p-6 shadow-sm max-w-4xl mx-auto" x-data="profileManager()">
    <h1 class="text-2xl font-bold mb-6 text-black">Profil</h1>

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
    <div class="bg-[#f0f0f0] rounded-[12px] p-8 flex flex-col md:flex-row justify-between items-start mb-8 relative">
        <div class="flex flex-col gap-4 w-full md:w-[60%]">
            <div class="font-bold text-lg text-black mb-2">Informasi Pribadi</div>
            
            <form action="{{ route('profil.updateInfo') }}" method="POST" id="profile-form" class="flex flex-col gap-4">
                @csrf
                <div class="flex items-center">
                    <span class="w-[120px] text-gray-600">Nama</span>
                    <span class="w-[15px] text-center text-gray-600">:</span>
                    <span class="flex-1 text-black font-semibold uppercase">{{ $profileData['name'] }}</span>
                </div>
                <div class="flex items-center">
                    <span class="w-[120px] text-gray-600">{{ $profileData['id_label'] }}</span>
                    <span class="w-[15px] text-center text-gray-600">:</span>
                    <span class="flex-1 text-black font-semibold">{{ $profileData['id_value'] }}</span>
                </div>
                <div class="flex items-center">
                    <span class="w-[120px] text-gray-600">Email</span>
                    <span class="w-[15px] text-center text-gray-600">:</span>
                    <div class="flex-1">
                        <span x-show="!editMode" class="text-black break-all">{{ $profileData['email'] }}</span>
                        <input x-cloak x-show="editMode" type="email" name="email" value="{{ $profileData['email'] }}" class="w-full border rounded px-2 py-1 text-sm outline-none focus:border-blue-500">
                    </div>
                </div>
                <div class="flex items-center">
                    <span class="w-[120px] text-gray-600">No HP</span>
                    <span class="w-[15px] text-center text-gray-600">:</span>
                    <div class="flex-1">
                        <span x-show="!editMode" class="text-black">{{ $profileData['no_hp'] }}</span>
                        <input x-cloak x-show="editMode" type="text" name="no_hp" value="{{ $profileData['no_hp'] == '-' ? '' : $profileData['no_hp'] }}" class="w-full border rounded px-2 py-1 text-sm outline-none focus:border-blue-500">
                    </div>
                </div>

                <div class="mt-4 flex gap-2">
                    <button type="button" x-show="!editMode" @click="editMode = true" class="bg-transparent border-none cursor-pointer flex items-center gap-2 text-gray-500 hover:text-black">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20h9"/><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"/></svg>
                        <span>Edit</span>
                    </button>
                    <button type="submit" x-cloak x-show="editMode" class="bg-blue-600 text-white px-4 py-1.5 rounded text-sm font-semibold hover:bg-blue-700">Simpan Detail</button>
                    <button type="button" x-cloak x-show="editMode" @click="editMode = false" class="bg-gray-400 text-white px-4 py-1.5 rounded text-sm font-semibold hover:bg-gray-500">Batal</button>
                </div>
            </form>
        </div>

        <div class="flex flex-col items-center w-full md:w-[30%] mt-6 md:mt-0 relative group">
            <div class="w-[120px] h-[120px] rounded-full bg-[#6c5ce7] flex justify-center items-center text-white overflow-hidden shadow-sm relative">
                @if($user->avatar)
                    <img src="{{ asset('storage/' . $user->avatar) }}" alt="Avatar" class="w-full h-full object-cover">
                @else
                    <svg width="60" height="60" viewBox="0 0 24 24" fill="currentColor"><path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/></svg>
                @endif
                
                <!-- Hover Upload Overlay -->
                <label for="avatar-upload" class="absolute inset-0 bg-black/50 hidden group-hover:flex items-center justify-center cursor-pointer text-xs text-center font-bold">Ubah Foto</label>
            </div>
            <div class="absolute bottom-2 right-4 text-gray-500 cursor-pointer pointer-events-none text-2xl drop-shadow-md">&#128394;</div>
            <form action="{{ route('profil.updateAvatar') }}" method="POST" enctype="multipart/form-data" id="avatar-form" class="hidden">
                @csrf
                <input type="file" id="avatar-upload" name="avatar" accept="image/*" onchange="document.getElementById('avatar-form').submit()">
            </form>
        </div>
    </div>

    <!-- Tanda Tangan Digital Section -->
    <div class="bg-[#e6e6e6] rounded-[12px] p-6 flex flex-col items-center mb-10 w-full max-w-md mx-auto relative shadow-inner">
        <div class="font-bold mb-4 text-[16px] text-black">Tanda Tangan Digital</div>
        
        <div class="bg-white w-full h-[120px] border border-[#dcdcdc] rounded-[8px] flex justify-center items-center mb-4 overflow-hidden relative">
            @if($user->signature_path)
                <img src="{{ asset('storage/' . $user->signature_path) }}" alt="Tanda Tangan" class="max-w-[80%] max-h-[80%] object-contain">
            @else
                <span class="text-sm text-gray-400 italic">Belum ada tanda tangan.</span>
            @endif
        </div>

        <div class="flex justify-center w-full mt-2">
            <button @click="showSigModal = true" type="button" class="bg-white border border-gray-300 shadow-sm px-6 py-2 rounded-md font-semibold text-sm text-gray-700 flex items-center hover:bg-gray-50 focus:outline-none transition-colors">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" class="mr-2" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20h9"></path><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"></path></svg>
                Buat / Ubah Tanda Tangan
            </button>
        </div>
    </div>

    <!-- Reset Password & Sign Out Context -->
    <div class="flex justify-center mb-10">
        <button type="button" onclick="alert('Fitur Reset Password (Dummy)')" class="px-5 py-2 bg-white text-gray-800 border border-gray-400 hover:border-gray-600 rounded-full font-bold text-sm shadow-sm transition-colors">
            Reset Password
        </button>
    </div>

    <div class="flex justify-end border-t border-[#eaeaea] pt-4">
        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit" class="flex items-center gap-2 bg-transparent border-none text-[#d63031] font-bold text-sm cursor-pointer hover:text-red-800">
                <span class="text-lg">&#10141;</span> <span>Sign Out</span>
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
            <div class="px-6 py-4 border-t border-gray-200 bg-gray-50 flex justify-between items-center">
                <button type="button" @click="clearCanvas()" class="text-red-600 hover:text-red-800 font-semibold text-sm">Hapus / Ulang</button>
                <div class="flex gap-3">
                    <button type="button" @click="showSigModal = false" class="px-4 py-2 bg-white border border-gray-300 rounded text-sm font-semibold hover:bg-gray-50">Batal</button>
                    <form action="{{ route('profil.updateSignatureDraw') }}" method="POST" id="draw-form">
                        @csrf
                        <input type="hidden" name="signature_base64" id="signature_base64">
                        <button type="button" @click="saveCanvas()" class="px-4 py-2 bg-blue-600 text-white rounded text-sm font-semibold hover:bg-blue-700 shadow-sm">Simpan</button>
                    </form>
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
                alert('Kanvas tanda tangan Anda masih kosong!');
                return;
            }
            
            document.getElementById('signature_base64').value = dataURL;
            document.getElementById('draw-form').submit();
        }
    }
}
</script>
