<?php

namespace App\Services;

use App\Models\Order;

class OrderService
{
   public function create($data){
      return Order::query()->create($data);
   }
    public function update(Order $order,$data){
        return $order->update($data);
    }
    public function updateStatus(Order $order){
       $order->update([
           'status'=>'done',
       ]);
    }
}
