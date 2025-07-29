<?php

namespace App\Services\Processes;

use App\Models\Commodity;
use App\Models\ProductionRequest;
use App\Services\BaseService;
use App\Services\InventoryService;
use App\Services\CommodityUnitService;
use Illuminate\Support\Facades\DB;

class ProductionRequestService extends BaseService
{
    protected $inventoryService;
    protected $commodityUnitService;

    public function __construct(InventoryService $inventoryService, CommodityUnitService $commodityUnitService)
    {
        $this->inventoryService = $inventoryService;
        $this->commodityUnitService = $commodityUnitService;
    }

    /**
     * Create a new production request
     *
     * @param array $data
     * @param mixed $file
     * @return ProductionRequest
     */
    public function create($data, $file)
    {
        $product = Commodity::findOrFail($data['product_id']);
        $productionAmount = $data['amount'];
        
        // Get product formula to calculate required materials
        $productFormula = $product->materials;
        
        $commodities = [];
        $totalInputCost = 0;
        
        // Add input materials based on product formula
        foreach ($productFormula as $material) {
            // Calculate required amount based on production amount
            $requiredAmount = ($material->pivot->amount * $productionAmount) / 185; // Based on 185kg standard
            
            // Get current inventory cost for this material
            $materialCost = $this->inventoryService->getAverageCost($material->id);
            $unitCost = $materialCost ?? 0;
            $totalCost = $requiredAmount * $unitCost;
            $totalInputCost += $totalCost;
            
            $commodities[$material->id] = [
                'amount' => $requiredAmount,
                'unit_id' => $material->pivot->unit_id,
                'type' => 'input',
                'unit_cost' => $unitCost,
                'total_cost' => $totalCost,
            ];
        }
        
        // Calculate output product cost and value
        $outputUnitCost = $totalInputCost / $productionAmount; // Cost per unit
        $outputTotalCost = $productionAmount * $product->sales_price; // Sales value
        
        // Add output product
        $commodities[$product->id] = [
            'amount' => $productionAmount,
            'unit_id' => $product->unit_id,
            'type' => 'output',
            'unit_cost' => $outputUnitCost,
            'total_cost' => $outputTotalCost,
        ];
        
        $number = $this->generateUniqueNumber(ProductionRequest::class, 'number');
        $user = auth()->user();
        
        $request = ProductionRequest::create([
            'status' => 'awaiting_approval',
            'number' => $number,
            'description' => $data['description'] ?? null,
            'total_cost' => $totalInputCost, // Store total input cost
        ]);
        
        // Attach commodities using sync to trigger proper pivot events for activity tracking
        $this->attachCommodities($request, $commodities);
        
        if (isset($data['comment'])) {
            $request->comments()->create([
                'user_id' => $user->id,
                'body' => $data['comment'],
            ]);
        }
        
        if (!empty($file)) {
            $this->uploadFile($file, 'production-request', $request);
        }
        
        return $request;
    }

    /**
     * Update an existing production request
     *
     * @param ProductionRequest $productionRequest
     * @param array $data
     * @param mixed $file
     * @return bool
     */
    public function update($productionRequest, $data, $file)
    {
        // Check if product or amount has changed
        $currentProduct = $productionRequest->outputProducts->first();
        $currentAmount = $currentProduct ? $currentProduct->pivot->amount : null;
        $currentProductId = $currentProduct ? $currentProduct->id : null;
        
        $productChanged = isset($data['product_id']) && $data['product_id'] != $currentProductId;
        $amountChanged = isset($data['amount']) && $data['amount'] != $currentAmount;
        
        if ($productChanged || $amountChanged) {
            // Recalculate the entire production request with new product/amount
            $newProduct = Commodity::findOrFail($data['product_id']);
            $newAmount = $data['amount'];
            
            // Get product formula to calculate required materials
            $productFormula = $newProduct->materials;
            
            $commodities = [];
            $totalInputCost = 0;
            
            // Add input materials based on product formula
            foreach ($productFormula as $material) {
                // Calculate required amount based on production amount
                $requiredAmount = ($material->pivot->amount * $newAmount) / 185; // Based on 185kg standard
                
                // Get current inventory cost for this material
                $materialCost = $this->inventoryService->getAverageCost($material->id);
                $unitCost = $materialCost ?? 0;
                $totalCost = $requiredAmount * $unitCost;
                $totalInputCost += $totalCost;
                
                $commodities[$material->id] = [
                    'amount' => $requiredAmount,
                    'unit_id' => $material->pivot->unit_id,
                    'type' => 'input',
                    'unit_cost' => $unitCost,
                    'total_cost' => $totalCost,
                ];
            }
            
            // Calculate output product cost and value
            $outputUnitCost = $totalInputCost / $newAmount; // Cost per unit
            $outputTotalCost = $newAmount * $newProduct->sales_price; // Sales value
            
            // Add output product
            $commodities[$newProduct->id] = [
                'amount' => $newAmount,
                'unit_id' => $newProduct->unit_id,
                'type' => 'output',
                'unit_cost' => $outputUnitCost,
                'total_cost' => $outputTotalCost,
            ];
            
            // Update the production request
            $productionRequest->update([
                'description' => $data['description'] ?? null,
                'total_cost' => $totalInputCost,
            ]);
            
            // Sync commodities (this will replace all existing relationships)
            $this->syncCommodities($productionRequest, $commodities);
        } else {
            // Only update description if no product/amount changes
            $productionRequest->update([
                'description' => $data['description'] ?? null,
            ]);
        }
        
        if (isset($data['comment'])) {
            $productionRequest->comments()->create([
                'user_id' => auth()->user()->id,
                'body' => $data['comment'],
            ]);
        }
        
        if (!empty($file)) {
            $this->uploadFile($file, 'production-request', $productionRequest);
        }
        
        return true;
    }

    /**
     * Delete a production request
     *
     * @param ProductionRequest $productionRequest
     * @return bool
     */
    public function delete($productionRequest)
    {
        // Detach commodities
        $productionRequest->commodities()->detach();
        
        // Delete the request
        $productionRequest->delete();
        
        return true;
    }

    /**
     * Approve a production request
     *
     * @param ProductionRequest $productionRequest
     * @return bool
     */
    public function approvalProduction($productionRequest)
    {
        // Deduct input materials from inventory
        foreach ($productionRequest->inputMaterials as $material) {
            $amountInMainUnit = $this->commodityUnitService->convertToMainUnit(
                $material,
                $material->pivot->amount,
                $material->pivot->unit_id
            );
            
            $this->inventoryService->removeStock(
                $material->id,
                $material->unit_id,
                $amountInMainUnit
            );
        }
        
        // Add output products to inventory
        foreach ($productionRequest->outputProducts as $product) {
            $amountInMainUnit = $this->commodityUnitService->convertToMainUnit(
                $product,
                $product->pivot->amount,
                $product->pivot->unit_id
            );
            
            $this->inventoryService->addStock(
                $product->id,
                $product->unit_id,
                $amountInMainUnit,
                $product->pivot->unit_cost,
                $product->sales_price
            );
        }
        
        $productionRequest->update([
            'status' => 'approvaled',
        ]);
        
        return true;
    }

    /**
     * Reject a production request
     *
     * @param ProductionRequest $productionRequest
     * @return bool
     */
    public function rejectProduction($productionRequest)
    {
        $productionRequest->update([
            'status' => 'rejected',
        ]);
        
        return true;
    }

    /**
     * Attach commodities to production request manually
     *
     * @param ProductionRequest $productionRequest
     * @param array $commodities
     * @return void
     */
    protected function attachCommodities(ProductionRequest $productionRequest, array $commodities)
    {
        // Use sync method which properly triggers pivot events for activity tracking
        $productionRequest->commodities()->sync($commodities);
    }

    /**
     * Sync commodities for production request manually
     *
     * @param ProductionRequest $productionRequest
     * @param array $commodities
     * @return void
     */
    protected function syncCommodities(ProductionRequest $productionRequest, array $commodities)
    {
        // Use sync method which properly triggers pivot events for activity tracking
        $productionRequest->commodities()->sync($commodities);
    }

    /**
     * Check production request data
     *
     * @param array $data
     * @return array
     */
    public function checkProductionData($data)
    {
        $errors = [];

        // Validate product exists
        if (!isset($data['product_id']) || empty($data['product_id'])) {
            $errors['product_id'] = 'محصول باید انتخاب شود';
        } else {
            $product = Commodity::find($data['product_id']);
            if (!$product) {
                $errors['product_id'] = 'محصول انتخاب شده وجود ندارد';
            } elseif (!$product->materials || $product->materials->isEmpty()) {
                $errors['product_id'] = 'فرمول ساخت برای این محصول تعریف نشده است';
            }
        }

        // Validate amount
        if (!isset($data['amount']) || empty($data['amount']) || $data['amount'] <= 0) {
            $errors['amount'] = 'مقدار تولید باید بیشتر از صفر باشد';
        }

        // Return format expected by controller
        if (empty($errors)) {
            return ['success' => true];
        } else {
            return ['success' => false, 'error' => $errors];
        }
    }

    /**
     * Check if production is possible (for approval)
     *
     * @param ProductionRequest $productionRequest
     * @return array
     */
    public function checkProduction($productionRequest)
    {
        foreach ($productionRequest->inputMaterials as $material) {
            $amountInMainUnit = $this->commodityUnitService->convertToMainUnit(
                $material,
                $material->pivot->amount,
                $material->pivot->unit_id
            );

            $availableStock = $this->inventoryService->getStockLevel($material->id, $material->pivot->unit_id);

            if ($amountInMainUnit > $availableStock) {
                return [
                    'success' => false,
                    'error' => "موجودی کافی برای ماده اولیه '{$material->title}' وجود ندارد. موجودی: {$availableStock}، مورد نیاز: {$amountInMainUnit}"
                ];
            }
        }

        return ['success' => true];
    }

    /**
     * Check if production request is expired
     *
     * @param ProductionRequest $productionRequest
     * @return array
     */
    public function checkExpiredRequest($productionRequest)
    {
        // Set expiry to 30 days from creation
        $expiryDate = $productionRequest->created_at->addDays(30);
        
        if (now()->isAfter($expiryDate)) {
            $productionRequest->update(['status' => 'expired']);
            
            return [
                'success' => false,
                'error' => 'درخواست تولید منقضی شده است. درخواست‌های تولید پس از ۳۰ روز منقضی می‌شوند.'
            ];
        }

        return ['success' => true];
    }

    /**
     * Get all production requests
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAll()
    {
        return ProductionRequest::with(['commodities.unit'])
            ->orderBy('id', 'DESC')
            ->get();
    }

    /**
     * Get active production requests
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getActive()
    {
        return ProductionRequest::active()
            ->with(['commodities.unit'])
            ->orderBy('id', 'DESC')
            ->get();
    }

    /**
     * Get awaiting approval production requests
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAwaitingApproval()
    {
        return ProductionRequest::awaitingApproval()
            ->with(['commodities.unit'])
            ->orderBy('id', 'DESC')
            ->get();
    }

    /**
     * Get the main product being produced in this request
     *
     * @param ProductionRequest $productionRequest
     * @return Commodity|null
     */
    public function getMainProduct(ProductionRequest $productionRequest)
    {
        return $productionRequest->outputProducts->first();
    }

    /**
     * Get production summary information
     *
     * @param ProductionRequest $productionRequest
     * @return array
     */
    public function getProductionSummary(ProductionRequest $productionRequest)
    {
        $mainProduct = $this->getMainProduct($productionRequest);
        
        // Get inventory data for input materials
        $inputMaterialsWithInventory = [];
        foreach ($productionRequest->inputMaterials as $material) {
            try {
                // Get current stock level for this material
                $currentStock = $this->inventoryService->getStockLevel($material->id, $material->pivot->unit_id);
                $requiredAmount = $material->pivot->amount;
                
                            // Calculate availability status
            $isSufficient = $currentStock >= $requiredAmount;
            $shortage = max(0, $requiredAmount - $currentStock);
            $surplus = max(0, $currentStock - $requiredAmount);
            
            $inputMaterialsWithInventory[] = [
                'material' => $material,
                'current_stock' => $currentStock,
                'required_amount' => $requiredAmount,
                'is_sufficient' => $isSufficient,
                'shortage' => $shortage,
                'surplus' => $surplus,
            ];
            } catch (\Exception $e) {
                // Log error and continue with default values
                \Log::error("Error getting inventory for material {$material->id}: " . $e->getMessage());
                
                $inputMaterialsWithInventory[] = [
                    'material' => $material,
                    'current_stock' => 0,
                    'required_amount' => $material->pivot->amount,
                    'is_sufficient' => false,
                    'shortage' => $material->pivot->amount,
                    'surplus' => 0,
                ];
            }
        }
        
        return [
            'main_product' => $mainProduct,
            'production_amount' => $mainProduct ? $mainProduct->pivot->amount : 0,
            'production_unit' => $mainProduct ? $mainProduct->unit : null,
            'input_materials_count' => $productionRequest->inputMaterials->count(),
            'input_materials_with_inventory' => $inputMaterialsWithInventory,
            'total_input_cost' => $productionRequest->total_input_cost,
            'total_output_value' => $productionRequest->total_output_value,
            'profit' => $productionRequest->profit,
            // Overall availability status
            'all_materials_available' => collect($inputMaterialsWithInventory)->every('is_sufficient'),
            'materials_with_shortage' => collect($inputMaterialsWithInventory)->where('is_sufficient', false)->count(),
        ];
    }
} 