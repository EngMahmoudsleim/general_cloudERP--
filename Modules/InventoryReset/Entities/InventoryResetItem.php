<?php

namespace Modules\InventoryReset\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Product;
use App\BusinessLocation;

class InventoryResetItem extends Model
{
    protected $fillable = [
        'inventory_reset_log_id',
        'product_id',
        'location_id', 
        'quantity_before',
        'quantity_after'
    ];

    protected $casts = [
        'quantity_before' => 'decimal:2',
        'quantity_after' => 'decimal:2'
    ];

    /**
     * Get the reset log
     */
    public function resetLog(): BelongsTo
    {
        return $this->belongsTo(InventoryResetLog::class, 'inventory_reset_log_id');
    }

    /**
     * Get the product
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get the location
     */
    public function location(): BelongsTo
    {
        return $this->belongsTo(BusinessLocation::class, 'location_id');
    }

    /**
     * Get quantity difference
     */
    public function getQuantityDifferenceAttribute(): float
    {
        return $this->quantity_before - $this->quantity_after;
    }

    /**
     * Get formatted quantity before
     */
    public function getFormattedQuantityBeforeAttribute(): string
    {
        return number_format($this->quantity_before, 2);
    }

    /**
     * Get formatted quantity after
     */
    public function getFormattedQuantityAfterAttribute(): string
    {
        return number_format($this->quantity_after, 2);
    }
}