<?php

namespace App\Services;

use App\Models\Commodity;

use Illuminate\Support\Facades\DB;

class CommodityService extends BaseService
{
    public function create(array $data): Commodity
    {
        return DB::transaction(function () use ($data) {
            $number = $this->generateUniqueNumber(Commodity::class, 'number');
            $type = $data['type'] === 'material' 
                ? 'raw_material'  
                : 'product';

            $commodity = Commodity::create([
                'number' => $number,
                'name' => $data['title'],
                'type' => $type,
                'purchase_price' => $data['purchase_price'] ?? null,
                'sales_price' => $data['sales_price'] ?? null,
                'warning_limit' => $data['warning_limit'],
                'unit_id' => $data['unit_id'],
            ]);

            // Handle product components
            if ($type === 'product' && isset($data['materials'])) {
                $this->attachComponents($commodity, $data['materials'], $data['material_amount']);
            }

            

            return $commodity;
        });
    }

    private function attachComponents(Commodity $product, array $materialIds, array $quantities): void
    {
        $components = [];
        foreach ($materialIds as $index => $materialId) {
            $components[$materialId] = ['quantity' => $quantities[$index]];
        }
        $product->productComponents()->sync($components);
    }



    public function update(Commodity $commodity, array $data): Commodity
    {
        return DB::transaction(function () use ($commodity, $data) {
            $commodity->update([
                'name' => $data['title'],
                'sales_price' => $data['sales_price'] ?? null,
                'warning_limit' => $data['warning_limit'],
                'unit_id' => $data['unit_id'],
            ]);

            // Update product components
            if ($commodity->isProduct() && isset($data['materials'])) {
                $this->attachComponents($commodity, $data['materials'], $data['material_amount']);
            }



            return $commodity->fresh();
        });
    }
}