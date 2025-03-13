<!DOCTYPE html>
<html>
<head>
    <title>Payment Settings</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

@extends('layouts.app')

@section('content')
    <div class="container mt-4">
        <h1 class="fw-bold mb-3" style="font-size: 28px;">Payment Settings</h1>
        <a href="{{ route('payment_settings.create') }}" class="btn btn-primary mb-3">Add Payment Setting</a>

        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Period of time</th>
                    <th>Cost of membership for this period of time</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($payment_settings as $payment_setting)
                    <tr>
                        <td>{{ $payment_setting->period }}</td>
                        <td>{{ $payment_setting->cost_of_membership_for_this_period_of_time }}</td>
                        <td>
                            <a href="{{ route('payment_settings.edit', $payment_setting->id) }}" class="btn btn-warning btn-sm">Edit</a>
                            <form action="{{ route('payment_settings.destroy', $payment_setting->id) }}" method="POST" style="display: inline-block;">
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