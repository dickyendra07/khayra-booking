<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HomeExerciseTemplate extends Model
{
    protected $fillable = [
        'name',
        'category',
        'target_area',
        'difficulty',
        'instructions',
        'dosage',
        'video_url',
        'status',
    ];
}
