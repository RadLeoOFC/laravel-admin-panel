<div class="d-flex flex-column flex-shrink-0 p-3 bg-white vh-100 shadow-sm">
    <a href="{{ url('/') }}" class="d-flex align-items-center mb-3 mb-md-0 me-md-auto text-decoration-none">
        <span class="fs-4 fw-bold">Admin panel</span>
    </a>
    <hr>
    <ul class="nav nav-pills flex-column mb-auto">
        <li class="nav-item">
            <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : 'text-dark' }}">
                <i class="bi bi-house-door"></i> Main
            </a>
        </li>
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
        <li>
            <a href="{{ route('reports.index') }}" class="nav-link {{ request()->routeIs('reports.*') ? 'active' : 'text-dark' }}">
                <i class="bi bi-clipboard-data"></i> Reports
            </a>
        </li>
    </ul>
    <hr>
    <div class="dropdown">
    <a href="#" class="d-flex align-items-center text-decoration-none dropdown-toggle" id="navbarDropdownSidebar" data-bs-toggle="dropdown">
        <strong>{{ Auth::user()->name ?? 'Администратор' }}</strong>
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
