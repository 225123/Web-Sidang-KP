<?php

namespace App\Http\Requests\Auth;

use App\Models\Dosen;
use App\Models\Mahasiswa;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LoginRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'login_id' => ['required', 'string'],
            'password' => ['required', 'string'],
        ];
    }

    /**
     * Attempt to authenticate the request's credentials.
     *
     * @throws ValidationException
     */
    public function authenticate(): void
    {
        $this->ensureIsNotRateLimited();

        $loginId = $this->input('login_id');

        // 1. Try to find the user in Mahasiswa
        $mahasiswa = Mahasiswa::where('nim', $loginId)->first();
        if ($mahasiswa) {
            $user = $mahasiswa->user;
        } else {
            // 2. Try to find the user in Dosen
            $dosen = Dosen::where('nidn', $loginId)->first();
            $user = $dosen ? $dosen->user : null;
        }

        if (! $user) {
            RateLimiter::hit($this->throttleKey());
            throw ValidationException::withMessages([
                'login_id' => 'NIM/NIDN tidak ditemukan dalam sistem.',
            ]);
        }

        // Jika akun belum memiliki password (belum mendaftar sendiri)
        if (! $user->password) {
            RateLimiter::hit($this->throttleKey());
            throw ValidationException::withMessages([
                'login_id' => 'Akun Anda belum diaktifkan. Silakan hubungi Koordinator KP untuk mengaktifkan akun Anda.',
            ]);
        }

        if (! Auth::attempt(['email' => $user->email, 'password' => $this->input('password')], $this->boolean('remember'))) {
            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                'login_id' => 'Kombinasi NIM/NIDN dan password salah.',
            ]);
        }

        RateLimiter::clear($this->throttleKey());
    }

    /**
     * Ensure the login request is not rate limited.
     *
     * @throws ValidationException
     */
    public function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout($this));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'login_id' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    /**
     * Get the rate limiting throttle key for the request.
     */
    public function throttleKey(): string
    {
        return Str::transliterate(Str::lower($this->string('login_id')).'|'.$this->ip());
    }
}
