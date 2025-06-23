<?php

namespace App\Services;

use App\Models\Commodity;
use App\Models\Inventory;
use Illuminate\Support\Facades\DB;

class ProductionService
{
    public function produceProduct(Commodity $product, float $quantity): Inventory
    {
        if (!$product->isProduct()) {
            throw new \InvalidArgumentException("تنها محصولات نهایی قابل تولید هستند");
        }

        return DB::transaction(function () use ($product, $quantity) {
            // Deduct raw materials
            foreach ($product->productComponents as $component) {
                $requiredQty = $component->pivot->quantity * $quantity;
                $this->consumeMaterial($component, $requiredQty);
            }

            // Create finished product inventory
            return Inventory::create([
                'commodity_id' => $product->id,
                'unit_id' => $product->unit_id,
                'quantity' => $quantity,
                'purchase_price' => $product->base_price,
                'sale_price' => $product->sales_price,
                'active' => true
            ]);
        });
    }

    private function consumeMaterial(Commodity $material, float $quantity): void
    {
        if (!$material->isRawMaterial()) {
            throw new \InvalidArgumentException("تنها مواد اولیه قابل مصرف هستند");
        }

        DB::transaction(function () use ($material, $quantity) {
            $remaining = $quantity;
            $batches = $material->inventoryItems()
                ->where('quantity', '>', 0)
                ->orderBy('created_at')
                ->get();

            foreach ($batches as $batch) {
                $deduct = min($remaining, $batch->quantity);
                $batch->decrement('quantity', $deduct);
                $remaining -= $deduct;

                if ($remaining <= 0) break;
            }

            if ($remaining > 0) {
                throw new \RuntimeException(" وجود ندارد {$material->name} موجودی کافی برای ");
            }
        });
    }
}