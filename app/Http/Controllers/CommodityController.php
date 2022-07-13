<?php

namespace App\Http\Controllers;

use App\Http\Requests\CommodityRequest;
use App\Http\Requests\CommodityUpdateRequest;
use App\Models\Commodity;
use App\Services\CommodityService;
use Illuminate\Http\Request;

class CommodityController extends Controller
{
    protected $service;

    public function __construct(CommodityService $service)
    {
        $this->service = $service;
        $this->authorizeResource(Commodity::class);
        $this->shareView();
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\Response
     */
    public function index()
    {
        $commodities = Commodity::query()->orderBy('id', 'DESC')->get();
        return view('dashboard.commodity.index',
            [
                'commodities' => $commodities,
            ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\Response
     */
    public function create()
    {
        return view('dashboard.commodity.create', [
            'materials' => Commodity::query()->where('type','material')->get(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Http\Response|\Illuminate\Routing\Redirector
     */
    public function store(CommodityRequest $request)
    {
        $this->service->create($request->validationData());
        return redirect(route('commodity.index'))->with('successful', 'اطلاعات ثبت شد.');
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Models\Commodity $commodity
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\Response
     */
    public function show(Commodity $commodity)
    {
        $materials = $commodity->materials;
        return view('dashboard.commodity.show', [
            'commodity' => $commodity,
            'materials' => $materials,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Models\Commodity $commodity
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\Response
     */
    public function edit(Commodity $commodity)
    {
        $used_materials = $commodity->materials;

        return view('dashboard.commodity.edit', [
            'commodity' => $commodity,
            'materials' => Commodity::all(),
            'used_materials' => $used_materials,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Commodity $commodity
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Http\Response|\Illuminate\Routing\Redirector
     */
    public function update(CommodityUpdateRequest $request, Commodity $commodity)
    {
        $this->service->update($commodity, $request->validationData());
        return redirect(route('commodity.show', $commodity))->with('successful', 'اطلاعات ویرایش شد.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\Commodity $commodity
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Http\Response|\Illuminate\Routing\Redirector
     */
    public function destroy(Commodity $commodity)
    {
        $commodity->delete();
        return redirect(route('commodity.index'))->with('successful', 'اطلاعات حذف شدند.');
    }

    public function inventory($id)
    {
        $commodity = Commodity::query()->findOrFail($id);
        $warehouses=$commodity->warehouses()->where('commodity_amount','>',0)->get()->toArray();
           foreach ($warehouses as $warehouse){
               $warehouse_res[]=[
                   'id'=>$warehouse['id'],
                   'title'=>$warehouse['title'],
                   'amount'=>$warehouse['pivot']['commodity_amount'],
               ];
           }
        $res = [
          'warehouses'=>$warehouse_res ?? null,
          'price'=>$commodity->sales_price,
        ];
        return response()->json($res);
    }
}
