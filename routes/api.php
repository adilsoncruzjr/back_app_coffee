<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ShoppingCartController;
use App\Http\Controllers\OrderController;

// Rotas públicas (não exigem autenticação)
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::middleware('auth:sanctum')->post('logout', [AuthController::class, 'logout']);

// Rotas protegidas (exigem autenticação com Sanctum)
Route::middleware('auth:sanctum')->group(function () {
   
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'user']);
    Route::get('user/{id}', [AuthController::class, 'getUserById']);
    Route::put('/user/{id}', [AuthController::class, 'updateUser']);
    Route::get('user/{id}/orders', [AuthController::class, 'getOrdersId']);
    Route::put('user/{id}/orders', [AuthController::class, 'addOrderId']);
    
    Route::post('/products', [ProductController::class, 'store']);
    Route::get('/products', [ProductController::class, 'index']);
    Route::get('/products/{id_prod}', [ProductController::class, 'show']);
    Route::get('products/{id}/stock', [ProductController::class, 'getStock']);
    Route::put('products/{id}/stock', [ProductController::class, 'updateStock']);

    Route::post('/shopping-cart', [ShoppingCartController::class, 'store']);
    Route::get('shopping-cart/{id}/products', [ShoppingCartController::class, 'getProductIds']);

    Route::post('orders', [OrderController::class, 'store']);
    Route::get('/user-orders/{id}', [OrderController::class, 'getUserOrders']);
    Route::get('orders/{id}/details', [OrderController::class, 'getOrderDetails']);
    Route::put('orders/{id}/details', [OrderController::class, 'updateOrderDetails']);
    Route::get('orders/{id}/status', [OrderController::class, 'getOrderStatus']);
    Route::put('orders/{id}/status', [OrderController::class, 'updateOrderStatus']);
});