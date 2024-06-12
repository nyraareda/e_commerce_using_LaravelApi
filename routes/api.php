<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\CartController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\ProductAttributeController;
use App\Http\Controllers\Api\AuthController;


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

Route::group([
    'middleware' => ['api'],
    'prefix' => 'auth'

], function () {
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/logout', [AuthController::class, 'logout']);

});

Route::group(['middleware' => ['api', 'auth:api', 'role:admin']], function () {

    //EndPoint for product
    Route::post('/products', [ProductController::class, 'store']);
    Route::put('/products/{id}', [ProductController::class, 'update']);
    Route::delete('/products/{id}', [ProductController::class, 'destroy']);
    Route::get('/products/{id}', [ProductController::class, 'show']);
    Route::post('/products/search', [ProductController::class, 'search']);

    //EndPoint for Category

    Route::get('/category', [CategoryController::class, 'index']);
    Route::post('/category', [CategoryController::class, 'store']);
    Route::get('/category/{id}', [CategoryController::class, 'show']);
    Route::put('/category/{id}', [CategoryController::class, 'update']);
    Route::delete('/category/{id}', [CategoryController::class, 'destroy']);

    //EndPoint for attributes

    Route::get('/attributes', [ProductAttributeController::class, 'index']);
    Route::post('/attributes', [ProductAttributeController::class, 'store']);
    Route::get('/attributes/{id}', [ProductAttributeController::class, 'show']);
    Route::put('/attributes/{id}', [ProductAttributeController::class, 'update']);
    Route::delete('/attributes/{id}', [ProductAttributeController::class, 'destroy']);

    Route::get('/carts', [CartController::class, 'index']);

});

Route::group(['middleware' => ['api', 'auth:api', 'role:customer']], function () {

    Route::get('/products/{id}', [ProductController::class, 'show']);
    Route::get('/products', [ProductController::class, 'index']);
    //Endpoint for Cart
    Route::post('/carts/{product_id}', [CartController::class, 'store']);
    Route::put('/carts/{product_id}', [CartController::class, 'update']);
    Route::delete('/carts/{id}', [CartController::class, 'destroy']);
    Route::get('/carts/{id}', [CartController::class, 'show']);

});


