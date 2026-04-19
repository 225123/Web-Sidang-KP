<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LogBimbingan extends Model
{
    use HasFactory;

    protected $table = 'log_bimbingan';

    // Disable default timestamps if they don't exist in the schema, but wait, schemas usually omit them or have them.
    // Let's assume public.log_bimbingan doesn't have created_at/updated_at since the dump doesn't show them.
    public $timestamps = false;

    protected $fillable = [
        'pendaftaran_kp_id',
        'mahasiswa_id',
        'tanggal',
        'materi_bahasan',
        'file_progress',
        'status_approval',
        'komentar_dosen',
        'is_supervisor',
        'tahun_ajaran_id',
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
