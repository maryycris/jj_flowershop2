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
        
        // Get inventory movement for this order
        $inventoryService = new \App\Services\InventoryManagementService();
        $inventoryMovement = $inventoryService->getOrderMovement($order);
        
        // If no movement exists yet, get preview number
        if (!$inventoryMovement) {
            $previewMovementNumber = $inventoryService->getPreviewMovementNumber();
            $inventoryMovement = (object)['movement_number' => $previewMovementNumber];
        }
        
        // Get product composition breakdown for each product in the order
        $compositionService = new \App\Services\ProductCompositionService();
        $productCompositions = [];
        
        foreach ($order->products as $product) {
            $quantity = $product->pivot->quantity;
            $productCompositions[$product->id] = $compositionService->getProductCompositionBreakdown($product->id, $quantity);
        }
        
        $view = auth()->user()->role === 'admin' ? 'admin.orders.online.validate' : 'clerk.orders.online.validate';
        return view($view, compact('order', 'inventoryMovement', 'productCompositions'));
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
            $route = auth()->user()->role === 'admin' ? 'admin.orders.online.done' : 'clerk.orders.online.done';
            return redirect()->route($route, $order->id);
        }
        
        $orderStatusService = new OrderStatusService();
        $invoiceService = new InvoiceService();
        
        // First approve the order
        $orderStatusService->approveOrder($order, auth()->id());
        
        // Generate invoice after validation
        $invoiceService->createInvoice($order);
        
        // Don't auto-assign driver - let clerk manually assign
        
        // Redirect to the done page instead of returning view directly
        $route = auth()->user()->role === 'admin' ? 'admin.orders.online.done' : 'clerk.orders.online.done';
        return redirect()->route($route, $order->id)
            ->with('success', 'Order validated successfully! Please assign a driver for delivery.');
    }


    public function onlineDone(Request $request, Order $order)
    {
        // Load relationships
        $order->load('user', 'products', 'delivery', 'assignedDriver', 'invoice');
        
        // Get invoice data for display
        $invoice = $order->invoice;
        
        // Get inventory movement for this order
        $inventoryService = new \App\Services\InventoryManagementService();
        $inventoryMovement = $inventoryService->getOrderMovement($order);
        
        // If no movement exists yet, get preview number
        if (!$inventoryMovement) {
            $previewMovementNumber = $inventoryService->getPreviewMovementNumber();
            $inventoryMovement = (object)['movement_number' => $previewMovementNumber];
        }
        
        $view = auth()->user()->role === 'admin' ? 'admin.orders.online.done' : 'clerk.orders.online.done';
        return view($view, compact('order', 'invoice', 'inventoryMovement'));
    }

    // Walk-in Orders Flow
    public function walkinPending(Order $order)
    {
        return view('clerk.orders.walkin.pending', compact('order'));
    }

    public function walkinQuotation(Order $order)
    {
        $order->load('user', 'products', 'delivery');
        
        // Get inventory movement for this order
        $inventoryService = new \App\Services\InventoryManagementService();
        $inventoryMovement = $inventoryService->getOrderMovement($order);
        
        // If no movement exists yet, get preview number
        if (!$inventoryMovement) {
            $previewMovementNumber = $inventoryService->getPreviewMovementNumber();
            $inventoryMovement = (object)['movement_number' => $previewMovementNumber];
        }
        
        $view = auth()->user()->role === 'admin' ? 'admin.orders.walkin.quotation' : 'clerk.orders.walkin.quotation';
        return view($view, compact('order', 'inventoryMovement'));
    }

    public function walkinCreateInvoice(Order $order)
    {
        // Skip invoice page and go directly to validate
        $route = auth()->user()->role === 'admin' ? 'admin.orders.walkin.validate' : 'clerk.orders.walkin.validate';
        return redirect()->route($route, $order);
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
        
        // Get inventory movement for this order
        $inventoryService = new \App\Services\InventoryManagementService();
        $inventoryMovement = $inventoryService->getOrderMovement($order);
        
        // If no movement exists yet, get preview number
        if (!$inventoryMovement) {
            $previewMovementNumber = $inventoryService->getPreviewMovementNumber();
            $inventoryMovement = (object)['movement_number' => $previewMovementNumber];
        }
        
        // Get product composition breakdown for each product in the order
        $compositionService = new \App\Services\ProductCompositionService();
        $productCompositions = [];
        
        foreach ($order->products as $product) {
            $quantity = $product->pivot->quantity;
            $productCompositions[$product->id] = $compositionService->getProductCompositionBreakdown($product->id, $quantity);
        }
        
        $view = auth()->user()->role === 'admin' ? 'admin.orders.walkin.validate' : 'clerk.orders.walkin.validate';
        return view($view, compact('order', 'inventoryMovement', 'productCompositions'));
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
        $invoiceService->createInvoice($order);
        
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
        
        // Get inventory movement for this order
        $inventoryService = new \App\Services\InventoryManagementService();
        $inventoryMovement = $inventoryService->getOrderMovement($order);
        
        // If no movement exists yet, get preview number
        if (!$inventoryMovement) {
            $previewMovementNumber = $inventoryService->getPreviewMovementNumber();
            $inventoryMovement = (object)['movement_number' => $previewMovementNumber];
        }
        
        $view = auth()->user()->role === 'admin' ? 'admin.orders.walkin.done' : 'clerk.orders.walkin.done';
        return view($view, compact('order', 'inventoryMovement'));
    }

    public function createWalkinOrder()
    {
        // Get products for the form from Inventory products
        $products = \App\Models\Product::where('status', true)->get();
        
        // Determine view based on URL or user role
        if (str_contains(request()->url(), '/admin/')) {
            $view = 'admin.orders.create';
        } else {
            $view = 'clerk.orders.create';
        }
        
        return view($view, compact('products'));
    }

    /**
     * Delivery-only Walk-in Order screen (mirrors customer checkout layout)
     */
    public function createWalkinDelivery()
    {
        // Use Catalog Products (approved & active) for selection
        $catalogProducts = \App\Models\CatalogProduct::where('status', true)
            ->where('is_approved', true)
            ->with(['compositions.componentProduct'])
            ->orderBy('name')
            ->get();
        $view = str_contains(request()->url(), '/admin/')
            ? 'admin.orders.walkin.delivery'
            : 'clerk.orders.walkin.delivery';
        return view($view, [ 'catalogProducts' => $catalogProducts ]);
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
            if ($product) {
                $totalPrice += (float)$product->price * (int)$productData['quantity'];
            }
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

        // Create initial status history entry
        $order->statusHistories()->create([
            'status' => 'quotation',
            'message' => 'Order created and pending quotation',
        ]);

        // Attach products to order
        foreach ($request->products as $productData) {
            $order->products()->attach($productData['product_id'], [
                'quantity' => (int)$productData['quantity']
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
            $route = 'admin.orders.walkin.invoice';
        } else {
            $route = 'clerk.orders.walkin.invoice';
        }
        
        return redirect()->route($route, $order);
    }
}



