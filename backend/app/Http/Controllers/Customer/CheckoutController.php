<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\CartItem;
use App\Models\User;
use App\Models\Order;
use App\Models\Delivery;
use App\Models\Address;
use App\Services\PayMongoService;

class CheckoutController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        // Check if this is a "Buy now" flow (product_id, catalog_product_id, or custom_bouquet_id provided)
        $productId = $request->input('product_id');
        $catalogProductId = $request->input('catalog_product_id');
        $customBouquetId = $request->input('custom_bouquet_id');
        $quantity = $request->input('quantity', 1);

        if ($productId) {
            // "Buy now" flow with regular product ID
            $product = \App\Models\Product::find($productId);
            if (!$product) {
                return redirect()->route('customer.products.index')->with('error', 'Product not found.');
            }
        } elseif ($catalogProductId) {
            // "Buy now" flow with catalog product ID - convert to regular product
            $catalog = \App\Models\CatalogProduct::find($catalogProductId);
            if (!$catalog) {
                return redirect()->route('customer.dashboard')->with('error', 'Product not found.');
            }

            // Find existing product or create new one
            $product = \App\Models\Product::where('name', $catalog->name)
                ->where('price', $catalog->price)
                ->where('category', $catalog->category)
                ->first();

            if (!$product) {
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
            }
            $productId = $product->id;
        } elseif ($customBouquetId) {
            // "Buy now" flow with custom bouquet ID
            $customBouquet = \App\Models\CustomBouquet::find($customBouquetId);
            if (!$customBouquet) {
                return redirect()->route('customer.products.bouquet-customize')->with('error', 'Custom bouquet not found.');
            }
        }

        if ($productId) {

            // Create a temporary cart item object for the checkout process
            $tempCartItem = new \stdClass();
            $tempCartItem->id = 'temp_' . $productId;
            $tempCartItem->product_id = $productId;
            $tempCartItem->item_type = 'product';
            $tempCartItem->quantity = $quantity;
            $tempCartItem->product = $product;
            $tempCartItem->customBouquet = null;

            $cartItems = collect([$tempCartItem]);
        } elseif ($customBouquetId) {
            // Create a temporary cart item object for custom bouquet checkout
            $tempCartItem = new \stdClass();
            $tempCartItem->id = 'temp_custom_' . $customBouquetId;
            $tempCartItem->custom_bouquet_id = $customBouquetId;
            $tempCartItem->item_type = 'custom_bouquet';
            $tempCartItem->quantity = $quantity;
            $tempCartItem->customBouquet = $customBouquet;
            $tempCartItem->product = null;

            $cartItems = collect([$tempCartItem]);
        } else {
            // Regular cart flow
            $selectedItemIds = $request->input('selected_items', []);

            if (!empty($selectedItemIds)) {
                $cartItems = $user->cartItems()->with('product')->whereIn('id', $selectedItemIds)->get();
            } else {
                $cartItems = $user->cartItems()->with('product')->get();
            }

        if ($cartItems->isEmpty()) {
            return redirect()->route('customer.cart.index')->with('error', 'Your cart is empty.');
            }
        }

        $subtotal = 0;
        foreach ($cartItems as $item) {
            $unitPrice = 0;
            if (($item->item_type ?? null) === 'custom_bouquet' && isset($item->customBouquet)) {
                $unitPrice = $item->customBouquet->unit_price ?? ($item->customBouquet->total_price ?? $item->customBouquet->price ?? 0);
            } else if (isset($item->product) && isset($item->product->price)) {
                $unitPrice = $item->product->price;
            }
            $subtotal += ($item->quantity ?? 1) * $unitPrice;
        }

        // Get user's addresses
        $addresses = $user->addresses()->get();
        // Use default address (or first) for initial calculation
        $deliveryAddress = $addresses->where('is_default', true)->first() ?? $addresses->first();

        // Get user's loyalty card
        $loyaltyCard = \App\Models\LoyaltyCard::where('user_id', $user->id)
            ->where('status', 'active')
            ->first();

        // Calculate loyalty discount if applicable
        $loyaltyService = new \App\Services\LoyaltyService();
        $loyaltyDiscount = 0;
        if ($loyaltyCard && $loyaltyService->canRedeem($loyaltyCard)) {
            $loyaltyDiscount = $loyaltyService->calculateDiscountForCart($cartItems);
        }

        \Log::info('Checkout address loading', [
            'user_id' => $user->id,
            'addresses_count' => $addresses->count(),
            'default_address' => $deliveryAddress ? [
                'id' => $deliveryAddress->id,
                'is_default' => $deliveryAddress->is_default,
                'street_address' => $deliveryAddress->street_address,
                'barangay' => $deliveryAddress->barangay,
                'city' => $deliveryAddress->city
            ] : null,
            'loyalty_card' => $loyaltyCard ? [
                'stamps_count' => $loyaltyCard->stamps_count,
                'can_redeem' => $loyaltyService->canRedeem($loyaltyCard),
                'discount_amount' => $loyaltyDiscount
            ] : null
        ]);

        // Use passed-in shipping fee if available; otherwise, calculate
        \Log::info('PaymentMethod params', ['query' => $request->query()]);
        $shippingFee = null;
        $shippingFeeParam = $request->query('shipping_fee', null);
        if ($shippingFeeParam !== null) {
            $raw = (string) $shippingFeeParam;
            $sanitized = is_numeric($raw) ? $raw : preg_replace('/[^0-9.]/', '', $raw);
            if ($sanitized !== '' && is_numeric($sanitized)) {
                $shippingFee = (float) $sanitized;
            }
        }
        if ($shippingFee === null) {
            // Default shipping fee to 0 if no address is set
            $shippingFee = 0;
            if ($deliveryAddress) {
                $originAddress = 'Cordova, Cebu'; // Shop location
                $destinationAddress = $deliveryAddress->street_address . ', ' .
                                    ($deliveryAddress->barangay ?? '') . ', ' .
                                    ($deliveryAddress->municipality ?? '') . ', ' .
                                    ($deliveryAddress->city ?? '') . ', ' .
                                    ($deliveryAddress->region ?? 'Region VII');

                $shippingFee = \App\Helpers\ShippingFeeHelper::calculateShippingFee($originAddress, $destinationAddress);
            }
        }
        \Log::info('PaymentMethod resolved shipping fee', ['shipping_fee' => $shippingFee]);

        // Calculate store credit balance for the customer
        $storeCreditBalance = Order::where('user_id', $user->id)
            ->where('refund_method', 'store_credit')
            ->whereNotNull('refund_amount')
            ->whereNotNull('refund_processed_at')
            ->sum('refund_amount');

        return view('customer.checkout.index', compact('cartItems', 'subtotal', 'deliveryAddress', 'shippingFee', 'addresses', 'loyaltyCard', 'loyaltyDiscount', 'storeCreditBalance'));
    }

    public function paymentMethod(Request $request)
    {
        $user = Auth::user();

        // Store recipient information in session for later use
        $checkoutData = [
            'recipient_type' => $request->input('recipient_type', 'someone'),
            'delivery_address' => $request->input('delivery_address', ''),
            'recipient_name' => $request->input('recipient_name', ''),
            'recipient_phone' => $request->input('recipient_phone', ''),
            'recipient_relationship' => $request->input('recipient_relationship', ''),
            'recipient_instructions' => $request->input('recipient_instructions', ''),
            'delivery_message' => $request->input('delivery_message', ''),
            'delivery_date' => $request->input('delivery_date', ''),
            'delivery_time' => $request->input('delivery_time', ''),
            'promo_code' => $request->input('promo_code', ''),
            'use_store_credit' => $request->has('use_store_credit'),
            'store_credit_amount' => $request->input('store_credit_amount', 0),
        ];

        // Validate phone number requirement based on recipient type
        $recipientType = $request->input('recipient_type', 'someone');
        $phoneNumber = $request->input('recipient_phone', '');

        // If customer will receive the order, phone number is required
        if ($recipientType === 'self' && empty($phoneNumber)) {
            return back()->withErrors([
                'recipient_phone' => 'Phone number is required when you will receive the order. The rider needs to contact you for delivery coordination.'
            ])->withInput();
        }

        // If someone else will receive, phone number is also required
        if ($recipientType === 'someone' && empty($phoneNumber)) {
            return back()->withErrors([
                'recipient_phone' => 'Recipient phone number is required for delivery coordination.'
            ])->withInput();
        }

        // Store in session
        session(['checkout_data' => $checkoutData]);

        \Log::info('Checkout data stored in session', $checkoutData);

        // Check if this is a "Buy now" flow (product_id and quantity provided)
        $productId = $request->input('product_id');
        $catalogProductId = $request->input('catalog_product_id');
        $customBouquetId = $request->input('custom_bouquet_id');
        $quantity = $request->input('quantity', 1);

        if ($productId) {
            // "Buy now" flow with regular product ID
            $product = \App\Models\Product::find($productId);
            if (!$product) {
                return redirect()->route('customer.products.index')->with('error', 'Product not found.');
            }
        } elseif ($catalogProductId) {
            // "Buy now" flow with catalog product ID - convert to regular product
            $catalog = \App\Models\CatalogProduct::find($catalogProductId);
            if (!$catalog) {
                return redirect()->route('customer.dashboard')->with('error', 'Product not found.');
            }

            // Find existing product or create new one
            $product = \App\Models\Product::where('name', $catalog->name)
                ->where('price', $catalog->price)
                ->where('category', $catalog->category)
                ->first();

            if (!$product) {
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
            }
            $productId = $product->id;
        } elseif ($customBouquetId) {
            // "Buy now" flow with custom bouquet ID
            $customBouquet = \App\Models\CustomBouquet::find($customBouquetId);
            if (!$customBouquet) {
                return redirect()->route('customer.products.bouquet-customize')->with('error', 'Custom bouquet not found.');
            }
        }

        if ($productId) {

            // Create a temporary cart item object for the checkout process
            $tempCartItem = new \stdClass();
            $tempCartItem->id = 'temp_' . $productId;
            $tempCartItem->product_id = $productId;
            $tempCartItem->item_type = 'product';
            $tempCartItem->quantity = $quantity;
            $tempCartItem->product = $product;
            $tempCartItem->customBouquet = null;

            $cartItems = collect([$tempCartItem]);
        } elseif ($customBouquetId) {
            // Create a temporary cart item object for custom bouquet checkout
            $tempCartItem = new \stdClass();
            $tempCartItem->id = 'temp_custom_' . $customBouquetId;
            $tempCartItem->custom_bouquet_id = $customBouquetId;
            $tempCartItem->item_type = 'custom_bouquet';
            $tempCartItem->quantity = $quantity;
            $tempCartItem->customBouquet = $customBouquet;
            $tempCartItem->product = null;

            $cartItems = collect([$tempCartItem]);
        } else {
            // Regular cart flow
            $selectedItemIds = $request->input('selected_items', []);

        if (!empty($selectedItemIds)) {
            $cartItems = $user->cartItems()->with('product')->whereIn('id', $selectedItemIds)->get();
        } else {
        $cartItems = $user->cartItems()->with('product')->get();
        }

        if ($cartItems->isEmpty()) {
            return redirect()->route('customer.cart.index')->with('error', 'Your cart is empty.');
            }
        }

        $subtotal = 0;
        foreach ($cartItems as $item) {
            $unitPrice = 0;
            if (($item->item_type ?? null) === 'custom_bouquet' && isset($item->customBouquet)) {
                $unitPrice = $item->customBouquet->unit_price ?? ($item->customBouquet->total_price ?? $item->customBouquet->price ?? 0);
            } else if (isset($item->product) && isset($item->product->price)) {
                $unitPrice = $item->product->price;
            }
            $subtotal += ($item->quantity ?? 1) * $unitPrice;
        }

        // Get user's default address or first address
        $deliveryAddress = $user->addresses()->where('is_default', true)->first()
                          ?? $user->addresses()->first();

        // Use shipping fee from URL parameter if available, otherwise calculate
        $shippingFee = $request->query('shipping_fee');
        \Log::info('Payment method shipping fee check', ['url_shipping_fee' => $shippingFee, 'type' => gettype($shippingFee)]);
        if (!$shippingFee || !is_numeric($shippingFee)) {
            \Log::info('Calculating shipping fee - no valid fee provided');
            // Calculate shipping fee using the updated helper
            $shippingFee = 30; // Default for Cordova
            if ($deliveryAddress) {
                $originAddress = 'Cordova, Cebu'; // Shop location
                $destinationAddress = $deliveryAddress->street_address . ', ' .
                                    ($deliveryAddress->barangay ?? '') . ', ' .
                                    ($deliveryAddress->municipality ?? '') . ', ' .
                                    ($deliveryAddress->city ?? '') . ', ' .
                                    ($deliveryAddress->region ?? 'Region VII');

                $shippingFee = \App\Helpers\ShippingFeeHelper::calculateShippingFee($originAddress, $destinationAddress);
            }
        } else {
            \Log::info('Using provided shipping fee', ['shipping_fee' => $shippingFee]);
            $shippingFee = (float) $shippingFee;
        }

        \Log::info('Shipping fee determined', ['final_shipping_fee' => $shippingFee]);

        // Load loyalty card data
        $loyaltyCard = \App\Models\LoyaltyCard::where('user_id', $user->id)->first();
        \Log::info('Loyalty card loaded', ['loyalty_card' => $loyaltyCard ? 'found' : 'not found']);

        // Check if customer is eligible for automatic discount (4/5 stamps = 5th order)
        $loyaltyDiscount = 0;
        $discountedItem = null;
        \Log::info('Starting loyalty discount calculation', ['loyalty_card_stamps' => $loyaltyCard ? $loyaltyCard->stamps_count : 'no card']);

        if ($loyaltyCard && $loyaltyCard->stamps_count >= 4) {
            // Find the most expensive item in the cart
            $mostExpensiveItem = $cartItems->sortByDesc(function($item) {
                $unitPrice = 0;
                if (($item->item_type ?? null) === 'custom_bouquet' && isset($item->customBouquet)) {
                    $unitPrice = $item->customBouquet->unit_price ?? ($item->customBouquet->total_price ?? $item->customBouquet->price ?? 0);
                } else if (isset($item->product) && isset($item->product->price)) {
                    $unitPrice = $item->product->price;
                }
                return $unitPrice * ($item->quantity ?? 1);
            })->first();

            if ($mostExpensiveItem) {
                // Calculate 50% discount on the most expensive item
                $unitPrice = 0;
                if (($mostExpensiveItem->item_type ?? null) === 'custom_bouquet' && isset($mostExpensiveItem->customBouquet)) {
                    $unitPrice = $mostExpensiveItem->customBouquet->unit_price ?? ($mostExpensiveItem->customBouquet->total_price ?? $mostExpensiveItem->customBouquet->price ?? 0);
                } else if (isset($mostExpensiveItem->product) && isset($mostExpensiveItem->product->price)) {
                    $unitPrice = $mostExpensiveItem->product->price;
                }
                $loyaltyDiscount = ($unitPrice * ($mostExpensiveItem->quantity ?? 1)) * 0.5;
                $discountedItem = $mostExpensiveItem;
            }
        }

        \Log::info('Loyalty discount calculation completed', ['loyalty_discount' => $loyaltyDiscount]);

        // Calculate store credit amount and validate
        $useStoreCredit = $request->has('use_store_credit');
        $storeCreditAmount = 0;
        $storeCreditBalance = 0;
        \Log::info('Starting store credit calculation', ['use_store_credit' => $useStoreCredit]);
        
        if ($useStoreCredit) {
            $storeCreditAmount = floatval($request->input('store_credit_amount', 0));
            \Log::info('Store credit amount from request', ['store_credit_amount' => $storeCreditAmount]);
            
            try {
                $storeCreditBalance = Order::where('user_id', $user->id)
                    ->where('refund_method', 'store_credit')
                    ->whereNotNull('refund_amount')
                    ->whereNotNull('refund_processed_at')
                    ->sum('refund_amount');
                \Log::info('Store credit balance calculated', ['store_credit_balance' => $storeCreditBalance]);
            } catch (\Exception $e) {
                \Log::error('Error calculating store credit balance', ['error' => $e->getMessage()]);
                $storeCreditBalance = 0;
            }
                
            // Calculate total amount needed
            $totalNeeded = $subtotal + $shippingFee - $loyaltyDiscount;
            \Log::info('Total amount calculation', ['subtotal' => $subtotal, 'shipping_fee' => $shippingFee, 'loyalty_discount' => $loyaltyDiscount, 'total_needed' => $totalNeeded]);
            
            // Validate store credit amount
            if ($storeCreditAmount > $storeCreditBalance) {
                return back()->withErrors([
                    'store_credit_amount' => 'Store credit amount cannot exceed your available balance of ₱' . number_format($storeCreditBalance, 2)
                ])->withInput();
            }
            
            if ($storeCreditAmount > $totalNeeded) {
                \Log::info('Store credit amount exceeds total needed', ['store_credit_amount' => $storeCreditAmount, 'total_needed' => $totalNeeded]);
                return back()->withErrors([
                    'store_credit_amount' => 'Store credit amount cannot exceed the total amount needed (₱' . number_format($totalNeeded, 2) . ')'
                ])->withInput();
            }
            
            if ($storeCreditAmount < 0) {
                \Log::info('Store credit amount is negative', ['store_credit_amount' => $storeCreditAmount]);
                return back()->withErrors([
                    'store_credit_amount' => 'Store credit amount cannot be negative'
                ])->withInput();
            }
            
            \Log::info('Store credit validation passed', ['store_credit_amount' => $storeCreditAmount, 'store_credit_balance' => $storeCreditBalance, 'total_needed' => $totalNeeded]);
        }

        // Calculate final total with loyalty discount and store credit
        $finalTotal = $subtotal + $shippingFee - $loyaltyDiscount - $storeCreditAmount;

        return view('customer.checkout.payment_method', compact('cartItems', 'subtotal', 'shippingFee', 'loyaltyCard', 'loyaltyDiscount', 'discountedItem', 'finalTotal', 'useStoreCredit', 'storeCreditAmount', 'storeCreditBalance'));
    }

    public function processOrder(Request $request)
    {
        // Add debugging
        \Log::info('Checkout process started', ['request_data' => $request->all()]);
        \Log::info('Payment method received', ['payment_method' => $request->input('payment_method')]);

        $user = Auth::user();

        // Check if this is a "Buy now" flow (product_id and quantity provided)
        $productId = $request->input('product_id');
        $catalogProductId = $request->input('catalog_product_id');
        $customBouquetId = $request->input('custom_bouquet_id');
        $quantity = $request->input('quantity', 1);

        \Log::info('ProcessOrder - Buy now flow check', [
            'product_id' => $productId,
            'catalog_product_id' => $catalogProductId,
            'quantity' => $quantity,
            'has_product_id' => !empty($productId),
            'has_catalog_product_id' => !empty($catalogProductId)
        ]);

        if ($productId) {
            // "Buy now" flow with regular product ID
            $product = \App\Models\Product::find($productId);
            if (!$product) {
                return redirect()->route('customer.products.index')->with('error', 'Product not found.');
            }
        } elseif ($catalogProductId) {
            // "Buy now" flow with catalog product ID - convert to regular product
            $catalog = \App\Models\CatalogProduct::find($catalogProductId);
            if (!$catalog) {
                return redirect()->route('customer.dashboard')->with('error', 'Product not found.');
            }

            // Find existing product or create new one
            $product = \App\Models\Product::where('name', $catalog->name)
                ->where('price', $catalog->price)
                ->where('category', $catalog->category)
                ->first();

            if (!$product) {
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
            }
            $productId = $product->id;
        } elseif ($customBouquetId) {
            // "Buy now" flow with custom bouquet ID
            $customBouquet = \App\Models\CustomBouquet::find($customBouquetId);
            if (!$customBouquet) {
                return redirect()->route('customer.products.bouquet-customize')->with('error', 'Custom bouquet not found.');
            }
        }

        if ($productId) {

            // Create a temporary cart item object for the checkout process
            $tempCartItem = new \stdClass();
            $tempCartItem->id = 'temp_' . $productId;
            $tempCartItem->product_id = $productId;
            $tempCartItem->item_type = 'product';
            $tempCartItem->quantity = $quantity;
            $tempCartItem->product = $product;
            $tempCartItem->customBouquet = null;

            $cartItems = collect([$tempCartItem]);
            \Log::info('ProcessOrder - Created temp cart item for Buy Now', ['product_id' => $productId, 'quantity' => $quantity]);
        } elseif ($customBouquetId) {
            // "Buy now" flow with custom bouquet ID
            $customBouquet = \App\Models\CustomBouquet::find($customBouquetId);
            if (!$customBouquet) {
                return redirect()->route('customer.products.bouquet-customize')->with('error', 'Custom bouquet not found.');
            }
            
            // Create a temporary cart item object for custom bouquet checkout
            $tempCartItem = new \stdClass();
            $tempCartItem->id = 'temp_custom_' . $customBouquetId;
            $tempCartItem->custom_bouquet_id = $customBouquetId;
            $tempCartItem->item_type = 'custom_bouquet';
            $tempCartItem->quantity = $quantity;
            $tempCartItem->customBouquet = $customBouquet;
            $tempCartItem->product = null;

            $cartItems = collect([$tempCartItem]);
            \Log::info('ProcessOrder - Created temp cart item for Custom Bouquet Buy Now', ['custom_bouquet_id' => $customBouquetId, 'quantity' => $quantity]);
        } else {
            // Regular cart flow - load both product and customBouquet relationships
            $selectedItemIds = $request->input('selected_items', []);
            if (!empty($selectedItemIds)) {
                $cartItems = $user->cartItems()->with(['product', 'customBouquet'])->whereIn('id', $selectedItemIds)->get();
            } else {
                $cartItems = $user->cartItems()->with(['product', 'customBouquet'])->get();
            }

        if ($cartItems->isEmpty()) {
            \Log::warning('Cart is empty for user', ['user_id' => $user->id]);
            return redirect()->route('customer.cart.index')->with('error', 'Your cart is empty.');
            }
        }

        \Log::info('Cart items found', ['count' => $cartItems->count()]);

        $request->validate([
            'payment_method' => 'required|in:cod,gcash,paymaya,gotyme,rcbc_debit_card,rcbc_credit_card,seabank_debit_card,seabank_credit_card,bpi_debit_card,bpi_credit_card,bdo_debit_card,bdo_credit_card,metrobank_debit_card,metrobank_credit_card,security_bank_debit_card,security_bank_credit_card,other_debit_card,other_credit_card',
        ]);

        \Log::info('Validation passed');

        // Calculate total
        $subtotal = $cartItems->sum(function($item) {
            $unitPrice = 0;
            if (($item->item_type ?? null) === 'custom_bouquet' && isset($item->customBouquet)) {
                $unitPrice = $item->customBouquet->unit_price ?? ($item->customBouquet->total_price ?? $item->customBouquet->price ?? 0);
            } else if (isset($item->product) && isset($item->product->price)) {
                $unitPrice = $item->product->price;
            }
            return ($item->quantity ?? 1) * $unitPrice;
        });
        // Use shipping_fee from form if present
        $shippingFee = $request->input('shipping_fee');
        \Log::info('Shipping fee from request', ['shipping_fee' => $shippingFee, 'type' => gettype($shippingFee)]);
        if (!$shippingFee || !is_numeric($shippingFee)) {
            $shippingFee = 30; // fallback minimum only if not provided or invalid
            \Log::info('Using fallback shipping fee', ['shipping_fee' => $shippingFee]);
        }
        // Check for automatic loyalty discount (4/5 stamps = 5th order = 50% off most expensive item)
        $loyaltyDiscount = 0;
        $loyaltyCard = \App\Models\LoyaltyCard::where('user_id', $user->id)->first();

        if ($loyaltyCard && $loyaltyCard->stamps_count >= 4) {
            // Find the most expensive item in the cart
            $mostExpensiveItem = $cartItems->sortByDesc(function($item) {
                $unitPrice = 0;
                if (($item->item_type ?? null) === 'custom_bouquet' && isset($item->customBouquet)) {
                    $unitPrice = $item->customBouquet->unit_price ?? ($item->customBouquet->total_price ?? $item->customBouquet->price ?? 0);
                } else if (isset($item->product) && isset($item->product->price)) {
                    $unitPrice = $item->product->price;
                }
                return $unitPrice * ($item->quantity ?? 1);
            })->first();

            if ($mostExpensiveItem) {
                // Calculate 50% discount on the most expensive item
                $unitPrice = 0;
                if (($mostExpensiveItem->item_type ?? null) === 'custom_bouquet' && isset($mostExpensiveItem->customBouquet)) {
                    $unitPrice = $mostExpensiveItem->customBouquet->unit_price ?? ($mostExpensiveItem->customBouquet->total_price ?? $mostExpensiveItem->customBouquet->price ?? 0);
                } else if (isset($mostExpensiveItem->product) && isset($mostExpensiveItem->product->price)) {
                    $unitPrice = $mostExpensiveItem->product->price;
                }
                $loyaltyDiscount = ($unitPrice * ($mostExpensiveItem->quantity ?? 1)) * 0.5;
                \Log::info('Loyalty discount applied', [
                    'discount_amount' => $loyaltyDiscount,
                    'discounted_item' => $mostExpensiveItem->product->name,
                    'original_price' => $mostExpensiveItem->product->price * $mostExpensiveItem->quantity,
                    'stamps_count' => $loyaltyCard->stamps_count
                ]);
            }
        }

        $totalPrice = $subtotal + $shippingFee - $loyaltyDiscount;

        \Log::info('Calculated totals', ['subtotal' => $subtotal, 'shipping_fee' => $shippingFee, 'total' => $totalPrice]);

        \Log::info('Delivery details before processing', [
            'delivery_address_input' => $request->input('delivery_address'),
            'recipient_name_input' => $request->input('recipient_name'),
            'recipient_phone_input' => $request->input('recipient_phone'),
            'user_first_name' => $user->first_name,
            'user_last_name' => $user->last_name,
            'user_contact_number' => $user->contact_number,
        ]);

        $paymentMethod = $request->input('payment_method');

        // Get delivery details from session or request
        $checkoutData = session('checkout_data', []);
        $deliveryDate = $request->input('delivery_date') ?? $checkoutData['delivery_date'] ?? now()->addDays(2)->format('Y-m-d');
        $deliveryTime = $request->input('delivery_time') ?? $checkoutData['delivery_time'] ?? '09:00 AM';
        $deliveryAddress = $request->input('delivery_address') ?? $checkoutData['delivery_address'] ?? $user->addresses()->where('is_default', true)->first()?->street_address ?? 'Bang-bang Cordova, Cebu';

        // Ensure delivery_date and delivery_time are not empty
        if (empty($deliveryDate)) {
            $deliveryDate = now()->addDays(2)->format('Y-m-d');
        }
        if (empty($deliveryTime)) {
            $deliveryTime = '09:00 AM';
        }

        // Use recipient information from session if available, otherwise fall back to user info
        $recipientType = $checkoutData['recipient_type'] ?? 'someone';
        if ($recipientType === 'someone') {
            $recipientName = $checkoutData['recipient_name'] ?? $user->first_name . ' ' . $user->last_name;
            $recipientPhone = $checkoutData['recipient_phone'] ?? $user->contact_number;
        } else {
            $recipientName = $user->first_name . ' ' . $user->last_name;
            $recipientPhone = $user->contact_number;
        }

        // Ensure delivery address is not null
        if (empty($deliveryAddress)) {
            $deliveryAddress = 'Bang-bang Cordova, Cebu';
        }

        // Ensure recipient name is not null
        if (empty($recipientName)) {
            $recipientName = $user->first_name . ' ' . $user->last_name;
        }

        // Ensure recipient phone is not null
        if (empty($recipientPhone)) {
            $recipientPhone = $user->contact_number ?? 'N/A';
        }

        \Log::info('Final delivery details', [
            'delivery_address' => $deliveryAddress,
            'recipient_name' => $recipientName,
            'recipient_phone' => $recipientPhone,
            'delivery_date' => $deliveryDate,
            'delivery_time' => $deliveryTime,
        ]);

        // Get store credit parameters
        $useStoreCredit = $request->has('use_store_credit');
        $storeCreditAmount = floatval($request->input('store_credit_amount', 0));

        \Log::info('About to handle payment method', ['payment_method' => $paymentMethod, 'use_store_credit' => $useStoreCredit, 'store_credit_amount' => $storeCreditAmount]);

        // Handle different payment methods
        if ($paymentMethod === 'cod') {
            // For COD, create order immediately
            return $this->createOrder($request, $user, $cartItems, $totalPrice, 'cod', $deliveryDate, $deliveryTime, $deliveryAddress, $recipientName, $recipientPhone, $shippingFee, $loyaltyDiscount, $loyaltyCard, $useStoreCredit, $storeCreditAmount);
        } elseif ($paymentMethod === 'store_credit') {
            // For Store Credit, create order immediately
            return $this->createOrder($request, $user, $cartItems, $totalPrice, 'store_credit', $deliveryDate, $deliveryTime, $deliveryAddress, $recipientName, $recipientPhone, $shippingFee, $loyaltyDiscount, $loyaltyCard, $useStoreCredit, $storeCreditAmount);
        } elseif ($paymentMethod === 'store_credit_hybrid') {
            // For hybrid payment (Store Credit + Other method), redirect to payment gateway
            return $this->redirectToPaymentGateway($request, $user, $cartItems, $totalPrice, $paymentMethod, $deliveryDate, $deliveryTime, $deliveryAddress, $recipientName, $recipientPhone, $shippingFee, $loyaltyDiscount, $loyaltyCard, $useStoreCredit, $storeCreditAmount);
        } else {
            // For all online payment methods (e-wallets and cards), redirect to payment gateway
            return $this->redirectToPaymentGateway($request, $user, $cartItems, $totalPrice, $paymentMethod, $deliveryDate, $deliveryTime, $deliveryAddress, $recipientName, $recipientPhone, $shippingFee, $loyaltyDiscount, $loyaltyCard, $useStoreCredit, $storeCreditAmount);
        }
    }

    private function createOrder(Request $request, $user, $cartItems, $totalPrice, $paymentMethod, $deliveryDate, $deliveryTime, $deliveryAddress, $recipientName, $recipientPhone, $shippingFee, $loyaltyDiscount = 0, $loyaltyCard = null, $useStoreCredit = false, $storeCreditAmount = 0)
    {
        try {
            \Log::info('CreateOrder method called', [
                'payment_method' => $paymentMethod,
                'total_price' => $totalPrice,
                'shipping_fee' => $shippingFee,
                'loyalty_discount' => $loyaltyDiscount,
                'store_credit_amount' => $storeCreditAmount,
                'use_store_credit' => $useStoreCredit
            ]);
            
            // Get selected item IDs for cart clearing later
            $selectedItemIds = $request->input('selected_items', []);
            
            // Calculate final total after store credit deduction
            $finalTotal = $totalPrice + $shippingFee - $loyaltyDiscount - $storeCreditAmount;
            
            \Log::info('Final total calculated', [
                'final_total' => $finalTotal,
                'calculation' => $totalPrice . ' + ' . $shippingFee . ' - ' . $loyaltyDiscount . ' - ' . $storeCreditAmount
            ]);

            // Create the order
            $order = new Order([
                'user_id' => $user->id,
                'total_price' => $totalPrice,
                'status' => 'pending',
                'order_status' => 'pending',
                'payment_status' => ($paymentMethod === 'cod' || ($paymentMethod === 'store_credit' && $finalTotal <= 0)) ? 'pending' : 'unpaid',
                'payment_method' => $paymentMethod,
                'type' => 'online',
                'notes' => $request->input('notes', ''),
                'selected_cart_item_ids' => $selectedItemIds,
            ]);
            $order->save();

            // Create initial status history entry
            $order->statusHistories()->create([
                'status' => 'pending',
                'message' => 'Order created and pending approval',
            ]);

            // Handle store credit deduction if used
            if ($useStoreCredit && $storeCreditAmount > 0) {
                $this->deductStoreCredit($user, $order, $storeCreditAmount);
            }

            \Log::info('Order created successfully', ['order_id' => $order->id]);

            // Reset loyalty stamps to 0/5 if discount was applied (complete cycle reset)
            if ($loyaltyDiscount > 0 && $loyaltyCard) {
                $loyaltyCard->stamps_count = 0; // Reset to 0 stamps after using discount
                $loyaltyCard->save();

                \Log::info('Loyalty stamps reset after discount', [
                    'user_id' => $user->id,
                    'new_stamps_count' => 0,
                    'discount_applied' => $loyaltyDiscount
                ]);
            }

            // Create notifications
            $this->createNotifications($order);

            // Attach products and custom bouquets to the order
            foreach ($cartItems as $item) {
                if (($item->item_type ?? null) === 'custom_bouquet') {
                    // Attach custom bouquet
                    $customBouquetId = $item->custom_bouquet_id ?? ($item->customBouquet->id ?? null);
                    if ($customBouquetId) {
                        $order->customBouquets()->attach($customBouquetId, ['quantity' => $item->quantity]);
                        \Log::info('Attached custom bouquet to order', [
                            'order_id' => $order->id,
                            'custom_bouquet_id' => $customBouquetId,
                            'quantity' => $item->quantity
                        ]);
                    }
                } elseif (isset($item->product_id)) {
                    // Attach regular product
                    $order->products()->attach($item->product_id, ['quantity' => $item->quantity]);
                }
            }

            // Log order for inventory tracking (no stock decrease yet)
            $inventoryService = new \App\Services\InventoryService();
            $inventoryService->updateInventoryOnOrder($order);

            // Create delivery record with enhanced recipient information
            $delivery = new Delivery([
                'order_id' => $order->id,
                'delivery_date' => $deliveryDate,
                'delivery_time' => $deliveryTime,
                'status' => 'pending',
                'recipient_name' => $recipientName,
                'recipient_phone' => $recipientPhone,
                'delivery_address' => $deliveryAddress,
                'shipping_fee' => $shippingFee,
                'special_instructions' => $checkoutData['recipient_instructions'] ?? '',
                'delivery_message' => $checkoutData['delivery_message'] ?? '',
                'recipient_relationship' => $checkoutData['recipient_relationship'] ?? '',
            ]);
            $delivery->save();

            // Clear only the purchased items from cart after order is placed
            // Only clear cart if this is not a "Buy now" flow (product_id, catalog_product_id, or custom_bouquet_id)
            if (!$request->has('product_id') && !$request->has('catalog_product_id') && !$request->has('custom_bouquet_id')) {
                // Get the selected item IDs that were purchased
                $selectedItemIds = $request->input('selected_items', []);
                if (!empty($selectedItemIds)) {
                    // Only delete the selected items that were purchased
                    $user->cartItems()->whereIn('id', $selectedItemIds)->delete();
                } else {
                    // If no selected items, clear all (fallback for old behavior)
                    $user->cartItems()->delete();
                }
            }

            $successMessage = 'Order placed successfully! Your order number is #' . $order->id;
            if ($paymentMethod === 'store_credit') {
                if ($finalTotal <= 0) {
                    $successMessage .= ' Payment completed using your store credit balance. Your order is now pending approval.';
                } else {
                    $successMessage .= ' Store credit applied. Please complete payment for remaining amount.';
                }
            }
            
            return redirect()->route('customer.orders.show', $order->id)
                            ->with('success', $successMessage);

        } catch (\Exception $e) {
            \Log::error('Error creating order', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->back()->with('error', 'An error occurred while processing your order. Please try again.');
        }
    }

    private function redirectToPaymentGateway(Request $request, $user, $cartItems, $totalPrice, $paymentMethod, $deliveryDate, $deliveryTime, $deliveryAddress, $recipientName, $recipientPhone, $shippingFee, $loyaltyDiscount = 0, $loyaltyCard = null, $useStoreCredit = false, $storeCreditAmount = 0)
    {
        \Log::info('TEST: Entered redirectToPaymentGateway', [
            'user_id' => $user->id,
            'payment_method' => $paymentMethod,
            'cart_count' => $cartItems->count(),
            'total_price' => $totalPrice,
            'recipient_name' => $recipientName,
            'recipient_phone' => $recipientPhone,
        ]);
        \Log::info('Payment method check', ['is_card_method' => str_contains($paymentMethod, '_card'), 'is_ewallet' => in_array($paymentMethod, ['gcash', 'paymaya', 'gotyme', 'rcbc', 'seabank'])]);
        try {
            // Ensure delivery_date and delivery_time are not empty
            if (empty($deliveryDate)) {
                $deliveryDate = now()->addDays(2)->format('Y-m-d');
            }
            if (empty($deliveryTime)) {
                $deliveryTime = '09:00 AM';
            }

            // Get selected item IDs for cart clearing later
            $selectedItemIds = $request->input('selected_items', []);

            // Create a temporary order for payment processing
            $order = new Order([
                'user_id' => $user->id,
                'total_price' => $totalPrice,
                'status' => 'pending',
                'order_status' => 'pending',
                'payment_status' => 'unpaid',
                'payment_method' => $paymentMethod,
                'type' => 'online',
                'notes' => $request->input('notes', ''),
                'selected_cart_item_ids' => $selectedItemIds,
            ]);
            $order->save();

            // Create initial status history entry
            $order->statusHistories()->create([
                'status' => 'pending',
                'message' => 'Order created and pending approval',
            ]);

            // Handle store credit deduction if used
            if ($useStoreCredit && $storeCreditAmount > 0) {
                $this->deductStoreCredit($user, $order, $storeCreditAmount);
            }

            // Reset loyalty stamps to 0/5 if discount was applied (complete cycle reset)
            if ($loyaltyDiscount > 0 && $loyaltyCard) {
                $loyaltyCard->stamps_count = 0; // Reset to 0 stamps after using discount
                $loyaltyCard->save();

                \Log::info('Loyalty stamps reset after discount (payment flow)', [
                    'user_id' => $user->id,
                    'new_stamps_count' => 0,
                    'discount_applied' => $loyaltyDiscount
                ]);
            }

            // Attach products and custom bouquets to the order
            foreach ($cartItems as $item) {
                if (($item->item_type ?? null) === 'custom_bouquet') {
                    // Attach custom bouquet
                    $customBouquetId = $item->custom_bouquet_id ?? ($item->customBouquet->id ?? null);
                    if ($customBouquetId) {
                        $order->customBouquets()->attach($customBouquetId, ['quantity' => $item->quantity]);
                        \Log::info('Attached custom bouquet to order (payment gateway)', [
                            'order_id' => $order->id,
                            'custom_bouquet_id' => $customBouquetId,
                            'quantity' => $item->quantity
                        ]);
                    }
                } elseif (isset($item->product_id)) {
                    // Attach regular product
                    $order->products()->attach($item->product_id, ['quantity' => $item->quantity]);
                }
            }
            // Get checkout data from session for additional recipient information
            $checkoutData = session('checkout_data', []);

            $delivery = new \App\Models\Delivery([
                'order_id' => $order->id,
                'delivery_date' => $deliveryDate,
                'delivery_time' => $deliveryTime,
                'status' => 'pending',
                'recipient_name' => $recipientName,
                'recipient_phone' => $recipientPhone,
                'delivery_address' => $deliveryAddress,
                'shipping_fee' => $shippingFee,
                'special_instructions' => $checkoutData['recipient_instructions'] ?? '',
                'delivery_message' => $checkoutData['delivery_message'] ?? '',
                'recipient_relationship' => $checkoutData['recipient_relationship'] ?? '',
            ]);
            $delivery->save();

            // DON'T clear cart yet - wait for successful payment
            // Cart will be cleared in payment callback when payment is confirmed

            // PayMongo integration via Checkout Sessions (shows E-Wallet page first)
            if (in_array($paymentMethod, ['gcash', 'paymaya', 'gotyme', 'rcbc_debit_card', 'rcbc_credit_card', 'seabank_debit_card', 'seabank_credit_card', 'bpi_debit_card', 'bpi_credit_card', 'bdo_debit_card', 'bdo_credit_card', 'metrobank_debit_card', 'metrobank_credit_card', 'security_bank_debit_card', 'security_bank_credit_card', 'other_debit_card', 'other_credit_card'])) {
                \Log::info('Creating PayMongo Checkout Session', ['payment_method' => $paymentMethod, 'amount' => $totalPrice]);
                try {
                    $amountInCents = (int) ($totalPrice * 100);
                    $response = \Illuminate\Support\Facades\Http::withBasicAuth(env('PAYMONGO_SECRET_KEY'), '')
                        ->post(env('PAYMONGO_BASE_URL', 'https://api.paymongo.com/v1') . '/checkout_sessions', [
                            'data' => [
                                'attributes' => [
                                    'billing' => [
                                        'name' => $user->name ?? ($user->first_name . ' ' . $user->last_name),
                                        'email' => $user->email,
                                    ],
                                    'line_items' => [
                                        [
                                            'name' => 'Order #' . $order->id,
                                            'quantity' => 1,
                                            'amount' => $amountInCents,
                                            'currency' => 'PHP'
                                        ]
                                    ],
                                    'payment_method_types' => ['card', 'gcash', 'paymaya'],
                                    'success_url' => route('customer.payment.callback', $order->id) . (env('DEMO_PAYMENTS', false) ? '?simulate=1' : ''),
                                    'cancel_url' => route('customer.orders.show', $order->id),
                                ]
                            ]
                        ]);
                    if ($response->successful()) {
                        $checkoutSessionId = $response['data']['id'] ?? null;
                        $checkoutUrl = $response['data']['attributes']['checkout_url'] ?? null;
                        \Log::info('Checkout Session created successfully', ['session_id' => $checkoutSessionId, 'checkout_url' => $checkoutUrl]);
                        $order->paymongo_checkout_session_id = $checkoutSessionId;
                        $order->save();
                        \Log::info('About to redirect to PayMongo', ['url' => $checkoutUrl]);
                        // Use JavaScript redirect to ensure it works
                        return response()->view('customer.checkout.redirect', ['checkout_url' => $checkoutUrl]);
                    }
                    \Log::error('Checkout Session error (wallets)', ['status' => $response->status(), 'body' => $response->body()]);
                } catch (\Throwable $e) {
                    \Log::error('Checkout Session exception (wallets)', ['error' => $e->getMessage()]);
                }
                return redirect()->route('customer.orders.show', $order->id)
                    ->with('warning', 'Order created but could not start payment session.');
            }
            // fallback (should not happen)
            return redirect()->route('customer.orders.show', $order->id)
                ->with('success', 'Order placed successfully! Your order number is #' . $order->id);
        } catch (\Exception $e) {
            \Log::error('Error preparing payment', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request' => $request->all(),
                'user_id' => $user->id,
            ]);
            return redirect()->back()->with('error', 'An error occurred while preparing payment. Please try again.');
        }
    }

    private function createNotifications($order)
    {
        try {
            // Create notification for admin
            $adminUsers = \App\Models\User::where('role', 'admin')->get();
            foreach ($adminUsers as $admin) {
                $admin->notify(new \App\Notifications\NewOrderNotification($order));
            }

            // Create notification for clerk
            $clerkUsers = \App\Models\User::where('role', 'clerk')->get();
            foreach ($clerkUsers as $clerk) {
                $clerk->notify(new \App\Notifications\NewOrderNotification($order));
            }

            // Create notification for customer
            $order->user->notify(new \App\Notifications\OrderPlacedNotification($order));

            \Log::info('Notifications created successfully');
        } catch (\Exception $e) {
            \Log::error('Error creating notifications', ['error' => $e->getMessage()]);
        }
    }

    /**
     * Deduct store credit from customer's balance
     */
    private function deductStoreCredit($user, $order, $amount)
    {
        try {
            // Find all store credit transactions ordered by most recent
            $storeCreditOrders = Order::where('user_id', $user->id)
                ->where('refund_method', 'store_credit')
                ->whereNotNull('refund_amount')
                ->whereNotNull('refund_processed_at')
                ->orderBy('refund_processed_at', 'desc')
                ->get();

            $totalAvailable = $storeCreditOrders->sum('refund_amount');
            
            if ($totalAvailable < $amount) {
                \Log::error('Insufficient store credit for deduction', [
                    'user_id' => $user->id,
                    'order_id' => $order->id,
                    'amount' => $amount,
                    'available' => $totalAvailable
                ]);
                return false;
            }

            $remainingAmount = $amount;
            $usedOrders = [];

            // Deduct from orders starting with the most recent
            foreach ($storeCreditOrders as $storeCreditOrder) {
                if ($remainingAmount <= 0) break;
                
                $availableInOrder = $storeCreditOrder->refund_amount;
                $amountToDeduct = min($remainingAmount, $availableInOrder);
                
                // Create a store credit usage record
                $storeCreditOrder->statusHistories()->create([
                    'status' => 'store_credit_used',
                    'message' => "Store credit of ₱" . number_format($amountToDeduct, 2) . " used for order #" . $order->id,
                    'changed_by' => $user->id,
                    'changed_at' => now()
                ]);
                
                $usedOrders[] = [
                    'order_id' => $storeCreditOrder->id,
                    'amount' => $amountToDeduct
                ];
                
                $remainingAmount -= $amountToDeduct;
            }

            // Update the order to track store credit usage
            $order->update([
                'store_credit_used' => $amount,
                'store_credit_order_id' => $usedOrders[0]['order_id'] // Primary source order
            ]);

            \Log::info('Store credit deducted successfully', [
                'user_id' => $user->id,
                'order_id' => $order->id,
                'amount' => $amount,
                'used_orders' => $usedOrders
            ]);

            return true;
        } catch (\Exception $e) {
            \Log::error('Error deducting store credit', [
                'error' => $e->getMessage(),
                'user_id' => $user->id,
                'order_id' => $order->id,
                'amount' => $amount
            ]);
            return false;
        }
    }
}
