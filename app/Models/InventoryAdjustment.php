<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InventoryAdjustment extends Model
{
    use HasFactory;

    protected $fillable = [
        'commodity_id',
        'unit_id',
        'adjustment_type',
        'amount',
        'reason',
        'adjustable_type',
        'adjustable_id',
        'user_id',
        'value_before',
        'value_after',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'value_before' => 'decimal:2',
        'value_after' => 'decimal:2',
    ];

    /**
     * Get the commodity for this adjustment
     */
    public function commodity()
    {
        return $this->belongsTo(Commodity::class);
    }

    /**
     * Get the unit for this adjustment
     */
    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    /**
     * Get the user who created this adjustment
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the related model (WithdrawalRequest, Order, etc.)
     */
    public function adjustable()
    {
        return $this->morphTo();
    }

    /**
     * Get adjustments for a specific withdrawal request
     */
    public function scopeForWithdrawal($query, $withdrawalId)
    {
        return $query->where('adjustable_type', WithdrawalRequest::class)
            ->where('adjustable_id', $withdrawalId);
    }

    /**
     * Get adjustments for a specific production request
     */
    public function scopeForProduction($query, $productionId)
    {
        return $query->where('adjustable_type', ProductionRequest::class)
            ->where('adjustable_id', $productionId);
    }

    /**
     * Get only removal adjustments (negative amounts)
     */
    public function scopeRemovals($query)
    {
        return $query->whereRaw('amount < 0');
    }

    /**
     * Get only addition adjustments (positive amounts)
     */
    public function scopeAdditions($query)
    {
        return $query->whereRaw('amount > 0');
    }

    /**
     * Scope to get adjustments by type
     */
    public function scopeByType($query, $type)
    {
        return $query->where('adjustment_type', $type);
    }
}
