<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Event;
use Illuminate\Support\Facades\Auth;
use App\Services\PayMongoService;

class CustomerEventController extends Controller
{
    // Show the booking form
    public function create()
    {
        return view('customer.events.book');
    }

    // Store the booking
    public function store(Request $request)
    {
        $request->validate([
            'event_type' => 'required|string',
            'event_date' => 'required|date|after_or_equal:today',
            'event_time' => 'nullable',
            'venue' => 'required|string',
            'recipient_name' => 'nullable|string|max:190',
            'recipient_phone' => 'nullable|regex:/^09\\d{9}$/',
            'guest_count' => 'nullable|integer|min:1',
            'personalized_message' => 'nullable|string|max:1000',
            'special_instructions' => 'nullable|string|max:1000',
            'color_scheme' => 'nullable|string|max:255',
            'contact_phone' => 'nullable|string|max:20',
            'contact_email' => 'nullable|email|max:255',
            'notes' => 'nullable|string',
        ]);

        $event = Event::create([
            'user_id'               => Auth::id(),
            'event_type'            => $request->event_type,
            'event_date'            => $request->event_date,
            'event_time'            => $request->event_time,
            'venue'                 => $request->venue,
            'recipient_name'        => $request->recipient_name,
            'recipient_phone'       => $request->recipient_phone,
            'guest_count'           => $request->guest_count,
            'personalized_message'  => $request->personalized_message,
            'special_instructions'  => $request->special_instructions,
            'color_scheme'          => $request->color_scheme,
            'contact_phone'         => $request->contact_phone,
            'contact_email'         => $request->contact_email,
            'notes'                 => $request->notes,
            'status'                => 'pending',
            'subtotal'              => 0,
            'delivery_fee'          => 0,
            'service_fee'           => 0,
            'total'                 => 0,
        ]);

        // Redirect to order summary after event creation
        return redirect()->route('customer.events.order_summary', $event->id)->with('success', 'Event booked successfully! Review your details and add flower selections.');
    }

    // Show customer's event history
    public function index()
    {
        $events = Event::where('user_id', Auth::id())
            ->orderByDesc('created_at')
            ->paginate(10);
        
        return view('customer.events.index', compact('events'));
    }

    // Show calendar view
    public function calendar()
    {
        $events = Event::where('user_id', Auth::id())
            ->orderBy('event_date')
            ->get();
        
        return view('customer.events.calendar', compact('events'));
    }

    // Show specific event details
    public function show(Event $event)
    {
        // Ensure user can only view their own events
        if ($event->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access.');
        }
        
        return view('customer.events.show', compact('event'));
    }

    // Show order summary
    public function orderSummary(Event $event)
    {
        // Ensure user can only view their own events
        if ($event->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access.');
        }
        
        return view('customer.events.order_summary', compact('event'));
    }

    // Show order confirmation
    public function confirmation(Request $request, Event $event)
    {
        // Ensure user can only view their own events
        if ($event->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access.');
        }

        // If this is a POST request (placing order), update the event status
        if ($request->isMethod('post')) {
            $event->update([
                'status' => 'confirmed',
                'order_id' => 'EVT-' . str_pad($event->id, 6, '0', STR_PAD_LEFT) . '-' . date('Ymd')
            ]);

            // Send notification (temporarily disabled)
            // \App\Http\Controllers\Customer\EventNotificationController::sendStatusChangeNotification($event, 'confirmed');

            return view('customer.events.confirmation', compact('event'));
        }
        
        return view('customer.events.confirmation', compact('event'));
    }

    // Edit event
    public function edit(Event $event)
    {
        // Ensure user can only edit their own events
        if ($event->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access.');
        }
        
        return view('customer.events.edit', compact('event'));
    }

    // Update event
    public function update(Request $request, Event $event)
    {
        // Ensure user can only update their own events
        if ($event->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access.');
        }

        $request->validate([
            'event_type' => 'required|string',
            'event_date' => 'required|date|after_or_equal:today',
            'event_time' => 'nullable',
            'venue' => 'required|string',
            'recipient_name' => 'nullable|string|max:190',
            'recipient_phone' => 'nullable|regex:/^09\\d{9}$/',
            'guest_count' => 'nullable|integer|min:1',
            'personalized_message' => 'nullable|string|max:1000',
            'special_instructions' => 'nullable|string|max:1000',
            'color_scheme' => 'nullable|string|max:255',
            'contact_phone' => 'nullable|string|max:20',
            'contact_email' => 'nullable|email|max:255',
            'notes' => 'nullable|string',
        ]);

        $event->update([
            'event_type'            => $request->event_type,
            'event_date'            => $request->event_date,
            'event_time'            => $request->event_time,
            'venue'                 => $request->venue,
            'recipient_name'        => $request->recipient_name,
            'recipient_phone'       => $request->recipient_phone,
            'guest_count'           => $request->guest_count,
            'personalized_message'  => $request->personalized_message,
            'special_instructions'  => $request->special_instructions,
            'color_scheme'          => $request->color_scheme,
            'contact_phone'         => $request->contact_phone,
            'contact_email'         => $request->contact_email,
            'notes'                 => $request->notes,
        ]);

        return redirect()->route('customer.events.order_summary', $event->id)->with('success', 'Event updated successfully!');
    }

    // Add product to event
    public function addProduct(Request $request, Event $event)
    {
        // Ensure user can only add products to their own events
        if ($event->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access.');
        }

        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
        ]);

        // Get the product
        $product = \App\Models\Product::findOrFail($request->product_id);
        
        // Get current flower selections to calculate total
        $flowerSelections = session()->get('event_' . $event->id . '_flowers', []);
        
        // Add the new product to the array for calculation
        $tempSelections = $flowerSelections;
        $existingIndex = null;
        foreach ($tempSelections as $index => $selection) {
            if ($selection['product_id'] == $product->id) {
                $existingIndex = $index;
                break;
            }
        }
        
        if ($existingIndex !== null) {
            $tempSelections[$existingIndex]['quantity'] += $request->quantity;
        } else {
            $tempSelections[] = [
                'product_id' => $product->id,
                'name' => $product->name,
                'image' => $product->image,
                'price' => $product->price,
                'quantity' => $request->quantity,
            ];
        }
        
        // Calculate total costs from all products
        $subtotal = array_sum(array_map(function($item) {
            return $item['price'] * $item['quantity'];
        }, $tempSelections));
        
        $deliveryFee = $subtotal > 0 ? 150 : 0; // Fixed delivery fee
        $serviceFee = $subtotal * 0.1; // 10% service fee
        $total = $subtotal + $deliveryFee + $serviceFee;

        // Update event with calculated costs
        $event->update([
            'subtotal' => $subtotal,
            'delivery_fee' => $deliveryFee,
            'service_fee' => $serviceFee,
            'total' => $total,
        ]);

        // Store product selection in session or create a relationship
        $flowerSelections = session()->get('event_' . $event->id . '_flowers', []);
        
        // Check if product already exists in selections
        $existingIndex = null;
        foreach ($flowerSelections as $index => $selection) {
            if ($selection['product_id'] == $product->id) {
                $existingIndex = $index;
                break;
            }
        }
        
        if ($existingIndex !== null) {
            // Update existing product quantity
            $flowerSelections[$existingIndex]['quantity'] += $request->quantity;
        } else {
            // Add new product
            $flowerSelections[] = [
                'product_id' => $product->id,
                'name' => $product->name,
                'image' => $product->image,
                'price' => $product->price,
                'quantity' => $request->quantity,
            ];
        }
        
        session()->put('event_' . $event->id . '_flowers', $flowerSelections);

        return redirect()->route('customer.events.order_summary', $event->id)->with('success', 'Product added to event successfully!');
    }

    // Remove product from event
    public function removeProduct(Request $request, Event $event)
    {
        // Ensure user can only modify their own events
        if ($event->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access.');
        }

        $request->validate([
            'product_id' => 'required|exists:products,id',
        ]);

        // Remove product from session
        $flowerSelections = session()->get('event_' . $event->id . '_flowers', []);
        $flowerSelections = array_filter($flowerSelections, function($item) use ($request) {
            return $item['product_id'] != $request->product_id;
        });
        session()->put('event_' . $event->id . '_flowers', $flowerSelections);

        // Recalculate costs
        $subtotal = array_sum(array_map(function($item) {
            return $item['price'] * $item['quantity'];
        }, $flowerSelections));
        
        $deliveryFee = $subtotal > 0 ? 150 : 0;
        $serviceFee = $subtotal * 0.1;
        $total = $subtotal + $deliveryFee + $serviceFee;

        $event->update([
            'subtotal' => $subtotal,
            'delivery_fee' => $deliveryFee,
            'service_fee' => $serviceFee,
            'total' => $total,
        ]);

        return redirect()->route('customer.events.order_summary', $event->id)->with('success', 'Product removed from event successfully!');
    }

    // Show payment method selection
    public function payment(Event $event)
    {
        if ($event->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access.');
        }

        if ($event->total <= 0) {
            return redirect()->route('customer.events.order_summary', $event->id)->with('error', 'Please add products to your event before proceeding to payment.');
        }

        return view('customer.events.payment', compact('event'));
    }

    // Process payment
    public function processPayment(Request $request, Event $event)
    {
        try {
            if ($event->user_id !== Auth::id()) {
                abort(403, 'Unauthorized access.');
            }

            $request->validate([
                'payment_method' => 'required|in:gcash,paymaya,seabank,rcbc',
            ]);

            $paymentMethod = $request->payment_method;
            $totalAmount = (float) $event->total; // Ensure it's a float

            \Log::info('Processing payment for event', [
                'event_id' => $event->id,
                'payment_method' => $paymentMethod,
                'total_amount' => $totalAmount,
                'amount_type' => gettype($totalAmount)
            ]);

            // PayMongo integration for supported payment methods
            if (in_array($paymentMethod, ['gcash', 'paymaya', 'seabank', 'rcbc'])) {
                $paymongo = new PayMongoService();
                $redirectUrl = route('customer.events.payment.callback', ['event' => $event->id]);
                
                // Map payment method to PayMongo type
                $sourceType = $paymentMethod;
                $source = $paymongo->createSource($totalAmount, $sourceType, $redirectUrl);
                
                // Store PayMongo source ID in event
                $event->update([
                    'paymongo_source_id' => $source['data']['id'],
                    'payment_method' => $paymentMethod,
                ]);
                
                return redirect($source['data']['attributes']['redirect']['checkout_url']);
            }

            // Fallback for unsupported payment methods
            return redirect()->back()->with('error', 'Selected payment method is not supported.');
            
        } catch (\Exception $e) {
            \Log::error('Payment processing error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'An error occurred while processing payment. Please try again.');
        }
    }

    // Handle PayMongo payment callback
    public function paymentCallback(Request $request, Event $event)
    {
        try {
            if ($event->user_id !== Auth::id()) {
                abort(403, 'Unauthorized access.');
            }

            $paymongo = new PayMongoService();
            $sourceId = $event->paymongo_source_id;
            
            if (!$sourceId) {
                return redirect()->route('customer.events.payment', $event->id)
                    ->with('error', 'Payment source not found.');
            }

            // Check payment status with PayMongo
            $status = $paymongo->getSourceStatus($sourceId);
            
            if ($status === 'chargeable') {
                // Payment successful - update event status
                $event->update([
                    'status' => 'confirmed',
                    'order_id' => 'EVT-' . str_pad($event->id, 6, '0', STR_PAD_LEFT) . '-' . date('Ymd'),
                    'payment_status' => 'paid',
                ]);

                // Send notification (temporarily disabled)
                // \App\Http\Controllers\Customer\EventNotificationController::sendStatusChangeNotification($event, 'confirmed');

                return redirect()->route('customer.events.confirmation', $event->id)
                    ->with('success', 'Payment processed successfully! Your event has been confirmed.');
            } else {
                // Payment failed or not completed
                return redirect()->route('customer.events.payment', $event->id)
                    ->with('error', 'Payment was not completed. Please try again.');
            }
            
        } catch (\Exception $e) {
            \Log::error('Payment callback error: ' . $e->getMessage());
            return redirect()->route('customer.events.payment', $event->id)
                ->with('error', 'An error occurred while processing payment. Please try again.');
        }
    }

    // Generate invoice for event
    public function invoice(Event $event)
    {
        // Ensure user can only view their own events
        if ($event->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access.');
        }

        $event->load(['user']);
        return view('customer.events.invoice', compact('event'));
    }

    // View invoice as PDF in browser
    public function viewInvoice(Event $event)
    {
        // Ensure user can only view their own events
        if ($event->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access.');
        }

        $event->load(['user']);
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('customer.events.invoice', compact('event'));
        
        return $pdf->stream('event-invoice-' . $event->id . '.pdf');
    }

    // Download invoice as PDF
    public function downloadInvoice(Event $event)
    {
        // Ensure user can only view their own events
        if ($event->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access.');
        }

        $event->load(['user']);
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('customer.events.invoice', compact('event'));
        
        return $pdf->download('event-invoice-' . $event->id . '.pdf');
    }
}