<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InventoryStockMovement extends Model
{
    protected $fillable = [
        'inventory_item_id',
        'voided_billing_id',
        'billing_id',
        'type',
        'quantity',
        'stock_before',
        'stock_after',
        'reference',
        'notes',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'stock_before' => 'integer',
        'stock_after' => 'integer',
    ];

    public function item()
    {
        return $this->belongsTo(InventoryItem::class, 'inventory_item_id');
    }


    public function billing()
    {
        return $this->belongsTo(Billing::class);
    }

    public function getTypeLabelAttribute()
    {
        return match ($this->type) {
            'in' => 'Stok Masuk',
            'out' => 'Stok Keluar',
            'void_return' => 'Void Return',
            default => 'Adjustment',
        };
    }

    public function inventoryItem()
    {
        return $this->belongsTo(InventoryItem::class);
    }

}