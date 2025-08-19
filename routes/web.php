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
    Route::resource('products', ProductController::class);
    Route::post('products/{product}/images/update', [ProductController::class, 'updateImages'])->name('products.updateImages');
    Route::delete('products/{product}/images/delete', [ProductController::class, 'deleteImage'])->name('products.deleteImage');
    Route::delete('products/{product}/images/delete-all', [ProductController::class, 'deleteAllImages'])->name('products.deleteAllImages');
    Route::resource('orders', OrderController::class);
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
    Route::get('/reports/sales', [ReportController::class, 'sales'])->name('reports.sales');
    Route::get('/chatbox', [AdminController::class, 'chatbox'])->name('chatbox');
    Route::post('/chatbox/send', [AdminController::class, 'sendMessage'])->name('chatbox.send');
    Route::get('orders/{order}/status-history', [OrderController::class, 'statusHistory'])->name('orders.statusHistory');
    Route::post('orders/{order}/assign-delivery', [OrderController::class, 'assignDelivery'])->name('orders.assignDelivery');
    Route::get('/inventory', [ProductController::class, 'inventory'])->name('inventory.index');
    Route::get('/profile', [AdminController::class, 'editProfile'])->name('profile');
    Route::post('/profile', [AdminController::class, 'updateProfile'])->name('profile.update');
    Route::post('/profile/password', [AdminController::class, 'updatePassword'])->name('profile.password');
});

// Clerk Routes
Route::middleware(['web', 'auth', \App\Http\Middleware\ClerkMiddleware::class])->prefix('clerk')->name('clerk.')->group(function () {
    Route::get('/dashboard', [ClerkController::class, 'dashboard'])->name('dashboard');
    // FIX: Use ClerkController for product_catalog
    Route::get('/product_catalog', [ClerkController::class, 'productCatalog'])->name('product_catalog.index');
    Route::post('/product_catalog', action: [ClerkController::class, 'storeProductCatalog'])->name('product_catalog.store');
    Route::resource('products', ProductController::class);
    Route::post('products/{product}/images/update', [ProductController::class, 'updateImages'])->name('products.updateImages');
    Route::delete('products/{product}/images/delete', [ProductController::class, 'deleteImage'])->name('products.deleteImage');
    Route::delete('products/{product}/images/delete-all', [ProductController::class, 'deleteAllImages'])->name('products.deleteAllImages');
    Route::get('/inventory', [ClerkController::class, 'inventory'])->name('inventory.index');
    Route::post('/inventory', [ClerkController::class, 'storeProduct'])->name('inventory.store');
    Route::put('/inventory/{product}', [ClerkController::class, 'updateProduct'])->name('inventory.update');
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
    Route::get('/profile', [ClerkController::class, 'editProfile'])->name('profile.edit');
    Route::get('/sales', [ClerkController::class, 'sales'])->name('sales.index');
    Route::resource('deliveries', DeliveryController::class);
    Route::put('/profile', [ClerkController::class, 'updateProfile'])->name('profile.update');
    Route::put('/profile/password', [ClerkController::class, 'updatePassword'])->name('profile.password');
    Route::post('/orders/{order}/assign-delivery', [ClerkController::class, 'assignDelivery'])->name('orders.assignDelivery');
    Route::get('/purchase-orders', [PurchaseOrderController::class, 'index'])->name('purchase_orders.index');
    Route::get('/purchase-orders/create', [PurchaseOrderController::class, 'create'])->name('purchase_orders.create');
    Route::post('/purchase-orders', [PurchaseOrderController::class, 'store'])->name('purchase_orders.store');
    Route::get('/purchase-orders/{purchaseOrder}', [PurchaseOrderController::class, 'show'])->name('purchase_orders.show');
    Route::post('/purchase-orders/{purchaseOrder}/validate', [PurchaseOrderController::class, 'validateOrder'])->name('purchase_orders.validate');
    Route::get('orders/{order}/status-history', [OrderController::class, 'statusHistory'])->name('orders.statusHistory');
    Route::post('orders/{order}/mark-delivered', [App\Http\Controllers\OrderController::class, 'markDelivered'])->name('clerk.orders.markDelivered');
});

// Clerk order approval and delivery assignment
Route::post('/orders/{order}/approve', [\App\Http\Controllers\Clerk\ClerkController::class, 'approveOrder'])->name('orders.approve');
Route::post('/orders/{order}/assign-delivery', [\App\Http\Controllers\Clerk\ClerkController::class, 'assignDelivery'])->name('orders.assignDelivery');

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
    Route::get('/products', [ProductController::class, 'index'])->name('products.index');
    Route::get('/products/bestsellers', [ProductController::class, 'bestsellers'])->name('products.bestsellers');
    Route::get('/products/customize', [ProductController::class, 'customize'])->name('products.customize');
    Route::post('/products/customize', [ProductController::class, 'submitCustomization'])->name('products.customize.submit');
    Route::get('/products/{product}', [ProductController::class, 'show'])->name('products.show');
    Route::get('/cart', [CartController::class, 'index'])->name('customer.cart.index');
    Route::post('/cart/add', [CartController::class, 'addToCart'])->name('cart.add');
    Route::post('/cart-items/{cartItem}/update-quantity', [CartController::class, 'updateQuantity'])->name('cart.updateQuantity');
    Route::delete('/cart-items/{cartItem}/remove', [CartController::class, 'removeItem'])->name('cart.remove');
    Route::delete('/cart-items/delete-all', [CartController::class, 'deleteAllItems'])->name('cart.deleteAll');
    Route::get('/account', [AccountController::class, 'index'])->name('account');
    Route::get('/account/change-password', [AccountController::class, 'changePassword'])->name('account.change_password');
    Route::post('/account/change-password', [AccountController::class, 'updatePassword'])->name('account.update_password');
    Route::post('/account/update', [AccountController::class, 'update'])->name('account.update');
    Route::resource('address_book', AddressController::class)->parameters(['address_book' => 'address']);
    Route::get('/address-book', [AddressController::class, 'index'])->name('address_book');
    Route::post('address_book/{address}/set-default', [AddressController::class, 'setDefault'])->name('address_book.set-default');

    // Notifications (FIXED)
    Route::get('/notifications', [CustomerNotificationController::class, 'index'])->name('notifications');
    Route::get('/notifications/index', [CustomerNotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/mark-all-as-read', [CustomerNotificationController::class, 'markAllAsRead'])->name('notifications.markAllAsRead');
    Route::delete('/notifications/delete-all', [CustomerNotificationController::class, 'destroyAll'])->name('notifications.deleteAll');
    Route::delete('/notifications/{id}', [CustomerNotificationController::class, 'destroy'])->name('customer.notifications.delete');
    Route::post('/notifications/{id}/read', [CustomerNotificationController::class, 'markAsRead'])->name('notifications.markAsRead');
    Route::post('/notifications/{id}/unread', [CustomerNotificationController::class, 'markAsUnread'])->name('notifications.markAsUnread');

    Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
    Route::get('/checkout/payment-method', [CheckoutController::class, 'paymentMethod'])->name('checkout.payment_method');
    Route::post('/checkout/process', [CheckoutController::class, 'processOrder'])->name('checkout.process');

    // Payment Gateway Routes
    Route::get('/payment/gcash/{order}', [PaymentController::class, 'gcashPayment'])->name('payment.gcash');
    Route::get('/payment/paymaya/{order}', [PaymentController::class, 'paymayaPayment'])->name('payment.paymaya');
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
    Route::get('/track-orders', [OrderController::class, 'trackOrdersPage'])->name('trackOrders.page');
});

// Customer Login Routes
Route::get('/customer/login', [AuthController::class, 'showCustomerLogin'])->name('customer.login');
Route::post('/customer/login', [AuthController::class, 'customerLogin']);

// Staff Login Routes
Route::get('/staff/login', [AuthController::class, 'showStaffLogin'])->name('staff.login');
Route::post('/staff/login', [AuthController::class, 'staffLogin']);

// Customer Notifications
Route::prefix('customer')->middleware(['auth', 'customer'])->group(function () {
    Route::post('/notifications/mark-all-read', [CustomerNotificationController::class, 'markAllAsRead'])->name('customer.notifications.markAllRead');
    Route::delete('/notifications/destroy-all', [CustomerNotificationController::class, 'destroyAll'])->name('customer.notifications.destroyAll');
});

// Driver Routes
Route::middleware(['web', 'auth', \App\Http\Middleware\DriverMiddleware::class])->prefix('driver')->name('driver.')->group(function () {
    Route::get('/dashboard', [DriverController::class, 'dashboard'])->name('dashboard');

    // Orders
    Route::get('/orders', [DriverController::class, 'orders'])->name('orders.index');
    Route::get('/orders/{delivery}', [DriverController::class, 'showOrder'])->name('orders.show');

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

Route::post('phone/send-code', [PhoneAuthController::class, 'sendCode']);
Route::post('phone/verify-code', [PhoneAuthController::class, 'verifyCode']);

// Social verification routes
Route::get('auth/social/verify', [App\Http\Controllers\AuthController::class, 'showSocialVerifyForm'])->name('social.verify.form');
Route::post('auth/social/verify', [App\Http\Controllers\AuthController::class, 'verifySocialCode'])->name('social.verify.code');

Route::get('/facebook/verify-phone', [App\Http\Controllers\AuthController::class, 'showFacebookPhoneForm'])->name('facebook.verify.phone');
Route::post('/facebook/verify-phone', [App\Http\Controllers\AuthController::class, 'verifyFacebookPhone'])->name('facebook.verify.phone.submit');
