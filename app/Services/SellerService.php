<?php

namespace App\Services;

use App\Models\Seller;

class SellerService
{
    public function create($data){
        return Seller::query()->create($data);
    }
    public function update(Seller $customer,$data){
        return $customer->update($data);
    }
}
