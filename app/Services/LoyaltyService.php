<?php

namespace App\Services;

use App\Models\LoyaltyCard;
use App\Models\LoyaltyStamp;
use App\Models\LoyaltyRedemption;
use App\Models\Order;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class LoyaltyService
{
    const REQUIRED_STAMPS = 5;

    public function getActiveCardForUser(int $userId): LoyaltyCard
    {
        return LoyaltyCard::firstOrCreate(
            ['user_id' => $userId, 'status' => 'active'],
            ['stamps_count' => 0]
        );
    }

    public function issueStampIfEligible(Order $order): void
    {
        if (!$this->orderQualifiesForStamp($order)) {
            return;
        }

        $card = $this->getActiveCardForUser($order->user_id);

        // Prevent duplicate stamp per order
        if (LoyaltyStamp::where('order_id', $order->id)->exists()) {
            return;
        }

        DB::transaction(function () use ($card, $order) {
            LoyaltyStamp::create([
                'loyalty_card_id' => $card->id,
                'order_id' => $order->id,
                'earned_at' => Carbon::now(),
            ]);

            // Check if this is the 5th order in the cycle
            if ($card->stamps_count >= 4) {
                // Reset to 0/5 for the next cycle
                $card->stamps_count = 0;
                \Log::info('Loyalty cycle completed - reset to 0/5', [
                    'user_id' => $order->user_id,
                    'order_id' => $order->id,
                    'previous_stamps' => 4
                ]);
            } else {
                // Normal increment
                $card->increment('stamps_count');
            }
            
            $card->last_earned_at = Carbon::now();
            $card->save();
        });
    }

    public function canRedeem(LoyaltyCard $card): bool
    {
        return $card->status === 'active' && $card->stamps_count >= self::REQUIRED_STAMPS;
    }

    public function calculateDiscountForCart(array $cartItems): float
    {
        // Choose the most expensive eligible bouquet item and apply 50%
        $eligibleMax = 0.0;
        foreach ($cartItems as $item) {
            $product = $item->product ?? null;
            if (!$product) { continue; }
            // Eligible if category is Bouquet and not Mini Bouquet
            $category = strtolower((string)($product->category ?? ''));
            if ($category !== 'bouquet' || str_contains($category, 'mini')) {
                continue;
            }
            $linePrice = (float)$product->price; // 50% applies to bouquet price only
            $eligibleMax = max($eligibleMax, $linePrice);
        }
        return $eligibleMax > 0 ? round($eligibleMax * 0.5, 2) : 0.0;
    }

    public function redeem(LoyaltyCard $card, Order $order, float $discountAmount): void
    {
        if ($discountAmount <= 0) { return; }

        DB::transaction(function () use ($card, $order, $discountAmount) {
            LoyaltyRedemption::create([
                'loyalty_card_id' => $card->id,
                'order_id' => $order->id,
                'discount_amount' => $discountAmount,
                'redeemed_at' => Carbon::now(),
            ]);

            // Reset stamps to 0 but keep card active for future accruals
            $card->stamps_count = 0;
            $card->save();
        });
    }

    private function orderQualifiesForStamp(Order $order): bool
    {
        // Must be paid/validated
        if (!in_array($order->payment_status, ['paid', 'validated', 'paid_cod'])) {
            return false;
        }
        // Only 1 stamp per order; any order qualifies for loyalty stamp
        // (Changed from bouquet-only to all orders to encourage customer loyalty)
        return true;
    }
}


