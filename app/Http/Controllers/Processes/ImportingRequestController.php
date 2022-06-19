<?php

namespace App\Http\Controllers\Processes;

use App\Http\Controllers\Controller;
use App\Models\Commodity;
use App\Models\ImportingRequest;
use App\Models\Warehouse;
use App\Services\Processes\ImportationCommodityService;
use Illuminate\Http\Request;

class ImportingRequestController extends Controller
{
    protected $service;

    public function __construct(ImportationCommodityService $service)
    {
        $this->service=$service;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\Response
     */
    public function create()
    {
        $commodities=Commodity::query()->get();
        $warehouses=Warehouse::query()->where('status','active')->get();
        if (count($commodities) < 1){
            return redirect(route('commodity.create'))->withErrors('ابتدا حداقل یک کالا ثبت کنید .');
        }
        if (count($warehouses) < 1){
            return redirect(route('warehouse.create'))->withErrors('ابتدا حداقل یک انبار ثبت کنید .');
        }
        return view('dashboard.processes.importing-request.create',[
            'commodities'=>Commodity::query()->get(),
            'warehouses'=>Warehouse::query()->where('status','active')->get(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data=$request->only('commodity_id', 'warehouse_id', 'unit', 'capacity');
        if ($request->hasFile('logo')) {
            $file = $request->file('logo');
        }
        $this->service->create($data,$file ?? null);
        return redirect(route('warehouse.index'))->with('successful', 'اطلاعات ثبت شد.');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\ImportingRequest  $importingRequest
     * @return \Illuminate\Http\Response
     */
    public function show(ImportingRequest $importingRequest)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\ImportingRequest  $importingRequest
     * @return \Illuminate\Http\Response
     */
    public function edit(ImportingRequest $importingRequest)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\ImportingRequest  $importingRequest
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ImportingRequest $importingRequest)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\ImportingRequest  $importingRequest
     * @return \Illuminate\Http\Response
     */
    public function destroy(ImportingRequest $importingRequest)
    {
        //
    }
}
