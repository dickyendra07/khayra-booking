<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TherapistLeaveRequest extends Model
{
    protected $fillable = [
        'therapist_id',
        'start_date',
        'end_date',
        'leave_type',
        'reason',
        'status',
        'admin_note',
        'reviewed_at',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'reviewed_at' => 'datetime',
    ];

    public function therapist()
    {
        return $this->belongsTo(Therapist::class);
    }
}
