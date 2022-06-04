<?php

namespace App\Services;

use App\Models\Commodity;

class CommodityService
{
    public function __construct()
    {
        //
    }

    public function create($data)
    {
        $number=$this->generateUniqueNumber();
        if ($data['type'] == 'material') {
           return Commodity::query()->create([
                'number' => $number,
               'title' => $data['title'],
                'type' => $data['type'],
            ]);
        }else{
          return  Commodity::query()->create([
                'number' => $number,
                'title' => $data['title'],
                'amount' => $data['amount'],
                'type' => $data['type'],
            ]);
        }
    }
    public function update(Commodity $commodity,$data)
    {
        if ($data['type'] == 'material') {
            return $commodity->update([
                'title' => $data['title'],
                'amount' => null,
                'type' => $data['type'],
            ]);
        }else{
            return  $commodity->update([
                'title' => $data['title'],
                'amount' => $data['amount'],
                'type' => $data['type'],
            ]);
        }
    }

    protected function generateUniqueNumber(){
        $number=rand(1000000,9999999);
        while (Commodity::query()->where('number',$number)->exists()){
            $number=rand(1000000,9999999);
        }
        return $number;
    }
}
