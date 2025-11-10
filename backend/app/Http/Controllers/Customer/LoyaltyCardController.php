<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Services\LoyaltyCardMechanics;
use App\Models\LoyaltyCard;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoyaltyCardController extends Controller
{
    protected $loyaltyService;

    public function __construct(LoyaltyCardMechanics $loyaltyService)
    {
        $this->loyaltyService = $loyaltyService;
    }

    /**
     * Show customer's loyalty card
     */
    public function index()
    {
        $user = Auth::user();
        $card = $this->loyaltyService->getActiveCardForUser($user->id);
        $status = $this->loyaltyService->getCardStatus($card);
        $history = $this->loyaltyService->getCardHistory($card);

        return view('customer.loyalty.index', compact('card', 'status', 'history'));
    }

    /**
     * Show loyalty card mechanics/rules
     */
    public function mechanics()
    {
        return view('customer.loyalty.mechanics');
    }

    /**
     * Get loyalty card status for AJAX requests
     */
    public function status()
    {
        $user = Auth::user();
        $card = $this->loyaltyService->getActiveCardForUser($user->id);
        $status = $this->loyaltyService->getCardStatus($card);

        return response()->json($status);
    }

    /**
     * Check if customer can redeem discount
     */
    public function canRedeem()
    {
        $user = Auth::user();
        $card = $this->loyaltyService->getActiveCardForUser($user->id);
        
        return response()->json([
            'can_redeem' => $this->loyaltyService->canRedeem($card),
            'stamps_count' => $card->stamps_count,
            'required_stamps' => LoyaltyCardMechanics::REQUIRED_STAMPS
        ]);
    }
}
