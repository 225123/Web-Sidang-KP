<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RiwayatPenolakanSidang extends Model
{
    use HasFactory;

    protected $fillable = [
        'pendaftaran_sidang_id',
        'mahasiswa_id',
        'feedback',
        'ditolak_oleh',
    ];

    public function pendaftaranSidang()
    {
        return $this->belongsTo(PendaftaranSidang::class, 'pendaftaran_sidang_id');
    }

    public function mahasiswa()
    {
        return $this->belongsTo(Mahasiswa::class, 'mahasiswa_id', 'user_id');
    }
}
