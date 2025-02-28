<!DOCTYPE html>
<html>
<head>
    <title>Admin dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

    @extends('layouts.app')

    @section('content')
    <div class="container mt-4">
        <h2 class="fw-bold mb-3" style="font-size: 28px;">Admin Dashboard</h2>

        <!-- Date range filter -->
        <form action="{{ route('admin.dashboard') }}" method="GET" class="mb-4">
            <div class="row">
                <div class="col-md-3">
                    <label for="start_date" class="form-label">Start Date (Filter Range)</label>
                    <input type="date" id="start_date" name="start_date" class="form-control" value="{{ $startDate }}">
                </div>
                <div class="col-md-3">
                    <label for="end_date" class="form-label">End Date (Filter Range)</label>
                    <input type="date" id="end_date" name="end_date" class="form-control" value="{{ $endDate }}">
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary">Filter</button>
                </div>
            </div>
        </form>

        <!-- Summary Metrics -->
        <div class="row">
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <h5>Total Memberships</h5>
                        <p>{{ $totalMemberships }}</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <h5>Active Memberships</h5>
                        <p>{{ $activeMemberships }}</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <h5>Total Revenue</h5>
                        <p>${{ number_format($totalRevenue, 2) }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Desk and Membership Charts in One Row -->
        <div class="row mt-4">
            <div class="col-md-6 d-flex flex-column align-items-center">
                <h5>Desk Occupancy</h5>
                <canvas id="deskChart" style="max-width: 400px; max-height: 400px;"></canvas>
            </div>
            <div class="col-md-6 d-flex flex-column align-items-center">
                <h5>Membership Types Distribution</h5>
                <canvas id="membershipChart" style="max-width: 400px; max-height: 400px;"></canvas>
            </div>
        </div>
    </div>

    <!-- Chart.js Integration -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        var ctx = document.getElementById('deskChart').getContext('2d');
        var deskChart = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: ['Occupied Desks', 'Available Desks'],
                datasets: [{
                    data: [{{ $occupiedDesks }}, {{ $availableDesks }}],
                    backgroundColor: ['#FF6384', '#36A2EB']
                }]
            }
        });
    </script>

    <script>
        var ctx2 = document.getElementById('membershipChart').getContext('2d');
        var membershipChart = new Chart(ctx2, {
            type: 'pie',
            data: {
                labels: {!! json_encode(array_keys($membershipTypes)) !!},
                datasets: [{
                    data: {!! json_encode(array_values($membershipTypes)) !!},
                    backgroundColor: ['#FF6384', '#36A2EB', '#FFCE56']
                }]
            }
        });
    </script>

    @endsection

</body>
</html>
