<x-guest-layout>
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-900 text-center">Ubah Password</h2>
        <p class="mt-2 text-sm text-gray-600 text-center">
            Pastikan password baru Anda kuat dan mudah diingat.
        </p>
    </div>

    <form method="POST" action="{{ route('profil.password.update') }}" x-data="{
        password: '',
        password_confirmation: '',
        showMismatchError: false,
        get strengthText() {
            if (this.password.length === 0) return '';
            let hasUpper = /[A-Z]/.test(this.password);
            let hasLower = /[a-z]/.test(this.password);
            let hasNumber = /[0-9]/.test(this.password);
            let hasSymbol = /[\W_]/.test(this.password);
            let requirementsMet = hasUpper + hasLower + hasNumber + hasSymbol;
            
            if (this.password.length < 8) return 'Lemah (Minimal 8 Karakter)';
            if (requirementsMet < 4) return 'Sedang (Butuh Kombinasi Huruf Besar, Kecil, Angka & Simbol)';
            return 'Kuat';
        },
        get strengthClass() {
            if (this.password.length === 0) return '';
            if (this.password.length < 8) return 'text-red-500';
            let requirementsMet = /[A-Z]/.test(this.password) + /[a-z]/.test(this.password) + /[0-9]/.test(this.password) + /[\W_]/.test(this.password);
            if (requirementsMet < 4) return 'text-yellow-500';
            return 'text-green-500';
        },
        submitForm(e) {
            if (this.password !== this.password_confirmation) {
                this.showMismatchError = true;
                e.preventDefault();
            } else {
                this.showMismatchError = false;
            }
        }
    }" @submit="submitForm">
        @csrf
        @method('put')


        <!-- Password Baru -->
        <div class="mb-4">
            <x-input-label for="password" value="Password Baru" />
            <x-text-input id="password" name="password" type="password" class="block mt-1 w-full" x-model="password" @input="showMismatchError = false" required autocomplete="new-password" placeholder="Minimal 8 karakter" />
            <p x-show="password.length > 0" x-cloak class="mt-2 text-sm font-medium transition-all" :class="strengthClass" x-text="'Kekuatan Password: ' + strengthText"></p>
            <x-input-error :messages="$errors->updatePassword->get('password')" class="mt-2" />
        </div>

        <!-- Konfirmasi Password Baru -->
        <div class="mb-4">
            <x-input-label for="password_confirmation" value="Konfirmasi Password Baru" />
            <x-text-input id="password_confirmation" name="password_confirmation" type="password" class="block mt-1 w-full" x-model="password_confirmation" @input="showMismatchError = false" required autocomplete="new-password" placeholder="Ulangi password baru" />
            <p x-show="showMismatchError" x-cloak class="mt-2 text-sm font-medium text-red-500">Konfirmasi password tidak cocok dengan password baru.</p>
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
