<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     * 
     * Hanya user yang emailnya sudah terdaftar di sistem (pre-seeded oleh Koordinator)
     * yang boleh mendaftarkan akun. Email yang belum ada di database akan ditolak.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'string', 'lowercase', 'email', 'max:255'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        // Cari user yang sudah dibuat oleh Koordinator berdasarkan email
        $existingUser = User::where('email', strtolower($request->email))->first();

        if (!$existingUser) {
            return back()->withInput($request->only('name', 'email'))
                ->withErrors(['email' => 'Email ini belum terdaftar di sistem. Hubungi Koordinator KP untuk mendapatkan akses.']);
        }

        // Jika password sudah diset (user sudah pernah mendaftar), tolak registrasi ulang
        if ($existingUser->password && $existingUser->email_verified_at) {
            return back()->withInput($request->only('name', 'email'))
                ->withErrors(['email' => 'Akun dengan email ini sudah aktif. Silakan login atau gunakan Lupa Password.']);
        }

        // Update user yang ada dengan data registrasi
        $existingUser->update([
            'name'     => $request->name,
            'password' => Hash::make($request->password),
        ]);

        // Kirim email verifikasi
        event(new Registered($existingUser));

        // Login user
        Auth::login($existingUser);

        // Arahkan ke halaman verifikasi email
        return redirect()->route('verification.notice');
    }
}
