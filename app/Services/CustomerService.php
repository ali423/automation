<?php

namespace App\Services;

use App\Models\Customer;

class CustomerService
{

    public function create($data){
        // Check if there's a soft-deleted customer with the same mobile number
        $existingCustomer = Customer::withTrashed()
            ->where('mobile', $data['mobile'])
            ->first();

        if ($existingCustomer && $existingCustomer->trashed()) {
            // Restore the soft-deleted customer and update with new data
            $existingCustomer->restore();
            $existingCustomer->update($data);
            return $existingCustomer;
        }

        // Create new customer if no soft-deleted customer exists
        return Customer::query()->create($data);
    }
    public function update(Customer $customer,$data){
        return $customer->update($data);
    }
}
