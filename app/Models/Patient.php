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
        'referral_source',
        'referral_source_other',
        'documentation_consent',
        'documentation_consent_notes',
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

    public function informedConsents(): HasMany
    {
        return $this->hasMany(InformedConsent::class);
    }

    public function generateMedicalRecordNumber(): ?string
    {
        if (!empty($this->medical_record_number)) {
            return $this->medical_record_number;
        }

        if (empty($this->gender)) {
            return null;
        }

        $branchCode = 'B'; // B = Bandung
        $gender = strtolower((string) $this->gender);

        $genderCode = match ($gender) {
            'female' => '01',
            'male' => '02',
            default => null,
        };

        if (!$genderCode) {
            return null;
        }

        $lastNumber = self::whereNotNull('medical_record_number')
            ->where('medical_record_number', 'like', "KP-{$branchCode}-{$genderCode}-%")
            ->get()
            ->map(function ($patient) {
                if (preg_match('/^KP-[A-Z]+-[0-9]{2}-([0-9]+)$/', (string) $patient->medical_record_number, $matches)) {
                    return (int) $matches[1];
                }

                return 0;
            })
            ->max();

        $nextNumber = ((int) $lastNumber) + 1;
        $runningNumber = str_pad((string) $nextNumber, 5, '0', STR_PAD_LEFT);

        return "KP-{$branchCode}-{$genderCode}-{$runningNumber}";
    }
    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }


}