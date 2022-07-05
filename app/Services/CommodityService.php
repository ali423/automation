<?php

namespace App\Services;

use App\Models\Commodity;
use Illuminate\Support\Facades\DB;

class CommodityService extends BaseService
{

    public function create($data)
    {
        $number = $this->generateUniqueNumber(Commodity::class,'number');
        if ($data['type'] == 'material') {
            return Commodity::query()->create([
                'number' => $number,
                'title' => $data['title'],
                'type' => $data['type'],
                'purchase_price' => $data['purchase_price'],
                'warning_limit'=>$data['warning_limit'],
            ]);
        } else {
            $materials = null;
            foreach ($data['materials'] as $key => $value) {
                $materials[$value] = [
                    'percentage' => $data['material_amount'][$key],
                ];
            }
            return DB::transaction(function () use ($data, $number, $materials) {
                $product = Commodity::query()->create([
                    'number' => $number,
                    'title' => $data['title'],
                    'sales_price' => $data['sales_price'],
                    'type' => $data['type'],
                    'warning_limit'=>$data['warning_limit'],
                ]);
                $product->materials()->attach($materials);
                return true;
            });
        }
    }

    public function update(Commodity $commodity, $data)
    {
        if ($commodity->type == 'material') {
            return $commodity->update([
                'title' => $data['title'],
                'sales_price' => null,
                'warning_limit'=>$data['warning_limit'],
            ]);
        } else {
            $materials = null;
            foreach ($data['materials'] as $key => $value) {
                $materials[$value] = [
                    'percentage' => $data['material_amount'][$key],
                ];
            }
            return DB::transaction(function () use ($data, $commodity, $materials) {
                $commodity->update([
                    'title' => $data['title'],
                    'sales_price' => $data['sales_price'],
                    'warning_limit'=>$data['warning_limit'],
                ]);
                $commodity->materials()->sync($materials);
                return true;
            });
        }
    }
}
