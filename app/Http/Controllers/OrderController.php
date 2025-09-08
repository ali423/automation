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
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\Inventory;

class OrderController extends Controller
{
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
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\Response
     */
    public function index()
    {
        $orders = Order::query()->with(['customer', 'orderItems.commodity', 'orderItems.unit'])
            ->whereHas('customer')
            ->whereHas('orderItems.commodity')
            ->orderByRaw("FIELD(status, \"pending\", \"done\")")
            ->orderBy('deadline', 'ASC')->get();
        return view('dashboard.order.index',
            [
                'orders' => $orders,
            ]);
    }

    /**
     * Display the order chart page.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\Response
     */
    public function chart()
    {
        $orders = Order::query()->with(['customer', 'orderItems.commodity', 'orderItems.unit'])
            ->whereHas('customer')
            ->whereHas('orderItems.commodity')
            ->orderByRaw("FIELD(status, 'pending', 'done')")
            ->orderBy('deadline', 'ASC')->get();
        return view('dashboard.order.chart', [
            'orders' => $orders,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function create()
    {
        $commodities = Commodity::query()->where('type', 'product')->with(['unit', 'unitConversions.fromUnit', 'unitConversions.toUnit'])->get();
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
        
        $inventoryInfo = $this->service->getInventoryInfo($order);
        
        return view('dashboard.order.show',
            [
                'order' => $order,
                'inventoryInfo' => $inventoryInfo,
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
        
        $commodities = Commodity::query()->where('type', 'product')->with(['unit', 'unitConversions.fromUnit', 'unitConversions.toUnit'])->get();
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
                return $this->withdrawal_service->create($data, $file);
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
        
        // Start with base query
        $ordersQuery = Order::with(['orderItems.commodity.materials.unit', 'orderItems.unit'])
            ->where('status', 'pending'); // Only include pending orders
        
        // Apply date filtering if provided
        if ($normalizedDateFrom) {
            $ordersQuery->where('deadline', '>=', $normalizedDateFrom);
        }
        
        if ($normalizedDateTo) {
            $ordersQuery->where('deadline', '<=', $normalizedDateTo);
        }
        
        // Apply order ID filtering if provided
        if (!empty($orderIds)) {
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
            
            // Parse the date parts
            $parts = explode('/', $dateWithEnglishDigits);
            if (count($parts) !== 3) {
                return null;
            }
            
            $year = (int)$parts[0];
            $month = (int)$parts[1];
            $day = (int)$parts[2];
            
            // Validate Persian date
            if ($year < 1300 || $year > 1500 || $month < 1 || $month > 12 || $day < 1 || $day > 31) {
                return null;
            }
            
            // Return normalized date in YYYY/M/D format (matching database format)
            return sprintf('%04d/%d/%d', $year, $month, $day);
            
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
                'unit' => $commodity->unit ? $commodity->unit->name : null
            ]
        ]);
    }

    /**
     * Display factory status page.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function factoryStatus()
    {
        // Get real pending orders with customer and commodity information
        $pendingOrders = Order::where('status', 'pending')
            ->with(['customer', 'orderItems.commodity', 'orderItems.unit'])
            ->get()
            ->map(function ($order) {
                // Get total order amount and value
                $totalAmount = $order->orderItems->sum('commodity_amount');
                $totalValue = $order->orderItems->sum(function ($item) {
                    return $item->price ? ($item->price * $item->commodity_amount) : 0;
                });
                
                // Get inventory for this commodity
                $inventory = 0;
                if ($order->orderItems->isNotEmpty()) {
                    $firstItem = $order->orderItems->first();
                    $inventory = Inventory::where('commodity_id', $firstItem->commodity_id)
                        ->where('unit_id', $firstItem->unit_id)
                        ->where('amount', '>', 0)
                        ->sum('amount');
                }
                
                return (object)[
                    'id' => $order->id,
                    'customer' => $order->customer,
                    'orderItems' => $order->orderItems,
                    'deadline' => $order->deadline,
                    'total_amount' => $totalAmount,
                    'total_value' => $totalValue,
                    'inventory_available' => $inventory,
                    'can_deliver' => $inventory >= $totalAmount
                ];
            });

        // Get real warehouse chart data
        $warehouseChartData = $this->getWarehouseChartData();

        return view('dashboard.order.factory-status', [
            'pendingOrders' => $pendingOrders,
            'warehouseChartData' => $warehouseChartData,
        ]);
    }

    /**
     * Get individual orders chart data with real data.
     *
     * @return array
     */
    private function getWarehouseChartData()
    {
        // Get real pending orders with inventory data
        $orders = Order::where('status', 'pending')
            ->with(['orderItems.commodity', 'orderItems.unit'])
            ->get();

        $chartData = [];
        
        foreach ($orders as $order) {
            foreach ($order->orderItems as $item) {
                // Get inventory for this commodity
                $inventory = Inventory::where('commodity_id', $item->commodity_id)
                    ->where('unit_id', $item->unit_id)
                    ->where('amount', '>', 0)
                    ->sum('amount');
                
                $chartData[] = [
                    'orderId' => $order->id,
                    'customerName' => $order->customer->name ?? 'نامشخص',
                    'productName' => $item->commodity->title ?? 'نامشخص',
                    'orderedAmount' => $item->commodity_amount,
                    'inventory' => $inventory,
                    'unit' => $item->unit->name ?? 'نامشخص',
                    'unitSymbol' => $item->unit->symbol ?? ''
                ];
            }
        }

        return [
            'orders' => $chartData
        ];
    }

    /**
     * Display customer details page with all orders for a specific customer.
     *
     * @param int $id
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function customerDetails($id)
    {
        // Get real customer data
        $customer = Customer::findOrFail($id);
        
        // Get real orders for this customer with related data
        $customerOrders = Order::where('customer_id', $id)
            ->with(['orderItems.commodity', 'orderItems.unit'])
            ->get()
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

        return view('dashboard.order.customer-details', [
            'customer' => $customer,
            'orders' => $customerOrders
        ]);
    }
}
