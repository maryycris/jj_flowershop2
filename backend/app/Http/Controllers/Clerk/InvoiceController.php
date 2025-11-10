<?php

namespace App\Http\Controllers\Clerk;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Barryvdh\DomPDF\Facade\Pdf;

class InvoiceController extends Controller
{
    /**
     * Create an invoice from an Order and redirect to the detailed invoice page.
     */
    public function createFromOrder(\App\Models\Order $order)
    {
        // Ensure invoice exists (idempotent)
        $invoiceService = new \App\Services\InvoiceService();
        $invoice = $invoiceService->createInvoice($order);

        return redirect()->route('clerk.invoices.show', $invoice);
    }

    /**
     * Display a listing of invoices
     */
    public function index()
    {
        $query = Invoice::with(['order', 'order.user'])
            ->orderBy('created_at', 'desc');
        
        // Search functionality
        if (request()->has('search') && !empty(request('search'))) {
            $searchTerm = request('search');
            $query->where(function($q) use ($searchTerm) {
                $q->where('invoice_number', 'like', "%{$searchTerm}%")
                  ->orWhereHas('order.user', function($userQuery) use ($searchTerm) {
                      $userQuery->where('name', 'like', "%{$searchTerm}%");
                  })
                  ->orWhereHas('order', function($orderQuery) use ($searchTerm) {
                      $orderQuery->where('notes', 'like', "%{$searchTerm}%");
                  });
            });
        }
        
        // Status filter
        if (request()->has('status') && !empty(request('status'))) {
            $query->where('status', request('status'));
        }
        
        $invoices = $query->paginate(10);

        return view('clerk.invoices.index', compact('invoices'));
    }

    /**
     * Display the specified invoice
     */
    public function show(Invoice $invoice)
    {
        $invoice->load(['order.products', 'order.customBouquets', 'order.user', 'order.delivery', 'payments']);
        
        return view('clerk.invoices.show', compact('invoice'));
    }

    /**
     * Register a payment for an invoice (AJAX JSON response from list/show pages)
     */
    public function registerPayment(Request $request, Invoice $invoice)
    {
        try {
            $validated = $request->validate([
                'mode_of_payment' => 'required|string|in:cash,gcash,bank,card',
                'amount' => 'required|numeric|min:0.01',
                'payment_date' => 'required|date',
                'memo' => 'nullable|string',
            ]);

            // Create payment record
            $payment = Payment::create([
                'payment_number' => 'PAY-' . date('Y') . '-' . str_pad($invoice->id, 6, '0', STR_PAD_LEFT) . '-' . time(),
                'invoice_id' => $invoice->id,
                'order_id' => $invoice->order_id,
                'mode_of_payment' => $validated['mode_of_payment'],
                'amount' => $validated['amount'],
                'payment_date' => $validated['payment_date'],
                'memo' => $validated['memo'] ?? null,
                'processed_by' => auth()->id(),
                'status' => 'completed',
            ]);

            // Mark invoice as paid when fully covered (for now, treat single payment as full)
            $invoice->update(['status' => 'paid']);

            Log::info('Invoice payment registered', [
                'invoice_id' => $invoice->id,
                'payment_id' => $payment->id,
            ]);

            return response()->json(['success' => true]);
        } catch (\Throwable $e) {
            Log::error('Register payment failed', [
                'invoice_id' => $invoice->id,
                'error' => $e->getMessage(),
            ]);
            return response()->json(['success' => false, 'message' => 'Failed to register payment'], 422);
        }
    }

    /**
     * Download invoice PDF
     */
    public function download(Invoice $invoice)
    {
        $invoice->load(['order.products', 'order.user', 'order.delivery', 'payments']);
        $pdf = Pdf::loadView('clerk.invoices.pdf', compact('invoice'));
        return $pdf->download($invoice->invoice_number . '.pdf');
    }

    /**
     * Show payment wizard for COD invoices
     */
    public function paymentWizard(Invoice $invoice)
    {
        if ($invoice->status !== 'ready') {
            return redirect()->back()->with('error', 'This invoice is not ready for payment.');
        }
        
        $invoice->load(['order.products', 'order.user']);
        
        return view('clerk.invoices.payment', compact('invoice'));
    }
    
    /**
     * Process payment for COD invoices
     */
    public function processPayment(Request $request, Invoice $invoice)
    {
        $request->validate([
            'payment_method' => 'required|string',
            'payment_mode' => 'required|string',
            'card_type' => 'nullable|string|in:credit,debit',
            'ewallet_provider' => 'nullable|string|in:gcash,paymaya',
            'bank_provider' => 'nullable|string|in:bpi,bdo,metrobank,security_bank,seabank,rcbc,other_banks',
            'amount' => 'required|numeric|min:0.01',
            'payment_date' => 'required|date',
            'memo' => 'nullable|string|max:500'
        ]);

        try {
            // Check if it's an online payment method
            $paymentMethod = $request->payment_method;
            $isOnlinePayment = false;
            $paymentMode = $request->payment_mode;
            
            if ($paymentMethod === 'ewallet') {
                $isOnlinePayment = true;
                $paymentMode = $request->ewallet_provider ?: 'gcash';
            } elseif ($paymentMethod === 'bank') {
                $isOnlinePayment = true;
                $paymentMode = $request->bank_provider ?: 'rcbc';
                if ($paymentMode === 'other_banks') {
                    $isOnlinePayment = false;
                    $paymentMode = 'bank transfer';
                }
            }
            
            if ($isOnlinePayment) {
                if (!$paymentMode || !is_string($paymentMode)) {
                    $paymentMode = 'gcash';
                }
                return $this->redirectToPayMongo($invoice, $paymentMode);
            }
            
            // Process cash/bank payments directly
            $paymentNumber = 'PAY-' . date('Y') . '-' . str_pad($invoice->id, 6, '0', STR_PAD_LEFT) . '-' . time();
            
            $payment = Payment::create([
                'payment_number' => $paymentNumber,
                'invoice_id' => $invoice->id,
                'order_id' => $invoice->order_id,
                'mode_of_payment' => $paymentMode,
                'amount' => $request->amount,
                'payment_date' => $request->payment_date,
                'memo' => $request->memo,
                'processed_by' => auth()->id(),
                'status' => 'completed'
            ]);
            
            // Update invoice status
            $invoice->update(['status' => 'paid']);
            
            Log::info("Payment processed for invoice {$invoice->id}", [
                'invoice_id' => $invoice->id,
                'payment_id' => $payment->id,
                'amount' => $request->amount,
                'mode' => $paymentMode
            ]);
            
            return redirect()->route('clerk.orders.index')
                ->with('success', 'Payment processed successfully! Invoice has been marked as paid.');
                
        } catch (\Exception $e) {
            Log::error("Payment processing failed for invoice {$invoice->id}: {$e->getMessage()}");
            
            return redirect()->back()
                ->with('error', 'Payment processing failed. Please try again.')
                ->withInput();
        }
    }

    /**
     * Redirect to PayMongo for online payments
     */
    private function redirectToPayMongo(Invoice $invoice, string $paymentMode)
    {
        try {
            $invoice->load(['order.user', 'order.delivery']);

            if (!env('PAYMONGO_SECRET_KEY')) {
                Log::warning("PayMongo not configured, processing as offline payment", [
                    'invoice_id' => $invoice->id,
                    'payment_mode' => $paymentMode
                ]);
                
                return $this->processOfflinePayment($invoice, $paymentMode);
            }
            
            $amount = $invoice->total_amount * 100;
            
            $paymongoType = $this->mapPaymentModeToPayMongo($paymentMode);
            $allowedTypes = [$paymongoType];
            if (!$paymongoType) {
                $allowedTypes = ['gcash'];
            }
            
            $order = $invoice->order;
            $notes = $order->notes ?? '';
            $customerName = $order->user->name ?? 'Walk-in Customer';
            $customerEmail = $order->user->email ?? 'walkin@example.com';
            
            if (!empty($notes) && preg_match('/Customer:\s*(.*?)(?:[;,]|$)/', $notes, $m)) {
                $customerName = trim($m[1]);
            } elseif ($order->delivery && !empty($order->delivery->recipient_name)) {
                $customerName = $order->delivery->recipient_name;
            }
            
            if (!empty($notes) && preg_match('/Email:\s*([^; ,\n\r]+@[^; ,\n\r]+)/', $notes, $m)) {
                $customerEmail = trim($m[1]);
            }
            
            $response = \Illuminate\Support\Facades\Http::withBasicAuth(env('PAYMONGO_SECRET_KEY'), '')
                ->post(env('PAYMONGO_BASE_URL', 'https://api.paymongo.com/v1') . '/checkout_sessions', [
                    'data' => [
                        'attributes' => [
                            'billing' => [
                                'name' => $customerName,
                                'email' => $customerEmail,
                            ],
                            'line_items' => [
                                [
                                    'name' => 'Invoice #' . $invoice->invoice_number,
                                    'description' => 'Payment for invoice',
                                    'quantity' => 1,
                                    'amount' => $amount,
                                    'currency' => 'PHP'
                                ]
                            ],
                            'payment_method_types' => $allowedTypes,
                            'success_url' => route('clerk.invoices.payment.callback', $invoice->id),
                            'cancel_url' => route('clerk.orders.index'),
                        ]
                    ]
                ]);

            if ($response->successful()) {
                $data = $response->json();
                $checkoutUrl = $data['data']['attributes']['checkout_url'];
                
                $invoice->update([
                    'paymongo_checkout_session_id' => $data['data']['id']
                ]);
                
                Log::info("PayMongo checkout session created for invoice {$invoice->id}", [
                    'invoice_id' => $invoice->id,
                    'checkout_url' => $checkoutUrl,
                    'payment_mode' => $paymentMode
                ]);
                
                return redirect($checkoutUrl);
            } else {
                Log::error("PayMongo checkout session creation failed", [
                    'invoice_id' => $invoice->id,
                    'response' => $response->json()
                ]);
                
                return redirect()->back()
                    ->with('error', 'Failed to create payment session. Please try again.');
            }
            
        } catch (\Exception $e) {
            Log::error("PayMongo redirect failed for invoice {$invoice->id}: {$e->getMessage()}");
            
            return redirect()->back()
                ->with('error', 'Payment processing error. Please try again.');
        }
    }

    /**
     * Process offline payment when PayMongo is not configured
     */
    private function processOfflinePayment(Invoice $invoice, string $paymentMode)
    {
        try {
            $paymentNumber = 'PAY-' . date('Y') . '-' . str_pad($invoice->id, 6, '0', STR_PAD_LEFT) . '-' . time();
            
            $payment = Payment::create([
                'payment_number' => $paymentNumber,
                'invoice_id' => $invoice->id,
                'order_id' => $invoice->order_id,
                'mode_of_payment' => $paymentMode,
                'amount' => $invoice->total_amount,
                'payment_date' => now(),
                'memo' => 'Offline payment processed',
                'processed_by' => auth()->id(),
                'status' => 'completed'
            ]);
            
            $invoice->update(['status' => 'paid']);
            
            Log::info("Offline payment processed for invoice {$invoice->id}", [
                'invoice_id' => $invoice->id,
                'payment_id' => $payment->id,
                'mode' => $paymentMode
            ]);
            
            return redirect()->route('clerk.orders.index')
                ->with('success', 'Payment processed successfully! Invoice has been marked as paid.');
                
        } catch (\Exception $e) {
            Log::error("Offline payment processing failed for invoice {$invoice->id}: {$e->getMessage()}");
            
            return redirect()->back()
                ->with('error', 'Payment processing failed. Please try again.');
        }
    }

    /**
     * Handle PayMongo payment callback
     */
    public function paymentCallback(Request $request, $invoiceId)
    {
        try {
            $invoice = Invoice::findOrFail($invoiceId);
            
            if (!$invoice->paymongo_checkout_session_id) {
                return redirect()->route('clerk.orders.index')
                    ->with('error', 'Invalid payment session.');
            }
            
            $response = \Illuminate\Support\Facades\Http::withBasicAuth(env('PAYMONGO_SECRET_KEY'), '')
                ->get(env('PAYMONGO_BASE_URL', 'https://api.paymongo.com/v1') . '/checkout_sessions/' . $invoice->paymongo_checkout_session_id);

            if (!$response->successful()) {
                return redirect()->route('clerk.orders.index')
                    ->with('error', 'Payment verification failed.');
            }

            $data = $response->json();
            $attributes = $data['data']['attributes'];
            
            if ($attributes['status'] === 'paid') {
                $hasSuccessfulPayment = false;
                
                if (isset($attributes['payments']) && is_array($attributes['payments'])) {
                    foreach ($attributes['payments'] as $payment) {
                        if ($payment['attributes']['status'] === 'paid') {
                            $hasSuccessfulPayment = true;
                            break;
                        }
                    }
                }
                
                if ($hasSuccessfulPayment) {
                    $paymentNumber = 'PAY-' . date('Y') . '-' . str_pad($invoice->id, 6, '0', STR_PAD_LEFT) . '-' . time();
                    
                    $payment = Payment::create([
                        'payment_number' => $paymentNumber,
                        'invoice_id' => $invoice->id,
                        'order_id' => $invoice->order_id,
                        'mode_of_payment' => 'online',
                        'amount' => $invoice->total_amount,
                        'payment_date' => now(),
                        'memo' => 'Online payment via PayMongo',
                        'processed_by' => auth()->id(),
                        'status' => 'completed'
                    ]);
                    
                    $invoice->update(['status' => 'paid']);
                    
                    Log::info("Online payment completed for invoice {$invoice->id}", [
                        'invoice_id' => $invoice->id,
                        'payment_id' => $payment->id
                    ]);
                    
                    return redirect()->route('clerk.orders.index')
                        ->with('success', 'Payment successful! Invoice has been marked as paid.');
                } else {
                    Log::warning("PayMongo payment not completed", [
                        'invoice_id' => $invoice->id,
                        'status' => $attributes['status'],
                        'payments_count' => isset($attributes['payments']) ? count($attributes['payments']) : 0
                    ]);
                    
                    return redirect()->route('clerk.invoices.payment', $invoice)
                        ->with('error', 'Payment was not completed. Please try again.');
                }
            } else {
                return redirect()->route('clerk.invoices.payment', $invoice)
                    ->with('error', 'Payment was not successful. Please try again.');
            }
            
        } catch (\Exception $e) {
            Log::error("Payment callback error for invoice {$invoiceId}: {$e->getMessage()}");
            
            return redirect()->route('clerk.orders.index')
                ->with('error', 'Payment verification failed. Please contact support.');
        }
    }

    /**
     * Map payment mode to PayMongo type
     */
    private function mapPaymentModeToPayMongo(string $paymentMode): ?string
    {
        $mapping = [
            'gcash' => 'gcash',
            'paymaya' => 'paymaya',
            'bpi' => 'bpi',
            'bdo' => 'bdo',
            'metrobank' => 'metrobank',
            'security_bank' => 'security_bank',
            'seabank' => 'seabank',
            'rcbc' => 'rcbc',
        ];
        
        return $mapping[$paymentMode] ?? null;
    }
}