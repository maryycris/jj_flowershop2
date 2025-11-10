<h2>Edit Product</h2>
<form method="POST" action="{{ route('products.update', $product->id) }}">
    @csrf
    @method('PUT')
    <label>Name:</label>
    <input type="text" name="name" value="{{ $product->name }}" required><br>

    <label>Price:</label>
    <input type="number" name="price" value="{{ $product->price }}" step="0.01" required><br>

    <button type="submit">Update</button>
</form>
<a href="{{ route('products.index') }}">Back</a>
