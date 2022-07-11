<?php

namespace App\Http\Controllers;

use App\Models\Commodity;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(){
        $commodities=Commodity::query()->with('warehouses')->whereHas('warehouses',function ($q){
            $q->where('commodity_amount','>',0);
        })->get()->toArray();
        foreach ($commodities as $commodity){
            $res[]=[
                'title'=>$commodity['title'],
                'id'=>$commodity['id'],
                'warehouses'=>$commodity['warehouses'],
                'amount'=>array_column(array_column($commodity['warehouses'],'pivot'),'commodity_amount'),
            ];
        }
        return view('dashboard.index',[
            'commodities'=>$res?? null,
        ]);
    }
}
