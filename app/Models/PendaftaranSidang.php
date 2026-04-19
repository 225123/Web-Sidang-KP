<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PendaftaranSidang extends Model
{
    // Arahkan ke nama tabel yang benar di database
    protected $table = 'pendaftaran_sidang';

    // Matikan timestamps karena di skema SQL Anda tabel ini tidak punya created_at & updated_at
    public $timestamps = false;

    // Daftarkan kolom-kolom yang boleh diisi (mass assignable)
    protected $fillable = [
        'pendaftaran_kp_id',
        'mahasiswa_id',
        'file_laporan',
        'file_log_bimbingan',
        'file_persetujuan_pembimbing',
        'file_nilai_supervisor',
        'file_berkas_lainnya',
        'link_github',
        'link_drive',
        'link_deploy',
        'status_verifikasi',
        'status_koordinator',
        'koordinator_feedback',
        'dosen_feedback',
        'tahun_ajaran_id',
    ];

    public function pendaftaranKp()
    {
        return $this->belongsTo(PendaftaranKp::class, 'pendaftaran_kp_id');
    }

    public function mahasiswa()
    {
        return $this->belongsTo(Mahasiswa::class, 'mahasiswa_id', 'user_id');
    }
}