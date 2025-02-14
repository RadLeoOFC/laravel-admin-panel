@extends('layouts.app')

@section('title', 'Reports')

@section('content')
<h2 class="mb-4">Reports</h2>

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
