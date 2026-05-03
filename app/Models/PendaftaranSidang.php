<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PendaftaranSidang extends Model
{
    // Arahkan ke nama tabel yang benar di database
    protected $table = 'pendaftaran_sidang';

    // Timestamps diaktifkan — dibutuhkan oleh query latest() di beberapa controller
    public $timestamps = true;

    protected static function booted()
    {
        static::addGlobalScope('periode', function (\Illuminate\Database\Eloquent\Builder $builder) {
            if (request() && session()->has('selected_periode_id')) {
                // If the query is already selecting from pendaftaran_sidang without an explicit pendaftaranKp join,
                // we use whereHas. (This is generally safe).
                $builder->whereHas('pendaftaranKp', function ($query) {
                    // the pendaftaranKp global scope might already handle this, but explicitly doing it doesn't hurt
                    $query->withoutGlobalScope('periode')->where('pendaftaran_kp.tahun_ajaran_id', session('selected_periode_id'));
                });
            }
        });
    }

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
        'token_penilaian_supervisor',
        'is_penilaian_supervisor_submitted',
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
