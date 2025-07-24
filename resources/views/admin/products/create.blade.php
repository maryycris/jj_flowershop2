<h2>Add Product</h2>
<form method="POST" action="{{ route('products.store') }}">
    @csrf
    <label>Name:</label>
    <input type="text" name="name" required><br>

    <label>Price:</label>
    <input type="number" name="price" step="0.01" required><br>

    <button type="submit">Save</button>
</form>
<a href="{{ route('products.index') }}">Back</a>
