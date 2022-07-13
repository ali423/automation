<?php

namespace App\Http\Controllers;

use App\Http\Requests\InventoryEditRequest;
use App\Http\Requests\InventoryUpdateRequest;
use App\Models\Warehouse;
use App\Services\InventoryService;
use Illuminate\Http\Request;

class InventoryController extends Controller
{
    protected $service;

    public function __construct(InventoryService $service)
    {
        $this->service=$service;
        $this->authorizeResource(Warehouse::class);
        $this->shareView();
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\Response
     */
    public function index()
    {
        $warehouses=Warehouse::query()->orderBy('id', 'DESC')->get();
        return view('dashboard.inventory.index',
            [
                'warehouses'=>$warehouses,
            ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\Response
     */
    public function show($id)
    {
        $warehouse=Warehouse::query()->findOrFail($id);
        return view('dashboard.inventory.show',[
            'warehouse'=>$warehouse,
            'commodities'=>$warehouse->commodities,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\Response
     */
    public function edit(InventoryEditRequest $request)
    {
        $warehouse=Warehouse::query()->findOrFail($request->get('warehouse'));
        $commodity=$warehouse->commodities()->where('commodity_id',$request->get('commodity'))->firstOrFail();
        return view('dashboard.inventory.edit',[
            'warehouse'=>$warehouse,
            'commodity'=>$commodity,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Http\Response|\Illuminate\Routing\Redirector
     */
    public function update(InventoryUpdateRequest $request)
    {
        $warehouse=Warehouse::query()->findOrFail($request->get('warehouse'));
        $res=$this->service->updateAmount($warehouse,$request->only('commodity','commodity_amount'));
        if (isset($res['success']) && $res['success']== false){
            return redirect()->back()->withErrors($res['error']);
        }
        return redirect(route('inventory.show',$warehouse))->with('successful', 'اطلاعات ویرایش شدند.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
