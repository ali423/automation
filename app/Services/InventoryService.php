<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class InventoryService extends BaseService
{

    public function updateAmount($warehouse,$data){
        $exits_commodity = $warehouse->commodities->find($data['commodity']);
        if ($data['commodity_amount'] > $exits_commodity->pivot->commodity_amount){
            $increased_value=$data['commodity_amount']-$exits_commodity->pivot->commodity_amount;
            if ($increased_value > $warehouse->empty_space ){
                $data['success'] = false;
                $data['error'] ='انبار گنجایش کافی ندارد';
                return $data;
            }
        }
        DB::transaction(function () use ($warehouse,$data) {
            $warehouse->commodities()->updateExistingPivot($data['commodity'], ['commodity_amount' => $data['commodity_amount']],true);
            $this->recalculateWarehousesEmptySpace([$warehouse]);
        });
        $data['success'] = true;
        return  $data;
    }
}
