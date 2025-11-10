<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\User;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\Delivery;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Notifications\OrderPaymentValidatedNotification;
use App\Models\OrderStatusHistory;
use Illuminate\Support\Facades\Storage;
use App\Notifications\OrderApprovedNotification;
use App\Notifications\NewChatMessageNotification;

class OrderController extends Controller
{
    /**
     * Display a listing of the orders.
     */
    public function index(Request $request)
    {
        // Check if the request is from a customer middleware and adjust the query accordingly
        if ($request->routeIs('customer.*')) {
            $query = Auth::user()->orders()->with([
                'products' => function($q) {
                    $q->select('products.id', 'products.name', 'products.price', 'products.image')->limit(10); // Limit products and only select needed fields
                },
                'customBouquets' => function($q) {
                    $q->select('custom_bouquets.id', 'custom_bouquets.bouquet_type', 'custom_bouquets.total_price', 'custom_bouquets.customization_data', 'custom_bouquets.focal_flower_1', 'custom_bouquets.focal_flower_2', 'custom_bouquets.focal_flower_3', 'custom_bouquets.greenery', 'custom_bouquets.wrapper', 'custom_bouquets.ribbon', 'custom_bouquets.filler')->limit(10); // Limit custom bouquets and only select needed fields
                }
            ]);
            
            // Handle search by product name or custom bouquet
            if ($request->has('search') && !empty($request->search)) {
                $searchTerm = $request->search;
                $query->where(function($q) use ($searchTerm) {
                    $q->whereHas('products', function($productQuery) use ($searchTerm) {
                        $productQuery->where('name', 'like', "%{$searchTerm}%");
                    })->orWhereHas('customBouquets', function($bouquetQuery) use ($searchTerm) {
                        $bouquetQuery->where('bouquet_type', 'like', "%{$searchTerm}%")
                                   ->orWhere('customization_data', 'like', "%{$searchTerm}%");
                    });
                });
            }
            
            // Handle status filtering
            if ($request->has('status') && $request->status !== 'all') {
                $status = $request->status;
                switch ($status) {
                    case 'to_pay':
                        $query->where(function($q) {
                            $q->where('order_status', 'pending')
                              ->orWhere(function($subQ) {
                                  $subQ->whereNull('order_status')
                                       ->where('status', 'pending');
                              });
                        });
                        break;
                    case 'to_ship':
                        $query->where(function($q) {
                            $q->where('order_status', 'approved')
                              ->orWhere(function($subQ) {
                                  $subQ->whereNull('order_status')
                                       ->where('status', 'approved');
                              });
                        });
                        break;
                    case 'to_receive':
                        $query->where(function($q) {
                            $q->where('order_status', 'on_delivery')
                              ->orWhere(function($subQ) {
                                  $subQ->whereNull('order_status')
                                       ->where('status', 'on_delivery');
                              });
                        });
                        break;
                    case 'receive':
                        $query->where(function($q) {
                            $q->whereIn('order_status', ['delivered', 'completed'])
                              ->orWhere(function($subQ) {
                                  $subQ->whereNull('order_status')
                                       ->whereIn('status', ['delivered', 'completed']);
                              });
                        });
                        break;
                    case 'to_review':
                        // Include orders considered finished/received for review eligibility
                        $query->where(function($q) {
                            $q->whereIn('order_status', ['completed', 'delivered', 'received'])
                              ->orWhere(function($subQ) {
                                  $subQ->whereNull('order_status')
                                       ->whereIn('status', ['completed', 'delivered', 'received']);
                              });
                        });
                        break;
                    default:
                        // For 'all' or any other status, show all orders
                        break;
                }
            }
            
            $orders = $query->latest()->paginate(5); // Reduced from 10 to 5 for better performance
            return view('customer.orders.index', compact('orders'));
        } else {
            $type = $request->input('type', 'online');
            $search = $request->input('search');
            if ($type === 'walkin') {
                $walkInOrdersQuery = Order::with(['user', 'products'])->where('type', 'walk-in')
                    ->whereIn('status', ['quotation', 'validated', 'done', 'approved']); // Only show appropriate walk-in statuses
                if ($search) {
                    $walkInOrdersQuery->where(function($q) use ($search) {
                        $q->whereHas('user', function($uq) use ($search) {
                            $uq->where('name', 'like', "%$search%");
                        })
                        ->orWhere('id', 'like', "%$search%");
                    });
                }
                $walkInOrders = $walkInOrdersQuery->latest()->get();
                $onlineOrders = collect();
            } else {
                // Admin online orders - default to pending status like clerk
                $status = $request->input('status', 'pending');
                $onlineOrdersQuery = Order::with(['user', 'products'])->where('type', 'online');
                
                if ($search) {
                    $onlineOrdersQuery->where(function($q) use ($search) {
                        $q->whereHas('user', function($uq) use ($search) {
                            $uq->where('name', 'like', "%$search%");
                        })
                        ->orWhere('id', 'like', "%$search%");
                    });
                }
                
                // Apply status filter (same logic as clerk)
                if ($status) {
                    $onlineOrdersQuery->where(function($qq) use ($status) {
                        $qq->where('order_status', $status)
                           ->orWhere(function($sub) use ($status){
                               $sub->whereNull('order_status')->where('status', $status);
                           });
                    });
                }
                
                // Complete today filter
                if ($request->boolean('today') && $status === 'completed') {
                    $onlineOrdersQuery->whereDate('updated_at', now()->toDateString());
                }
                
                $onlineOrders = $onlineOrdersQuery->latest()->get();
                $walkInOrders = collect();
            }
            // Redirect to the new unified Sales Orders page
            $redirectUrl = route('admin.sales-orders.index');
            
            // Preserve query parameters
            $queryParams = $request->only(['type', 'search', 'status']);
            if (!empty(array_filter($queryParams))) {
                $redirectUrl .= '?' . http_build_query($queryParams);
            }
            
            return redirect($redirectUrl);
        }
    }

    public function walkInIndex()
    {
        $walkInOrders = Order::with(['user', 'products'])->where('type', 'walk-in')->latest()->get();
        $products = Product::all(); // Fetch all products for the modal
        return view('admin.orders.walk_in_index', compact('walkInOrders', 'products'));
    }

    /**
     * Show the form for creating a new order.
     */
    public function create()
    {
        $products = Product::all();
        return view('clerk.orders.create', compact('products'));
    }

    /**
     * Store a newly created order in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'customer_name' => 'nullable|string|max:255',
            'order_date' => 'nullable|date',
            'invoice_address' => 'nullable|string|max:255',
            'delivery_address' => 'required|string|max:255',
            'price_list' => 'nullable|string',
            'products' => 'required|array',
            'products.*.product_id' => 'required|exists:products,id',
            'products.*.quantity' => 'required|integer|min:1',
            'order_type' => 'required|in:online,walk-in',
            'shipping_fee' => 'nullable|numeric',
            'payment_method' => 'required|string',
            'order_method' => 'required|string',
        ]);

        $user = Auth::user();
        $orderUserId = $user->id;

        if ($validatedData['order_type'] === 'walk-in' && !empty($validatedData['customer_name'])) {
            $walkInUser = User::firstOrCreate(
                ['email' => 'walkin_' . str_replace(' ', '_', strtolower($validatedData['customer_name'])) . '@example.com'],
                [
                    'name' => $validatedData['customer_name'],
                    'password' => Hash::make(Str::random(10)),
                    'role' => 'customer',
                    'contact_number' => 'N/A',
                ]
            );
            $orderUserId = $walkInUser->id;
        }

        $totalPrice = 0;
        foreach ($validatedData['products'] as $productData) {
            $product = Product::find($productData['product_id']);
            if ($product) {
                $totalPrice += $product->price * $productData['quantity'];
            }
        }
        $shippingFee = $validatedData['shipping_fee'] ?? 0;
        $grandTotal = $totalPrice + $shippingFee;

        // Set status for all new orders
        $status = 'pending';

        $order = Order::create([
            'user_id' => $orderUserId,
            'total_price' => $grandTotal,
            'status' => $status,
            'notes' => 'Order placed by ' . ($validatedData['order_type'] ?? 'customer') . '.',
            'payment_status' => 'unpaid',
            'payment_method' => $validatedData['payment_method'],
            'type' => $validatedData['order_type'],
        ]);

        // Create initial status history entry
        $order->statusHistories()->create([
            'status' => 'pending',
            'message' => 'Order created and pending approval',
        ]);

        foreach ($validatedData['products'] as $productData) {
            $order->products()->attach($productData['product_id'], ['quantity' => $productData['quantity']]);
        }

        // Log order for inventory tracking (no stock decrease yet)
        $inventoryService = new \App\Services\InventoryService();
        $inventoryService->updateInventoryOnOrder($order);

        // Create delivery record for walk-in orders
        $delivery = new Delivery([
            'order_id' => $order->id,
            'delivery_date' => $validatedData['order_date'] ?? date('Y-m-d'), // Use order date as delivery date if not specified
            'delivery_time' => 'Anytime (8AM to 7PM)',
            'status' => 'pending',
            'recipient_name' => $validatedData['customer_name'] ?? ($user->name ?? 'N/A'),
            'recipient_phone' => $walkInUser->contact_number ?? ($user->contact_number ?? 'N/A'),
            'delivery_address' => $validatedData['delivery_address'] ?? 'N/A',
            'shipping_fee' => $shippingFee,
        ]);
        $delivery->save();

        // Automatically generate invoice for walk-in orders (Evaluator 3 suggestion)
        if ($validatedData['order_type'] === 'walk-in') {
            // Generate PDF invoice
            $order->load(['user', 'products', 'delivery']);
            $pdf = Pdf::loadView('orders.invoice', compact('order'));

            // Save invoice to storage
            $invoicePath = 'invoices/invoice-' . $order->id . '-' . date('Y-m-d') . '.pdf';
            Storage::disk('public')->put($invoicePath, $pdf->output());

            // Store invoice path in order (you might want to add an invoice_path column to orders table)
            // $order->update(['invoice_path' => $invoicePath]);
        }

        if (Auth::user()->role === 'clerk') {
            return redirect()->route('clerk.orders.index')->with('success', 'Walk-in order created successfully!');
        }

        return redirect()->route('admin.orders.index')->with('success', 'Order created successfully.');
    }

    /**
     * Display the specified order.
     */
    public function show(Order $order)
    {
        // Ensure the order belongs to the authenticated user if it's a customer route
        if (Auth::user()->role === 'customer' && $order->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $order->load(['user', 'products', 'customBouquets', 'delivery', 'paymentTracking']); // Eager load relationships for the show view

        if (Auth::user()->role === 'customer') {
            return view('customer.orders.show', compact('order'));
        } elseif (Auth::user()->role === 'clerk') {
            return view('clerk.orders.show', compact('order'));
        } else {
            return view('admin.orders.show', compact('order'));
        }
    }

    /**
     * Update the specified order in storage.
     */
    public function update(Request $request, Order $order)
    {
        $validatedData = $request->validate([
            'user_id' => 'required|exists:users,id',
            'status' => 'required|string|in:pending,approved,validated,processing,shipped,completed,cancelled,out_for_delivery,delivered',
            'notes' => 'nullable|string',
            'products' => 'required|array',
            'products.*.product_id' => 'required|exists:products,id',
            'products.*.quantity' => 'required|integer|min:1',
        ]);

        $oldStatus = $order->status;
        $order->user_id = $validatedData['user_id'];
        $order->status = $validatedData['status'];
        $order->notes = $validatedData['notes'];

        $totalPrice = 0;
        $productsToSync = [];
        foreach ($validatedData['products'] as $productData) {
            $product = Product::find($productData['product_id']);
            if ($product) {
                $totalPrice += $product->price * $productData['quantity'];
                $productsToSync[$productData['product_id']] = ['quantity' => $productData['quantity']];
            }
        }

        $order->total_price = $totalPrice;
        $order->save();
        $order->products()->sync($productsToSync);

        // Record status change if changed
        if ($oldStatus !== $order->status) {
            OrderStatusHistory::create([
                'order_id' => $order->id,
                'status' => $order->status,
                'message' => 'Order status updated via admin/clerk.',
            ]);
        }

        return back()->with('success', 'Order updated successfully.');
    }

    /**
     * Remove the specified order from storage.
     */
    public function destroy(Order $order)
    {
        $order->delete();
        return back()->with('success', 'Order deleted successfully.');
    }

    /**
     * Show the form for editing the specified order.
     */
    public function edit(Order $order)
    {
        $order->load(['user', 'products', 'delivery']);
        $products = Product::all();
        $users = User::all();
        return view('admin.orders.edit', compact('order', 'products', 'users'));
    }

    /**
     * Approve the specified order.
     */
    public function approve(Order $order)
    {
        $oldStatus = $order->status;
        $order->status = 'approved';
        $order->save();

        // Notify customer
        $customer = $order->user; // FIX: use user relationship
        if ($customer) {
            $customer->notify(new OrderApprovedNotification($order));
        }

        if ($oldStatus !== $order->status) {
            OrderStatusHistory::create([
                'order_id' => $order->id,
                'status' => $order->status,
                'message' => 'Order approved.',
            ]);
        }
        return back()->with('success', 'Order approved.');
    }

    /**
     * Validate the specified order.
     */
    public function validateOrder(Order $order)
    {
        $orderStatusService = new \App\Services\OrderStatusService();
        
        // Approve the order (moves to Approved Orders)
        $orderStatusService->approveOrder($order, auth()->id());

        // Update payment proof status
        $latestProof = $order->paymentProofs()->latest()->first();
        if ($latestProof) {
            $latestProof->status = 'approved';
            $latestProof->save();
        }

        // Send notification to customer
        $order->user->notify(new OrderPaymentValidatedNotification($order));

        return back()->with('success', 'Order validated successfully. Order moved to Approved Orders. Ready for driver assignment.');
    }

    /**
     * Show the invoice for the specified order (view in browser).
     */
    public function invoice(Order $order)
    {
        $order->load(['user', 'products', 'customBouquets', 'delivery']);

        if (Auth::user()->role === 'clerk') {
            return view('clerk.orders.invoice', compact('order'));
        }

        return view('admin.orders.invoice', compact('order'));
    }

    /**
     * Download the invoice as PDF for the specified order.
     */
    public function downloadInvoice(Order $order)
    {
        $order->load(['user', 'products', 'delivery']);

        $pdf = Pdf::loadView('orders.invoice', compact('order'));

        return $pdf->download('invoice-' . $order->id . '.pdf');
    }

    /**
     * View the invoice as PDF in browser for the specified order.
     */
    public function viewInvoice(Order $order)
    {
        $order->load(['user', 'products', 'delivery']);

        $pdf = Pdf::loadView('orders.invoice', compact('order'));

        return $pdf->stream('invoice-' . $order->id . '.pdf');
    }

    /**
     * Cancel the specified order (customer only, if pending).
     */
    public function cancel(Order $order)
    {
        if (auth()->user()->id !== $order->user_id) {
            abort(403, 'Unauthorized action.');
        }
        if ($order->status !== 'pending') {
            return back()->with('error', 'Order cannot be cancelled. Only pending orders can be cancelled.');
        }
        $hoursSinceCreation = now()->diffInHours($order->created_at);
        if ($hoursSinceCreation > 24) {
            return back()->with('error', 'Order cannot be cancelled after 24 hours from placement.');
        }
        $oldStatus = $order->status;
        $order->status = 'cancelled';
        $order->save();
        if ($order->delivery) {
            $order->delivery->status = 'cancelled';
            $order->delivery->save();
        }
        if ($oldStatus !== $order->status) {
            OrderStatusHistory::create([
                'order_id' => $order->id,
                'status' => $order->status,
                'message' => 'Order cancelled by customer.',
            ]);
        }
        return back()->with('success', 'Order cancelled successfully.');
    }

    /**
     * Store payment proof for an order (customer).
     */
    public function uploadPaymentProof(Request $request, Order $order)
    {
        // Only allow if order belongs to user and is unpaid
        if (auth()->id() !== $order->user_id || $order->payment_status === 'paid') {
            return 'Step 2: Unauthorized or already paid';
        }

        $request->validate([
            'payment_method' => 'required|in:gcash,paymaya,seabank,rcbc',
            'reference_number' => 'nullable|string|max:100',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:4096',
        ]);

        // Step 4
        // return 'Step 4: Passed validation';

        // Store image
        $imagePath = $request->file('image')->store('payment_proofs', 'public');

        // Save payment proof
        $order->paymentProofs()->create([
            'image_path' => $imagePath,
            'reference_number' => $request->reference_number,
            'payment_method' => $request->payment_method,
            'status' => 'pending',
        ]);

        return back()->with('success', 'Payment proof uploaded! Please wait for admin/clerk verification.');
    }

    /**
     * Return the status history for an order as JSON (for AJAX timeline).
     */
    public function statusHistory(Order $order)
    {
        $user = auth()->user();
        // Only allow if the user owns the order or is admin/clerk
        if ($user->role === 'customer' && $order->user_id !== $user->id) {
            abort(403, 'Unauthorized action.');
        }
        $history = $order->statusHistories()->orderBy('created_at')->get(['status', 'message', 'created_at']);
        
        // Transform the data to include formatted status
        $formattedHistory = $history->map(function ($item) {
            return [
                'status' => ucfirst($item->status),
                'message' => $item->message,
                'created_at' => $item->created_at,
            ];
        });
        
        return response()->json($formattedHistory);
    }

    /**
     * Show the Track Orders page for the customer.
     */
    public function trackOrdersPage()
    {
        $orders = auth()->user()->orders()->with(['statusHistories' => function($q) { 
            $q->orderBy('created_at')->limit(20); // Limit status histories to prevent loading too many
        }])->latest()->limit(50)->get(); // Limit orders to 50 most recent
        return view('customer.track_orders', compact('orders'));
    }

    public function markDelivered(Order $order)
    {
        $order->status = 'delivered';
        $order->save();

        // Update delivery status if exists
        if ($order->delivery) {
            $order->delivery->status = 'delivered';
            $order->delivery->save();
        }

        // Log delivery for inventory tracking (no stock change yet)
        $inventoryService = new \App\Services\InventoryService();
        $inventoryService->updateInventoryOnDelivery($order);

        // Notify customer about delivery
        try {
            $order->user->notify(new \App\Notifications\OrderDeliveredNotification($order));
        } catch (\Throwable $e) {
            \Log::error("Failed to send delivery notification for order {$order->id}: {$e->getMessage()}");
        }

        return redirect()->back()->with('success', 'Order marked as delivered successfully!');
    }

    public function assignDelivery(Request $request, Order $order)
    {
        $request->validate([
            'driver_id' => 'required|exists:users,id',
            'delivery_date' => 'required|date',
        ]);

        $orderStatusService = new \App\Services\OrderStatusService();
        
        // Use OrderStatusService to assign driver (sets status to 'assigned' - pending acceptance)
        if ($orderStatusService->assignDriver($order, $request->driver_id, auth()->id())) {
            // Create or update delivery record
            $delivery = $order->delivery;
            if (!$delivery) {
                $delivery = new \App\Models\Delivery();
                $delivery->order_id = $order->id;
            }
            $delivery->driver_id = $request->driver_id;
            $delivery->delivery_date = $request->delivery_date;
            $delivery->status = 'assigned'; // Keep as 'assigned' until driver accepts
            $delivery->recipient_name = $order->user->name;
            $delivery->recipient_phone = $order->user->contact_number ?? 'N/A';
            $delivery->delivery_address = $order->delivery_address ?? 'N/A';
            $delivery->save();

            return redirect()->back()->with('success', 'Driver assigned successfully. Order is now pending driver acceptance.');
        } else {
            return redirect()->back()->with('error', 'Failed to assign driver. Please try again.');
        }
    }

    /**
     * Mark order as received by customer (completes the order)
     */
    public function markReceived(Order $order)
    {
        $orderStatusService = new \App\Services\OrderStatusService();
        
        if ($orderStatusService->completeOrder($order, auth()->id())) {
            // Update inventory when order is received by customer
            $inventoryService = new \App\Services\InventoryService();
            $inventoryService->updateInventoryOnReceived($order);
            
            return redirect()->back()->with('success', 'Order marked as received. Order completed successfully.');
        } else {
            return redirect()->back()->with('error', 'Failed to mark order as received. Please try again.');
        }
    }

    /**
     * Validate recipient details by clerk (Evaluator 2 suggestion)
     */
    public function validateRecipient(Request $request, Order $order)
    {
        $request->validate([
            'recipient_name' => 'required|string|max:255',
            'recipient_phone' => 'required|string|max:20',
            'delivery_address' => 'required|string',
            'special_instructions' => 'nullable|string',
            'recipient_verified' => 'required|accepted',
            'address_verified' => 'required|accepted',
            'contact_verified' => 'required|accepted',
        ]);

        // Update delivery details
        if ($order->delivery) {
            $order->delivery->update([
                'recipient_name' => $request->recipient_name,
                'recipient_phone' => $request->recipient_phone,
                'delivery_address' => $request->delivery_address,
                'special_instructions' => $request->special_instructions,
                'validated_by' => auth()->id(),
                'validated_at' => now(),
            ]);
        }

        // Create status history entry
        OrderStatusHistory::create([
            'order_id' => $order->id,
            'status' => $order->status,
            'message' => 'Recipient details validated by clerk: ' . auth()->user()->name,
        ]);

        return redirect()->back()->with('success', 'Recipient details validated successfully!');
    }

    public function send(Request $request)
    {
        // ...existing code to save chat...

        // Notify recipient
        $recipient = User::find($request->recipient_id);
        $recipient->notify(new NewChatMessageNotification($request->message));

        // ...existing code...
    }

    public function submitReview(Request $request)
    {
        $request->validate([
            'order_id' => 'required|exists:orders,id',
            'product_id' => 'required|exists:products,id',
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000'
        ]);

        try {
            $order = auth()->user()->orders()->findOrFail($request->order_id);
            
            // Check if the order is completed
            if ($order->order_status !== 'completed' && $order->status !== 'completed') {
                return response()->json([
                    'success' => false,
                    'message' => 'You can only review completed orders.'
                ], 400);
            }

            // Check if the product belongs to this order
            $product = $order->products()->where('products.id', $request->product_id)->first();
            if (!$product) {
                return response()->json([
                    'success' => false,
                    'message' => 'Product not found in this order.'
                ], 400);
            }

            // Check if already reviewed
            if ($product->pivot->reviewed) {
                return response()->json([
                    'success' => false,
                    'message' => 'This product has already been reviewed.'
                ], 400);
            }

            // Update the pivot table with review data
            $order->products()->updateExistingPivot($request->product_id, [
                'rating' => $request->rating,
                'review_comment' => $request->comment,
                'reviewed' => true,
                'reviewed_at' => now()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Review submitted successfully!'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to submit review. Please try again.'
            ], 500);
        }
    }

    public function submitShopReview(Request $request)
    {
        $request->validate([
            'shop_rating' => 'required|integer|min:1|max:5',
            'shop_comment' => 'required|string|max:1000',
            'liked_most' => 'nullable|string|max:255'
        ]);

        try {
            // Persist shop review
            \DB::table('shop_reviews')->insert([
                'user_id' => auth()->id(),
                'rating' => $request->shop_rating,
                'comment' => $request->shop_comment,
                'liked_most' => $request->liked_most,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Shop review submitted successfully!'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to submit shop review. Please try again.'
            ], 500);
        }
    }

    public function updateDeliverySchedule(Request $request, $orderId)
    {
        $request->validate([
            'delivery_date' => 'required|date|after:today',
            'delivery_time' => 'required|string|in:08:00 AM,09:00 AM,10:00 AM,11:00 AM,01:00 PM,02:00 PM,03:00 PM,04:00 PM,05:00 PM'
        ]);

        try {
            $order = auth()->user()->orders()->findOrFail($orderId);
            
            // Check if order can be modified
            $orderStatus = $order->order_status ?? $order->status;
            if (!in_array($orderStatus, ['pending', 'approved'])) {
                return back()->withErrors(['error' => 'You can only update delivery schedule for pending or approved orders.']);
            }

            // Update delivery information
            if ($order->delivery) {
                $order->delivery->update([
                    'delivery_date' => $request->delivery_date,
                    'delivery_time' => $request->delivery_time,
                    'updated_at' => now()
                ]);
            } else {
                // Create delivery record if it doesn't exist
                $order->delivery()->create([
                    'delivery_address' => $order->user->address ?? 'N/A',
                    'recipient_name' => $order->user->name,
                    'delivery_date' => $request->delivery_date,
                    'delivery_time' => $request->delivery_time,
                    'status' => 'scheduled'
                ]);
            }

            return back()->with('success', 'Delivery schedule updated successfully! We will deliver your flowers on ' . 
                date('M d, Y', strtotime($request->delivery_date)) . ' at ' . $request->delivery_time . '.');
                
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Failed to update delivery schedule. Please try again.']);
        }
    }

    /**
     * Mark order as ready for delivery
     */
    public function markReady(Order $order)
    {
        try {
            $orderStatusService = new \App\Services\OrderStatusService();
            
            // First approve the order if not already approved
            if ($order->order_status !== 'approved') {
                $orderStatusService->approveOrder($order, auth()->id());
            }
            
            // Get available drivers
            $drivers = \App\Models\Driver::with('user')->where('is_active', true)->get();
            
            // Auto-assign the first available driver
            if ($drivers->isNotEmpty()) {
                $driver = $drivers->first();
                $orderStatusService->assignDriver($order, $driver->user_id, auth()->id());
            }
            
            return redirect()->back()->with('success', 'Order marked as ready and driver assigned successfully.');
        } catch (\Exception $e) {
            \Log::error('Error marking order as ready: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to mark order as ready.');
        }
    }

    /**
     * Mark order as done (completed processing)
     */
    public function markDone(Order $order)
    {
        try {
            $orderStatusService = new \App\Services\OrderStatusService();
            
            // Complete the order
            if ($orderStatusService->completeOrder($order, auth()->id())) {
                return redirect()->back()->with('success', 'Order processing completed successfully.');
            } else {
                return redirect()->back()->with('error', 'Failed to complete order processing.');
            }
        } catch (\Exception $e) {
            \Log::error('Error completing order: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to complete order processing.');
        }
    }
}
