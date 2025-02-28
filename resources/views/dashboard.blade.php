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
