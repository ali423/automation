<?php

namespace App\Services;

use App\Models\Warehouse;
use Illuminate\Support\Facades\DB;

class BaseService
{
    public function calculateCommodityAmount($amount,$unit){
        switch ($unit) {
            case 'keg':
                return round($amount*185,2);
            case 'kg':
                return $amount;
            case 'twenty_liters':
                return round($amount*17.8,2);
        }
    }
    public function uploadFile($file,$patch,$attached){
        $user=auth()->user();
        $file_name=$file->getClientOriginalName();
        $data['file'] = $file->storeAs('public/upload/'.$patch, str_shuffle(time()) . $file_name);
        $attached->files()->create([
            'user_id' => $user->id,
            'source' => $data['file'],
            'name'=>explode('.',$file_name)[0],
            'format'=>$file->extension(),
             'size'=>$file->getSize(),
        ]);
    }
    public function recalculateWarehousesEmptySpace($warehouses){
        DB::transaction(function () use ($warehouses) {

            foreach ($warehouses as $warehouse) {
                $occupied_space = array_sum(array_column(array_column($warehouse->commodities->toArray(), 'pivot'), 'commodity_amount'));
                $warehouse->update([
                    'empty_space' => $warehouse->empty_space - $occupied_space,
                ]);
            }
        });
    }
}
