<?php

namespace App\Http\Controllers\Clerk;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Invoice;
use App\Services\InvoiceService;
use App\Services\LoyaltyService;
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

        return view('clerk.invoices.index', compact('invoices'));
    }

    /**
     * Display the specified invoice
     */
    public function show(Invoice $invoice)
    {
        $invoice->load(['order.user', 'order.products', 'order.delivery', 'payments']);
        
        return view('clerk.invoices.show', compact('invoice'));
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
            // Loyalty: redeem if discount used, then issue stamp for COD
            try {
                $order = $invoice->order()->with('products')->first();
                if ($order) {
                    $order->payment_status = 'paid_cod';
                    $loyalty = new LoyaltyService();
                    $discount = (float) (session('loyalty_discount_pending', 0));
                    if ($discount > 0) {
                        $card = $loyalty->getActiveCardForUser($order->user_id);
                        if ($loyalty->canRedeem($card)) {
                            $loyalty->redeem($card, $order, $discount);
                        }
                        session()->forget('loyalty_discount_pending');
                    }
                    $loyalty->issueStampIfEligible($order);
                }
            } catch (\Throwable $e) {
                \Log::error('Loyalty stamp issue failed (COD)', ['invoice_id' => $invoice->id, 'error' => $e->getMessage()]);
            }
            
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