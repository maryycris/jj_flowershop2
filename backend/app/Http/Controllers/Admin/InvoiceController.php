<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

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

        return redirect()->route('invoices.show', $invoice);
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

        return view('admin.invoices.index', compact('invoices'));
    }

    /**
     * Display the specified invoice
     */
    public function show(Invoice $invoice)
    {
        $invoice->load(['order.products', 'order.customBouquets', 'order.user', 'order.delivery', 'payments']);
        
        return view('admin.invoices.show', compact('invoice'));
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
        
        return view('admin.invoices.payment', compact('invoice'));
    }
    
    /**
     * Process payment for COD invoices
     */
    public function processPayment(Request $request, Invoice $invoice)
    {
        $request->validate([
            'payment_method' => 'required|string|in:cash,bank,ewallet',
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
                // Use the specific e-wallet provider; default to gcash if not provided
                $paymentMode = $request->ewallet_provider ?: 'gcash';
            } elseif ($paymentMethod === 'bank') {
                $isOnlinePayment = true;
                // Use exact bank provider if provided, but treat "other_banks" as offline
                $paymentMode = $request->bank_provider ?: 'rcbc';
                if ($paymentMode === 'other_banks') {
                    $isOnlinePayment = false; // Process as offline payment
                    $paymentMode = 'bank transfer';
                }
            }
            
            // Ensure we always pass a non-null string payment mode
            if ($isOnlinePayment) {
                if (!$paymentMode || !is_string($paymentMode)) {
                    $paymentMode = 'gcash';
                }
                // Redirect to PayMongo for online payments
                return $this->redirectToPayMongo($invoice, $paymentMode);
            }
            
            // Process cash/bank payments directly
            $paymentNumber = 'PAY-' . date('Y') . '-' . str_pad($invoice->id, 6, '0', STR_PAD_LEFT) . '-' . time();
            
            // Determine the final payment mode for storage
            $finalPaymentMode = $request->payment_mode;
            
            $payment = Payment::create([
                'payment_number' => $paymentNumber,
                'invoice_id' => $invoice->id,
                'order_id' => $invoice->order_id,
                'mode_of_payment' => $finalPaymentMode,
                'amount' => $request->amount,
                'payment_date' => $request->payment_date,
                'memo' => $request->memo,
                'processed_by' => auth()->id(),
                'status' => 'completed'
            ]);
            
            // Update invoice status to paid
            $invoice->update(['status' => 'paid']);
            
            Log::info("Payment processed for invoice {$invoice->id}", [
                'invoice_id' => $invoice->id,
                'payment_id' => $payment->id,
                'amount' => $request->amount,
                'mode' => $request->payment_mode
            ]);
            
            return redirect()->route('invoices.show', $invoice)
                ->with('success', 'Payment processed successfully!');
                
        } catch (\Exception $e) {
            Log::error("Payment processing failed for invoice {$invoice->id}: {$e->getMessage()}");
            
            return redirect()->back()
                ->with('error', 'Failed to process payment. Please try again.')
                ->withInput();
        }
    }

    /**
     * Redirect to PayMongo for online payments
     */
    private function redirectToPayMongo(Invoice $invoice, string $paymentMode)
    {
        try {
            // Ensure related order data is available
            $invoice->load(['order.user', 'order.delivery']);

            // Check if PayMongo is configured
            if (!env('PAYMONGO_SECRET_KEY')) {
                Log::warning("PayMongo not configured, processing as offline payment", [
                    'invoice_id' => $invoice->id,
                    'payment_mode' => $paymentMode
                ]);
                
                // Process as offline payment instead
                return $this->processOfflinePayment($invoice, $paymentMode);
            }
            
            $amount = $invoice->total_amount * 100; // PayMongo expects amount in cents
            
            // Map payment mode to PayMongo type and restrict to that type only
            $paymongoType = $this->mapPaymentModeToPayMongo($paymentMode);
            $allowedTypes = [$paymongoType];
            // Fallback if mapping is unknown
            if (!$paymongoType) {
                $allowedTypes = ['gcash'];
            }
            
            // Derive customer info from order notes (preferred), then delivery, then user
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
                            'success_url' => route('invoices.payment.callback', $invoice->id),
                            'cancel_url' => route('admin.orders.index', ['type' => 'walkin']),
                        ]
                    ]
                ]);

            if ($response->successful()) {
                $data = $response->json();
                $checkoutUrl = $data['data']['attributes']['checkout_url'];
                
                // Store PayMongo session ID for callback verification
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
                'memo' => 'Offline payment via ' . $paymentMode,
                'processed_by' => auth()->id(),
                'status' => 'completed'
            ]);
            
            // Update invoice status to paid
            $invoice->update(['status' => 'paid']);
            
            Log::info("Offline payment processed for invoice {$invoice->id}", [
                'invoice_id' => $invoice->id,
                'payment_id' => $payment->id,
                'mode' => $paymentMode
            ]);
            
            return redirect()->route('invoices.show', $invoice)
                ->with('success', 'Payment processed successfully! (Offline mode)');
                
        } catch (\Exception $e) {
            Log::error("Offline payment processing failed for invoice {$invoice->id}: {$e->getMessage()}");
            
            return redirect()->back()
                ->with('error', 'Failed to process payment. Please try again.')
                ->withInput();
        }
    }

    /**
     * Map payment mode to PayMongo type
     */
    private function mapPaymentModeToPayMongo(string $paymentMode): string
    {
        $mapping = [
            'gcash' => 'gcash',
            'paymaya' => 'paymaya',
            'seabank' => 'seabank',
            'rcbc' => 'rcbc'
        ];
        
        return $mapping[$paymentMode] ?? 'gcash';
    }
    
    /**
     * Handle PayMongo payment callback
     */
    public function paymentCallback(Request $request, Invoice $invoice)
    {
        Log::info('PayMongo callback hit for invoice', [
            'invoice_id' => $invoice->id,
            'user_id' => auth()->id()
        ]);
        
        try {
            // Check payment status from PayMongo
            $checkoutSessionId = $invoice->paymongo_checkout_session_id;
            
            if ($checkoutSessionId) {
                $response = \Illuminate\Support\Facades\Http::withBasicAuth(env('PAYMONGO_SECRET_KEY'), '')
                    ->get(env('PAYMONGO_BASE_URL', 'https://api.paymongo.com/v1') . '/checkout_sessions/' . $checkoutSessionId);
                
                if ($response->successful()) {
                    $data = $response->json();
                    $attributes = $data['data']['attributes'];
                    
                    Log::info("PayMongo checkout session status", [
                        'invoice_id' => $invoice->id,
                        'status' => $attributes['status'],
                        'attributes' => $attributes
                    ]);
                    
                    // Check if there are successful payments
                    $hasSuccessfulPayment = false;
                    if (isset($attributes['payments']) && is_array($attributes['payments'])) {
                        foreach ($attributes['payments'] as $payment) {
                            if (isset($payment['attributes']['status']) && $payment['attributes']['status'] === 'paid') {
                                $hasSuccessfulPayment = true;
                                break;
                            }
                        }
                    }
                    
                    if ($hasSuccessfulPayment) {
                        // Payment successful - create payment record
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
                        
                        // Update invoice status to paid
                        $invoice->update(['status' => 'paid']);
                        
                        Log::info("Online payment completed for invoice {$invoice->id}", [
                            'invoice_id' => $invoice->id,
                            'payment_id' => $payment->id
                        ]);
                        
                        return redirect()->route('admin.orders.index', ['type' => 'walkin'])
                            ->with('success', 'Payment successful! Invoice has been marked as paid.');
                    } else {
                        Log::warning("PayMongo payment not completed", [
                            'invoice_id' => $invoice->id,
                            'status' => $attributes['status'],
                            'payments_count' => isset($attributes['payments']) ? count($attributes['payments']) : 0
                        ]);
                        
                        return redirect()->route('invoices.payment', $invoice)
                            ->with('error', 'Payment not completed. Session status: ' . $attributes['status'] . '. Please try again.');
                    }
                } else {
                    Log::error("PayMongo API call failed", [
                        'invoice_id' => $invoice->id,
                        'status' => $response->status(),
                        'response' => $response->json()
                    ]);
                    
                    return redirect()->route('invoices.payment', $invoice)
                        ->with('error', 'Failed to verify payment status. Please try again.');
                }
            } else {
                return redirect()->route('invoices.payment', $invoice)
                    ->with('error', 'No payment session found for this invoice.');
            }
            
        } catch (\Exception $e) {
            Log::error('PayMongo callback error for invoice', [
                'invoice_id' => $invoice->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return redirect()->route('invoices.payment', $invoice)
                ->with('error', 'An error occurred while verifying payment: ' . $e->getMessage());
        }
    }
}