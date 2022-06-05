<?php

namespace App\Services;

use App\Models\Warehouse;

class WarehouseService
{
    public function create($data){
       return Warehouse::query()->create($data);
    }
    public function update(Warehouse $warehouse,$data){
      return $warehouse->update($data);
    }
}
