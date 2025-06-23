<?php

namespace App\Services;

use App\Models\Inventory;
use Illuminate\Support\Facades\DB;

class InventoryService extends BaseService
{
    public function create(array $data): Inventory
    {
        return DB::transaction(function () use ($data) {
            return Inventory::create($data);
        });
    }

    public function update(Inventory $inventory, array $data): Inventory
    {
        return DB::transaction(function () use ($inventory, $data) {
            $inventory->update($data);
            return $inventory->fresh();
        });
    }

    public function updateQuantity(Inventory $inventory, float $quantity): Inventory
    {
        return DB::transaction(function () use ($inventory, $quantity) {
            $inventory->update(['quantity' => $quantity]);
            return $inventory->fresh();
        });
    }

    public function incrementQuantity(Inventory $inventory, float $delta): Inventory
    {
        return DB::transaction(function () use ($inventory, $delta) {
            $inventory->increment('quantity', $delta);
            return $inventory->fresh();
        });
    }

    public function decrementQuantity(Inventory $inventory, float $delta): Inventory
    {
        return DB::transaction(function () use ($inventory, $delta) {
            if ($inventory->quantity < $delta) {
                throw new \RuntimeException("Insufficient stock");
            }
            
            $inventory->decrement('quantity', $delta);
            return $inventory->fresh();
        });
    }
}