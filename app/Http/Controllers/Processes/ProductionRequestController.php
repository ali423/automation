<?php

namespace App\Http\Controllers\Processes;

use App\Http\Controllers\Controller;
use App\Http\Requests\Processes\CreateProductionRequest;
use App\Models\Attribute;
use App\Models\Commodity;
use App\Models\ProductionRequest;
use App\Services\Processes\ProductionRequestService;
use App\Traits\PaginationTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class ProductionRequestController extends Controller
{
    use PaginationTrait;
    
    protected $service;

    public function __construct(ProductionRequestService $service)
    {
        $this->service = $service;
        $this->authorizeResource(ProductionRequest::class);
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
        $query = ProductionRequest::with([
            'activities.user', // Load activities with user for creator_user attribute
            'product.unit', // Load product with its unit
            'unit', // Load production request unit
            'materials.unit' // Load materials with their units
        ]);
        
        // Use advanced pagination with search and filter capabilities
        $requests = $this->getPaginatedResults($query, $request, 10, [
            'searchable_fields' => ['number', 'product.title', 'product.number'],
            'filterable_fields' => ['status', 'product_id'],
            'sortable_fields' => ['id', 'number', 'status', 'production_amount', 'total_cost', 'created_at', 'updated_at'],
            'default_sort_field' => 'created_at',
            'default_sort_direction' => 'desc',
            'max_per_page' => 100
        ]);
        
        // Prepare options for the pagination components
        $paginationOptions = [
            'searchable_fields' => ['number', 'product.title', 'product.number'],
            'filterable_fields' => ['status', 'product_id'],
            'per_page_options' => [5, 10, 25, 50, 100],
            'search_placeholder' => 'جستجو در شماره درخواست، نام محصول یا شماره محصول...',
            'status_options' => [
                'awaiting_approval' => __('fields.production-request.status.awaiting_approval'),
                'approved' => __('fields.production-request.status.approved'), // Maps to both 'approved' and 'approvaled'
                'rejected' => __('fields.production-request.status.rejected'),
                'expired' => __('fields.production-request.status.expired'),
                'done' => __('fields.production-request.status.done'),
            ]
        ];
        
        // Get products for filter dropdown
        $products = Commodity::where('type', 'product')->orderBy('title')->get();
        
        return view('dashboard.processes.production-request.index', [
            'requests' => $requests,
            'products' => $products,
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
        // Get products with formulas (no caching for real-time data)
        $products = Commodity::query()
            ->where('type', 'product')
            ->whereHas('materials') // Only products with formulas
            ->with(['unit', 'materials.unit', 'attributes'])
            ->orderBy('title')
            ->get();
        
        if ($products->count() < 1) {
            return redirect(route('commodity.create'))->withErrors('ابتدا حداقل یک محصول ثبت کنید.');
        }
        
        return view('dashboard.processes.production-request.create', [
            'products' => $products,
            'attributes' => Attribute::orderBy('name')->get(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param CreateProductionRequest $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Http\Response|\Illuminate\Routing\Redirector
     */
    public function store(CreateProductionRequest $request)
    {
        $data = $request->only('product_id', 'amount', 'comment');
        $this->service->validationSecondLayer($data);
        $check_production = $this->service->checkProductionData($data);
        
        if ($check_production['success'] == true) {
            $file = null;
            if ($request->hasFile('file')) {
                $file = $request->file('file');
            }
            
            $production = DB::transaction(function () use ($data, $file) {
                return $this->service->create($data, $file);
            });
        } else {
            return redirect()->back()->withErrors($check_production['error']);
        }
        
        return redirect(route('production-request.show', $production))->with('successful', 'اطلاعات ثبت شد.');
    }

    /**
     * Display the specified resource.
     *
     * @param ProductionRequest $productionRequest
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\Response
     */
    public function show(ProductionRequest $productionRequest)
    {
        // Load the production request with all necessary relationships
        $productionRequest->load([
            'product.unit', // Load product with its unit
            'unit', // Load production request unit
            'materials.unit', // Load materials with their units
            'comments.user', // Load comments with users
            'files.user', // Load files with users
            'activities.user' // Load activities with users for creator info
        ]);
        
        // Pre-calculate inventory data for materials to avoid N+1 queries in view
        $materialsWithInventory = $this->service->getMaterialsWithInventoryData($productionRequest);
        
        return view('dashboard.processes.production-request.show', [
            'request' => $productionRequest,
            'materialsWithInventory' => $materialsWithInventory,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param ProductionRequest $productionRequest
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function edit(ProductionRequest $productionRequest)
    {
        // Prevent editing of approved, rejected, expired, or done requests
        if (!in_array($productionRequest->status, ['awaiting_approval'])) {
            return redirect()->back()->withErrors('در این مرحله امکان ویرایش وجود ندارد. درخواست‌های تایید شده، رد شده، منقضی شده یا تکمیل شده قابل ویرایش نیستند.');
        }
        
        // Get products with formulas (no caching for real-time data)
        $products = Commodity::query()
            ->where('type', 'product')
            ->whereHas('materials') // Only products with formulas
            ->with(['unit', 'materials.unit'])
            ->orderBy('title')
            ->get();
        
        if ($products->count() < 1) {
            return redirect(route('commodity.create'))->withErrors('ابتدا حداقل یک محصول ثبت کنید.');
        }
        
        return view('dashboard.processes.production-request.edit', [
            'request' => $productionRequest,
            'products' => $products,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param CreateProductionRequest $request
     * @param ProductionRequest $productionRequest
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(CreateProductionRequest $request, ProductionRequest $productionRequest)
    {
        // Prevent editing of approved, rejected, expired, or done requests
        if (!in_array($productionRequest->status, ['awaiting_approval'])) {
            return redirect()->back()->withErrors('در این مرحله امکان ویرایش وجود ندارد. درخواست‌های تایید شده، رد شده، منقضی شده یا تکمیل شده قابل ویرایش نیستند.');
        }

        $data = $request->only('product_id', 'amount', 'comment');
        
        $file = null;
        if ($request->hasFile('file')) {
            $file = $request->file('file');
        }
        
        DB::transaction(function () use ($productionRequest, $data, $file) {
            $this->service->update($productionRequest, $data, $file);
        });
        
        // No cache clearing needed since we removed caching
        
        return redirect(route('production-request.index'))->with('successful', 'اطلاعات درخواست ویرایش شد.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param ProductionRequest $productionRequest
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(ProductionRequest $productionRequest)
    {
        // Prevent deletion of approved, rejected, expired, or done requests
        if (!in_array($productionRequest->status, ['awaiting_approval'])) {
            return redirect()->back()->withErrors('در این مرحله امکان حذف وجود ندارد. درخواست‌های تایید شده، رد شده، منقضی شده یا تکمیل شده قابل حذف نیستند.');
        }
        
        DB::transaction(function () use ($productionRequest) {
            $this->service->delete($productionRequest);
        });
        
        // Clear related caches after deletion
        $this->service->clearProductionCaches($productionRequest->id);
        
        return redirect(route('production-request.index'))->with('successful', 'درخواست حذف شد.');
    }

    /**
     * Approve production request
     *
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function approvalRequest($id)
    {
        if (!auth()->user()->role->havePermission('status_production')) {
            return redirect()->back()->withErrors('شما این دسترسی را ندارید.');
        }
        
        $productionRequest = ProductionRequest::query()->findOrFail($id);
        
        if ($productionRequest->status !== 'awaiting_approval') {
            return redirect()->back()->withErrors('در این مرحله امکان تایید وجود ندارد.');
        }
        
        $check_production = $this->service->checkProduction($productionRequest);
        if ($check_production['success'] == true) {
            DB::transaction(function () use ($productionRequest) {
                $this->service->approve($productionRequest);
            });
            
            // No cache clearing needed since we removed caching
            
            return redirect(route('production-request.show', $productionRequest))->with('successful', 'درخواست تولید تایید شد.');
        } else {
            return redirect()->back()->withErrors($check_production['error']);
        }
    }

    /**
     * Reject production request
     *
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function rejectRequest($id)
    {
        if (!auth()->user()->role->havePermission('status_production')) {
            return redirect()->back()->withErrors('شما این دسترسی را ندارید.');
        }
        
        $productionRequest = ProductionRequest::query()->findOrFail($id);
        
        if ($productionRequest->status !== 'awaiting_approval') {
            return redirect()->back()->withErrors('در این مرحله امکان رد وجود ندارد.');
        }
        
        $this->service->reject($productionRequest);
        
        // Clear related caches after rejection
        $this->service->clearProductionCaches($productionRequest->id);
        
        return redirect(route('production-request.show', $productionRequest))->with('successful', 'درخواست تولید رد شد.');
    }
} 