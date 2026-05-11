<?php

namespace App\Helpers;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ImageHelper
{
    /**
     * Konversi gambar yang diupload ke format WebP, resize sesuai batas maksimal,
     * lalu simpan ke disk yang ditentukan. Mengembalikan path yang tersimpan.
     *
     * Menggunakan PHP GD (built-in, tanpa library eksternal).
     *
     * @param  UploadedFile  $file       File yang diupload dari request
     * @param  string        $folder     Folder tujuan (e.g. 'avatars', 'signatures')
     * @param  int           $maxWidth   Lebar maksimal (piksel)
     * @param  int           $maxHeight  Tinggi maksimal (piksel)
     * @param  int           $quality    Kualitas WebP 0–100 (default 85)
     * @param  string        $disk       Storage disk ('public', 'r2', dll)
     * @return string                    Path relatif file yang disimpan
     */
    public static function convertToWebP(
        UploadedFile $file,
        string $folder,
        int $maxWidth = 800,
        int $maxHeight = 800,
        int $quality = 85,
        string $disk = 'public'
    ): string {
        $mime = $file->getMimeType();
        $tempPath = $file->getRealPath();

        // Load sumber gambar sesuai tipe MIME
        $source = match (true) {
            str_contains($mime, 'jpeg') || str_contains($mime, 'jpg') => imagecreatefromjpeg($tempPath),
            str_contains($mime, 'png')  => imagecreatefrompng($tempPath),
            str_contains($mime, 'gif')  => imagecreatefromgif($tempPath),
            str_contains($mime, 'webp') => imagecreatefromwebp($tempPath),
            str_contains($mime, 'bmp')  => imagecreatefrombmp($tempPath),
            default                     => imagecreatefromjpeg($tempPath),
        };

        if (! $source) {
            // Fallback: simpan file asli tanpa konversi
            return $file->store($folder, $disk);
        }

        $origW = imagesx($source);
        $origH = imagesy($source);

        // Hitung dimensi baru dengan mempertahankan aspect ratio
        [$newW, $newH] = self::calculateDimensions($origW, $origH, $maxWidth, $maxHeight);

        // Buat canvas baru dengan background transparan (penting untuk PNG/signature)
        $canvas = imagecreatetruecolor($newW, $newH);

        // Aktifkan transparansi (untuk PNG dengan alpha channel)
        imagealphablending($canvas, false);
        imagesavealpha($canvas, true);
        $transparent = imagecolorallocatealpha($canvas, 0, 0, 0, 127);
        imagefilledrectangle($canvas, 0, 0, $newW, $newH, $transparent);
        imagealphablending($canvas, true);

        // Resize gambar
        imagecopyresampled($canvas, $source, 0, 0, 0, 0, $newW, $newH, $origW, $origH);

        // Tulis WebP ke buffer memory
        ob_start();
        imagewebp($canvas, null, $quality);
        $webpData = ob_get_clean();

        // Simpan ke storage
        $filename = $folder . '/' . Str::uuid() . '.webp';
        Storage::disk($disk)->put($filename, $webpData);

        return $filename;
    }

    /**
     * Hitung dimensi baru dengan mempertahankan aspect ratio.
     */
    private static function calculateDimensions(int $origW, int $origH, int $maxW, int $maxH): array
    {
        if ($origW <= $maxW && $origH <= $maxH) {
            return [$origW, $origH]; // Tidak perlu diperkecil
        }

        $ratio = min($maxW / $origW, $maxH / $origH);
        return [(int) round($origW * $ratio), (int) round($origH * $ratio)];
    }
}
