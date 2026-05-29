<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Services\InventoryService;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Efficient inventory API for production requests
Route::get('/production/inventory', function (Request $request) {
    $productId = $request->get('product_id');
    
    if (!$productId) {
        return response()->json(['error' => 'Product ID required'], 400);
    }
    
    // Load product with materials
    $product = \App\Models\Commodity::with(['materials.unit'])
        ->where('type', 'product')
        ->find($productId);
    
    if (!$product) {
        return response()->json(['error' => 'Product not found'], 404);
    }
    
    $materialsData = [];
    
    foreach ($product->materials as $material) {
        $materialsData[] = [
            'material_id' => $material->id,
            'title' => $material->title,
            'amount' => $material->pivot->amount,
            'unit_id' => $material->pivot->unit_id,
            'unit_symbol' => \App\Models\Unit::find($material->pivot->unit_id) ? \App\Models\Unit::find($material->pivot->unit_id)->symbol : ''
        ];
    }
    
    return response()->json([
        'product' => [
            'id' => $product->id,
            'title' => $product->title,
            'unit' => $product->unit ? $product->unit->symbol : '',
            'pieces_per_box' => $product->pieces_per_box,
        ],
        'materials' => $materialsData
    ]);
})->name('api.production.inventory');
