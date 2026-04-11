<x-guest-layout>
    <div class="mb-8">
        <h2 class="text-2xl font-bold text-gray-900">Selamat Datang Kembali</h2>
        <p class="text-sm text-gray-600 mt-2">Silakan masuk menggunakan identitas universitas Anda.</p>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" class="space-y-6">
        @csrf

        <!-- Username / NIM / NIDN -->
        <div>
            <label for="login_id" class="block text-sm font-semibold text-gray-700">NIM / NIDN /NIDK </label>
            <div class="mt-2 relative rounded-md shadow-sm">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                </div>
                <input id="login_id" type="text" name="login_id" value="{{ old('login_id') }}" required autofocus autocomplete="username"
                    class="block w-full pl-10 pt-3 pb-3 sm:text-sm border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 transition-colors"
                    placeholder="Masukkan NIM atau NIDN">
            </div>
            <x-input-error :messages="$errors->get('login_id')" class="mt-2" />
        </div>

        <!-- Password -->
        <div>
            <label for="password" class="block text-sm font-semibold text-gray-700">Password</label>
            <div class="mt-2 relative rounded-md shadow-sm">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                    </svg>
                </div>
                <input id="password" type="password" name="password" required autocomplete="current-password"
                    class="block w-full pl-10 pt-3 pb-3 sm:text-sm border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 transition-colors"
                    placeholder="••••••••">
            </div>
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Remember & Forgot -->
        <div class="flex items-center justify-between">
            <div class="flex items-center">
                <input id="remember_me" name="remember" type="checkbox" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                <label for="remember_me" class="ml-2 block text-sm text-gray-700">Ingat Saya</label>
            </div>

            <div class="text-sm">
                @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}" class="font-semibold text-blue-600 hover:text-blue-500 transition-colors">
                        Lupa password?
                    </a>
                @endif
            </div>
        </div>

        <!-- Submit -->
        <div>
            <button type="submit" class="w-full flex justify-center py-3 px-4 border border-transparent rounded-lg shadow-sm text-sm font-bold text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors transform hover:-translate-y-0.5">
                Log In
            </button>
        </div>
        
        <!-- Registration Route (If available) -->
        @if (Route::has('register'))
        <div class="text-center mt-4">
            <span class="text-sm text-gray-600">Belum memiliki akun?</span>
            <a href="{{ route('register') }}" class="font-semibold text-sm text-emerald-600 hover:text-emerald-500 transition-colors ml-1">
                Daftar sekarang
            </a>
        </div>
        @endif
    </form>
</x-guest-layout>
