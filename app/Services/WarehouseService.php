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
        $occupied_space=$warehouse->capacity-$warehouse->empty_space;
        if ($warehouse->capacity > $data['capacity'] ){
            if ($occupied_space > $data['capacity']){
                $data['success'] = false;
                $data['error'] ='ظرفیت انبار قابل کاهش نیست';
                return $data;
            }
        }
        $data['empty_space']=$data['capacity']-$occupied_space;
        return $warehouse->update($data);
    }
}
