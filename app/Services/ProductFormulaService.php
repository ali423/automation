<?php

namespace App\Services;

use App\Models\Commodity;
use App\Models\Unit;
use App\Models\UnitConversion;
use Illuminate\Support\Facades\DB;

class ProductFormulaService extends BaseService
{
    protected $unitConversionService;
    protected $commodityUnitService;

    public function __construct(UnitConversionService $unitConversionService, CommodityUnitService $commodityUnitService)
    {
        $this->unitConversionService = $unitConversionService;
        $this->commodityUnitService = $commodityUnitService;
    }

    /**
     * Create a unit-based product formula
     *
     * @param Commodity $product
     * @param array $materialsData Array of [material_id, amount, unit_id]
     * @return bool
     */
    public function createFormula(Commodity $product, array $materialsData)
    {
        return DB::transaction(function () use ($product, $materialsData) {
            $formulaData = [];
            
            foreach ($materialsData as $materialData) {
                $materialId = $materialData['material_id'];
                $amount = $materialData['amount'];
                $unitId = $materialData['unit_id'];
                
                // Store amount in original unit (no conversion)
                $material = Commodity::find($materialId);
                if (!$material) {
                    throw new \Exception("ماده با شناسه {$materialId} یافت نشد.");
                }
                
                $formulaData[$materialId] = [
                    'amount' => $amount,
                    'unit_id' => $unitId, // Store in original unit
                ];
            }
            
            $product->materials()->attach($formulaData);
            return true;
        });
    }

    /**
     * Update an existing product formula
     *
     * @param Commodity $product
     * @param array $materialsData
     * @return bool
     */
    public function updateFormula(Commodity $product, array $materialsData)
    {
        return DB::transaction(function () use ($product, $materialsData) {
            // Remove existing formula
            $product->materials()->detach();
            
            // Create new formula
            return $this->createFormula($product, $materialsData);
        });
    }

    /**
     * Calculate the total material cost for a product
     *
     * @param Commodity $product
     * @return float
     */
    public function calculateMaterialCost(Commodity $product): float
    {
        if ($product->type !== 'product' || !$product->materials->count()) {
            return 0;
        }

        $totalCost = 0;
        
        foreach ($product->materials as $material) {
            $amount = $material->pivot->amount;
            $unitId = $material->pivot->unit_id;
            
            // Convert to material's main unit for cost calculation
            $amountInMaterialUnit = $this->commodityUnitService->convertToMainUnit($material, $amount, $unitId);
            
            if ($amountInMaterialUnit !== null) {
                $materialCost = $amountInMaterialUnit * $material->purchase_price;
                $totalCost += $materialCost;
            }
        }
        
        return round($totalCost, 2);
    }

    /**
     * Get formula summary with both unit amounts and percentages
     *
     * @param Commodity $product
     * @return array
     */
    public function getFormulaSummary(Commodity $product): array
    {
        $materials = [];
        $totalAmount = 0;
        
        foreach ($product->materials as $material) {
            $amountInProductUnit = $material->pivot->amount;
            $totalAmount += $amountInProductUnit;
            
            $materials[] = [
                'material' => $material,
                'amount_in_product_unit' => $amountInProductUnit,
                'unit_name' => $product->unit->name,
                'unit_symbol' => $product->unit->symbol,
            ];
        }
        
        // Calculate percentages for display purposes
        foreach ($materials as &$materialData) {
            $materialData['percentage'] = $totalAmount > 0 ? 
                round(($materialData['amount_in_product_unit'] / $totalAmount) * 100, 2) : 0;
        }
        
        return [
            'materials' => $materials,
            'total_amount' => $totalAmount,
            'product_unit' => $product->unit,
        ];
    }
} 