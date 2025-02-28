<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Memberships list</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

@extends('layouts.app')

@section('content')
    <form action="{{ route('memberships.extend', $membership->id) }}" method="POST" class="container mt-3">
        @csrf
        <label for="end_date" class="fw-bold mb-2" style="font-size: 24px;">New End Date:</label>
        <input type="date" name="new_end_date" id="new_end_date" class="form-control" value="{{ old('end_date', $membership->end_date) }}">
        @if ($errors->has('new_end_date'))
            <div class="alert alert-danger mt-2">
                {{ $errors->first('end_date') }}
            </div>
        @endif
        <button type="submit" class="btn btn-primary" style="margin-top: 5px;">Extend Membership</button>
    </form>
@endsection

</body>
</html>
