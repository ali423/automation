<?php

namespace App\Services\Processes;

use App\Models\ProductionRequest;
use App\Models\Commodity;
use App\Services\BaseService;
use App\Services\InventoryService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class ProductionRequestService extends BaseService
{
    protected $inventoryService;

    public function __construct(InventoryService $inventoryService)
    {
        $this->inventoryService = $inventoryService;
    }

    /**
     * Create a new production request
     */
    public function create($data, $file = null)
    {
        // Validate product exists and is of type 'product'
        $product = Commodity::where('type', 'product')->findOrFail($data['product_id']);

        // Calculate required materials based on product formula
        $materials = $this->calculateRequiredMaterials($product, $data['amount']);

        // Calculate total cost
        $totalCost = $materials->sum('total_cost');

        // Create production request
        $productionRequest = ProductionRequest::create([
            'product_id' => $product->id,
            'production_amount' => $data['amount'],
            'packaging_count' => $this->normalizePackagingCount($data['packaging_count'] ?? null),
            'unit_id' => $product->unit_id,
            'description' => $data['description'] ?? null,
            'status' => 'awaiting_approval',
            'number' => $this->generateUniqueNumber(ProductionRequest::class, 'number'),
            'total_cost' => $totalCost,
        ]);

        // Store materials in pivot table using bulk insert
        $this->bulkAttachMaterials($productionRequest, $materials);

        // Add comment if provided
        if (!empty($data['comment'])) {
            $productionRequest->comments()->create([
                'user_id' => auth()->user()->id,
                'body' => $data['comment'],
            ]);
        }

        // Upload file if provided
        if ($file) {
            $this->uploadFile($file, 'production-request', $productionRequest);
        }

        return $productionRequest;
    }

    /**
     * Update an existing production request
     */
    public function update($productionRequest, $data, $file = null)
    {
        // Check if product or amount has changed
        $productChanged = isset($data['product_id']) && $data['product_id'] != $productionRequest->product_id;
        $amountChanged = isset($data['amount']) && $data['amount'] != $productionRequest->production_amount;

        if ($productChanged || $amountChanged) {
            // Recalculate everything
            $product = Commodity::where('type', 'product')->findOrFail($data['product_id']);
            $materials = $this->calculateRequiredMaterials($product, $data['amount']);
            $totalCost = $materials->sum('total_cost');

            // Update production request
            $productionRequest->update([
                'product_id' => $product->id,
                'production_amount' => $data['amount'],
                'packaging_count' => $this->normalizePackagingCount($data['packaging_count'] ?? null),
                'unit_id' => $product->unit_id,
                'description' => $data['description'] ?? $productionRequest->description,
                'total_cost' => $totalCost,
            ]);

            // Update materials in pivot table using bulk operations
            $productionRequest->materials()->detach(); // Remove existing materials
            $this->bulkAttachMaterials($productionRequest, $materials);
        } else {
            // Description and/or packaging (amount and product unchanged)
            $productionRequest->update([
                'description' => $data['description'] ?? $productionRequest->description,
                'packaging_count' => $this->normalizePackagingCount($data['packaging_count'] ?? null),
            ]);
        }

        // Add comment if provided
        if (!empty($data['comment'])) {
            $productionRequest->comments()->create([
                'user_id' => auth()->user()->id,
                'body' => $data['comment'],
            ]);
        }

        // Upload file if provided
        if ($file) {
            $this->uploadFile($file, 'production-request', $productionRequest);
        }

        return $productionRequest;
    }

    /**
     * Delete a production request
     */
    public function delete($productionRequest)
    {
        // Materials are handled through pivot table, no need to clear JSON field

        // Delete the production request
        $productionRequest->delete();

        return true;
    }

    /**
     * Approve a production request with optimized batch operations
     */
    public function approve($productionRequest)
    {
        $unitConversionService = app(\App\Services\UnitConversionService::class);

        // Pre-load all inventory data for all materials in one query
        $materialIds = $productionRequest->materials->pluck('id')->toArray();
        $allInventoryData = $this->getBatchInventoryData($materialIds);

        // Step 1: Deduct the required raw materials from inventory with unit conversion support
        $inventoryUpdates = []; // Batch inventory updates
        
        foreach ($productionRequest->materials as $material) {
            $requiredAmount = $material->pivot->required_amount;
            $requiredUnitId = $material->pivot->unit_id;
            $remainingRequired = $requiredAmount;

            // Get pre-loaded inventory data for this material
            $inventoryData = $allInventoryData[$material->id] ?? [];

            // Sort inventory by unit - prioritize exact matches first, then conversions
            $exactMatches = collect($inventoryData)->where('unit_id', $requiredUnitId)->where('amount', '>', 0);
            $otherUnits = collect($inventoryData)->where('unit_id', '!=', $requiredUnitId)->where('amount', '>', 0);

            // First, try to consume from exact matches
            foreach ($exactMatches as $inventory) {
                if ($remainingRequired <= 0) break;

                $amountToConsume = min($remainingRequired, $inventory['amount']);
                $this->addInventoryUpdate($inventoryUpdates, $material->id, $inventory['unit_id'], -$amountToConsume);
                $remainingRequired -= $amountToConsume;
            }

            // If still need more, consume from other units with conversion
            foreach ($otherUnits as $inventory) {
                if ($remainingRequired <= 0) break;

                // Convert the remaining required amount to the inventory unit
                $requiredInInventoryUnit = $unitConversionService->convert(
                    $remainingRequired,
                    $requiredUnitId,
                    $inventory['unit_id'],
                    $material->id
                );

                if ($requiredInInventoryUnit !== null && $requiredInInventoryUnit > 0) {
                    $amountToConsume = min($requiredInInventoryUnit, $inventory['amount']);
                    $this->addInventoryUpdate($inventoryUpdates, $material->id, $inventory['unit_id'], -$amountToConsume);

                    // Convert back to required unit to update remaining amount
                    $consumedInRequiredUnit = $unitConversionService->convert(
                        $amountToConsume,
                        $inventory['unit_id'],
                        $requiredUnitId,
                        $material->id
                    );

                    if ($consumedInRequiredUnit !== null) {
                        $remainingRequired -= $consumedInRequiredUnit;
                    }
                }
            }

            // If we still have remaining required amount, throw an error
            if ($remainingRequired > 0) {
                throw new \Exception("موجودی کافی برای ماده {$material->title} وجود ندارد. مورد نیاز: {$requiredAmount}، مصرف شده: " . ($requiredAmount - $remainingRequired));
            }
        }

        // Apply all inventory updates in batch
        $this->applyBatchInventoryUpdates($inventoryUpdates);

        // Step 2: Add the final product to inventory
        $this->inventoryService->addStock(
            $productionRequest->product_id,
            $productionRequest->unit_id,
            $productionRequest->production_amount,
            $productionRequest->total_cost / $productionRequest->production_amount
        );

        // Step 3: Update production request status
        $productionRequest->update(['status' => 'approved']);

        // Clear related caches
        $this->clearProductionCaches($productionRequest->id);

        return $productionRequest;
    }

    /**
     * Add inventory update to batch operations
     */
    private function addInventoryUpdate(&$updates, $commodityId, $unitId, $amountChange)
    {
        $key = "{$commodityId}_{$unitId}";
        if (!isset($updates[$key])) {
            $updates[$key] = [
                'commodity_id' => $commodityId,
                'unit_id' => $unitId,
                'amount_change' => 0
            ];
        }
        $updates[$key]['amount_change'] += $amountChange;
    }

    /**
     * Apply batch inventory updates efficiently
     */
    private function applyBatchInventoryUpdates($updates)
    {
        foreach ($updates as $update) {
            if ($update['amount_change'] != 0) {
                $this->inventoryService->removeStock(
                    $update['commodity_id'],
                    $update['unit_id'],
                    abs($update['amount_change'])
                );
            }
        }
    }

    /**
     * Bulk attach materials to production request for better performance
     */
    private function bulkAttachMaterials($productionRequest, $materials)
    {
        if (empty($materials)) {
            return;
        }

        $pivotData = [];
        $timestamp = now();

        foreach ($materials as $material) {
            $pivotData[] = [
                'production_request_id' => $productionRequest->id,
                'material_id' => $material['material_id'],
                'required_amount' => $material['required_amount'],
                'unit_id' => $material['unit_id'],
                'unit_cost' => $material['unit_cost'],
                'total_cost' => $material['total_cost'],
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ];
        }

        // Use DB::table for bulk insert instead of Eloquent
        DB::table('production_materials')->insert($pivotData);
    }

    /**
     * Reject a production request
     */
    public function reject($productionRequest)
    {
        $productionRequest->update(['status' => 'rejected']);
        return $productionRequest;
    }



    /**
     * Calculate required materials for a product with optimized queries
     */
    protected function calculateRequiredMaterials($product, $productionAmount)
    {
        // Use cached product data if available
        $cachedProduct = $this->getCachedProductFormulas($product->id);
        if ($cachedProduct) {
            $product = $cachedProduct;
        }

        $materials = collect();
        $materialIds = $product->materials->pluck('id')->toArray();
        
        // Batch load inventory costs for all materials at once
        $inventoryCosts = $this->getBatchInventoryCosts($materialIds);

        foreach ($product->materials as $material) {
            // Calculate required amount based on product formula
            $requiredAmount = $material->pivot->amount * $productionAmount;

            // Get pre-loaded inventory cost for this material
            $unitCost = $inventoryCosts[$material->id] ?? 0;
            $totalCost = $requiredAmount * $unitCost;

            $materials->push([
                'material_id' => $material->id,
                'required_amount' => $requiredAmount,
                'unit_id' => $material->pivot->unit_id,
                'unit_cost' => $unitCost,
                'total_cost' => $totalCost,
            ]);
        }

        return $materials;
    }

    /**
     * Get fresh inventory costs for multiple materials (no caching)
     */
    protected function getBatchInventoryCosts($materialIds)
    {
        $costs = [];
        
        // Get all inventory records for these materials in one query
        $inventories = DB::table('inventories')
            ->whereIn('commodity_id', $materialIds)
            ->where('amount', '>', 0)
            ->select('commodity_id', 'purchase_price', 'amount')
            ->get()
            ->groupBy('commodity_id');

        foreach ($inventories as $commodityId => $inventoryRecords) {
            $totalValue = 0;
            $totalAmount = 0;
            
            foreach ($inventoryRecords as $record) {
                $totalValue += $record->purchase_price * $record->amount;
                $totalAmount += $record->amount;
            }
            
            $costs[$commodityId] = $totalAmount > 0 ? $totalValue / $totalAmount : 0;
        }
        
        return $costs;
    }



    /**
     * Check if production request can be approved with optimized queries
     */
    public function checkProduction($productionRequest)
    {
        $unitConversionService = app(\App\Services\UnitConversionService::class);
        
        // Batch load all required data
        $materialIds = $productionRequest->materials->pluck('id')->toArray();
        $unitIds = $productionRequest->materials->pluck('pivot.unit_id')->unique()->toArray();
        
        // Get all inventory data for materials in one query
        $allInventoryData = $this->getBatchInventoryData($materialIds);
        
        // Get all units in one query
        $units = \App\Models\Unit::whereIn('id', $unitIds)->get()->keyBy('id');

        foreach ($productionRequest->materials as $material) {
            $requiredAmount = $material->pivot->required_amount;
            $requiredUnitId = $material->pivot->unit_id;
            $requiredUnit = $units[$requiredUnitId] ?? null;

            // Get pre-loaded inventory data for this material
            $inventoryData = $allInventoryData[$material->id] ?? [];

            // Calculate total available stock in the required unit
            $availableStock = 0;
            $availableUnits = [];

            foreach ($inventoryData as $inventory) {
                if ($inventory['amount'] > 0) {
                    $unit = $units[$inventory['unit_id']] ?? null;
                    if ($unit) {
                        $availableUnits[] = "{$inventory['amount']} {$unit->symbol}";
                    }

                    if ($inventory['unit_id'] == $requiredUnitId) {
                        // Direct match - no conversion needed
                        $availableStock += $inventory['amount'];
                    } else {
                        // Convert from available unit to required unit
                        $convertedAmount = $unitConversionService->convert(
                            $inventory['amount'],
                            $inventory['unit_id'],
                            $requiredUnitId,
                            $material->id
                        );

                        if ($convertedAmount !== null && $convertedAmount > 0) {
                            $availableStock += $convertedAmount;
                        }
                    }
                }
            }

            if ($availableStock < $requiredAmount) {
                $availableInfo = !empty($availableUnits) ? ' (موجود در: ' . implode(', ', $availableUnits) . ')' : '';

                return [
                    'success' => false,
                    'error' => "موجودی کافی برای ماده {$material->title} وجود ندارد. مورد نیاز: {$requiredAmount} {$requiredUnit->symbol}، موجود: {$availableStock} {$requiredUnit->symbol}{$availableInfo}"
                ];
            }
        }

        return ['success' => true];
    }

    /**
     * Get fresh inventory data for multiple materials (no caching)
     */
    protected function getFreshInventoryData($materialIds)
    {
        $inventoryData = [];
        
        // Get all inventory records for these materials in one query
        $inventories = DB::table('inventories')
            ->whereIn('commodity_id', $materialIds)
            ->where('amount', '>', 0)
            ->select('commodity_id', 'unit_id', 'amount')
            ->get()
            ->groupBy('commodity_id');

        foreach ($inventories as $commodityId => $records) {
            $inventoryData[$commodityId] = [];
            foreach ($records as $record) {
                $inventoryData[$commodityId][] = [
                    'unit_id' => $record->unit_id,
                    'amount' => $record->amount
                ];
            }
        }
        
        return $inventoryData;
    }

    /**
     * Get inventory data for multiple materials in a single query (cached version - deprecated)
     */
    protected function getBatchInventoryData($materialIds)
    {
        // Use fresh data instead of cached data
        return $this->getFreshInventoryData($materialIds);
    }



    /**
     * Second layer validation
     */
    public function validationSecondLayer($data)
    {
        // Add any additional validation logic here if needed
        return true;
    }

    /**
     * Check production data validation
     */
    public function checkProductionData($data)
    {
        // Validate required fields
        if (empty($data['product_id'])) {
            return [
                'success' => false,
                'error' => 'محصول تولیدی انتخاب نشده است.'
            ];
        }

        if (empty($data['amount']) || $data['amount'] <= 0) {
            return [
                'success' => false,
                'error' => 'مقدار تولید باید بیشتر از صفر باشد.'
            ];
        }

        // Check if product exists and is of type 'product'
        $product = Commodity::where('type', 'product')->find($data['product_id']);
        if (!$product) {
            return [
                'success' => false,
                'error' => 'محصول انتخاب شده یافت نشد یا نوع آن صحیح نیست.'
            ];
        }

        // Check if product has materials (formula)
        if ($product->materials->isEmpty()) {
            return [
                'success' => false,
                'error' => 'محصول انتخاب شده فرمول ساخت ندارد. ابتدا فرمول ساخت محصول را تعریف کنید.'
            ];
        }

        return ['success' => true];
    }

    /**
     * Get materials with real-time inventory data (no caching)
     */
    public function getMaterialsWithInventoryData($productionRequest)
    {
        $unitConversionService = app(\App\Services\UnitConversionService::class);
        $materialsWithInventory = [];

        // Get fresh inventory data for all materials
        $materialIds = $productionRequest->materials->pluck('id')->toArray();
        $allInventoryData = $this->getFreshInventoryData($materialIds);
        
        // Get all units
        $unitIds = collect($allInventoryData)->flatten(1)->pluck('unit_id')->unique()->toArray();
        $units = \App\Models\Unit::whereIn('id', $unitIds)->get()->keyBy('id');

        foreach ($productionRequest->materials as $material) {
            // Get fresh inventory data for this material
            $inventoryData = $allInventoryData[$material->id] ?? [];
            
            // Calculate total available stock in the formula unit
            $availableStock = 0;
            $conversionInfo = '';
            $availableUnits = [];
            
            foreach ($inventoryData as $inventory) {
                if ($inventory['amount'] > 0) {
                    $unit = $units[$inventory['unit_id']] ?? null;
                    if ($unit) {
                        $availableUnits[] = "{$inventory['amount']} {$unit->symbol}";
                    }
                    
                    if ($inventory['unit_id'] == $material->pivot->unit_id) {
                        // Direct match - no conversion needed
                        $availableStock += $inventory['amount'];
                    } else {
                        // Convert from available unit to formula unit
                        $convertedAmount = $unitConversionService->convert(
                            $inventory['amount'],
                            $inventory['unit_id'],
                            $material->pivot->unit_id,
                            $material->id
                        );
                        
                        if ($convertedAmount !== null && $convertedAmount > 0) {
                            $availableStock += $convertedAmount;
                            if ($unit) {
                                $conversionInfo .= " (تبدیل شده از {$inventory['amount']} {$unit->symbol})";
                            }
                        }
                    }
                }
            }
            
            // If no stock found, show available inventory information
            $allAvailableInfo = '';
            if ($availableStock == 0 && !empty($availableUnits)) {
                $allAvailableInfo = ' (موجود در: ' . implode(', ', $availableUnits) . ')';
            }
            
            // Determine stock status for styling
            $stockStatus = 'success';
            $stockIcon = 'fa-check-circle';
            $stockText = 'کافی';
            
            if ($availableStock < $material->pivot->required_amount) {
                $stockStatus = 'danger';
                $stockIcon = 'fa-times-circle';
                $stockText = 'ناکافی';
            } elseif ($availableStock < $material->pivot->required_amount * 1.1) {
                $stockStatus = 'warning';
                $stockIcon = 'fa-exclamation-triangle';
                $stockText = 'کم';
            }
            
            $materialsWithInventory[] = [
                'material' => $material,
                'availableStock' => $availableStock,
                'conversionInfo' => $conversionInfo,
                'allAvailableInfo' => $allAvailableInfo,
                'stockStatus' => $stockStatus,
                'stockIcon' => $stockIcon,
                'stockText' => $stockText,
            ];
        }

        return $materialsWithInventory;
    }

    /**
     * Get fresh product formulas (no caching)
     */
    public function getCachedProductFormulas($productId)
    {
        return Commodity::where('id', $productId)
            ->with(['materials.unit', 'unit'])
            ->first();
    }

    /**
     * Clear production-related caches (simplified - most caching removed)
     */
    public function clearProductionCaches($productionRequestId = null, $productId = null)
    {
        // Since we removed most caching, this method is now mostly for compatibility
        // No specific cache clearing needed as we're using fresh data
        return true;
    }

    /**
     * Clear inventory-related caches for a specific product (simplified)
     */
    private function clearInventoryCaches($productId)
    {
        // No caching to clear since we removed most caching mechanisms
        return true;
    }

    /**
     * Clear all inventory-related caches (simplified)
     */
    private function clearAllInventoryCaches()
    {
        // No caching to clear since we removed most caching mechanisms
        return true;
    }

    /**
     * Persist packaging count only when a positive integer is provided.
     */
    private function normalizePackagingCount($value): ?int
    {
        if ($value === null || $value === '') {
            return null;
        }
        $int = (int) $value;

        return $int > 0 ? $int : null;
    }
}
