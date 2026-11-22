<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class Room extends Model
{
    public const RATE_UNITS = [
        'per_session' => 'Per sesi',
        'per_hour' => 'Per jam',
        'per_day' => 'Per hari',
    ];

    protected $fillable = ['name', 'status', 'is_billable', 'rate', 'rate_unit', 'notes'];

    protected $casts = [
        'is_billable' => 'boolean',
        'rate' => 'decimal:2',
    ];

    public function getRateUnitLabelAttribute(): ?string
    {
        return $this->rate_unit ? (self::RATE_UNITS[$this->rate_unit] ?? $this->rate_unit) : null;
    }

    /** Ruangan berbayar yang tarif dan satuannya sudah lengkap (siap disarankan di kasir). */
    public function getIsChargeableAttribute(): bool
    {
        return $this->status === 'active' && $this->is_billable && (float) $this->rate > 0 && ! empty($this->rate_unit);
    }

    /** Potret field master yang memengaruhi penagihan, untuk jejak audit. */
    public static function auditSnapshot(self $room): array
    {
        return [
            'name' => $room->name,
            'status' => $room->status,
            'is_billable' => $room->is_billable ? '1' : '0',
            'rate' => $room->rate === null ? '' : number_format((float) $room->rate, 2, '.', ''),
            'rate_unit' => (string) $room->rate_unit,
        ];
    }

    public static function findByName(?string $name): ?self
    {
        $name = trim((string) $name);

        return $name === '' ? null : static::whereRaw('LOWER(name) = ?', [mb_strtolower($name)])->first();
    }

    /**
     * Daftar nama ruangan untuk dropdown booking/visit: ruangan aktif dari master, ditambah nilai
     * yang sedang tersimpan agar data lama tidak hilang. Bila master kosong, memakai daftar bawaan.
     *
     * @return array<int, string>
     */
    public static function options(?string $current = null): array
    {
        $names = static::where('status', 'active')->orderBy('id')->pluck('name')->all();

        if ($names === []) {
            $names = ['VIP 1', 'VIP 2', 'R.1.1', 'R.2.1', 'R.2.2', 'R.2.3', 'R.2.4', 'R.2.5', 'R. GYM', 'R. Studio Pilates'];
        }

        if (filled($current) && ! in_array($current, $names, true)) {
            $names[] = $current;
        }

        return $names;
    }

    /**
     * Validasi input master ruangan. Ruangan berbayar wajib punya tarif dan satuan (tidak ada default).
     *
     * @return array<string, mixed>
     */
    public static function validated(Request $request, ?self $room): array
    {
        $rules = [
            'status' => 'required|in:active,inactive',
            'is_billable' => 'nullable|boolean',
            'rate' => 'nullable|numeric|min:0|max:100000000',
            'rate_unit' => 'nullable|in:'.implode(',', array_keys(self::RATE_UNITS)),
            'notes' => 'nullable|string|max:1000',
        ];

        if (! $room) {
            $rules['name'] = 'required|string|max:100|unique:rooms,name';
        }

        $data = $request->validate($rules);
        $data['is_billable'] = (bool) $request->boolean('is_billable');

        if ($data['is_billable'] && ((float) ($data['rate'] ?? 0) <= 0 || empty($data['rate_unit']))) {
            throw ValidationException::withMessages([
                'rate' => 'Ruangan berbayar wajib memiliki tarif lebih dari 0 dan satuan tarif (tidak ada default).',
            ]);
        }

        return $data;
    }
}
