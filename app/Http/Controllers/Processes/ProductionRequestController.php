<?php

namespace App\Http\Controllers\Processes;

use App\Http\Controllers\Controller;
use App\Http\Requests\Processes\CreateProductionRequest;
use App\Models\Commodity;
use App\Models\ProductionRequest;
use App\Services\CommodityUnitService;
use App\Services\Processes\ProductionRequestService;
use Illuminate\Support\Facades\DB;

class ProductionRequestController extends Controller
{
    protected $service;
    protected $commodityUnitService;

    public function __construct(ProductionRequestService $service, CommodityUnitService $commodityUnitService)
    {
        $this->service = $service;
        $this->commodityUnitService = $commodityUnitService;
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
        $requests = ProductionRequest::with(['commodities.unit', 'outputProducts.unit'])
            ->orderBy('id', 'DESC')
            ->get();
        
        // Add empty state handling
        if ($requests->isEmpty()) {
            return view('dashboard.processes.production-request.index', compact('requests'))
                ->with('message', 'هیچ درخواست تولیدی یافت نشد. برای شروع، یک درخواست تولید جدید ایجاد کنید.');
        }
        
        return view('dashboard.processes.production-request.index', compact('requests'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function create()
    {
        // Load only products (not materials) since we'll get materials from product formulas
        $products = Commodity::query()->where('type', 'product')->with(['unit', 'materials.unit'])->get();
        
        // Check if there are any products available
        if ($products->isEmpty()) {
            return redirect()->route('commodity.create')
                ->withErrors('ابتدا حداقل یک محصول ثبت کنید. محصولات برای ایجاد درخواست تولید نیاز هستند.');
        }
        
        // Preload product formulas for JavaScript
        $productFormulas = [];
        foreach ($products as $product) {
            $productFormulas[$product->id] = [];
            foreach ($product->materials as $material) {
                $productFormulas[$product->id][] = [
                    'material_id' => $material->id,
                    'material_title' => $material->title,
                    'amount' => $material->pivot->amount,
                    'unit_id' => $material->pivot->unit_id,
                    'unit_name' => $material->unit->name,
                    'unit_symbol' => $material->unit->symbol,
                ];
            }
        }
        
        return view('dashboard.processes.production-request.create', [
            'products' => $products,
            'productFormulas' => $productFormulas,
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
        
        // Check production data validation
        $check_production = $this->service->checkProductionData($data);
        if ($check_production['success'] == false) {
            return redirect()->back()->withErrors($check_production['error']);
        }
        
        $file = null;
        if ($request->hasFile('file')) {
            $file = $request->file('file');
        }
        
        try {
            DB::transaction(function () use ($data, $file) {
                $production = $this->service->create($data, $file);
            });
            return redirect(route('production-request.index'))->with('successful', 'اطلاعات ثبت شد.');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors('خطا در ثبت درخواست تولید: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     *
     * @param ProductionRequest $productionRequest
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\Response
     */
    public function show(ProductionRequest $productionRequest)
    {
        // Load the production request with commodities and their units
        $productionRequest->load(['commodities' => function ($query) {
            $query->with('unit');
        }, 'activities.user', 'comments.user', 'files.user']);
        
        // Get production summary
        $productionSummary = $this->service->getProductionSummary($productionRequest);
        
        return view('dashboard.processes.production-request.show', [
            'request' => $productionRequest,
            'summary' => $productionSummary,
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
        // Check if request is editable
        if (!$productionRequest->is_editable) {
            return redirect()->back()->withErrors('در این مرحله امکان ویرایش وجود ندارد. درخواست‌های تایید شده، رد شده، منقضی شده یا تکمیل شده قابل ویرایش نیستند.');
        }
        
        $check_expired = $this->service->checkExpiredRequest($productionRequest);
        if ($check_expired['success'] == false) {
            return redirect()->back()->withErrors($check_expired['error']);
        }

        // Load the production request with commodities and their units
        $productionRequest->load(['commodities' => function ($query) {
            $query->with('unit');
        }]);
        
        // Load only products (not materials) since we'll get materials from product formulas
        $products = Commodity::query()->where('type', 'product')->with(['unit', 'materials.unit'])->get();
        
        // Check if there are any products available
        if ($products->isEmpty()) {
            return redirect()->route('commodity.create')
                ->withErrors('ابتدا حداقل یک محصول ثبت کنید. محصولات برای ویرایش درخواست تولید نیاز هستند.');
        }
        
        // Get current product and amount from the production request
        $currentProduct = $productionRequest->outputProducts->first();
        $currentAmount = $currentProduct ? $currentProduct->pivot->amount : null;
        $currentProductId = $currentProduct ? $currentProduct->id : null;
        
        return view('dashboard.processes.production-request.edit', [
            'request' => $productionRequest,
            'products' => $products,
            'currentProduct' => $currentProduct,
            'currentAmount' => $currentAmount,
            'currentProductId' => $currentProductId,
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
        // Check if request is editable
        if (!$productionRequest->is_editable) {
            return redirect()->back()->withErrors('در این مرحله امکان ویرایش وجود ندارد. درخواست‌های تایید شده، رد شده، منقضی شده یا تکمیل شده قابل ویرایش نیستند.');
        }
        
        $check_expired = $this->service->checkExpiredRequest($productionRequest);
        if ($check_expired['success'] == false) {
            return redirect()->back()->withErrors($check_expired['error']);
        }

        $data = $request->only('product_id', 'amount', 'description', 'comment');
        
        $file = null;
        if ($request->hasFile('file')) {
            $file = $request->file('file');
        }
        
        try {
            DB::transaction(function () use ($productionRequest, $data, $file) {
                $this->service->update($productionRequest, $data, $file);
            });
            return redirect(route('production-request.index'))->with('successful', 'اطلاعات درخواست ویرایش شد.');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors('خطا در ویرایش درخواست: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param ProductionRequest $productionRequest
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(ProductionRequest $productionRequest)
    {
        // Check if request is deletable
        if (!$productionRequest->is_deletable) {
            return redirect()->back()->withErrors('در این مرحله امکان حذف وجود ندارد. درخواست‌های تایید شده، رد شده، منقضی شده یا تکمیل شده قابل حذف نیستند.');
        }
        
        $check_expired = $this->service->checkExpiredRequest($productionRequest);
        if ($check_expired['success'] == false) {
            return redirect()->back()->withErrors($check_expired['error']);
        }
        
        try {
            DB::transaction(function () use ($productionRequest) {
                $this->service->delete($productionRequest);
            });
            return redirect(route('production-request.index'))->with('successful', 'درخواست حذف شد.');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors('خطا در حذف درخواست: ' . $e->getMessage());
        }
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
        
        if (!$productionRequest->can_be_approved) {
            return redirect()->back()->withErrors('در این مرحله امکان تایید وجود ندارد.');
        }
        
        $check_expired = $this->service->checkExpiredRequest($productionRequest);
        if ($check_expired['success'] == false) {
            return redirect()->back()->withErrors($check_expired['error']);
        }
        
        $check_production = $this->service->checkProduction($productionRequest);
        if ($check_production['success'] == true) {
            try {
                DB::transaction(function () use ($productionRequest) {
                    $this->service->approvalProduction($productionRequest);
                });
                return redirect(route('production-request.show', $productionRequest))->with('successful', 'درخواست تولید تایید شد.');
            } catch (\Exception $e) {
                return redirect()->back()->withErrors('خطا در تایید درخواست: ' . $e->getMessage());
            }
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
        
        if (!$productionRequest->can_be_rejected) {
            return redirect()->back()->withErrors('در این مرحله امکان رد وجود ندارد.');
        }
        
        $check_expired = $this->service->checkExpiredRequest($productionRequest);
        if ($check_expired['success'] == false) {
            return redirect()->back()->withErrors($check_expired['error']);
        }
        
        try {
            DB::transaction(function () use ($productionRequest) {
                $this->service->rejectProduction($productionRequest);
            });
            return redirect(route('production-request.show', $productionRequest))->with('successful', 'درخواست تولید رد شد.');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors('خطا در رد درخواست: ' . $e->getMessage());
        }
    }
} 