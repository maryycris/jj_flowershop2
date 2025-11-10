<?php

namespace App\Services;

use App\Models\LoyaltyCard;
use App\Models\LoyaltyStamp;
use App\Models\LoyaltyRedemption;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

/**
 * Loyalty Card Mechanics Service
 * 
 * Based on JJ Flowershop Loyalty Card Rules:
 * 1. Earn 1 stamp per bouquet purchase (excluding mini bouquets)
 * 2. Multiple bouquets in one transaction = 1 stamp only
 * 3. Collect 5 stamps to get 50% discount on bouquet in package
 * 4. Card valid until discount is redeemed
 * 5. Started March 24, 2024
 */
class LoyaltyCardMechanics
{
    const REQUIRED_STAMPS = 5;
    const DISCOUNT_PERCENTAGE = 50;
    const PROGRAM_START_DATE = '2024-03-24';

    /**
     * Get or create active loyalty card for user
     */
    public function getActiveCardForUser(int $userId): LoyaltyCard
    {
        return LoyaltyCard::firstOrCreate(
            ['user_id' => $userId, 'status' => 'active'],
            [
                'stamps_count' => 0,
                'created_at' => Carbon::now()
            ]
        );
    }

    /**
     * Check if order qualifies for a loyalty stamp
     * Rules:
     * - Must be paid/validated
     * - Must contain at least one bouquet (excluding mini bouquets)
     * - Only one stamp per order regardless of bouquet quantity
     */
    public function orderQualifiesForStamp(Order $order): bool
    {
        // Must be paid/validated
        if (!in_array($order->payment_status, ['paid', 'validated', 'paid_cod'])) {
            return false;
        }

        // Check if order contains qualifying bouquet products
        $order->load('products');
        foreach ($order->products as $product) {
            if ($this->isQualifyingBouquet($product)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if product is a qualifying bouquet for loyalty stamp
     */
    private function isQualifyingBouquet(Product $product): bool
    {
        $category = strtolower((string)($product->category ?? ''));
        $name = strtolower((string)($product->name ?? ''));

        // Must be bouquet category (accepts both "bouquet" and "bouquets")
        if ($category !== 'bouquet' && $category !== 'bouquets') {
            return false;
        }

        // Exclude mini bouquets
        if (str_contains($category, 'mini') || str_contains($name, 'mini')) {
            return false;
        }

        return true;
    }

    /**
     * Issue stamp for qualifying order
     */
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

        // Don't issue stamp if already at 5 stamps (waiting for redemption)
        if ($card->stamps_count >= self::REQUIRED_STAMPS) {
            return;
        }

        DB::transaction(function () use ($card, $order) {
            // Create stamp record
            LoyaltyStamp::create([
                'loyalty_card_id' => $card->id,
                'order_id' => $order->id,
                'earned_at' => Carbon::now(),
            ]);

            // Increment stamp count
            $card->increment('stamps_count');
            $card->last_earned_at = Carbon::now();
            $card->save();

            \Log::info('Loyalty stamp issued', [
                'user_id' => $order->user_id,
                'order_id' => $order->id,
                'stamps_count' => $card->stamps_count,
                'card_id' => $card->id
            ]);
        });
    }

    /**
     * Check if user can redeem loyalty discount
     */
    public function canRedeem(LoyaltyCard $card): bool
    {
        return $card->status === 'active' && $card->stamps_count >= self::REQUIRED_STAMPS;
    }

    /**
     * Calculate 50% discount for bouquet in package
     * Rules:
     * - Only applies to bouquet products (excluding mini bouquets)
     * - If multiple bouquets, applies to the most expensive one
     * - Only applies to bouquet, not the entire package
     */
    public function calculateDiscountForCart(array $cartItems): float
    {
        $eligibleBouquetPrice = 0.0;

        foreach ($cartItems as $item) {
            $product = $item->product ?? null;
            if (!$product) {
                continue;
            }

            // Check if this is a qualifying bouquet
            if ($this->isQualifyingBouquet($product)) {
                $bouquetPrice = (float)$product->price;
                $eligibleBouquetPrice = max($eligibleBouquetPrice, $bouquetPrice);
            }
        }

        // Apply 50% discount to the most expensive qualifying bouquet
        return $eligibleBouquetPrice > 0 ? round($eligibleBouquetPrice * (self::DISCOUNT_PERCENTAGE / 100), 2) : 0.0;
    }

    /**
     * Redeem loyalty discount
     * Rules:
     * - Reset stamps to 0 after redemption
     * - Keep card active for future accruals
     * - Record redemption for tracking
     */
    public function redeem(LoyaltyCard $card, Order $order, float $discountAmount): void
    {
        if ($discountAmount <= 0) {
            return;
        }

        if (!$this->canRedeem($card)) {
            throw new \Exception('Card is not eligible for redemption');
        }

        DB::transaction(function () use ($card, $order, $discountAmount) {
            // Record redemption
            LoyaltyRedemption::create([
                'loyalty_card_id' => $card->id,
                'order_id' => $order->id,
                'discount_amount' => $discountAmount,
                'redeemed_at' => Carbon::now(),
            ]);

            // Reset stamps to 0 but keep card active
            $card->stamps_count = 0;
            $card->save();

            \Log::info('Loyalty discount redeemed', [
                'user_id' => $order->user_id,
                'order_id' => $order->id,
                'discount_amount' => $discountAmount,
                'card_id' => $card->id
            ]);
        });
    }

    /**
     * Get loyalty card status for display
     */
    public function getCardStatus(LoyaltyCard $card): array
    {
        return [
            'stamps_count' => $card->stamps_count,
            'required_stamps' => self::REQUIRED_STAMPS,
            'can_redeem' => $this->canRedeem($card),
            'discount_percentage' => self::DISCOUNT_PERCENTAGE,
            'status' => $card->status,
            'last_earned_at' => $card->last_earned_at,
            'progress_percentage' => round(($card->stamps_count / self::REQUIRED_STAMPS) * 100, 1)
        ];
    }

    /**
     * Get loyalty card history
     */
    public function getCardHistory(LoyaltyCard $card): array
    {
        $card->load(['stamps.order', 'redemptions.order', 'adjustments.adjustedBy']);

        return [
            'stamps' => $card->stamps->map(function ($stamp) {
                return [
                    'id' => $stamp->id,
                    'order_id' => $stamp->order_id,
                    'earned_at' => $stamp->earned_at,
                    'order_total' => $stamp->order->total_price ?? 0
                ];
            }),
            'redemptions' => $card->redemptions->map(function ($redemption) {
                return [
                    'id' => $redemption->id,
                    'order_id' => $redemption->order_id,
                    'discount_amount' => $redemption->discount_amount,
                    'redeemed_at' => $redemption->redeemed_at
                ];
            }),
            'adjustments' => $card->adjustments->map(function ($adjustment) {
                return [
                    'id' => $adjustment->id,
                    'delta' => $adjustment->delta,
                    'previous_count' => $adjustment->previous_count,
                    'new_count' => $adjustment->new_count,
                    'reason' => $adjustment->reason,
                    'adjusted_by' => $adjustment->adjustedBy->name ?? 'Unknown',
                    'created_at' => $adjustment->created_at
                ];
            })
        ];
    }
}
