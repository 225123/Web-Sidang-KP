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
        'tanggal_sidang',
        'waktu_mulai_sidang',
        'waktu_selesai_sidang',
        'ruang_sidang',
        'status_jadwal',
        'penguji_1_id',
        'penguji_2_id',
        'nilai_pembimbing',
        'nilai_penguji_1',
        'nilai_penguji_2',
        'nilai_akhir',
        'grade',
        'catatan_sidang',
        'status_kelulusan',
        'pelaksanaan',
        'nb_laporan',
        'nb_produk',
        'nb_sikap',
        'n1_laporan',
        'n1_produk',
        'n1_presentasi',
        'n2_laporan',
        'n2_produk',
        'n2_presentasi',
        'ns_motivasi',
        'ns_kualitas',
        'ns_inisiatif',
        'ns_sikap',
        'nilai_supervisor',
        'file_revisi',
        'link_revisi',
        'status_revisi',
        'tanggal_revisi',
        'berita_acara_disubmit',
        'nilai_dipublikasi',
    ];

    public function pendaftaranKp()
    {
        return $this->belongsTo(PendaftaranKp::class, 'pendaftaran_kp_id');
    }

    public function mahasiswa()
    {
        return $this->belongsTo(Mahasiswa::class, 'mahasiswa_id', 'user_id');
    }

    public function penguji1()
    {
        return $this->belongsTo(User::class, 'penguji_1_id');
    }

    public function penguji2()
    {
        return $this->belongsTo(User::class, 'penguji_2_id');
    }
}
