<x-guest-layout>
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-900 text-center">Ubah Password</h2>
        <p class="mt-2 text-sm text-gray-600 text-center">
            Pastikan password baru Anda kuat dan mudah diingat.
        </p>
    </div>

    <form method="POST" action="{{ route('profil.password.update') }}">
        @csrf
        @method('put')


        <!-- Password Baru -->
        <div class="mb-4">
            <x-input-label for="password" value="Password Baru" />
            <x-text-input id="password" name="password" type="password" class="block mt-1 w-full" required autocomplete="new-password" placeholder="Minimal 8 karakter" />
            <x-input-error :messages="$errors->updatePassword->get('password')" class="mt-2" />
        </div>

        <!-- Konfirmasi Password Baru -->
        <div class="mb-4">
            <x-input-label for="password_confirmation" value="Konfirmasi Password Baru" />
            <x-text-input id="password_confirmation" name="password_confirmation" type="password" class="block mt-1 w-full" required autocomplete="new-password" placeholder="Ulangi password baru" />
            <x-input-error :messages="$errors->updatePassword->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex items-center justify-between mt-8">
            <a href="{{ route('profil.index') }}?scroll=reset-btn" class="text-sm text-gray-600 hover:text-gray-900 font-medium transition-colors">
                &larr; Kembali ke Profil
            </a>

            <x-primary-button class="bg-[#4285F4] hover:bg-blue-600 px-8 py-2.5">
                {{ __('Selesai') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
