<?php

namespace App\Services\Processes;

use App\Models\ProductionRequest;
use App\Models\Commodity;
use App\Services\BaseService;
use App\Services\InventoryService;
use Illuminate\Support\Facades\DB;

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
            'unit_id' => $product->unit_id,
            'description' => $data['description'] ?? null,
            'status' => 'awaiting_approval',
            'number' => $this->generateUniqueNumber(ProductionRequest::class, 'number'),
            'total_cost' => $totalCost,
        ]);

        // Store materials in pivot table
        foreach ($materials as $material) {
            $productionRequest->materials()->attach($material['material_id'], [
                'required_amount' => $material['required_amount'],
                'unit_id' => $material['unit_id'],
                'unit_cost' => $material['unit_cost'],
                'total_cost' => $material['total_cost'],
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
                'unit_id' => $product->unit_id,
                'description' => $data['description'] ?? $productionRequest->description,
                'total_cost' => $totalCost,
            ]);

            // Update materials in pivot table
            $productionRequest->materials()->detach(); // Remove existing materials
            foreach ($materials as $material) {
                $productionRequest->materials()->attach($material['material_id'], [
                    'required_amount' => $material['required_amount'],
                    'unit_id' => $material['unit_id'],
                    'unit_cost' => $material['unit_cost'],
                    'total_cost' => $material['total_cost'],
                ]);
            }
        } else {
            // Only update description
            $productionRequest->update([
                'description' => $data['description'] ?? $productionRequest->description,
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
     * Approve a production request
     */
    public function approve($productionRequest)
    {
        $unitConversionService = app(\App\Services\UnitConversionService::class);

        // Step 1: Deduct the required raw materials from inventory with unit conversion support
        foreach ($productionRequest->materials as $material) {
            $requiredAmount = $material->pivot->required_amount;
            $requiredUnitId = $material->pivot->unit_id;
            $remainingRequired = $requiredAmount;

            // Get all available inventory for this material
            $allInventory = $this->inventoryService->getAllInventoryForCommodity($material->id);

            // Sort inventory by unit - prioritize exact matches first, then conversions
            $exactMatches = $allInventory->where('unit_id', $requiredUnitId)->where('amount', '>', 0);
            $otherUnits = $allInventory->where('unit_id', '!=', $requiredUnitId)->where('amount', '>', 0);

            // First, try to consume from exact matches
            foreach ($exactMatches as $inventory) {
                if ($remainingRequired <= 0) break;

                $amountToConsume = min($remainingRequired, $inventory->amount);
                $this->inventoryService->removeStock($material->id, $inventory->unit_id, $amountToConsume);
                $remainingRequired -= $amountToConsume;
            }

            // If still need more, consume from other units with conversion
            foreach ($otherUnits as $inventory) {
                if ($remainingRequired <= 0) break;

                // Convert the remaining required amount to the inventory unit
                $requiredInInventoryUnit = $unitConversionService->convert(
                    $remainingRequired,
                    $requiredUnitId,
                    $inventory->unit_id,
                    $material->id
                );

                if ($requiredInInventoryUnit !== null && $requiredInInventoryUnit > 0) {
                    $amountToConsume = min($requiredInInventoryUnit, $inventory->amount);
                    $this->inventoryService->removeStock($material->id, $inventory->unit_id, $amountToConsume);

                    // Convert back to required unit to update remaining amount
                    $consumedInRequiredUnit = $unitConversionService->convert(
                        $amountToConsume,
                        $inventory->unit_id,
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

        // Step 2: Add the final product to inventory
        $this->inventoryService->addStock(
            $productionRequest->product_id,
            $productionRequest->unit_id, // Use production request unit
            $productionRequest->production_amount,
            $productionRequest->total_cost / $productionRequest->production_amount // unit cost
        );

        // Step 3: Update production request status
        $productionRequest->update(['status' => 'approved']);

        return $productionRequest;
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
     * Calculate required materials for a product
     */
    protected function calculateRequiredMaterials($product, $productionAmount)
    {
        $materials = collect();

        foreach ($product->materials as $material) {
            // Calculate required amount based on product formula
            $requiredAmount = $material->pivot->amount * $productionAmount;

            // Get current inventory cost for this material
            $unitCost = $this->inventoryService->getAverageCost($material->id) ?? 0;
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
     * Check if production request can be approved
     */
    public function checkProduction($productionRequest)
    {
        $unitConversionService = app(\App\Services\UnitConversionService::class);

        foreach ($productionRequest->materials as $material) {
            $requiredAmount = $material->pivot->required_amount;
            $requiredUnitId = $material->pivot->unit_id;

            // Get all available inventory for this material
            $allInventory = $this->inventoryService->getAllInventoryForCommodity($material->id);

            // Calculate total available stock in the required unit
            $availableStock = 0;
            $availableUnits = [];

            foreach ($allInventory as $inventory) {
                if ($inventory->amount > 0) {
                    $unit = \App\Models\Unit::find($inventory->unit_id);
                    $availableUnits[] = "{$inventory->amount} {$unit->symbol}";

                    if ($inventory->unit_id == $requiredUnitId) {
                        // Direct match - no conversion needed
                        $availableStock += $inventory->amount;
                    } else {
                        // Convert from available unit to required unit
                        $convertedAmount = $unitConversionService->convert(
                            $inventory->amount,
                            $inventory->unit_id,
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
                $requiredUnit = \App\Models\Unit::find($requiredUnitId);
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
}
