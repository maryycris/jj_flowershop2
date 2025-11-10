<?php

namespace App\Http\Controllers\Clerk;

use App\Http\Controllers\Controller;
use App\Models\PurchaseOrder;
use App\Models\Product;
use Illuminate\Http\Request;

class PurchaseOrderController extends Controller
{
    public function index()
    {
        $purchaseOrders = PurchaseOrder::latest()->get();
        return view('clerk.purchase_orders.index', compact('purchaseOrders'));
    }

    public function create()
    {
        $products = Product::all();
        return view('clerk.purchase_orders.create', compact('products'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'supplier_name' => 'required|string|max:255',
            'contact' => 'nullable|string|max:255',
            'address' => 'nullable|string|max:255',
            'order_date_received' => 'required|date',
            'products' => 'required|array',
            'products.*.product_id' => 'required|exists:products,id',
            'products.*.quantity' => 'required|integer|min:1',
            'products.*.unit_price' => 'required|numeric|min:0',
        ]);

        $po = PurchaseOrder::create($request->only(['supplier_name', 'contact', 'address', 'order_date_received']));
        $total = 0;
        foreach ($request->products as $item) {
            $subtotal = $item['quantity'] * $item['unit_price'];
            $po->products()->attach($item['product_id'], [
                'quantity' => $item['quantity'],
                'received' => 0,
                'unit_price' => $item['unit_price'],
                'subtotal' => $subtotal,
            ]);
            $total += $subtotal;
        }
        $po->total_amount = $total;
        $po->save();
        return redirect()->route('clerk.purchase_orders.index')->with('success', 'Purchase order created.');
    }

    public function show(PurchaseOrder $purchaseOrder)
    {
        $purchaseOrder->load('products');
        return view('clerk.purchase_orders.show', compact('purchaseOrder'));
    }

    public function validateOrder(Request $request, PurchaseOrder $purchaseOrder)
    {
        foreach ($purchaseOrder->products as $product) {
            $received = $request->input('received.' . $product->id, $product->pivot->quantity);
            $product->stock += $received;
            $product->save();
            $purchaseOrder->products()->updateExistingPivot($product->id, ['received' => $received]);
        }
        $purchaseOrder->status = 'validated';
        $purchaseOrder->save();
        return redirect()->route('clerk.purchase_orders.show', $purchaseOrder)->with('success', 'Purchase order validated and inventory updated.');
    }
} 