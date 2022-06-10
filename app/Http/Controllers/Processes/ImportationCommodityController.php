<?php

namespace App\Http\Controllers\Processes;

use App\Http\Controllers\Controller;
use App\Http\Requests\Processes\ImportationCommodityRequest;
use App\Models\Commodity;
use App\Models\ImportationCommodity;
use App\Models\Warehouse;
use App\Services\Processes\ImportationCommodityService;
use Illuminate\Http\Request;

class ImportationCommodityController extends Controller
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
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse
     */
    public function create()
    {
        $commodities=Commodity::query()->get();
        if (count($commodities) < 1){
            return redirect(route('commodity.create'))->withErrors('ابتدا حداقل یک کالا ثبت کنید .');
        }
        return view('dashboard.processes.importation-commodity.create',[
            'commodities'=>Commodity::query()->get(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Http\Response|\Illuminate\Routing\Redirector
     */
    public function store(ImportationCommodityRequest $request)
    {
        $data=$request->only('commodity_id', 'unit', 'amount');
        if ($request->hasFile('file')) {
            $file = $request->file('file');
        }
        $this->service->create($data,$file ?? null);
        return redirect(route('warehouse.index'))->with('successful', 'اطلاعات ثبت شد.');

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\ImportationCommodity  $importationCommodity
     * @return \Illuminate\Http\Response
     */
    public function show(ImportationCommodity $importationCommodity)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\ImportationCommodity  $importationCommodity
     * @return \Illuminate\Http\Response
     */
    public function edit(ImportationCommodity $importationCommodity)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\ImportationCommodity  $importationCommodity
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ImportationCommodity $importationCommodity)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\ImportationCommodity  $importationCommodity
     * @return \Illuminate\Http\Response
     */
    public function destroy(ImportationCommodity $importationCommodity)
    {
        //
    }
}
