<?php

namespace App\Http\Controllers\Processes;

use App\Http\Controllers\Controller;
use App\Http\Requests\Processes\CreateProductionRequest;
use App\Models\Commodity;
use App\Models\ProductionRequest;
use App\Services\Processes\ProductionRequestService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class ProductionRequestController extends Controller
{
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
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\Response
     */
    public function index()
    {
        $requests = ProductionRequest::query()
            ->with([
                'activities.user', // Load activities with user for creator_user attribute
                'product.unit', // Load product with its unit
                'unit', // Load production request unit
                'materials.unit' // Load materials with their units
            ])
            ->orderBy('id', 'DESC')
            ->paginate(20); // Add pagination for better performance
        
        return view('dashboard.processes.production-request.index', [
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
        // Use cached products with pagination for better performance
        $products = Cache::remember('products_with_formulas', 600, function () { // Cache for 10 minutes
            return Commodity::query()
                ->where('type', 'product')
                ->whereHas('materials') // Only products with formulas
                ->with(['unit', 'materials.unit'])
                ->orderBy('title')
                ->get();
        });
        
        if ($products->count() < 1) {
            return redirect(route('commodity.create'))->withErrors('ابتدا حداقل یک محصول ثبت کنید.');
        }
        
        return view('dashboard.processes.production-request.create', [
            'products' => $products,
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
        
        // Use cached products with pagination for better performance
        $products = Cache::remember('products_with_formulas', 600, function () { // Cache for 10 minutes
            return Commodity::query()
                ->where('type', 'product')
                ->whereHas('materials') // Only products with formulas
                ->with(['unit', 'materials.unit'])
                ->orderBy('title')
                ->get();
        });
        
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
        
        // Clear related caches after update
        $this->service->clearProductionCaches($productionRequest->id);
        
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
            
            // Clear related caches after approval
            $this->service->clearProductionCaches($productionRequest->id);
            
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