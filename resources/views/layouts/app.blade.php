<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-light d-flex flex-column min-vh-90">
    <div class="container-fluid flex-grow-1">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 bg-white vh-90 shadow-sm p-3">
                @include('layouts.sidebar')
            </div>
            
            <!-- Main content -->
            <div class="col-md-9 d-flex flex-column">
                @include('layouts.navigation')

                @isset($header)
                    <header class="bg-white shadow-sm mb-3 p-3">
                        <div class="container">
                            {{ $header }}
                        </div>
                    </header>
                @endisset

                <main class="container flex-grow-1">
                    @yield('content')
                </main>
            </div>
        </div>
    </div>

    <!-- Include footer -->
    @include('layouts.footer')

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            var dropdownTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="dropdown"]'));
            var dropdownList = dropdownTriggerList.map(function (dropdownTriggerEl) {
                return new bootstrap.Dropdown(dropdownTriggerEl);
            });
        });
    </script>
</body>
</html>
