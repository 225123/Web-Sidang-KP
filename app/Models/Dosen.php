<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Dosen extends Model
{
    use HasFactory;

    protected $table = 'dosen';
    
    // Disable timestamps since they are not in the schema level 1
    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'nidn',
        'kuota_bimbingan',
        'is_aktif',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
