<?php

namespace App\Services;

use App\Models\Commodity;
use App\Models\Inventory;
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

            // Create initial inventory
            $this->createInitialInventory($commodity, $data);

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

    private function createInitialInventory(Commodity $commodity, array $data): void
    {
        $initialPrice = $commodity->isProduct() 
            ? $commodity->base_price 
            : ($data['purchase_price'] ?? 0);

        Inventory::create([
            'commodity_id' => $commodity->id,
            'unit_id' => $data['unit_id'],
            'quantity' => 0,
            'purchase_price' => $initialPrice,
            'sale_price' => $data['sales_price'] ?? $initialPrice,
            'active' => true
        ]);
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

            // Update inventory unit
            if ($inventory = $commodity->inventoryItems->first()) {
                $inventory->update(['unit_id' => $data['unit_id']]);
            }

            return $commodity->fresh();
        });
    }
}