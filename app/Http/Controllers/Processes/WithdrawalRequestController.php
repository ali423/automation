<?php

namespace App\Http\Controllers\Processes;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateWithdrawalRequest;
use App\Models\Commodity;
use App\Models\Customer;
use App\Models\WithdrawalRequest;
use App\Services\CommodityUnitService;
use App\Services\Processes\WithdrawalRequestService;
use Illuminate\Support\Facades\DB;

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
            ->with(['commodities.unit', 'customer'])
            ->orderBy('id', 'DESC')->get();
        return view('dashboard.processes.withdrawal-request.index',
            [
                'requests' => $requests,
            ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
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
        
        // Preload all selectable units for each commodity
        $commoditiesWithUnits = $commodities->map(function ($commodity) {
            $selectableUnits = $this->commodityUnitService->getSelectableUnits($commodity);
            $commodity->selectable_units = $selectableUnits;
            return $commodity;
        });
        
        return view('dashboard.processes.withdrawal-request.create', [
            'commodities' => $commoditiesWithUnits,
            'customers' => $customers,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param CreateWithdrawalRequest $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Http\Response|\Illuminate\Routing\Redirector
     */
    public function store(CreateWithdrawalRequest $request)
    {
        $data = $request->only('commodity_id', 'unit_id', 'amount', 'comment', 'price', 'customer_id');
        
        // Debug: Log the extracted data
        \Log::info('WithdrawalRequestController::store - Extracted data:', $data);
        
        $this->service->validationSecondLayer($data);
        
        $check_inventory = $this->service->checkWithdrawalData($data);
        if ($check_inventory['success'] == true) {
            $file = null;
            if ($request->hasFile('file')) {
                $file = $request->file('file');
            }
            
            $withdrawal = DB::transaction(function () use ($data, $file) {
                return $this->service->create($data, $file);
            });
        } else {
            return redirect()->back()->withErrors($check_inventory['error']);
        }
        
        return redirect(route('withdrawal-request.show', $withdrawal))->with('successful', 'اطلاعات ثبت شد.');
    }

    /**
     * Display the specified resource.
     *
     * @param WithdrawalRequest $withdrawalRequest
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\Response
     */
    public function show(WithdrawalRequest $withdrawalRequest)
    {
        // Load the withdrawal request with commodities and their units
        $withdrawalRequest->load(['commodities' => function ($query) {
            $query->with('unit');
        }]);
        
        // Also load the unit for each commodity's pivot data
        foreach ($withdrawalRequest->commodities as $commodity) {
            if ($commodity->pivot->unit_id) {
                $commodity->pivot->unit = \App\Models\Unit::find($commodity->pivot->unit_id);
            }
        }
        
        return view('dashboard.processes.withdrawal-request.show', [
            'request' => $withdrawalRequest,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param WithdrawalRequest $withdrawalRequest
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function edit(WithdrawalRequest $withdrawalRequest)
    {
        // Prevent editing of approved, rejected, expired, or done requests
        if (!in_array($withdrawalRequest->status, ['awaiting_approval'])) {
            return redirect()->back()->withErrors('در این مرحله امکان ویرایش وجود ندارد. درخواست‌های تایید شده، رد شده، منقضی شده یا تکمیل شده قابل ویرایش نیستند.');
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
     * @param CreateWithdrawalRequest $request
     * @param WithdrawalRequest $withdrawalRequest
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(CreateWithdrawalRequest $request, WithdrawalRequest $withdrawalRequest)
    {
        // Prevent editing of approved, rejected, expired, or done requests
        if (!in_array($withdrawalRequest->status, ['awaiting_approval'])) {
            return redirect()->back()->withErrors('در این مرحله امکان ویرایش وجود ندارد. درخواست‌های تایید شده، رد شده، منقضی شده یا تکمیل شده قابل ویرایش نیستند.');
        }
        
        $check_expired = $this->service->checkExpiredRequest($withdrawalRequest);
        if ($check_expired['success'] == false) {
            return redirect()->back()->withErrors($check_expired['error']);
        }
        
        $data = $request->only('commodity_id', 'unit_id', 'amount', 'comment', 'price', 'customer_id');
        $this->service->validationSecondLayer($data);
        
        $check_inventory = $this->service->checkWithdrawalData($data);
        if ($check_inventory['success'] == true) {
        $file = null;
        if ($request->hasFile('file')) {
            $file = $request->file('file');
        }
        
        DB::transaction(function () use ($withdrawalRequest, $data, $file) {
            $this->service->update($withdrawalRequest, $data, $file);
        });
        } else {
            return redirect()->back()->withErrors($check_inventory['error']);
        }
        
        return redirect(route('withdrawal-request.index'))->with('successful', 'اطلاعات درخواست ویرایش شد.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param WithdrawalRequest $withdrawalRequest
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(WithdrawalRequest $withdrawalRequest)
    {
        // Prevent deletion of approved, rejected, expired, or done requests
        if (!in_array($withdrawalRequest->status, ['awaiting_approval'])) {
            return redirect()->back()->withErrors('در این مرحله امکان حذف وجود ندارد. درخواست‌های تایید شده، رد شده، منقضی شده یا تکمیل شده قابل حذف نیستند.');
        }
        
        $check_expired = $this->service->checkExpiredRequest($withdrawalRequest);
        if ($check_expired['success'] == false) {
            return redirect()->back()->withErrors($check_expired['error']);
        }
        
        DB::transaction(function () use ($withdrawalRequest) {
            $this->service->delete($withdrawalRequest);
        });
        
        return redirect(route('withdrawal-request.index'))->with('successful', 'درخواست با موفقیت حذف شد.');
    }

    /**
     * Approve a withdrawal request
     *
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function approvalRequest($id)
    {
        if (!auth()->user()->role->havePermission('status_withdrawal')) {
            return redirect()->back()->withErrors('شما این دسترسی را ندارید .');
        }
        
        $withdrawalRequest = WithdrawalRequest::query()->findOrFail($id);
        if ($withdrawalRequest->status != 'awaiting_approval') {
            return redirect()->back()->withErrors('در این مرحله امکان تایید وجود ندارد .');
        }
        
        $check_expired = $this->service->checkExpiredRequest($withdrawalRequest);
        if ($check_expired['success'] == false) {
            return redirect()->back()->withErrors($check_expired['error']);
        }
        
        $check_inventory = $this->service->checkWithdrawal($withdrawalRequest);
        if ($check_inventory['success'] == true) {
            DB::transaction(function () use ($withdrawalRequest) {
                $this->service->approvalWithdrawal($withdrawalRequest);
            });
        } else {
            return redirect()->back()->withErrors($check_inventory['error']);
        }
        
        return redirect(route('withdrawal-request.show', $withdrawalRequest))->with('successful', 'درخواست با موفقیت تایید شد.');
    }

    /**
     * Reject a withdrawal request
     *
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function rejectRequest($id)
    {
        if (!auth()->user()->role->havePermission('status_withdrawal')) {
            return redirect()->back()->withErrors('شما این دسترسی را ندارید .');
        }
        
        $withdrawalRequest = WithdrawalRequest::query()->findOrFail($id);
        if ($withdrawalRequest->status != 'awaiting_approval') {
            return redirect()->back()->withErrors('در این مرحله امکان رد وجود ندارد .');
        }
        
        $check_expired = $this->service->checkExpiredRequest($withdrawalRequest);
        if ($check_expired['success'] == false) {
            return redirect()->back()->withErrors($check_expired['error']);
        }
        
        $this->service->rejectWithdrawal($withdrawalRequest);
        return redirect(route('withdrawal-request.show', $withdrawalRequest))->with('successful', 'درخواست با موفقیت رد شد.');
    }

}
