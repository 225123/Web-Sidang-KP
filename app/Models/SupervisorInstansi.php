<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SupervisorInstansi extends Model
{
    use HasFactory;

    protected $table = 'supervisor_instansi';

    public $timestamps = false;

    protected $fillable = [
        'pendaftaran_kp_id',
        'nama_supervisor',
        'kontak_supervisor',
        'no_hp_supervisor',
        'email_supervisor',
        'jabatan_supervisor',
    ];

    public function pendaftaranKp()
    {
        return $this->belongsTo(PendaftaranKp::class, 'pendaftaran_kp_id');
    }
}
