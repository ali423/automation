<?php

namespace App\Services;

use App\Jobs\NotifyAdminsJob;
use App\Models\Commodity;
use App\Models\Warehouse;
use App\Services\InventoryService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class BaseService
{
    public function calculateCommodityAmount($amount, $unit_id){
        // Get unit from database
        $unit = \App\Models\Unit::find($unit_id);
        if (!$unit) {
            return $amount; // Return original amount if unit not found
        }
        
        // Convert amount based on unit conversion rate to main unit (kg)
        switch ($unit->name) {
            case 'keg':
                return round($amount*185, 2);
            case 'kg':
                return $amount;
            case 'twenty_liters':
                return round($amount*17.8, 2);
            default:
                return $amount; // Return original amount for unknown units
        }
    }
    public function calculateCommodityPrice($price, $unit_id){
        // Get unit from database
        $unit = \App\Models\Unit::find($unit_id);
        if (!$unit) {
            return $price; // Return original price if unit not found
        }
        
        // For now, we'll use a simple conversion based on unit name
        // This should be replaced with proper conversion logic when commodity context is available
        switch ($unit->name) {
            case 'keg':
                return round($price/185, 2); // Convert keg price to kg price
            case 'kg':
                return $price; // Already in main unit
            case 'twenty_liters':
                return round($price/17.8, 2); // Convert 20L price to kg price
            default:
                return $price; // Return original price for unknown units
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
    public function warningCommodity(Commodity $commodity){
        // Get total stock from inventory system
        $inventoryService = app(InventoryService::class);
        $total_amount = $inventoryService->getStockLevel($commodity->id, $commodity->unit_id);
        
        if ($total_amount < $commodity->warning_limit){
            NotifyAdminsJob::dispatch($commodity);
        }
    }
    protected function generateUniqueNumber($model,$field)
    {
        $number = rand(1000000, 9999999);
        while ($model::query()->where($field, $number)->exists()) {
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
