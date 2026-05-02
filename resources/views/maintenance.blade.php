<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Under Maintenance — BRAG</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        :root {
            --neon-cyan: #00f0ff;
            --neon-magenta: #ff00ff;
            --bg-dark: #0a0a1a;
        }
        body {
            background-color: var(--bg-dark);
            color: #fff;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
            overflow: hidden;
            text-align: center;
        }
        .container {
            position: relative;
            padding: 2rem;
            z-index: 1;
        }
        .orbitron {
            font-family: 'Orbitron', sans-serif;
        }
        .neon-text {
            color: var(--neon-cyan);
            text-shadow: 0 0 10px rgba(0, 240, 255, 0.7), 0 0 20px rgba(0, 240, 255, 0.5);
            letter-spacing: 4px;
        }
        .icon {
            font-size: 5rem;
            margin-bottom: 1.5rem;
            color: var(--neon-magenta);
            filter: drop-shadow(0 0 15px rgba(255, 0, 255, 0.6));
            animation: pulse 2s infinite ease-in-out;
        }
        @keyframes pulse {
            0%, 100% { transform: scale(1); opacity: 0.8; }
            50% { transform: scale(1.1); opacity: 1; }
        }
        .message {
            font-size: 1.2rem;
            color: #8888aa;
            max-width: 500px;
            margin: 0 auto;
            line-height: 1.6;
        }
        .grid-bg {
            position: fixed;
            top: 0; left: 0; width: 100%; height: 100%;
            background-image: 
                linear-gradient(rgba(0, 240, 255, 0.05) 1px, transparent 1px),
                linear-gradient(90deg, rgba(0, 240, 255, 0.05) 1px, transparent 1px);
            background-size: 50px 50px;
            z-index: 0;
            perspective: 1000px;
        }
        .grid-inner {
            width: 100%; height: 100%;
            background: linear-gradient(to bottom, transparent, var(--bg-dark));
        }
        .footer-link {
            margin-top: 3rem;
            display: block;
            color: var(--neon-cyan);
            text-decoration: none;
            font-size: 0.9rem;
            opacity: 0.6;
            transition: opacity 0.3s;
        }
        .footer-link:hover {
            opacity: 1;
        }
    </style>
</head>
<body>
    <div class="grid-bg"><div class="grid-inner"></div></div>
    
    <div class="container">
        <div class="icon">
            <i class="bi bi-gear-fill"></i>
        </div>
        <h1 class="orbitron neon-text mb-4">SYSTEM MAINTENANCE</h1>
        <p class="message orbitron text-uppercase">
            The Arena is currently undergoing essential upgrades. 
            Opponents are being recalibrated. 
            Please stand by, Citizen.
        </p>
        
        @auth
            @if(Auth::user()->is_admin)
                <a href="{{ route('admin.dashboard') }}" class="footer-link orbitron">
                    <i class="bi bi-shield-lock"></i> ADMIN ACCESS ACTIVE
                </a>
            @else
                <form action="{{ route('logout') }}" method="POST" class="mt-5">
                    @csrf
                    <button type="submit" style="background: none; border: 1px solid var(--neon-cyan); color: var(--neon-cyan); padding: 8px 20px; cursor: pointer;" class="orbitron">LOGOUT</button>
                </form>
            @endif
        @else
            <a href="{{ route('login') }}" class="footer-link orbitron">
                <i class="bi bi-box-arrow-in-right"></i> LOG IN (ADMINS ONLY)
            </a>
        @endauth
    </div>
</body>
</html>
