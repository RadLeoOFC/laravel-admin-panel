<div class="d-flex flex-column flex-shrink-0 p-3 bg-white shadow-sm sidebar">
    @if(auth()->user()->isAdmin())
        <a href="{{ url('/admin/dashboard') }}" class="d-flex align-items-center mb-3 mb-md-0 me-md-auto text-decoration-none">
            <span class="fs-4 fw-bold">Admin panel</span>
        </a>
    @else
        <a href="{{ url('/dashboard') }}" class="d-flex align-items-center mb-3 mb-md-0 me-md-auto text-decoration-none">
            <span class="fs-4 fw-bold">To the main page</span>
        </a>
    @endif
    <hr>
    <ul class="nav nav-pills flex-column mb-auto">
        <li class="nav-item">
            <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : 'text-dark' }}">
                <i class="bi bi-house-door"></i> Main
            </a>
        </li>
        @if(auth()->user()->isAdmin())
            <li class="nav-item">
                <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : 'text-dark' }}">
                    <i class="bi bi-people"></i> Dashboard
                </a>
            </li>
        @endif
        <li>
            <a href="{{ route('memberships.index') }}" class="nav-link {{ request()->routeIs('memberships.*') ? 'active' : 'text-dark' }}">
                <i class="bi bi-people"></i> Memberships
            </a>
        </li>
        <li>
            <a href="{{ route('desks.index') }}" class="nav-link {{ request()->routeIs('desks.*') ? 'active' : 'text-dark' }}">
                <i class="bi bi-table"></i> Desks
            </a>
        </li>
        @if(auth()->user()->isAdmin())
            <li>
                <a href="{{ route('reports.index') }}" class="nav-link {{ request()->routeIs('reports.*') ? 'active' : 'text-dark' }}">
                    <i class="bi bi-clipboard-data"></i> Reports
                </a>
            </li>
        @endif
        @if(auth()->user()->isAdmin())
            <li>
                <a href="{{ route('payment_settings.index') }}" class="nav-link {{ request()->routeIs('payment_settings.*') ? 'active' : 'text-dark' }}">
                    <i class="bi bi-clipboard-data"></i> Payment settings
                </a>
            </li>
        @endif
    </ul>
    <hr>
    <div class="dropdown">
        <a href="#" class="d-flex align-items-center text-decoration-none dropdown-toggle" id="navbarDropdownSidebar" data-bs-toggle="dropdown">
            <strong>{{ Auth::user()->name ?? 'Administrator' }}</strong>
        </a>
        <ul class="dropdown-menu" aria-labelledby="navbarDropdownSidebar">
            <li><a class="dropdown-item" href="#">Profile</a></li>
            <li>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="dropdown-item">Escape</button>
                </form>
            </li>
        </ul>
    </div>
</div>
