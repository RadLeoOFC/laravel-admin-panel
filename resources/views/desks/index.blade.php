<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Desk List</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    @extends('layouts.app')

    @section('content')
        <div class="container mt-5">
            <h1 class="fw-bold mb-3" style="font-size: 28px;">Desks</h1>
            <a href="{{ route('desks.create') }}" class="btn btn-primary mb-3">Create New Desk</a>

            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Location</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($desks as $desk)
                    <tr>
                        <td>{{ $desk->name }}</td>
                        <td>{{ $desk->location }}</td>
                        <td>{{ $desk->status }}</td>
                        <td>
                            <a href="{{ route('desks.edit', $desk->id) }}" class="btn btn-warning btn-sm">Edit</a>
                            <form action="{{ route('desks.destroy', $desk->id) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure?')">Delete</button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endsection
</body>
</html>
