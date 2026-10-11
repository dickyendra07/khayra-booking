<?php

namespace App\Models;

use App\Services\BillingCalculator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BillingItem extends Model
{
    protected $fillable = [
        'billing_id',
        'inventory_item_id',
        'clinic_service_id',
        'item_type',
        'item_category',
        'description',
        'quantity',
        'unit_price',
        'line_total',
        'is_discount_eligible',
        'discount_amount',
        'notes',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'unit_price' => 'decimal:2',
        'line_total' => 'decimal:2',
        'is_discount_eligible' => 'boolean',
        'discount_amount' => 'decimal:2',
    ];

    /**
     * Category label for display. Legacy rows (no category) return null.
     */
    public function getCategoryLabelAttribute(): ?string
    {
        return $this->item_category ? BillingCalculator::categoryLabel($this->item_category) : null;
    }

    public function billing(): BelongsTo
    {
        return $this->belongsTo(Billing::class);
    }

    public function inventoryItem(): BelongsTo
    {
        return $this->belongsTo(InventoryItem::class);
    }
}
