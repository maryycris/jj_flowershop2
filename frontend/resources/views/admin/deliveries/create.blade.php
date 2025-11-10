<h2>Assign Delivery</h2>
<form method="POST" action="{{ route('deliveries.store') }}">
    @csrf
    <label>Order ID:</label>
    <input type="number" name="order_id" required><br>

    <label>Driver Name:</label>
    <input type="text" name="driver_name" required><br>

    <label>Status:</label>
    <input type="text" name="status" required><br>

    <button type="submit">Assign</button>
</form>
<a href="{{ route('deliveries.index') }}">Back</a>
