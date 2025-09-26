@extends('layouts.admin_app')
@section('content')
<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Sales Report</h1>
    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered align-middle">
                    <thead class="table-success">
                        <tr>
                            <th>Date</th>
                            <th>Order</th>
                            <th>Customer</th>
                            <th>Qty</th>
                            <th>Total</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($sales as $sale)
                        <tr>
                            <td>{{ $sale->date }}</td>
                            <td>{{ $sale->order_number }}</td>
                            <td>{{ $sale->customer }}</td>
                            <td>{{ $sale->qty }}</td>
                            <td>₱{{ number_format($sale->total, 2) }}</td>
                            <td>{{ $sale->status }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection 