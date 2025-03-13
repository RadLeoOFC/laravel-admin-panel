<!DOCTYPE html>
<html>
<head>
    <title>Create Payment Settings</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    @extends('layouts.app')

    @section('content')
    <div class="container">
        <h1 class="mb-4">Add Payment Setting</h1>

        <form action="{{ route('payment_settings.store') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label for="period" class="form-label">Period of time</label>
                <select name="period" class="form-select">
                    <option value="daily">Daily</option>
                    <option value="monthly">Monthly</option>
                    <option value="yearly">Yearly</option>
                </select>
            </div>
            <div class="mb-3">
                <label for="cost_of_membership_for_this_period_of_time" class="form-label">Cost of membership for this period of time</label>
                <input type="text" class="form-control @error('cost_of_membership_for_this_period_of_time') is-invalid @enderror" id="cost_of_membership_for_this_period_of_time" name="cost_of_membership_for_this_period_of_time" value="{{ old('cost_of_membership_for_this_period_of_time') }}" required>
            </div>
            <button type="submit" class="btn btn-success">Save</button>
            <a href="{{ route('payment_settings.index') }}" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
    @endsection

</body>
</html>