<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\NotificationController;
use App\Http\Controllers\Clerk\ClerkController;
use App\Http\Controllers\Customer\CustomerController;
use App\Http\Controllers\Driver\DriverController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\DeliveryController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Customer\CartController;
use App\Http\Controllers\Customer\AccountController;
use App\Http\Controllers\Customer\CustomerNotificationController;
use App\Http\Controllers\Customer\AddressController;
use App\Http\Controllers\Customer\CheckoutController;
use App\Http\Controllers\Customer\PaymentController;
use App\Http\Controllers\Clerk\PurchaseOrderController;
use App\Http\Controllers\Customer\ChatController;
use App\Http\Controllers\PhoneAuthController;

Route::get('/', function () {
    return view('welcome');
});

// Serve storage files - fallback route if symlink doesn't work
Route::get('/storage/{path}', function ($path) {
    // Try multiple possible paths
    $possiblePaths = [
        // From frontend directory
        base_path('../backend/storage/app/public/' . $path),
        // From root directory
        base_path('backend/storage/app/public/' . $path),
        // Absolute path from backend
        __DIR__ . '/../../backend/storage/app/public/' . $path,
    ];
    
    $filePath = null;
    foreach ($possiblePaths as $possiblePath) {
        if (file_exists($possiblePath)) {
            $filePath = $possiblePath;
            break;
        }
    }
    
    if (!$filePath || !file_exists($filePath)) {
        \Log::warning('Storage file not found', [
            'requested_path' => $path,
            'tried_paths' => $possiblePaths,
            'current_dir' => __DIR__,
            'base_path' => base_path(),
        ]);
        abort(404, "File not found: $path");
    }
    
    // Security: prevent directory traversal
    $realPath = realpath($filePath);
    $storageRoot = realpath(base_path('../backend/storage/app/public')) ?: realpath(__DIR__ . '/../../backend/storage/app/public');
    if (!$storageRoot || strpos($realPath, $storageRoot) !== 0) {
        abort(403, 'Access denied');
    }
    
    $mimeType = mime_content_type($filePath);
    if (!$mimeType) {
        $mimeType = 'application/octet-stream';
    }
    
    \Log::info('Serving storage file', [
        'path' => $path,
        'file_path' => $filePath,
        'mime_type' => $mimeType,
    ]);
    
    return response()->file($filePath, [
        'Content-Type' => $mimeType,
    ]);
})->where('path', '.*');

// Public FAQ page
Route::view('/faq', 'customer.faq')->name('faq');

// Map demo route
Route::get('/map-demo', function () {
    return view('map-demo');
});

// Simple test endpoint
Route::get('/api/test', function () {
    return response()->json(['message' => 'API is working!']);
});

// Compact analytics for dashboard widgets
Route::get('/api/analytics/compact', function () {
    $today = \Carbon\Carbon::today();
    $thisMonth = \Carbon\Carbon::now()->startOfMonth();
    // Daily last 7
    $daily = [];
    for ($i=6;$i>=0;$i--) {
        $day = \Carbon\Carbon::now()->subDays($i);
        $revenue = \App\Models\Order::whereBetween('created_at', [$day->copy()->startOfDay(), $day->copy()->endOfDay()])
            ->where('status','!=','cancelled')->sum('total_price');
        $daily[] = ['day'=>$day->format('M d'),'revenue'=>$revenue];
    }
    // Monthly last 6
    $monthly = [];
    for ($i=5;$i>=0;$i--) {
        $m = \Carbon\Carbon::now()->subMonths($i);
        $revenue = \App\Models\Order::whereBetween('created_at', [$m->copy()->startOfMonth(), $m->copy()->endOfMonth()])
            ->where('status','!=','cancelled')->sum('total_price');
        $monthly[] = ['month'=>$m->format('M Y'),'revenue'=>$revenue];
    }
    return response()->json(['daily'=>$daily,'monthly'=>$monthly]);
});




// Map API routes (temporary fix)
Route::post('/api/map/geocode', [\App\Http\Controllers\MapController::class, 'geocode']);
Route::get('/api/map/test', function() {
    return response()->json(['success' => true, 'message' => 'API is working', 'timestamp' => now()]);
});
Route::post('/api/map/route', [\App\Http\Controllers\MapController::class, 'getRoute']);
Route::post('/api/map/shipping-calculate', [\App\Http\Controllers\MapController::class, 'calculateShippingWithDistance']);

// PUBLIC CUSTOMER CART ROUTE
Route::get('/cart', [CartController::class, 'index'])->name('customer.cart.index');
// PUBLIC CUSTOMER ACCOUNT ROUTE
Route::get('/account', [AccountController::class, 'index'])->name('customer.account.index');
Route::post('/account/update-picture', [AccountController::class, 'updatePicture'])->name('customer.account.update_picture');

// Authentication Routes
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('register.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Social Login (Google/Facebook)
Route::get('auth/{provider}', [App\Http\Controllers\AuthController::class, 'redirectToProvider'])->name('social.redirect');
Route::get('auth/{provider}/callback', [App\Http\Controllers\AuthController::class, 'handleProviderCallback'])->name('social.callback');

// Password Reset Routes
Route::get('password/reset', [App\Http\Controllers\AuthController::class, 'showLinkRequestForm'])->name('password.request');
Route::post('password/email', [App\Http\Controllers\AuthController::class, 'sendResetLinkEmail'])->name('password.email');
Route::get('password/reset/{token}', [App\Http\Controllers\AuthController::class, 'showResetForm'])->name('password.reset');
Route::post('password/reset', [App\Http\Controllers\AuthController::class, 'reset'])->name('password.update');

// Admin Routes
Route::middleware(['web', 'auth', \App\Http\Middleware\AdminMiddleware::class])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    // Analytics page removed; key analytics moved to dashboard
    Route::resource('products', ProductController::class);
    Route::post('products/{product}/images/update', [ProductController::class, 'updateImages'])->name('products.updateImages');
    Route::delete('products/{product}/images/delete', [ProductController::class, 'deleteImage'])->name('products.deleteImage');
    Route::delete('products/{product}/images/delete-all', [ProductController::class, 'deleteAllImages'])->name('products.deleteAllImages');
    
    // Product Approval Routes
    Route::get('/api/products/pending', [\App\Http\Controllers\Admin\AdminProductApprovalController::class, 'getPendingProducts'])->name('api.products.pending');
    Route::get('/api/products/approved', [\App\Http\Controllers\Admin\AdminProductApprovalController::class, 'getApprovedProducts'])->name('api.products.approved');
    Route::post('/api/products/{product}/approve', [\App\Http\Controllers\Admin\AdminProductApprovalController::class, 'approveProduct'])->name('api.products.approve');
    Route::delete('/api/products/{product}/disapprove', [\App\Http\Controllers\Admin\AdminProductApprovalController::class, 'disapproveProduct'])->name('api.products.disapprove');
    Route::get('/api/products/{product}/details', [\App\Http\Controllers\Admin\AdminProductApprovalController::class, 'getProductDetails'])->name('api.products.details');
    Route::get('/api/products/{product}/compositions', [\App\Http\Controllers\Admin\AdminProductApprovalController::class, 'getProductCompositions'])->name('api.products.compositions');
    
    // Pending Product Changes Routes
    Route::get('/api/product-changes/pending', [ProductController::class, 'getPendingProductChanges'])->name('api.product-changes.pending');
    Route::post('/api/product-changes/{id}/approve', [ProductController::class, 'approveProductChange'])->name('api.product-changes.approve');
    Route::post('/api/product-changes/{id}/reject', [ProductController::class, 'rejectProductChange'])->name('api.product-changes.reject');
    Route::get('/api/product-changes/{id}/details', [ProductController::class, 'getProductChangeDetails'])->name('api.product-changes.details');
    
    // Admin Walk-in Order Creation (must be before resource routes)
    Route::get('orders/create', [\App\Http\Controllers\Clerk\OrderFlowController::class, 'createWalkinOrder'])->name('orders.create');
    // Delivery-only walk-in order (mirrors customer checkout)
    Route::get('orders/walkin/delivery', [\App\Http\Controllers\Clerk\OrderFlowController::class, 'createWalkinDelivery'])->name('orders.walkin.delivery');
    Route::post('orders/store', [\App\Http\Controllers\Clerk\OrderFlowController::class, 'storeWalkinOrder'])->name('orders.store');
    
    // Admin Online Order Flow
    Route::get('orders/{order}/online/invoice', [\App\Http\Controllers\Clerk\OrderFlowController::class, 'onlineInvoice'])->name('orders.online.invoice');
    Route::get('orders/{order}/online/validate', [\App\Http\Controllers\Clerk\OrderFlowController::class, 'onlineValidate'])->name('orders.online.validate');
    Route::post('orders/{order}/online/validate/confirm', [\App\Http\Controllers\Clerk\OrderFlowController::class, 'onlineValidateConfirm'])->name('orders.online.validate.confirm');
    Route::get('orders/{order}/online/done', [\App\Http\Controllers\Clerk\OrderFlowController::class, 'onlineDone'])->name('orders.online.done');
    
    // Admin Walk-in Order Flow
    Route::get('orders/{order}/walkin/quotation', [\App\Http\Controllers\Clerk\OrderFlowController::class, 'walkinQuotation'])->name('orders.walkin.quotation');
    Route::get('orders/{order}/walkin/invoice', [\App\Http\Controllers\Clerk\OrderFlowController::class, 'walkinCreateInvoice'])->name('orders.walkin.invoice');
    // Alias route so URL can read 'sales-order' instead of 'invoice'
    Route::get('orders/{order}/walkin/sales-order', [\App\Http\Controllers\Clerk\OrderFlowController::class, 'walkinCreateInvoice'])->name('orders.walkin.sales_order');
    Route::post('orders/{order}/walkin/update-invoice', [\App\Http\Controllers\Clerk\OrderFlowController::class, 'updateWalkinInvoice'])->name('orders.walkin.update_invoice');
    Route::get('orders/{order}/walkin/validate', [\App\Http\Controllers\Clerk\OrderFlowController::class, 'walkinValidate'])->name('orders.walkin.validate');
    Route::post('orders/{order}/walkin/validate/confirm', [\App\Http\Controllers\Clerk\OrderFlowController::class, 'walkinValidateConfirm'])->name('orders.walkin.validate.confirm');
    Route::get('orders/{order}/walkin/done', [\App\Http\Controllers\Clerk\OrderFlowController::class, 'walkinDone'])->name('orders.walkin.done');
    
    // Admin Sales Orders (unified tabbed interface)
    Route::get('sales-orders', [\App\Http\Controllers\Admin\SalesOrdersController::class, 'index'])->name('sales-orders.index');
    Route::get('sales-orders/{order}', [\App\Http\Controllers\Admin\SalesOrdersController::class, 'show'])->name('sales-orders.show');
    Route::post('sales-orders/{order}/confirm', [\App\Http\Controllers\Admin\SalesOrdersController::class, 'confirm'])->name('sales-orders.confirm');
    
    // Admin Orders Resource (must be after specific routes)
    Route::resource('orders', OrderController::class)->except(['create', 'store']);
    Route::post('orders/{order}/approve', [OrderController::class, 'approve'])->name('orders.approve');
    Route::post('orders/{order}/validate', [OrderController::class, 'validateOrder'])->name('orders.validate');
    Route::get('orders/{order}/invoice', [OrderController::class, 'invoice'])->name('orders.invoice');
    // Create detailed invoice from Sales Order and redirect to invoice detail
    Route::post('orders/{order}/invoice/create', [\App\Http\Controllers\Admin\InvoiceController::class, 'createFromOrder'])->name('orders.invoice.create');
    Route::get('orders/{order}/invoice/view', [OrderController::class, 'viewInvoice'])->name('orders.invoice.view');
    Route::get('orders/{order}/invoice/download', [OrderController::class, 'downloadInvoice'])->name('orders.invoice.download');
    Route::get('/walk-in-orders', [OrderController::class, 'walkInIndex'])->name('walkInOrders.index');
    Route::resource('deliveries', DeliveryController::class);
    Route::resource('users', UserController::class);
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/{notification}/mark-as-read', [NotificationController::class, 'markAsRead'])->name('notifications.markAsRead')->where('notification', '[0-9]+');
    
    // Inventory Logs
    // Inventory Logs routes removed
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::post('/reports/sales', [ReportController::class, 'generateSalesReport'])->name('reports.sales.generate');
    Route::post('/reports/order-status', [ReportController::class, 'generateOrderStatusReport'])->name('reports.orderStatus');
    Route::post('/reports/product-performance', [ReportController::class, 'generateProductPerformanceReport'])->name('reports.productPerformance');
    Route::post('/reports/user-activity', [ReportController::class, 'generateUserActivityReport'])->name('reports.userActivity');
    Route::get('/reports/inventory', [ReportController::class, 'inventory'])->name('reports.inventory');
    
    // Admin Inventory Management
    Route::get('/inventory', [\App\Http\Controllers\Admin\AdminInventoryController::class, 'index'])->name('inventory.index');
    Route::post('/inventory/approve/{id}', [\App\Http\Controllers\Admin\AdminInventoryController::class, 'approve'])->name('admin.inventory.approve');
    Route::post('/inventory/reject/{id}', [\App\Http\Controllers\Admin\AdminInventoryController::class, 'reject'])->name('admin.inventory.reject');
    
    
    // Inventory Log Management
    Route::post('/inventory/approve-pending', function() {
        $count = 0;
        try {
            $logsQuery = \App\Models\InventoryLog::with('product')->orderBy('created_at');
            if (\Illuminate\Support\Facades\Schema::hasColumn('inventory_logs','status')) {
                $logsQuery->where('status','pending');
            }
            $logs = $logsQuery->get();
            foreach ($logs as $log) {
                if ($log->action === 'create') {
                    $data = (array)($log->new_values ?? []);
                    // Sanitize numeric fields
                    foreach (['price','cost_price','reorder_min','reorder_max','stock','qty_consumed','qty_damaged','qty_sold'] as $nf) {
                        if (!isset($data[$nf]) || $data[$nf] === '' || !is_numeric($data[$nf])) { $data[$nf] = 0; }
                        else { $data[$nf] = (int)$data[$nf]; }
                    }
                    $product = new \App\Models\Product();
                    foreach (['name','category','price','cost_price','reorder_min','reorder_max','stock','qty_consumed','qty_damaged','qty_sold'] as $f) {
                        if (array_key_exists($f, $data)) { $product->{$f} = $data[$f]; }
                    }
                    // Approve flags if columns exist
                    if (\Illuminate\Support\Facades\Schema::hasColumn('products','status')) { $product->status = true; }
                    if (\Illuminate\Support\Facades\Schema::hasColumn('products','is_approved')) { $product->is_approved = true; }
                    $product->save();
                } elseif ($log->action === 'edit' && $log->product) {
                    $data = $log->new_values ?? [];
                    foreach ($data as $k=>$v) { $log->product->{$k} = $v; }
                    $log->product->save();
                } elseif ($log->action === 'delete' && $log->product) {
                    $log->product->delete();
                }
                if (\Illuminate\Support\Facades\Schema::hasColumn('inventory_logs','status')) {
                    $log->status = 'approved';
                }
                $log->save();
                $count++;
            }
            return response()->json(['success'=>true,'applied'=>$count,'message'=>"Applied {$count} change(s)."]);
        } catch (\Throwable $e) {
            return response()->json(['success'=>false,'message'=>'Error: '.$e->getMessage()], 500);
        }
    })->name('admin.inventory.approve-pending');

    // Approve a single inventory log id (helper endpoint used by per-row approve button)
    Route::post('/inventory/approve-log/{log}', function(\App\Models\InventoryLog $log) {
        try {
            if (\Illuminate\Support\Facades\Schema::hasColumn('inventory_logs','status') && $log->status !== 'pending') {
                return response()->json(['success'=>false,'message'=>'Already processed']);
            }
            if ($log->action === 'create') {
                $data = (array)($log->new_values ?? []);
                foreach (['price','cost_price','reorder_min','reorder_max','stock','qty_consumed','qty_damaged','qty_sold'] as $nf) {
                    if (!isset($data[$nf]) || $data[$nf] === '' || !is_numeric($data[$nf])) { $data[$nf] = 0; }
                    else { $data[$nf] = (int)$data[$nf]; }
                }
                $product = new \App\Models\Product();
                foreach (['name','category','price','cost_price','reorder_min','reorder_max','stock','qty_consumed','qty_damaged','qty_sold'] as $f) {
                    if (array_key_exists($f, $data)) { $product->{$f} = $data[$f]; }
                }
                if (\Illuminate\Support\Facades\Schema::hasColumn('products','is_approved')) { $product->is_approved = true; }
                if (\Illuminate\Support\Facades\Schema::hasColumn('products','status')) { $product->status = true; }
                $product->save();
            } elseif ($log->action === 'edit' && $log->product) {
                // Sanitize the new values to handle empty strings
                $sanitizedValues = [];
                foreach ((array)($log->new_values ?? []) as $k => $v) {
                    // Convert empty strings to 0 for numeric fields
                    if (in_array($k, ['qty_consumed', 'qty_damaged', 'qty_sold', 'stock', 'reorder_min', 'reorder_max', 'price', 'cost_price'])) {
                        $sanitizedValues[$k] = $v === '' || $v === null ? 0 : (int)$v;
                    } else {
                        $sanitizedValues[$k] = $v;
                    }
                }
                foreach ($sanitizedValues as $k => $v) { 
                    $log->product->{$k} = $v; 
                }
                $log->product->save();
            } elseif ($log->action === 'delete' && $log->product) {
                $log->product->delete();
            }
            if (\Illuminate\Support\Facades\Schema::hasColumn('inventory_logs','status')) { $log->status = 'approved'; }
            $log->save();
            return response()->json(['success'=>true]);
        } catch (\Throwable $e) {
            return response()->json(['success'=>false,'message'=>$e->getMessage()], 500);
        }
    })->name('admin.inventory.approve-single');

    // Reject a single inventory log
    Route::post('/inventory/reject-log/{log}', function(\App\Models\InventoryLog $log) {
        try {
            if (\Illuminate\Support\Facades\Schema::hasColumn('inventory_logs','status')) {
                $log->status = 'rejected';
                $log->save();
            } else {
                // No status column; just delete the log to simulate rejection
                $log->delete();
            }
            return response()->json(['success'=>true]);
        } catch (\Throwable $e) {
            return response()->json(['success'=>false,'message'=>$e->getMessage()], 500);
        }
    })->name('admin.inventory.reject-single');
    
    
    // Admin Customize (bouquet components)
    Route::get('/customize', [\App\Http\Controllers\Admin\CustomizeController::class, 'index'])->name('customize.index');
    Route::post('/customize', [\App\Http\Controllers\Admin\CustomizeController::class, 'store'])->name('customize.store');
    Route::post('/customize/update-assembling-fee', [\App\Http\Controllers\Admin\CustomizeController::class, 'updateAssemblingFee'])->name('customize.update-assembling-fee');
    Route::delete('/customize/bulk-delete', [\App\Http\Controllers\Admin\CustomizeController::class, 'bulkDelete'])->name('customize.bulk-delete');
    Route::put('/customize/{id}', [\App\Http\Controllers\Admin\CustomizeController::class, 'update'])->name('customize.update');
    Route::delete('/customize/{id}', [\App\Http\Controllers\Admin\CustomizeController::class, 'destroy'])->name('customize.destroy');
    Route::get('/inventory/pending-count', [\App\Http\Controllers\Admin\AdminInventoryController::class, 'getPendingCount'])->name('inventory.pending-count');
    
    Route::get('/reports/sales', [ReportController::class, 'sales'])->name('reports.sales');
    Route::post('/reports/generate-detailed-sales', [ReportController::class, 'generateDetailedSales'])->name('reports.generateDetailedSales');
    Route::get('/chatbox', [AdminController::class, 'chatbox'])->name('chatbox');
    Route::post('/chatbox/send', [AdminController::class, 'sendMessage'])->name('chatbox.send');
    Route::get('orders/{order}/status-history', [OrderController::class, 'statusHistory'])->name('orders.statusHistory');
    Route::post('orders/{order}/assign-delivery', [OrderController::class, 'assignDelivery'])->name('orders.assignDelivery');
    Route::post('orders/{order}/mark-ready', [OrderController::class, 'markReady'])->name('orders.mark-ready');
    Route::post('orders/{order}/mark-done', [OrderController::class, 'markDone'])->name('orders.mark-done');
    Route::post('/inventory', [ProductController::class, 'storeInventory'])->name('inventory.store');
    Route::put('/inventory/product/{product}', [ProductController::class, 'updateInventory'])->name('inventory.update');
    Route::delete('/inventory/product/{product}', [ProductController::class, 'destroyInventory'])->name('inventory.destroy');
    Route::post('/inventory/approve-changes', [\App\Http\Controllers\Admin\AdminInventoryController::class, 'approveChanges'])->name('inventory.approve-changes');

    // Promoted banners management
    Route::resource('promoted-banners', \App\Http\Controllers\Admin\PromotedBannerController::class)->names('admin.promoted-banners');
    
    // API endpoints for product composition
    Route::get('/api/categories', [ProductController::class, 'getCategories'])->name('api.categories');
    Route::get('/api/inventory/{category?}', [ProductController::class, 'getInventoryByCategory'])->name('api.inventory.by_category');
    
    // Order Status Management
    Route::post('orders/{order}/approve', [AdminController::class, 'approveOrder'])->name('orders.approve');
    Route::post('orders/{order}/assign-driver', [AdminController::class, 'assignDriver'])->name('orders.assign-driver');
    Route::post('orders/{order}/complete', [AdminController::class, 'completeOrder'])->name('orders.complete');
    
    // Invoice Management
    Route::resource('invoices', \App\Http\Controllers\Admin\InvoiceController::class);
    Route::post('orders/{order}/create-invoice', [\App\Http\Controllers\Admin\InvoiceController::class, 'createInvoice'])->name('orders.create-invoice');
    Route::post('invoices/{invoice}/register-payment', [\App\Http\Controllers\Admin\InvoiceController::class, 'registerPayment'])->name('invoices.register-payment');
    Route::get('api/payment-modes', [\App\Http\Controllers\Admin\InvoiceController::class, 'getPaymentModes'])->name('api.payment-modes');
    // Loyalty management
    Route::get('loyalty', [\App\Http\Controllers\Admin\LoyaltyController::class, 'index'])->name('loyalty.index');
    Route::put('loyalty/{card}/adjust', [\App\Http\Controllers\Admin\LoyaltyController::class, 'adjust'])->name('loyalty.adjust');
    Route::get('loyalty/{card}/history', [\App\Http\Controllers\Admin\LoyaltyController::class, 'history'])->name('loyalty.history');
    
    // Payment Verification Management
    
    // Return Management
    Route::get('returns', [\App\Http\Controllers\Admin\ReturnManagementController::class, 'index'])->name('returns.index');
    Route::get('returns/{order}', [\App\Http\Controllers\Admin\ReturnManagementController::class, 'show'])->name('returns.show');
    Route::post('returns/{order}/update-status', [\App\Http\Controllers\Admin\ReturnManagementController::class, 'updateStatus'])->name('returns.update-status');
    Route::post('returns/{order}/process-refund', [\App\Http\Controllers\Admin\ReturnManagementController::class, 'processRefund'])->name('returns.process-refund');
    Route::get('returns/analytics', [\App\Http\Controllers\Admin\ReturnManagementController::class, 'analytics'])->name('returns.analytics');
    Route::get('returns/analytics/export', [\App\Http\Controllers\Admin\ReturnManagementController::class, 'exportAnalytics'])->name('returns.analytics.export');
});

// Invoice and Payment routes (accessible to authenticated users)
Route::middleware(['auth'])->group(function () {
    Route::get('invoices', [\App\Http\Controllers\Admin\InvoiceController::class, 'index'])->name('invoices.index');
    Route::get('invoices/{invoice}', [\App\Http\Controllers\Admin\InvoiceController::class, 'show'])->name('invoices.show');
    Route::get('invoices/{invoice}/payment', [\App\Http\Controllers\Admin\InvoiceController::class, 'paymentWizard'])->name('invoices.payment');
    Route::post('invoices/{invoice}/payment', [\App\Http\Controllers\Admin\InvoiceController::class, 'processPayment'])->name('invoices.payment.process');
    Route::get('invoices/{invoice}/payment/callback', [\App\Http\Controllers\Admin\InvoiceController::class, 'paymentCallback'])->name('invoices.payment.callback');
});

// Clerk Routes
Route::middleware(['web', 'auth', \App\Http\Middleware\ClerkMiddleware::class])->prefix('clerk')->name('clerk.')->group(function () {
    Route::get('/dashboard', [ClerkController::class, 'dashboard'])->name('dashboard');
    // FIX: Use ClerkController for product_catalog
    Route::get('/product_catalog', [ClerkController::class, 'productCatalog'])->name('product_catalog.index');
    Route::post('/product_catalog', [ClerkController::class, 'storeProduct'])->name('product_catalog.store');
    Route::put('/product_catalog/{id}', [ClerkController::class, 'updateCatalogProduct'])->name('product_catalog.update');
    Route::delete('/product_catalog/{id}', [ClerkController::class, 'destroyProduct'])->name('product_catalog.destroy');
    // Fallback: allow DELETE without ID, read id from request body
    Route::delete('/product_catalog', [ClerkController::class, 'destroyProductByForm'])->name('product_catalog.destroy.noid');
    
    // Clerk API routes
    Route::get('/api/products/{product}/details', [ClerkController::class, 'getProductDetails'])->name('api.products.details');
    Route::get('/api/products/{product}/compositions', [ClerkController::class, 'getProductCompositions'])->name('api.products.compositions');
    Route::resource('products', ProductController::class);
    Route::post('products/{product}/images/update', [ProductController::class, 'updateImages'])->name('products.updateImages');
    Route::delete('products/{product}/images/delete', [ProductController::class, 'deleteImage'])->name('products.deleteImage');
    Route::delete('products/{product}/images/delete-all', [ProductController::class, 'deleteAllImages'])->name('products.deleteAllImages');
    Route::get('/inventory', [ClerkController::class, 'inventory'])->name('inventory.manage');
    Route::get('/inventory/check-approval-status', [ClerkController::class, 'checkApprovalStatus'])->name('clerk.inventory.check-approval');
    Route::post('/inventory', [ClerkController::class, 'storeInventory'])->name('inventory.store');
    Route::put('/inventory/{product}', [ClerkController::class, 'updateProduct'])->name('inventory.update');
    Route::delete('/inventory/{product}', [\App\Http\Controllers\ProductController::class, 'destroyInventory'])->name('inventory.destroy');
    Route::post('/inventory/submit-changes', [ClerkController::class, 'submitInventoryChanges'])->name('inventory.submit-changes');
    Route::get('/orders', [ClerkController::class, 'orders'])->name('orders.index');
    // Clerk Walk-in Order Creation (same as admin)
    Route::get('orders/create', [\App\Http\Controllers\Clerk\OrderFlowController::class, 'createWalkinOrder'])->name('orders.create');
    // Delivery-only walk-in order (mirrors customer checkout)
    Route::get('orders/walkin/delivery', [\App\Http\Controllers\Clerk\OrderFlowController::class, 'createWalkinDelivery'])->name('orders.walkin.delivery');
    Route::post('orders/store', [\App\Http\Controllers\Clerk\OrderFlowController::class, 'storeWalkinOrder'])->name('orders.store');
    
    // Clerk Walk-in Order Flow (same as admin)
    Route::get('orders/{order}/walkin/quotation', [\App\Http\Controllers\Clerk\OrderFlowController::class, 'walkinQuotation'])->name('orders.walkin.quotation');
    Route::get('orders/{order}/walkin/invoice', [\App\Http\Controllers\Clerk\OrderFlowController::class, 'walkinCreateInvoice'])->name('orders.walkin.invoice');
    Route::get('orders/{order}/walkin/sales-order', [\App\Http\Controllers\Clerk\OrderFlowController::class, 'walkinCreateInvoice'])->name('orders.walkin.sales_order');
    Route::post('orders/{order}/walkin/update-invoice', [\App\Http\Controllers\Clerk\OrderFlowController::class, 'updateWalkinInvoice'])->name('orders.walkin.update_invoice');
    Route::get('orders/{order}/walkin/validate', [\App\Http\Controllers\Clerk\OrderFlowController::class, 'walkinValidate'])->name('orders.walkin.validate');
    Route::post('orders/{order}/walkin/validate/confirm', [\App\Http\Controllers\Clerk\OrderFlowController::class, 'walkinValidateConfirm'])->name('orders.walkin.validate.confirm');
    Route::get('orders/{order}/walkin/done', [\App\Http\Controllers\Clerk\OrderFlowController::class, 'walkinDone'])->name('orders.walkin.done');
    
    // Clerk Sales Orders (mirror admin routes to show same screen after confirm)
    Route::get('sales-orders', [\App\Http\Controllers\Admin\SalesOrdersController::class, 'index'])->name('sales-orders.index');
    Route::get('sales-orders/{order}', [\App\Http\Controllers\Admin\SalesOrdersController::class, 'show'])->name('sales-orders.show');
    Route::post('sales-orders/{order}/confirm', [\App\Http\Controllers\Admin\SalesOrdersController::class, 'confirm'])->name('sales-orders.confirm');

    Route::resource('orders', OrderController::class)->except(['index','create','store']);
    Route::post('orders/{order}/approve', [OrderController::class, 'approve'])->name('orders.approve');
    Route::post('orders/{order}/validate', [OrderController::class, 'validateOrder'])->name('orders.validate');
    Route::get('orders/{order}/invoice', [OrderController::class, 'invoice'])->name('orders.invoice');
    Route::get('orders/{order}/invoice/view', [OrderController::class, 'viewInvoice'])->name('orders.invoice.view');
    Route::get('orders/{order}/invoice/download', [OrderController::class, 'downloadInvoice'])->name('orders.invoice.download');
    
    // Clerk Invoice Management (same as admin)
    Route::post('orders/{order}/invoice/create', [\App\Http\Controllers\Clerk\InvoiceController::class, 'createFromOrder'])->name('orders.invoice.create');
    
    // Clerk Invoice Routes
    Route::get('invoices', [\App\Http\Controllers\Clerk\InvoiceController::class, 'index'])->name('invoices.index');
    Route::get('invoices/{invoice}', [\App\Http\Controllers\Clerk\InvoiceController::class, 'show'])->name('invoices.show');
    Route::get('invoices/{invoice}/payment', [\App\Http\Controllers\Clerk\InvoiceController::class, 'paymentWizard'])->name('invoices.payment');
    Route::post('invoices/{invoice}/payment', [\App\Http\Controllers\Clerk\InvoiceController::class, 'processPayment'])->name('invoices.payment.process');
    Route::get('invoices/{invoice}/payment/callback', [\App\Http\Controllers\Clerk\InvoiceController::class, 'paymentCallback'])->name('invoices.payment.callback');
    Route::post('/orders/{order}/validate-recipient', [OrderController::class, 'validateRecipient'])->name('orders.validate-recipient');
    Route::resource('users', UserController::class);
    Route::get('/notifications', [ClerkController::class, 'notifications'])->name('notifications.index');
    Route::post('/notifications/{notification}/mark-as-read', [ClerkController::class, 'markNotificationAsRead'])->name('notifications.markAsRead');
    Route::delete('/notifications/delete-all', [ClerkController::class, 'deleteAllNotifications'])->name('notifications.deleteAll');
    Route::get('/profile', [ClerkController::class, 'editProfile'])->name('profile.edit');
    Route::get('/sales', [ClerkController::class, 'sales'])->name('sales.index');
    Route::resource('deliveries', DeliveryController::class);
    Route::put('/profile', [ClerkController::class, 'updateProfile'])->name('profile.update');
    Route::put('/profile/password', [ClerkController::class, 'updatePassword'])->name('profile.password');
    Route::post('/orders/{order}/assign-delivery', [ClerkController::class, 'assignDelivery'])->name('orders.assignDelivery');
    Route::get('/purchase-orders', [PurchaseOrderController::class, 'index'])->name('purchase_orders.index');
    Route::get('/purchase-orders/create', [PurchaseOrderController::class, 'create'])->name('purchase_orders.create');
    // Clerk Customize (bouquet components)
    Route::get('/customize', [\App\Http\Controllers\Clerk\CustomizeController::class, 'index'])->name('customize.index');
    Route::post('/customize', [\App\Http\Controllers\Clerk\CustomizeController::class, 'store'])->name('customize.store');
    Route::delete('/customize/bulk-delete', [\App\Http\Controllers\Clerk\CustomizeController::class, 'bulkDelete'])->name('customize.bulk-delete');
    Route::put('/customize/{id}', [\App\Http\Controllers\Clerk\CustomizeController::class, 'update'])->name('customize.update');
    Route::delete('/customize/{id}', [\App\Http\Controllers\Clerk\CustomizeController::class, 'destroy'])->name('customize.destroy');
    Route::post('/purchase-orders', [PurchaseOrderController::class, 'store'])->name('purchase_orders.store');
    Route::get('/purchase-orders/{purchaseOrder}', [PurchaseOrderController::class, 'show'])->name('purchase_orders.show');
    Route::post('/purchase-orders/{purchaseOrder}/validate', [PurchaseOrderController::class, 'validateOrder'])->name('purchase_orders.validate');
    Route::get('orders/{order}/status-history', [OrderController::class, 'statusHistory'])->name('orders.statusHistory');
    Route::post('orders/{order}/mark-delivered', [App\Http\Controllers\OrderController::class, 'markDelivered'])->name('clerk.orders.markDelivered');
    
    // Order Status Management
    Route::post('orders/{order}/approve', [ClerkController::class, 'approveOrder'])->name('orders.approve');
    Route::post('orders/{order}/assign-driver', [ClerkController::class, 'assignDriver'])->name('orders.assign-driver');
    Route::post('orders/{order}/complete', [ClerkController::class, 'completeOrder'])->name('orders.complete');
    
    // Payment Management
    Route::get('orders/{order}/payment/form', [\App\Http\Controllers\PaymentController::class, 'showPaymentForm'])->name('payment.form');
    Route::post('orders/{order}/payment/register', [\App\Http\Controllers\PaymentController::class, 'registerPayment'])->name('payment.register');
    Route::get('orders/{order}/payment/history', [\App\Http\Controllers\PaymentController::class, 'getPaymentHistory'])->name('payment.history');

    // Invoice Management
    Route::get('invoices', [\App\Http\Controllers\Clerk\InvoiceController::class, 'index'])->name('invoices.index');
    Route::get('invoices/{invoice}', [\App\Http\Controllers\Clerk\InvoiceController::class, 'show'])->name('invoices.show');
    Route::get('invoices/{invoice}/download', [\App\Http\Controllers\Clerk\InvoiceController::class, 'download'])->name('invoices.download');

    // Clerk Orders Flow routes
    Route::prefix('orders')->name('orders.')->group(function() {
        // Online flow
        Route::get('{order}/online/validate', [\App\Http\Controllers\Clerk\OrderFlowController::class, 'onlineValidate'])->name('online.validate');
        Route::get('{order}/online/invoice', [\App\Http\Controllers\Clerk\OrderFlowController::class, 'onlineInvoice'])->name('online.invoice');
        Route::get('{order}/online/validate/confirm', [\App\Http\Controllers\Clerk\OrderFlowController::class, 'onlineValidateConfirm'])->name('online.validate.confirm');
        Route::post('{order}/online/validate/confirm', [\App\Http\Controllers\Clerk\OrderFlowController::class, 'onlineValidateConfirm'])->name('online.validate.confirm.post');
        Route::get('{order}/online/done', [\App\Http\Controllers\Clerk\OrderFlowController::class, 'onlineDone'])->name('online.done');
        Route::post('{order}/online/done', [\App\Http\Controllers\Clerk\OrderFlowController::class, 'onlineDone'])->name('online.done.post');

            // Walk-in flow
            Route::get('{order}/walkin/pending', [\App\Http\Controllers\Clerk\OrderFlowController::class, 'walkinPending'])->name('walkin.pending');
            Route::get('{order}/walkin/quotation', [\App\Http\Controllers\Clerk\OrderFlowController::class, 'walkinQuotation'])->name('walkin.quotation');
            Route::get('{order}/walkin/create-invoice', [\App\Http\Controllers\Clerk\OrderFlowController::class, 'walkinCreateInvoice'])->name('walkin.create_invoice');
            Route::post('{order}/walkin/invoice', [\App\Http\Controllers\Clerk\OrderFlowController::class, 'walkinInvoice'])->name('walkin.invoice.post');
            Route::get('{order}/walkin/validate', [\App\Http\Controllers\Clerk\OrderFlowController::class, 'walkinValidate'])->name('walkin.validate');
            Route::get('{order}/walkin/validate-confirmation', [\App\Http\Controllers\Clerk\OrderFlowController::class, 'walkinValidateConfirmation'])->name('walkin.validate_confirmation');
            Route::post('{order}/walkin/validate/confirm', [\App\Http\Controllers\Clerk\OrderFlowController::class, 'walkinValidateConfirm'])->name('walkin.validate.confirm');
            Route::get('{order}/walkin/done', [\App\Http\Controllers\Clerk\OrderFlowController::class, 'walkinDone'])->name('walkin.done');
    });
    
    // Create new walk-in order
    Route::get('create', [\App\Http\Controllers\Clerk\OrderFlowController::class, 'createWalkinOrder'])->name('create');
    Route::post('store', [\App\Http\Controllers\Clerk\OrderFlowController::class, 'storeWalkinOrder'])->name('store');
    
    // API endpoints for product composition (clerk access to inventory)
    Route::get('/api/categories', [\App\Http\Controllers\ProductController::class, 'getCategories'])->name('api.categories');
    Route::get('/api/inventory/{category?}', [\App\Http\Controllers\ProductController::class, 'getInventoryByCategory'])->name('api.inventory.by_category');
    
    // Clerk order approval and delivery assignment
    Route::post('/orders/{order}/approve', [\App\Http\Controllers\Clerk\ClerkController::class, 'approveOrder'])->name('orders.approve');
    Route::post('/orders/{order}/assign-delivery', [\App\Http\Controllers\Clerk\ClerkController::class, 'assignDelivery'])->name('orders.assignDelivery');
    Route::post('/orders/{order}/mark-ready', [\App\Http\Controllers\Clerk\ClerkController::class, 'markReady'])->name('orders.mark-ready');
    Route::post('/orders/{order}/mark-done', [\App\Http\Controllers\Clerk\ClerkController::class, 'markDone'])->name('orders.mark-done');
    
    // Invoice Management
    Route::resource('invoices', \App\Http\Controllers\Clerk\InvoiceController::class);
    Route::post('orders/{order}/create-invoice', [\App\Http\Controllers\Clerk\InvoiceController::class, 'createInvoice'])->name('orders.create-invoice');
    Route::post('invoices/{invoice}/register-payment', [\App\Http\Controllers\Clerk\InvoiceController::class, 'registerPayment'])->name('invoices.register-payment');
    Route::get('api/payment-modes', [\App\Http\Controllers\Clerk\InvoiceController::class, 'getPaymentModes'])->name('api.payment-modes');
    // Loyalty management
    Route::get('loyalty', [\App\Http\Controllers\Clerk\LoyaltyController::class, 'index'])->name('loyalty.index');
    Route::put('loyalty/{card}/adjust', [\App\Http\Controllers\Clerk\LoyaltyController::class, 'adjust'])->name('loyalty.adjust');
    Route::get('loyalty/{card}/history', [\App\Http\Controllers\Clerk\LoyaltyController::class, 'history'])->name('loyalty.history');
});

// Delivery mark as delivered
Route::post('/deliveries/{delivery}/delivered', [\App\Http\Controllers\DeliveryController::class, 'markDelivered'])->name('deliveries.markDelivered');

// Customer Routes
Route::middleware(['web', 'auth', \App\Http\Middleware\CustomerMiddleware::class])->prefix('customer')->name('customer.')->group(function () {
    // Main customer dashboard route with both names
    Route::get('/dashboard', [CustomerController::class, 'dashboard'])->name('dashboard');
    Route::get('/search-suggestions', [CustomerController::class, 'searchSuggestions'])->name('search-suggestions');
    Route::post('/save-location', [CustomerController::class, 'saveLocation'])->name('saveLocation');
    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');
    Route::get('/orders/{order}/invoice/view', [OrderController::class, 'viewInvoice'])->name('orders.invoice.view');
    Route::get('/orders/{order}/invoice/download', [OrderController::class, 'downloadInvoice'])->name('orders.invoice.download');
    Route::post('/orders/{order}/mark-received', [OrderController::class, 'markReceived'])->name('orders.mark-received');
    Route::get('/products', [ProductController::class, 'index'])->name('products.index');
    Route::get('/products/bestsellers', [ProductController::class, 'bestsellers'])->name('products.bestsellers');
    Route::get('/products/customize', [ProductController::class, 'customize'])->name('products.customize');
    Route::post('/products/customize', [ProductController::class, 'submitCustomization'])->name('products.customize.submit');
    Route::get('/products/bouquet-customize', [\App\Http\Controllers\Customer\CustomizeController::class, 'index'])->name('products.bouquet-customize');
    Route::post('/products/bouquet-customize', [\App\Http\Controllers\Customer\CustomizeController::class, 'store'])->name('products.bouquet-customize.store');
    Route::post('/products/bouquet-customize/add-to-cart', [\App\Http\Controllers\Customer\CustomizeController::class, 'addToCart'])->name('products.bouquet-customize.add-to-cart');
    Route::post('/products/bouquet-customize/buy-now', [\App\Http\Controllers\Customer\CustomizeController::class, 'buyNow'])->name('products.bouquet-customize.buy-now');
    Route::get('/loyalty', [\App\Http\Controllers\Customer\LoyaltyCardController::class, 'index'])->name('loyalty.index');
    Route::get('/loyalty/mechanics', [\App\Http\Controllers\Customer\LoyaltyCardController::class, 'mechanics'])->name('loyalty.mechanics');
    Route::get('/loyalty/status', [\App\Http\Controllers\Customer\LoyaltyCardController::class, 'status'])->name('loyalty.status');
    Route::get('/loyalty/can-redeem', [\App\Http\Controllers\Customer\LoyaltyCardController::class, 'canRedeem'])->name('loyalty.can-redeem');
    Route::get('/products/{product}', [ProductController::class, 'show'])->name('products.show');
    Route::get('/products/{product}/reviews', [ProductController::class, 'reviews'])->name('products.reviews');
    Route::get('/cart', [CartController::class, 'index'])->name('customer.cart.index');
    Route::post('/cart/add', [CartController::class, 'addToCart'])->name('cart.add');
    Route::post('/cart-items/{cartItem}/update-quantity', [CartController::class, 'updateQuantity'])->name('cart.updateQuantity');
    Route::delete('/cart-items/{cartItem}/remove', [CartController::class, 'removeItem'])->name('cart.remove');
    Route::delete('/cart-items/delete-all', [CartController::class, 'deleteAllItems'])->name('cart.deleteAll');
    // Favorites placeholder page
    Route::view('/favorites', 'customer.favorites')->name('favorites');
    // Favorites API
    Route::get('/api/favorites', [\App\Http\Controllers\Customer\FavoriteController::class, 'index'])->name('favorites.index');
    Route::get('/api/favorites/check/{product}', [\App\Http\Controllers\Customer\FavoriteController::class, 'check'])->name('favorites.check');
    Route::post('/api/favorites', [\App\Http\Controllers\Customer\FavoriteController::class, 'store'])->name('favorites.store');
    Route::delete('/api/favorites/{product}', [\App\Http\Controllers\Customer\FavoriteController::class, 'destroy'])->name('favorites.destroy');
    Route::get('/account', [AccountController::class, 'index'])->name('account');
    Route::post('/account/change-password', [AccountController::class, 'updatePassword'])->name('account.update_password');
    Route::post('/account/update', [AccountController::class, 'update'])->name('account.update');
    Route::resource('address_book', AddressController::class)->parameters(['address_book' => 'address']);
    Route::get('/address-book', [AddressController::class, 'index'])->name('address_book');
    Route::post('address_book/{address}/set-default', [AddressController::class, 'setDefault'])->name('address_book.set-default');

    // Notifications
    Route::get('/notifications', [CustomerNotificationController::class, 'index'])->name('notifications.index');
    Route::get('/notifications/list', [CustomerNotificationController::class, 'list'])->name('notifications.list');
    Route::post('/notifications/mark-all-as-read', [CustomerNotificationController::class, 'markAllAsRead'])->name('notifications.markAllAsRead');
    Route::delete('/notifications/delete-all', [CustomerNotificationController::class, 'destroyAll'])->name('notifications.deleteAll');
    Route::delete('/notifications/{id}', [CustomerNotificationController::class, 'destroy'])->name('notifications.delete');
    Route::post('/notifications/{id}/read', [CustomerNotificationController::class, 'markAsRead'])->name('notifications.markAsRead');
    Route::post('/notifications/{id}/unread', [CustomerNotificationController::class, 'markAsUnread'])->name('notifications.markAsUnread');
    Route::post('/notifications/{id}/hide', [CustomerNotificationController::class, 'hide'])->name('notifications.hide');
    Route::post('/notifications/{id}/unhide', [CustomerNotificationController::class, 'unhide'])->name('notifications.unhide');
    Route::get('/notifications/hidden', [CustomerNotificationController::class, 'getHidden'])->name('notifications.hidden');

    // Store Credit (removed)

    // Order Reviews
    Route::post('/orders/submit-review', [OrderController::class, 'submitReview'])->name('orders.submitReview');

    Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
    Route::get('/checkout/payment-method', [CheckoutController::class, 'paymentMethod'])->name('checkout.payment_method');
    Route::post('/checkout/process', [CheckoutController::class, 'processOrder'])->name('checkout.process');

    // Payment Gateway Routes
    Route::get('/payment/gcash/{order}', [PaymentController::class, 'gcashPayment'])->name('payment.gcash');
    Route::get('/payment/paymaya/{order}', [PaymentController::class, 'paymayaPayment'])->name('payment.paymaya');
    Route::get('/payment/seabank/{order}', [PaymentController::class, 'seabankPayment'])->name('payment.seabank');
    Route::get('/payment/rcbc/{order}', [PaymentController::class, 'rcbcPayment'])->name('payment.rcbc');
    Route::post('/payment/processing/{order}', [PaymentController::class, 'showProcessing'])->name('payment.processing');
    Route::post('/payment/process/{order}', [PaymentController::class, 'processPayment'])->name('payment.process');

    // Chat routes
    Route::get('/chat', [ChatController::class, 'index'])->name('chat.index');
    Route::post('/chat/send', [ChatController::class, 'send'])->name('chat.send');
    Route::get('/chat/messages', [ChatController::class, 'getMessages'])->name('chat.messages');
    
    Route::post('/account/update-picture', [AccountController::class, 'updatePicture'])->name('customer.account.update_picture');
    Route::post('/orders/{order}/upload-payment-proof', [OrderController::class, 'uploadPaymentProof'])->name('orders.uploadPaymentProof');
    Route::post('/orders/{order}/cancel', [OrderController::class, 'cancel'])->name('orders.cancel');
    Route::get('/orders/{order}/status-history', [OrderController::class, 'statusHistory'])->name('orders.statusHistory');
    Route::post('/orders/submit-review', [OrderController::class, 'submitReview'])->name('orders.submitReview');
    Route::post('/orders/submit-shop-review', [OrderController::class, 'submitShopReview'])->name('orders.submitShopReview');
    Route::post('/orders/{order}/update-delivery-schedule', [OrderController::class, 'updateDeliverySchedule'])->name('orders.update-delivery-schedule');
    Route::get('/track-orders', [OrderController::class, 'trackOrdersPage'])->name('trackOrders.page');
    
    // Payment Verification Routes
});

// Customer Login Routes
Route::get('/customer/login', [AuthController::class, 'showCustomerLogin'])->name('customer.login');
Route::post('/customer/login', [AuthController::class, 'customerLogin']);

// Staff Login Routes
Route::get('/staff/login', [AuthController::class, 'showStaffLogin'])->name('staff.login');
Route::post('/staff/login', [AuthController::class, 'staffLogin']);



// Driver Routes
Route::middleware(['web', 'auth', \App\Http\Middleware\DriverMiddleware::class])->prefix('driver')->name('driver.')->group(function () {
    Route::get('/dashboard', [DriverController::class, 'dashboard'])->name('dashboard');

    // Orders
    Route::get('/orders', [DriverController::class, 'orders'])->name('orders.index');
    Route::get('/orders/{order}', [DriverController::class, 'showOrder'])->name('orders.show');
    Route::post('/orders/{order}/accept', [DriverController::class, 'acceptOrder'])->name('orders.accept');
    Route::post('/orders/{order}/decline', [DriverController::class, 'declineOrder'])->name('orders.decline');
    Route::post('/orders/{order}/complete', [DriverController::class, 'completeOrder'])->name('orders.complete');
    Route::post('/orders/{order}/return', [DriverController::class, 'returnOrder'])->name('orders.return');
    
    // Return Order Routes
    Route::get('/orders/{order}/return-form', [\App\Http\Controllers\Driver\ReturnOrderController::class, 'show'])->name('orders.return.show');
    Route::post('/orders/{order}/return-store', [\App\Http\Controllers\Driver\ReturnOrderController::class, 'store'])->name('orders.return.store');
    

    // History
    Route::get('/history', [DriverController::class, 'history'])->name('history.index');
    Route::get('/history/{delivery}', [DriverController::class, 'showHistory'])->name('history.show');

    // Profile
    Route::get('/profile', [DriverController::class, 'profile'])->name('profile');
    Route::put('/profile', [DriverController::class, 'updateProfile'])->name('profile.update');
    Route::put('/profile/password', [DriverController::class, 'updatePassword'])->name('profile.password');

    // Delivery Status Updates
    Route::post('/deliveries/{delivery}/status', [DriverController::class, 'updateDeliveryStatus'])->name('deliveries.updateStatus');

    // Legacy routes (keep for compatibility)
    Route::get('/deliveries', [DeliveryController::class, 'index'])->name('deliveries');
    Route::get('/deliveries/{delivery}', [DeliveryController::class, 'show'])->name('deliveries.show');
});

Route::get('/verify-code', [AuthController::class, 'showVerificationForm'])->name('verify.code');
Route::post('/verify-code', [AuthController::class, 'verifyCode'])->name('verify.code.submit');
Route::post('/verify-code/resend', [AuthController::class, 'resendCode'])->name('verify.code.resend');

// PayMongo payment callback route
Route::get('/customer/payment/callback/{order}', [App\Http\Controllers\Customer\PaymentController::class, 'paymongoCallback'])->name('customer.payment.callback');

// PayMongo webhook route (no authentication required)
Route::post('/webhooks/paymongo', [App\Http\Controllers\PayMongoWebhookController::class, 'handle'])->name('webhooks.paymongo');

// Debug route for reviews
Route::get('/debug-reviews', function() {
    echo "=== DEBUG REVIEWS ===\n";
    
    // Check Order 215
    $order = App\Models\Order::find(215);
    if ($order) {
        echo "Order 215 found\n";
        $products = $order->products;
        echo "Products in order: " . $products->count() . "\n";
        foreach($products as $p) {
            echo "Product: " . $p->id . " - " . $p->name . " | Reviewed: " . ($p->pivot->reviewed ? 'Yes' : 'No') . " | Rating: " . $p->pivot->rating . "\n";
        }
    } else {
        echo "Order 215 not found\n";
    }
    
    // Check Blooming Charm CatalogProduct
    $catalogProduct = App\Models\CatalogProduct::where('name', 'like', '%Blooming Charm%')->first();
    if ($catalogProduct) {
        echo "\nCatalogProduct found: " . $catalogProduct->id . " - " . $catalogProduct->name . "\n";
        $product = App\Models\Product::where('name', $catalogProduct->name)
            ->where('price', $catalogProduct->price)
            ->where('category', $catalogProduct->category)
            ->first();
        if ($product) {
            echo "Corresponding Product: " . $product->id . " - " . $product->name . "\n";
            $reviews = DB::table('order_product')
                ->where('product_id', $product->id)
                ->where('reviewed', true)
                ->get();
            echo "Reviews for this Product: " . $reviews->count() . "\n";
            foreach($reviews as $review) {
                echo "Review: Rating " . $review->rating . " - " . $review->review_comment . "\n";
            }
        } else {
            echo "No corresponding Product found\n";
        }
    } else {
        echo "CatalogProduct not found\n";
    }
    
    echo "==================\n";
    return "Check the output above";
});

Route::post('phone/send-code', [PhoneAuthController::class, 'sendCode']);
Route::post('phone/verify-code', [PhoneAuthController::class, 'verifyCode']);

// Social authentication routes
Route::get('auth/{provider}', [App\Http\Controllers\AuthController::class, 'redirectToProvider'])->name('auth.provider');
Route::get('auth/{provider}/callback', [App\Http\Controllers\AuthController::class, 'handleProviderCallback'])->name('auth.provider.callback');

// Test route for notifications (remove in production)

// Social verification routes
Route::get('auth/social/verify', [App\Http\Controllers\AuthController::class, 'showSocialVerifyForm'])->name('social.verify.form');
Route::post('auth/social/verify', [App\Http\Controllers\AuthController::class, 'verifySocialCode'])->name('social.verify.code');
Route::get('auth/social/resend/email', [App\Http\Controllers\AuthController::class, 'resendEmailCode'])->name('social.resend.email');
Route::get('auth/social/resend/sms', [App\Http\Controllers\AuthController::class, 'resendSMSCode'])->name('social.resend.sms');

// Manual verification route for support team
Route::get('auth/social/manual-verify/{userId}', [App\Http\Controllers\AuthController::class, 'manualVerifyUser'])->name('social.manual.verify');

Route::get('/facebook/verify-phone', [App\Http\Controllers\AuthController::class, 'showFacebookPhoneForm'])->name('facebook.verify.phone');
Route::post('/facebook/verify-phone', [App\Http\Controllers\AuthController::class, 'verifyFacebookPhone'])->name('facebook.verify.phone.submit');

// GEO Optimization routes
Route::get('/api/geo/content', [App\Http\Controllers\GeoOptimizationController::class, 'getLocationBasedContent'])->name('geo.content');
Route::post('/api/geo/location', [App\Http\Controllers\GeoOptimizationController::class, 'updateLocation'])->name('geo.location.update');
Route::get('/api/geo/homepage-products', [App\Http\Controllers\GeoOptimizationController::class, 'getHomepageProducts'])->name('geo.homepage.products');

// Flower Design feature removed
