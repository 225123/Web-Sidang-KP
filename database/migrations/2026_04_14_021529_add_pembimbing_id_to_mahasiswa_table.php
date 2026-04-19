<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('mahasiswa', function (Blueprint $table) {
            $table->unsignedBigInteger('pembimbing_id')->nullable();
            // Since Dosen isn't a dedicated table but a 'users' role, constraint points to 'users'
            $table->foreign('pembimbing_id')->references('id')->on('users')->onDelete('set null');
        });

        // Data migration logic to prevent pollution/loss of existing Dosen mappings:
        // We iterate through all existing valid 'pendaftaran_kp' records and assign the pembimbing
        // down to all its associated mahasiswa_id and anggota_kelompok_ids.
        $proposals = \DB::table('pendaftaran_kp')->whereNotNull('pembimbing_id')->get();
        foreach ($proposals as $p) {
            // Assign to the main proponent
            \DB::table('mahasiswa')->where('user_id', $p->mahasiswa_id)->update(['pembimbing_id' => $p->pembimbing_id]);
            
            // Assign to all members if it's a group
            if ($p->pengerjaan_kp === 'kelompok' || $p->pengerjaan_kp === 'berkelompok') {
                if (!empty($p->anggota_kelompok_ids)) {
                    $anggotaIds = is_string($p->anggota_kelompok_ids) ? json_decode($p->anggota_kelompok_ids, true) : $p->anggota_kelompok_ids;
                    if (is_array($anggotaIds)) {
                        foreach ($anggotaIds as $uid) {
                            \DB::table('mahasiswa')->where('user_id', $uid)->update(['pembimbing_id' => $p->pembimbing_id]);
                        }
                    }
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mahasiswa', function (Blueprint $table) {
            $table->dropForeign(['pembimbing_id']);
            $table->dropColumn('pembimbing_id');
        });
    }
};
