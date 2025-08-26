<?php

namespace App\Http\Controllers;

use App\Http\Requests\InventoryUpdateRequest;
use App\Models\Inventory;
use App\Models\Commodity;
use App\Models\Unit;
use App\Services\InventoryService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InventoryController extends Controller
{
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
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\Response
     */
    public function index()
    {
        $inventories = $this->service->getActiveInventory();
        
        // Add empty state handling
        if ($inventories->isEmpty()) {
            return view('dashboard.inventory.index', compact('inventories'))
                ->with('message', 'هیچ موجودی فعالی یافت نشد. موجودی ها از طریق فرآیندهای خرید و فروش ایجاد می‌شوند.');
        }
        
        return view('dashboard.inventory.index', compact('inventories'));
    }



    /**
     * Display the specified resource.
     *
     * @param Inventory $inventory
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\Response
     */
    public function show(Inventory $inventory)
    {
        return view('dashboard.inventory.show', compact('inventory'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param Inventory $inventory
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\Response
     */
    public function edit(Inventory $inventory)
    {
        $commodities = Commodity::all();
        $units = Unit::all();
        return view('dashboard.inventory.edit', compact('inventory', 'commodities', 'units'));
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
            $commodity = Commodity::findOrFail($commodityId);
            
            // Get the latest inventory price for this commodity
            $inventory = Inventory::where('commodity_id', $commodityId)
                ->where('active', true)
                ->orderBy('created_at', 'desc')
                ->first();

            // Use the calculated sale price from commodity (not stored in inventory)
            $price = $commodity->sales_price ?? 0;
            
            return response()->json([
                'success' => true,
                'price' => $price,
                'commodity' => [
                    'id' => $commodity->id,
                    'title' => $commodity->title,
                    'type' => $commodity->type
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'خطا در دریافت اطلاعات کالا',
                'price' => 0
            ], 500);
        }
    }
} 