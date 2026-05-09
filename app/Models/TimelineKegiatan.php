<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TimelineKegiatan extends Model
{
    use HasFactory;

    protected $table = 'timeline_kegiatan';

    protected $fillable = [
        'periode_id',
        'nama_kegiatan',
        'tanggal',
        'waktu',
        'kategori',
        'keterangan',
    ];

    public function periode()
    {
        return $this->belongsTo(TahunAjaran::class, 'periode_id');
    }

    protected $casts = [
        'tanggal' => 'date',
    ];
}
