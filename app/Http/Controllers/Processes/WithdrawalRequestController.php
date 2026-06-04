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
                'cancelled' => __('fields.withdrawal-request.status.cancelled'),
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
        // Load the withdrawal request with commodities, units, and adjustments
        $withdrawalRequest->load([
            'commodities' => function ($query) {
                $query->with('unit');
            },
            'adjustments' => function ($query) {
                $query->with('commodity')->orderBy('created_at', 'desc');
            }
        ]);
        
        // Eager load all pivot units in a single query to avoid N+1 problem
        $pivotUnitIds = $withdrawalRequest->commodities->pluck('pivot.unit_id')->filter()->unique();
        $pivotUnits = \App\Models\Unit::whereIn('id', $pivotUnitIds)->get()->keyBy('id');
        
        // Calculate returned and corrected amounts for each commodity (all in main unit)
        $returnedAmounts = [];
        $correctionAmounts = [];
        if (in_array($withdrawalRequest->status, ['approvaled', 'done', 'cancelled'])) {
            foreach ($withdrawalRequest->commodities as $commodity) {
                $originalAmountInMainUnit = $this->commodityUnitService->convertToMainUnit(
                    $commodity,
                    $commodity->pivot->amount,
                    $commodity->pivot->unit_id
                ) ?? 0;

                // Get sales returns (when customer returns the item)
                $returned = \App\Models\InventoryAdjustment::forWithdrawal($withdrawalRequest->id)
                    ->byType('sales_return')
                    ->where('commodity_id', $commodity->id)
                    ->sum('amount');
                $returnedAmounts[$commodity->id] = $returned;
                
                // Get corrections (when we fix shipping discrepancies)
                $corrections = \App\Models\InventoryAdjustment::forWithdrawal($withdrawalRequest->id)
                    ->where('commodity_id', $commodity->id)
                    ->where('adjustment_type', 'manual_adjustment')
                    ->where('reason', 'like', '%[تصحیح ارسال%')
                    ->sum('amount');
                $correctionAmounts[$commodity->id] = $corrections;
                $commodity->original_amount_main_unit = $originalAmountInMainUnit;
            }
        }
        
        // Attach pivot units and return/correction data to commodities
        foreach ($withdrawalRequest->commodities as $commodity) {
            if ($commodity->pivot->unit_id && isset($pivotUnits[$commodity->pivot->unit_id])) {
                $commodity->pivot->unit = $pivotUnits[$commodity->pivot->unit_id];
            }
            // Attach returned amount (from actual sales returns)
            $commodity->returned_amount = $returnedAmounts[$commodity->id] ?? 0;
            // Attach correction amount (from shipping corrections)
            $commodity->correction_amount = $correctionAmounts[$commodity->id] ?? 0;
            // Calculate effective shipped amount in main unit:
            // original - sales returns - all shipping corrections
            $originalAmountInMainUnit = $commodity->original_amount_main_unit ?? ($this->commodityUnitService->convertToMainUnit(
                $commodity,
                $commodity->pivot->amount,
                $commodity->pivot->unit_id
            ) ?? 0);
            $commodity->net_amount = $originalAmountInMainUnit - $commodity->returned_amount - $commodity->correction_amount;
        }

        // Find commodities that were ADDED via corrections (not in original withdrawal)
        $originalCommodityIds = $withdrawalRequest->commodities->pluck('id')->toArray();
        $addedCommodities = [];
        
        if (in_array($withdrawalRequest->status, ['approvaled', 'done', 'cancelled'])) {
            // Get all commodities from adjustments that aren't in original list
            $adjustmentCommodityIds = $withdrawalRequest->adjustments
                ->filter(function ($adjustment) {
                    return $adjustment->adjustment_type === 'manual_adjustment'
                        && str_contains($adjustment->reason ?? '', '[تصحیح ارسال:');
                })
                ->pluck('commodity_id')
                ->unique()
                ->diff($originalCommodityIds);
            
            // Build added commodities with their adjustment info
            foreach ($adjustmentCommodityIds as $commodityId) {
                $commodity = \App\Models\Commodity::find($commodityId);
                if (!$commodity) continue;
                
                // Get total adjustments for this commodity
                $adjustmentAmount = \App\Models\InventoryAdjustment::forWithdrawal($withdrawalRequest->id)
                    ->where('commodity_id', $commodityId)
                    ->where('reason', 'like', '%[تصحیح ارسال%')
                    ->sum('amount');
                
                if ($adjustmentAmount != 0) {
                    $commodity->adjustment_amount = $adjustmentAmount;
                    $commodity->adjustment_type = 'added_via_correction';
                    // Get the unit from the adjustment
                    $lastAdjustment = \App\Models\InventoryAdjustment::forWithdrawal($withdrawalRequest->id)
                        ->where('commodity_id', $commodityId)
                        ->where('reason', 'like', '%[تصحیح ارسال%')
                        ->latest()
                        ->first();
                    $commodity->unit_id_used = $lastAdjustment->unit_id;
                    $addedCommodities[] = $commodity;
                }
            }
        }
        
        // Build final effective request items for display/invoices (main unit basis)
        $effectiveCommodities = collect();
        foreach ($withdrawalRequest->commodities as $commodity) {
            $effectiveAmount = max(0, (float) ($commodity->net_amount ?? 0));
            $commodity->effective_amount = $effectiveAmount;
            $commodity->effective_unit = $commodity->unit;
            $commodity->effective_price = $commodity->pivot->price;
            $effectiveCommodities->push($commodity);
        }

        foreach ($addedCommodities as $addedCommodity) {
            $correctionSum = \App\Models\InventoryAdjustment::forWithdrawal($withdrawalRequest->id)
                ->where('commodity_id', $addedCommodity->id)
                ->where('adjustment_type', 'manual_adjustment')
                ->where('reason', 'like', '%[تصحیح ارسال%')
                ->sum('amount');
            $returnedForAdded = \App\Models\InventoryAdjustment::forWithdrawal($withdrawalRequest->id)
                ->byType('sales_return')
                ->where('commodity_id', $addedCommodity->id)
                ->sum('amount');

            $addedCommodity->effective_amount = max(0, (-1 * $correctionSum) - $returnedForAdded);
            $addedCommodity->effective_unit = $addedCommodity->unit;

            $pivotLine = $withdrawalRequest->commodities->firstWhere('id', $addedCommodity->id);
            if ($pivotLine && $pivotLine->pivot->price !== null) {
                $addedCommodity->effective_price = $pivotLine->pivot->price;
                $addedCommodity->discount_percentage = $pivotLine->pivot->discount_percentage;
            } else {
                $addedCommodity->effective_price = $addedCommodity->sales_price ?? null;
                $addedCommodity->discount_percentage = null;
            }

            $effectiveCommodities->push($addedCommodity);
        }

        return view('dashboard.processes.withdrawal-request.show', [
            'request' => $withdrawalRequest,
            'addedCommodities' => $addedCommodities,
            'effectiveCommodities' => $effectiveCommodities->filter(function ($commodity) {
                return ($commodity->effective_amount ?? 0) > 0;
            })->values(),
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

    /**
     * Show cancel form for a withdrawal request
     *
     * @param int $id
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse
     */
    public function cancelForm($id)
    {
        if (!auth()->user()->role->havePermission('cancel_withdrawal')) {
            return redirect()->back()->withErrors('شما این دسترسی را ندارید.');
        }

        $withdrawalRequest = WithdrawalRequest::query()->findOrFail($id);
        if (!in_array($withdrawalRequest->status, ['approvaled', 'done', 'completed'])) {
            return redirect()->back()->withErrors('در این مرحله امکان لغو وجود ندارد.');
        }

        return view('dashboard.processes.withdrawal-request.cancel', [
            'request' => $withdrawalRequest,
        ]);
    }

    /**
     * Cancel a withdrawal request and restore inventory
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function cancelSubmit(\Illuminate\Http\Request $request, $id)
    {
        if (!auth()->user()->role->havePermission('cancel_withdrawal')) {
            return redirect()->back()->withErrors('شما این دسترسی را ندارید.');
        }

        $withdrawalRequest = WithdrawalRequest::query()->findOrFail($id);

        $validated = $request->validate([
            'reason' => ['nullable', 'string', 'max:1000'],
        ]);

        try {
            DB::transaction(function () use ($withdrawalRequest, $validated) {
                $this->service->cancelWithdrawal($withdrawalRequest, $validated['reason'] ?? null);
            });
        } catch (\Exception $e) {
            return redirect()->back()->withErrors($e->getMessage());
        }

        return redirect(route('withdrawal-request.show', $withdrawalRequest))->with('successful', 'درخواست با موفقیت لغو و موجودی بازگردانده شد.');
    }

    /**
     * Show sales return form for a withdrawal request
     *
     * @param int $id
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse
     */
    public function salesReturnForm($id)
    {
        if (!auth()->user()->role->havePermission('cancel_withdrawal')) {
            return redirect()->back()->withErrors('شما این دسترسی را ندارید.');
        }

        $withdrawalRequest = WithdrawalRequest::query()->findOrFail($id);
        if (!in_array($withdrawalRequest->status, ['approvaled', 'done', 'completed'])) {
            return redirect()->back()->withErrors('امکان برگشت از فروش تنها برای درخواست‌های تایید شده وجود دارد.');
        }

        // Load commodities with their units and adjustments for the form
        $withdrawalRequest->load([
            'commodities' => function ($query) {
                $query->with(['unit', 'unitConversions.fromUnit', 'unitConversions.toUnit']);
            },
            'adjustments' => function ($query) {
                $query->with('commodity')->orderBy('created_at', 'desc');
            }
        ]);

        // Eager load pivot units
        $pivotUnitIds = $withdrawalRequest->commodities->pluck('pivot.unit_id')->filter()->unique();
        $pivotUnits = \App\Models\Unit::whereIn('id', $pivotUnitIds)->get()->keyBy('id');

        // Attach pivot units and selectable units to commodities
        foreach ($withdrawalRequest->commodities as $commodity) {
            if ($commodity->pivot->unit_id && isset($pivotUnits[$commodity->pivot->unit_id])) {
                $commodity->pivot->unit = $pivotUnits[$commodity->pivot->unit_id];
            }
            $commodity->selectable_units = $this->commodityUnitService->getSelectableUnits($commodity);
            
            // Calculate already returned amount for this commodity
            $commodity->already_returned = \App\Models\InventoryAdjustment::forWithdrawal($withdrawalRequest->id)
                ->byType('sales_return')
                ->where('commodity_id', $commodity->id)
                ->sum('amount');
        }

        // Prepare commodities data for discrepancies section
        $allCommodities = \App\Models\Commodity::all();
        $commodityUnitsData = [];
        
        foreach ($allCommodities as $commodity) {
            $commodityUnitsData[$commodity->id] = [];
            
            // Add main unit
            if ($commodity->unit) {
                $commodityUnitsData[$commodity->id][$commodity->unit_id] = [
                    'id' => $commodity->unit_id,
                    'name' => $commodity->unit->name,
                    'symbol' => $commodity->unit->symbol
                ];
            }
            
            // Add conversion units
            foreach ($commodity->unitConversions as $conversion) {
                if ($conversion->toUnit) {
                    $commodityUnitsData[$commodity->id][$conversion->toUnit->id] = [
                        'id' => $conversion->toUnit->id,
                        'name' => $conversion->toUnit->name,
                        'symbol' => $conversion->toUnit->symbol
                    ];
                }
            }
        }

        $allUnits = \App\Models\Unit::all();

        // Identify commodities added via corrections (manual adjustments)
        $originalCommodityIds = $withdrawalRequest->commodities->pluck('id')->toArray();
        $addedCommodityIds = $withdrawalRequest->adjustments
            ->filter(function ($adjustment) {
                return $adjustment->adjustment_type === 'manual_adjustment'
                    && str_contains($adjustment->reason ?? '', '[تصحیح ارسال:');
            })
            ->pluck('commodity_id')
            ->unique()
            ->diff($originalCommodityIds)
            ->values()
            ->toArray();

        // Combine both lists for the request commodity IDs (original + added)
        $requestCommodityIds = array_merge($originalCommodityIds, $addedCommodityIds);

        return view('dashboard.processes.withdrawal-request.sales-return', [
            'request' => $withdrawalRequest,
            'allCommodities' => $allCommodities,
            'allUnits' => $allUnits,
            'commodityUnitsData' => $commodityUnitsData,
            'requestCommodityIds' => $requestCommodityIds,
            'originalCommodityIds' => $originalCommodityIds,
            'addedCommodityIds' => $addedCommodityIds,
        ]);
    }

    /**
     * Process sales return submission
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function salesReturnSubmit(\Illuminate\Http\Request $request, $id)
    {
        if (!auth()->user()->role->havePermission('cancel_withdrawal')) {
            return redirect()->back()->withErrors('شما این دسترسی را ندارید.');
        }

        $withdrawalRequest = WithdrawalRequest::query()->findOrFail($id);

        // Validate unified returns form
        $validated = $request->validate([
            'return_type' => ['nullable', 'array'],
            'return_type.*' => ['in:normal,add_back,deduct,qty_adjustment'],
            'return_direction' => ['nullable', 'array'],
            'return_direction.*' => ['nullable', 'in:increase,decrease'],
            'return_commodity_id' => ['nullable', 'array'],
            'return_commodity_id.*' => ['exists:commodities,id'],
            'return_amount' => ['nullable', 'array'],
            'return_amount.*' => ['numeric', 'min:0'],
            'return_unit_id' => ['nullable', 'array'],
            'return_unit_id.*' => ['exists:units,id'],
            'return_reason' => ['nullable', 'array'],
            'return_reason.*' => ['nullable', 'string', 'max:500'],
            'return_price' => ['nullable', 'array'],
            'return_price.*' => ['nullable', 'numeric', 'min:0'],
            'return_discount_percentage' => ['nullable', 'array'],
            'return_discount_percentage.*' => ['nullable', 'integer', 'min:0', 'max:100'],
            'reason' => ['nullable', 'string', 'max:1000'],
        ]);

        // Separate returns from corrections
        $returns = [];      // Items for sales_return
        $corrections = [];  // Items for shipping_correction
        $originalCommodityIds = $withdrawalRequest->commodities->pluck('id')->all();
        $requestCommodityIds = $originalCommodityIds;

        if (!empty($validated['return_type'])) {
            foreach ($validated['return_type'] as $index => $type) {
                if (empty($validated['return_amount'][$index]) || $validated['return_amount'][$index] <= 0) {
                    continue; // Skip empty rows
                }

                if (
                    empty($validated['return_commodity_id'][$index]) ||
                    empty($validated['return_unit_id'][$index]) ||
                    empty($type)
                ) {
                    return redirect()->back()->withErrors('برای هر ردیف دارای مقدار، نوع، کالا و واحد الزامی است.')->withInput();
                }

                $item = [
                    'commodity_id' => $validated['return_commodity_id'][$index],
                    'amount' => $validated['return_amount'][$index],
                    'unit_id' => $validated['return_unit_id'][$index],
                    'reason' => $validated['return_reason'][$index] ?? null,
                    'direction' => $validated['return_direction'][$index] ?? 'decrease',
                    'price' => $validated['return_price'][$index] ?? null,
                    'discount_percentage' => $validated['return_discount_percentage'][$index] ?? null,
                ];

                // For these types, commodity must be from current withdrawal request.
                if (in_array($type, ['normal', 'add_back', 'qty_adjustment']) && !in_array((int) $item['commodity_id'], $requestCommodityIds)) {
                    return redirect()->back()->withErrors('کالای انتخابی برای این نوع باید از کالاهای همین درخواست باشد.')->withInput();
                }

                if ($type === 'deduct') {
                    $commodity = \App\Models\Commodity::find($item['commodity_id']);
                    if (!$commodity || $commodity->type !== 'product') {
                        return redirect()->back()->withErrors('برای «ارسال شده اما ثبت نشده» فقط فرآورده قابل انتخاب است.')->withInput();
                    }
                }

                if ($type === 'normal') {
                    $commodityId = $validated['return_commodity_id'][$index];
                    if (isset($returns[$commodityId])) {
                        // Aggregate duplicate normal rows for the same commodity
                        if ((int) $returns[$commodityId]['unit_id'] !== (int) $item['unit_id']) {
                            return redirect()->back()->withErrors('برای یک کالا در برگشت عادی، واحد باید یکسان باشد.')->withInput();
                        }
                        $returns[$commodityId]['amount'] += $item['amount'];
                        if (!empty($item['reason'])) {
                            $existingReason = $returns[$commodityId]['reason'] ?? '';
                            $returns[$commodityId]['reason'] = trim($existingReason . ' | ' . $item['reason'], ' |');
                        }
                    } else {
                        $returns[$commodityId] = $item;
                    }
                } else {
                    // add_back, deduct, qty_adjustment are corrections
                    $corrections[] = [
                        'type' => $type,
                        'commodity_id' => $item['commodity_id'],
                        'amount' => $item['amount'],
                        'unit_id' => $item['unit_id'],
                        'reason' => $item['reason'],
                        'direction' => $item['direction'],
                    ];
                }
            }
        }

        // Validate: at least one return or correction must exist
        if (empty($returns) && empty($corrections)) {
            return redirect()->back()->withErrors('لطفاً حداقل یک برگشت یا تصحیح ارسالی را وارد کنید.');
        }

        try {
            DB::transaction(function () use ($withdrawalRequest, $corrections, $returns, $originalCommodityIds) {
                // Step 1: Apply all corrections first
                if (!empty($corrections)) {
                    $this->service->applyShippingCorrections($withdrawalRequest, $corrections, $originalCommodityIds);
                }
                
                // Step 2: Then process returns (only if returns has items)
                if (!empty($returns)) {
                    $this->service->processSalesReturn($withdrawalRequest, $returns);
                }
            });
        } catch (\Exception $e) {
            return redirect()->back()->withErrors($e->getMessage());
        }

        return redirect(route('withdrawal-request.show', $withdrawalRequest))->with('successful', 'برگشت و تصحیحات با موفقیت ثبت شدند.');
    }

}
