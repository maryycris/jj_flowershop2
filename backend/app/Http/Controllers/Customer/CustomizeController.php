<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Traits\CustomizeFilterTrait;
use App\Models\Product;
use App\Models\CustomBouquet;
use App\Models\CartItem;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CustomizeController extends Controller
{
    use CustomizeFilterTrait;
    public function index()
    {
        $items = $this->getCustomizeItems();
        $categories = $this->getCustomizeCategories();
        $occasions = \App\Models\BouquetOccasion::where('is_active', true)->orderBy('name')->get();
        $assemblingFee = Setting::get('assembling_fee', 150);
        
        return view('products.bouquet-customize', compact('items','categories','occasions', 'assemblingFee'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'bouquet_type' => 'required|in:regular,money',
            'quantity' => 'required|integer|min:1|max:10',
            'wrapper' => 'nullable|string',
            'focal_flower_1' => 'nullable|string',
            'focal_flower_2' => 'nullable|string',
            'focal_flower_3' => 'nullable|string',
            'greenery' => 'nullable|string',
            'filler' => 'nullable|string',
            'ribbon' => 'nullable|string',
            'money_amount' => 'nullable|numeric|min:0',
        ]);

        try {
            DB::beginTransaction();

            // Server-side restriction: require wrapper + ribbon + greenery AND at least one flower (fresh/artificial)
            if ($request->bouquet_type === 'regular') {
                $hasBase = $request->filled('wrapper') && $request->filled('ribbon') && $request->filled('greenery');
                $hasAnyFlower = $request->filled('focal_flower_1') || $request->filled('focal_flower_2') || $request->filled('focal_flower_3') || $request->filled('filler');
                if (!$hasBase || !$hasAnyFlower) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Please select a wrapper, ribbon, greenery, and at least one flower (fresh or artificial).'
                    ], 422);
                }
            }

            // Calculate total price
            $totalPrice = $this->calculateTotalPrice($request);

            // Create custom bouquet
            $customBouquet = CustomBouquet::create([
                'user_id' => Auth::id(),
                'bouquet_type' => $request->bouquet_type,
                'wrapper' => $request->wrapper,
                'focal_flower_1' => $request->focal_flower_1,
                'focal_flower_2' => $request->focal_flower_2,
                'focal_flower_3' => $request->focal_flower_3,
                'greenery' => $request->greenery,
                'filler' => $request->filler,
                'ribbon' => $request->ribbon,
                'money_amount' => $request->money_amount,
                'quantity' => $request->quantity,
                'total_price' => $totalPrice,
                'customization_data' => $request->all(),
                'is_active' => true
            ]);

            DB::commit();

            // Generate preview image immediately (non-blocking, won't fail if it errors)
            if ($customBouquet->bouquet_type === 'regular') {
                try {
                    if (extension_loaded('gd')) {
                        $imageService = new \App\Services\CustomBouquetImageService();
                        $previewPath = $imageService->generateCompositeImage($customBouquet);
                        if ($previewPath && file_exists(storage_path('app/public/' . $previewPath))) {
                            // Update directly in database to avoid triggering accessor recursion
                            DB::table('custom_bouquets')
                                ->where('id', $customBouquet->id)
                                ->update(['preview_image' => $previewPath]);
                            
                            // Refresh the model to include the new preview_image
                            $customBouquet->refresh();
                        }
                    }
                } catch (\Exception $e) {
                    // Silently fail - preview will be generated on-demand via accessor
                    \Log::info('Preview image generation failed (non-critical): ' . $e->getMessage());
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Custom bouquet created successfully!',
                'bouquet_id' => $customBouquet->id,
                'total_price' => $customBouquet->formatted_price
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error creating custom bouquet: ' . $e->getMessage()
            ], 500);
        }
    }

    private function calculateTotalPrice(Request $request)
    {
        $assemblyFee = Setting::get('assembling_fee', 150);
        $total = $assemblyFee;

        // Add money amount for money bouquets
        if ($request->bouquet_type === 'money' && $request->money_amount) {
            $total += $request->money_amount;
        }

        // Add component prices for regular bouquets
        if ($request->bouquet_type === 'regular') {
            $items = $this->getCustomizeItemsForPricing();

            // Quantities from the UI (default to 1)
            $freshQty = max(1, (int) $request->input('fresh_flower_qty', 1));
            $artificialQty = max(1, (int) $request->input('artificial_flower_qty', 1));

            $components = [
                'wrapper' => $request->wrapper,
                'focal_flower_1' => $request->focal_flower_1,
                'focal_flower_2' => $request->focal_flower_2,
                'focal_flower_3' => $request->focal_flower_3,
                'greenery' => $request->greenery,
                'filler' => $request->filler,
                'ribbon' => $request->ribbon,
            ];

            foreach ($components as $key => $component) {
                if ($component) {
                    // Try to find by exact name match (case-insensitive)
                    $componentKey = strtolower(trim($component));
                    
                    // Check if items is a collection keyed by name
                    if ($items->has($componentKey)) {
                        $item = $items[$componentKey];
                        $price = is_object($item) ? ($item->price ?? 0) : ($item['price'] ?? 0);
                    } else {
                        // Fallback: search in collection
                        $item = $items->first(function ($item) use ($componentKey) {
                            $itemName = is_object($item) ? ($item->name ?? '') : ($item['name'] ?? '');
                            return strtolower(trim($itemName)) === $componentKey;
                        });
                        $price = $item ? (is_object($item) ? ($item->price ?? 0) : ($item['price'] ?? 0)) : 0;
                    }

                    // Apply per-component quantity rules
                    if ($key === 'focal_flower_1') {
                        $total += $price * $freshQty;
                    } elseif ($key === 'filler') {
                        $total += $price * $artificialQty;
                    } else {
                        $total += $price;
                    }
                }
            }
        }

        return $total * $request->quantity;
    }

    public function addToCart(Request $request)
    {
        $request->validate([
            'bouquet_type' => 'required|in:regular,money',
            'quantity' => 'required|integer|min:1|max:10',
            'wrapper' => 'nullable|string',
            'focal_flower_1' => 'nullable|string',
            'focal_flower_2' => 'nullable|string',
            'focal_flower_3' => 'nullable|string',
            'greenery' => 'nullable|string',
            'filler' => 'nullable|string',
            'ribbon' => 'nullable|string',
            'money_amount' => 'nullable|numeric|min:0',
        ]);

        try {
            DB::beginTransaction();

            if ($request->bouquet_type === 'regular') {
                $hasBase = $request->filled('wrapper') && $request->filled('ribbon') && $request->filled('greenery');
                $hasAnyFlower = $request->filled('focal_flower_1') || $request->filled('focal_flower_2') || $request->filled('focal_flower_3') || $request->filled('filler');
                if (!$hasBase || !$hasAnyFlower) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Please select a wrapper, ribbon, greenery, and at least one flower (fresh or artificial).'
                    ], 422);
                }
            }

            // Calculate total price
            $totalPrice = $this->calculateTotalPrice($request);

            // Create custom bouquet
            $customBouquet = CustomBouquet::create([
                'user_id' => Auth::id(),
                'bouquet_type' => $request->bouquet_type,
                'wrapper' => $request->wrapper,
                'focal_flower_1' => $request->focal_flower_1,
                'focal_flower_2' => $request->focal_flower_2,
                'focal_flower_3' => $request->focal_flower_3,
                'greenery' => $request->greenery,
                'filler' => $request->filler,
                'ribbon' => $request->ribbon,
                'money_amount' => $request->money_amount,
                'quantity' => $request->quantity,
                'total_price' => $totalPrice,
                'customization_data' => $request->all(),
                'is_active' => true
            ]);

            // Add to cart
            CartItem::create([
                'user_id' => Auth::id(),
                'custom_bouquet_id' => $customBouquet->id,
                'quantity' => $request->quantity,
                'item_type' => 'custom_bouquet'
            ]);

            DB::commit();

            // Generate preview image immediately (non-blocking, won't fail if it errors)
            if ($customBouquet->bouquet_type === 'regular') {
                try {
                    if (extension_loaded('gd')) {
                        $imageService = new \App\Services\CustomBouquetImageService();
                        $previewPath = $imageService->generateCompositeImage($customBouquet);
                        if ($previewPath && file_exists(storage_path('app/public/' . $previewPath))) {
                            // Update directly in database to avoid triggering accessor recursion
                            DB::table('custom_bouquets')
                                ->where('id', $customBouquet->id)
                                ->update(['preview_image' => $previewPath]);
                            
                            // Refresh the model to include the new preview_image
                            $customBouquet->refresh();
                        }
                    }
                } catch (\Exception $e) {
                    // Silently fail - preview will be generated on-demand via accessor
                    \Log::info('Preview image generation failed (non-critical): ' . $e->getMessage());
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Custom bouquet added to cart successfully!',
                'redirect_url' => route('customer.cart.index')
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error adding custom bouquet to cart: ' . $e->getMessage()
            ], 500);
        }
    }

    public function buyNow(Request $request)
    {
        $request->validate([
            'bouquet_type' => 'required|in:regular,money',
            'quantity' => 'required|integer|min:1|max:10',
            'wrapper' => 'nullable|string',
            'focal_flower_1' => 'nullable|string',
            'focal_flower_2' => 'nullable|string',
            'focal_flower_3' => 'nullable|string',
            'greenery' => 'nullable|string',
            'filler' => 'nullable|string',
            'ribbon' => 'nullable|string',
            'money_amount' => 'nullable|numeric|min:0',
        ]);

        try {
            DB::beginTransaction();

            if ($request->bouquet_type === 'regular') {
                $hasBase = $request->filled('wrapper') && $request->filled('ribbon') && $request->filled('greenery');
                $hasAnyFlower = $request->filled('focal_flower_1') || $request->filled('focal_flower_2') || $request->filled('focal_flower_3') || $request->filled('filler');
                if (!$hasBase || !$hasAnyFlower) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Please select a wrapper, ribbon, greenery, and at least one flower (fresh or artificial).'
                    ], 422);
                }
            }

            // Calculate total price
            $totalPrice = $this->calculateTotalPrice($request);

            // Create custom bouquet
            $customBouquet = CustomBouquet::create([
                'user_id' => Auth::id(),
                'bouquet_type' => $request->bouquet_type,
                'wrapper' => $request->wrapper,
                'focal_flower_1' => $request->focal_flower_1,
                'focal_flower_2' => $request->focal_flower_2,
                'focal_flower_3' => $request->focal_flower_3,
                'greenery' => $request->greenery,
                'filler' => $request->filler,
                'ribbon' => $request->ribbon,
                'money_amount' => $request->money_amount,
                'quantity' => $request->quantity,
                'total_price' => $totalPrice,
                'customization_data' => $request->all(),
                'is_active' => true
            ]);

            DB::commit();

            // Redirect to checkout with custom bouquet ID
            return response()->json([
                'success' => true,
                'message' => 'Redirecting to checkout...',
                'redirect_url' => route('customer.checkout.index', ['custom_bouquet_id' => $customBouquet->id, 'quantity' => $request->quantity])
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error processing buy now: ' . $e->getMessage()
            ], 500);
        }
    }
}
