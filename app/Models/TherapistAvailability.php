<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TherapistAvailability extends Model
{
    protected $fillable = [
        'therapist_id',
        'valid_from',
        'valid_until',
        'day_of_week',
        'start_time',
        'end_time',
        'slot_duration_minutes',
        'capacity',
        'status',
        'notes',
    ];

    protected $casts = [
        'valid_from' => 'date',
        'valid_until' => 'date',
        'day_of_week' => 'integer',
        'slot_duration_minutes' => 'integer',
        'capacity' => 'integer',
    ];

    public function therapist()
    {
        return $this->belongsTo(Therapist::class);
    }

    public function getDayNameAttribute(): string
    {
        return match ((int) $this->day_of_week) {
            1 => 'Monday',
            2 => 'Tuesday',
            3 => 'Wednesday',
            4 => 'Thursday',
            5 => 'Friday',
            6 => 'Saturday',
            7 => 'Sunday',
            default => '-',
        };
    }

    public function getStatusLabelAttribute(): string
    {
        return $this->status === 'active' ? 'Active' : 'Inactive';
    }
}
