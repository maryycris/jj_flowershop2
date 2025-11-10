<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CartItem;
use App\Models\Product;
use App\Services\InventoryValidationService;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login')->withErrors(['You must be logged in to view your cart.']);
        }
        $cartItems = $user->cartItems()->with(['product', 'customBouquet'])->get();
        $subtotal = 0;
        foreach ($cartItems as $item) {
            if ($item->item_type === 'custom_bouquet') {
                $unitPrice = $item->customBouquet ? ($item->customBouquet->unit_price ?? ($item->customBouquet->total_price ?? 0)) : 0;
                $subtotal += $unitPrice * $item->quantity;
            } else {
                $subtotal += $item->product ? $item->product->price * $item->quantity : 0;
            }
        }
        return view('customer.cart.index', compact('cartItems', 'subtotal'));
    }

    public function addToCart(Request $request)
    {
        // Accept either a catalog_product_id (preferred from dashboard modal) or a product_id
        $request->validate([
            'quantity' => 'required|integer|min:1',
            'product_id' => 'nullable|integer|exists:products,id',
            'catalog_product_id' => 'nullable|integer|exists:catalog_products,id',
            'force_add' => 'nullable|boolean',
        ]);

        $quantity = (int) $request->input('quantity');
        $productId = $request->input('product_id');
        $catalogProductId = $request->input('catalog_product_id');
        $forceAdd = $request->input('force_add', false);
        $user = Auth::user();
        
        $inventoryService = new InventoryValidationService();
        $inventoryCheckService = new \App\Services\InventoryCheckService();

        // Inventory validation disabled - customers can add any product to cart
        // This allows customers to add products even if components are missing or stock is low
        // Inventory will be managed separately by admin/clerk

        // Resolve to a Product ID if only catalog product is provided.
        if (!$productId && $catalogProductId) {
            // Strategy: find an existing Product with same name/price/category as CatalogProduct, else create a shadow Product
            $catalog = \App\Models\CatalogProduct::find($catalogProductId);
            if ($catalog) {
                $existing = \App\Models\Product::where('name', $catalog->name)
                    ->where('price', $catalog->price)
                    ->where('category', $catalog->category)
                    ->first();
                if ($existing) {
                    $productId = $existing->id;
                } else {
                    $product = new \App\Models\Product([
                        'code' => null,
                        'name' => $catalog->name,
                        'description' => $catalog->description,
                        'price' => $catalog->price,
                        'stock' => 0,
                        'category' => $catalog->category,
                        'image' => $catalog->image,
                        'image2' => $catalog->image2,
                        'image3' => $catalog->image3,
                        'status' => true,
                        'is_approved' => true,
                    ]);
                    $product->save();
                    $productId = $product->id;
                }
            }
        }

        if (!$productId) {
            return response()->json(['message' => 'Invalid product.'], 422);
        }

        $cartItem = CartItem::where('user_id', $user->id)
                              ->where('product_id', $productId)
                              ->first();

        if ($cartItem) {
            // Update quantity if item already exists in cart
            $cartItem->quantity += $quantity;
            $cartItem->save();
        } else {
            // Add new item to cart
            CartItem::create([
                'user_id' => $user->id,
                'product_id' => $productId,
                'quantity' => $quantity,
            ]);
        }

        // For fetch requests expecting JSON, return JSON success; otherwise fallback to redirect
        if ($request->expectsJson()) {
            $message = $forceAdd ? 'Product added to cart (backorder)' : 'Product added to cart!';
            return response()->json(['success' => true, 'message' => $message]);
        }
        $message = $forceAdd ? 'Product added to cart (backorder)' : 'Product added to cart!';
        return redirect()->back()->with('success', $message);
    }

    public function updateQuantity(Request $request, CartItem $cartItem)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        if ($cartItem->user_id !== Auth::id()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $cartItem->quantity = $request->quantity;
        $cartItem->save();

        // Compute new total price depending on item type
        $newTotal = 0;
        if ($cartItem->item_type === 'custom_bouquet' && $cartItem->customBouquet) {
            $unitPrice = $cartItem->customBouquet->unit_price ?? ($cartItem->customBouquet->total_price ?? 0);
            $newTotal = $unitPrice * $cartItem->quantity;
        } else if ($cartItem->product) {
            $newTotal = $cartItem->quantity * $cartItem->product->price;
        }
        return response()->json(['success' => true, 'new_quantity' => $cartItem->quantity, 'new_total_price' => number_format($newTotal, 2)]);
    }

    public function removeItem(CartItem $cartItem)
    {
        if ($cartItem->user_id !== Auth::id()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $cartItem->delete();

        return response()->json(['success' => true, 'message' => 'Item removed from cart.']);
    }

    public function deleteAllItems()
    {
        Auth::user()->cartItems()->delete();
        return response()->json(['success' => true, 'message' => 'All items removed from cart.']);
    }
}
