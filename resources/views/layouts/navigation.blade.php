<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container-fluid">
        @if(auth()->user()->isAdmin())
            <a class="navbar-brand" href="{{ route('dashboard') }}">Main page</a>
        @else
            <a class="navbar-brand" href="{{ route('dashboard') }}">To the main page</a>
        @endif
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                @auth
                <div class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" id="navbarDropdownNav" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <strong>{{ Auth::user()->name ?? 'Administrator' }}</strong>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end shadow" aria-labelledby="navbarDropdownNav">
                        <li><a class="dropdown-item" href="{{ route('profile.edit') }}">Profile</a></li>
                        <li>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="dropdown-item text-danger">Escape</button>
                            </form>
                        </li>
                    </ul>
                </div>
                @endauth
            </ul>
        </div>
    </div>
</nav>
