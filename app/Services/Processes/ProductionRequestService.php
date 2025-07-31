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
        return DB::transaction(function () use ($data, $file) {
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
        });
    }

    /**
     * Update an existing production request
     */
    public function update($productionRequest, $data, $file = null)
    {
        return DB::transaction(function () use ($productionRequest, $data, $file) {
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
        });
    }

    /**
     * Delete a production request
     */
    public function delete($productionRequest)
    {
        return DB::transaction(function () use ($productionRequest) {

            
            // Materials are handled through pivot table, no need to clear JSON field
            
            // Delete the production request
            $productionRequest->delete();
            
            return true;
        });
    }

    /**
     * Approve a production request
     */
    public function approve($productionRequest)
    {
        return DB::transaction(function () use ($productionRequest) {
            // Step 1: Deduct the exact required raw materials from inventory
            foreach ($productionRequest->materials as $material) {
                $requiredAmount = $material->pivot->required_amount;
                $requiredUnitId = $material->pivot->unit_id;
                
                // Remove from inventory using the material's unit (no conversion needed)
                $this->inventoryService->removeStock(
                    $material->id,
                    $requiredUnitId, // Use the material's unit as specified in pivot
                    $requiredAmount
                );
            }

            // Step 2: Add the final product to inventory
            $this->inventoryService->addStock(
                $productionRequest->product_id,
                $productionRequest->unit_id, // Use production request unit
                $productionRequest->production_amount,
                $productionRequest->total_cost / $productionRequest->production_amount, // unit cost
                $productionRequest->product->sales_price
            );

            // Step 3: Update production request status
            $productionRequest->update(['status' => 'approvaled']);

            return $productionRequest;
        });
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
        foreach ($productionRequest->materials as $material) {
            $requiredAmount = $material->pivot->required_amount;
            $requiredUnitId = $material->pivot->unit_id;
            
            // Check if we have enough inventory in the material's unit
            $availableStock = $this->inventoryService->getStockLevel($material->id, $requiredUnitId);
            
            if ($availableStock < $requiredAmount) {
                return [
                    'success' => false,
                    'error' => "موجودی کافی برای ماده {$material->title} وجود ندارد. مورد نیاز: {$requiredAmount} {$material->unit->symbol}، موجود: {$availableStock} {$material->unit->symbol}"
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