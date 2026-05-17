<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InventoryItem extends Model
{
    protected $fillable = [
        'sku',
        'name',
        'category',
        'unit',
        'stock',
        'minimum_stock',
        'purchase_price',
        'selling_price',
        'supplier',
        'storage_location',
        'status',
        'notes',
    ];

    protected $casts = [
        'purchase_price' => 'decimal:2',
        'selling_price' => 'decimal:2',
        'stock' => 'integer',
        'minimum_stock' => 'integer',
    ];

    public function movements()
    {
        return $this->hasMany(InventoryStockMovement::class);
    }

    public function getStockStatusAttribute()
    {
        if ($this->stock <= 0) {
            return 'empty';
        }

        if ($this->minimum_stock > 0 && $this->stock <= $this->minimum_stock) {
            return 'low';
        }

        return 'safe';
    }

    public function getStockStatusLabelAttribute()
    {
        return match ($this->stock_status) {
            'empty' => 'Habis',
            'low' => 'Menipis',
            default => 'Aman',
        };
    }
    public function stockMovements()
    {
        return $this->hasMany(InventoryStockMovement::class);
    }

}