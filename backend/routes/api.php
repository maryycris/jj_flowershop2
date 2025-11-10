<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\MapApiController;
use App\Http\Controllers\Api\ShippingApiController;

/*
|--------------------------------------------------------------------------
| API Routes (Backend)
|--------------------------------------------------------------------------
|
| All API endpoints return JSON responses.
| These are backend-only routes for frontend consumption.
|
*/

// Test endpoint
Route::get('/test', function() {
    return response()->json(['message' => 'API is working!', 'status' => 'success']);
});

// Authenticated user endpoint
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return response()->json([
        'success' => true,
        'data' => $request->user()
    ]);
});

// Shipping API
Route::post('/shipping/calculate', [ShippingApiController::class, 'calculate'])->name('api.shipping.calculate');

// Map API endpoints
Route::post('/map/geocode', [MapApiController::class, 'geocode'])->name('api.map.geocode');
Route::post('/map/route', [MapApiController::class, 'getRoute'])->name('api.map.route');
Route::post('/map/shipping-calculate', [MapApiController::class, 'calculateShippingWithDistance'])->name('api.map.shipping');

// Inventory API endpoints
Route::get('/inventory/items', [\App\Http\Controllers\Clerk\ClerkController::class, 'getInventoryItems'])->name('api.inventory.items');

