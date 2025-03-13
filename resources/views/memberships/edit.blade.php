<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Membership</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>

<div class="container mt-4">
    <div class="card shadow-sm">
        <div class="card-header">
            <h2 class="mb-0">Edit Membership</h2>
        </div>
        <div class="card-body">
            <a href="{{ route('memberships.index') }}" class="btn btn-secondary mb-3">Back to List</a>

            <form action="{{ route('memberships.update', $membership) }}" method="POST">
                @csrf
                @method('PUT')

                <!-- User selection -->
                @if(auth()->user()->role === 'admin')
                    <div class="mb-3">
                        <label for="user_id" class="form-label">User</label>
                        <select name="user_id" id="user_id" class="form-select">
                            @foreach($users as $user)
                                <option value="{{ $user->id }}" {{ old('user_id', $membership->user_id) == $user->id ? 'selected' : '' }}>
                                    {{ $user->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                @endif

                <!-- Desk selection -->
                <div class="mb-3">
                    <label for="desk_id" class="form-label">Desk</label>
                    <select name="desk_id" id="desk_id" class="form-select">
                        @foreach($desks as $desk)
                            <option value="{{ $desk->id }}" {{ old('desk_id', $membership->desk_id) == $desk->id ? 'selected' : '' }}>
                                {{ $desk->name }}
                            </option>
                        @endforeach
                    </select>
                    
                    @if ($errors->has('desk_id'))
                        <div class="alert alert-danger mt-2">
                            {{ $errors->first('desk_id') }}
                        </div>
                    @endif
                </div>

                <!-- Membership Type -->
                <div class="mb-3">
                    <label for="membership_type" class="form-label">Membership Type</label>
                    <select name="membership_type" id="membership_type" class="form-select">
                        <option value="daily" {{ old('membership_type', $membership->membership_type) == 'daily' ? 'selected' : '' }}>Daily</option>
                        <option value="monthly" {{ old('membership_type', $membership->membership_type) == 'monthly' ? 'selected' : '' }}>Monthly</option>
                        <option value="yearly" {{ old('membership_type', $membership->membership_type) == 'yearly' ? 'selected' : '' }}>Yearly</option>
                    </select>
                    @if ($errors->has('membership_type'))
                        <div class="alert alert-danger mt-2">
                            {{ $errors->first('membership_type') }}
                        </div>
                    @endif
                </div>


                <div class="mb-3">
                    <label for="start_date" class="form-label">Start Date</label>
                    <input type="date" name="start_date" id="start_date" class="form-control" value="{{ old('start_date', $membership->start_date) }}">
                </div>

                <div class="mb-3">
                    <label for="end_date" class="form-label">End Date</label>
                    <input type="date" name="end_date" id="end_date" class="form-control" value="{{ old('end_date', $membership->end_date) }}">
                    @if ($errors->has('end_date'))
                        <div class="alert alert-danger mt-2">
                            {{ $errors->first('end_date') }}
                        </div>
                    @endif
                </div>

                @if(auth()->user()->role === 'admin')
                    <div class="mb-3">
                        <label for="price" class="form-label">Price</label>
                        <input type="number" step="0.01" name="price" id="price" class="form-control" value="{{ old('price', $membership->price) }}" {{ auth()->user()->role !== 'admin' ? 'readonly' : '' }}>
                    </div>
                @endif

                <button type="submit" class="btn btn-success">Update Membership</button>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const startDate = document.getElementById('start_date');
        const endDate = document.getElementById('end_date');

        startDate.addEventListener('change', function () {
            endDate.min = startDate.value;
        });
    });
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
