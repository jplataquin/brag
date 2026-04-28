<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Brag — The ultimate gamer card-battling social platform. Forge digital cards, challenge opponents, collect trophies.">
    <meta name="keywords" content="gaming, digital cards, battles, trophies, social platform, Brag, Forge Battle Brag">
    <meta name="theme-color" content="#0a0a1a">

    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="@yield('og_type', 'website')">
    <meta property="og:url" content="@yield('og_url', url()->current())">
    <meta property="og:title" content="@yield('og_title', config('app.name', 'Brag') . ' — Forge. Battle. Brag.')">
    <meta property="og:description" content="@yield('og_description', 'The ultimate gamer card-battling social platform. Forge unique digital cards, challenge opponents, and collect trophies.')">
    <meta property="og:image" content="@yield('og_image', asset('img/og-image.png'))">

    <!-- Twitter -->
    <meta property="twitter:card" content="@yield('twitter_card', 'summary_large_image')">
    <meta property="twitter:url" content="@yield('twitter_url', url()->current())">
    <meta property="twitter:title" content="@yield('twitter_title', config('app.name', 'Brag') . ' — Forge. Battle. Brag.')">
    <meta property="twitter:description" content="@yield('twitter_description', 'The ultimate gamer card-battling social platform. Forge unique digital cards, challenge opponents, and collect trophies.')">
    <meta property="twitter:image" content="@yield('twitter_image', asset('img/og-image.png'))">

    <!-- Favicon -->
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    
    <!-- PWA -->
    <link rel="manifest" href="{{ asset('manifest.json') }}">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Brag') }} — @yield('title', 'Forge. Battle. Brag.')</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

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
                    @auth
                        <!-- Mobile User Info Row -->
                        <div class="d-lg-none py-3 border-bottom border-secondary mb-2" style="border-bottom-color: rgba(0,240,255,0.1) !important;">
                            <div class="row align-items-center g-0">
                                <div class="col-6 text-start">
                                    <div class="d-flex align-items-center gap-2">
                                        <img src="{{ Auth::user()->avatar_url }}" alt="Avatar" style="width:32px;height:32px;border-radius:50%;border:1px solid rgba(0,240,255,0.3);">
                                        <span class="text-white fw-bold" style="font-family: 'Orbitron', sans-serif; font-size: 0.85rem;">{{ Auth::user()->username }}</span>
                                    </div>
                                </div>
                                <div class="col-6 text-end">
                                    <a href="{{ route('wallet.index') }}" class="text-decoration-none d-inline-flex align-items-center" style="background: rgba(0, 240, 255, 0.1); border: 1px solid rgba(0, 240, 255, 0.3); border-radius: 20px; padding: 4px 12px;">
                                        <i class="bi bi-gem me-2" style="color: #00f0ff; font-size: 0.9rem;"></i>
                                        <span class="fw-bold" style="color: #fff; font-family: 'Orbitron', sans-serif; font-size: 0.9rem;">{{ number_format(Auth::user()->shards_balance, 0) }}</span>
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endauth

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

                            <!-- Mobile Only Links -->
                            <li class="nav-item d-lg-none">
                                <a class="nav-link {{ request()->routeIs('profile.show') && request()->route('username') == Auth::user()->username ? 'active' : '' }}" href="{{ route('profile.show', Auth::user()->username) }}" id="nav-profile-mob">
                                    <i class="bi bi-person-fill"></i> My Profile
                                </a>
                            </li>
                            <li class="nav-item d-lg-none">
                                <a class="nav-link {{ request()->routeIs('wallet.index') ? 'active' : '' }}" href="{{ route('wallet.index') }}" id="nav-wallet-mob">
                                    <i class="bi bi-wallet2"></i> My Wallet
                                </a>
                            </li>
                            <li class="nav-item d-lg-none">
                                <a class="nav-link {{ request()->routeIs('profile.edit') ? 'active' : '' }}" href="{{ route('profile.edit') }}" id="nav-edit-profile-mob">
                                    <i class="bi bi-gear-fill"></i> Edit Profile
                                </a>
                            </li>
                            <li class="nav-item d-lg-none">
                                <form action="{{ route('logout') }}" method="POST" class="m-0 p-0">
                                    @csrf
                                    <button type="submit" class="nav-link border-0 bg-transparent w-100 text-start">
                                        <i class="bi bi-box-arrow-right"></i> Logout
                                    </button>
                                </form>
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
                    <ul class="navbar-nav ms-auto align-items-center">
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
                            <!-- Wallet Shards Balance -->
                            <li class="nav-item me-3 d-none d-lg-block">
                                <a href="{{ route('wallet.index') }}" class="text-decoration-none d-flex align-items-center" style="background: rgba(0, 240, 255, 0.1); border: 1px solid rgba(0, 240, 255, 0.3); border-radius: 20px; padding: 4px 12px; transition: all 0.2s;" id="nav-wallet">
                                    <i class="bi bi-gem me-2" style="color: #00f0ff; font-size: 1.1rem; text-shadow: 0 0 5px #00f0ff;"></i>
                                    <span class="fw-bold" style="color: #fff; font-family: 'Orbitron', sans-serif;">{{ number_format(Auth::user()->shards_balance, 0) }}</span>
                                </a>
                            </li>

                            <div class="d-none d-lg-block me-2">
                                <livewire:notification-dropdown />
                            </div>
                            
                            <li class="nav-item dropdown d-none d-lg-block">
                                <a id="navbarUserDropdown" class="nav-link dropdown-toggle d-flex align-items-center gap-2" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <img src="{{ Auth::user()->avatar_url }}" alt="Avatar" style="width:28px;height:28px;border-radius:50%;border:1px solid rgba(0,240,255,0.3);">
                                    {{ Auth::user()->username }}
                                </a>
                                <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarUserDropdown">
                                    <a class="dropdown-item" href="{{ route('profile.show', Auth::user()->username) }}" id="nav-profile">
                                        <i class="bi bi-person-fill"></i> My Profile
                                    </a>
                                    <a class="dropdown-item" href="{{ route('wallet.index') }}" id="nav-wallet-menu">
                                        <i class="bi bi-wallet2"></i> My Wallet
                                    </a>
                                    <a class="dropdown-item" href="{{ route('profile.edit') }}" id="nav-edit-profile">
                                        <i class="bi bi-gear-fill"></i> Edit Profile
                                    </a>
                                    @if(Auth::user()->is_admin)
                                        <div class="dropdown-divider" style="border-color: rgba(0,240,255,0.1);"></div>
                                        <a class="dropdown-item text-danger" href="{{ route('admin.dashboard') }}">
                                            <i class="bi bi-speedometer2"></i> Admin Panel
                                        </a>
                                    @endif
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
                    @if(str_contains(session('error'), 'Shard'))
                        <script type="module">
                            window.neonAlert("{!! addslashes(session('error')) !!}", "INSUFFICIENT SHARDS");
                        </script>
                    @else
                        <div class="alert alert-danger alert-dismissible fade show" role="alert" id="alert-error">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif
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
            <a href="{{ route('terms.show') }}" style="font-size: 0.8rem; color: var(--neon-cyan); text-decoration: none;">Terms of Service</a>
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

    <!-- Global Active Battle FAB -->
    @if(Auth::check())
        @php
            $currentRoute = Route::currentRouteName();
            $hideFabRoutes = ['battles.index', 'battles.room', 'team-battles.room'];
        @endphp
        @if(!in_array($currentRoute, $hideFabRoutes))
            @php
                $currentRoomInfo = Auth::user()->currentBattleRoom();
            @endphp
            @if($currentRoomInfo)
                <a href="{{ $currentRoomInfo['type'] === '1v1' ? route('battles.room', $currentRoomInfo['battle']->id) : route('team-battles.room', $currentRoomInfo['battle']->id) }}" 
                   class="btn btn-neon active-battle-fab" 
                   title="Return to Active Battle"
                   style="position: fixed; bottom: 30px; right: 30px; border-radius: 50%; width: 65px; height: 65px; display: flex; align-items: center; justify-content: center; z-index: 1050; padding: 0; background: rgba(10, 10, 30, 0.9); box-shadow: 0 0 20px rgba(0, 240, 255, 0.6); animation: pulse-fab 2s infinite;">
                    <i class="bi bi-swords" style="font-size: 2rem; color: #00f0ff; filter: drop-shadow(0 0 5px #00f0ff);"></i>
                </a>
                <style>
                    @keyframes pulse-fab {
                        0% { box-shadow: 0 0 0 0 rgba(0, 240, 255, 0.7); }
                        70% { box-shadow: 0 0 0 15px rgba(0, 240, 255, 0); }
                        100% { box-shadow: 0 0 0 0 rgba(0, 240, 255, 0); }
                    }
                    .active-battle-fab:hover {
                        transform: scale(1.1);
                        transition: transform 0.2s ease-in-out;
                    }
                </style>
            @endif
        @endif
    @endif

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

        // Register Service Worker
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register('/sw.js')
                    .then(reg => console.log('Service Worker registered.', reg))
                    .catch(err => console.error('Service Worker registration failed.', err));
            });
        }
    </script>
</body>
</html>
