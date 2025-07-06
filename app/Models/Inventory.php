<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inventory extends Model
{
    use HasFactory;

    protected $fillable = [
        'commodity_id',
        'unit_id',
        'amount',
        'purchase_price',
        'sale_price',
        'active'
    ];

    protected $casts = [
        'active' => 'boolean',
        'amount' => 'decimal:2',
        'purchase_price' => 'decimal:2',
        'sale_price' => 'decimal:2'
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

    public function scopeRawMaterials($query)
    {
        return $query->whereHas('commodity', function($q) {
            $q->where('type', 'raw_material');
        });
    }

    public function scopeProducts($query)
    {
        return $query->whereHas('commodity', function($q) {
            $q->where('type', 'product');
        });
    }

    public function scopeAvailable($query)
    {
        return $query->where('amount', '>', 0);
    }

    public function scopeByCommodityAndUnit($query, $commodityId, $unitId)
    {
        return $query->where('commodity_id', $commodityId)
                    ->where('unit_id', $unitId);
    }

    /**
     * Get total available quantity for a commodity and unit
     */
    public static function getTotalAvailableQuantity($commodityId, $unitId)
    {
        return static::byCommodityAndUnit($commodityId, $unitId)
                    ->available()
                    ->sum('amount');
    }

    /**
     * Get average purchase price for a commodity and unit
     */
    public static function getAveragePurchasePrice($commodityId, $unitId)
    {
        $inventory = static::byCommodityAndUnit($commodityId, $unitId)
                          ->available()
                          ->get();

        if ($inventory->isEmpty()) {
            return 0;
        }

        $totalValue = $inventory->sum(function ($item) {
            return $item->amount * $item->purchase_price;
        });

        $totalQuantity = $inventory->sum('amount');

        return $totalQuantity > 0 ? round($totalValue / $totalQuantity, 2) : 0;
    }

    /**
     * Consume inventory for a commodity and unit (single record)
     */
    public static function consume($commodityId, $unitId, $quantity)
    {
        $inventory = static::byCommodityAndUnit($commodityId, $unitId)
            ->available()
            ->first();

        if (!$inventory || $inventory->amount < $quantity) {
            throw new \RuntimeException("موجودی کافی برای کالای مورد نظر وجود ندارد");
        }

        $inventory->decrement('amount', $quantity);

        return [
            'inventory_id' => $inventory->id,
            'quantity' => $quantity,
            'purchase_price' => $inventory->purchase_price
        ];
    }
}