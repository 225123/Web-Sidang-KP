<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Helpers\ImageHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

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

        if ($role === 'mahasiswa') {
            $profileData['id_label'] = 'NIM';
            $profileData['id_value'] = $user->mahasiswa?->nim ?? '-';
            $profileData['no_hp'] = $user->mahasiswa?->no_hp ?? '-';
        } elseif (in_array($role, ['dosen', 'koordinator_kp']) || str_contains($role, 'koordinator')) {
            $profileData['id_label'] = 'NIDN/NIDK';
            $profileData['id_value'] = $user->dosen?->nidn ?? '-';
            $profileData['no_hp'] = $user->dosen?->no_hp ?? '-';
        }

        // Tentukan view yang tepat berdasarkan role
        $viewName = match (true) {
            $role === 'mahasiswa'                                          => 'mahasiswa.profil',
            $role === 'dosen'                                             => 'dosen.profil',
            $role === 'koordinator_kp' || str_contains($role, 'koordinator') => 'koordinator.profil',
            default                                                       => 'koordinator.profil', // safe fallback
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
            'avatar' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:4096',
        ]);

        $user = Auth::user();

        if ($request->hasFile('avatar')) {
            // Hapus avatar lama
            if ($user->avatar) {
                Storage::disk(upload_disk())->delete($user->avatar);
            }

            // Konversi ke WebP (max 400×400, quality 85)
            $path = ImageHelper::convertToWebP(
                $request->file('avatar'),
                'avatars',
                400, 400, 85, upload_disk()
            );

            $user->avatar = $path;
            $user->save();
        }

        return redirect()->back()->with('success', 'Foto profil berhasil diperbarui!');
    }

    public function updateSignatureUpload(Request $request)
    {
        $request->validate([
            'signature_file' => 'required|image|mimes:jpeg,png,jpg,webp|max:4096',
        ]);

        $user = Auth::user();

        if ($request->hasFile('signature_file')) {
            if ($user->signature_path) {
                Storage::disk(upload_disk())->delete($user->signature_path);
            }

            // Konversi ke WebP (max 800×250, quality 90 — transparan dipertahankan)
            $path = ImageHelper::convertToWebP(
                $request->file('signature_file'),
                'signatures',
                800, 250, 90, upload_disk()
            );

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

        // Decode base64 PNG dari canvas
        $image_parts   = explode(';base64,', $request->signature_base64);
        $image_base64  = base64_decode($image_parts[1] ?? '');

        // Konversi PNG canvas → WebP menggunakan GD
        $source = imagecreatefromstring($image_base64);
        $webpData = null;

        if ($source) {
            // Pertahankan transparansi
            imagealphablending($source, true);
            imagesavealpha($source, true);

            ob_start();
            imagewebp($source, null, 90);
            $webpData = ob_get_clean();
        }

        // Hapus signature lama
        if ($user->signature_path) {
            Storage::disk(upload_disk())->delete($user->signature_path);
        }

        $fileName = 'signatures/' . Str::uuid() . '.webp';
        Storage::disk(upload_disk())->put($fileName, $webpData ?? $image_base64);

        $user->signature_path = $fileName;
        $user->save();

        return redirect()->back()->with('success', 'Tanda tangan berhasil dibuat!');
    }
}
