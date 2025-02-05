<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Memberships list</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-4">  
        <h2>Memberships</h2>
        <a href="{{ route('memberships.create') }}" class="btn btn-primary mb-3">Create Membership</a>
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <table class="table table-bordered">
            <tr>
                <th>User</th>
                <th>Desk</th>
                <th>Type</th>
                <th>Dates</th>
                <th>Price</th>
                <th>Actions</th>
            </tr>
            @foreach($memberships as $membership)
            <tr>
                <td>{{ $membership->user->name }}</td>
                <td>{{ $membership->desk->name }}</td>
                <td>{{ $membership->membership_type }}</td>
                <td>{{ $membership->start_date }} - {{ $membership->end_date }}</td>
                <td>{{ $membership->price }}</td>
                <td>
                    <a href="{{ route('memberships.edit', $membership->id) }}" class="btn btn-warning btn-sm">Edit</a>
                    <form action="{{ route('memberships.destroy', $membership->id) }}" method="POST" style="display:inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this membership?')">Delete</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </table>
    </div>
</body>
</html>
