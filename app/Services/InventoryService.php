<?php

namespace App\Services;

use App\Models\Inventory;
use App\Models\Commodity;
use App\Models\Unit;
use Illuminate\Support\Facades\DB;

class InventoryService extends BaseService
{
    /**
     * Add stock to inventory (for purchases/imports)
     */
    public function addStock($commodityId, $unitId, $amount, $purchasePrice = null, $salePrice = null)
    {
        $inventory = Inventory::where('commodity_id', $commodityId)
            ->where('unit_id', $unitId)
            ->where('active', true)
            ->first();

        if ($inventory) {
            // Update existing inventory
            $newAmount = $inventory->amount + $amount;
            
            $inventory->update([
                'amount' => $newAmount,
                'purchase_price' => $purchasePrice ?? $inventory->purchase_price,
                'sale_price' => $salePrice ?? $inventory->sale_price,
            ]);
            
            return $inventory;
        } else {
            // Create new inventory record
            return Inventory::create([
                'commodity_id' => $commodityId,
                'unit_id' => $unitId,
                'amount' => $amount,
                'purchase_price' => $purchasePrice,
                'sale_price' => $salePrice,
                'active' => true,
            ]);
        }
    }



    /**
     * Remove stock from inventory (for sales/withdrawals)
     */
    public function removeStock($commodityId, $unitId, $amount)
    {
        $inventory = Inventory::where('commodity_id', $commodityId)
            ->where('unit_id', $unitId)
            ->where('active', true)
            ->where('amount', '>=', $amount)
            ->first();

        if (!$inventory) {
            throw new \Exception('موجودی کافی برای کالای مورد نظر وجود ندارد');
        }

        $newAmount = $inventory->amount - $amount;
        
        if ($newAmount == 0) {
            $inventory->update(['active' => false]);
        } else {
            $inventory->update(['amount' => $newAmount]);
        }

        return $inventory;
    }

    /**
     * Get current stock level for a commodity and unit
     */
    public function getStockLevel($commodityId, $unitId)
    {
        return Inventory::where('commodity_id', $commodityId)
            ->where('unit_id', $unitId)
            ->where('active', true)
            ->sum('amount');
    }

    /**
     * Get all active inventory items
     */
    public function getActiveInventory()
    {
        return Inventory::with(['commodity', 'unit'])
            ->where('active', true)
            ->where('amount', '>', 0)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Get all inventory items (including inactive)
     */
    public function getAllInventory()
    {
        return Inventory::with(['commodity', 'unit'])
            ->orderBy('created_at', 'desc')
            ->get();
    }



    /**
     * Check if there's enough stock for withdrawal
     */
    public function hasEnoughStock($commodityId, $unitId, $requiredAmount)
    {
        $availableStock = $this->getStockLevel($commodityId, $unitId);
        return $availableStock >= $requiredAmount;
    }

    /**
     * Get inventory summary for dashboard
     */
    public function getInventorySummary()
    {
        return Inventory::with(['commodity', 'unit'])
            ->where('active', true)
            ->where('amount', '>', 0)
            ->selectRaw('
                commodity_id,
                unit_id,
                SUM(amount) as total_amount,
                AVG(purchase_price) as avg_purchase_price,
                AVG(sale_price) as avg_sale_price
            ')
            ->groupBy('commodity_id', 'unit_id')
            ->get();
    }



    /**
     * Update inventory record
     */
    public function update($inventory, $data)
    {
        return DB::transaction(function () use ($inventory, $data) {
            $inventory->update([
                'commodity_id' => $data['commodity_id'],
                'unit_id' => $data['unit_id'],
                'amount' => $data['amount'],
                'purchase_price' => $data['purchase_price'],
                'sale_price' => $data['sale_price'],
            ]);
            
            return $inventory;
        });
    }

    /**
     * Delete inventory record
     */
    public function delete($inventory)
    {
        return DB::transaction(function () use ($inventory) {
            $inventory->update(['active' => false]);
            return $inventory;
        });
    }

    /**
     * Manual stock adjustment
     */
    public function adjustStock($inventory, $data)
    {
        return DB::transaction(function () use ($inventory, $data) {
            $adjustmentType = $data['adjustment_type'];
            $quantity = $data['quantity'];
            $reason = $data['reason'];

            if ($adjustmentType === 'add') {
                $newAmount = $inventory->amount + $quantity;
            } else {
                $newAmount = $inventory->amount - $quantity;
                if ($newAmount < 0) {
                    throw new \Exception('مقدار موجودی نمی‌تواند منفی باشد');
                }
            }

            $inventory->update([
                'amount' => $newAmount,
                'active' => $newAmount > 0
            ]);

            // Log the adjustment
            $this->logStockAdjustment($inventory, $adjustmentType, $quantity, $reason);

            return $inventory;
        });
    }

    /**
     * Manual price adjustment
     */
    public function adjustPrice($inventory, $data)
    {
        return DB::transaction(function () use ($inventory, $data) {
            $newPrice = $data['new_price'];
            $reason = $data['reason'];

            $inventory->update([
                'sale_price' => $newPrice
            ]);

            // Log the price adjustment
            $this->logPriceAdjustment($inventory, $newPrice, $reason);

            return $inventory;
        });
    }

    /**
     * Log stock adjustment for audit trail
     */
    private function logStockAdjustment($inventory, $adjustmentType, $quantity, $reason)
    {
        // Use the existing ActivityTrait system with 'update' action
        $reason = $reason ?: 'بدون دلیل';
        $inventory->activities()->create([
            'user_id' => auth()->user()->id,
            'action' => 'update',
            'data' => json_encode([
                'adjustment_type' => 'stock_adjustment',
                'operation' => $adjustmentType,
                'quantity' => $quantity,
                'reason' => $reason,
                'old_amount' => $inventory->getOriginal('amount'),
                'new_amount' => $inventory->amount,
                'description' => "Stock adjustment: {$adjustmentType} {$quantity} units. Reason: {$reason}"
            ]),
        ]);
    }

    /**
     * Log price adjustment for audit trail
     */
    private function logPriceAdjustment($inventory, $newPrice, $reason)
    {
        // Use the existing ActivityTrait system with 'update' action
        $reason = $reason ?: 'بدون دلیل';
        $inventory->activities()->create([
            'user_id' => auth()->user()->id,
            'action' => 'update',
            'data' => json_encode([
                'adjustment_type' => 'price_adjustment',
                'old_price' => $inventory->getOriginal('sale_price'),
                'new_price' => $newPrice,
                'reason' => $reason,
                'description' => "Price adjustment: New price {$newPrice}. Reason: {$reason}"
            ]),
        ]);
    }
}
