<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClinicService extends Model
{
    protected $fillable = [
        'name',
        'price_per_visit',
        'package_3x_price',
        'package_6x_price',
        'package_12x_price',
        'category',
        'notes',
        'status',
    ];

    private const CATEGORY_ALIASES = [
        'program' => 'Program',
        'specialist' => 'Specialist',
        'add-on' => 'Add-on',
        'add on' => 'Add-on',
        'addon' => 'Add-on',
        'consultation' => 'Consultation',
        'konsultasi' => 'Consultation',
    ];

    /** Kunci pencocokan kategori: huruf kecil, spasi dirapikan, alias disamakan. */
    public static function canonicalCategoryKey(?string $category): string
    {
        $key = mb_strtolower(preg_replace('/\s+/', ' ', trim((string) $category)));

        return isset(self::CATEGORY_ALIASES[$key]) ? mb_strtolower(self::CATEGORY_ALIASES[$key]) : $key;
    }

    /** Nama kategori baku untuk disimpan; kategori yang tidak dikenal disimpan apa adanya (dirapikan). */
    public static function normalizeCategory(?string $category): ?string
    {
        $trimmed = trim((string) $category);

        if ($trimmed === '') {
            return null;
        }

        $key = mb_strtolower(preg_replace('/\s+/', ' ', $trimmed));

        return self::CATEGORY_ALIASES[$key] ?? $trimmed;
    }

    public function getStatusLabelAttribute(): string
    {
        return $this->status === 'active' ? 'Active' : 'Inactive';
    }
}
