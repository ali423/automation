<?php

namespace App\Http\Controllers;

use App\Http\Requests\InventoryUpdateRequest;
use App\Models\Inventory;
use App\Models\Commodity;
use App\Models\Unit;
use App\Services\InventoryService;
use App\Traits\PaginationTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InventoryController extends Controller
{
    use PaginationTrait;
    
    protected $service;

    public function __construct(InventoryService $service)
    {
        $this->service = $service;
        $this->authorizeResource(Inventory::class);
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
        $query = Inventory::with(['commodity', 'unit'])
            ->where('amount', '>', 0);
        
        // Use advanced pagination with search and filter capabilities
        $inventories = $this->getPaginatedResults($query, $request, 10, [
            'searchable_fields' => ['commodity.title', 'commodity.number', 'commodity.product_identifier', 'unit.name'],
            'filterable_fields' => ['unit_id'],
            'sortable_fields' => ['id', 'created_at', 'updated_at', 'amount', 'purchase_price'],
            'default_sort_field' => 'created_at',
            'default_sort_direction' => 'desc',
            'max_per_page' => 50
        ]);
        
        // Pre-calculate financial data efficiently
        $this->preCalculateFinancialData($inventories);
        
        // Prepare options for the pagination components
        $paginationOptions = [
            'searchable_fields' => ['commodity.title', 'commodity.number', 'commodity.product_identifier', 'unit.name'],
            'filterable_fields' => ['unit_id'],
            'per_page_options' => [5, 10, 25, 50, 100],
            'search_placeholder' => 'جستجو در کالا، شماره، شناسه کالا یا واحد...'
        ];
        
        return view('dashboard.inventory.index', [
            'inventories' => $inventories,
            'options' => $paginationOptions,
        ]);
    }
    
    /**
     * Pre-calculate financial data for multiple inventories efficiently
     *
     * @param \Illuminate\Pagination\LengthAwarePaginator $inventories
     * @return void
     */
    private function preCalculateFinancialData($inventories)
    {
        // Pre-calculate financial data to avoid N+1 queries in views
        $inventories->getCollection()->transform(function ($inventory) {
            $inventory->financial_data = $this->service->calculateFinancialData($inventory);
            return $inventory;
        });
    }

    /**
     * Display the specified resource.
     *
     * @param Inventory $inventory
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\Response
     */
    public function show(Inventory $inventory)
    {
        // Load relationships and pre-calculate financial data
        $inventory->load(['commodity', 'unit']);
        $financialData = $this->service->calculateFinancialData($inventory);
        
        return view('dashboard.inventory.show', compact('inventory', 'financialData'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param Inventory $inventory
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\Response
     */
    public function edit(Inventory $inventory)
    {
        // Load inventory with relationships
        $inventory->load(['commodity', 'unit']);
        
        // Get optimized data for form
        $formData = $this->service->getFormData();
        
        return view('dashboard.inventory.edit', compact('inventory', 'formData'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param InventoryUpdateRequest $request
     * @param Inventory $inventory
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(InventoryUpdateRequest $request, Inventory $inventory)
    {
        DB::transaction(function () use ($request, $inventory) {
            $this->service->update($inventory, $request->validated());
        });
        return redirect()->route('inventory.index')->with('successful', 'اطلاعات ویرایش شد.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Inventory $inventory
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Inventory $inventory)
    {
        DB::transaction(function () use ($inventory) {
            $this->service->delete($inventory);
        });
        return redirect()->route('inventory.index')->with('successful', 'اطلاعات حذف شدند.');
    }

    /**
     * Manual stock adjustment
     *
     * @param Request $request
     * @param Inventory $inventory
     * @return \Illuminate\Http\RedirectResponse
     */
    public function adjustStock(Request $request, Inventory $inventory)
    {
        $request->validate([
            'adjustment_type' => 'required|in:add,subtract',
            'quantity' => 'required|numeric|min:0.01',
            'reason' => 'nullable|string|max:255'
        ]);

        DB::transaction(function () use ($request, $inventory) {
            $this->service->adjustStock($inventory, $request->all());
        });
        return redirect()->route('inventory.show', $inventory)->with('successful', 'موجودی با موفقیت تنظیم شد.');
    }

    /**
     * AJAX endpoint to get commodity inventory data
     *
     * @param int $commodityId
     * @return \Illuminate\Http\JsonResponse
     */
    public function getCommodityInventory($commodityId)
    {
        try {
            $inventoryData = $this->service->getCommodityInventoryData($commodityId);
            
            return response()->json([
                'success' => true,
                'data' => $inventoryData
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }
} 