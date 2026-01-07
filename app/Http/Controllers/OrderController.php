<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateWithdrawalRequest;
use App\Http\Requests\OrderRequest;
use App\Models\Commodity;
use App\Models\Customer;
use App\Models\Order;
use App\Models\WithdrawalRequest;
use App\Services\OrderService;
use App\Services\Processes\WithdrawalRequestService;
use App\Services\CommodityUnitService;
use App\Traits\PaginationTrait;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\Inventory;
use Morilog\Jalali\Jalalian;
use Illuminate\Pagination\LengthAwarePaginator;

class OrderController extends Controller
{
    use PaginationTrait;
    
    protected $service;
    protected $withdrawal_service;
    protected $commodityUnitService;

    public function __construct(OrderService $service, WithdrawalRequestService $withdrawal_service, CommodityUnitService $commodityUnitService)
    {
        $this->service = $service;
        $this->withdrawal_service = $withdrawal_service;
        $this->commodityUnitService = $commodityUnitService;
        $this->authorizeResource(Order::class);
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
        $query = Order::with(['customer', 'orderItems.commodity', 'orderItems.unit'])
            ->whereHas('customer')
            ->whereHas('orderItems.commodity');
        
        // Use advanced pagination with search and filter capabilities
        $orders = $this->getPaginatedResults($query, $request, 10, [
            'searchable_fields' => ['customer.name', 'customer.comp_name', 'deadline'],
            'filterable_fields' => ['status', 'customer_id'],
            'sortable_fields' => ['id', 'status', 'deadline', 'created_at', 'updated_at'],
            'default_sort_field' => 'status',
            'default_sort_direction' => 'asc',
            'max_per_page' => 100
        ]);
        
        // Add calculated properties to each order
        $orders->getCollection()->transform(function ($order) {
            $order->items_count = $order->orderItems->count();
            return $order;
        });
        
        // Prepare options for the pagination components
        $paginationOptions = [
            'searchable_fields' => ['customer.name', 'customer.comp_name', 'deadline'],
            'filterable_fields' => ['status', 'customer_id'],
            'per_page_options' => [5, 10, 25, 50, 100],
            'search_placeholder' => 'جستجو در نام مشتری، نام شرکت یا تاریخ مهلت...',
            'status_options' => [
                'pending' => __('fields.order.status.pending'),
                'done' => __('fields.order.status.done'),
            ],
            // show date range controls only on chart view
            'show_date_range' => true,
        ];
        
        // Get customers for filter dropdown
        $customers = Customer::orderBy('name')->get();
        
        return view('dashboard.order.index', [
            'orders' => $orders,
            'customers' => $customers,
            'options' => $paginationOptions,
        ]);
    }

    /**
     * Display the order chart page.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\Response
     */
    public function chart(Request $request)
    {
        $ordersQuery = Order::query()->with(['customer', 'orderItems.commodity', 'orderItems.unit'])
            ->whereHas('customer')
            ->whereHas('orderItems.commodity')
            ->orderByRaw("FIELD(status, 'pending', 'done')")
            ->orderBy('deadline', 'ASC');

        // Apply server-side date filtering (persisted via query string)
        $dateFrom = $request->query('date_from');
        $dateTo = $request->query('date_to');
        $normalizedDateFrom = $dateFrom ? $this->normalizePersianDate($dateFrom) : null;
        $normalizedDateTo = $dateTo ? $this->normalizePersianDate($dateTo) : null;

        // Because deadline is stored as string, compare via STR_TO_DATE with multiple possible formats
        $deadlineExpr = "COALESCE(STR_TO_DATE(deadline, '%Y-%m-%d'), STR_TO_DATE(deadline, '%Y/%m/%d'), STR_TO_DATE(deadline, '%Y-%m-%d %H:%i:%s'), STR_TO_DATE(deadline, '%Y/%m/%d %H:%i:%s'))";
        if ($normalizedDateFrom) {
            $ordersQuery->whereRaw("$deadlineExpr >= ?", [$normalizedDateFrom]);
        }
        if ($normalizedDateTo) {
            $ordersQuery->whereRaw("$deadlineExpr <= ?", [$normalizedDateTo]);
        }

        // Apply server-side search (similar to index view)
        $search = $request->query('search');
        if (!empty($search)) {
            $ordersQuery->where(function($q) use ($search) {
                $q->whereHas('customer', function($cq) use ($search) {
                    $cq->where('name', 'like', "%{$search}%")
                       ->orWhere('comp_name', 'like', "%{$search}%");
                })
                ->orWhere('deadline', 'like', "%{$search}%");
            });
        }

        // Apply server-side simple filters (status, customer_id, etc.)
        $filtersParam = $request->query('filters');
        $filters = [];
        if (is_string($filtersParam)) {
            $decoded = json_decode($filtersParam, true);
            if (is_array($decoded)) { $filters = $decoded; }
        } elseif (is_array($filtersParam)) {
            $filters = $filtersParam;
        }

        if (!empty($filters)) {
            if (!empty($filters['status'])) {
                $ordersQuery->where('status', $filters['status']);
            }
            if (!empty($filters['customer_id'])) {
                $ordersQuery->where('customer_id', (int)$filters['customer_id']);
            }
        }

        $perPage = (int) $request->query('per_page', 10);
        $orders = $ordersQuery->paginate($perPage)->appends($request->query());

        // Calculate total amounts for each order on the current page
        foreach ($orders as $order) {
            $order->total_amount = $order->orderItems->sum('commodity_amount');
        }

        // Prepare options for the pagination components (aligned with index view)
        $paginationOptions = [
            'searchable_fields' => ['customer.name', 'customer.comp_name', 'deadline'],
            'filterable_fields' => ['status', 'customer_id'],
            'per_page_options' => [5, 10, 25, 50, 100],
            'search_placeholder' => 'جستجو در نام مشتری، نام شرکت یا تاریخ مهلت...',
            'status_options' => [
                'pending' => __('fields.order.status.pending'),
                'done' => __('fields.order.status.done'),
            ]
        ];

        return view('dashboard.order.chart', [
            'orders' => $orders,
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
            return redirect(route('commodity.create'))->withErrors('ابتدا حداقل یک کالای فراورده ثبت کنید .');
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
        
        return view('dashboard.order.create',
            [
                'commodities' => $commoditiesWithUnits,
                'customers' => $customers,
            ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param OrderRequest $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Http\Response|\Illuminate\Routing\Redirector
     */
    public function store(OrderRequest $request)
    {
        $data = [
            'customer_id' => $request->input('customer_id'),
            'commodity_id' => $request->input('commodity_id'),
            'unit_id' => $request->input('unit_id'),
            'deadline' => $request->input('deadline'),
            'price' => $request->input('price'),
            'packaging_count' => $request->input('packaging_count'),
            'commodity_amount' => $request->input('commodity_amount'),
        ];

        $this->service->validationSecondLayer($data);
        
        $order = DB::transaction(function () use ($data) {
            return $this->service->create($data);
        });

        return redirect(route('order.show', $order))->with('successful', 'اطلاعات ثبت شد.');
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Models\Order $order
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\Response
     */
    public function show(Order $order)
    {
        // Load the order with all necessary relationships
        $order->load(['customer', 'orderItems.commodity', 'orderItems.unit', 'comments.user', 'files.user']);
        
        // Add calculated properties
        $order->items_count = $order->orderItems->count();
        
        return view('dashboard.order.show',
            [
                'order' => $order,
            ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Models\Order $order
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse
     */
    public function edit(Order $order)
    {
        // Prevent editing of done orders
        if ($order->status === 'done') {
            return redirect()->back()->withErrors('در این مرحله امکان ویرایش وجود ندارد. سفارش‌های تحویل شده قابل ویرایش نیستند.');
        }
        
        $commodities = Commodity::query()->where('type', 'product')->with(['unit', 'unitConversions.fromUnit', 'unitConversions.toUnit'])->orderBy('title')->get();
        $customers = Customer::query()->get();
        
        if (count($commodities) < 1) {
            return redirect(route('commodity.create'))->withErrors('ابتدا حداقل یک کالای فراورده ثبت کنید .');
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
        
        return view('dashboard.order.edit',
            [
                'order' => $order,
                'commodities' => $commoditiesWithUnits,
                'customers' => $customers,
            ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param OrderRequest $request
     * @param \App\Models\Order $order
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Http\Response|\Illuminate\Routing\Redirector
     */
    public function update(OrderRequest $request, Order $order)
    {
        // Prevent editing of done orders
        if ($order->status === 'done') {
            return redirect()->back()->withErrors('در این مرحله امکان ویرایش وجود ندارد. سفارش‌های تحویل شده قابل ویرایش نیستند.');
        }
        
        $data = [
            'customer_id' => $request->input('customer_id'),
            'commodity_id' => $request->input('commodity_id'),
            'unit_id' => $request->input('unit_id'),
            'deadline' => $request->input('deadline'),
            'price' => $request->input('price'),
            'packaging_count' => $request->input('packaging_count'),
            'commodity_amount' => $request->input('commodity_amount'),
        ];
        
        $this->service->validationSecondLayer($data);
        
        DB::transaction(function () use ($order, $data) {
            $this->service->update($order, $data);
        });
        
        return redirect(route('order.show', $order))->with('successful', 'اطلاعات ویرایش شد.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\Order $order
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Order $order)
    {
        // Prevent deletion of done orders
        if ($order->status === 'done') {
            return redirect()->back()->withErrors('در این مرحله امکان حذف وجود ندارد. سفارش‌های تحویل شده قابل حذف نیستند.');
        }
        
        DB::transaction(function () use ($order) {
            $this->service->delete($order);
        });
        
        return redirect(route('order.index'))->with('successful', 'سفارش حذف شد.');
    }

    /**
     * Show the order confirmation form
     *
     * @param Order $order
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\Response
     */
    public function confirm(Order $order){
        // Load the order with all necessary relationships
        $order->load(['customer', 'orderItems.commodity', 'orderItems.unit']);
        
        // Add calculated properties
        $order->items_count = $order->orderItems->count();
        
        return view('dashboard.order.confirm',
            [
                'order' => $order,
            ]);
    }

    /**
     * Store the order confirmation and create withdrawal request
     *
     * @param CreateWithdrawalRequest $request
     * @param Order $order
     * @return \Illuminate\Http\RedirectResponse
     */
    public function confirmStore(CreateWithdrawalRequest $request, Order $order){
        $data = $request->only('commodity_id', 'unit_id', 'amount', 'comment','price','customer_id');
        
        // Debug: Log the extracted data
        \Log::info('OrderController::confirmStore - Extracted data:', $data);
        
        $inventory_check = $this->withdrawal_service->checkWithdrawalData($data);
        if ($inventory_check['success'] == true) {
            $file = null;
            if ($request->hasFile('file')) {
                $file = $request->file('file');
            }
            $withdrawal = DB::transaction(function () use ($order, $data, $file) {
                $this->service->updateStatus($order);
                $withdrawal = $this->withdrawal_service->create($data, $file);
                // Link order to withdrawal for future reference and display
                $order->update(['withdrawal_request_id' => $withdrawal->id]);
                return $withdrawal;
            });
        } else {
            return redirect()->back()->withErrors($inventory_check['error']);
        }
        return redirect(route('withdrawal-request.show',$withdrawal))->with('successful', 'اطلاعات ثبت شد.');
    }

    /**
     * Get chart data for orders with raw materials inventory information.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getChartData(Request $request)
    {
        $orderIds = $request->input('order_ids', []);
        $selectAll = $request->input('select_all', false);
        $dateFrom = $request->input('date_from');
        $dateTo = $request->input('date_to');
        
        // Convert Persian digits to English digits for date comparison
        $normalizedDateFrom = null;
        $normalizedDateTo = null;
        
        if ($dateFrom) {
            $normalizedDateFrom = $this->normalizePersianDate($dateFrom);
        }
        
        if ($dateTo) {
            $normalizedDateTo = $this->normalizePersianDate($dateTo);
        }
        
        // Parse additional filters/search passed from the frontend (so select_all can respect them)
        $search = $request->input('search', null);
        $filtersParam = $request->input('filters', []);
        $filters = [];
        if (is_string($filtersParam)) {
            $decoded = json_decode($filtersParam, true);
            if (is_array($decoded)) { $filters = $decoded; }
        } elseif (is_array($filtersParam)) {
            $filters = $filtersParam;
        }

        // Start with base query
        $ordersQuery = Order::with(['orderItems.commodity.materials.unit', 'orderItems.unit']);

        // If no explicit status filter provided, default to pending orders only
        if (empty($filters['status'])) {
            $ordersQuery->where('status', 'pending');
        }

        // Apply date filtering if provided
        if ($normalizedDateFrom) {
            $ordersQuery->whereDate('deadline', '>=', $normalizedDateFrom);
        }

        if ($normalizedDateTo) {
            $ordersQuery->whereDate('deadline', '<=', $normalizedDateTo);
        }

        // Apply search if provided (matches customer name/company or deadline)
        if (!empty($search)) {
            $ordersQuery->where(function($q) use ($search) {
                $q->whereHas('customer', function($cq) use ($search) {
                    $cq->where('name', 'like', "%{$search}%")
                       ->orWhere('comp_name', 'like', "%{$search}%");
                })
                ->orWhere('deadline', 'like', "%{$search}%");
            });
        }

        // Apply simple filters if provided
        if (!empty($filters)) {
            if (!empty($filters['status'])) {
                $ordersQuery->where('status', $filters['status']);
            }
            if (!empty($filters['customer_id'])) {
                $ordersQuery->where('customer_id', (int)$filters['customer_id']);
            }
        }

        // Apply order ID filtering if provided and select_all is not requested
        if (!$selectAll && !empty($orderIds)) {
            $ordersQuery->whereIn('id', $orderIds);
        }

        $orders = $ordersQuery->get();
        

        
        if ($orders->isEmpty()) {
            return response()->json([
                'names' => [],
                'amounts' => [],
                'units' => [],
                'inventory' => []
            ]);
        }



        $rawMaterialMap = [];
        $inventoryData = [];

        // Calculate raw materials required for all selected orders
        foreach ($orders as $order) {
            foreach ($order->orderItems as $item) {
                $product = $item->commodity;
                
                if (!$product || $product->type !== 'product') {
                    continue; // Skip if not a product or commodity doesn't exist
                }

                // Get the product formula (raw materials)
                $productFormulaService = app(\App\Services\ProductFormulaService::class);
                $productTitle = $product->title; // Store title before try block
                
                try {
                    $formulaSummary = $productFormulaService->getFormulaSummary($product);
                    
                    foreach ($formulaSummary['materials'] as $materialData) {
                        $material = $materialData['material'];
                        $materialName = $material->title;
                        
                        // Calculate required amount for this order item
                        $requiredAmount = $materialData['amount_in_product_unit'] * $item->commodity_amount;
                        
                        // Ensure the amount is positive and reasonable
                        if ($requiredAmount <= 0) {
                            continue;
                        }
                        
                        if (!isset($rawMaterialMap[$materialName])) {
                            $rawMaterialMap[$materialName] = [
                                'amount' => 0,
                                'unit' => $material->unit ? $material->unit->symbol : 'نامشخص',
                                'material_id' => $material->id
                            ];
                        }
                        
                        $rawMaterialMap[$materialName]['amount'] += $requiredAmount;
                    }
                } catch (\Exception $e) {
                    continue;
                }
            }
        }

        // Filter out materials with zero amounts
        $filteredRawMaterialMap = array_filter($rawMaterialMap, function($data) {
            return $data['amount'] > 0;
        });

        // Get inventory data for each raw material (only for materials with amounts > 0)
        foreach ($filteredRawMaterialMap as $materialName => $data) {
            if ($data['material_id']) {
                $inventory = Inventory::where('commodity_id', $data['material_id'])->first();
                $inventoryData[$materialName] = $inventory ? $inventory->amount : 0;
            } else {
                $inventoryData[$materialName] = 0;
            }
        }


        
        return response()->json([
            'names' => array_keys($filteredRawMaterialMap),
            'amounts' => array_column($filteredRawMaterialMap, 'amount'),
            'units' => array_column($filteredRawMaterialMap, 'unit'),
            'inventory' => array_values($inventoryData)
        ]);
    }

    /**
     * Normalize Persian date string by converting Persian digits to English digits
     *
     * @param string $persianDate Date in format YYYY/MM/DD (Persian digits)
     * @return string|null Normalized date in YYYY/MM/DD format with English digits or null if invalid
     */
    private function normalizePersianDate($persianDate)
    {
        try {
            // Convert Persian digits to English digits
            $persianDigits = ['۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹'];
            $englishDigits = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];
            $dateWithEnglishDigits = str_replace($persianDigits, $englishDigits, $persianDate);
            
            // Convert Jalali (YYYY/MM/DD) to Gregorian (Y-m-d) for DB comparisons
            try {
                $jalali = Jalalian::fromFormat('Y/m/d', $dateWithEnglishDigits);
                return $jalali->toCarbon()->format('Y-m-d');
            } catch (\Throwable $t) {
                // Fall back to parsing as Gregorian in common formats
                $tryFormats = ['Y/m/d', 'Y-m-d', 'Y/m/d H:i:s', 'Y-m-d H:i:s'];
                foreach ($tryFormats as $fmt) {
                    $dt = \DateTime::createFromFormat($fmt, $dateWithEnglishDigits);
                    if ($dt !== false) {
                        return $dt->format('Y-m-d');
                    }
                }
            }
            
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Get commodity units for AJAX request.
     *
     * @param int $commodityId
     * @return \Illuminate\Http\JsonResponse
     */
    public function getCommodityUnits($commodityId)
    {
        $commodity = Commodity::with(['unit', 'unitConversions.fromUnit', 'unitConversions.toUnit'])->findOrFail($commodityId);
        
        $selectableUnits = $this->commodityUnitService->getSelectableUnits($commodity);
        
        return response()->json([
            'success' => true,
            'units' => $selectableUnits,
            'commodity' => [
                'id' => $commodity->id,
                'title' => $commodity->title,
                'unit' => $commodity->unit ? $commodity->unit->name : null,
                'unit_id' => $commodity->unit_id,
                'weight_per_unit' => $commodity->weight_per_unit
            ]
        ]);
    }

    /**
     * Calculate weight for a commodity with given amount and unit
     *
     * @param int $commodityId
     * @param float $amount
     * @param int $unitId
     * @return \Illuminate\Http\JsonResponse
     */
    public function calculateWeight($commodityId, $amount, $unitId)
    {
        try {
            $commodity = Commodity::findOrFail($commodityId);
            $weight = calculate_weight($commodity, $amount, $unitId);
            
            // Provide more specific feedback for why weight is unknown
            $weightFormatted = 'نامشخص';
            if ($weight === null) {
                if (!$commodity->weight_per_unit) {
                    $weightFormatted = 'وزن تعریف نشده';
                } else {
                    $weightFormatted = 'خطا در محاسبه';
                }
            } else {
                // Display weight in kilograms without decimal places (problem #70)
                $weightFormatted = number_format($weight, 0) . ' کیلوگرم';
            }
            
            return response()->json([
                'success' => true,
                'weight' => $weight,
                'weight_formatted' => $weightFormatted
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'خطا در محاسبه وزن: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Convert amount from one unit to another for a specific commodity
     *
     * @param int $commodityId
     * @param float $amount
     * @param int $fromUnitId
     * @param int $toUnitId
     * @return \Illuminate\Http\JsonResponse
     */
    public function convertAmount($commodityId, $amount, $fromUnitId, $toUnitId)
    {
        try {
            $commodity = Commodity::findOrFail($commodityId);
            
            // Validate that both units are valid for this commodity
            $selectableUnits = $this->commodityUnitService->getSelectableUnits($commodity);
            $fromUnitValid = $selectableUnits->contains('id', (int)$fromUnitId);
            $toUnitValid = $selectableUnits->contains('id', (int)$toUnitId);
            
            if (!$fromUnitValid || !$toUnitValid) {
                return response()->json([
                    'success' => false,
                    'message' => 'واحد انتخاب شده برای این کالا معتبر نیست.'
                ]);
            }
            
            // Use UnitConversionService to convert
            $unitConversionService = app(\App\Services\UnitConversionService::class);
            $convertedAmount = $unitConversionService->convert(
                (float)$amount,
                (int)$fromUnitId,
                (int)$toUnitId,
                $commodity->id
            );
            
            if ($convertedAmount === null) {
                return response()->json([
                    'success' => false,
                    'message' => 'تبدیل واحد امکان‌پذیر نیست. لطفاً نرخ تبدیل را تعریف کنید.'
                ]);
            }
            
            return response()->json([
                'success' => true,
                'converted_amount' => $convertedAmount,
                'converted_amount_rounded' => round($convertedAmount, 2)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'خطا در تبدیل واحد: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Display factory status page.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function factoryStatus(Request $request)
    {
        // Build base query with eager loads
        $ordersQuery = Order::where('status', 'pending')
            ->with(['customer', 'orderItems.commodity', 'orderItems.unit']);

        // Apply server-side search (customer name/company, deadline)
        $search = $request->query('search');
        if (!empty($search)) {
            $ordersQuery->where(function($q) use ($search) {
                $q->whereHas('customer', function($cq) use ($search) {
                    $cq->where('name', 'like', "%{$search}%")
                       ->orWhere('comp_name', 'like', "%{$search}%");
                })
                ->orWhere('deadline', 'like', "%{$search}%");
            });
        }

        // Apply simple filters (e.g., customer_id) from shared controls
        $filtersParam = $request->query('filters');
        $filters = [];
        if (is_string($filtersParam)) {
            $decoded = json_decode($filtersParam, true);
            if (is_array($decoded)) { $filters = $decoded; }
        } elseif (is_array($filtersParam)) {
            $filters = $filtersParam;
        }

        if (!empty($filters)) {
            if (!empty($filters['customer_id'])) {
                $ordersQuery->where('customer_id', (int)$filters['customer_id']);
            }
            // Note: deliverability status ('deliverable'/'undeliverable') is applied after computing can_deliver
        }

        // Apply date filtering (chart-like) based on query date_from/date_to
        $dateFrom = $request->query('date_from');
        $dateTo = $request->query('date_to');
        $normalizedDateFrom = $dateFrom ? $this->normalizePersianDate($dateFrom) : null;
        $normalizedDateTo = $dateTo ? $this->normalizePersianDate($dateTo) : null;
        if ($normalizedDateFrom || $normalizedDateTo) {
            $deadlineExpr = "COALESCE(STR_TO_DATE(deadline, '%Y-%m-%d'), STR_TO_DATE(deadline, '%Y/%m/%d'), STR_TO_DATE(deadline, '%Y-%m-%d %H:%i:%s'), STR_TO_DATE(deadline, '%Y/%m/%d %H:%i:%s'))";
            if ($normalizedDateFrom) {
                $ordersQuery->whereRaw("$deadlineExpr >= ?", [$normalizedDateFrom]);
            }
            if ($normalizedDateTo) {
                $ordersQuery->whereRaw("$deadlineExpr <= ?", [$normalizedDateTo]);
            }
        }

        // Fetch and compute derived fields
        $ordersCollection = $ordersQuery->orderBy('deadline', 'ASC')->get();
        foreach ($ordersCollection as $order) {
            $totalAmount = $order->orderItems->sum('commodity_amount');
            $totalValue = $order->orderItems->sum(function ($item) {
                return $item->price ? ($item->price * $item->commodity_amount) : 0;
            });
            
            // Check inventory for each order item - order is deliverable only if ALL items have sufficient inventory
            $canDeliver = false; // Default to false for orders without items
            $totalInventory = 0;
            if ($order->orderItems->isNotEmpty()) {
                $canDeliver = true; // Start with true, will be set to false if any item lacks inventory
                foreach ($order->orderItems as $item) {
                    $inventory = Inventory::where('commodity_id', $item->commodity_id)
                        ->where('unit_id', $item->unit_id)
                        ->where('amount', '>', 0)
                        ->sum('amount');
                    $totalInventory += $inventory;
                    if ($inventory < $item->commodity_amount) {
                        $canDeliver = false;
                        // Don't break - continue to calculate totalInventory for display
                    }
                }
            }
            
            $order->total_amount = $totalAmount;
            $order->total_value = $totalValue;
            $order->inventory_available = $totalInventory;
            $order->can_deliver = $canDeliver;
        }

        // Apply deliverability status filter (قابل تحویل / غیر قابل تحویل)
        if (!empty($filters['status'])) {
            if ($filters['status'] === 'deliverable') {
                $ordersCollection = $ordersCollection->where('can_deliver', true)->values();
            } elseif ($filters['status'] === 'undeliverable') {
                $ordersCollection = $ordersCollection->where('can_deliver', false)->values();
            }
        }

        // Manual pagination after computing deliverability
        $perPage = (int) $request->query('per_page', 10);
        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $total = $ordersCollection->count();
        $results = $ordersCollection->slice(($currentPage - 1) * $perPage, $perPage)->values();
        $pendingOrders = new LengthAwarePaginator($results, $total, $perPage, $currentPage, [
            'path' => $request->url(),
            'query' => $request->query(),
        ]);

        // Get real warehouse chart data
        $warehouseChartData = $this->getWarehouseChartData();

        // Calculate summary statistics
        // Summary cards should IGNORE filters and pagination – compute over ALL pending orders
        $allOrders = Order::where('status', 'pending')
            ->with(['orderItems.commodity', 'orderItems.unit', 'customer'])
            ->get();
        foreach ($allOrders as $order) {
            $totalAmount = $order->orderItems->sum('commodity_amount');
            $totalValue = $order->orderItems->sum(function ($item) {
                return $item->price ? ($item->price * $item->commodity_amount) : 0;
            });
            
            // Check inventory for each order item - order is deliverable only if ALL items have sufficient inventory
            $canDeliver = false; // Default to false for orders without items
            $totalInventory = 0;
            if ($order->orderItems->isNotEmpty()) {
                $canDeliver = true; // Start with true, will be set to false if any item lacks inventory
                foreach ($order->orderItems as $item) {
                    $inventory = Inventory::where('commodity_id', $item->commodity_id)
                        ->where('unit_id', $item->unit_id)
                        ->where('amount', '>', 0)
                        ->sum('amount');
                    $totalInventory += $inventory;
                    if ($inventory < $item->commodity_amount) {
                        $canDeliver = false;
                        // Don't break - continue to calculate totalInventory for display
                    }
                }
            }
            
            $order->total_amount = $totalAmount;
            $order->total_value = $totalValue;
            $order->inventory_available = $totalInventory;
            $order->can_deliver = $canDeliver;
        }
        $summaryStats = [
            'totalOrders' => $allOrders->count(),
            'totalValue' => $allOrders->sum('total_value'),
            'canDeliverCount' => $allOrders->where('can_deliver', true)->count(),
            'cannotDeliverCount' => $allOrders->where('can_deliver', false)->count(),
            'totalAmount' => $allOrders->sum('total_amount'),
            'totalInventory' => $allOrders->sum('inventory_available'),
        ];

        // Pagination options (include status deliverability and date range)
        $paginationOptions = [
            'searchable_fields' => ['customer.name', 'customer.comp_name', 'deadline'],
            'filterable_fields' => ['customer_id', 'status'],
            'per_page_options' => [5, 10, 25, 50, 100],
            'search_placeholder' => 'جستجو در نام مشتری یا تاریخ...',
            'status_options' => [
                'deliverable' => 'قابل تحویل',
                'undeliverable' => 'غیر قابل تحویل',
            ],
            'show_date_range' => true,
        ];

        return view('dashboard.order.factory-status', [
            'pendingOrders' => $pendingOrders,
            'warehouseChartData' => $warehouseChartData,
            'summaryStats' => $summaryStats,
            'options' => $paginationOptions,
        ]);
    }

    /**
     * Get individual orders chart data with real data.
     * Aggregates data by commodity+unit combination to avoid duplicates.
     *
     * @return array
     */
    private function getWarehouseChartData()
    {
        // Get real pending orders with inventory data
        $orders = Order::where('status', 'pending')
            ->with(['orderItems.commodity', 'orderItems.unit', 'customer'])
            ->get();

        // Aggregate by commodity_id + unit_id combination
        $aggregatedData = [];
        
        foreach ($orders as $order) {
            foreach ($order->orderItems as $item) {
                // Create a unique key for commodity+unit combination
                $key = $item->commodity_id . '_' . $item->unit_id;
                
                // Initialize aggregated entry if it doesn't exist
                if (!isset($aggregatedData[$key])) {
                    // Get inventory for this commodity+unit (only once per combination)
                    // Remove amount > 0 filter to ensure zero inventory products are included
                    $inventory = Inventory::where('commodity_id', $item->commodity_id)
                        ->where('unit_id', $item->unit_id)
                        ->sum('amount') ?? 0;
                    
                    $aggregatedData[$key] = [
                        'productName' => $item->commodity->title ?? 'نامشخص',
                        'orderedAmount' => 0, // Total across all orders (for reference)
                        'inventory' => $inventory,
                        'unit' => $item->unit->name ?? 'نامشخص',
                        'unitSymbol' => $item->unit->symbol ?? '',
                        'commodityId' => $item->commodity_id,
                        'unitId' => $item->unit_id,
                        'orderIds' => [], // Track which orders contribute to this aggregate
                        'orderAmounts' => [] // Track per-order amounts: orderId => amount
                    ];
                }
                
                // Sum the ordered amount for this commodity+unit combination
                $aggregatedData[$key]['orderedAmount'] += $item->commodity_amount;
                
                // Track order IDs that contribute to this aggregate
                if (!in_array($order->id, $aggregatedData[$key]['orderIds'])) {
                    $aggregatedData[$key]['orderIds'][] = $order->id;
                }
                
                // Track per-order amounts (sum if same order has multiple items of same commodity+unit)
                if (!isset($aggregatedData[$key]['orderAmounts'][$order->id])) {
                    $aggregatedData[$key]['orderAmounts'][$order->id] = 0;
                }
                $aggregatedData[$key]['orderAmounts'][$order->id] += $item->commodity_amount;
            }
        }

        // Convert to array format (remove keys, keep values)
        $chartData = array_values($aggregatedData);

        return [
            'orders' => $chartData
        ];
    }

    /**
     * Display customer details page with orders for a specific customer.
     * If order_id is provided, shows only that specific order's items.
     *
     * @param int $id Customer ID
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function customerDetails($id, Request $request)
    {
        // Get real customer data
        $customer = Customer::findOrFail($id);
        
        // Check if filtering by specific order
        $orderId = $request->query('order_id');
        
        // Build query for PENDING orders for this customer
        $ordersQuery = Order::where('customer_id', $id)
            ->where('status', 'pending')  // Only show pending orders
            ->with(['orderItems.commodity', 'orderItems.unit']);
        
        // Filter by specific order if provided
        if ($orderId) {
            $ordersQuery->where('id', (int)$orderId);
        }
        
        // Get orders and flatten to items
        $customerOrders = $ordersQuery->get()
            ->flatMap(function ($order) {
                return $order->orderItems->map(function ($item) use ($order) {
                    // Get inventory for this commodity
                    $inventory = Inventory::where('commodity_id', $item->commodity_id)
                        ->where('unit_id', $item->unit_id)
                        ->where('amount', '>', 0)
                        ->sum('amount');
                    
                    return (object)[
                        'id' => $item->id,
                        'order_number' => $order->id,
                        'commodity_title' => $item->commodity->title ?? 'نامشخص',
                        'amount' => $item->commodity_amount,
                        'unit' => $item->unit->name ?? 'نامشخص',
                        'unit_symbol' => $item->unit->symbol ?? '',
                        'deadline' => $order->deadline,
                        'status' => $order->status,
                        'total_value' => $item->price ? ($item->price * $item->commodity_amount) : 0,
                        'inventory' => $inventory,
                        'can_deliver' => $inventory >= $item->commodity_amount,
                        'created_at' => $order->created_at
                    ];
                });
            });

        // Calculate summary statistics
        $summaryStats = [
            'totalOrders' => $customerOrders->count(),
            'totalValue' => $customerOrders->sum('total_value'),
            'canDeliverCount' => $customerOrders->where('can_deliver', true)->count(),
            'cannotDeliverCount' => $customerOrders->where('can_deliver', false)->count(),
        ];

        return view('dashboard.order.customer-details', [
            'customer' => $customer,
            'orders' => $customerOrders,
            'summaryStats' => $summaryStats,
            'orderId' => $orderId,  // Pass to view to show appropriate message
        ]);
    }

    /**
     * Get item row partial for AJAX requests
     * Note: This method is now deprecated as we use client-side HTML generation for better performance
     *
     * @param \Illuminate\Http\Request $request
     * @return string
     */
    public function getItemRowPartial(Request $request)
    {
        // This method is kept for backward compatibility but should not be used
        // as we now generate HTML client-side for better performance
        return response()->json(['error' => 'This endpoint is deprecated. Use client-side HTML generation instead.']);
    }
}
