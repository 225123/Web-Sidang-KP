<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PendaftaranKp extends Model
{
    use HasFactory;

    protected $table = 'pendaftaran_kp';

    protected static function booted()
    {
        static::addGlobalScope('periode', function (\Illuminate\Database\Eloquent\Builder $builder) {
            if (request() && session()->has('selected_periode_id')) {
                $builder->where('pendaftaran_kp.tahun_ajaran_id', session('selected_periode_id'));
            }
        });
    }

    // We recently added updated_at via migration to support feature logic constraints

    protected $fillable = [
        'mahasiswa_id',
        'tahun_ajaran_id',
        'judul_kp',
        'jenis_proyek',
        'instansi_nama',
        'instansi_alamat',
        'pembimbing_id',
        'status_kp',
        'is_lanjutan',
        'pendaftaran_asal_id',
        'jenis_instansi',
        'supervisor_internal_id',
        'tipe_kp',
        'pengerjaan_kp',
        'anggota_kelompok_ids',
        'catatan',
    ];

    protected function casts(): array
    {
        return [
            'anggota_kelompok_ids' => 'array',
        ];
    }

    public function mahasiswa()
    {
        return $this->belongsTo(Mahasiswa::class, 'mahasiswa_id', 'user_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'mahasiswa_id');
    }

    public function supervisorInstansi()
    {
        return $this->hasOne(SupervisorInstansi::class, 'pendaftaran_kp_id');
    }

    public function pembimbing()
    {
        return $this->belongsTo(User::class, 'pembimbing_id');
    }

    public function supervisorInternal()
    {
        return $this->belongsTo(User::class, 'supervisor_internal_id');
    }

    public function logBimbingans()
    {
        return $this->hasMany(LogBimbingan::class, 'pendaftaran_kp_id');
    }

    public function pendaftaranSidang()
    {
        return $this->hasOne(PendaftaranSidang::class, 'pendaftaran_kp_id');
    }
}
