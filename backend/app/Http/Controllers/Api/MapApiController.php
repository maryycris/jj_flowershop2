<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;

/**
 * Map API Controller
 * 
 * Handles all map-related API endpoints.
 * Delegates to MapController for actual implementation.
 */
class MapApiController extends BaseApiController
{
    public function geocode(Request $request)
    {
        $controller = new \App\Http\Controllers\MapController();
        return $controller->geocode($request);
    }

    public function getRoute(Request $request)
    {
        $controller = new \App\Http\Controllers\MapController();
        return $controller->getRoute($request);
    }

    public function calculateShippingWithDistance(Request $request)
    {
        $controller = new \App\Http\Controllers\MapController();
        return $controller->calculateShippingWithDistance($request);
    }
}

