<?php

namespace App\Http\Controllers;

use App\Models\Delivery;
use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DeliveryController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            // Check if the authenticated user has 'admin' or 'clerk' role
            if (!in_array(Auth::user()->role, ['admin', 'clerk'])) {
                abort(403, 'Unauthorized action.');
            }
            return $next($request);
        });
    }

    /**
     * Display a listing of the deliveries.
     */
    public function index()
    {
        $deliveries = Delivery::with(['order.user', 'order.product', 'driver'])->latest()->paginate(10);
        return view('deliveries.index', compact('deliveries'));
    }

    /**
     * Show the form for creating a new delivery.
     */
    public function create()
    {
        $orders = Order::whereDoesntHave('delivery')->get(); // Only show orders that don't have a delivery yet
        $drivers = User::where('role', 'driver')->get();
        return view('deliveries.create', compact('orders', 'drivers'));
    }

    /**
     * Store a newly created delivery in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'order_id' => 'required|exists:orders,id|unique:deliveries',
            'driver_id' => 'required|exists:users,id',
            'delivery_date' => 'required|date',
            'status' => 'required|string',
        ]);

        $delivery = Delivery::create($request->all());
        return redirect()->route('deliveries.index')->with('success', 'Delivery created successfully.');
    }

    /**
     * Display the specified delivery.
     */
    public function show(Delivery $delivery)
    {
        return view('deliveries.show', compact('delivery'));
    }

    /**
     * Show the form for editing the specified delivery.
     */
    public function edit(Delivery $delivery)
    {
        $orders = Order::whereDoesntHave('delivery')->orWhere('id', $delivery->order_id)->get();
        $drivers = User::where('role', 'driver')->get();
        return view('deliveries.edit', compact('delivery', 'orders', 'drivers'));
    }

    /**
     * Update the specified delivery in storage.
     */
    public function update(Request $request, Delivery $delivery)
    {
        $request->validate([
            'order_id' => 'required|exists:orders,id|unique:deliveries,order_id,'.$delivery->id,
            'driver_id' => 'required|exists:users,id',
            'delivery_date' => 'required|date',
            'status' => 'required|string',
        ]);

        $wasDelivered = $delivery->status === 'delivered';
        $delivery->update($request->all());

        // Inventory logic: only trigger if status is now delivered and wasn't delivered before
        if ($request->status === 'delivered' && !$wasDelivered) {
            $order = $delivery->order;
            foreach ($order->products as $product) {
                $qty = $product->pivot->quantity;
                
                // Check if product has composition (materials needed)
                if ($product->compositions()->exists()) {
                    // Use InventoryService to deduct materials
                    $result = \App\Services\InventoryService::deductMaterialsForProduct($product, $qty);
                    
                    if (!$result['success']) {
                        // If materials can't be deducted, show error
                        return redirect()->route('deliveries.index')
                            ->with('error', 'Cannot complete delivery: ' . $result['message'] . 
                                   (isset($result['insufficient_materials']) ? 
                                    ' Missing: ' . implode(', ', array_column($result['insufficient_materials'], 'component')) : ''));
                    }
                } else {
                    // For products without composition, just deduct from stock directly
                    $product->stock = max(0, $product->stock - $qty);
                }
                
                // Add to qty_sold
                $product->qty_sold = ($product->qty_sold ?? 0) + $qty;
                $product->save();
            }
            // Also update the order status to delivered
            $order->status = 'delivered';
            $order->save();
        }

        return redirect()->route('deliveries.index')->with('success', 'Delivery updated successfully.');
    }

    /**
     * Remove the specified delivery from storage.
     */
    public function destroy(Delivery $delivery)
    {
        $delivery->delete();
        return redirect()->route('deliveries.index')->with('success', 'Delivery deleted successfully.');
    }

    /**
     * Mark the delivery as delivered and complete the order.
     */
    public function markDelivered($deliveryId)
    {
        $delivery = Delivery::findOrFail($deliveryId);
        $order = $delivery->order;

        // Use OrderStatusService to complete the order (this will send notifications)
        try {
            $orderStatusService = new \App\Services\OrderStatusService();
            if ($orderStatusService->completeOrder($order, auth()->id())) {
                return redirect()->route('deliveries.index')->with('success', 'Order marked as completed!');
            } else {
                return redirect()->route('deliveries.index')->with('error', 'Failed to complete order.');
            }
        } catch (\Exception $e) {
            \Log::error("Failed to complete order for delivery {$deliveryId}: " . $e->getMessage());
            return redirect()->route('deliveries.index')->with('error', 'Error completing order: ' . $e->getMessage());
        }
    }
} 