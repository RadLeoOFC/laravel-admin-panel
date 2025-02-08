<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Membership</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>

<div class="container mt-4">
    <div class="card shadow-sm">
        <div class="card-header">
            <h2 class="mb-0">Create Membership</h2>
        </div>
        <div class="card-body">
            <a href="{{ route('memberships.index') }}" class="btn btn-secondary mb-3">Back to List</a>

            <form action="{{ route('memberships.store') }}" method="POST">
                @csrf

                <div class="mb-3">
                    <label for="user_id" class="form-label">User</label>
                    <select name="user_id" id="user_id" class="form-select">
                        @foreach($users as $user)
                            <option value="{{ $user->id }}">{{ $user->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-3">
                    <label for="desk_id" class="form-label">Select a desk:</label>
                    <select name="desk_id" id="desk_id" class="form-select">
                        @foreach($desks as $desk)
                            <option value="{{ $desk->id }}">{{ $desk->name }}</option>
                        @endforeach
                    </select>

                    @if ($errors->has('desk_id'))
                        <div class="alert alert-danger mt-2">
                            {{ $errors->first('desk_id') }}
                        </div>
                    @endif
                </div>


                <div class="mb-3">
                    <label for="membership_type" class="form-label">Membership Type</label>
                    <select name="membership_type" id="membership_type" class="form-select">
                        <option value="daily">Daily</option>
                        <option value="monthly">Monthly</option>
                        <option value="yearly">Yearly</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="start_date" class="form-label">Start Date</label>
                    <input type="date" name="start_date" id="start_date" class="form-control">
                </div>

                <div class="mb-3">
                    <label for="end_date" class="form-label">End Date</label>
                    <input type="date" name="end_date" id="end_date" class="form-control">
                </div>

                <div class="mb-3">
                    <label for="price" class="form-label">Price</label>
                    <input type="number" step="0.01" name="price" id="price" class="form-control">
                </div>

                <button type="submit" class="btn btn-success">Create Membership</button>
            </form>
        </div>
    </div>
</div>


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
