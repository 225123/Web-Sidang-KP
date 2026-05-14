<x-guest-layout>
    <div class="mb-8">
        <h2 class="text-2xl font-bold text-gray-900">Lupa Password?</h2>
        <p class="text-sm text-gray-600 mt-2">Tidak masalah. Masukkan email yang terdaftar di sistem. Kami akan mengirimkan tautan untuk membuat password baru.</p>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('password.email') }}" class="space-y-6">
        @csrf

        <!-- Email Address -->
        <div>
            <label for="email" class="block text-sm font-semibold text-gray-700">Alamat Email</label>
            <div class="mt-2 relative rounded-md shadow-sm">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207" />
                    </svg>
                </div>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus
                    class="block w-full pl-10 pt-3 pb-3 sm:text-sm border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 transition-colors"
                    placeholder="Masukkan alamat email Anda">
            </div>
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="flex items-center justify-between mt-4 gap-4">
            <a href="{{ route('login') }}" class="text-sm font-semibold text-gray-500 hover:text-gray-900 transition-colors">
                Kembali ke Login
            </a>
            <button type="submit" 
                x-data="{ loading: false }" 
                x-on:click="setTimeout(() => { if($el.closest('form').checkValidity()) loading = true }, 10)"
                :disabled="loading"
                class="flex-1 flex justify-center py-3 px-4 border border-transparent rounded-lg shadow-sm text-sm font-bold text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors transform hover:-translate-y-0.5 disabled:opacity-50 disabled:cursor-not-allowed">
                <template x-if="!loading">
                    <span>Kirim Tautan Reset</span>
                </template>
                <template x-if="loading">
                    <span class="flex items-center gap-2">
                        <svg class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Sedang Mengirim...
                    </span>
                </template>
            </button>
        </div>
    </form>
</x-guest-layout>
