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

            // Normal increment - don't auto-reset at 5 stamps
            // Reset only happens when discount is redeemed
            $previousCount = $card->stamps_count;
            $card->increment('stamps_count');
            $card->refresh();
            
            \Log::info('Loyalty stamp earned', [
                'user_id' => $order->user_id,
                'order_id' => $order->id,
                'new_stamps_count' => $card->stamps_count
            ]);
            
            $card->last_earned_at = Carbon::now();
            $card->save();
            
            // Notify admin if stamps reached 5
            if ($card->stamps_count >= self::REQUIRED_STAMPS && $previousCount < self::REQUIRED_STAMPS) {
                try {
                    $user = $card->user;
                    $adminUsers = \App\Models\User::where('role', 'admin')->get();
                    foreach ($adminUsers as $admin) {
                        $admin->notify(new \App\Notifications\LoyaltyStampReachNotification($card, $user));
                    }
                } catch (\Throwable $e) {
                    \Log::error("Failed to send loyalty stamp reach notification for card {$card->id}: {$e->getMessage()}");
                }
            }
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
            // Check for custom bouquets - they always qualify
            if (($item->item_type ?? null) === 'custom_bouquet' && isset($item->customBouquet)) {
                $customBouquetPrice = (float)($item->customBouquet->unit_price ?? $item->customBouquet->total_price ?? $item->customBouquet->price ?? 0);
                $eligibleMax = max($eligibleMax, $customBouquetPrice);
                continue;
            }
            
            // Check for regular bouquet products
            $product = $item->product ?? null;
            if (!$product) { continue; }
            // Eligible if category is Bouquet/Bouquets and not Mini Bouquet
            $category = strtolower((string)($product->category ?? ''));
            if (($category !== 'bouquet' && $category !== 'bouquets') || str_contains($category, 'mini')) {
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
        
        // Check if order contains bouquet products (excluding mini bouquets)
        $order->load('products');
        foreach ($order->products as $product) {
            $category = strtolower((string)($product->category ?? ''));
            
            // Must be bouquet category (accepts both "bouquet" and "bouquets")
            if ($category !== 'bouquet' && $category !== 'bouquets') {
                continue;
            }
            
            // Exclude mini bouquets
            if (str_contains($category, 'mini') || str_contains(strtolower($product->name ?? ''), 'mini')) {
                continue;
            }
            
            // If we find at least one qualifying bouquet, this order earns a stamp
            return true;
        }
        
        // Also check for custom bouquets - they qualify for stamps too
        $order->load('customBouquets');
        if ($order->customBouquets->count() > 0) {
            // Custom bouquets always qualify (they are bouquets by definition)
            return true;
        }
        
        return false;
    }
}


