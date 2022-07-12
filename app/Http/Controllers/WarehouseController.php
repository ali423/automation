<?php

namespace App\Http\Controllers;

use App\Http\Requests\WarehouseRequest;
use App\Http\Requests\WarehouseUpdateRequest;
use App\Models\Warehouse;
use App\Services\WarehouseService;
use Illuminate\Http\Request;

class WarehouseController extends Controller
{

    protected $service;

    public function __construct(WarehouseService $service)
    {
        $this->service=$service;
        $this->authorizeResource(Warehouse::class);

    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\Response
     */
    public function index()
    {
        $warehouses=Warehouse::query()->orderBy('id', 'DESC')->get();
        return view('dashboard.warehouse.index',
            [
                'warehouses'=>$warehouses,
            ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\Response
     */
    public function create()
    {
        return view('dashboard.warehouse.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Http\Response|\Illuminate\Routing\Redirector
     */
    public function store(WarehouseRequest $request)
    {
        $this->service->create($request->only('title','type','capacity','status'));
        return redirect(route('warehouse.index'))->with('successful', 'اطلاعات ثبت شد.');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Warehouse  $warehouse
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\Response
     */
    public function show(Warehouse $warehouse)
    {
        return view('dashboard.warehouse.show',[
            'warehouse'=>$warehouse,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Warehouse  $warehouse
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse
     */
    public function edit(Warehouse $warehouse)
    {
        return view('dashboard.warehouse.edit',[
            'warehouse'=>$warehouse,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Warehouse  $warehouse
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Http\Response|\Illuminate\Routing\Redirector
     */
    public function update(WarehouseUpdateRequest $request, Warehouse $warehouse)
    {
        $res= $this->service->update($warehouse,$request->only('title','type','capacity','status'));
        if (isset($res['success']) && $res['success']== false){
            return redirect()->back()->withErrors($res['error']);
        }
        return redirect(route('warehouse.show',$warehouse))->with('successful', 'اطلاعات ویرایش شدند.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Warehouse  $warehouse
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Http\Response|\Illuminate\Routing\Redirector
     */
    public function destroy(Warehouse $warehouse)
    {
        $warehouse->delete();
        return redirect(route('warehouse.index'))->with('successful', 'اطلاعات حذف شدند.');
    }
}
