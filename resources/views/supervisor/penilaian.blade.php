<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Penilaian Kerja Praktek - UKRIDA</title>
    <script src="https://cdn.tailwindcss.com"></script>
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

            <form action="{{ route('supervisor.penilaian.submit', $token) }}" method="POST" enctype="multipart/form-data">
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

                <h3 class="font-bold text-gray-800 mb-4 text-lg border-b pb-2">Dokumen Validasi</h3>
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-5 mb-8">
                    <label class="block text-sm font-bold text-gray-700 mb-2">Unggah PDF Form Penilaian (Ber-Cap Perusahaan) <span class="text-red-500">*</span></label>
                    <p class="text-xs text-yellow-800 mb-4 leading-relaxed">Untuk mencegah manipulasi data, Bapak/Ibu diwajibkan untuk tetap mengunggah dokumen Lembar Penilaian yang telah dicetak, ditandatangani, dan diberikan **Cap Basah Perusahaan / Stempel Digital Resmi**.</p>
                    
                    <input type="file" name="file_nilai_supervisor" accept=".pdf" required class="block w-full text-sm text-gray-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-600 file:text-white hover:file:bg-blue-700 focus:outline-none cursor-pointer">
                    <p class="mt-2 text-[11px] text-gray-500">Format: PDF, Maksimal: 5MB</p>
                </div>

                <!-- Submit -->
                <div class="flex items-center justify-end border-t pt-6">
                    <button type="submit" class="bg-green-600 hover:bg-green-700 text-white font-bold py-3 px-8 rounded-lg shadow-md transition-colors w-full md:w-auto text-center">
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
</html>
