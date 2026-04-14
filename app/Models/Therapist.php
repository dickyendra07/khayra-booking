<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Therapist extends Model
{
    protected $fillable = [
        'full_name',
        'email',
        'whatsapp',
        'specialty',
        'password',
        'status',
    ];

    protected $hidden = [
        'password',
    ];

    public function visits()
    {
        return $this->hasMany(Visit::class);
    }

    public function createdMedicalRecords()
    {
        return $this->hasMany(MedicalRecord::class, 'created_by_therapist_id');
    }

    public function updatedMedicalRecords()
    {
        return $this->hasMany(MedicalRecord::class, 'updated_by_therapist_id');
    }
}