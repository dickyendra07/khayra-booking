<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Patient extends Model
{
    protected $fillable = [
        'full_name',
        'gender',
        'birth_date',
        'whatsapp',
        'address',
    ];

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    public function visits()
    {
        return $this->hasMany(Visit::class);
    }

    public function billings()
    {
        return $this->hasMany(Billing::class);
    }
}