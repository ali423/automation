<?php

namespace App\Services;

use App\Models\Warehouse;

class WarehouseService
{
    public function create($data){
        $data['empty_space']=$data['capacity'];
       return Warehouse::query()->create($data);
    }
    public function update(Warehouse $warehouse,$data){
        $data['empty_space']=$data['capacity'];
        return $warehouse->update($data);
    }
}
