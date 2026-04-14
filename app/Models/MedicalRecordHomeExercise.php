<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MedicalRecordHomeExercise extends Model
{
    protected $fillable = [
        'medical_record_id',
        'exercise',
        'dosage',
        'note_caution',
    ];

    public function medicalRecord()
    {
        return $this->belongsTo(MedicalRecord::class);
    }
}