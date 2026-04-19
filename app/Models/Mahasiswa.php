<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mahasiswa extends Model
{
    use HasFactory;

    // Define table name as standard schema does not use plural in this case
    protected $table = 'mahasiswa';
    
    // Disable timestamps since they are not in the schema level 1
    public $timestamps = false;

    // Menjadikan NIM sebagai Primary Key
    protected $primaryKey = 'nim';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'user_id',
        'nim',
        'prodi',
        'angkatan',
        'no_hp',
        'email',
        'pembimbing_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function pembimbing()
    {
        return $this->belongsTo(User::class, 'pembimbing_id');
    }
}
