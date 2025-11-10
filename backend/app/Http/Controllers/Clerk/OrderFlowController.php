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
        $order->load('user', 'products', 'customBouquets', 'delivery');
        
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
        $order->load('user', 'products', 'customBouquets', 'delivery');
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
        // Show the Create Invoice page
        $order->load('user', 'products', 'delivery');
        
        // Get all active products from the database
        $products = \App\Models\Product::where('status', true)
            ->where('is_approved', true)
            ->orderBy('name')
            ->get(['id', 'name', 'price', 'description', 'category']);
        
        $view = auth()->user()->role === 'admin' ? 'admin.orders.walkin.create_invoice' : 'clerk.orders.walkin.create_invoice';
        return view($view, compact('order', 'products'));
    }

    public function updateWalkinInvoice(Request $request, Order $order)
    {
        try {
            \Log::info('Update walk-in invoice request', $request->all());
            
            // Validate the request with more flexible validation
            $request->validate([
                'customer_name' => 'required|string|max:255',
                'invoice_address' => 'required|string', // This is now delivery address
                // Email address is only required for delivery method
                'delivery_address' => 'nullable|email',
                'order_date' => 'nullable|date',
                'price_list' => 'nullable|string',
                'delivery_method' => 'nullable|string|in:pickup,delivery',
                'order_lines' => 'required|array|min:1',
                'order_lines.*.product' => 'required|string',
                'order_lines.*.description' => 'nullable|string',
                'order_lines.*.quantity' => 'required|integer|min:1',
                'order_lines.*.unit_price' => 'required|numeric|min:0',
                'total_amount' => 'required|numeric|min:0',
                'shipping_fee' => 'nullable|numeric|min:0'
            ]);

            // Additional validation for delivery orders
            if ($request->delivery_method === 'delivery') {
                $request->validate([
                    'delivery_data.driver_id' => 'required|exists:users,id',
                    'delivery_address' => 'required|string'
                ], [
                    'delivery_data.driver_id.required' => 'Please select a driver for delivery orders.',
                    'delivery_data.driver_id.exists' => 'Selected driver does not exist.'
                ]);
            }
            
            // Additional validation for pickup orders
            if ($request->delivery_method === 'pickup') {
                // Temporarily disabled for debugging
                // $this->validatePickupTime($request);
            }

            // Derive customer contact and email from pickup/delivery data
            $customerContact = null;
            $customerEmail = $request->delivery_address; // This is now the email field
            
            if ($request->delivery_method === 'pickup') {
                $customerContact = $request->pickup_data['contact'] ?? null;
            } else {
                $customerContact = $request->delivery_data['contact'] ?? null;
            }

            // Update order basic information
            $order->update([
                'total_price' => $request->total_amount + ($request->shipping_fee ?? 0),
                'notes' => trim("Customer: {$request->customer_name}" . 
                    ($customerContact ? "; Contact: {$customerContact}" : '') . 
                    ($customerEmail ? "; Email: {$customerEmail}" : '')),
                // Don't force payment method to cash; respect walk-in COD behavior
                'payment_method' => $request->delivery_method === 'pickup' ? 'cod' : ($order->payment_method ?? 'cod'),
                'status' => 'quotation',
                'order_status' => 'pending' // Will be updated to 'assigned' if delivery method is delivery
            ]);

            \Log::info('Order updated successfully', ['order_id' => $order->id, 'total_price' => $order->total_price]);

            // Clear existing products
            $order->products()->detach();

            // Add new products
            foreach ($request->order_lines as $line) {
                \Log::info('Processing order line', $line);
                
                // Find product by name or create if not exists
                $product = \App\Models\Product::where('name', $line['product'])->first();
                
                if (!$product) {
                    \Log::info('Creating new product', ['name' => $line['product']]);
                    // Create new product if it doesn't exist
                    $product = \App\Models\Product::create([
                        'name' => $line['product'],
                        'description' => $line['description'] ?? '',
                        'price' => $line['unit_price'],
                        'stock' => 0,
                        'category' => 'Walk-in',
                        'status' => true,
                        'is_approved' => true
                    ]);
                }

                // Attach product to order
                $order->products()->attach($product->id, [
                    'quantity' => $line['quantity']
                ]);
                
                \Log::info('Product attached to order', ['product_id' => $product->id, 'quantity' => $line['quantity']]);
            }

            // Update delivery information and assign driver
            if ($request->delivery_method === 'delivery') {
                $delivery = $order->delivery;
                if ($delivery) {
                    $delivery->update([
                        'delivery_address' => $request->delivery_address,
                        'recipient_name' => $request->delivery_data['recipient_name'] ?? $request->customer_name,
                        'recipient_phone' => $request->delivery_data['contact'] ?? 'N/A'
                    ]);
                } else {
                    $delivery = \App\Models\Delivery::create([
                        'order_id' => $order->id,
                        'delivery_address' => $request->delivery_address,
                        'recipient_name' => $request->delivery_data['recipient_name'] ?? $request->customer_name,
                        'recipient_phone' => $request->delivery_data['contact'] ?? 'N/A',
                        'status' => 'pending'
                    ]);
                }
                
                // Assign driver for delivery orders (same as online orders)
                $orderStatusService = new \App\Services\OrderStatusService();
                
                // Use selected driver or auto-assign if not selected
                $driverId = $request->delivery_data['driver_id'] ?? null;
                
                if ($driverId) {
                    // Use the selected driver
                    $driver = \App\Models\User::find($driverId);
                    if ($driver && $driver->role === 'driver') {
                        // Use OrderStatusService to assign driver (sets status to 'assigned' - pending acceptance)
                        if ($orderStatusService->assignDriver($order, $driver->id, auth()->id())) {
                            // Update delivery record with driver assignment
                            $delivery->update([
                                'driver_id' => $driver->id,
                                'delivery_date' => $request->delivery_data['delivery_date'] ?? date('Y-m-d'),
                                'status' => 'assigned' // Keep as 'assigned' until driver accepts
                            ]);
                            
                            // Update order status to 'assigned' to match online orders
                            $order->update(['order_status' => 'assigned']);
                            
                            \Log::info("Selected driver assigned to walk-in order", [
                                'order_id' => $order->id,
                                'driver_id' => $driver->id,
                                'driver_name' => $driver->name,
                                'order_status' => 'assigned'
                            ]);
                        }
                    }
                } else {
                    // Auto-assign first available driver if none selected
                    $drivers = \App\Models\User::where('role', 'driver')->get();
                    
                    if ($drivers->isNotEmpty()) {
                        $driver = $drivers->first();
                        
                        // Use OrderStatusService to assign driver (sets status to 'assigned' - pending acceptance)
                        if ($orderStatusService->assignDriver($order, $driver->id, auth()->id())) {
                            // Update delivery record with driver assignment
                            $delivery->update([
                                'driver_id' => $driver->id,
                                'delivery_date' => $request->delivery_data['delivery_date'] ?? date('Y-m-d'),
                                'status' => 'assigned' // Keep as 'assigned' until driver accepts
                            ]);
                            
                            // Update order status to 'assigned' to match online orders
                            $order->update(['order_status' => 'assigned']);
                            
                            \Log::info("Auto-assigned driver to walk-in order", [
                                'order_id' => $order->id,
                                'driver_id' => $driver->id,
                                'driver_name' => $driver->name,
                                'order_status' => 'assigned'
                            ]);
                        }
                    }
                }
            }

            // Create Sales Order if it doesn't exist
            $salesOrder = \App\Models\SalesOrder::where('order_id', $order->id)->first();
            if (!$salesOrder) {
                $numberingService = new \App\Services\NumberingService();
                $soNumber = $numberingService->generateSalesOrderNumber();
                
                $salesOrder = \App\Models\SalesOrder::create([
                    'so_number' => $soNumber,
                    'order_id' => $order->id,
                    'user_id' => $order->user_id,
                    'subtotal' => $request->total_amount,
                    'shipping_fee' => $request->shipping_fee ?? 0,
                    'total_amount' => $request->total_amount + ($request->shipping_fee ?? 0),
                    'status' => 'draft',
                    'notes' => 'Walk-in order - ' . $request->customer_name,
                ]);
                
                \Log::info('Sales Order created', [
                    'so_number' => $soNumber,
                    'order_id' => $order->id,
                    'sales_order_id' => $salesOrder->id
                ]);
            }

            // Prepare success message
            $message = 'Order updated successfully';
            if ($request->delivery_method === 'delivery') {
                $message .= '. Driver has been assigned for delivery.';
            }
            if ($salesOrder) {
                $message .= ' Sales Order ' . $salesOrder->so_number . ' has been generated.';
            }

            return response()->json([
                'success' => true,
                'message' => $message,
                'order_id' => $order->id,
                'sales_order_id' => $salesOrder ? $salesOrder->id : null,
                'so_number' => $salesOrder ? $salesOrder->so_number : null,
                'debug' => [
                    'order_id' => $order->id,
                    'sales_order_id' => $salesOrder ? $salesOrder->id : null,
                    'so_number' => $salesOrder ? $salesOrder->so_number : null
                ]
            ]);

        } catch (\Exception $e) {
            \Log::error('Error updating walk-in invoice', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Error updating order: ' . $e->getMessage(),
                'debug' => config('app.debug') ? $e->getTraceAsString() : null
            ], 500);
        }
    }

    public function walkinInvoice(Order $order)
    {
        $order->load('user', 'products', 'delivery');
        $view = auth()->user()->role === 'admin' ? 'admin.orders.walkin.create_invoice' : 'clerk.orders.walkin.create_invoice';
        return view($view, compact('order'));
    }

    public function walkinValidate(Order $order)
    {
        $order->load('user', 'products', 'delivery', 'salesOrder');
        
        // Update order status to SALES ORDER when accessing validate page
        if ($order->status === 'quotation') {
            $order->update(['status' => 'sales_order']);
            \Log::info("Order status updated to sales_order for order {$order->id}");
        }
        
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
        
        // Generate invoice after validation with proper payment status
        $invoice = $invoiceService->createInvoice($order);
        
        // Set payment status based on payment method
        if ($order->payment_method === 'online') {
            // Online payments are marked as paid
            $invoice->update(['status' => 'paid']);
        } else {
            // COD orders are marked as ready for payment
            $invoice->update(['status' => 'ready']);
        }
        
        // Deduct materials/components from inventory based on product compositions
        try {
            $order->load('products');
            $allMaterialsDeducted = true;
            $failedProducts = [];
            $updatedComponents = [];
            
            foreach ($order->products as $product) {
                $quantity = $product->pivot->quantity;
                
                // Use the new material deduction logic
                $result = \App\Services\InventoryService::deductMaterialsForProduct($product, $quantity, $order->id);
                
                if (!$result['success']) {
                    $allMaterialsDeducted = false;
                    $failedProducts[] = [
                        'product' => $product->name,
                        'error' => $result['message'],
                        'insufficient_materials' => $result['insufficient_materials'] ?? []
                    ];
                    
                    \Log::error("Failed to deduct materials for product {$product->name} in order {$order->id}", [
                        'product_id' => $product->id,
                        'quantity' => $quantity,
                        'error' => $result['message']
                    ]);
                } else {
                    \Log::info("Materials deducted successfully for product {$product->name}", [
                        'product_id' => $product->id,
                        'quantity' => $quantity,
                        'message' => $result['message']
                    ]);

                    // Collect component stock updates for UI sync if provided
                    if (!empty($result['components'])) {
                        foreach ($result['components'] as $comp) {
                            $updatedComponents[] = $comp;
                        }
                    }
                }
            }
            
            if (!$allMaterialsDeducted) {
                \Log::warning("Some materials could not be deducted for order {$order->id}", [
                    'order_id' => $order->id,
                    'failed_products' => $failedProducts
                ]);
                
                // You might want to show a warning to the user here
                // but still proceed with the order validation
            }
            
            \Log::info("Material deduction process completed for order {$order->id}", [
                'order_id' => $order->id,
                'products_count' => $order->products->count(),
                'all_successful' => $allMaterialsDeducted,
                'user_id' => auth()->id()
            ]);
            
        } catch (\Throwable $e) {
            \Log::error("Material deduction process failed for order {$order->id}: {$e->getMessage()}", [
                'order_id' => $order->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            // Don't fail the validation if material deduction fails, but log the error
            \Log::warning("Order validation completed but material deduction failed", [
                'order_id' => $order->id,
                'error' => $e->getMessage()
            ]);
        }
        
        // Check and apply loyalty discount if customer has 5 stamps
        try {
            $loyaltyService = new \App\Services\LoyaltyService();
            
            // Find customer's loyalty card
            $loyaltyCard = null;
            if ($order->user_id) {
                $loyaltyCard = \App\Models\LoyaltyCard::where('user_id', $order->user_id)->where('status', 'active')->first();
            } else {
                // For walk-in customers, try to find by email from notes
                $notes = $order->notes ?? '';
                if (preg_match('/Email:\s*([^;,\s]+@[^;,\s]+)/', $notes, $matches)) {
                    $email = trim($matches[1]);
                    $user = \App\Models\User::where('email', $email)->first();
                    if ($user) {
                        $loyaltyCard = \App\Models\LoyaltyCard::where('user_id', $user->id)->where('status', 'active')->first();
                    }
                }
            }
            
            // Apply discount if customer has 5 stamps
            if ($loyaltyCard && $loyaltyCard->stamps_count >= 5) {
                $order->load('products');
                $bouquets = $order->products->filter(function($product) {
                    return strtolower($product->category) === 'bouquet' && !str_contains(strtolower($product->category), 'mini');
                });
                
                if ($bouquets->isNotEmpty()) {
                    $discountedProduct = $bouquets->sortByDesc('price')->first();
                    $discountAmount = $discountedProduct->price * 0.5;
                    
                    // Apply discount to the order total
                    $order->total_amount = max(0, $order->total_amount - $discountAmount);
                    $order->save();
                    
                    // Redeem the loyalty card (reset stamps to 0)
                    $loyaltyService->redeem($loyaltyCard, $order, $discountAmount);
                    
                    \Log::info("Loyalty discount applied for walk-in order {$order->id}", [
                        'discount_amount' => $discountAmount,
                        'product' => $discountedProduct->name,
                        'new_total' => $order->total_amount
                    ]);
                }
            }
            
            // Issue loyalty stamp if eligible (for walk-in orders)
            $loyaltyService->issueStampIfEligible($order->fresh(['products']));
        } catch (\Throwable $e) {
            \Log::error("Loyalty processing failed for walk-in order {$order->id}: {$e->getMessage()}");
        }
        
        // If AJAX, return JSON immediately with updates for UI
        if ($request->ajax() || $request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Order validated successfully!',
                'order_id' => $order->id,
                'updated_components' => $updatedComponents,
            ]);
        }

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
        
        // Check if this is an AJAX request
        if ($request->ajax() || $request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Order validated successfully!',
                'order_id' => $order->id,
            ]);
        }
        
        // After validation/assignment, go to Create Invoice page
        $route = auth()->user()->role === 'admin' ? 'admin.orders.walkin.sales_order' : 'clerk.orders.walkin.create_invoice';
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
        // For admin, create a new order and redirect to create invoice page
        if (str_contains(request()->url(), '/admin/')) {
            // Create a new walk-in order with basic info
            $order = \App\Models\Order::create([
                'user_id' => auth()->id(),
                'total_price' => 0,
                'status' => 'quotation',
                'order_status' => 'pending',
                'type' => 'walk-in',
                'payment_status' => 'pending',
                'payment_method' => 'cash',
                'notes' => 'New walk-in order',
            ]);
            
            // Create initial status history entry
            $order->statusHistories()->create([
                'status' => 'quotation',
                'message' => 'Order created and pending quotation',
            ]);
            
            // Redirect to create invoice page
            return redirect()->route('admin.orders.walkin.invoice', $order);
        }
        
        // For clerk, mirror admin behavior: create a new order then go to create-invoice
        $order = \App\Models\Order::create([
            'user_id' => auth()->id(),
            'total_price' => 0,
            'status' => 'quotation',
            'order_status' => 'pending',
            'type' => 'walk-in',
            'payment_status' => 'pending',
            'payment_method' => 'cash',
            'notes' => 'New walk-in order',
        ]);

        $order->statusHistories()->create([
            'status' => 'quotation',
            'message' => 'Order created and pending quotation',
        ]);

        return redirect()->route('clerk.orders.walkin.create_invoice', $order);
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
            'order_status' => 'pending', // Add missing order_status field
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
        
        \Log::info('Order created successfully', [
            'order_id' => $order->id,
            'requestUrl' => $requestUrl,
            'refererUrl' => $refererUrl
        ]);
        
        if (str_contains($requestUrl, '/admin/') || str_contains($refererUrl, '/admin/')) {
            // Admin has a GET route named 'orders.walkin.invoice'
            $route = 'admin.orders.walkin.sales_order';
            \Log::info('Redirecting to admin route', ['route' => $route, 'order_id' => $order->id]);
        } else {
            // Clerk should be redirected to the GET screen to create invoice
            // Route name: clerk.orders.walkin.create_invoice
            $route = 'clerk.orders.walkin.create_invoice';
            \Log::info('Redirecting to clerk route', ['route' => $route, 'order_id' => $order->id]);
        }

        try {
            $redirectUrl = route($route, $order);
            \Log::info('Redirect URL generated', ['url' => $redirectUrl]);
            return redirect($redirectUrl);
        } catch (\Exception $e) {
            \Log::error('Redirect failed', [
                'route' => $route,
                'order_id' => $order->id,
                'error' => $e->getMessage()
            ]);
            // Fallback redirect
            return redirect()->back()->with('error', 'Order created but redirect failed. Order ID: ' . $order->id);
        }
    }
    
    /**
     * Validate pickup time based on current time
     */
    private function validatePickupTime(Request $request)
    {
        $pickupData = $request->pickup_data ?? [];
        $pickupTime = $pickupData['time'] ?? null;
        $pickupDate = $pickupData['date'] ?? null;
        
        if (!$pickupTime || !$pickupDate) {
            throw new \Illuminate\Validation\ValidationException(
                validator([], []),
                ['pickup_time' => ['Pickup time and date are required for pickup orders.']]
            );
        }
        
        // Check if pickup date is today
        $today = date('Y-m-d');
        if ($pickupDate === $today) {
            // Parse pickup time (e.g., "02:00 PM" -> 14)
            $pickupHour = $this->parseTimeToHour($pickupTime);
            $currentHour = (int) date('H');
            $currentMinute = (int) date('i');
            
            // If it's the same hour, check minutes (need at least 30 minutes buffer)
            if ($pickupHour === $currentHour) {
                if ($currentMinute >= 30) {
                    throw new \Illuminate\Validation\ValidationException(
                        validator([], []),
                        ['pickup_time' => ['Pickup time must be at least 30 minutes after current time.']]
                    );
                }
            } elseif ($pickupHour <= $currentHour) {
                throw new \Illuminate\Validation\ValidationException(
                    validator([], []),
                    ['pickup_time' => ['Pickup time must be after current time.']]
                );
            }
        }
        
        // Check if pickup time is within shop hours (8 AM to 7 PM) for any day
        $pickupHour = $this->parseTimeToHour($pickupTime);
        if ($pickupHour < 8 || $pickupHour >= 19) { // 8 AM = 8, 7 PM = 19
            throw new \Illuminate\Validation\ValidationException(
                validator([], []),
                ['pickup_time' => ['Pickup time must be between 8:00 AM and 7:00 PM (shop hours).']]
            );
        }
    }
    
    /**
     * Parse time string to hour (24-hour format)
     */
    private function parseTimeToHour($timeString)
    {
        // Convert "02:00 PM" to 24-hour format
        $time = \DateTime::createFromFormat('h:i A', $timeString);
        return $time ? (int) $time->format('H') : 0;
    }
}



