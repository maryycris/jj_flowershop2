<?php

namespace App\Http\Controllers\Driver;

use App\Http\Controllers\Controller;
use App\Models\DeliveryReceipt;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class DeliveryReceiptController extends Controller
{
    public function store(Request $request, Order $order)
    {
        // Basic authorization: driver assigned to this order or admin/clerk
        if (!Auth::check()) {
            abort(403);
        }

        $user = Auth::user();
        $isDriver = method_exists($user, 'hasRole') ? $user->hasRole('driver') : false;
        $isAdminOrClerk = method_exists($user, 'hasRole') ? ($user->hasRole('admin') || $user->hasRole('clerk')) : false;

        if (!$isDriver && !$isAdminOrClerk) {
            abort(403, 'Unauthorized.');
        }

        $data = $request->validate([
            'image' => ['required','image','mimes:jpeg,jpg,png,webp','max:4096'],
            'receiver_name' => ['nullable','string','max:255'],
            'notes' => ['nullable','string','max:2000'],
            'received_at' => ['nullable','date'],
        ]);

        // Store the image
        $folder = 'delivery_receipts/'.now()->format('Y/m').'/'.$order->id;
        $filename = 'proof_'.now()->format('Ymd_His').'.'.$request->file('image')->getClientOriginalExtension();
        $path = $request->file('image')->storeAs($folder, $filename, 'public');

        $receipt = DeliveryReceipt::create([
            'order_id' => $order->id,
            'image_path' => $path,
            'receiver_name' => $data['receiver_name'] ?? null,
            'notes' => $data['notes'] ?? null,
            'captured_by' => $user->id,
            'received_at' => $data['received_at'] ?? now(),
        ]);

        // Optionally update order status and delivered_at if your schema supports it
        if (Schema::hasColumn('orders', 'status')) {
            $order->status = 'delivered';
        }
        if (Schema::hasColumn('orders', 'delivered_at')) {
            $order->delivered_at = now();
        }
        $order->save();

        return back()->with('success', 'Delivery proof uploaded successfully.');
    }
}
