<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Sinkronisasi Data Pembimbing (Mahasiswa -> PendaftaranKp)
        $mahasiswas = DB::table('mahasiswa')->whereNotNull('pembimbing_id')->get();
        foreach ($mahasiswas as $mhs) {
            // Update KP individu atau kelompok terbaru yang dimiliki
            $kp = DB::table('pendaftaran_kp')
                ->where('mahasiswa_id', $mhs->user_id)
                ->orWhereJsonContains('anggota_kelompok_ids', (string) $mhs->user_id)
                ->orWhereJsonContains('anggota_kelompok_ids', $mhs->user_id)
                ->orderBy('created_at', 'desc')
                ->first();

            if ($kp && empty($kp->pembimbing_id)) {
                DB::table('pendaftaran_kp')->where('id', $kp->id)->update([
                    'pembimbing_id' => $mhs->pembimbing_id
                ]);
            }
        }

        // 2. Drop columns
        Schema::table('mahasiswa', function (Blueprint $table) {
            $table->dropForeign(['pembimbing_id']);
            $table->dropColumn('pembimbing_id');
        });

        // Use raw statements to safely drop constraints if they exist
        // Since Postgres aborts transaction on exception, we just drop columns directly 
        // if we know there is no FK, or we drop the FK we are sure exists.
        
        Schema::table('pendaftaran_sidang', function (Blueprint $table) {
            $table->dropColumn('tahun_ajaran_id');
        });

        Schema::table('log_bimbingan', function (Blueprint $table) {
            $table->dropColumn('tahun_ajaran_id');
        });
    }

    public function down(): void
    {
        Schema::table('mahasiswa', function (Blueprint $table) {
            $table->unsignedBigInteger('pembimbing_id')->nullable();
            $table->foreign('pembimbing_id')->references('id')->on('users')->onDelete('set null');
        });

        Schema::table('pendaftaran_sidang', function (Blueprint $table) {
            $table->unsignedBigInteger('tahun_ajaran_id')->nullable();
        });

        Schema::table('log_bimbingan', function (Blueprint $table) {
            $table->unsignedBigInteger('tahun_ajaran_id')->nullable();
        });
        
        // Revert data logic
        $proposals = DB::table('pendaftaran_kp')->whereNotNull('pembimbing_id')->get();
        foreach ($proposals as $p) {
            DB::table('mahasiswa')->where('user_id', $p->mahasiswa_id)->update(['pembimbing_id' => $p->pembimbing_id]);
            if ($p->pengerjaan_kp === 'kelompok' || $p->pengerjaan_kp === 'berkelompok') {
                if (!empty($p->anggota_kelompok_ids)) {
                    $anggotaIds = is_string($p->anggota_kelompok_ids) ? json_decode($p->anggota_kelompok_ids, true) : $p->anggota_kelompok_ids;
                    if (is_array($anggotaIds)) {
                        foreach ($anggotaIds as $uid) {
                            DB::table('mahasiswa')->where('user_id', $uid)->update(['pembimbing_id' => $p->pembimbing_id]);
                        }
                    }
                }
            }
        }
    }
};
