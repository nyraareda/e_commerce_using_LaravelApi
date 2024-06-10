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

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::group([
    'middleware' => ['api'],
    'prefix' => 'auth'
], function () {
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/refresh', [AuthController::class, 'refresh']);
    Route::get('/user-profile', [AuthController::class, 'userProfile']);
    Route::put('/profile/update/{id}', [AuthController::class, 'updateProfile']);
    Route::get('email/verify/{id}', [AuthController::class, 'verify'])->name('verification.verify');
});

// Route::group(['middleware' => ['auth.jwt']], function () {
    
// });
// Route::get('/products', [ProductController::class, 'index']);
// Route::post('/products', [ProductController::class, 'store']);
// Route::post('/cart/{product_id}', [CartController::class, 'store']);
// Route::get('/products/{id}', [ProductController::class, 'show']);
// Route::post('/products/{id}', [ProductController::class, 'update']);
// Route::delete('/products/{id}', [ProductController::class, 'destroy']);
// Route::post('/products/search', [ProductController::class, 'search']);

Route::get('/category', [CategoryController::class, 'index']);
Route::post('/category', [CategoryController::class, 'store']);
Route::get('/category/{id}', [CategoryController::class, 'show']);
Route::put('/category/{id}', [CategoryController::class, 'update']);
Route::delete('/category/{id}', [CategoryController::class, 'destroy']);

Route::get('/attributes', [ProductAttributeController::class, 'index']);
Route::post('/attributes', [ProductAttributeController::class, 'store']);
Route::get('/attributes/{id}', [ProductAttributeController::class, 'show']);
Route::put('/attributes/{id}', [ProductAttributeController::class, 'update']);
Route::delete('/attributes/{id}', [ProductAttributeController::class, 'destroy']);


Route::group(['middleware' => ['api', 'auth:api', 'role:admin']], function () {
    Route::post('/products', [ProductController::class, 'store']);
    Route::post('/products/{id}', [ProductController::class, 'update']);
    Route::delete('/products/{id}', [ProductController::class, 'destroy']);

    // Cart routes
    Route::post('/cart/{product_id}', [CartController::class, 'store']);
});

