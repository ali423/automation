<?php

namespace App\Http\Controllers;

use App\Models\Commodity;
use App\Models\Order;
use App\Models\Warehouse;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{

    public function __construct()
    {
        $this->shareView();
    }

    public function index(){
        $warehouses=Warehouse::query()->with('commodities')->where('status','active')->get()->toArray();
        foreach ($warehouses as $warehouse){
            $warehouses_res[$warehouse['id']]=[
                'title'=>$warehouse['title'],
            ];
            foreach ($warehouse['commodities'] as $commodity){
                $warehouses_res[$warehouse['id']]['amount'][]=[
                    'y'=>$commodity['pivot']['commodity_amount'],
                    'label'=>$commodity['title'],
                ];
            }
        }
        $orders=Order::query()->with('commodity.warehouses')->where('status','pending')->get();

        foreach ($orders as $order){
            $orders_res[]=[
                'title'=> $order->commodity->title.'('.$order->deadline_diff.'روز)',
                'amount'=>$order->kg_amount,
                'exists_amount'=>$order->commodity->total_amount,
            ];
        }
        return view('dashboard.index',[
            'warehouses'=>$warehouses_res?? array(),
            'orders'=>$orders_res?? array(),
        ]);
    }
}
