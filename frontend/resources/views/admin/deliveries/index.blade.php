@extends('layouts.app')

@section('content')
<h2>Delivery List</h2>
<a href="{{ route('deliveries.create') }}">Assign Delivery</a>
@if(session('success'))
    <p style="color: green">{{ session('success') }}</p>
@endif
<table border="1">
    <tr>
        <th>Order ID</th>
        <th>Driver</th>
        <th>Status</th>
        <th>Actions</th>
    </tr>
    @foreach($deliveries as $delivery)
        <tr>
            <td>{{ $delivery->order_id }}</td>
            <td>{{ $delivery->driver_name }}</td>
            <td>{{ $delivery->status }}</td>
            <td>
                <a href="{{ route('deliveries.edit', $delivery->id) }}">Edit</a>
                <form action="{{ route('deliveries.destroy', $delivery->id) }}" method="POST" style="display:inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit">Delete</button>
                </form>
            </td>
        </tr>
    @endforeach
</table>
@endsection
