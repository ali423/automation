<?php

namespace App\Http\Controllers;

use App\Models\Commodity;
use App\Models\Warehouse;
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
            $res[$warehouse['id']]=[
                'title'=>$warehouse['title'],
            ];
            foreach ($warehouse['commodities'] as $commodity){
                $res[$warehouse['id']]['amount'][]=[
                    'y'=>$commodity['pivot']['commodity_amount'],
                    'label'=>$commodity['title'],
                ];
            }
        }
        return view('dashboard.index',[
            'warehouses'=>$res?? array(),
        ]);
    }
}
