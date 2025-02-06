<!DOCTYPE html>
<html>
<head>
    <title>Create Product</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <h1>Create Product</h1>

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

    <form action="{{ route('products.store') }}" method="POST">
        @csrf
        <div>
            <label for="name" class="form-label">Name:</label>
            <input type="text" name="name" id="name" value="{{ old('name') }}" required>
        </div>
        <div>
            <label for="price" class="form-label">Price:</label>
            <input type="text" name="price" id="price" value="{{ old('price') }}" required>
        </div>
        <div>
            <label for="description" class="form-label">Description:</label>
            <textarea name="description" id="description">{{ old('description') }}</textarea>
        </div>
        <div class="form-group">
            <label for="category_id" class="form-label">Category</label>
            <select name="category_id" id="category_id" class="mb-3">
                <option value="">Select category</option>
                @foreach($categories as $category)
                    <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                        {{ $category->name }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="form-group">
            <label for="new_category" class="form-label">Or create a new category:</label>
            <input type="text" name="new_category" id="new_category" class="mb-3" placeholder="Enter a new category">
        </div>
        <div>
            <button type="submit" class="btn btn-success">Create</button>
        </div>
    </form>

    <a href="{{ route('products.index') }}">Back to Products</a>
</body>
</html>

