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

    // Menjadikan NIDN sebagai Primary Key
    protected $primaryKey = 'nidn';
    public $incrementing = false;
    protected $keyType = 'string';

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
