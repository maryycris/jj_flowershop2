<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;

/**
 * Shipping API Controller
 * 
 * Handles shipping fee calculation API endpoints.
 */
class ShippingApiController extends BaseApiController
{
    public function calculate(Request $request)
    {
        $controller = new \App\Http\Controllers\ShippingFeeController();
        return $controller->calculate($request);
    }
}

