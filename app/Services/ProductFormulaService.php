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
     * Convert material amount to product's main unit
     *
     * @param Commodity $material
     * @param float $amount
     * @param int $unitId
     * @param Commodity $product
     * @return float
     */
    public function convertToProductUnit(Commodity $material, float $amount, int $unitId, Commodity $product): float
    {
        // Since products must use kg, we can use convertToKg
        return $this->convertToKg($material, $amount, $unitId);
    }

    /**
     * Convert material amount to kg (product unit)
     *
     * @param Commodity $material
     * @param float $amount
     * @param int $unitId
     * @return float
     */
    public function convertToKg(Commodity $material, float $amount, int $unitId): float
    {
        // First convert to material's main unit
        $amountInMaterialUnit = $this->commodityUnitService->convertToMainUnit($material, $amount, $unitId);
        
        if ($amountInMaterialUnit === null) {
            throw new \Exception("نمی‌توان مقدار را به واحد اصلی ماده تبدیل کرد");
        }
        
        // If material's main unit is already kg, return the amount as is
        if ($material->unit->symbol === 'kg') {
            return $amountInMaterialUnit;
        }
        
        // Find kg unit
        $kgUnit = Unit::where('symbol', 'kg')->first();
        if (!$kgUnit) {
            throw new \Exception('واحد کیلوگرم در پایگاه داده یافت نشد');
        }
        
        // Try to convert from material's main unit to kg
        $convertedAmount = $this->unitConversionService->convert(
            $amountInMaterialUnit,
            $material->unit_id,
            $kgUnit->id,
            $material->id // Use material's conversion rates
        );
        
        if ($convertedAmount !== null) {
            return $convertedAmount;
        }
        
        // If no conversion exists, throw an exception - user must define proper conversions
        throw new \Exception("برای ماده '{$material->title}' نرخ تبدیل از {$material->unit->name} به کیلوگرم تعریف نشده است. لطفاً در بخش تبدیل واحد، نرخ تبدیل مناسب را تعریف کنید.");
    }

    /**
     * Calculate the total cost of materials for a product
     *
     * @param Commodity $product
     * @return float
     */
    public function calculateMaterialCost(Commodity $product): float
    {
        $totalCost = 0;
        
        foreach ($product->materials as $material) {
            $amountInProductUnit = $material->pivot->amount;
            
            // Convert to material's main unit for cost calculation
            $amountInMaterialUnit = $this->unitConversionService->convert(
                $amountInProductUnit,
                $product->unit_id,
                $material->unit_id,
                $material->id
            );
            
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

    /**
     * Validate that material amounts are reasonable
     *
     * @param array $materialsData
     * @param Commodity $product
     * @return array
     */
    public function validateFormula(array $materialsData, Commodity $product): array
    {
        $errors = [];
        $totalAmount = 0;
        
        // Ensure product unit is kg
        if ($product->unit->symbol !== 'kg') {
            $errors[] = "محصولات باید از کیلوگرم به عنوان واحد استفاده کنند.";
            return [
                'valid' => false,
                'errors' => $errors,
                'total_amount' => 0,
            ];
        }
        
        foreach ($materialsData as $index => $materialData) {
            $material = Commodity::find($materialData['material_id']);
            
            if (!$material) {
                $errors[] = "ماده در شاخص {$index} یافت نشد";
                continue;
            }
            
            if ($material->type !== 'material') {
                $errors[] = "فقط مواد می‌توانند در فرمول محصولات استفاده شوند";
                continue;
            }
            
            $amount = $materialData['amount'];
            $unitId = $materialData['unit_id'];
            
            if ($amount <= 0) {
                $errors[] = "مقدار باید بزرگتر از 0 باشد برای {$material->title}";
                continue;
            }
            
            // Convert to kg and add to total
            try {
                $amountInKg = $this->convertToKg($material, $amount, $unitId);
                $totalAmount += $amountInKg;
            } catch (\Exception $e) {
                $errors[] = "نمی‌توان مقدار را برای {$material->title} تبدیل کرد: " . $e->getMessage();
            }
        }
        
        if ($totalAmount <= 0) {
            $errors[] = "مجموع مقدار مواد باید بزرگتر از 0 باشد";
        }
        
        return [
            'valid' => empty($errors),
            'errors' => $errors,
            'total_amount' => $totalAmount,
        ];
    }
} 