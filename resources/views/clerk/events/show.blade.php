@extends('layouts.clerk_app')
@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="bi bi-calendar-event me-2"></i>
                        Event Details - {{ $event->event_type }}
                    </h5>
                    <a href="{{ route('clerk.events.index') }}" class="btn btn-light btn-sm">
                        <i class="bi bi-arrow-left"></i> Back to Events
                    </a>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-8">
                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <h6 class="text-muted">Event Information</h6>
                                    <table class="table table-borderless">
                                        <tr>
                                            <td><strong>Event Type:</strong></td>
                                            <td>{{ $event->event_type }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Date:</strong></td>
                                            <td>{{ \Carbon\Carbon::parse($event->event_date)->format('F d, Y') }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Time:</strong></td>
                                            <td>{{ $event->event_time ?? 'Not specified' }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Venue:</strong></td>
                                            <td>{{ $event->venue }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Status:</strong></td>
                                            <td>
                                                <span class="badge bg-{{ $event->status === 'pending' ? 'warning' : ($event->status === 'confirmed' ? 'info' : ($event->status === 'completed' ? 'success' : 'danger')) }}">
                                                    {{ ucfirst($event->status) }}
                                                </span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>Order ID:</strong></td>
                                            <td>
                                                @if($event->order_id)
                                                    <span class="badge bg-primary">{{ $event->order_id }}</span>
                                                @else
                                                    <span class="text-muted">Not generated</span>
                                                @endif
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                                <div class="col-md-6">
                                    <h6 class="text-muted">Customer Information</h6>
                                    <table class="table table-borderless">
                                        <tr>
                                            <td><strong>Customer:</strong></td>
                                            <td>{{ $event->user->name ?? 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Email:</strong></td>
                                            <td>{{ $event->user->email ?? 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Phone:</strong></td>
                                            <td>{{ $event->user->contact_number ?? 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Recipient:</strong></td>
                                            <td>{{ $event->recipient_name ?? 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Recipient Phone:</strong></td>
                                            <td>{{ $event->recipient_phone ?? 'N/A' }}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>

                            <!-- Payment Information -->
                            <div class="row mb-4">
                                <div class="col-12">
                                    <h6 class="text-muted">Payment Information</h6>
                                    <div class="card">
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <table class="table table-borderless">
                                                        <tr>
                                                            <td><strong>Payment Status:</strong></td>
                                                            <td>
                                                                @if($event->payment_status === 'paid')
                                                                    <span class="badge bg-success">
                                                                        <i class="bi bi-check-circle me-1"></i>Fully Paid
                                                                    </span>
                                                                @elseif($event->payment_status === 'partial')
                                                                    <span class="badge bg-warning">
                                                                        <i class="bi bi-clock me-1"></i>Partially Paid
                                                                    </span>
                                                                @else
                                                                    <span class="badge bg-danger">
                                                                        <i class="bi bi-x-circle me-1"></i>Unpaid
                                                                    </span>
                                                                @endif
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td><strong>Payment Method:</strong></td>
                                                            <td>
                                                                @if($event->payment_method)
                                                                    <span class="badge bg-info text-uppercase">{{ $event->payment_method }}</span>
                                                                @else
                                                                    <span class="text-muted">Not selected</span>
                                                                @endif
                                                            </td>
                                                        </tr>
                                                    </table>
                                                </div>
                                                <div class="col-md-6">
                                                    <table class="table table-borderless">
                                                        <tr>
                                                            <td><strong>Subtotal:</strong></td>
                                                            <td class="fw-bold">₱{{ number_format($event->subtotal ?? 0, 2) }}</td>
                                                        </tr>
                                                        <tr>
                                                            <td><strong>Delivery Fee:</strong></td>
                                                            <td class="fw-bold">₱{{ number_format($event->delivery_fee ?? 0, 2) }}</td>
                                                        </tr>
                                                        <tr>
                                                            <td><strong>Service Fee:</strong></td>
                                                            <td class="fw-bold">₱{{ number_format($event->service_fee ?? 0, 2) }}</td>
                                                        </tr>
                                                        <tr class="border-top">
                                                            <td><strong>Total Amount:</strong></td>
                                                            <td class="fw-bold text-success fs-5">₱{{ number_format($event->total ?? 0, 2) }}</td>
                                                        </tr>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            @if($event->notes)
                            <div class="mb-4">
                                <h6 class="text-muted">Special Instructions & Notes</h6>
                                <div class="card">
                                    <div class="card-body">
                                        <p class="mb-0">{{ $event->notes }}</p>
                                    </div>
                                </div>
                            </div>
                            @endif

                            <div class="mb-4">
                                <h6 class="text-muted">Event Timeline</h6>
                                <div class="card">
                                    <div class="card-body">
                                        <p class="mb-2"><strong>Created:</strong> {{ $event->created_at->format('F d, Y \a\t g:i A') }}</p>
                                        <p class="mb-0"><strong>Last Updated:</strong> {{ $event->updated_at->format('F d, Y \a\t g:i A') }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="mb-0">Quick Actions</h6>
                                </div>
                                <div class="card-body">
                                    <form id="statusForm" method="POST" action="{{ route('clerk.events.updateStatus', $event) }}">
                                        @csrf
                                        <div class="mb-3">
                                            <label for="status" class="form-label">Update Status</label>
                                            <select class="form-select" id="status" name="status" onchange="this.form.submit()">
                                                <option value="pending" {{ $event->status === 'pending' ? 'selected' : '' }}>Pending</option>
                                                <option value="confirmed" {{ $event->status === 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                                                <option value="completed" {{ $event->status === 'completed' ? 'selected' : '' }}>Completed</option>
                                                <option value="cancelled" {{ $event->status === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                            </select>
                                        </div>
                                    </form>

                                    <div class="d-grid gap-2">
                                        <a href="mailto:{{ $event->user->email ?? '#' }}" class="btn btn-outline-primary btn-sm">
                                            <i class="bi bi-envelope"></i> Email Customer
                                        </a>
                                        @if($event->user->contact_number)
                                        <a href="tel:{{ $event->user->contact_number }}" class="btn btn-outline-success btn-sm">
                                            <i class="bi bi-telephone"></i> Call Customer
                                        </a>
                                        @endif
                                        @if($event->recipient_phone)
                                        <a href="tel:{{ $event->recipient_phone }}" class="btn btn-outline-info btn-sm">
                                            <i class="bi bi-telephone"></i> Call Recipient
                                        </a>
                                        @endif
                                    </div>
                                    
                                    <hr>
                                    
                                    <div class="d-grid gap-2">
                                        <a href="{{ route('clerk.events.invoice.view', $event->id) }}" class="btn btn-outline-secondary btn-sm" target="_blank">
                                            <i class="bi bi-eye"></i> View Invoice
                                        </a>
                                        <a href="{{ route('clerk.events.invoice.download', $event->id) }}" class="btn btn-outline-dark btn-sm">
                                            <i class="bi bi-download"></i> Download PDF
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
