<?php

namespace App\Services;

use App\Models\Warehouse;
use Carbon\Carbon;
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
                $zero_commodities=$warehouse->commodities()->where('commodity_amount',0)->get()->toArray();
                if (count($zero_commodities)>0){
                    $warehouse->commodities()->detach(array_column($zero_commodities,'id'));
                }
                $occupied_space = array_sum(array_column(array_column($warehouse->commodities()->get()->toArray(), 'pivot'), 'commodity_amount'));
                $warehouse->update([
                    'empty_space' => $warehouse->capacity - $occupied_space,
                ]);
            }
        });
    }
    protected function generateUniqueNumber($model,$field)
    {
        $number = rand(1000000, 9999999);
        while ($model::query()->where('number', $field)->exists()) {
            $number = rand(1000000, 9999999);
        }
        return $number;
    }
    public function checkExpiredRequest($importing_request){
        if (Carbon::now()->diffInDays($importing_request->created_at) > 7){
            $importing_request->update([
                'status'=>'expired',
            ]);
            $data['success'] = false;
            $data['error'] ='درخواست منقضی شده است';
            return $data;
        }
        $data['success'] = true;
        return $data;
    }
}
