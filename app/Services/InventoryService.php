<?php

namespace App\Services;

use App\Models\Inventory;
use App\Models\Commodity;
use App\Models\Unit;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class InventoryService extends BaseService
{
    /**
     * Add stock to inventory (for purchases/imports)
     */
    public function addStock($commodityId, $unitId, $amount, $purchasePrice = null)
    {
        // Validate that the unit is valid for this commodity
        $this->validateCommodityUnit($commodityId, $unitId);

        // Use DB transaction to prevent race conditions
        return DB::transaction(function () use ($commodityId, $unitId, $amount, $purchasePrice) {
            // Lock the row to prevent concurrent updates creating duplicates
            $inventory = Inventory::where('commodity_id', $commodityId)
                ->where('unit_id', $unitId)
                ->lockForUpdate()
                ->first();

            if ($inventory) {
                // Update existing inventory
                $newAmount = $inventory->amount + $amount;
                
                // Calculate weighted average purchase price if both exist
                if ($purchasePrice !== null && $inventory->purchase_price !== null && $inventory->amount > 0) {
                    $totalValue = ($inventory->amount * $inventory->purchase_price) + ($amount * $purchasePrice);
                    $totalAmount = $inventory->amount + $amount;
                    $avgPurchasePrice = $totalValue / $totalAmount;
                } else {
                    $avgPurchasePrice = $purchasePrice ?? $inventory->purchase_price;
                }
                
                $inventory->update([
                    'amount' => $newAmount,
                    'purchase_price' => $avgPurchasePrice,
                ]);
            } else {
                // Create new inventory record with unique constraint protection
                $inventory = Inventory::create([
                    'commodity_id' => $commodityId,
                    'unit_id' => $unitId,
                    'amount' => $amount,
                    'purchase_price' => $purchasePrice,
                ]);
            }

            return $inventory;
        });
    }

    /**
     * Validate that a unit is valid for a commodity
     * Only allows the main unit or units with valid conversions
     *
     * @param int $commodityId
     * @param int $unitId
     * @throws \Exception
     */
    private function validateCommodityUnit($commodityId, $unitId)
    {
        $commodity = Commodity::find($commodityId);
        if (!$commodity) {
            throw new \Exception('کالای مورد نظر یافت نشد');
        }

        // Check if the unit is the main unit of the commodity
        if ($commodity->unit_id == $unitId) {
            return; // Main unit is always valid
        }

        // Check if there's a valid unit conversion
        $commodityUnitService = app(\App\Services\CommodityUnitService::class);
        if (!$commodityUnitService->isUnitSelectable($commodity, $unitId)) {
            throw new \Exception("واحد انتخاب شده برای کالای {$commodity->title} معتبر نیست. فقط واحد اصلی یا واحدهای دارای تبدیل معتبر هستند.");
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

        // Get all inventory records for this commodity and unit, ordered by creation date (FIFO)
        $inventories = Inventory::where('commodity_id', $commodityId)
            ->where('unit_id', $unitId)
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
            // When amount becomes 0, we can delete the record or keep it with 0 amount
            // For now, we'll keep it with 0 amount for audit purposes
            $inventory->update(['amount' => $newAmount]);
        } else {
            $inventory->update(['amount' => $newAmount]);
        }
        }

        // No cache clearing needed since we removed caching mechanisms

        return true;
    }

    /**
     * Get average cost for a commodity from inventory
     */


    /**
     * Get average cost for a commodity from inventory
     */
    public function getAverageCost($commodityId)
    {
        $inventory = Inventory::where('commodity_id', $commodityId)
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
            ->where('amount', '>', 0)
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

        $results = Inventory::where('amount', '>', 0)
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
            ->where('amount', '>', 0)
            ->orderBy('created_at', 'asc')
            ->get();
    }

    

    /**
     * Get all active inventory items (amount > 0)
     */
    public function getActiveInventory()
    {
        return Inventory::with(['commodity', 'unit'])
            ->where('amount', '>', 0)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Get active inventory with pre-calculated financial data for better performance
     */
    public function getActiveInventoryWithCalculations()
    {
        $inventories = Inventory::with(['commodity', 'unit'])
            ->where('amount', '>', 0)
            ->orderBy('created_at', 'desc')
            ->get();

        // Pre-calculate financial data to avoid N+1 queries in views
        return $inventories->map(function ($inventory) {
            $inventory->financial_data = $this->calculateFinancialData($inventory);
            return $inventory;
        });
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
            ->where('amount', '>', 0)
            ->selectRaw('
                commodity_id,
                unit_id,
                SUM(amount) as total_amount,
                AVG(purchase_price) as avg_purchase_price
            ')
            ->groupBy('commodity_id', 'unit_id')
            ->get();
    }

    /**
     * Update inventory record
     */
    public function update($inventory, $data)
    {
        // Validate that the unit is valid for this commodity
        $this->validateCommodityUnit($data['commodity_id'], $data['unit_id']);

        $inventory->update([
            'commodity_id' => $data['commodity_id'],
            'unit_id' => $data['unit_id'],
            'amount' => $data['amount'],
            'purchase_price' => $data['purchase_price'],
        ]);

        // No cache clearing needed since we removed caching mechanisms
        
        return $inventory;
    }

    /**
     * Delete inventory record (set amount to 0 for audit purposes)
     */
    public function delete($inventory)
    {
        $inventory->update(['amount' => 0]);
        
        // No cache clearing needed since we removed caching mechanisms
        
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
            'amount' => $newAmount
        ]);

        // No cache clearing needed since we removed caching mechanisms

        // Log the adjustment
        $this->logStockAdjustment($inventory, $adjustmentType, $quantity, $reason);

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
     * Calculate financial data for an inventory item
     * This method pre-calculates all financial metrics to avoid calculations in views
     */
    public function calculateFinancialData($inventory)
    {
        $purchasePrice = $inventory->purchase_price ?? 0;
        $isProduct = $inventory->commodity && $inventory->commodity->type === 'product';
        $salePrice = $isProduct ? ($inventory->commodity->sales_price ?? 0) : 0;
        
        $data = [
            'purchase_price' => $purchasePrice,
            'sale_price' => $salePrice,
            'amount' => $inventory->amount,
            'is_product' => $isProduct,
        ];

        if ($isProduct) {
            $profit = $salePrice - $purchasePrice;
            $profitPercentage = $purchasePrice > 0 ? ($profit / $purchasePrice) * 100 : 0;
            $totalValue = $inventory->amount * $salePrice;
            
            $data = array_merge($data, [
                'profit' => $profit,
                'profit_percentage' => $profitPercentage,
                'total_value' => $totalValue,
                'has_sale_price' => true,
            ]);
        } else {
            $totalValue = $inventory->amount * $purchasePrice;
            
            $data = array_merge($data, [
                'profit' => 0,
                'profit_percentage' => 0,
                'total_value' => $totalValue,
                'has_sale_price' => false,
            ]);
        }

        return $data;
    }

    /**
     * Get form data for inventory edit/create forms
     * This method optimizes data loading for forms
     */
    public function getFormData()
    {
        return [
            'commodities' => Commodity::select('id', 'title', 'type')->orderBy('title')->get(),
            'units' => Unit::select('id', 'name')->orderBy('name')->get(),
        ];
    }

    /**
     * Get commodity inventory data for AJAX requests
     * This method provides optimized data for AJAX endpoints
     */
    public function getCommodityInventoryData($commodityId)
    {
        $commodity = Commodity::findOrFail($commodityId);
        
        // Get the latest inventory price for this commodity
        $inventory = Inventory::where('commodity_id', $commodityId)
            ->where('amount', '>', 0)
            ->orderBy('created_at', 'desc')
            ->first();

        // Use the direct sale price from commodity
        $price = $commodity->sales_price ?? 0;
        
        return [
            'price' => $price,
            'commodity' => [
                'id' => $commodity->id,
                'title' => $commodity->title,
                'type' => $commodity->type
            ],
            'inventory' => $inventory ? [
                'amount' => $inventory->amount,
                'purchase_price' => $inventory->purchase_price,
                'unit' => $inventory->unit->name ?? 'نامشخص'
            ] : null
        ];
    }

    /**
     * Clear inventory-related caches for a specific commodity
     */
    private function clearInventoryCaches($commodityId)
    {
        // Clear specific cache keys that might contain this commodity
        $this->clearSpecificCacheKeys($commodityId);
        
        // Also clear any production request caches that might be affected
        $this->clearProductionRequestCaches($commodityId);
    }

    /**
     * Clear specific cache keys for a commodity
     */
    private function clearSpecificCacheKeys($commodityId)
    {
        // Get all products that might use this commodity as a material
        $products = \App\Models\Commodity::whereHas('materials', function($query) use ($commodityId) {
            $query->where('material_id', $commodityId);
        })->pluck('id')->toArray();
        
        // Clear batch inventory caches for this commodity
        $this->clearBatchInventoryCaches([$commodityId]);
        
        // Clear batch inventory caches for products that use this commodity
        if (!empty($products)) {
            $this->clearBatchInventoryCaches($products);
        }
    }
    
    /**
     * Clear production request caches that might be affected by this commodity
     */
    private function clearProductionRequestCaches($commodityId)
    {
        // Get all production requests that might be affected
        $productionRequestIds = \App\Models\ProductionRequest::whereHas('materials', function($query) use ($commodityId) {
            $query->where('material_id', $commodityId);
        })->pluck('id')->toArray();
        
        // Clear specific production request caches
        foreach ($productionRequestIds as $requestId) {
            $request = \App\Models\ProductionRequest::find($requestId);
            if ($request) {
                $cacheKey = "production_materials_inventory_{$request->id}_{$request->updated_at->timestamp}";
                Cache::forget($cacheKey);
            }
        }
    }
    
    /**
     * Clear batch inventory caches for specific commodity IDs
     */
    private function clearBatchInventoryCaches($commodityIds)
    {
        // Generate all possible cache keys for these commodities
        $allCombinations = $this->generateAllCombinations($commodityIds);
        
        foreach ($allCombinations as $combination) {
            $costsKey = 'batch_inventory_costs_' . md5(implode(',', $combination));
            $dataKey = 'batch_inventory_data_' . md5(implode(',', $combination));
            
            Cache::forget($costsKey);
            Cache::forget($dataKey);
        }
    }
    
    /**
     * Generate all possible combinations of commodity IDs for cache clearing
     */
    private function generateAllCombinations($commodityIds)
    {
        $combinations = [];
        
        // Add individual commodity IDs
        foreach ($commodityIds as $id) {
            $combinations[] = [$id];
        }
        
        // Add combinations of 2 or more if there are multiple commodities
        if (count($commodityIds) > 1) {
            $combinations[] = $commodityIds;
        }
        
        return $combinations;
    }
    
    /**
     * Clear all production request caches (nuclear option for cache issues)
     */
    private function clearAllProductionRequestCaches()
    {
        // Get all production requests and clear their caches
        $productionRequests = \App\Models\ProductionRequest::all();
        
        foreach ($productionRequests as $request) {
            $cacheKey = "production_materials_inventory_{$request->id}_{$request->updated_at->timestamp}";
            Cache::forget($cacheKey);
        }
        
        // Also clear product formula caches
        $products = \App\Models\Commodity::where('type', 'product')->pluck('id');
        foreach ($products as $productId) {
            Cache::forget("product_formula_{$productId}");
        }
    }
}
