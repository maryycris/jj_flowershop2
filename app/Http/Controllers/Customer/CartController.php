<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login')->withErrors(['You must be logged in to view your cart.']);
        }
        $cartItems = $user->cartItems()->with('product')->get();
        $subtotal = 0;
        foreach ($cartItems as $item) {
            $subtotal += $item->quantity * $item->product->price;
        }
        return view('customer.cart.index', compact('cartItems', 'subtotal'));
    }

    public function addToCart(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $productId = $request->input('product_id');
        $quantity = $request->input('quantity');
        $user = Auth::user();

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

        return redirect()->back()->with('success', 'Product added to cart!');
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

        return response()->json(['success' => true, 'new_quantity' => $cartItem->quantity, 'new_total_price' => number_format($cartItem->quantity * $cartItem->product->price, 2)]);
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
