<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Root cause fix:
     * Kolom status_koordinator punya default 'Pending' (kapital P) di DB.
     * Seluruh controller code menggunakan 'pending' (lowercase) dan 'unsubmitted'.
     * Akibatnya, record baru dari PersetujuanSidangController (persetujuan dosen, bukan
     * upload berkas final) otomatis mendapat status_koordinator = 'Pending' dari DB default,
     * yang lolos filter `!= 'unsubmitted'` dan muncul di tabel Verifikasi Berkas Koordinator
     * dengan isian berkas yang kosong/cacat.
     *
     * Fix:
     * 1. Ubah default kolom dari 'Pending' ke 'unsubmitted'.
     * 2. Patch data legacy: semua record dengan status_koordinator = 'Pending' (kapital)
     *    yang tidak memiliki file_laporan dari koordinator (artinya belum benar-benar disubmit)
     *    diubah ke 'unsubmitted'.
     */
    public function up(): void
    {
        // 1. Ubah default kolom ke 'unsubmitted'
        Schema::table('pendaftaran_sidang', function (Blueprint $table) {
            $table->string('status_koordinator')->default('unsubmitted')->change();
        });

        // 2. Patch data legacy:
        //    'Pending' (kapital, DB default) yang berkas koordinatornya masih kosong
        //    → ubah ke 'unsubmitted'
        DB::table('pendaftaran_sidang')
            ->where('status_koordinator', 'Pending')
            ->whereNull('file_laporan')   // file_laporan wajib diisi saat submit ke koordinator
            ->update(['status_koordinator' => 'unsubmitted']);

        // 3. Jika ada 'Pending' (kapital) yang sudah punya file (edge case sangat langka),
        //    normalkan ke lowercase 'pending' agar konsisten dengan seluruh code
        DB::table('pendaftaran_sidang')
            ->where('status_koordinator', 'Pending')
            ->whereNotNull('file_laporan')
            ->update(['status_koordinator' => 'pending']);
    }

    public function down(): void
    {
        // Kembalikan default ke semula (hanya jika rollback diperlukan)
        Schema::table('pendaftaran_sidang', function (Blueprint $table) {
            $table->string('status_koordinator')->default('Pending')->change();
        });
    }
};
