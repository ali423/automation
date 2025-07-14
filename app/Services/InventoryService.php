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
}
