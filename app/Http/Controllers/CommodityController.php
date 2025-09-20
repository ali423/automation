<?php

namespace App\Http\Controllers;

use App\Http\Requests\CommodityRequest;
use App\Http\Requests\CommodityUpdateRequest;
use App\Models\Commodity;
use App\Models\Unit;
use App\Services\CommodityService;
use App\Traits\PaginationTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CommodityController extends Controller
{
    use PaginationTrait;
    
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
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // Build query with eager loading to fix N+1 query problem
        $query = Commodity::with(['unit', 'materials.unit']);
        
        // Use advanced pagination with search and filter capabilities
        $commodities = $this->getPaginatedResults($query, $request, 10, [
            'searchable_fields' => ['title', 'number', 'product_identifier'],
            'filterable_fields' => ['type', 'unit_id'],
            'sortable_fields' => ['id', 'title', 'number', 'type', 'purchase_price', 'warning_limit', 'profit_margin', 'pieces_per_box', 'weight_per_unit', 'created_at', 'updated_at'],
            'default_sort_field' => 'id',
            'default_sort_direction' => 'desc',
            'max_per_page' => 50
        ]);
        
        // Pre-calculate base prices efficiently
        $this->preCalculateBasePrices($commodities);
        
        // Prepare options for the pagination components
        $paginationOptions = [
            'searchable_fields' => ['title', 'number', 'product_identifier'],
            'filterable_fields' => ['type', 'unit_id']
        ];
        
        return view('dashboard.commodity.index', [
            'commodities' => $commodities,
            'options' => $paginationOptions,
        ]);
    }
    
    /**
     * Pre-calculate base prices for multiple commodities efficiently
     *
     * @param \Illuminate\Pagination\LengthAwarePaginator $commodities
     * @return void
     */
    private function preCalculateBasePrices($commodities)
    {
        // Group commodities by type for efficient processing
        $products = $commodities->where('type', 'product');
        $materials = $commodities->where('type', 'material');
        
        // For materials, just use purchase_price
        foreach ($materials as $material) {
            $material->base_price = $material->purchase_price;
        }
        
        // For products, calculate material costs efficiently
        if ($products->isNotEmpty()) {
            $this->calculateProductBasePrices($products->values());
        }
    }
    
    /**
     * Calculate base prices for products efficiently
     *
     * @param \Illuminate\Support\Collection|\Illuminate\Pagination\LengthAwarePaginator $products
     * @return void
     */
    private function calculateProductBasePrices($products)
    {
        $commodityUnitService = app(\App\Services\CommodityUnitService::class);
        
        foreach ($products as $product) {
            $totalCost = 0;
            
            foreach ($product->materials as $material) {
                $amount = $material->pivot->amount;
                $unitId = $material->pivot->unit_id;
                
                // Convert to material's main unit for cost calculation
                $amountInMaterialUnit = $commodityUnitService->convertToMainUnit($material, $amount, $unitId);
                
                if ($amountInMaterialUnit !== null) {
                    $materialCost = $amountInMaterialUnit * $material->purchase_price;
                    $totalCost += $materialCost;
                }
            }
            
            $product->base_price = round($totalCost, 2);
        }
    }

    /**
     * Calculate base price for a single commodity (legacy method for other uses)
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
        $commodity->load(['unit', 'materials.unit']);
        
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
        $commodity->load(['unit', 'materials.unit']);
        
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
