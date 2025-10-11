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
    
    // Admin Walk-in Order Creation (must be before resource routes)
    Route::get('orders/create', [\App\Http\Controllers\Clerk\OrderFlowController::class, 'createWalkinOrder'])->name('orders.create');
    Route::post('orders/store', [\App\Http\Controllers\Clerk\OrderFlowController::class, 'storeWalkinOrder'])->name('orders.store');
    
    // Admin Online Order Flow
    Route::get('orders/{order}/online/invoice', [\App\Http\Controllers\Clerk\OrderFlowController::class, 'onlineInvoice'])->name('orders.online.invoice');
    Route::get('orders/{order}/online/validate', [\App\Http\Controllers\Clerk\OrderFlowController::class, 'onlineValidate'])->name('orders.online.validate');
    Route::post('orders/{order}/online/validate/confirm', [\App\Http\Controllers\Clerk\OrderFlowController::class, 'onlineValidateConfirm'])->name('orders.online.validate.confirm');
    Route::get('orders/{order}/online/done', [\App\Http\Controllers\Clerk\OrderFlowController::class, 'onlineDone'])->name('orders.online.done');
    
    // Admin Walk-in Order Flow
    Route::get('orders/{order}/walkin/quotation', [\App\Http\Controllers\Clerk\OrderFlowController::class, 'walkinQuotation'])->name('orders.walkin.quotation');
    Route::get('orders/{order}/walkin/invoice', [\App\Http\Controllers\Clerk\OrderFlowController::class, 'walkinCreateInvoice'])->name('orders.walkin.invoice');
    Route::get('orders/{order}/walkin/validate', [\App\Http\Controllers\Clerk\OrderFlowController::class, 'walkinValidate'])->name('orders.walkin.validate');
    Route::post('orders/{order}/walkin/validate/confirm', [\App\Http\Controllers\Clerk\OrderFlowController::class, 'walkinValidateConfirm'])->name('orders.walkin.validate.confirm');
    Route::get('orders/{order}/walkin/done', [\App\Http\Controllers\Clerk\OrderFlowController::class, 'walkinDone'])->name('orders.walkin.done');
    
    // Admin Orders Resource (must be after specific routes)
    Route::resource('orders', OrderController::class)->except(['create', 'store']);
    Route::post('orders/{order}/approve', [OrderController::class, 'approve'])->name('orders.approve');
    Route::post('orders/{order}/validate', [OrderController::class, 'validateOrder'])->name('orders.validate');
    Route::get('orders/{order}/invoice', [OrderController::class, 'invoice'])->name('orders.invoice');
    Route::get('orders/{order}/invoice/view', [OrderController::class, 'viewInvoice'])->name('orders.invoice.view');
    Route::get('orders/{order}/invoice/download', [OrderController::class, 'downloadInvoice'])->name('orders.invoice.download');
    Route::get('/walk-in-orders', [OrderController::class, 'walkInIndex'])->name('walkInOrders.index');
    Route::resource('deliveries', DeliveryController::class);
    Route::resource('users', UserController::class);
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/{notification}/mark-as-read', [NotificationController::class, 'markAsRead'])->name('notifications.markAsRead');
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::post('/reports/sales', [ReportController::class, 'generateSalesReport'])->name('reports.sales');
    Route::post('/reports/order-status', [ReportController::class, 'generateOrderStatusReport'])->name('reports.orderStatus');
    Route::post('/reports/product-performance', [ReportController::class, 'generateProductPerformanceReport'])->name('reports.productPerformance');
    Route::post('/reports/user-activity', [ReportController::class, 'generateUserActivityReport'])->name('reports.userActivity');
    Route::get('/reports/inventory', [ReportController::class, 'inventory'])->name('reports.inventory');
    
    // Admin Inventory Management
    Route::get('/inventory', [\App\Http\Controllers\Admin\AdminInventoryController::class, 'index'])->name('inventory.index');
    Route::post('/inventory/approve/{id}', [\App\Http\Controllers\Admin\AdminInventoryController::class, 'approve'])->name('inventory.approve');
    Route::post('/inventory/reject/{id}', [\App\Http\Controllers\Admin\AdminInventoryController::class, 'reject'])->name('inventory.reject');
    
    // Admin Inventory Reports
    Route::get('/inventory/reports', [\App\Http\Controllers\Admin\AdminInventoryController::class, 'reports'])->name('inventory.reports');
    
    
    // Admin Customize (bouquet components)
    Route::get('/customize', [\App\Http\Controllers\Admin\CustomizeController::class, 'index'])->name('customize.index');
    Route::post('/customize', [\App\Http\Controllers\Admin\CustomizeController::class, 'store'])->name('customize.store');
    Route::delete('/customize/bulk-delete', [\App\Http\Controllers\Admin\CustomizeController::class, 'bulkDelete'])->name('customize.bulk-delete');
    Route::put('/customize/{id}', [\App\Http\Controllers\Admin\CustomizeController::class, 'update'])->name('customize.update');
    Route::delete('/customize/{id}', [\App\Http\Controllers\Admin\CustomizeController::class, 'destroy'])->name('customize.destroy');
    Route::get('/inventory/pending-count', [\App\Http\Controllers\Admin\AdminInventoryController::class, 'getPendingCount'])->name('inventory.pending-count');
    Route::get('/reports/sales', [ReportController::class, 'sales'])->name('reports.sales');
    Route::get('/chatbox', [AdminController::class, 'chatbox'])->name('chatbox');
    Route::post('/chatbox/send', [AdminController::class, 'sendMessage'])->name('chatbox.send');
    Route::get('orders/{order}/status-history', [OrderController::class, 'statusHistory'])->name('orders.statusHistory');
    Route::post('orders/{order}/assign-delivery', [OrderController::class, 'assignDelivery'])->name('orders.assignDelivery');
    Route::get('/inventory', [ProductController::class, 'inventory'])->name('inventory.index');
    Route::post('/inventory', [ProductController::class, 'storeInventory'])->name('inventory.store');
    Route::put('/inventory/{product}', [ProductController::class, 'updateInventory'])->name('inventory.update');
    Route::delete('/inventory/{product}', [ProductController::class, 'destroyInventory'])->name('inventory.destroy');
    Route::get('/profile', [AdminController::class, 'editProfile'])->name('profile');
    Route::post('/profile', [AdminController::class, 'updateProfile'])->name('profile.update');
    Route::post('/profile/password', [AdminController::class, 'updatePassword'])->name('profile.password');

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
    Route::get('/inventory', [ClerkController::class, 'inventory'])->name('inventory.index');
    Route::post('/inventory', [ClerkController::class, 'storeInventory'])->name('inventory.store');
    Route::put('/inventory/{product}', [ClerkController::class, 'updateProduct'])->name('inventory.update');
    Route::delete('/inventory/{product}', [\App\Http\Controllers\ProductController::class, 'destroyInventory'])->name('inventory.destroy');
    Route::post('/inventory/submit-changes', [ClerkController::class, 'submitInventoryChanges'])->name('inventory.submit-changes');
    Route::get('/orders', [ClerkController::class, 'orders'])->name('orders.index');
    Route::resource('orders', OrderController::class)->except(['index']);
    Route::post('orders/{order}/approve', [OrderController::class, 'approve'])->name('orders.approve');
    Route::post('orders/{order}/validate', [OrderController::class, 'validateOrder'])->name('orders.validate');
    Route::get('orders/{order}/invoice', [OrderController::class, 'invoice'])->name('orders.invoice');
    Route::get('orders/{order}/invoice/view', [OrderController::class, 'viewInvoice'])->name('orders.invoice.view');
    Route::get('orders/{order}/invoice/download', [OrderController::class, 'downloadInvoice'])->name('orders.invoice.download');
    Route::post('/orders/{order}/validate-recipient', [OrderController::class, 'validateRecipient'])->name('orders.validate-recipient');
    Route::resource('users', UserController::class);
    Route::get('/notifications', [ClerkController::class, 'notifications'])->name('notifications.index');
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
    Route::get('invoices/{order}', [\App\Http\Controllers\Clerk\InvoiceController::class, 'show'])->name('invoices.show');
    Route::get('invoices/{order}/download', [\App\Http\Controllers\Clerk\InvoiceController::class, 'download'])->name('invoices.download');

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
            Route::post('{order}/walkin/invoice', [\App\Http\Controllers\Clerk\OrderFlowController::class, 'walkinInvoice'])->name('walkin.invoice');
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
    Route::get('/notifications/index', [CustomerNotificationController::class, 'index'])->name('notifications.index');
    Route::get('/notifications/list', [CustomerNotificationController::class, 'list'])->name('notifications.list');
    Route::post('/notifications/mark-all-as-read', [CustomerNotificationController::class, 'markAllAsRead'])->name('notifications.markAllAsRead');
    Route::delete('/notifications/delete-all', [CustomerNotificationController::class, 'destroyAll'])->name('notifications.deleteAll');
    Route::delete('/notifications/{id}', [CustomerNotificationController::class, 'destroy'])->name('notifications.delete');
    Route::post('/notifications/{id}/read', [CustomerNotificationController::class, 'markAsRead'])->name('notifications.markAsRead');
    Route::post('/notifications/{id}/unread', [CustomerNotificationController::class, 'markAsUnread'])->name('notifications.markAsUnread');

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
    Route::post('/orders/{order}/update-delivery-schedule', [OrderController::class, 'updateDeliverySchedule'])->name('orders.update-delivery-schedule');
    Route::get('/track-orders', [OrderController::class, 'trackOrdersPage'])->name('trackOrders.page');
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

Route::post('phone/send-code', [PhoneAuthController::class, 'sendCode']);
Route::post('phone/verify-code', [PhoneAuthController::class, 'verifyCode']);

// Social authentication routes
Route::get('auth/{provider}', [App\Http\Controllers\AuthController::class, 'redirectToProvider'])->name('auth.provider');
Route::get('auth/{provider}/callback', [App\Http\Controllers\AuthController::class, 'handleProviderCallback'])->name('auth.provider.callback');

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
