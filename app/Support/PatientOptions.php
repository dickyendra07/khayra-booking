<?php

namespace App\Support;

use Illuminate\Validation\Rule;

/**
 * Controlled option lists for patient registration.
 *
 * The patients table stores plain strings (no schema change), so records
 * created before these dropdowns existed keep their free-text value. Such a
 * legacy value stays selectable and saveable on that patient's own edit form.
 */
class PatientOptions
{
    public const RELIGIONS = [
        'Islam',
        'Kristen',
        'Katolik',
        'Hindu',
        'Buddha',
        'Konghucu',
        'Lainnya',
    ];

    public const EDUCATIONS = [
        'Tidak/Belum sekolah',
        'SD',
        'SMP',
        'SMA/SMK',
        'Diploma (D1-D3)',
        'Sarjana (S1/D4)',
        'Magister (S2)',
        'Doktor (S3)',
    ];

    /**
     * Validation rule: optional, one of the options, or the patient's
     * existing stored value (so legacy data can be re-saved unchanged).
     */
    public static function rule(array $options, ?string $currentValue = null): array
    {
        $allowed = $options;

        if ($currentValue !== null && $currentValue !== '') {
            $allowed[] = $currentValue;
        }

        return ['nullable', 'string', 'max:100', Rule::in($allowed)];
    }
}
