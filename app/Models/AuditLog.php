<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    protected $table = 'audit_logs';
    public $timestamps = false; // Using only created_at via default

    protected $fillable = [
        'user_id',
        'role',
        'module',
        'action',
        'url',
        'method',
        'ip_address',
        'user_agent',
        'created_at',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
