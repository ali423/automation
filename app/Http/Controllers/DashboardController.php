<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Inventory;
use Illuminate\Http\Request;

class DashboardController extends Controller
{

    public function __construct()
    {
        $this->shareView();
    }

    public function index(){
        return view('dashboard.index');
    }

    /**
     * Get chart data for raw materials required (processing orders) and inventory.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getChartData(Request $request)
    {
        // Get all pending orders (processing orders)
        $orders = Order::with(['orderItems.commodity.materials.unit', 'orderItems.unit'])
            ->where('status', 'pending')
            ->get();

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

        // Calculate raw materials required for all pending orders
        foreach ($orders as $order) {
            foreach ($order->orderItems as $item) {
                $product = $item->commodity;
                
                if (!$product || $product->type !== 'product') {
                    continue; // Skip if not a product or commodity doesn't exist
                }

                // Get the product formula (raw materials)
                $productFormulaService = app(\App\Services\ProductFormulaService::class);
                
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
}
