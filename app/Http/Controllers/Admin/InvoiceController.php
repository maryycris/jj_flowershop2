<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Invoice;
use App\Services\InvoiceService;
use Illuminate\Http\Request;
use Carbon\Carbon;

class InvoiceController extends Controller
{
    protected $invoiceService;

    public function __construct(InvoiceService $invoiceService)
    {
        $this->invoiceService = $invoiceService;
    }

    /**
     * Display a listing of invoices
     */
    public function index()
    {
        $invoices = Invoice::with(['order.user', 'order.products', 'order.delivery', 'payments'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.invoices.index', compact('invoices'));
    }

    /**
     * Display the specified invoice
     */
    public function show(Invoice $invoice)
    {
        $invoice->load(['order.user', 'order.products', 'order.delivery', 'payments']);
        
        return view('admin.invoices.show', compact('invoice'));
    }

    /**
     * Create invoice for an order
     */
    public function createInvoice(Order $order)
    {
        try {
            $invoice = $this->invoiceService->createInvoice($order);
            
            return response()->json([
                'success' => true,
                'message' => 'Invoice created successfully',
                'invoice' => $invoice
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create invoice: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Register payment for COD invoice
     */
    public function registerPayment(Request $request, Invoice $invoice)
    {
        $request->validate([
            'mode_of_payment' => 'required|in:cash,gcash,bank,card',
            'amount' => 'required|numeric|min:0.01',
            'payment_date' => 'required|date',
            'memo' => 'nullable|string|max:500'
        ]);

        try {
            $payment = $this->invoiceService->registerPayment($invoice, $request->all());
            
            return response()->json([
                'success' => true,
                'message' => 'Payment registered successfully',
                'payment' => $payment
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to register payment: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get payment modes for dropdown
     */
    public function getPaymentModes()
    {
        return response()->json([
            'payment_modes' => $this->invoiceService->getPaymentModes()
        ]);
    }
}
