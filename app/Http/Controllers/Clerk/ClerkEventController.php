<?php

namespace App\Http\Controllers\Clerk;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Event;

class ClerkEventController extends Controller
{
    public function index()
    {
        $events = Event::with('user')->orderByDesc('created_at')->paginate(15);
        return view('clerk.events.index', compact('events'));
    }

    public function show(Event $event)
    {
        $event->load('user');
        return view('clerk.events.show', compact('event'));
    }

    public function updateStatus(Request $request, Event $event)
    {
        $request->validate([
            'status' => 'required|in:pending,confirmed,completed,cancelled'
        ]);

        $oldStatus = $event->status;
        $event->update(['status' => $request->status]);

        // Send notification to customer
        \App\Http\Controllers\Customer\EventNotificationController::sendStatusChangeNotification($event, $request->status, $oldStatus);

        return redirect()->back()->with('success', 'Event status updated successfully!');
    }

    // Generate invoice for event
    public function invoice(Event $event)
    {
        $event->load(['user']);
        return view('clerk.events.invoice', compact('event'));
    }

    // View invoice as PDF in browser
    public function viewInvoice(Event $event)
    {
        $event->load(['user']);
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('clerk.events.invoice', compact('event'));
        
        return $pdf->stream('event-invoice-' . $event->id . '.pdf');
    }

    // Download invoice as PDF
    public function downloadInvoice(Event $event)
    {
        $event->load(['user']);
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('clerk.events.invoice', compact('event'));
        
        return $pdf->download('event-invoice-' . $event->id . '.pdf');
    }
}
