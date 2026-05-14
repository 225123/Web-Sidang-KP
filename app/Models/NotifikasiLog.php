<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NotifikasiLog extends Model
{
    use HasFactory;

    protected static function booted()
    {
        static::creating(function ($notifikasi) {
            if (!$notifikasi->periode_id) {
                $notifikasi->periode_id = session('selected_periode_id') ?? \App\Models\TahunAjaran::aktif()?->id ?? null;
            }
        });
    }

    protected $fillable = [
        'sender_id',
        'receiver_id',
        'target_role',
        'judul',
        'pesan',
        'file_path',
        'target_url',
        'is_read',
        'periode_id',
    ];

    protected function casts(): array
    {
        return [
            'is_read' => 'boolean',
        ];
    }

    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function receiver()
    {
        return $this->belongsTo(User::class, 'receiver_id');
    }
}
