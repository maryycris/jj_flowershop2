<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SalesReportController extends Controller
{
    public function index(Request $request)
    {
        $from = $request->input('from');
        $to = $request->input('to');

        $query = Order::with(['latestDeliveryReceipt']);
        if ($from) {
            $query->whereDate('created_at', '>=', $from);
        }
        if ($to) {
            $query->whereDate('created_at', '<=', $to);
        }
        $orders = $query->orderByDesc('created_at')->paginate(20)->withQueryString();

        return view('admin.reports.sales_report', compact('orders', 'from', 'to'));
    }
}
