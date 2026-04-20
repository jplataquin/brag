<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Brag — The ultimate gamer card-battling social platform. Forge digital cards, challenge opponents, collect trophies.">
    <meta name="keywords" content="gaming, digital cards, battles, trophies, social platform, Brag, Forge Battle Brag">
    <meta name="theme-color" content="#0a0a1a">

    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:title" content="{{ config('app.name', 'Brag') }} — Forge. Battle. Brag.">
    <meta property="og:description" content="The ultimate gamer card-battling social platform. Forge unique digital cards, challenge opponents, and collect trophies.">
    <meta property="og:image" content="{{ asset('img/og-image.png') }}">

    <!-- Twitter -->
    <meta property="twitter:card" content="summary_large_image">
    <meta property="twitter:url" content="{{ url()->current() }}">
    <meta property="twitter:title" content="{{ config('app.name', 'Brag') }} — Forge. Battle. Brag.">
    <meta property="twitter:description" content="The ultimate gamer card-battling social platform. Forge unique digital cards, challenge opponents, and collect trophies.">
    <meta property="twitter:image" content="{{ asset('img/og-image.png') }}">

    <!-- Favicon -->
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Brag') }} — @yield('title', 'Forge. Battle. Brag.')</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    @livewireStyles
    <!-- Scripts -->
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
</head>
<body>
    <div id="app">
        <nav class="navbar navbar-expand-lg navbar-dark sticky-top">
            <div class="container">
                <a class="navbar-brand" href="{{ url('/') }}" id="brand-logo">
                    <i class="bi bi-lightning-charge-fill"></i> BRAG
                </a>
                
                <div class="d-flex align-items-center gap-2 d-lg-none">
                    @auth
                        <div class="navbar-nav flex-row">
                            <livewire:notification-dropdown :isMobile="true" />
                        </div>
                    @endauth
                    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMain" aria-controls="navbarMain" aria-expanded="false" aria-label="Toggle navigation">
                        <span class="navbar-toggler-icon"></span>
                    </button>
                </div>

                <div class="collapse navbar-collapse" id="navbarMain">
                    <!-- Left Side -->
                    <ul class="navbar-nav me-auto">
                        @auth
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('dashboard') || request()->routeIs('home') ? 'active' : '' }}" href="{{ route('dashboard') }}" id="nav-dashboard">
                                    <i class="bi bi-grid-fill"></i> Dashboard
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('templates.*') ? 'active' : '' }}" href="{{ route('templates.index') }}" id="nav-templates">
                                    <i class="bi bi-layers-fill"></i> Templates
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('cards.*') ? 'active' : '' }}" href="{{ route('cards.index') }}" id="nav-cards">
                                    <i class="bi bi-suit-diamond-fill"></i> Inventory
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('battles.*') ? 'active' : '' }}" href="{{ route('battles.index') }}" id="nav-battles">
                                    <i class="bi bi-crosshair"></i> Arena
                                </a>
                            </li>
                        @endauth
                    </ul>

                    <!-- Search -->
                    <div class="nav-search me-3 d-none d-lg-block">
                        <i class="bi bi-search search-icon"></i>
                        <input type="text" class="form-control" id="nav-search-input" placeholder="Search players..." autocomplete="off">
                        <div class="search-results" id="search-results"></div>
                    </div>

                    <!-- Right Side -->
                    <ul class="navbar-nav ms-auto">
                        @guest
                            @if (Route::has('login'))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('login') }}" id="nav-login">
                                        <i class="bi bi-box-arrow-in-right"></i> Login
                                    </a>
                                </li>
                            @endif
                            @if (Route::has('register'))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('register') }}" id="nav-register">
                                        <i class="bi bi-person-plus-fill"></i> Sign Up
                                    </a>
                                </li>
                            @endif
                        @else
                            <div class="d-none d-lg-block">
                                <livewire:notification-dropdown />
                            </div>
                            
                            <li class="nav-item dropdown">
                                <a id="navbarUserDropdown" class="nav-link dropdown-toggle d-flex align-items-center gap-2" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <img src="{{ Auth::user()->avatar_url }}" alt="Avatar" style="width:28px;height:28px;border-radius:50%;border:1px solid rgba(0,240,255,0.3);">
                                    {{ Auth::user()->username }}
                                </a>
                                <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarUserDropdown">
                                    <a class="dropdown-item" href="{{ route('profile.show', Auth::user()->username) }}" id="nav-profile">
                                        <i class="bi bi-person-fill"></i> My Profile
                                    </a>
                                    <a class="dropdown-item" href="{{ route('profile.edit') }}" id="nav-edit-profile">
                                        <i class="bi bi-gear-fill"></i> Edit Profile
                                    </a>
                                    <div class="dropdown-divider" style="border-color: rgba(0,240,255,0.1);"></div>
                                    <form action="{{ route('logout') }}" method="POST" class="m-0 p-0">
                                        @csrf
                                        <button type="submit" class="dropdown-item d-flex align-items-center gap-2" id="nav-logout" style="border: none; background: transparent; width: 100%; text-align: left; cursor: pointer;">
                                            <i class="bi bi-box-arrow-right"></i> {{ __('Logout') }}
                                        </button>
                                    </form>
                                </div>
                            </li>
                        @endguest
                    </ul>
                </div>
            </div>
        </nav>

        <!-- Mobile Search -->
        @auth
        <div class="d-lg-none px-3 py-2" style="background: rgba(5,5,16,0.9);">
            <div class="nav-search">
                <i class="bi bi-search search-icon"></i>
                <input type="text" class="form-control" id="nav-search-mobile" placeholder="Search players..." autocomplete="off" style="width: 100% !important;">
                <div class="search-results" id="search-results-mobile"></div>
            </div>
        </div>
        @endauth

        <main class="py-4">
            <div class="container">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert" id="alert-success">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert" id="alert-error">
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if(session('info'))
                    <div class="alert alert-info alert-dismissible fade show" style="background: rgba(0, 240, 255, 0.1); border-color: #00f0ff; color: #00f0ff;" role="alert" id="alert-info">
                        <i class="bi bi-bell-fill me-2"></i> {{ session('info') }}
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @yield('content')
            </div>
        </main>

        <footer class="text-center py-4" style="border-top: 1px solid rgba(0,240,255,0.08);">
            <p style="font-family: 'Orbitron', sans-serif; font-size: 0.7rem; color: #555577; letter-spacing: 2px;">
                &copy; {{ date('Y') }} BRAG — FORGE. BATTLE. BRAG.
            </p>
        </footer>
    </div>

    @stack('modals')

    <!-- Global Neon Alert Modal -->
    <div class="modal fade" id="globalNeonAlertModal" tabindex="-1" aria-hidden="true" style="z-index: 1060;">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content" style="background: rgba(10, 10, 30, 0.95); border: 1px solid #00f0ff; backdrop-filter: blur(20px);">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title neon-text" id="globalNeonAlertTitle">ALERT</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body py-4 text-center">
                    <p id="globalNeonAlertMessage" style="color: #fff; font-size: 1.1rem;"></p>
                    <div class="mt-4">
                        <button type="button" class="btn btn-neon w-100" data-bs-dismiss="modal">OK</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Global Neon Confirm Modal -->
    <div class="modal fade" id="globalNeonConfirmModal" tabindex="-1" aria-hidden="true" style="z-index: 1060;">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content" style="background: rgba(10, 10, 30, 0.95); border: 1px solid #ff00ff; backdrop-filter: blur(20px);">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title neon-text-magenta" id="globalNeonConfirmTitle">CONFIRM</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body py-4 text-center">
                    <p id="globalNeonConfirmMessage" style="color: #fff; font-size: 1.1rem;"></p>
                    <div class="mt-4 d-flex gap-3">
                        <button type="button" class="btn btn-neon flex-fill" data-bs-dismiss="modal">CANCEL</button>
                        <button type="button" class="btn btn-neon-magenta flex-fill" id="globalNeonConfirmBtn">YES</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Global Neon Prompt Modal -->
    <div class="modal fade" id="globalNeonPromptModal" tabindex="-1" aria-hidden="true" style="z-index: 1060;">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content" style="background: rgba(10, 10, 30, 0.95); border: 1px solid #39ff14; backdrop-filter: blur(20px);">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title" style="color: #39ff14; font-family: 'Orbitron', sans-serif;" id="globalNeonPromptTitle">PROMPT</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body py-4 text-center">
                    <p id="globalNeonPromptMessage" style="color: #fff; font-size: 1.1rem;"></p>
                    <input type="text" id="globalNeonPromptInput" class="form-control mb-3" style="background: #111122; color: #fff; border-color: #39ff14;">
                    <div class="d-flex gap-3">
                        <button type="button" class="btn btn-neon flex-fill" data-bs-dismiss="modal">CANCEL</button>
                        <button type="button" class="btn btn-neon flex-fill" style="border-color: #39ff14; color: #39ff14;" id="globalNeonPromptBtn">SUBMIT</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @livewireScripts
    @yield('scripts')

    <script>
        // User search functionality
        document.querySelectorAll('#nav-search-input, #nav-search-mobile').forEach(input => {
            const resultsId = input.id === 'nav-search-input' ? 'search-results' : 'search-results-mobile';
            const resultsEl = document.getElementById(resultsId);
            let debounce = null;

            input.addEventListener('input', function() {
                clearTimeout(debounce);
                const q = this.value.trim();

                if (q.length < 2) {
                    resultsEl.classList.remove('active');
                    resultsEl.innerHTML = '';
                    return;
                }

                debounce = setTimeout(() => {
                    fetch(`/search?q=${encodeURIComponent(q)}`, {
                        headers: { 'X-Requested-With': 'XMLHttpRequest' }
                    })
                    .then(r => r.json())
                    .then(users => {
                        if (users.length === 0) {
                            resultsEl.innerHTML = '<div class="p-3 text-center" style="color:#555577;font-size:0.85rem;">No players found</div>';
                        } else {
                            resultsEl.innerHTML = users.map(u => `
                                <a href="/user/${u.username}" class="search-item">
                                    <img src="${u.avatar_url}" alt="${u.username}">
                                    <div class="search-user-info">
                                        <div class="username">@${u.username}</div>
                                    </div>
                                </a>
                            `).join('');
                        }
                        resultsEl.classList.add('active');
                    });
                }, 300);
            });

            // Close on click outside
            document.addEventListener('click', (e) => {
                if (!input.contains(e.target) && !resultsEl.contains(e.target)) {
                    resultsEl.classList.remove('active');
                }
            });
        });

        // Auto-dismiss alerts
        setTimeout(() => {
            document.querySelectorAll('.alert').forEach(el => {
                const bsAlert = new bootstrap.Alert(el);
                bsAlert.close();
            });
        }, 5000);
    </script>
</body>
</html>
