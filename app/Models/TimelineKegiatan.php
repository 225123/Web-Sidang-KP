<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TimelineKegiatan extends Model
{
    use HasFactory;

    protected $table = 'timeline_kegiatan';

    protected $fillable = [
        'nama_kegiatan',
        'tanggal',
        'waktu',
        'kategori',
        'keterangan',
    ];

    protected $casts = [
        'tanggal' => 'date',
    ];
}
