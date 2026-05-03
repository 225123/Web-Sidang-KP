<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LogBimbingan extends Model
{
    use HasFactory;

    protected $table = 'log_bimbingan';

    protected static function booted()
    {
        static::addGlobalScope('periode', function (\Illuminate\Database\Eloquent\Builder $builder) {
            if (request() && session()->has('selected_periode_id')) {
                $builder->whereHas('pendaftaranKp', function ($query) {
                    $query->withoutGlobalScope('periode')->where('pendaftaran_kp.tahun_ajaran_id', session('selected_periode_id'));
                });
            }
        });
    }

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
