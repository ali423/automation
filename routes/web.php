<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
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
    Route::resource('user',UserController::class);
    Route::resource('role',RoleController::class);
});


Route::prefix('test')->group(function (){
    Route::get('/', function () {
        return view('dashboard.index');
    });

    Route::get('/login', function () {
        return view('login.index');
    });

});
