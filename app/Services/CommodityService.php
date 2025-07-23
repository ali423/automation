<?php

namespace App\Services;

use App\Models\Commodity;
use Illuminate\Support\Facades\DB;

class CommodityService extends BaseService
{
    protected $productFormulaService;

    public function __construct(ProductFormulaService $productFormulaService)
    {
        $this->productFormulaService = $productFormulaService;
    }

    public function create($data)
    {
        $number = $this->generateUniqueNumber(Commodity::class,'number');
        if ($data['type'] == 'material') {
            return Commodity::query()->create([
                'number' => $number,
                'title' => $data['title'],
                'type' => $data['type'],
                'purchase_price' => $data['purchase_price'],
                'warning_limit'=>$data['warning_limit'],
                'unit_id' => $data['unit_id'],
            ]);
        } else {
            // Create the product first
            $product = Commodity::query()->create([
                'number' => $number,
                'title' => $data['title'],
                'sales_price' => $data['sales_price'],
                'type' => $data['type'],
                'warning_limit'=>$data['warning_limit'],
                'unit_id' => $data['unit_id']
            ]);
            
            // Prepare materials data for unit-based formula
            $materialsData = [];
            foreach ($data['materials'] as $key => $materialId) {
                $materialsData[] = [
                    'material_id' => $materialId,
                    'amount' => $data['material_amount'][$key],
                    'unit_id' => $data['material_units'][$key] ?? $data['unit_id'], // Use product unit as default
                ];
            }
            
            // Create unit-based formula
            $this->productFormulaService->createFormula($product, $materialsData);
            return true;
        }
    }

    public function update(Commodity $commodity, $data)
    {
        if ($commodity->type == 'material') {
            return $commodity->update([
                'title' => $data['title'],
                'sales_price' => null,
                'warning_limit'=>$data['warning_limit'],
                'unit_id' => $data['unit_id']

            ]);
        } else {
            // Update the product first
            $commodity->update([
                'title' => $data['title'],
                'sales_price' => $data['sales_price'],
                'warning_limit'=>$data['warning_limit'],
                'unit_id' => $data['unit_id']
            ]);
            
            // Prepare materials data for unit-based formula
            $materialsData = [];
            foreach ($data['materials'] as $key => $materialId) {
                $materialsData[] = [
                    'material_id' => $materialId,
                    'amount' => $data['material_amount'][$key],
                    'unit_id' => $data['material_units'][$key] ?? $data['unit_id'], // Use product unit as default
                ];
            }
            
            // Update unit-based formula
            $this->productFormulaService->updateFormula($commodity, $materialsData);
            return true;
        }
    }
}
