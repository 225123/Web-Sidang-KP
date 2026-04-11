<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-inter antialiased text-gray-900 bg-white">
    <div class="min-h-screen flex">
        <!-- Left Side (Visual/Branding) -->
        <div class="hidden lg:flex lg:w-1/2 relative bg-gray-900 items-center justify-center overflow-hidden">
            <!-- Modern Gradient Background -->
            <div class="absolute inset-0 bg-gradient-to-br from-blue-900 via-gray-900 to-emerald-900 opacity-90"></div>
            
            <!-- Abstract Shapes -->
            <div class="absolute top-[-10%] left-[-10%] w-96 h-96 bg-blue-600 rounded-full mix-blend-multiply filter blur-3xl opacity-30 animate-blob"></div>
            <div class="absolute top-[20%] right-[-10%] w-96 h-96 bg-emerald-600 rounded-full mix-blend-multiply filter blur-3xl opacity-30 animate-blob animation-delay-2000"></div>
            <div class="absolute bottom-[-10%] left-[20%] w-96 h-96 bg-orange-600 rounded-full mix-blend-multiply filter blur-3xl opacity-30 animate-blob animation-delay-4000"></div>

            <!-- Content -->
            <div class="relative z-10 text-center px-12">
                <h1 class="text-6xl font-extrabold text-white tracking-tight mb-6">
                    KERJA<br/><span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-400 to-emerald-400">PRAKTEK</span>
                </h1>
                <p class="text-lg text-gray-300 max-w-md mx-auto leading-relaxed">
                    Sistem Informasi Manajemen Pelaksanaan & Penilaian Sidang Kerja Praktek Universitas Kristen Krida Wacana.
                </p>
                <div class="mt-12 flex justify-center gap-4">
                    <div class="h-2 w-12 bg-white rounded-full opacity-20"></div>
                    <div class="h-2 w-4 bg-white rounded-full opacity-20"></div>
                    <div class="h-2 w-4 bg-white rounded-full opacity-20"></div>
                </div>
            </div>
        </div>

        <!-- Right Side (Form) -->
        <div class="w-full lg:w-1/2 flex items-center justify-center p-8 sm:p-12 lg:p-24 bg-gray-50">
            <div class="w-full max-w-md space-y-8">
                <!-- Mobile Logo (Visible only on small screens) -->
                <div class="lg:hidden text-center mb-8">
                    <h1 class="text-4xl font-extrabold text-gray-900 tracking-tight">
                        KERJA<span class="text-blue-600">PRAKTEK</span>
                    </h1>
                </div>

                {{ $slot }}
                
                <div class="mt-10 text-center">
                    <p class="text-xs text-gray-500">&copy; {{ date('Y') }} Universitas Kristen Krida Wacana. Hak Cipta Dilindungi.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Add some animation for the background blobs -->
    <style>
        @keyframes blob {
            0% { transform: translate(0px, 0px) scale(1); }
            33% { transform: translate(30px, -50px) scale(1.1); }
            66% { transform: translate(-20px, 20px) scale(0.9); }
            100% { transform: translate(0px, 0px) scale(1); }
        }
        .animate-blob {
            animation: blob 7s infinite;
        }
        .animation-delay-2000 {
            animation-delay: 2s;
        }
        .animation-delay-4000 {
            animation-delay: 4s;
        }
    </style>
</body>
</html>
