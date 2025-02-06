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
                <th>Amount Paid</th>
                <th>Payment Status</th>
                <th>Payment Method</th>
                <th>Transaction Reference</th>
                <th>Actions</th>
            </tr>
            @foreach($memberships as $membership)
            <tr>
                <td>{{ $membership->user->name }}</td>
                <td>{{ $membership->desk->name }}</td>
                <td>{{ $membership->membership_type }}</td>
                <td>{{ $membership->start_date }} - {{ $membership->end_date }}</td>
                <td>{{ $membership->price }}</td>
                <td>{{ $membership->amount_paid ?? 'N/A' }}</td>
                <td>{{ ucfirst($membership->payment_status) }}</td>
                <td>{{ $membership->payment_method ?? 'N/A' }}</td>
                <td>{{ $membership->transaction_reference ?? 'N/A' }}</td>
                <td>
                    <a href="{{ route('memberships.edit', $membership->id) }}" class="btn btn-warning btn-sm">Edit</a>
                    <form action="{{ route('memberships.destroy', $membership->id) }}" method="POST" style="display:inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this membership?')">Delete</button>
                    </form>

                    <!-- Button for extending membership -->
                    <form action="{{ route('memberships.extend', $membership->id) }}" method="POST" style="display:inline; margin-left: 5px;">
                        @csrf
                        <button type="submit" class="btn btn-primary btn-sm">Extend Membership</button>
                    </form>

                    <!-- Payment Update Form -->
                    <form action="{{ route('memberships.updatePayment', $membership->id) }}" method="POST" class="mt-2">
                        @csrf
                        <div class="input-group input-group-sm">
                            <input type="number" name="amount_paid" placeholder="Amount" class="form-control" required>
                            <select name="payment_status" class="form-select">
                                <option value="pending" {{ $membership->payment_status == 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="paid" {{ $membership->payment_status == 'paid' ? 'selected' : '' }}>Paid</option>
                                <option value="failed" {{ $membership->payment_status == 'failed' ? 'selected' : '' }}>Failed</option>
                            </select>
                            <input type="text" name="payment_method" placeholder="Payment Method" class="form-control">
                            <input type="text" name="transaction_reference" placeholder="Transaction Ref" class="form-control">
                            <button type="submit" class="btn btn-success">Update</button>
                        </div>
                    </form>

                </td>
            </tr>
            @endforeach
        </table>
    </div>
</body>
</html>
