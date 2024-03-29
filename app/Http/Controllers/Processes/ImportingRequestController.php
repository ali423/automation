<?php

namespace App\Http\Controllers\Processes;

use App\Http\Controllers\Controller;
use App\Http\Requests\importingReportRequest;
use App\Http\Requests\Processes\CreateImportingRequest;
use App\Models\Commodity;
use App\Models\ImportingRequest;
use App\Models\Seller;
use App\Models\Warehouse;
use App\Services\Processes\ImportingRequestService;
use Morilog\Jalali\Jalalian;

class ImportingRequestController extends Controller
{
    protected $service;

    public function __construct(ImportingRequestService $service)
    {
        $this->service = $service;
        $this->authorizeResource(ImportingRequest::class);
        $this->shareView();
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\Response
     */
    public function index()
    {
        $requests = ImportingRequest::query()
            ->with('activities')
            ->orderBy('id', 'DESC')->get();
        return view('dashboard.processes.importing-request.index',
            [
                'requests' => $requests,
            ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\Response
     */
    public function create()
    {
        $commodities = Commodity::query()->get();
        $warehouses = Warehouse::query()->where('status', 'active')->get();
        $sellers=Seller::all();
        if (count($commodities) < 1) {
            return redirect(route('commodity.create'))->withErrors('ابتدا حداقل یک کالا ثبت کنید .');
        }
        if (count($warehouses) < 1) {
            return redirect(route('warehouse.create'))->withErrors('ابتدا حداقل یک انبار ثبت کنید .');
        }
        if (count($sellers) < 1) {
            return redirect(route('seller.create'))->withErrors('ابتدا حداقل یک فروشنده ثبت کنید .');
        }
        return view('dashboard.processes.importing-request.create', [
            'commodities' => $commodities,
            'warehouses' => $warehouses,
            'sellers'=>$sellers,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Http\Response|\Illuminate\Routing\Redirector
     */
    public function store(CreateImportingRequest $request)
    {
        $data = $request->only('commodity_id', 'warehouse_id', 'unit', 'amount', 'comment', 'purchase_price','seller_id');
        $this->service->validationSecondLayer($data);
        $check_warehouses = $this->service->checkImportingStore($data);
        if ($check_warehouses['success'] == true) {
            if ($request->hasFile('file')) {
                $file = $request->file('file');
            }
            $importing = $this->service->create($data, $file ?? null);
        } else {
            return redirect()->back()->withErrors($check_warehouses['error']);
        }
        return redirect(route('importing-request.show', $importing))->with('successful', 'اطلاعات ثبت شد.');
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Models\ImportingRequest $importingRequest
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\Response
     */
    public function show(ImportingRequest $importingRequest)
    {
        return view('dashboard.processes.importing-request.show', [
            'request' => $importingRequest,
            'warehouses' => Warehouse::all(),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Models\ImportingRequest $importingRequest
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\Response
     */
    public function edit(ImportingRequest $importingRequest)
    {
        return view('dashboard.processes.importing-request.edit', [
            'request' => $importingRequest,
            'commodities' => Commodity::query()->get(),
            'warehouses' => Warehouse::all(),
            'sellers'=>Seller::all(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\ImportingRequest $importingRequest
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(CreateImportingRequest $request, ImportingRequest $importingRequest)
    {
        if ($importingRequest->status != 'awaiting_approval') {
            return redirect()->back()->withErrors('در این مرحله امکان ویرایش وجود ندارد .');
        }
        $check_expired = $this->service->checkExpiredRequest($importingRequest);
        if ($check_expired['success'] == false) {
            return redirect()->back()->withErrors($check_expired['error']);
        }
        $data = $request->only('commodity_id', 'warehouse_id', 'unit', 'amount', 'comment', 'purchase_price','seller_id');
        $this->service->validationSecondLayer($data);
        if ($request->hasFile('file')) {
            $file = $request->file('file');
        }
        $this->service->update($importingRequest, $data, $file ?? null);
        return redirect(route('importing-request.index'))->with('successful', 'اطلاعات درخواست ویرایش شد.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\ImportingRequest $importingRequest
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(ImportingRequest $importingRequest)
    {
        if ($importingRequest->status != 'awaiting_approval') {
            return redirect()->back()->withErrors('در این مرحله امکان ویرایش وجود ندارد .');
        }
        $check_expired = $this->service->checkExpiredRequest($importingRequest);
        if ($check_expired['success'] == false) {
            return redirect()->back()->withErrors($check_expired['error']);
        }
        $this->service->delete($importingRequest);
        return redirect(route('importing-request.index'))->with('successful', 'درخواست با موفقیت حذف شد.');
    }

    public function approvalRequest($id)
    {
        if (!auth()->user()->role->havePermission('status_importing')) {
            return redirect()->back()->withErrors('شما این دسترسی را ندارید .');
        }
        $importing_request = ImportingRequest::query()->findOrFail($id);
        if ($importing_request->status != 'awaiting_approval') {
            return redirect()->back()->withErrors('در این مرحله امکان تایید وجود ندارد .');
        }
        $check_expired = $this->service->checkExpiredRequest($importing_request);
        if ($check_expired['success'] == false) {
            return redirect()->back()->withErrors($check_expired['error']);
        }
        $check_warehouses = $this->service->checkImporting($importing_request);
        if ($check_warehouses['success'] == true) {
            $this->service->approvalImporting($importing_request);
        } else {
            return redirect()->back()->withErrors($check_warehouses['error']);
        }
        return redirect(route('importing-request.show', $importing_request))->with('successful', 'درخواست با موفقیت تایید شد.');
    }

    public function rejectRequest($id)
    {
        if (!auth()->user()->role->havePermission('status_importing')) {
            return redirect()->back()->withErrors('شما این دسترسی را ندارید .');
        }
        $importing_request = ImportingRequest::query()->findOrFail($id);
        if ($importing_request->status != 'awaiting_approval') {
            return redirect()->back()->withErrors('در این مرحله امکان رد وجود ندارد .');
        }
        $check_expired = $this->service->checkExpiredRequest($importing_request);
        if ($check_expired['success'] == false) {
            return redirect()->back()->withErrors($check_expired['error']);
        }
        $this->service->rejectImporting($importing_request);
        return redirect(route('importing-request.show', $importing_request))->with('successful', 'درخواست با موفقیت رد شد.');
    }

    public function createReport()
    {
        $commodities = Commodity::query()->where('type', 'material')->whereHas('importingRequests')->get();
        if ($commodities->count() < 1) {
            return redirect()->back()->withErrors('ابتدا حداقل یک کالا و درخواست خرید ثبت کنید .');
        }
        return view('dashboard.processes.importing-request.report-create', [
            'commodities' => $commodities,
        ]);
    }

    public function storeReport(importingReportRequest $request)
    {
        $data = $request->validated();
        $requests=$this->service->getReportData($data);
        if (isset($requests['error'])){
            return redirect(route('importing.report.create'))->withErrors($requests['error']);
        }
        return view('dashboard.processes.importing-request.report-show', [
            'requests' => $requests,
        ]);
    }




}
