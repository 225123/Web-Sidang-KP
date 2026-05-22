<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Penilaian Kerja Praktek - UKRIDA</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-gray-50 text-gray-800">

<div class="min-h-screen flex flex-col items-center justify-center p-4">
    <div class="max-w-3xl w-full bg-white rounded-xl shadow-lg overflow-hidden border border-gray-200">
        
        <!-- Header -->
        <div class="bg-blue-700 text-white p-6 md:p-8 text-center">
            <h1 class="text-2xl font-bold mb-2">Formulir Penilaian Supervisor Perusahaan</h1>
            <p class="text-blue-100 text-sm">Program Studi Teknik Informatika, Universitas Kristen Krida Wacana</p>
        </div>

        <!-- Content -->
        <div class="p-6 md:p-8">
            <div class="bg-blue-50 border border-blue-100 rounded-lg p-5 mb-8">
                <h2 class="text-sm font-bold text-blue-800 mb-4 uppercase tracking-wider">Identitas Mahasiswa</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                    <div>
                        <span class="block text-gray-500 mb-1">Nama Mahasiswa</span>
                        <span class="font-semibold text-gray-900">{{ $sidang->mahasiswa->user->name ?? '-' }}</span>
                    </div>
                    <div>
                        <span class="block text-gray-500 mb-1">NIM</span>
                        <span class="font-semibold text-gray-900">{{ $sidang->mahasiswa->nim ?? '-' }}</span>
                    </div>
                    <div class="md:col-span-2">
                        <span class="block text-gray-500 mb-1">Judul Kerja Praktek</span>
                        <span class="font-semibold text-gray-900">{{ $sidang->pendaftaranKp->judul_kp ?? '-' }}</span>
                    </div>
                </div>
            </div>

            @if ($errors->any())
                <div class="bg-red-50 text-red-700 p-4 rounded-lg mb-6 border border-red-200">
                    <ul class="list-disc list-inside text-sm">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('supervisor.penilaian.submit', $token) }}" method="POST" id="penilaian-form">
                @csrf

                <h3 class="font-bold text-gray-800 mb-4 text-lg border-b pb-2">Komponen Penilaian (Skala 0 - 100)</h3>
                
                <div class="space-y-6 mb-8">
                    <!-- Motivasi & Kedisiplinan -->
                    <div>
                        <label class="flex justify-between items-center mb-2">
                            <span class="text-sm font-bold text-gray-700">1. Motivasi & Kedisiplinan (25%)</span>
                            <span class="text-xs text-gray-500">Kehadiran, ketepatan waktu, dan semangat kerja</span>
                        </label>
                        <input type="number" name="nilai_motivasi" min="0" max="100" required value="{{ old('nilai_motivasi') }}" placeholder="Contoh: 85" class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>

                    <!-- Kualitas Pekerjaan -->
                    <div>
                        <label class="flex justify-between items-center mb-2">
                            <span class="text-sm font-bold text-gray-700">2. Kualitas Hasil Pekerjaan (25%)</span>
                            <span class="text-xs text-gray-500">Ketelitian, pemahaman teknis, dan kesesuaian target</span>
                        </label>
                        <input type="number" name="nilai_kualitas" min="0" max="100" required value="{{ old('nilai_kualitas') }}" placeholder="Contoh: 85" class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>

                    <!-- Inisiatif -->
                    <div>
                        <label class="flex justify-between items-center mb-2">
                            <span class="text-sm font-bold text-gray-700">3. Inisiatif & Kreativitas (25%)</span>
                            <span class="text-xs text-gray-500">Kemampuan problem solving dan gagasan inovatif</span>
                        </label>
                        <input type="number" name="nilai_inisiatif" min="0" max="100" required value="{{ old('nilai_inisiatif') }}" placeholder="Contoh: 85" class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>

                    <!-- Sikap -->
                    <div>
                        <label class="flex justify-between items-center mb-2">
                            <span class="text-sm font-bold text-gray-700">4. Sikap & Kerjasama Tim (25%)</span>
                            <span class="text-xs text-gray-500">Komunikasi, etika profesi, dan kemampuan adaptasi</span>
                        </label>
                        <input type="number" name="nilai_sikap" min="0" max="100" required value="{{ old('nilai_sikap') }}" placeholder="Contoh: 85" class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                </div>

                <h3 class="font-bold text-gray-800 mb-4 text-lg border-b pb-2">Dokumen Validasi (Tanda Tangan)</h3>
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-5 mb-8" x-data="signaturePad()">
                    <label class="block text-sm font-bold text-gray-700 mb-2">Tanda Tangan Digital <span class="text-red-500">*</span></label>
                    <p class="text-xs text-yellow-800 mb-4 leading-relaxed">Goreskan tanda tangan Anda pada area di bawah ini. Tanda tangan ini akan dicantumkan secara resmi pada lembar Berita Acara Sidang.</p>
                    
                    <div class="w-full flex items-center justify-between mb-3 bg-white p-2 rounded border border-gray-200 shadow-sm">
                        <label class="text-sm font-semibold text-gray-700 flex items-center gap-2">
                            <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                            Tebal Garis:
                        </label>
                        <input type="range" id="lineWidth" min="1" max="10" value="3" class="w-[60%] cursor-pointer accent-blue-600">
                    </div>

                    <div class="border-2 border-gray-300 rounded-lg bg-white w-full overflow-hidden mb-3" style="cursor: crosshair; user-select: none;">
                        <canvas id="signaturePadCanvas" width="600" height="300" class="w-full h-[250px] touch-none"
                            @mousedown="startDraw($event)"
                            @mousemove="draw($event)"
                            @mouseup.window="stopDraw($event)"
                            @touchstart.prevent="startDraw($event)"
                            @touchmove.prevent="draw($event)"
                            @touchend.window="stopDraw($event)"
                            @touchcancel.window="stopDraw($event)">
                        </canvas>
                    </div>

                    <div class="flex justify-between items-center w-full">
                        <button type="button" @click="clearCanvas()" class="text-red-600 hover:text-red-800 font-semibold text-sm">Hapus / Ulang</button>
                        <div x-show="sigErrorMsg" x-transition style="display: none;" class="text-red-600 text-xs font-medium">
                            <span x-text="sigErrorMsg"></span>
                        </div>
                    </div>

                    <input type="hidden" name="file_nilai_supervisor" id="signature_base64">
                </div>

                <!-- Submit -->
                <div class="flex items-center justify-end border-t pt-6">
                    <button type="button" onclick="submitForm()" class="bg-green-600 hover:bg-green-700 text-white font-bold py-3 px-8 rounded-lg shadow-md transition-colors w-full md:w-auto text-center">
                        Kirim Penilaian Final
                    </button>
                </div>
            </form>
        </div>
    </div>
    
    <div class="mt-8 text-center text-xs text-gray-400">
        &copy; {{ date('Y') }} Program Studi Teknik Informatika, UKRIDA.
    </div>
</div>

</body>

<script>
    function submitForm() {
        const canvas = document.getElementById('signaturePadCanvas');
        if(!canvas) return;

        const dataURL = canvas.toDataURL('image/png');
        
        const blank = document.createElement('canvas');
        blank.width = canvas.width;
        blank.height = canvas.height;
        
        if(dataURL === blank.toDataURL('image/png')) {
            alert('Tanda tangan masih kosong! Silakan berikan tanda tangan Anda terlebih dahulu.');
            return;
        }
        
        document.getElementById('signature_base64').value = dataURL;
        document.getElementById('penilaian-form').submit();
    }

    document.addEventListener('alpine:init', () => {
        Alpine.data('signaturePad', () => ({
            sigErrorMsg: '',
            isDrawing: false,
            lastX: 0,
            lastY: 0,
            ctx: null,

            init() {
                setTimeout(() => {
                    this.setupCanvas();
                }, 50);
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
            }
        }));
    });
</script>

</html>
