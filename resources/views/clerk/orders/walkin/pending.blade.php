@extends('layouts.clerk_app')

@section('content')
<div class="container-fluid">
    <h4 class="mb-3">Walk-in Order Pending</h4>
    <div class="card">
        <div class="card-body">
            <div class="mb-3">Order #{{ $order->id }} | Customer: {{ $order->user->name ?? 'Walk-in' }}</div>
            <a href="{{ route('clerk.orders.walkin.quotation', $order) }}" class="btn btn-primary">Proceed to Quotation</a>
            <a href="{{ route('clerk.orders.index') }}" class="btn btn-outline-secondary ms-2">Back to Orders</a>
        </div>
    </div>
  </div>
@endsection


