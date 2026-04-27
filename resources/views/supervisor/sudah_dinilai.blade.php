<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tautan Tidak Berlaku - UKRIDA</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 flex items-center justify-center min-h-screen">
    <div class="max-w-md w-full bg-white rounded-xl shadow-lg p-8 text-center border border-gray-100">
        <div class="w-20 h-20 bg-yellow-100 text-yellow-600 rounded-full flex items-center justify-center mx-auto mb-6">
            <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
        </div>
        <h1 class="text-xl font-bold text-gray-800 mb-2">Tautan Telah Kedaluwarsa</h1>
        <p class="text-gray-500 mb-6 leading-relaxed">Nilai untuk mahasiswa <strong>{{ $sidang->mahasiswa->user->name ?? '-' }}</strong> telah dikirimkan sebelumnya. Demi keamanan dan integritas data, tautan ini hanya dapat digunakan satu kali.</p>
        <p class="text-sm text-gray-400">Jika Anda merasa belum pernah mengisi penilaian ini, silakan hubungi Koordinator KP UKRIDA.</p>
    </div>
</body>
</html>
