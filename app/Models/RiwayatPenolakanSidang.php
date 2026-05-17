<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RiwayatPenolakanSidang extends Model
{
    use HasFactory;

    protected $table = 'riwayat_penolakan_sidang';

    protected $fillable = [
        'pendaftaran_sidang_id',
        'alasan_penolakan',
        'ditolak_oleh',
    ];

    protected $appends = ['mahasiswa', 'feedback'];

    public function getFeedbackAttribute()
    {
        return $this->alasan_penolakan;
    }

    public function pendaftaranSidang()
    {
        return $this->belongsTo(PendaftaranSidang::class, 'pendaftaran_sidang_id');
    }

    // Relasi mahasiswa diakses melalui pendaftaranSidang, karena tabel ini tidak menyimpan mahasiswa_id
    public function getMahasiswaAttribute()
    {
        return $this->pendaftaranSidang ? $this->pendaftaranSidang->mahasiswa : null;
    }
}
