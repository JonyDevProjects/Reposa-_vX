<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Reposa+') - {{ __('messages.app.tagline') }}</title>

    <!-- Fonts & Core Web Vitals Preconnect/Prefetch -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="dns-prefetch" href="https://fonts.googleapis.com">
    <link rel="dns-prefetch" href="https://fonts.gstatic.com">
    <link rel="dns-prefetch" href="https://cdn.jsdelivr.net">
    <!-- impeccable-disable-next-line overused-font -- Brand approved dual typography in DESIGN.md -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@600;700;800&display=swap" rel="stylesheet">

    <!-- Scripts and Styles -->
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
</head>
<body class="d-flex flex-column min-vh-100">
    <a href="#main-content" class="skip-link btn btn-primary">
        {{ __('messages.layout.skip_to_content') ?? 'Saltar al contenido principal' }}
    </a>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary sticky-top shadow-sm">
        <div class="container">
            <a class="navbar-brand fw-bold text-white d-inline-flex align-items-center" href="/">
                <i class="bi bi-moon-stars-fill me-2 text-white"></i>Reposa+
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Abrir navegación">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="/">{{ __('messages.nav.home') }}</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/catalog">{{ __('messages.nav.catalog') }}</a>
                    </li>
                </ul>
                <form action="/catalog" method="GET" class="d-flex my-2 my-lg-0 me-lg-3" style="max-width: 300px; width: 100%;">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-white border-end-0"><i class="bi bi-search text-muted"></i></span>
                        <input type="text" name="q" class="form-control border-start-0" placeholder="{{ __('messages.layout.search_placeholder') }}" value="{{ request('q') }}" aria-label="{{ __('messages.layout.search_placeholder') }}">
                    </div>
                </form>
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle d-inline-flex align-items-center" href="#" role="button" data-bs-toggle="dropdown">
                            <i class="bi bi-globe me-1"></i> {{ strtoupper(app()->getLocale()) }}
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item {{ app()->getLocale() == 'es' ? 'active' : '' }}" href="{{ route('lang.switch', 'es') }}">Español</a></li>
                            <li><a class="dropdown-item {{ app()->getLocale() == 'en' ? 'active' : '' }}" href="{{ route('lang.switch', 'en') }}">English</a></li>
                        </ul>
                    </li>
                    @guest
                        <li class="nav-item">
                            <a class="nav-link" href="/login">{{ __('messages.nav.login') }}</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link btn btn-secondary text-white ms-lg-2 px-4" href="/register">{{ __('messages.nav.register') }}</a>
                        </li>
                    @else
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                                {{ Auth::user()->name }}
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><a class="dropdown-item" href="/profile">{{ __('messages.nav.profile') }}</a></li>
                                @if(Auth::user()->role === 'admin')
                                    <li><a class="dropdown-item text-danger" href="{{ route('admin.dashboard') }}">{{ __('messages.layout.admin_panel') }}</a></li>
                                @endif
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <form action="/logout" method="POST">
                                        @csrf
                                        <button type="submit" class="dropdown-item">{{ __('messages.nav.logout') }}</button>
                                    </form>
                                </li>
                            </ul>
                        </li>
                    @endguest
                    <li class="nav-item">
                        <a class="nav-link position-relative ms-lg-3" href="/cart">
                            <i class="bi bi-cart3 fs-5"></i>
                            <span id="cart-badge" class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                @php
                                    $cartCount = Auth::check() 
                                        ? \App\Models\CartItem::where('user_id', Auth::id())->sum('quantity')
                                        : collect(session()->get('cart', []))->sum('quantity');
                                @endphp
                                {{ $cartCount }}
                            </span>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <main id="main-content" class="main-content flex-grow-1">
        @yield('content')
    </main>

    <footer class="bg-primary text-white py-5 mt-auto">
        <div class="container">
            <div class="row">
                <div class="col-md-4 mb-4">
                    <h5 class="fw-bold mb-3">{{ __('messages.footer.title') }}</h5>
                    <p class="text-light opacity-75">{{ __('messages.footer.desc') }}</p>
                </div>
                <div class="col-md-2 mb-4">
                    <h6 class="fw-bold mb-3">{{ __('messages.footer.shop') }}</h6>
                    <ul class="list-unstyled">
                        <li><a href="/catalog" class="text-white text-decoration-none opacity-75">{{ __('messages.nav.catalog') }}</a></li>
                        <li><a href="#" class="text-white text-decoration-none opacity-75">{{ __('messages.footer.offers') }}</a></li>
                        <li><a href="#" class="text-white text-decoration-none opacity-75">{{ __('messages.footer.favorites') }}</a></li>
                    </ul>
                </div>
                <div class="col-md-3 mb-4">
                    <h6 class="fw-bold mb-3">{{ __('messages.footer.support') }}</h6>
                    <ul class="list-unstyled">
                        <li><a href="#" class="text-white text-decoration-none opacity-75">{{ __('messages.footer.contact') }}</a></li>
                        <li><a href="#" class="text-white text-decoration-none opacity-75">{{ __('messages.footer.shipping') }}</a></li>
                        <li><a href="#" class="text-white text-decoration-none opacity-75">{{ __('messages.footer.faq') }}</a></li>
                    </ul>
                </div>
                <div class="col-md-3">
                    <h6 class="fw-bold mb-3">{{ __('messages.footer.subscribe') }}</h6>
                    <div class="input-group mb-3">
                        <input type="email" name="newsletter_email" class="form-control" placeholder="Email" aria-label="Email" autocomplete="email">
                        <button class="btn btn-secondary" type="button">{{ __('messages.footer.subscribe_btn') }}</button>
                    </div>
                </div>
            </div>
            <hr class="my-4 opacity-25">
            <div class="row align-items-center opacity-75">
                <div class="col-md-6 text-center text-md-start mb-3 mb-md-0">
                    <small>&copy; 2026 Reposa+. {{ __('messages.footer.rights') }}</small>
                </div>
                <div class="col-md-6 text-center text-md-end">
                    <x-trust-seals variant="footer" />
                </div>
            </div>
        </div>
    </footer>

    <!-- Dynamic Sanctuary Toasts -->
    <div id="toast-container" class="toast-sanctuary-container" aria-live="polite" aria-atomic="true">
        @if(session('success'))
            <div id="flash-session-success" data-message="{{ session('success') }}" class="d-none"></div>
            <noscript>
                <div class="toast-sanctuary mb-3">
                    <div class="toast-icon-badge badge-success"><i class="bi bi-check2-circle"></i></div>
                    <div class="toast-content">
                        <div class="toast-title">Éxito</div>
                        <p class="toast-message">{{ session('success') }}</p>
                    </div>
                </div>
            </noscript>
        @endif

        @if(session('error'))
            <div id="flash-session-error" data-message="{{ session('error') }}" class="d-none"></div>
            <noscript>
                <div class="toast-sanctuary mb-3">
                    <div class="toast-icon-badge badge-error"><i class="bi bi-exclamation-octagon-fill"></i></div>
                    <div class="toast-content">
                        <div class="toast-title">Aviso</div>
                        <p class="toast-message">{{ session('error') }}</p>
                    </div>
                </div>
            </noscript>
        @endif
    </div>

    <!-- Phase 7: Ergonomic Mobile Navigation Bar (<768px Viewports) -->
    @include('layouts.partials.mobile-nav')

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    @stack('scripts')
</body>
</html>
