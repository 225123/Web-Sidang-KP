<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TahunAjaran extends Model
{
    protected $table = 'tahun_ajaran';

    protected $fillable = [
        'semester',
        'tahun',
        'label_tahun_ajaran',
        'is_active',
        'tanggal_mulai',
        'tanggal_selesai',
        'keterangan',
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
}
