<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $order_id
 * @property int $commodity_id
 * @property float $commodity_amount
 * @property int $unit_id
 * @property float|null $price
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 */

class OrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'commodity_id',
        'commodity_amount',
        'unit_id',
        'price',
        'discount_percentage',
    ];

    /**
     * Get the order that owns this item
     */
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Get the commodity for this item
     */
    public function commodity()
    {
        return $this->belongsTo(Commodity::class);
    }

    /**
     * Get the unit for this item
     */
    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    /**
     * Calculate the total price for this item
     */
    public function getTotalPriceAttribute()
    {
        return $this->price * $this->commodity_amount;
    }

    /**
     * Calculate the VAT amount for this item (dynamic rate for domestic products)
     */
    public function getVatAmountAttribute()
    {
        if ($this->commodity && $this->commodity->type === 'product') {
            return $this->total_price * vat_rate(); // vat_rate() returns decimal (0.1 for 10%)
        }
        return 0;
    }

    /**
     * Calculate the total price including VAT for this item
     */
    public function getTotalPriceWithVatAttribute()
    {
        return $this->total_price + $this->vat_amount;
    }

    /**
     * Calculate the unit price with VAT
     */
    public function getPriceWithVatAttribute()
    {
        if ($this->commodity && $this->commodity->type === 'product') {
            return $this->price * (1 + vat_rate()); // Add 10% VAT for products
        }
        return $this->price; // No VAT for materials
    }

    /**
     * Get the amount in kilograms
     */
    // public function getKgAmountAttribute()
    // {
    //     $baseService = new \App\Services\BaseService();
    //     return $baseService->calculateCommodityAmount($this->commodity_amount, $this->unit->symbol);
    // }

    /**
     * Get available units for this commodity
     */
    public function getAvailableUnitsAttribute()
    {
        if (!$this->commodity) {
            return [];
        }

        $units = [$this->commodity->unit];
        
        // Add converted units if they exist
        $conversions = $this->commodity->unitConversions;
        foreach ($conversions as $conversion) {
            $units[] = $conversion->toUnit;
        }

        return array_unique($units);
    }

    /**
     * Get formatted amount with unit
     */
    public function getFormattedAmountAttribute()
    {
        $unitSymbol = $this->unit && is_object($this->unit) ? $this->unit->symbol : 'نامشخص';
        return number_format($this->commodity_amount) . ' ' . $unitSymbol;
    }

    /**
     * Get the unit symbol for this item
     */
    public function getUnitSymbolAttribute()
    {
        return $this->unit && is_object($this->unit) ? $this->unit->symbol : 'نامشخص';
    }

    /**
     * Get the unit name for this item
     */
    public function getUnitNameAttribute()
    {
        return $this->unit && is_object($this->unit) ? $this->unit->name : 'نامشخص';
    }

    /**
     * Calculate the weight in kg for this order item
     */
    public function getWeightKgAttribute()
    {
        return calculate_weight($this->commodity, $this->commodity_amount, $this->unit_id);
    }

    /**
     * Get the original price before discount
     */
    public function getOriginalPriceAttribute()
    {
        $discount = $this->discount_percentage ?? 0;
        if ($discount > 0) {
            return $this->price / (1 - $discount / 100);
        }
        return $this->price;
    }

    /**
     * Calculate the discount amount in currency
     */
    public function getDiscountAmountAttribute()
    {
        $discount = $this->discount_percentage ?? 0;
        if ($discount > 0) {
            return $this->original_price - $this->price;
        }
        return 0;
    }
} 