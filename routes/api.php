<?php

use App\Http\Controllers\Api\Admin\AdminOrderController;
use App\Http\Controllers\Api\Admin\AdminProductController;
use App\Http\Controllers\Api\AuthController;
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

Route::prefix('auth')->middleware('api')->controller(AuthController::class)->group(function () {
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
        Route::post('/products', [AdminProductController::class, 'store']);
    }
);


/** Order Routes */

Route::group(['middleware' => 'auth'], function () {
    Route::resource('admin/orders', AdminOrderController::class)->middleware('admin');
    Route::get('/orders', [OrderController::class, 'index']);
    Route::post('/orders', [OrderController::class, 'store']);
});

Route::group(['middleware' => 'check.order.owner'], function () {
    Route::get('/orders/{order}', [OrderController::class, 'show']);
    Route::put('/orders/{order}', [OrderController::class, 'update']);
    Route::delete('/orders/{order}', [OrderController::class, 'destroy']);
});
