<?php

namespace App\Http\Controllers\Processes;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateWithdrawalRequest;
use App\Models\Commodity;
use App\Models\Customer;
use App\Models\WithdrawalRequest;
use App\Services\CommodityUnitService;
use App\Services\Processes\WithdrawalRequestService;
use App\Traits\PaginationTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WithdrawalRequestController extends Controller
{
    use PaginationTrait;
    
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
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // Build query with eager loading to fix N+1 query problem
        $query = WithdrawalRequest::with(['commodities.unit', 'customer']);
        
        // Use advanced pagination with search and filter capabilities
        $requests = $this->getPaginatedResults($query, $request, 10, [
            'searchable_fields' => ['number', 'customer.name', 'customer.comp_name'],
            'filterable_fields' => ['status', 'customer_id'],
            'sortable_fields' => ['id', 'number', 'status', 'created_at', 'updated_at'],
            'default_sort_field' => 'created_at',
            'default_sort_direction' => 'desc',
            'max_per_page' => 100
        ]);
        
        // Prepare options for the pagination components
        $paginationOptions = [
            'searchable_fields' => ['number', 'customer.name', 'customer.comp_name'],
            'filterable_fields' => ['status', 'customer_id'],
            'per_page_options' => [5, 10, 25, 50, 100],
            'search_placeholder' => 'جستجو در شماره درخواست، نام مشتری یا نام شرکت...',
            'status_options' => [
                'awaiting_approval' => __('fields.withdrawal-request.status.awaiting_approval'),
                'approved' => __('fields.withdrawal-request.status.approvaled'), // Maps to both 'approved' and 'approvaled'
                'rejected' => __('fields.withdrawal-request.status.rejected'),
                'expired' => __('fields.withdrawal-request.status.expired'),
                'done' => __('fields.withdrawal-request.status.done'),
            ]
        ];
        
        // Get customers for filter dropdown
        $customers = Customer::all();
        
        return view('dashboard.processes.withdrawal-request.index', [
            'requests' => $requests,
            'customers' => $customers,
            'options' => $paginationOptions,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function create()
    {
        $commodities = Commodity::query()->where('type', 'product')->with(['unit', 'unitConversions.fromUnit', 'unitConversions.toUnit'])->orderBy('title')->get();
        $customers = Customer::query()->get();
        
        if (count($commodities) < 1) {
            return redirect(route('commodity.create'))->withErrors('ابتدا حداقل یک محصول ثبت کنید .');
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
        
        // Eager load all pivot units in a single query to avoid N+1 problem
        $pivotUnitIds = $withdrawalRequest->commodities->pluck('pivot.unit_id')->filter()->unique();
        $pivotUnits = \App\Models\Unit::whereIn('id', $pivotUnitIds)->get()->keyBy('id');
        
        // Attach pivot units to commodities
        foreach ($withdrawalRequest->commodities as $commodity) {
            if ($commodity->pivot->unit_id && isset($pivotUnits[$commodity->pivot->unit_id])) {
                $commodity->pivot->unit = $pivotUnits[$commodity->pivot->unit_id];
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
        
        // Eager load all pivot units in a single query to avoid N+1 problem
        $pivotUnitIds = $withdrawalRequest->commodities->pluck('pivot.unit_id')->filter()->unique();
        $pivotUnits = \App\Models\Unit::whereIn('id', $pivotUnitIds)->get()->keyBy('id');
        
        // Attach pivot units to commodities and add selectable units
        foreach ($withdrawalRequest->commodities as $commodity) {
            if ($commodity->pivot->unit_id && isset($pivotUnits[$commodity->pivot->unit_id])) {
                $commodity->pivot->unit = $pivotUnits[$commodity->pivot->unit_id];
            }
            
            $selectableUnits = $this->commodityUnitService->getSelectableUnits($commodity);
            $commodity->selectable_units = $selectableUnits;
        }
        
        return view('dashboard.processes.withdrawal-request.edit', [
            'request' => $withdrawalRequest,
            'commodities' => Commodity::query()->where('type', 'product')->with(['unit', 'unitConversions.fromUnit', 'unitConversions.toUnit'])->orderBy('title')->get(),
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

        // Enforce driver info capture: redirect to approval form if missing
        if (empty($withdrawalRequest->driver_name) || empty($withdrawalRequest->driver_phone)) {
            return redirect()->route('approval.withdrawal.form', $withdrawalRequest->id)
                ->withErrors('لطفاً اطلاعات راننده را تکمیل و سپس تایید کنید.');
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
     * Show approval form to capture driver info before approval
     */
    public function approvalForm($id)
    {
        if (!auth()->user()->role->havePermission('status_withdrawal')) {
            return redirect()->back()->withErrors('شما این دسترسی را ندارید .');
        }
        $withdrawalRequest = WithdrawalRequest::query()->findOrFail($id);
        if ($withdrawalRequest->status != 'awaiting_approval') {
            return redirect()->back()->withErrors('در این مرحله امکان تایید وجود ندارد .');
        }
        return view('dashboard.processes.withdrawal-request.approve', [
            'request' => $withdrawalRequest,
        ]);
    }

    /**
     * Submit approval with driver info
     */
    public function approvalSubmit(\Illuminate\Http\Request $request, $id)
    {
        if (!auth()->user()->role->havePermission('status_withdrawal')) {
            return redirect()->back()->withErrors('شما این دسترسی را ندارید .');
        }
        $withdrawalRequest = WithdrawalRequest::query()->findOrFail($id);
        if ($withdrawalRequest->status != 'awaiting_approval') {
            return redirect()->back()->withErrors('در این مرحله امکان تایید وجود ندارد .');
        }

        // Validate driver info
        $validated = $request->validate([
            'driver_name' => ['required','string','max:255'],
            'driver_phone' => ['required','string','max:50'],
            'driver_national_id' => ['nullable','string','max:20'],
            'vehicle_type' => ['nullable','string','max:100'],
            'plate_serial' => ['nullable','string','max:50'],
            'plate_number' => ['nullable','string','max:50'],
            'bill_of_lading_number' => ['nullable','string','max:255'],
            'shipping_city' => ['nullable','string','max:255'],
            'shipping_province' => ['nullable','string','max:255'],
        ], [
            'driver_name.required' => 'نام راننده الزامی است.',
            'driver_phone.required' => 'تلفن راننده الزامی است.',
        ]);

        // Save driver info
        $withdrawalRequest->update($validated);

        // Proceed with the same checks and approval
        $check_expired = $this->service->checkExpiredRequest($withdrawalRequest);
        if ($check_expired['success'] == false) {
            return redirect()->back()->withErrors($check_expired['error']);
        }

        $check_inventory = $this->service->checkWithdrawal($withdrawalRequest);
        if ($check_inventory['success'] == true) {
            \DB::transaction(function () use ($withdrawalRequest) {
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
