<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BackupHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'koordinator_id',
        'tahun_ajaran_id',
        'periode_name',
        'file_name',
    ];

    public function koordinator()
    {
        return $this->belongsTo(User::class, 'koordinator_id');
    }

    public function tahunAjaran()
    {
        return $this->belongsTo(TahunAjaran::class, 'tahun_ajaran_id');
    }
}
