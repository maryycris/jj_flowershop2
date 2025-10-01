<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Traits\CustomizeFilterTrait;
use App\Models\Product;
use App\Models\CustomBouquet;
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
        
        return view('products.bouquet-customize', compact('items','categories','occasions'));
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
        $assemblyFee = 150;
        $total = $assemblyFee;

        // Add money amount for money bouquets
        if ($request->bouquet_type === 'money' && $request->money_amount) {
            $total += $request->money_amount;
        }

        // Add component prices for regular bouquets
        if ($request->bouquet_type === 'regular') {
            $items = $this->getCustomizeItemsForPricing();

            $components = [
                'wrapper' => $request->wrapper,
                'focal_flower_1' => $request->focal_flower_1,
                'focal_flower_2' => $request->focal_flower_2,
                'focal_flower_3' => $request->focal_flower_3,
                'greenery' => $request->greenery,
                'filler' => $request->filler,
                'ribbon' => $request->ribbon,
            ];

            foreach ($components as $component) {
                if ($component && isset($items[$component])) {
                    $total += $items[$component]->price;
                }
            }
        }

        return $total * $request->quantity;
    }

    public function addToCart(Request $request)
    {
        $result = $this->store($request);
        
        if ($result->getData()->success) {
            // Here you would typically add the custom bouquet to the cart
            // For now, we'll just return success
            return response()->json([
                'success' => true,
                'message' => 'Custom bouquet added to cart successfully!',
                'bouquet_id' => $result->getData()->bouquet_id
            ]);
        }

        return $result;
    }
}
