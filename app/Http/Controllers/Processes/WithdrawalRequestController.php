<?php

namespace App\Http\Controllers\Processes;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateWithdrawalRequest;
use App\Models\Commodity;
use App\Models\Customer;
use App\Models\WithdrawalRequest;
use App\Services\CommodityUnitService;
use App\Services\Processes\WithdrawalRequestService;
use Illuminate\Http\Request;

class WithdrawalRequestController extends Controller
{
    protected $service;
    protected $commodityUnitService;

    public function __construct(WithdrawalRequestService $service, CommodityUnitService $commodityUnitService)
    {
        $this->service = $service;
        $this->commodityUnitService = $commodityUnitService;
        $this->authorizeResource(WithdrawalRequest::class);
        $this->shareView();
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\Response
     */
    public function index()
    {
        $requests = WithdrawalRequest::query()
            ->with(['activities', 'commodities.unit', 'customer'])
            ->orderBy('id', 'DESC')->get();
        return view('dashboard.processes.withdrawal-request.index',
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
        $commodities = Commodity::query()->with(['unit', 'unitConversions.fromUnit', 'unitConversions.toUnit'])->get();
        $customers = Customer::query()->get();
        
        if (count($commodities) < 1) {
            return redirect(route('commodity.create'))->withErrors('ابتدا حداقل یک کالا ثبت کنید .');
        }
        if (count($customers) < 1) {
            return redirect(route('customer.create'))->withErrors('ابتدا حداقل یک مشتری ثبت کنید .');
        }
        
        return view('dashboard.processes.withdrawal-request.create', [
            'commodities' => $commodities,
            'customers' => $customers,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(CreateWithdrawalRequest $request)
    {
        $data = $request->only('commodity_id', 'unit', 'amount', 'comment', 'price', 'customer_id');
        $this->service->validationSecondLayer($data);
        
        $check_inventory = $this->service->checkWithdrawalData($data);
        if ($check_inventory['success'] == true) {
            if ($request->hasFile('file')) {
                $file = $request->file('file');
            }
            $withdrawal = $this->service->create($data, $file ?? null);
        } else {
            return redirect()->back()->withErrors($check_inventory['error']);
        }
        
        return redirect(route('withdrawal-request.show', $withdrawal))->with('successful', 'اطلاعات ثبت شد.');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\WithdrawalRequest  $withdrawalRequest
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\Response
     */
    public function show(WithdrawalRequest $withdrawalRequest)
    {
        // Load the withdrawal request with commodities and their units
        $withdrawalRequest->load(['commodities' => function ($query) {
            $query->with('unit');
        }]);
        
        return view('dashboard.processes.withdrawal-request.show', [
            'request' => $withdrawalRequest,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\WithdrawalRequest  $withdrawalRequest
     * @return \Illuminate\Http\Response
     */
    public function edit(WithdrawalRequest $withdrawalRequest)
    {
        if ($withdrawalRequest->status != 'awaiting_approval') {
            return redirect()->back()->withErrors('در این مرحله امکان ویرایش وجود ندارد .');
        }
        
        $check_expired = $this->service->checkExpiredRequest($withdrawalRequest);
        if ($check_expired['success'] == false) {
            return redirect()->back()->withErrors($check_expired['error']);
        }
        
        // Load the withdrawal request with commodities and their selectable units
        $withdrawalRequest->load(['commodities' => function ($query) {
            $query->with(['unit', 'unitConversions.fromUnit', 'unitConversions.toUnit']);
        }]);
        
        // Add selectable units to each commodity
        foreach ($withdrawalRequest->commodities as $commodity) {
            $selectableUnits = $this->commodityUnitService->getSelectableUnits($commodity);
            $commodity->selectable_units = $selectableUnits;
        }
        
        return view('dashboard.processes.withdrawal-request.edit', [
            'request' => $withdrawalRequest,
            'commodities' => Commodity::query()->with(['unit', 'unitConversions.fromUnit', 'unitConversions.toUnit'])->get(),
            'customers' => Customer::query()->get(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\WithdrawalRequest  $withdrawalRequest
     * @return \Illuminate\Http\Response
     */
    public function update(CreateWithdrawalRequest $request, WithdrawalRequest $withdrawalRequest)
    {
        if ($withdrawalRequest->status != 'awaiting_approval') {
            return redirect()->back()->withErrors('در این مرحله امکان ویرایش وجود ندارد .');
        }
        
        $check_expired = $this->service->checkExpiredRequest($withdrawalRequest);
        if ($check_expired['success'] == false) {
            return redirect()->back()->withErrors($check_expired['error']);
        }
        
        $data = $request->only('commodity_id', 'unit', 'amount', 'comment', 'price', 'customer_id');
        $this->service->validationSecondLayer($data);
        
        if ($request->hasFile('file')) {
            $file = $request->file('file');
        }
        
        $this->service->update($withdrawalRequest, $data, $file ?? null);
        return redirect(route('withdrawal-request.index'))->with('successful', 'اطلاعات درخواست ویرایش شد.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\WithdrawalRequest  $withdrawalRequest
     * @return \Illuminate\Http\Response
     */
    public function destroy(WithdrawalRequest $withdrawalRequest)
    {
        if ($withdrawalRequest->status != 'awaiting_approval') {
            return redirect()->back()->withErrors('در این مرحله امکان حذف وجود ندارد .');
        }
        
        $check_expired = $this->service->checkExpiredRequest($withdrawalRequest);
        if ($check_expired['success'] == false) {
            return redirect()->back()->withErrors($check_expired['error']);
        }
        
        $this->service->delete($withdrawalRequest);
        return redirect(route('withdrawal-request.index'))->with('successful', 'درخواست با موفقیت حذف شد.');
    }

    public function approvalRequest($id)
    {
        if (!auth()->user()->role->havePermission('status_withdrawal')) {
            return redirect()->back()->withErrors('شما این دسترسی را ندارید .');
        }
        
        $withdrawal_request = WithdrawalRequest::query()->with(['commodities' => function ($query) {
            $query->with('unit');
        }])->findOrFail($id);
        if ($withdrawal_request->status != 'awaiting_approval') {
            return redirect()->back()->withErrors('در این مرحله امکان تایید وجود ندارد .');
        }
        
        $check_expired = $this->service->checkExpiredRequest($withdrawal_request);
        if ($check_expired['success'] == false) {
            return redirect()->back()->withErrors($check_expired['error']);
        }
        
        $check_inventory = $this->service->checkWithdrawal($withdrawal_request);
        if ($check_inventory['success'] == true) {
            $this->service->approvalWithdrawal($withdrawal_request);
        } else {
            return redirect()->back()->withErrors($check_inventory['error']);
        }
        
        return redirect(route('withdrawal-request.show', $withdrawal_request))->with('successful', 'درخواست با موفقیت تایید شد.');
    }
    public function rejectRequest($id)
    {
        if (!auth()->user()->role->havePermission('status_withdrawal')) {
            return redirect()->back()->withErrors('شما این دسترسی را ندارید .');
        }
        
        $withdrawal_request = WithdrawalRequest::query()->findOrFail($id);
        if ($withdrawal_request->status != 'awaiting_approval') {
            return redirect()->back()->withErrors('در این مرحله امکان رد وجود ندارد .');
        }
        
        $check_expired = $this->service->checkExpiredRequest($withdrawal_request);
        if ($check_expired['success'] == false) {
            return redirect()->back()->withErrors($check_expired['error']);
        }
        
        $this->service->rejectWithdrawal($withdrawal_request);
        return redirect(route('withdrawal-request.show', $withdrawal_request))->with('successful', 'درخواست با موفقیت رد شد.');
    }

    /**
     * Get selectable units for a commodity via AJAX
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getSelectableUnits(\Illuminate\Http\Request $request)
    {
        $request->validate([
            'commodity_id' => 'required|exists:commodities,id',
        ]);

        $commodity = Commodity::findOrFail($request->commodity_id);
        $selectableUnits = $this->commodityUnitService->getSelectableUnits($commodity);

        return response()->json([
            'success' => true,
            'units' => $selectableUnits->map(function ($unit) {
                return [
                    'id' => $unit->id,
                    'name' => $unit->name,
                    'symbol' => $unit->symbol,
                    'display_name' => $unit->name . ' (' . $unit->symbol . ')'
                ];
            })
        ]);
    }
}
