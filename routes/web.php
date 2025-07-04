<?php

use App\Http\Controllers\ActivityController;
use App\Http\Controllers\CommodityController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\Processes\ImportingRequestController;
use App\Http\Controllers\Processes\WithdrawalRequestController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SellerController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WarehouseController;
use App\Http\Controllers\UnitController;
use App\Http\Controllers\UnitConversionController;
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
    Route::resource('user',UserController::class)->except('destroy');
    Route::get('user/rest-password/{user:id}',[UserController::class,'resetPassword'])->name('reset-password');
    Route::patch('user/rest-password/{user:id}',[UserController::class,'resetPasswordStore'])->name('reset-password.store');

    Route::resource('role',RoleController::class);
    Route::resource('activity',ActivityController::class)->only('show','index');

    Route::resource('commodity',CommodityController::class);
    Route::resource('warehouse',WarehouseController::class);
    Route::resource('customer',CustomerController::class);
    Route::resource('seller',SellerController::class);

    Route::resource('importing-request',ImportingRequestController::class);

    Route::resource('withdrawal-request',WithdrawalRequestController::class);

    Route::get('importing-request/approval/{id}',[ImportingRequestController::class,'approvalRequest'])->name('approval.importing');

    Route::get('importing-request/reject/{id}',[ImportingRequestController::class,'rejectRequest'])->name('reject.importing');

    Route::get('withdrawal-request/approval/{id}',[WithdrawalRequestController::class,'approvalRequest'])->name('approval.withdrawal');

    Route::get('withdrawal-request/reject/{id}',[WithdrawalRequestController::class,'rejectRequest'])->name('reject.withdrawal');

    Route::get('inventory/edit',[InventoryController::class,'edit'])->name('inventory.edit');

    Route::patch('inventory/update',[InventoryController::class,'update'])->name('inventory.update');

    Route::get('order/confirm/{order}',[OrderController::class,'confirm'])->name('order.confirm');

    Route::post('order/confirm/{order}',[OrderController::class,'confirmStore'])->name('order-confirm.store');

    Route::get('importing/report',[ImportingRequestController::class,'createReport'])->name('importing.report.create');

    Route::post('importing/report',[ImportingRequestController::class,'storeReport'])->name('importing.report.store');

    Route::resource('order',OrderController::class);

    Route::resource('inventory',InventoryController::class)->only('index','show');

    Route::get('inventory-ajax/{id}',[CommodityController::class,'inventory'])->name('inventory');

    Route::get('commodity-type-ajax/{id}',[CommodityController::class,'commodityType']);

    Route::resource('unit', UnitController::class);

    Route::get('unit-conversion/select-commodity', [UnitConversionController::class, 'index'])->name('unit-conversion.select-commodity');

    Route::resource('unit-conversion', UnitConversionController::class);
    Route::post('unit-conversion/convert', [UnitConversionController::class, 'convert'])->name('unit-conversion.convert');

});


Route::prefix('test')->group(function (){
    Route::get('/', function () {
        return view('dashboard.index');
    });

    Route::get('/login', function () {
        return view('login.index');
    });

});
