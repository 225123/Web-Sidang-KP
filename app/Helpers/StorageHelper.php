<?php

use Illuminate\Support\Facades\Storage;

if (! function_exists('storage_url')) {
    /**
     * Generate URL file dari storage yang aktif (local 'public' atau R2).
     * Gunakan fungsi ini di views/controllers sebagai pengganti asset('storage/'.$path).
     *
     * Lokal  : asset('storage/avatars/file.webp')
     * Production (R2): https://pub-xxx.r2.dev/avatars/file.webp
     *
     * @param  string|null  $path  Path relatif file (e.g. 'avatars/uuid.webp')
     * @param  string|null  $disk  Override disk (null = gunakan FILESYSTEM_DISK default)
     * @return string
     */
    function storage_url(?string $path, ?string $disk = null): string
    {
        if (! $path) {
            return '';
        }

        $activeDisk = $disk ?? config('filesystems.default', 'public');

        // Untuk disk 'public' lokal, gunakan rute file-manager agar file dapat diakses di Vercel
        if ($activeDisk === 'public') {
            return route('serve.file', ['path' => $path]);
        }

        // Untuk disk lain (r2, s3), gunakan Storage::disk()->url()
        /** @var \Illuminate\Contracts\Filesystem\Cloud $cloudDisk */
        $cloudDisk = Storage::disk($activeDisk);
        return $cloudDisk->url($path);
    }
}

if (! function_exists('upload_disk')) {
    /**
     * Mendapatkan nama disk aktif untuk upload file.
     * Shorthand untuk config('filesystems.default').
     *
     * @return string  Nama disk (e.g. 'public', 'r2', 's3')
     */
    function upload_disk(): string
    {
        return config('filesystems.default', 'public');
    }
}
