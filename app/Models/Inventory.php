<?php

namespace App\Models;

use App\Traits\ActivityTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inventory extends Model
{
    use HasFactory, ActivityTrait;

    protected $fillable = [
        'commodity_id',
        'unit_id',
        'amount',
        'purchase_price',
        'active'
    ];

    protected $casts = [
        'active' => 'boolean',
        'amount' => 'decimal:2',
        'purchase_price' => 'decimal:2'
    ];

    public function commodity()
    {
        return $this->belongsTo(Commodity::class);
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    public function scopeActive($query)
    {
        return $query->where('active', true);
    }

    public function scopeAvailable($query)
    {
        return $query->where('amount', '>', 0);
    }

    /**
     * Get the calculated sale price based on commodity's profit margin
     * For products: calculated from material costs + profit margin
     * For materials: null (materials don't have sale prices)
     *
     * @return float|null
     */
    public function getSalePriceAttribute()
    {
        if ($this->commodity->type === 'product') {
            // Use the commodity's calculated sales price
            return $this->commodity->sales_price;
        }
        
        // Materials don't have sale prices
        return null;
    }

    /**
     * Get the average purchase price for this inventory
     *
     * @return float|null
     */
    public function getAveragePurchasePriceAttribute()
    {
        return $this->purchase_price;
    }
}
