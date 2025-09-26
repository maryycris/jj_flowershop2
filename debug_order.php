<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';

$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$order = App\Models\Order::find(36);

echo "Order 36 Details:\n";
echo "Total Price: " . $order->total_price . "\n";
echo "Delivery exists: " . ($order->delivery ? 'Yes' : 'No') . "\n";

if ($order->delivery) {
    echo "Shipping Fee: " . $order->delivery->shipping_fee . "\n";
    echo "Delivery Address: " . $order->delivery->delivery_address . "\n";
}

echo "Products:\n";
foreach ($order->products as $product) {
    $subtotal = $product->pivot->quantity * $product->price;
    echo "- " . $product->name . " x" . $product->pivot->quantity . " @ ₱" . $product->price . " = ₱" . $subtotal . "\n";
}

$calculatedSubtotal = $order->products->sum(function($product) {
    return $product->pivot->quantity * $product->price;
});

echo "Calculated Subtotal: ₱" . $calculatedSubtotal . "\n";
echo "Difference (shipping): ₱" . ($order->total_price - $calculatedSubtotal) . "\n";
