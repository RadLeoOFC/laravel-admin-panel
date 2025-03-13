<!DOCTYPE html>
<html>
<head>
    <title>Edit Payment Settings</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

@extends('layouts.app')

@section('content')

    <h1>Edit Payment Settings</h1>


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

    <form action="{{ route('payment_settings.update', $payment_setting->id) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="mb-3">
            <label for="period" class="form-label">Period of time</label>
            <select name="period" class="form-select">
                <option value="daily" {{ old('period', $payment_setting->period) == 'daily' ? 'selected' : '' }}>Daily</option>
                <option value="monthly" {{ old('period', $payment_setting->period) == 'monthly' ? 'selected' : '' }}>Monthly</option>
                <option value="yearly" {{ old('period', $payment_setting->period) == 'daily' ? 'yearly' : '' }}>Yearly</option>
            </select>
        </div>
        <div class="mb-3">
            <label for="cost_of_membership_for_this_period_of_time" class="form-label">Cost of membership for this period of time</label>
            <input type="text" class="form-control @error('cost_of_membership_for_this_period_of_time') is-invalid @enderror" id="cost_of_membership_for_this_period_of_time" name="cost_of_membership_for_this_period_of_time" value="{{ old('cost_of_membership_for_this_period_of_time', $payment_setting->cost_of_membership_for_this_period_of_time) }}" required>
        </div>
        <div>
            <button type="submit" class="btn btn-success">Update</button>
        </div>
    </form>

    <a href="{{ route('payment_settings.index') }}">Back to Settings</a>
@endsection

</body>
</html>
