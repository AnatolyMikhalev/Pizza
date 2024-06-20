<?php

use App\Http\Controllers\Api\Admin\AdminOrderController;
use App\Http\Controllers\Api\Admin\AdminProductController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CartController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/


/** Auth Routes */

Route::group(['prefix' => 'auth','middleware' => 'api', 'controller' => AuthController::class], function () {
    Route::post('/register', 'register');
    Route::post('/login', 'login');
    Route::post('/user', 'user');

    Route::post('/logout', 'logout');
    Route::post('/refresh', 'refresh');
});


/** Product Routes */

Route::get('/products', [ProductController::class, 'index']);
Route::get('/products/{id}', [ProductController::class, 'show']);

Route::group(['prefix' => 'admin', 'middleware' => ['auth', 'admin']], function () {
        Route::resource('/products', AdminProductController::class);
    }
);


/** Order Routes */

Route::group(['middleware' => 'check.order.owner', 'controller' => OrderController::class], function () {
    Route::get('/orders/{order}', 'show');
    Route::put('/orders/{order}', 'update');
    Route::delete('/orders/{order}', 'destroy');
});

Route::group(['middleware' => 'auth'], function () {
    Route::resource('admin/orders', AdminOrderController::class)->middleware('admin');
    Route::get('/orders', [OrderController::class, 'index']);
    Route::post('/orders', [OrderController::class, 'store']);
});


/** Cart Routes */

Route::group(['middleware' => 'auth', 'controller' => CartController::class], function () {
    Route::get('/cart', 'index');
    Route::post('/cart', 'store');
    Route::delete('/cart', 'destroy');
});



