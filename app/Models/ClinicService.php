<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClinicService extends Model
{
    protected $fillable = [
        'name',
        'price_per_visit',
        'package_3x_price',
        'package_6x_price',
        'package_12x_price',
        'category',
        'notes',
        'status',
    ];

    public function getStatusLabelAttribute(): string
    {
        return $this->status === 'active' ? 'Active' : 'Inactive';
    }
}
