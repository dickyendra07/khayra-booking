<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PatientProgressEntry extends Model
{
    protected $fillable = [
        'patient_id',
        'visit_id',
        'entry_date',
        'pain_scale',
        'rom_notes',
        'functional_goal',
        'progress_notes',
    ];

    protected $casts = [
        'entry_date' => 'date',
        'pain_scale' => 'integer',
    ];

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function visit()
    {
        return $this->belongsTo(Visit::class);
    }
}
