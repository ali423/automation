<?php

namespace App\Http\Controllers\Processes;

use App\Http\Controllers\Controller;
use App\Http\Requests\importingReportRequest;
use App\Http\Requests\Processes\CreateImportingRequest;
use App\Models\Commodity;
use App\Models\ImportingRequest;
use App\Models\Seller;
use Illuminate\Http\Request;
use App\Services\CommodityUnitService;
use App\Services\Processes\ImportingRequestService;
use App\Traits\PaginationTrait;
use Illuminate\Support\Facades\DB;

class ImportingRequestController extends Controller
{
    use PaginationTrait;
    
    protected $service;
    protected $commodityUnitService;

    public function __construct(ImportingRequestService $service, CommodityUnitService $commodityUnitService)
    {
        $this->service = $service;
        $this->commodityUnitService = $commodityUnitService;
        $this->authorizeResource(ImportingRequest::class);
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
        $query = ImportingRequest::with(['commodities.unit', 'seller']);
        
        // Use advanced pagination with search and filter capabilities
        $requests = $this->getPaginatedResults($query, $request, 10, [
            'searchable_fields' => ['number', 'commodities.title'],
            'filterable_fields' => ['status', 'seller_id'],
            'sortable_fields' => ['id', 'created_at', 'updated_at', 'status'],
            'default_sort_field' => 'id',
            'default_sort_direction' => 'desc',
            'max_per_page' => 50
        ]);
        
        // Prepare options for the pagination components
        $paginationOptions = [
            'searchable_fields' => ['number', 'commodities.title'],
            'filterable_fields' => ['status', 'seller_id'],
            'per_page_options' => [5, 10, 25, 50, 100],
            'search_placeholder' => 'جستجو در شماره درخواست یا نام کالا...',
            'status_options' => [
                'awaiting_approval' => __('fields.importing_request.status.awaiting_approval'),
                'approved' => __('fields.importing_request.status.approvaled'), // Maps to both 'approved' and 'approvaled'
                'rejected' => __('fields.importing_request.status.rejected'),
                'expired' => __('fields.importing_request.status.expired'),
                'done' => __('fields.importing_request.status.done'),
            ]
        ];
        
        return view('dashboard.processes.importing-request.index', [
            'requests' => $requests,
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
        $commodities = Commodity::query()->where('type', 'material')->with(['unit', 'unitConversions.fromUnit', 'unitConversions.toUnit'])->get();
        $sellers = Seller::all();
        
        if (count($commodities) < 1) {
            return redirect(route('commodity.create'))->withErrors('ابتدا حداقل یک کالای ماده اولیه ثبت کنید .');
        }
        if (count($sellers) < 1) {
            return redirect(route('seller.create'))->withErrors('ابتدا حداقل یک فروشنده ثبت کنید .');
        }
        
        // Preload all selectable units for each commodity
        $commoditiesWithUnits = $commodities->map(function ($commodity) {
            $selectableUnits = $this->commodityUnitService->getSelectableUnits($commodity);
            $commodity->selectable_units = $selectableUnits;
            return $commodity;
        });
        
        return view('dashboard.processes.importing-request.create', [
            'commodities' => $commoditiesWithUnits,
            'sellers' => $sellers,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param CreateImportingRequest $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Http\Response|\Illuminate\Routing\Redirector
     */
    public function store(CreateImportingRequest $request)
    {
        $data = $request->only('commodity_id', 'unit', 'amount', 'comment', 'purchase_price','seller_id');
        $this->service->validationSecondLayer($data);
        $check_warehouses = $this->service->checkImportingStore($data);
        
        if ($check_warehouses['success'] == true) {
            $file = null;
            if ($request->hasFile('file')) {
                $file = $request->file('file');
            }
            
            $importing = DB::transaction(function () use ($data, $file) {
                return $this->service->create($data, $file);
            });
        } else {
            return redirect()->back()->withErrors($check_warehouses['error']);
        }
        
        return redirect(route('importing-request.show', $importing))->with('successful', 'اطلاعات ثبت شد.');
    }

    /**
     * Display the specified resource.
     *
     * @param ImportingRequest $importingRequest
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\Response
     */
    public function show(ImportingRequest $importingRequest)
    {
        // Load the importing request with commodities and their units
        $importingRequest->load(['commodities' => function ($query) {
            $query->with('unit');
        }]);
        
        return view('dashboard.processes.importing-request.show', [
            'request' => $importingRequest,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param ImportingRequest $importingRequest
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function edit(ImportingRequest $importingRequest)
    {
        // Prevent editing of approved, rejected, expired, or done requests
        if (!in_array($importingRequest->status, ['awaiting_approval'])) {
            return redirect()->back()->withErrors('در این مرحله امکان ویرایش وجود ندارد. درخواست‌های تایید شده، رد شده، منقضی شده یا تکمیل شده قابل ویرایش نیستند.');
        }
        
        // Load the importing request with commodities and their selectable units
        $importingRequest->load(['commodities' => function ($query) {
            $query->with(['unit', 'unitConversions.fromUnit', 'unitConversions.toUnit']);
        }]);
        
        // Add selectable units to each commodity
        foreach ($importingRequest->commodities as $commodity) {
            $selectableUnits = $this->commodityUnitService->getSelectableUnits($commodity);
            $commodity->selectable_units = $selectableUnits;
        }
        
        return view('dashboard.processes.importing-request.edit', [
            'request' => $importingRequest,
            'commodities' => Commodity::query()->where('type', 'material')->with(['unit', 'unitConversions.fromUnit', 'unitConversions.toUnit'])->get(),
            'sellers' => Seller::all(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param CreateImportingRequest $request
     * @param ImportingRequest $importingRequest
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(CreateImportingRequest $request, ImportingRequest $importingRequest)
    {
        // Prevent editing of approved, rejected, expired, or done requests
        if (!in_array($importingRequest->status, ['awaiting_approval'])) {
            return redirect()->back()->withErrors('در این مرحله امکان ویرایش وجود ندارد. درخواست‌های تایید شده، رد شده، منقضی شده یا تکمیل شده قابل ویرایش نیستند.');
        }
        
        $check_expired = $this->service->checkExpiredRequest($importingRequest);
        if ($check_expired['success'] == false) {
            return redirect()->back()->withErrors($check_expired['error']);
        }
        $data = $request->only('commodity_id', 'unit', 'amount', 'comment', 'purchase_price','seller_id');
        $this->service->validationSecondLayer($data);
        $file = null;
        if ($request->hasFile('file')) {
            $file = $request->file('file');
        }
        
        DB::transaction(function () use ($importingRequest, $data, $file) {
            $this->service->update($importingRequest, $data, $file);
        });
        
        return redirect(route('importing-request.index'))->with('successful', 'اطلاعات درخواست ویرایش شد.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param ImportingRequest $importingRequest
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(ImportingRequest $importingRequest)
    {
        // Prevent deletion of approved, rejected, expired, or done requests
        if (!in_array($importingRequest->status, ['awaiting_approval'])) {
            return redirect()->back()->withErrors('در این مرحله امکان حذف وجود ندارد. درخواست‌های تایید شده، رد شده، منقضی شده یا تکمیل شده قابل حذف نیستند.');
        }
        
        $check_expired = $this->service->checkExpiredRequest($importingRequest);
        if ($check_expired['success'] == false) {
            return redirect()->back()->withErrors($check_expired['error']);
        }
        
        DB::transaction(function () use ($importingRequest) {
            $this->service->delete($importingRequest);
        });
        
        return redirect(route('importing-request.index'))->with('successful', 'درخواست با موفقیت حذف شد.');
    }

    /**
     * Approve an importing request
     *
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function approvalRequest($id)
    {
        if (!auth()->user()->role->havePermission('status_importing')) {
            return redirect()->back()->withErrors('شما این دسترسی را ندارید .');
        }
        $importingRequest = ImportingRequest::query()->findOrFail($id);
        if ($importingRequest->status != 'awaiting_approval') {
            return redirect()->back()->withErrors('در این مرحله امکان تایید وجود ندارد .');
        }
        $check_expired = $this->service->checkExpiredRequest($importingRequest);
        if ($check_expired['success'] == false) {
            return redirect()->back()->withErrors($check_expired['error']);
        }
        $check_warehouses = $this->service->checkImporting($importingRequest);
        if ($check_warehouses['success'] == true) {
            
            DB::transaction(function () use ($importingRequest) {
                $this->service->approvalImporting($importingRequest);
            });
        } else {
            return redirect()->back()->withErrors($check_warehouses['error']);
        }
        return redirect(route('importing-request.show', $importingRequest))->with('successful', 'درخواست با موفقیت تایید شد.');
    }

    /**
     * Reject an importing request
     *
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function rejectRequest($id)
    {
        if (!auth()->user()->role->havePermission('status_importing')) {
            return redirect()->back()->withErrors('شما این دسترسی را ندارید .');
        }
        $importingRequest = ImportingRequest::query()->findOrFail($id);
        if ($importingRequest->status != 'awaiting_approval') {
            return redirect()->back()->withErrors('در این مرحله امکان رد وجود ندارد .');
        }
        $check_expired = $this->service->checkExpiredRequest($importingRequest);
        if ($check_expired['success'] == false) {
            return redirect()->back()->withErrors($check_expired['error']);
        }
        $this->service->rejectImporting($importingRequest);
        return redirect(route('importing-request.show', $importingRequest))->with('successful', 'درخواست با موفقیت رد شد.');
    }

    /**
     * Show the form for creating a report
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
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

    /**
     * Store and display the report
     *
     * @param importingReportRequest $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function storeReport(importingReportRequest $request)
    {
        $data = $request->validated();
        $requests = $this->service->getReportData($data);
        if (isset($requests['error'])) {
            return redirect(route('importing.report.create'))->withErrors($requests['error']);
        }
        
        // Get available units and conversions for the commodity
        $commodity = \App\Models\Commodity::find($data['commodity_id']);
        $availableUnits = $commodity->unitConversions()
            ->with(['fromUnit', 'toUnit'])
            ->get()
            ->groupBy('to_unit_id')
            ->map(function ($conversions) {
                return $conversions->first()->toUnit;
            });
        
        // Add the main unit if not already included
        if ($commodity->unit && !$availableUnits->has($commodity->unit->id)) {
            $availableUnits->put($commodity->unit->id, $commodity->unit);
        }
        
        return view('dashboard.processes.importing-request.report-show', [
            'requests' => $requests,
            'availableUnits' => $availableUnits,
            'commodity' => $commodity,
        ]);
    }

    /**
     * Convert price to different unit via AJAX
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function convertPrice(Request $request)
    {
        $request->validate([
            'commodity_id' => 'required|exists:commodities,id',
            'from_unit_id' => 'required|exists:units,id',
            'to_unit_id' => 'required|exists:units,id',
            'price' => 'required|numeric|min:0',
        ]);

        $unitConversionService = app(\App\Services\UnitConversionService::class);
        $convertedPrice = $unitConversionService->convert(
            $request->price,
            $request->from_unit_id,
            $request->to_unit_id,
            $request->commodity_id
        );

        if ($convertedPrice === null) {
            return response()->json([
                'success' => false,
                'message' => 'تبدیل واحد برای این کالا تعریف نشده است.'
            ], 400);
        }

        return response()->json([
            'success' => true,
            'converted_price' => round($convertedPrice, 2),
            'formatted_price' => number_format(round($convertedPrice, 2))
        ]);
    }
}
