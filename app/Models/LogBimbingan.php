<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LogBimbingan extends Model
{
    use HasFactory;

    protected $table = 'log_bimbingan';

    protected $fillable = [
        'pendaftaran_kp_id',
        'mahasiswa_id',
        'tanggal',
        'materi_bahasan',
        'file_progress',
        'status_approval',
        'komentar_dosen',
        'is_supervisor',
    ];

    protected $casts = [
        'tanggal' => 'date',
        // 'materi_bahasan' => 'array', // Uncomment if we store JSON string
    ];

    public function pendaftaranKp()
    {
        return $this->belongsTo(PendaftaranKp::class, 'pendaftaran_kp_id');
    }
}
