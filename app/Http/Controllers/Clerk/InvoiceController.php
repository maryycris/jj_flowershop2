<?php

namespace App\Http\Controllers\Clerk;

use App\Http\Controllers\Controller;
use App\Models\Order;
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
        // Get all orders that have invoices (invoice_status is not 'draft')
        $invoices = Order::with(['user', 'products', 'delivery'])
            ->whereNotNull('invoice_generated_at')
            ->orderBy('invoice_generated_at', 'desc')
            ->get()
            ->map(function ($order) {
                $invoiceData = $this->invoiceService->getInvoiceData($order);
                return [
                    'id' => $order->id,
                    'name' => $order->user->name,
                    'invoice_number' => $invoiceData['invoice_number'],
                    'invoice_date' => $order->invoice_generated_at ? Carbon::parse($order->invoice_generated_at)->format('M d, Y') : 'N/A',
                    'source_document' => 'SO-' . str_pad($order->id, 6, '0', STR_PAD_LEFT),
                    'status' => $order->invoice_status,
                    'total' => $invoiceData['total'],
                    'order' => $order
                ];
            });

        return view('clerk.invoices.index', compact('invoices'));
    }

    /**
     * Display the specified invoice
     */
    public function show(Order $order)
    {
        // Ensure the order has an invoice
        if (!$order->invoice_generated_at) {
            return redirect()->back()->with('error', 'Invoice not found for this order.');
        }

        $invoiceData = $this->invoiceService->getInvoiceData($order);
        
        return view('clerk.invoices.show', compact('order', 'invoiceData'));
    }

    /**
     * Download invoice as PDF
     */
    public function download(Order $order)
    {
        // Ensure the order has an invoice
        if (!$order->invoice_generated_at) {
            return redirect()->back()->with('error', 'Invoice not found for this order.');
        }

        $invoiceData = $this->invoiceService->getInvoiceData($order);
        
        // For now, redirect to the view page
        // TODO: Implement PDF generation
        return view('clerk.invoices.show', compact('order', 'invoiceData'));
    }
}