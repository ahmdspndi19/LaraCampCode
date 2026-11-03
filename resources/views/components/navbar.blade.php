<nav class="navbar navbar-expand-lg navbar-light sticky-top">
    <div class="container">
        <a class="navbar-brand" href="{{ route('welcome') }}">
            <img src="{{ asset('images/logo.png') }}" alt="CampCode">
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent"
            aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item"><a class="nav-link" href="{{ route('welcome') }}#benefits">Program</a></li>
                <li class="nav-item"><a class="nav-link" href="{{ route('welcome') }}#steps">Mentor</a></li>
                <li class="nav-item"><a class="nav-link" href="{{ route('welcome') }}#pricing">Pricing</a></li>
                <li class="nav-item"><a class="nav-link" href="{{ route('welcome') }}#testimonials">Business</a></li>
                @auth
                    <li class="nav-item">
                        @if (auth()->user()->is_admin)
                            <a class="nav-link {{ request()->routeIs('admin.*') ? 'active' : '' }}"
                                href="{{ route('admin.dashboard') }}">Admin</a>
                        @else
                            <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}"
                                href="{{ route('dashboard') }}">My Bootcamps</a>
                        @endif
                    </li>
                @endauth
            </ul>

            @auth
                <div class="d-flex align-items-center user-logged">
                    <a href="{{ auth()->user()->is_admin ? route('admin.dashboard') : route('dashboard') }}">
                        Halo, {{ auth()->user()->name }}!
                        <img src="{{ auth()->user()->avatar ?: asset('images/user_photo.png') }}"
                            class="user-photo rounded-circle" alt="">
                    </a>
                    <form action="{{ route('logout') }}" method="POST" class="ms-3">
                        @csrf
                        <button type="submit" class="btn btn-master btn-secondary">Logout</button>
                    </form>
                </div>
            @else
                <div class="d-flex">
                    <a href="{{ route('login') }}" class="btn btn-master btn-secondary me-3">Sign In</a>
                    <a href="{{ route('register') }}" class="btn btn-master btn-primary">Sign Up</a>
                </div>
            @endauth
        </div>
    </div>
</nav>
