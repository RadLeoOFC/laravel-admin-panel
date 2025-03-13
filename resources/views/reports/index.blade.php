<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reports</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

@extends('layouts.app')

@section('content')

<h2 class="fw-bold mb-2" style="font-size: 28px; padding-bottom: 15px; padding-top: 15px;">Reports</h2>

<!-- Filtering form -->
<form action="{{ route('reports.index') }}" method="GET" class="mb-4">
    <div class="row">
        <div class="col-md-4">
            <input type="month" name="month" class="form-control" value="{{ request('month') }}">
        </div>
        <div class="col-md-4">
            <button type="submit" class="btn btn-primary">Filter</button>
        </div>
    </div>
</form>

<!-- Reports table -->
<table class="table table-bordered">
    <thead>
        <tr>
            <th>User</th>
            <th>Membership Type</th>
            <th>Desk</th>
            <th>Start Date</th>
            <th>End Date</th>
        </tr>
    </thead>
    <tbody>
        @foreach($memberships as $membership)
        <tr>
            <td>{{ $membership->user->name }}</td>
            <td>{{ $membership->membership_type }}</td>
            <td>{{ $membership->desk->name ?? 'Not Assigned' }}</td>
            <td>{{ $membership->start_date }}</td>
            <td>{{ $membership->end_date }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
@endsection

</body>
</html>