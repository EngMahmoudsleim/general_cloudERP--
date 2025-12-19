<?php

namespace Modules\InventoryReset\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\User;

class InventoryResetLog extends Model
{
    protected $fillable = [
        'business_id',
        'user_id',
        'reset_type',
        'reset_mode',
		'target_quantity',
        'location_id',
        'reason',
        'status',
        'items_reset',
        'error_message',
        'completed_at'
    ];

    protected $casts = [
        'completed_at' => 'datetime',
        'items_reset' => 'integer'
    ];

    /**
     * Get the user who performed the reset
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the reset items
     */
    public function resetItems(): HasMany
    {
        return $this->hasMany(InventoryResetItem::class, 'inventory_reset_log_id');
    }

    /**
     * Get the location if reset type is location
     */
    public function location(): BelongsTo
    {
        return $this->belongsTo(\App\BusinessLocation::class, 'location_id');
    }

    /**
     * Get formatted reset type
     */
    public function getFormattedResetTypeAttribute(): string
    {
        return match($this->reset_type) {
            'all_products' => __('inventoryreset::lang.all_products'),
            'selected_products' => __('inventoryreset::lang.selected_products_type'),
            default => $this->reset_type ?? __('inventoryreset::lang.all_products')
        };
    }

    /**
     * Get formatted reset mode
     */
    public function getFormattedResetModeAttribute(): string
    {
        return match($this->reset_mode) {
            'all_levels' => __('inventoryreset::lang.all_stock_levels'),
            'positive_only' => __('inventoryreset::lang.positive_stock_only'),
			'negative_only' => __('inventoryreset::lang.negative_stock_only'),
            'zero_only' => __('inventoryreset::lang.zero_stock_only'),
            default => $this->reset_mode ?? __('inventoryreset::lang.all_stock_levels')
        };
    }

    /**
     * Get formatted target quantity
     */
    public function getFormattedTargetQuantityAttribute(): string
    {
		$targetQuantity = ($this->target_quantity == 0) ? __('inventoryreset::lang.reset_to_zero') : __('inventoryreset::lang.set_to_quantity');
        return $targetQuantity;
    }

    /**
     * Get status badge class
     */
    public function getStatusBadgeClassAttribute(): string
    {
        return match($this->status) {
            'completed' => 'label-success',
            'processing' => 'label-warning',
            'failed' => 'label-danger',
            default => 'label-default'
        };
    }

    /**
     * Business scoping - matches InventoryManagement pattern
     */
    public function scopeBusiness($query)
    {
        $business_id = request()->session()->get('user.business_id');
        return $query->where('business_id', $business_id);
    }

    /**
     * Scope for business (alternative method for explicit business_id)
     */
    public function scopeForBusiness($query, $business_id)
    {
        return $query->where('business_id', $business_id);
    }

    /**
     * Scope for completed resets
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }
}