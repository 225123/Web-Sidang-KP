<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TahunAjaran extends Model
{
    use SoftDeletes;

    protected $table = 'tahun_ajaran';

    protected $fillable = [
        'semester',
        'tahun',
        'label_tahun_ajaran',
        'is_active',
        'tanggal_mulai',
        'tanggal_selesai',
        'keterangan',
        'koordinator_id',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'tanggal_mulai' => 'date',
        'tanggal_selesai' => 'date',
    ];

    /**
     * Get the currently active period.
     */
    public static function aktif(): ?self
    {
        return self::where('is_active', true)->first();
    }

    /**
     * Generate the next period label after the given one.
     * Format: Ganjil/Genap YYYY/YYYY
     */
    public static function generateNextPeriod(?self $last = null): array
    {
        if (!$last) {
            $semester = 'Ganjil';
            $startYear = now()->year;
        } elseif ($last->semester === 'Ganjil') {
            $semester = 'Genap';
            [$startYear] = explode('/', $last->tahun);
            $startYear = (int)$startYear;
        } else {
            $semester = 'Ganjil';
            $parts = explode('/', $last->tahun);
            $startYear = (int)($parts[1] ?? $parts[0]);
        }

        $tahun = $startYear . '/' . ($startYear + 1);
        $label = "$semester $tahun";

        return compact('semester', 'tahun', 'label');
    }
    /**
     * Scope to sort periods from newest to oldest.
     */
    public function scopeTerbaru($query)
    {
        // Menggunakan ORDER BY tahun DESC (format "YYYY/YYYY" — urutan leksikografis = numerik)
        // CASE WHEN adalah SQL standar, kompatibel SQLite & PostgreSQL
        return $query->orderBy('tahun', 'desc')
            ->orderByRaw("CASE WHEN semester = 'Genap' THEN 1 ELSE 0 END DESC");
    }
    public function pendaftaranKps()
    {
        return $this->hasMany(PendaftaranKp::class, 'tahun_ajaran_id');
    }

    public function koordinator()
    {
        return $this->belongsTo(User::class, 'koordinator_id');
    }
}
