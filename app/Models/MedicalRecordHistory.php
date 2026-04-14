<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MedicalRecordHistory extends Model
{
    protected $fillable = [
        'medical_record_id',
        'history_type',
        'history_note',
        'history_date',
    ];

    protected $casts = [
        'history_date' => 'date',
    ];

    public function medicalRecord()
    {
        return $this->belongsTo(MedicalRecord::class);
    }
}