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
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function pembimbing()
    {
        // Karena pembimbing_id dipindah ke pendaftaran_kp, kita ambil dari pendaftaran terakhir
        return $this->hasOne(PendaftaranKp::class, 'mahasiswa_id', 'user_id')
            ->latestOfMany()
            ->withDefault();
    }
}
