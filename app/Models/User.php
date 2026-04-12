<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Disable the default updated_at since it's not in the PostgreSQL schema.
     */
    const UPDATED_AT = null;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'avatar',
        'signature_path',
        'reset_token',
        'reset_token_expires',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'reset_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'reset_token_expires' => 'datetime',
        ];
    }
    
    // Disable remember token as it's not in the schema
    public function getRememberTokenName()
    {
        return '';
    }

    // Relationships
    public function mahasiswa()
    {
        return $this->hasOne(Mahasiswa::class);
    }
    
    public function dosen()
    {
        return $this->hasOne(Dosen::class);
    }

    protected static function booted()
    {
        static::deleting(function ($user) {
            $user->mahasiswa()->delete();
            $user->dosen()->delete();
        });
    }
}
