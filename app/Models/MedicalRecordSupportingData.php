<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MedicalRecordSupportingData extends Model
{
    protected $fillable = [
        'medical_record_id',
        'data_date',
        'data_type',
        'interpretation',
    ];

    protected $casts = [
        'data_date' => 'date',
    ];

    public function medicalRecord()
    {
        return $this->belongsTo(MedicalRecord::class);
    }
}