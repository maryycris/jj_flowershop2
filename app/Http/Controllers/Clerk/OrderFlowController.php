<?php

namespace App\Http\Controllers\Clerk;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\OrderStatusService;
use App\Services\InvoiceService;
use Illuminate\Http\Request;

class OrderFlowController extends Controller
{
    // Online Orders Flow
    public function onlineValidate(Order $order)
    {
        $order->load('user', 'products', 'delivery');
        $view = auth()->user()->role === 'admin' ? 'admin.orders.online.validate' : 'clerk.orders.online.validate';
        return view($view, compact('order'));
    }

    public function onlineInvoice(Order $order)
    {
        $order->load('user', 'products', 'delivery');
        $view = auth()->user()->role === 'admin' ? 'admin.orders.online.create_invoice' : 'clerk.orders.online.create_invoice';
        return view($view, compact('order'));
    }

    public function onlineValidateConfirm(Request $request, Order $order)
    {
        // If this is a GET request, redirect to the done page (for page refreshes)
        if ($request->isMethod('get')) {
            return redirect()->route('clerk.orders.online.done', $order->id);
        }
        
        $orderStatusService = new OrderStatusService();
        $invoiceService = new InvoiceService();
        
        // First approve the order
        $orderStatusService->approveOrder($order, auth()->id());
        
        // Generate invoice after validation
        $invoiceService->generateInvoice($order, auth()->id());
        
        // Don't auto-assign driver - let clerk manually assign
        
        // Redirect to the done page instead of returning view directly
        return redirect()->route('clerk.orders.online.done', $order->id)
            ->with('success', 'Order validated successfully! Please assign a driver for delivery.');
    }


    public function onlineDone(Request $request, Order $order)
    {
        // Load relationships
        $order->load('user', 'products', 'delivery', 'assignedDriver');
        
        // Get invoice data for display
        $invoiceService = new InvoiceService();
        $invoiceData = $invoiceService->getInvoiceData($order);
        
        $view = auth()->user()->role === 'admin' ? 'admin.orders.online.done' : 'clerk.orders.online.done';
        return view($view, compact('order', 'invoiceData'));
    }

    // Walk-in Orders Flow
    public function walkinPending(Order $order)
    {
        return view('clerk.orders.walkin.pending', compact('order'));
    }

    public function walkinQuotation(Order $order)
    {
        $order->load('user', 'products', 'delivery');
        $view = auth()->user()->role === 'admin' ? 'admin.orders.walkin.quotation' : 'clerk.orders.walkin.quotation';
        return view($view, compact('order'));
    }

    public function walkinCreateInvoice(Order $order)
    {
        $order->load('user', 'products', 'delivery');
        $view = auth()->user()->role === 'admin' ? 'admin.orders.walkin.create_invoice' : 'clerk.orders.walkin.create_invoice';
        return view($view, compact('order'));
    }

    public function walkinInvoice(Order $order)
    {
        $order->load('user', 'products', 'delivery');
        $view = auth()->user()->role === 'admin' ? 'admin.orders.walkin.create_invoice' : 'clerk.orders.walkin.create_invoice';
        return view($view, compact('order'));
    }

    public function walkinValidate(Order $order)
    {
        $order->load('user', 'products', 'delivery');
        $view = auth()->user()->role === 'admin' ? 'admin.orders.walkin.validate' : 'clerk.orders.walkin.validate';
        return view($view, compact('order'));
    }

    public function walkinValidateConfirmation(Order $order)
    {
        $order->load('user', 'products', 'delivery');
        $view = auth()->user()->role === 'admin' ? 'admin.orders.walkin.validate_confirmation' : 'clerk.orders.walkin.validate_confirmation';
        return view($view, compact('order'));
    }

    public function walkinValidateConfirm(Request $request, Order $order)
    {
        $orderStatusService = new OrderStatusService();
        $invoiceService = new InvoiceService();
        
        // First approve the order
        $orderStatusService->approveOrder($order, auth()->id());
        
        // Generate invoice after validation
        $invoiceService->generateInvoice($order, auth()->id());
        
        // Get available drivers (users with role 'driver' or similar)
        $drivers = \App\Models\User::where('role', 'driver')->get();
        
        // If no drivers found, get any available users as fallback
        if ($drivers->isEmpty()) {
            $drivers = \App\Models\User::where('id', '!=', auth()->id())->take(5)->get();
        }
        
        // Auto-assign the first available driver
        if ($drivers->isNotEmpty()) {
            $driver = $drivers->first();
            $orderStatusService->assignDriver($order, $driver->id, auth()->id());
        }
        
        $route = auth()->user()->role === 'admin' ? 'admin.orders.walkin.done' : 'clerk.orders.walkin.done';
        return redirect()->route($route, $order);
    }

    public function walkinDone(Order $order)
    {
        $order->load('user', 'products', 'delivery');
        $view = auth()->user()->role === 'admin' ? 'admin.orders.walkin.done' : 'clerk.orders.walkin.done';
        return view($view, compact('order'));
    }

    public function createWalkinOrder()
    {
        // Get products for the form
        $products = \App\Models\Product::where('status', true)->get();
        
        // Determine view based on URL or user role
        if (str_contains(request()->url(), '/admin/')) {
            $view = 'admin.orders.create';
        } else {
            $view = 'clerk.orders.create';
        }
        
        return view($view, compact('products'));
    }

    public function storeWalkinOrder(Request $request)
    {
        // Validate the request
        $request->validate([
            'customer_name' => 'required|string|max:255',
            'order_date' => 'required|date',
            'order_method' => 'required|in:delivery,picked_up',
            'payment_method' => 'required|string',
            'invoice_address' => 'required|string',
            'delivery_address' => 'required|string',
            'products' => 'required|array|min:1',
            'products.*.product_id' => 'required|exists:products,id',
            'products.*.quantity' => 'required|integer|min:1',
        ]);

        // Calculate total price
        $totalPrice = 0;
        $shippingFee = $request->order_method === 'delivery' ? ($request->shipping_fee ?? 0) : 0;
        
        foreach ($request->products as $productData) {
            $product = \App\Models\Product::find($productData['product_id']);
            $totalPrice += $product->price * $productData['quantity'];
        }
        $totalPrice += $shippingFee;

        // Create the order
        $order = Order::create([
            'user_id' => auth()->id(), // Clerk creates the order
            'total_price' => $totalPrice,
            'status' => 'quotation', // Start at quotation stage
            'type' => 'walk-in',
            'payment_status' => 'pending',
            'payment_method' => $request->payment_method,
            'notes' => "Customer: {$request->customer_name}",
        ]);

        // Attach products to order
        foreach ($request->products as $productData) {
            $order->products()->attach($productData['product_id'], [
                'quantity' => $productData['quantity']
            ]);
        }

        // Create delivery record if needed
        if ($request->order_method === 'delivery') {
            \App\Models\Delivery::create([
                'order_id' => $order->id,
                'recipient_name' => $request->customer_name,
                'delivery_address' => $request->delivery_address,
                'recipient_phone' => 'N/A', // Walk-in customers might not provide phone
                'shipping_fee' => $shippingFee,
                'status' => 'pending',
            ]);
        }

        // Redirect directly to quotation view
        // Check if the request came from admin or clerk based on URL
        $requestUrl = request()->url();
        $refererUrl = request()->header('referer');
        
        if (str_contains($requestUrl, '/admin/') || str_contains($refererUrl, '/admin/')) {
            $route = 'admin.orders.walkin.quotation';
        } else {
            $route = 'clerk.orders.walkin.quotation';
        }
        
        return redirect()->route($route, $order);
    }
}



