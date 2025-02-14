<!DOCTYPE html>
<html>
<head>
    <title>Edit Product</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <h1>Edit Product</h1>


    @if ($errors->any())
        <div class="alert alert-danger">
            <strong>Error!</strong> Please correct the following errors:
            <ul>
                @foreach ($errors->all() as $error)
                    <li class="text-danger">{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('products.update', $product->id) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="mb-3">
            <label for="name">Name:</label>
            <input type="text" name="name" id="name" value="{{ old('name', $product->name) }}" required>
        </div>
        <div class="mb-3">
            <label for="price">Price:</label>
            <input type="text" name="price" id="price" value="{{ old('price', $product->price) }}" required>
        </div>
        <div class="mb-3">
            <label for="description">Description:</label>
            <textarea name="description" id="description">{{ old('description', $product->description) }}</textarea>
        </div>
        <div class="form-group">
            <label for="category_id">Category</label>
            <select name="category_id" id="category_id" class="mb-3">
                <option value="">Select category</option>
                @foreach($categories as $category)
                    <option value="{{ $category->id }}" {{ $product->category_id == $category->id ? 'selected' : '' }}>
                        {{ $category->name }}
                    </option>
                @endforeach
            </select>
        </div>
        <div>
            <button type="submit" class="btn btn-success">Update</button>
        </div>
    </form>

    <a href="{{ route('products.index') }}">Back to Products</a>
</body>
</html>
