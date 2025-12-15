<?php

use App\Http\Controllers\ActivityController;
use App\Http\Controllers\CommodityController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DashboardController;

use App\Http\Controllers\LoginController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\Processes\ImportingRequestController;
use App\Http\Controllers\Processes\ProductionRequestController;
use App\Http\Controllers\Processes\WithdrawalRequestController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SellerController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UnitController;
use App\Http\Controllers\UnitConversionController;
use App\Http\Controllers\InventoryController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::middleware('guest')->group(function () {
    Route::get('/login',[LoginController::class,'create'])->name('login');
    Route::post('/login',[LoginController::class,'store'])->name('login.store');
});


Route::middleware('auth')->group(function () {

    Route::get('/logout',[LoginController::class,'logout'])->name('logout');
    Route::get('/',[DashboardController::class,'index'])->name('home');
    Route::post('dashboard/chart-data', [DashboardController::class, 'getChartData'])->name('dashboard.chart.data');
    Route::resource('user',UserController::class)->except('destroy');
    Route::get('user/rest-password/{user:id}',[UserController::class,'resetPassword'])->name('reset-password');
    Route::patch('user/rest-password/{user:id}',[UserController::class,'resetPasswordStore'])->name('reset-password.store');

    Route::resource('role',RoleController::class);
    Route::resource('activity',ActivityController::class)->only('show','index');
    Route::resource('settings',SettingsController::class)->except(['destroy']);
    Route::patch('settings/{setting}/toggle', [SettingsController::class, 'toggle'])->name('settings.toggle');

    // Prices tab and data (client-side export support) - keep BEFORE resource to avoid route shadowing
    Route::get('commodity/prices', [CommodityController::class, 'prices'])->name('commodity.prices');
    Route::get('commodity/search', [CommodityController::class, 'search'])->name('commodity.search');
    Route::resource('commodity',CommodityController::class);
    Route::resource('customer',CustomerController::class);
    Route::resource('seller',SellerController::class);

    Route::resource('importing-request',ImportingRequestController::class);
    Route::post('importing-request/get-selectable-units', [ImportingRequestController::class, 'getSelectableUnits'])->name('importing-request.get-selectable-units');
    Route::resource('withdrawal-request',WithdrawalRequestController::class);

    Route::resource('production-request',ProductionRequestController::class);

    Route::get('importing-request/approval/{id}',[ImportingRequestController::class,'approvalRequest'])->name('approval.importing');

    Route::get('importing-request/reject/{id}',[ImportingRequestController::class,'rejectRequest'])->name('reject.importing');

    Route::get('withdrawal-request/approval/{id}',[WithdrawalRequestController::class,'approvalRequest'])->name('approval.withdrawal');
    Route::get('withdrawal-request/approval/{id}/form',[WithdrawalRequestController::class,'approvalForm'])->name('approval.withdrawal.form');
    Route::post('withdrawal-request/approval/{id}',[WithdrawalRequestController::class,'approvalSubmit'])->name('approval.withdrawal.submit');

    Route::get('withdrawal-request/reject/{id}',[WithdrawalRequestController::class,'rejectRequest'])->name('reject.withdrawal');

    Route::get('production-request/approval/{id}',[ProductionRequestController::class,'approvalRequest'])->name('approval.production');

    Route::get('production-request/reject/{id}',[ProductionRequestController::class,'rejectRequest'])->name('reject.production');



    Route::get('order/confirm/{order}',[OrderController::class,'confirm'])->name('order.confirm');

    Route::post('order/confirm/{order}',[OrderController::class,'confirmStore'])->name('order-confirm.store');

    Route::get('importing/report',[ImportingRequestController::class,'createReport'])->name('importing.report.create');

    Route::post('importing/report',[ImportingRequestController::class,'storeReport'])->name('importing.report.store');
    
    Route::post('importing/report/convert-price',[ImportingRequestController::class,'convertPrice'])->name('importing.report.convert-price');

    Route::get('order/chart', [OrderController::class, 'chart'])->name('order.chart');
    Route::post('order/chart-data', [OrderController::class, 'getChartData'])->name('order.chart.data');
    Route::get('order/factory-status', [OrderController::class, 'factoryStatus'])->name('order.factory-status');
    Route::get('order/factory-status/customer/{id}', [OrderController::class, 'customerDetails'])->name('order.factory-status.customer');
    Route::resource('order',OrderController::class);



    Route::get('commodity-type-ajax/{id}',[CommodityController::class,'commodityType']);

    Route::resource('unit', UnitController::class);

    Route::get('unit-conversion/select-commodity', [UnitConversionController::class, 'index'])->name('unit-conversion.select-commodity');

    Route::resource('unit-conversion', UnitConversionController::class);
    Route::post('unit-conversion/convert', [UnitConversionController::class, 'convert'])->name('unit-conversion.convert');

    Route::resource('inventory', InventoryController::class)->except(['create', 'store']);
    Route::post('inventory/{inventory}/adjust-stock', [InventoryController::class, 'adjustStock'])->name('inventory.adjust-stock');
    Route::get('inventory-ajax/{commodityId}', [InventoryController::class, 'getCommodityInventory'])->name('inventory.ajax');
    Route::get('order/commodity-units/{commodityId}', [OrderController::class, 'getCommodityUnits'])->name('order.commodity.units');
    Route::get('order/calculate-weight/{commodityId}/{amount}/{unitId}', [OrderController::class, 'calculateWeight'])->name('order.calculate.weight');
    Route::get('order/partial/item-row', [OrderController::class, 'getItemRowPartial'])->name('order.partial.item-row');

});


Route::prefix('test')->group(function (){
    Route::get('/', function () {
        return view('dashboard.index');
    });

    Route::get('/login', function () {
        return view('login.index');
    });

});
