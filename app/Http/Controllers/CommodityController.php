<?php

namespace App\Http\Controllers;

use App\Http\Requests\CommodityRequest;
use App\Http\Requests\CommodityUpdateRequest;
use App\Models\Commodity;
use App\Models\Unit;
use App\Services\CommodityService;
use Illuminate\Support\Facades\DB;

class CommodityController extends Controller
{
    protected $service;

    public function __construct(CommodityService $service)
    {
        $this->service = $service;
        $this->authorizeResource(Commodity::class);
        $this->shareView();
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\Response
     */
    public function index()
    {
        // Fix N+1 query problem by eager loading relationships
        $commodities = Commodity::with(['unit', 'materials.unit', 'warehouses'])
            ->orderBy('id', 'DESC')
            ->paginate(20); // Add pagination for better performance
        
        // Pre-calculate base prices to avoid N+1 queries in the view
        foreach ($commodities as $commodity) {
            $commodity->base_price = $this->calculateBasePrice($commodity);
        }
        
        return view('dashboard.commodity.index', [
            'commodities' => $commodities,
        ]);
    }
    
    /**
     * Calculate base price for a commodity without triggering N+1 queries
     *
     * @param Commodity $commodity
     * @return float|null
     */
    private function calculateBasePrice(Commodity $commodity)
    {
        if ($commodity->type === 'product') {
            // For products, calculate material cost
            $totalCost = 0;
            
            foreach ($commodity->materials as $material) {
                $amount = $material->pivot->amount;
                $unitId = $material->pivot->unit_id;
                
                // Convert to material's main unit for cost calculation
                $commodityUnitService = app(\App\Services\CommodityUnitService::class);
                $amountInMaterialUnit = $commodityUnitService->convertToMainUnit($material, $amount, $unitId);
                
                if ($amountInMaterialUnit !== null) {
                    $materialCost = $amountInMaterialUnit * $material->purchase_price;
                    $totalCost += $materialCost;
                }
            }
            
            return round($totalCost, 2);
        }
        
        return $commodity->purchase_price;
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\Response
     */
    public function create()
    {
        // Optimize: Load materials with their units and conversions in one query
        $materials = Commodity::where('type', 'material')
            ->with(['unit', 'unitConversions.fromUnit', 'unitConversions.toUnit'])
            ->get();
        
        // Pre-calculate selectable units to avoid N+1 queries
        $commodityUnitService = app(\App\Services\CommodityUnitService::class);
        $materialsWithUnits = $materials->map(function ($material) use ($commodityUnitService) {
            $selectableUnits = $commodityUnitService->getSelectableUnits($material);
            $material->selectable_units = $selectableUnits;
            return $material;
        });
        
        return view('dashboard.commodity.create', [
            'materials' => $materialsWithUnits,
            'units' => Unit::all()
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Http\Response|\Illuminate\Routing\Redirector
     */
    public function store(CommodityRequest $request)
    {
        DB::transaction(function () use ($request) {
            $this->service->create($request->validationData());
        });
        return redirect(route('commodity.index'))->with('successful', 'اطلاعات ثبت شد.');
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Models\Commodity $commodity
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\Response
     */
    public function show(Commodity $commodity)
    {
        // Optimize: Load commodity with all necessary relationships
        $commodity->load(['unit', 'materials.unit', 'warehouses']);
        
        // Pre-calculate base price to avoid N+1 queries
        $commodity->base_price = $this->calculateBasePrice($commodity);
        
        $materials = $commodity->materials;
        
        return view('dashboard.commodity.show', [
            'commodity' => $commodity,
            'materials' => $materials,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Models\Commodity $commodity
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\Response
     */
    public function edit(Commodity $commodity)
    {
        // Optimize: Load commodity with all necessary relationships
        $commodity->load(['unit', 'materials.unit', 'warehouses']);
        
        // Pre-calculate base price to avoid N+1 queries
        $commodity->base_price = $this->calculateBasePrice($commodity);
        
        $used_materials = $commodity->materials;
        
        // Optimize: Load materials with their units and conversions in one query
        $materials = Commodity::where('type', 'material')
            ->with(['unit', 'unitConversions.fromUnit', 'unitConversions.toUnit'])
            ->get();
        
        // Pre-calculate selectable units to avoid N+1 queries
        $commodityUnitService = app(\App\Services\CommodityUnitService::class);
        $materialsWithUnits = $materials->map(function ($material) use ($commodityUnitService) {
            $selectableUnits = $commodityUnitService->getSelectableUnits($material);
            $material->selectable_units = $selectableUnits;
            return $material;
        });

        return view('dashboard.commodity.edit', [
            'commodity' => $commodity,
            'units' => Unit::all(),
            'materials' => $materialsWithUnits,
            'used_materials' => $used_materials,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Commodity $commodity
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Http\Response|\Illuminate\Routing\Redirector
     */
    public function update(CommodityUpdateRequest $request, Commodity $commodity)
    {
        DB::transaction(function () use ($request, $commodity) {
            $this->service->update($commodity, $request->validationData());
        });
        return redirect(route('commodity.show', $commodity))->with('successful', 'اطلاعات ویرایش شد.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\Commodity $commodity
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Http\Response|\Illuminate\Routing\Redirector
     */
    public function destroy(Commodity $commodity)
    {
        $commodity->delete();
        return redirect(route('commodity.index'))->with('successful', 'اطلاعات حذف شدند.');
    }

    public function inventory($id)
    {
        // Optimize: Load commodity with warehouses in one query
        $commodity = Commodity::with(['warehouses'])
            ->findOrFail($id);
            
        // Pre-calculate sales price to avoid N+1 queries
        $commodity->sales_price = $this->calculateSalesPrice($commodity);
        
        $warehouses = $commodity->warehouses()
            ->where('commodity_amount', '>', 0)
            ->get();
            
        $warehouse_res = $warehouses->map(function ($warehouse) {
            return [
                'id' => $warehouse->id,
                'title' => $warehouse->title,
                'amount' => $warehouse->pivot->commodity_amount,
            ];
        })->toArray();
        
        $res = [
            'warehouses' => $warehouse_res,
            'price' => $commodity->sales_price,
        ];
        
        return response()->json($res);
    }
    
    /**
     * Calculate sales price for a commodity
     *
     * @param Commodity $commodity
     * @return float|null
     */
    private function calculateSalesPrice(Commodity $commodity)
    {
        if ($commodity->type !== 'product') {
            return null;
        }

        $basePrice = $this->calculateBasePrice($commodity);
        if ($basePrice === null || $commodity->profit_margin === null) {
            return null;
        }

        // Calculate sales price: base price + profit margin percentage
        $profitAmount = $basePrice * ($commodity->profit_margin / 100);
        return round($basePrice + $profitAmount, 2);
    }
    public function commodityType($id)
    {
        // Optimize: Only select the type field instead of loading the entire model
        $type = Commodity::where('id', $id)->value('type');
        
        if (!$type) {
            return response()->json(['error' => 'Commodity not found'], 404);
        }
        
        return response()->json([
            'type' => $type,
        ]);
    }


}
