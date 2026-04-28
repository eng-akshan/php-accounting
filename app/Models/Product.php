<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    protected $fillable = [
        'name', 'sku', 'description', 'category', 'unit',
        'price', 'cost', 'quantity', 'min_stock', 'is_active', 'created_by'
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'cost' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(InventoryTransaction::class);
    }

    public function isLowStock(): bool
    {
        return $this->quantity <= $this->min_stock;
    }

    public function hasStock(int $qty = 1): bool
    {
        return $this->quantity >= $qty;
    }

    public function reduceStock(int $qty): bool
    {
        if (!$this->hasStock($qty)) {
            return false;
        }
        $this->decrement('quantity', $qty);
        return true;
    }

    public function addStock(int $qty): void
    {
        $this->increment('quantity', $qty);
    }

    public function getValue(): float
    {
        return $this->quantity * $this->cost;
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeLowStock($query)
    {
        return $query->whereColumn('quantity', '<=', 'min_stock');
    }
}