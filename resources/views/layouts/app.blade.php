<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'SplitHub')</title>
    <link rel="icon" type="image/png" href="{{ asset('images/splithub-logo.png') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="{{ asset('css/splithub.css') }}" rel="stylesheet">
   
</head>
<body>
    <nav class="navbar navbar-expand-lg nav-glass sticky-top">
        <div class="container">
            <a class="navbar-brand brand-mark" href="{{ auth()->check() ? route('dashboard') : route('landing') }}">
                <span class="brand-icon"><img src="{{ asset('images/splithub-logo.png') }}" alt="SplitHub logo"></span>
                SplitHub
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="offcanvas" data-bs-target="#mobileNav" aria-controls="mobileNav" aria-label="Open menu">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse">
                <ul class="navbar-nav ms-auto align-items-lg-center gap-lg-2">
                    @auth
                        <li class="nav-item"><a class="nav-link" href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="nav-item"><a class="nav-link" href="{{ route('groups.index') }}">Groups</a></li>
                        <li class="nav-item">
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button class="btn btn-outline-dark btn-sm rounded-pill px-3">Logout</button>
                            </form>
                        </li>
                    @else
                        <li class="nav-item"><a class="nav-link" href="#features">Features</a></li>
                        <li class="nav-item"><a class="btn btn-outline-dark btn-sm rounded-pill px-3" href="{{ route('login') }}">Login</a></li>
                        <li class="nav-item"><a class="btn btn-dark btn-sm rounded-pill px-3" href="{{ route('register') }}">Start free</a></li>
                    @endauth
                </ul>
            </div>
        </div>
    </nav>

    <div class="offcanvas offcanvas-end" tabindex="-1" id="mobileNav">
        <div class="offcanvas-header">
            <h5 class="offcanvas-title brand-mark"><span class="brand-icon"><img src="{{ asset('images/splithub-logo.png') }}" alt="SplitHub logo"></span> SplitHub</h5>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body">
            <div class="stacked-btns">
                @auth
                    <a class="stacked-btn secondary text-start" href="{{ route('dashboard') }}">Dashboard</a>
                    <a class="stacked-btn secondary text-start" href="{{ route('groups.index') }}">Groups</a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="stacked-btn primary w-100">Logout</button>
                    </form>
                @else
                    <a class="stacked-btn secondary" href="{{ route('landing') }}#features">Features</a>
                    <a class="stacked-btn secondary" href="{{ route('login') }}">Login</a>
                    <a class="stacked-btn primary" href="{{ route('register') }}">Start free</a>
                @endauth
            </div>
        </div>
    </div>

    @if (session('status'))
        <div class="container mt-3">
            <div class="alert alert-success border-0 shadow-sm">{{ session('status') }}</div>
        </div>
    @endif

    @if ($errors->any())
        <div class="container mt-3">
            <div class="alert alert-danger border-0 shadow-sm">
                {{ $errors->first() }}
            </div>
        </div>
    @endif

    <main>
        @yield('content')
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.7/dist/chart.umd.min.js"></script>
    <script src="{{ asset('js/splithub.js') }}"></script>
    @stack('scripts')
</body>
</html>
