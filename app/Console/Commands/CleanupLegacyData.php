<?php

namespace App\Console\Commands;

use App\Models\PendaftaranKp;
use Illuminate\Console\Command;

class CleanupLegacyData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:cleanup-legacy-data';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Memulai pembersihan data lama...');

        $kps = PendaftaranKp::all();
        $updatedCount = 0;

        foreach ($kps as $kp) {
            $changed = false;

            // 1. Standarisasi Pengerjaan KP (Menghapus format lama berkelompok / sendiri)
            $pengerjaan = strtolower($kp->pengerjaan_kp ?? '');
            if (in_array($pengerjaan, ['berkelompok / sendiri', 'sendiri/berkelompok', 'sendiri', 'individu', ''])) {
                $kp->pengerjaan_kp = 'individu';
                $changed = true;
            } elseif (in_array($pengerjaan, ['berkelompok', 'kelompok'])) {
                $kp->pengerjaan_kp = 'kelompok';
                if (! in_array($pengerjaan, ['kelompok'])) {
                    $changed = true;
                }
            }

            // Auto correct based on members presence
            if ($kp->pengerjaan_kp === 'kelompok' && empty($kp->anggota_kelompok_ids)) {
                $kp->pengerjaan_kp = 'individu';
                $changed = true;
            } elseif ($kp->pengerjaan_kp === 'individu' && ! empty($kp->anggota_kelompok_ids)) {
                $kp->pengerjaan_kp = 'kelompok';
                $changed = true;
            }

            // 2. Pembersihan Anggota Kelompok pada proposal lama yang berstatus DITOLAK
            // sesuai aturan baru bahwa pembubaran terjadi otomatis. Data lama masih tertinggal.
            if ($kp->status_kp === 'rejected' && ! empty($kp->anggota_kelompok_ids)) {
                $kp->anggota_kelompok_ids = null;
                $changed = true;
            }

            if ($changed) {
                $kp->save();
                $updatedCount++;
            }
        }

        $this->info("Pembersihan selesai! Berhasil merapikan $updatedCount row data legacy yang menyimpang.");
    }
}
