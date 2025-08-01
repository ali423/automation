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
        // Get total available stock for this commodity and unit
        $totalStock = $this->getStockLevel($commodityId, $unitId);
        
        if ($totalStock < $amount) {
            throw new \Exception('موجودی کافی برای کالای مورد نظر وجود ندارد');
        }

        // Get all active inventory records for this commodity and unit, ordered by creation date (FIFO)
        $inventories = Inventory::where('commodity_id', $commodityId)
            ->where('unit_id', $unitId)
            ->where('active', true)
            ->where('amount', '>', 0)
            ->orderBy('created_at', 'asc')
            ->get();

        $remainingAmount = $amount;

        foreach ($inventories as $inventory) {
            if ($remainingAmount <= 0) {
                break;
            }

            $availableInThisRecord = $inventory->amount;
            $amountToRemove = min($remainingAmount, $availableInThisRecord);
            
            $newAmount = $availableInThisRecord - $amountToRemove;
            $remainingAmount -= $amountToRemove;
        
        if ($newAmount == 0) {
            $inventory->update(['active' => false]);
        } else {
            $inventory->update(['amount' => $newAmount]);
            }
        }

        return true;
    }

    /**
     * Get average cost for a commodity from inventory
     */
    public function getAverageCost($commodityId)
    {
        $inventory = Inventory::where('commodity_id', $commodityId)
            ->where('active', true)
            ->where('amount', '>', 0)
            ->whereNotNull('purchase_price')
            ->get();

        if ($inventory->isEmpty()) {
            return null;
        }

        $totalValue = 0;
        $totalAmount = 0;

        foreach ($inventory as $item) {
            $totalValue += $item->amount * $item->purchase_price;
            $totalAmount += $item->amount;
        }

        return $totalAmount > 0 ? $totalValue / $totalAmount : null;
    }

    /**
     * Get average cost for a commodity from inventory
     */
    public function getAverageCost($commodityId)
    {
        $inventory = Inventory::where('commodity_id', $commodityId)
            ->where('active', true)
            ->where('amount', '>', 0)
            ->whereNotNull('purchase_price')
            ->get();

        if ($inventory->isEmpty()) {
            return null;
        }

        $totalValue = 0;
        $totalAmount = 0;

        foreach ($inventory as $item) {
            $totalValue += $item->amount * $item->purchase_price;
            $totalAmount += $item->amount;
        }

        return $totalAmount > 0 ? $totalValue / $totalAmount : null;
    }

    
    /**
     * Get stock level for a specific commodity and unit
     */
    public function getStockLevel($commodityId, $unitId)
    {
        return Inventory::where('commodity_id', $commodityId)
            ->where('unit_id', $unitId)
            ->where('active', true)
            ->sum('amount');
    }

    /**
     * Get stock levels for multiple commodity-unit pairs in a single query (performance optimized)
     */
    public function getBatchStockLevels(array $commodityUnitPairs)
    {
        if (empty($commodityUnitPairs)) {
            return [];
        }

        $results = Inventory::where('active', true)
            ->where(function ($query) use ($commodityUnitPairs) {
                foreach ($commodityUnitPairs as $pair) {
                    $query->orWhere(function ($q) use ($pair) {
                        $q->where('commodity_id', $pair['material_id'])
                          ->where('unit_id', $pair['unit_id']);
                    });
                }
            })
            ->selectRaw('commodity_id, unit_id, SUM(amount) as total_amount')
            ->groupBy('commodity_id', 'unit_id')
            ->get()
            ->keyBy(function ($item) {
                return $item->commodity_id . '_' . $item->unit_id;
            });

        // Format results to match expected structure - ensure numbers, not strings
        $formattedResults = [];
        foreach ($commodityUnitPairs as $pair) {
            $key = $pair['material_id'] . '_' . $pair['unit_id'];
            $amount = $results->get($key)->total_amount ?? 0;
            $formattedResults[$key] = is_numeric($amount) ? (float) $amount : 0;
        }

        return $formattedResults;
    }

    /**
     * Get detailed inventory information for a commodity and unit
     */
    public function getDetailedInventory($commodityId, $unitId)
    {
        return Inventory::where('commodity_id', $commodityId)
            ->where('unit_id', $unitId)
            ->where('active', true)
            ->where('amount', '>', 0)
            ->orderBy('created_at', 'asc')
            ->get();
    }

    /**
     * Get all inventory records for a commodity across all units (for debugging)
     */
    public function getAllInventoryForCommodity($commodityId)
    {
        return Inventory::where('commodity_id', $commodityId)
            ->where('active', true)
            ->with(['unit'])
            ->orderBy('unit_id', 'asc')
            ->orderBy('created_at', 'asc')
            ->get();
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
     * Get all inventory items for a specific commodity
     */
    public function getAllInventoryForCommodity($commodityId)
    {
        return Inventory::with(['commodity', 'unit'])
            ->where('commodity_id', $commodityId)
            ->where('active', true)
            ->where('amount', '>', 0)
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
        $inventory->update([
            'commodity_id' => $data['commodity_id'],
            'unit_id' => $data['unit_id'],
            'amount' => $data['amount'],
            'purchase_price' => $data['purchase_price'],
            'sale_price' => $data['sale_price'],
        ]);
        
        return $inventory;
    }

    /**
     * Delete inventory record
     */
    public function delete($inventory)
    {
        $inventory->update(['active' => false]);
        return $inventory;
    }

    /**
     * Manual stock adjustment
     */
    public function adjustStock($inventory, $data)
    {
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
    }

    /**
     * Manual price adjustment
     */
    public function adjustPrice($inventory, $data)
    {
        $newPrice = $data['new_price'];
        $reason = $data['reason'];

        $inventory->update([
            'sale_price' => $newPrice
        ]);

        // Log the price adjustment
        $this->logPriceAdjustment($inventory, $newPrice, $reason);

        return $inventory;
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
