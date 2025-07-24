<h2>Edit Delivery</h2>
<form method="POST" action="{{ route('deliveries.update', $delivery->id) }}">
    @csrf
    @method('PUT')
    <label>Order ID:</label>
    <input type="number" name="order_id" value="{{ $delivery->order_id }}" required><br>

    <label>Driver Name:</label>
    <input type="text" name="driver_name" value="{{ $delivery->driver_name }}" required><br>

    <label>Status:</label>
    <input type="text" name="status" value="{{ $delivery->status }}" required><br>

    <button type="submit">Update</button>
</form>
<a href="{{ route('deliveries.index') }}">Back</a>
