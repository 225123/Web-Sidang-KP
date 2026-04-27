<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class UserProfileController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $role = $user->role;
        $active = 'profil';

        $profileData = [
            'name' => $user->name,
            'email' => $user->email,
            'id_label' => 'NIDN/NIDK',
            'id_value' => '-',
            'no_hp' => '-',
        ];

        if ($role === 'mahasiswa' && $user->mahasiswa) {
            $profileData['id_label'] = 'NIM';
            $profileData['id_value'] = $user->mahasiswa->nim;
            $profileData['no_hp'] = $user->mahasiswa->no_hp ?? '-';
        } elseif (in_array($role, ['dosen', 'koordinator_kp']) && $user->dosen) {
            $profileData['id_label'] = 'NIDN/NIDK';
            $profileData['id_value'] = $user->dosen->nidn;
            $profileData['no_hp'] = $user->dosen->no_hp ?? '-';
        }

        // Tentukan view yang tepat berdasarkan role
        $viewName = match ($role) {
            'mahasiswa' => 'mahasiswa.profil',
            'dosen' => 'dosen.profil',
            'koordinator_kp' => 'koordinator.profil',
            default => 'dashboard', // fallback
        };

        return view($viewName, compact('user', 'profileData', 'active'));
    }

    public function updateInfo(Request $request)
    {
        $request->validate([
            'email' => 'required|email|max:255|unique:users,email,'.Auth::id(),
            'no_hp' => 'nullable|string|max:20',
        ], [
            'email.unique' => 'Email yang baru saja Anda masukkan sudah terdaftar untuk pengguna lain.',
        ]);

        $user = Auth::user();

        // Update email on user table
        if ($user->email !== $request->email) {
            $user->email = $request->email;
            $user->save();
        }

        // Update no_hp on role table
        if ($user->role === 'mahasiswa' && $user->mahasiswa) {
            $user->mahasiswa->no_hp = $request->no_hp;
            $user->mahasiswa->save();
        } elseif (in_array($user->role, ['dosen', 'koordinator_kp']) && $user->dosen) {
            $user->dosen->no_hp = $request->no_hp;
            $user->dosen->save();
        }

        return redirect()->back()->with('success', 'Profil berhasil diperbarui!');
    }

    public function updateAvatar(Request $request)
    {
        $request->validate([
            'avatar' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $user = Auth::user();

        if ($request->hasFile('avatar')) {
            if ($user->avatar) {
                Storage::disk('public')->delete($user->avatar);
            }
            $path = $request->file('avatar')->store('avatars', 'public');
            $user->avatar = $path;
            $user->save();
        }

        return redirect()->back()->with('success', 'Foto profil berhasil diperbarui!');
    }

    public function updateSignatureUpload(Request $request)
    {
        $request->validate([
            'signature_file' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $user = Auth::user();

        if ($request->hasFile('signature_file')) {
            if ($user->signature_path) {
                Storage::disk('public')->delete($user->signature_path);
            }
            $path = $request->file('signature_file')->store('signatures', 'public');
            $user->signature_path = $path;
            $user->save();
        }

        return redirect()->back()->with('success', 'Tanda tangan digital berhasil diunggah!');
    }

    public function updateSignatureDraw(Request $request)
    {
        $request->validate([
            'signature_base64' => 'required|string',
        ]);

        $user = Auth::user();

        $image_parts = explode(';base64,', $request->signature_base64);
        $image_type_aux = explode('image/', $image_parts[0]);
        $image_type = $image_type_aux[1] ?? 'png';
        $image_base64 = base64_decode($image_parts[1]);

        $fileName = 'signatures/'.uniqid().'.png';

        Storage::disk('public')->put($fileName, $image_base64);

        if ($user->signature_path) {
            Storage::disk('public')->delete($user->signature_path);
        }

        $user->signature_path = $fileName;
        $user->save();

        return redirect()->back()->with('success', 'Tanda tangan berhasil dibuat!');
    }
}
