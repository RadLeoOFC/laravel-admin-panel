<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Main page</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<x-app-layout>
    <x-slot name="header">
        @if(auth()->user()->isAdmin())
            <h2 class="text-2xl font-bold">
                {{ __('Admin Overview') }}
            </h2>
            <p>Manage your desks and memberships here.</p>
        @else
            <h2 class="text-2xl font-bold">
                {{ __('Main Page') }}
            </h2>
            <p>You can book the desk here.</p>
        @endif
    </x-slot>
    
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    {{ __("You're logged in!") }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

</body>
</html>