<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Patient extends Model
{
    protected $fillable = [
        'full_name',
        'gender',
        'birth_date',
        'whatsapp',
        'address',
        'nik',
        'religion',
        'occupation',
        'education',
        'marital_status',
        'medical_record_number',
    ];

    protected $casts = [
        'birth_date' => 'date',
    ];

    public function visits(): HasMany
    {
        return $this->hasMany(Visit::class);
    }

    public function billings(): HasMany
    {
        return $this->hasMany(Billing::class);
    }

    public function generateMedicalRecordNumber(): ?string
    {
        if (!empty($this->medical_record_number)) {
            return $this->medical_record_number;
        }

        if (empty($this->gender) || empty($this->birth_date)) {
            return null;
        }

        $gender = strtolower((string) $this->gender);

        $genderCode = match ($gender) {
            'female' => '01',
            'male' => '02',
            default => null,
        };

        if (!$genderCode) {
            return null;
        }

        $birthYearTwoDigits = date('y', strtotime((string) $this->birth_date));

        $runningNumber = str_pad(
            (string) (self::whereNotNull('medical_record_number')->count() + 1),
            5,
            '0',
            STR_PAD_LEFT
        );

        return "KP-{$genderCode}-{$runningNumber}-{$birthYearTwoDigits}";
    }
}