<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MedicalRecordComorbidity extends Model
{
    protected $fillable = [
        'medical_record_id',
        'name',
        'is_checked',
        'measurement_date',
        'final_value',
        'note',
    ];

    protected $casts = [
        'is_checked' => 'boolean',
        'measurement_date' => 'date',
    ];

    public function medicalRecord()
    {
        return $this->belongsTo(MedicalRecord::class);
    }
}