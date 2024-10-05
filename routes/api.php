<?php

use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\CustomerController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\Api\BentoController;
use App\Http\Controllers\Api\StoreController;
use Illuminate\Support\Facades\Route;

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



// Routes that require authentication
Route::middleware(['auth:sanctum', 'admin'])->group(function () {
    Route::get('/user', [\App\Http\Controllers\Api\AuthController::class, 'getUser']);
    Route::post('/logout', [\App\Http\Controllers\Api\AuthController::class, 'logout']);
    Route::get('/stores', [StoreController::class, 'index']); // Fetch all stores
    Route::post('/stores', [StoreController::class, 'store']); // Create a new store
    Route::get('/stores/{id}', [StoreController::class, 'show']); // Get a store by ID
    Route::put('/stores/{id}', [StoreController::class, 'update']); // Update a store
    Route::delete('/stores/{id}', [StoreController::class, 'destroy']); // Delete a store
    Route::get('bentos/{bento}', [BentoController::class, 'show']);
    Route::post('/bentos/batch', [BentoController::class, 'storeBatch']);
    Route::get('/bentos', [BentoController::class, 'getBentos']);
    Route::put('bentos/{bento}', [BentoController::class, 'update']);
    Route::delete('/bentos/{bento}', [BentoController::class, 'destroy']);
    Route::get('/dashboard/recent-stores', [DashboardController::class, 'recentStores']);
    Route::get('/dashboard/user-feedback', [DashboardController::class, 'getUserFeedback']);
    Route::apiResource('users', UserController::class);
    Route::apiResource('customers', CustomerController::class);
    Route::apiResource('categories', CategoryController::class)->except('show');
    Route::get('/categories/tree', [CategoryController::class, 'getAsTree']);
    Route::get('/countries', [CustomerController::class, 'countries']);
    Route::get('orders', [OrderController::class, 'index']);
    Route::get('orders/statuses', [OrderController::class, 'getStatuses']);
    Route::post('orders/change-status/{order}/{status}', [OrderController::class, 'changeStatus']);
    Route::get('orders/{order}', [OrderController::class, 'view']);

    // Dashboard Routes
    Route::get('/dashboard/users-count', [DashboardController::class, 'activeUsers']);
    Route::get('/dashboard/bentos-count', [DashboardController::class, 'activeBentos']);
    Route::get('/dashboard/stores-count', [DashboardController::class, 'activeStores']);
    Route::get('/dashboard/reviews-count', [DashboardController::class, 'activeReviews']);
    //Route::get('/bentos', [BentoController::class, 'index']);
    Route::post('/bentos/{bento}/update-dynamic', [BentoController::class, 'storeUpdate']);
    Route::get('/dashboard/recent-bentos', [DashboardController::class, 'recentBentos']);
    Route::get('/dashboard/income-amount', [DashboardController::class, 'totalIncome']);
    Route::get('/dashboard/orders-by-country', [DashboardController::class, 'ordersByCountry']);
    Route::get('/dashboard/latest-customers', [DashboardController::class, 'latestCustomers']);
    Route::get('/dashboard/latest-orders', [DashboardController::class, 'latestOrders']);

    // Report routes
    Route::get('/report/orders', [ReportController::class, 'orders']);
    Route::get('/report/customers', [ReportController::class, 'customers']);
});

// Routes for actions that only authenticated users can perform
Route::middleware(['auth:sanctum'])->group(function () {
    Route::post('/bentos/{bento}/like', [BentoController::class, 'likeBento']);
    Route::post('/bentos/{bento}/dislike', [BentoController::class, 'dislikeBento']);
    Route::post('/bentos/{bento}/comment', [BentoController::class, 'commentOnBento']);
});

// Publicly accessible routes

Route::post('/login', [\App\Http\Controllers\Api\AuthController::class, 'login']);

Route::options('{any}', function () {
    return response()->json([], 200);
})->where('any', '.*');