<?php

namespace App\Services;

use App\Models\Customer;

class CustomerService
{

    public function create($data){
       return Customer::query()->create($data);
    }
    public function update(Customer $customer,$data){
        return $customer->update($data);
    }
}
