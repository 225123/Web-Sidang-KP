<x-guest-layout>
    <div class="mb-8">
        <h2 class="text-2xl font-bold text-gray-900">Selamat Datang Kembali</h2>
        <p class="text-sm text-gray-600 mt-2">Silakan masuk menggunakan identitas universitas Anda.</p>
    </div>

    <!-- Session Status (e.g. password reset link sent) -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <!-- Alert Box for Errors (Session or Validation) -->
    @if(session('error') || $errors->any())
    <div class="mb-4 flex items-start gap-3 bg-red-50 border border-red-300 rounded-lg px-4 py-3">
        <svg class="w-5 h-5 text-red-500 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <div class="flex-1">
            @if(session('error'))
                <p class="text-sm font-medium text-red-800">{!! session('error') !!}</p>
            @endif
            
            @if($errors->any())
                <ul class="text-sm font-medium text-red-800 {{ $errors->count() > 1 ? 'list-disc list-inside' : '' }}">
                    @foreach ($errors->all() as $error)
                        <li>{!! $error !!}</li>
                    @endforeach
                </ul>
            @endif
        </div>
    </div>
    @endif

    <form method="POST" action="{{ route('login') }}" class="space-y-6" data-turbo="false">
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
                <input id="login_id" type="text" name="login_id" value="{{ session('prefilled_id') ?? old('login_id') }}" required autofocus autocomplete="username"
                    class="block w-full pl-10 pt-3 pb-3 sm:text-sm border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 transition-colors"
                    placeholder="Masukkan NIM atau NIDN">
            </div>

        </div>

        <!-- Password -->
        <div x-data="{ show: false }">
            <label for="password" class="block text-sm font-semibold text-gray-700">Password</label>
            <div class="mt-2 relative rounded-md shadow-sm">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                    </svg>
                </div>
                <input id="password" :type="show ? 'text' : 'password'" name="password" required autocomplete="current-password"
                    class="block w-full pl-10 pr-10 pt-3 pb-3 sm:text-sm border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 transition-colors"
                    placeholder="••••••••">
                <div class="absolute inset-y-0 right-0 pr-3 flex items-center cursor-pointer">
                    <button type="button" @click="show = !show" class="text-gray-400 hover:text-gray-600 focus:outline-none transition-colors mt-1">
                        <!-- Eye icon for show -->
                        <svg x-show="!show" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                        <!-- Eye slash icon for hide -->
                        <svg x-show="show" style="display:none;" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                        </svg>
                    </button>
                </div>
            </div>

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
        

    </form>
</x-guest-layout>
