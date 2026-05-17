<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Promo extends Model
{
    protected $fillable = [
        'code',
        'name',
        'discount_type',
        'discount_value',
        'start_date',
        'end_date',
        'minimum_purchase',
        'maximum_discount',
        'status',
        'notes',
    ];

    protected $casts = [
        'discount_value' => 'decimal:2',
        'minimum_purchase' => 'decimal:2',
        'maximum_discount' => 'decimal:2',
        'start_date' => 'date',
        'end_date' => 'date',
    ];


    public function getAvailabilityStatusAttribute(): string
    {
        if ($this->status !== 'active') {
            return 'inactive';
        }

        $today = now()->toDateString();

        if ($this->start_date && $this->start_date->toDateString() > $today) {
            return 'upcoming';
        }

        if ($this->end_date && $this->end_date->toDateString() < $today) {
            return 'expired';
        }

        return 'active';
    }

    public function getAvailabilityLabelAttribute(): string
    {
        return match ($this->availability_status) {
            'active' => 'Active Now',
            'upcoming' => 'Upcoming',
            'expired' => 'Expired',
            default => 'Inactive',
        };
    }

    public function getAvailabilityClassAttribute(): string
    {
        return match ($this->availability_status) {
            'active' => 'active',
            'upcoming' => 'upcoming',
            'expired' => 'expired',
            default => 'inactive',
        };
    }

    public function getDiscountLabelAttribute(): string
    {
        if ($this->discount_type === 'percent') {
            return rtrim(rtrim(number_format($this->discount_value, 2, '.', ''), '0'), '.') . '%';
        }

        return 'Rp ' . number_format($this->discount_value, 0, ',', '.');
    }

    public function isAvailableForSubtotal(float $subtotal): bool
    {
        if ($this->status !== 'active') {
            return false;
        }

        if ($this->minimum_purchase > 0 && $subtotal < $this->minimum_purchase) {
            return false;
        }

        $today = now()->toDateString();

        if ($this->start_date && $this->start_date->toDateString() > $today) {
            return false;
        }

        if ($this->end_date && $this->end_date->toDateString() < $today) {
            return false;
        }

        return true;
    }

    public function calculateDiscount(float $subtotal): float
    {
        if (!$this->isAvailableForSubtotal($subtotal)) {
            return 0;
        }

        if ($this->discount_type === 'percent') {
            $discount = $subtotal * min((float) $this->discount_value, 100) / 100;
        } else {
            $discount = (float) $this->discount_value;
        }

        if ($this->maximum_discount > 0) {
            $discount = min($discount, (float) $this->maximum_discount);
        }

        return min($discount, $subtotal);
    }
}
