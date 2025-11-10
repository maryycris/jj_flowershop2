<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LoyaltyCard;
use Illuminate\Http\Request;

class LoyaltyController extends Controller
{
    public function index()
    {
        $cards = LoyaltyCard::with('user')->orderByDesc('updated_at')->paginate(8);
        return view('admin.loyalty.index', compact('cards'));
    }

    public function adjust(Request $request, LoyaltyCard $card)
    {
        // Guard: only admins
        if (!auth()->user() || !method_exists(auth()->user(), 'hasRole') || !auth()->user()->hasRole('admin')) {
            abort(403);
        }

        $data = $request->validate([
            'delta' => ['required','integer','min:-5','max:5'],
            'reason' => ['nullable','string','max:255'],
        ]);

        $previousCount = $card->stamps_count;
        $newCount = max(0, min(5, $card->stamps_count + $data['delta']));
        
        if ($newCount === $previousCount) {
            return back()->with('warning', 'No change made - stamps already at limit.');
        }

        \DB::transaction(function () use ($card, $data, $previousCount, $newCount) {
            $card->stamps_count = $newCount;
            $card->save();

            \App\Models\LoyaltyAdjustment::create([
                'loyalty_card_id' => $card->id,
                'adjusted_by' => auth()->id(),
                'delta' => $data['delta'],
                'previous_count' => $previousCount,
                'new_count' => $newCount,
                'reason' => $data['reason'],
            ]);
        });

        return back()->with('success', "Stamps updated from {$previousCount} to {$newCount}.");
    }

    public function history(LoyaltyCard $card)
    {
        $card->load(['stamps.order', 'redemptions.order', 'adjustments.adjustedBy']);
        return view('admin.loyalty.history', compact('card'));
    }
}


