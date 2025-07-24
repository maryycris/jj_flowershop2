<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Helpers\ShippingFeeHelper;

class ShippingFeeController extends Controller
{
    public function calculate(Request $request)
    {
        $request->validate([
            'origin' => 'required|string',
            'destination' => 'required|string',
        ]);

        $origin = $request->input('origin');
        $destination = $request->input('destination');
        $fee = ShippingFeeHelper::calculateShippingFee($origin, $destination);

        return response()->json(['fee' => round($fee)]);
    }
} 