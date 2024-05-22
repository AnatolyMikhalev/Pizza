<?php

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

//Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//    return $request->user();
//});


Route::get('/products', [ProductController::class, 'index']);
Route::get('/products/{id}', [ProductController::class, 'show']);
//Route::post('/products', [ProductController::class, 'store']);
//Route::put('/products/{product}', [ProductController::class, 'update']);
//Route::delete('/products/{product}', [ProductController::class, 'destroy']);

//Route::get('/orders', [OrderController::class, 'index']);
//Route::get('/orders/{id}', [OrderCsontroller::class, 'show']);
//Route::post('/orders', [OrderController::class, 'store']);
//Route::post('orders', [OrderController::class, 'store']);

Route::prefix('auth')->middleware('api')->controller(AuthController::class)->group(function () {
    Route::post('/register', 'register');
    Route::post('/login', 'login');
    Route::post('/user', 'user');
    Route::post('/logout', 'logout');
    Route::post('/refresh', 'refresh');
});

//Route::group(['middleware' => 'auth'], function () {
//    Route::get('/products', [ProductController::class, 'index']);
//    Route::get('/products/{id}', [ProductController::class, 'show']);
//    Route::post('/products', [ProductController::class, 'store']);
//    Route::put('/products/{product}', [ProductController::class, 'update']);
//    Route::delete('/products/{product}', [ProductController::class, 'destroy']);
//});

Route::group(['namespace' => 'Admin', 'prefix' => 'admin','middleware' => 'auth'], function () {
    Route::get('/products/{id}', [ProductController::class, 'show']);
    Route::post('/products', [ProductController::class, 'store']);
    Route::put('/products/{product}', [ProductController::class, 'update']);
    Route::delete('/products/{product}', [ProductController::class, 'destroy']);

    Route::get('/orders', [OrderController::class, 'index']);
});

//Route::get('/products', [ProductController::class, 'index'])->middleware('admin');

Route::group(['namespace' => 'User', 'middleware' => 'auth'], function () {
    Route::get('/orders', [OrderController::class, 'index']);
    Route::post('/orders', [OrderController::class, 'store']);
});
