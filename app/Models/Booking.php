<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    protected $fillable = [
        'patient_id',
        'full_name',
        'whatsapp',
        'service',
        'booking_date',
        'booking_time',
        'complaint',
        'status',
    ];

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function visits()
    {
        return $this->hasMany(Visit::class);
    }
}