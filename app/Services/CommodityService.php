<?php

namespace App\Services;

use App\Models\Commodity;
use Illuminate\Support\Facades\DB;

class CommodityService
{
    public function __construct()
    {
        //
    }

    public function create($data)
    {
        $number = $this->generateUniqueNumber();
        if ($data['type'] == 'material') {
            return Commodity::query()->create([
                'number' => $number,
                'title' => $data['title'],
                'type' => $data['type'],
                'purchase_price' => $data['purchase_price'],
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
                ]);
                $commodity->materials()->sync($materials);
                return true;
            });
        }
    }

    protected function generateUniqueNumber()
    {
        $number = rand(1000000, 9999999);
        while (Commodity::query()->where('number', $number)->exists()) {
            $number = rand(1000000, 9999999);
        }
        return $number;
    }
}
