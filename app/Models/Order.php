<?php

namespace App\Models;

use App\Services\BaseService;
use App\Traits\ActivityTrait;
use App\Traits\CommentTrait;
use App\Traits\FileTrait;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use HasFactory, ActivityTrait, CommentTrait, FileTrait, SoftDeletes;

    protected $fillable = [
        'customer_id',
        'deadline',
        'status',
    ];

    /**
     * Get the customer for this order
     */
    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    /**
     * Get the order items for this order
     */
    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Get the commodities for this order through order items
     */
    public function commodities()
    {
        return $this->belongsToMany(Commodity::class, 'order_items')
                    ->withPivot(['commodity_amount', 'unit', 'price'])
                    ->withTimestamps();
    }

    /**
     * Get the total amount in kilograms for all items
     */
    public function getTotalKgAmountAttribute()
    {
        $total = 0;
        foreach ($this->orderItems as $item) {
            $total += $item->kg_amount;
        }
        return $total;
    }

    /**
     * Get the total price for all items
     */
    public function getTotalPriceAttribute()
    {
        $total = 0;
        foreach ($this->orderItems as $item) {
            $total += $item->total_price;
        }
        return $total;
    }

    /**
     * Get the created date in Y-m-d format
     */
    public function getCreatedDateAttribute()
    {
        return $this->created_at->format('Y-m-d');
    }

    /**
     * Get the deadline difference in days
     */
    public function getDeadlineDiffAttribute()
    {
        $shamsi_date = $this->deadline;
        $date_arr = explode('/', $shamsi_date);
        $month = $date_arr[1];
        $day = $date_arr[2];

        if (strlen($date_arr[1]) < 2) {
            $month = '0' . $date_arr[1];
        }
        if (strlen($date_arr[2]) < 2) {
            $day = '0' . $date_arr[2];
        }
        $new_date = $date_arr[0] . '/' . $month . '/' . $day;
        $carbon_deadline = \Morilog\Jalali\Jalalian::fromFormat('Y/m/d', $new_date)->toCarbon();
        $now_date = Carbon::now();
        return $carbon_deadline->diffInDays($now_date);
    }

    /**
     * Get the main unit amount for each order item
     * This is a computed attribute that calculates the equivalent amount in the main unit
     */
    public function getMainUnitAmountAttribute()
    {
        $commodityUnitService = app(\App\Services\CommodityUnitService::class);
        $mainUnitAmounts = [];
        
        foreach ($this->orderItems as $item) {
            $commodity = $item->commodity;
            if (!$commodity) {
                continue;
            }
            
            // Get the unit from the relationship
            $unit = $item->unit;
            if (!$unit) {
                continue;
            }
            
            $amountInMainUnit = $commodityUnitService->convertToMainUnit(
                $commodity,
                $item->commodity_amount,
                $unit->id
            );
            
            $mainUnitAmounts[$item->id] = [
                'commodity_title' => $commodity->title,
                'original_amount' => $item->commodity_amount,
                'original_unit' => $unit->symbol,
                'main_unit_amount' => $amountInMainUnit,
                'main_unit_name' => $commodity->unit ? $commodity->unit->name : 'نامشخص',
                'main_unit_symbol' => $commodity->unit ? $commodity->unit->symbol : '',
            ];
        }
        
        return $mainUnitAmounts;
    }

    /**
     * Get formatted total amount with units for display
     */
    public function getFormattedTotalAmountAttribute()
    {
        if ($this->orderItems->count() === 0) {
            return '0';
        }

        // If all items have the same unit, show total with that unit
        $units = $this->orderItems->pluck('unit_id')->unique();
        if ($units->count() === 1) {
            $unit = $this->orderItems->first()->unit;
            $total = $this->orderItems->sum('commodity_amount');
            return number_format($total) . ' ' . ($unit ? $unit->symbol : 'نامشخص');
        }

        // If different units, show breakdown
        $breakdown = [];
        foreach ($this->orderItems as $item) {
            $unitSymbol = $item->unit ? $item->unit->symbol : 'نامشخص';
            $breakdown[] = number_format($item->commodity_amount) . ' ' . $unitSymbol;
        }
        return implode(' + ', $breakdown);
    }

    /**
     * Get the primary unit for this order (most common unit)
     */
    public function getPrimaryUnitAttribute()
    {
        if ($this->orderItems->count() === 0) {
            return null;
        }

        // Get the most common unit
        $unitCounts = $this->orderItems->groupBy('unit_id')->map->count();
        $mostCommonUnitId = $unitCounts->keys()->sortByDesc(function ($unitId) use ($unitCounts) {
            return $unitCounts[$unitId];
        })->first();

        return $this->orderItems->firstWhere('unit_id', $mostCommonUnitId)->unit;
    }
}
