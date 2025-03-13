<footer class="bg-dark text-white text-center py-3 mt-4">
    <div class="container">
        @if(auth()->user()->isAdmin())
            <p class="mb-0">© {{ date('Y') }} Admin Panel. All rights reserved.</p>
        @else
            <p class="mb-0">© {{ date('Y') }} Desks booking. All rights reserved.</p>
        @endif
    </div>
</footer>
