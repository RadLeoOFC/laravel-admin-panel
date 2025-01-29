<!DOCTYPE html>
<html>
<head>
    <title>Create Product</title>
</head>
<body>
    <h1>Create Product</h1>

    @if ($errors->any())
        <div class="alert alert-danger">
            <strong>Error!</strong> Please correct the following errors:
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('products.store') }}" method="POST">
        @csrf
        <div>
            <label for="name">Name:</label>
            <input type="text" name="name" id="name" value="{{ old('name') }}" required>
        </div>
        <div>
            <label for="price">Price:</label>
            <input type="text" name="price" id="price" value="{{ old('price') }}" required>
        </div>
        <div>
            <label for="description">Description:</label>
            <textarea name="description" id="description">{{ old('description') }}</textarea>
        </div>
        <div class="form-group">
            <label for="category_id">Category</label>
            <select name="category_id" id="category_id" class="form-control">
                <option value="">Select category</option>
                @foreach($categories as $category)
                    <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                        {{ $category->name }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="form-group">
            <label for="new_category">Or create a new category:</label>
            <input type="text" name="new_category" id="new_category" class="form-control" placeholder="Enter a new category">
        </div>
        <div>
            <button type="submit">Create</button>
        </div>
    </form>

    <a href="{{ route('products.index') }}">Back to Products</a>
</body>
</html>

