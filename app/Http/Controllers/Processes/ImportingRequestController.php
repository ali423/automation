<?php

namespace App\Http\Controllers\Processes;

use App\Http\Controllers\Controller;
use App\Http\Requests\Processes\CreateImportingRequest;
use App\Models\Commodity;
use App\Models\ImportingRequest;
use App\Models\Warehouse;
use App\Services\Processes\ImportingRequestService;
use Illuminate\Http\Request;

class ImportingRequestController extends Controller
{
    protected $service;

    public function __construct(ImportingRequestService $service)
    {
        $this->service=$service;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\Response
     */
    public function index()
    {
        $requests=ImportingRequest::query()->orderBy('id', 'DESC')->get();
        return view('dashboard.processes.importing-request.index',
            [
                'requests'=>$requests,
            ]);
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
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Http\Response|\Illuminate\Routing\Redirector
     */
    public function store(CreateImportingRequest $request)
    {
        $data=$request->only('commodity_id', 'warehouse_id', 'unit', 'amount','comment');
        if ($request->hasFile('file')) {
            $file = $request->file('file');
        }
        $this->service->create($data,$file ?? null);
        return redirect(route('importing-request.index'))->with('successful', 'اطلاعات ثبت شد.');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\ImportingRequest  $importingRequest
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\Response
     */
    public function show(ImportingRequest $importingRequest)
    {
        return view('dashboard.processes.importing-request.show',[
            'request'=>$importingRequest,
            'warehouses'=>Warehouse::all(),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\ImportingRequest  $importingRequest
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\Response
     */
    public function edit(ImportingRequest $importingRequest)
    {
        return view('dashboard.processes.importing-request.edit',[
            'request'=>$importingRequest,
            'commodities'=>Commodity::query()->get(),
            'warehouses'=>Warehouse::all(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\ImportingRequest  $importingRequest
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(CreateImportingRequest $request, ImportingRequest $importingRequest)
    {
        if ($importingRequest->status !='awaiting_approval'){
            return redirect()->back()->withErrors('در این مرحله امکان ویرایش وجود ندارد .');
        }
        $data=$request->only('commodity_id', 'warehouse_id', 'unit', 'amount','comment');
        if ($request->hasFile('file')) {
            $file = $request->file('file');
        }
        $this->service->update($importingRequest,$data,$file ?? null);
        return redirect(route('importing-request.index'))->with('successful', 'اطلاعات درخواست ویرایش شد.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\ImportingRequest  $importingRequest
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(ImportingRequest $importingRequest)
    {
        if ($importingRequest->status !='awaiting_approval'){
            return redirect()->back()->withErrors('در این مرحله امکان ویرایش وجود ندارد .');
        }
        $this->service->delete($importingRequest);
        return redirect(route('importing-request.index'))->with('successful', 'درخواست با موفقیت حذف شد.');
    }
}
