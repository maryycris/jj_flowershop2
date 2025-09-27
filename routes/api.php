<?php

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/calculate-shipping-fee', [\App\Http\Controllers\ShippingFeeController::class, 'calculate']);

// Inventory API endpoints
Route::get('/inventory-items', [\App\Http\Controllers\Clerk\ClerkController::class, 'getInventoryItems']);

// Map and routing API endpoints
Route::post('/map/geocode', [\App\Http\Controllers\MapController::class, 'geocode']);
Route::post('/map/route', [\App\Http\Controllers\MapController::class, 'getRoute']);
Route::post('/map/shipping-calculate', [\App\Http\Controllers\MapController::class, 'calculateShippingWithDistance']); 