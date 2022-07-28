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
        $commodities = Commodity::query()->whereHas('warehouses')->with('warehouses')->get();
        $warehouses=Warehouse::query()->whereHas('commodities')->with('commodities')->where('status','active')->get();
        foreach ($warehouses as $warehouse){
            $warehouses_res[$warehouse->id]=[
                'title'=>$warehouse->title,
            ];
            foreach ($commodities as $commodity) {
                $commodity_amount = $warehouse->commodities->find($commodity->id);
                $warehouses_res[$warehouse->id]['amount'][] = [
                    'y' => (float) ($commodity_amount->pivot->commodity_amount ?? 0),
                    'label' => $commodity->title,
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
