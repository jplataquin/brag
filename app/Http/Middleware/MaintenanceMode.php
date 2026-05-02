<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\PlatformSetting;
use Illuminate\Support\Facades\Auth;

class MaintenanceMode
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $settings = PlatformSetting::current();

        if ($settings->is_maintenance_mode) {
            // Allow admins to bypass
            if (Auth::check() && Auth::user()->is_admin) {
                return $next($request);
            }

            // Define allowed routes during maintenance
            $allowedRoutes = [
                'maintenance',
                'login',
                'logout',
                'up',
            ];

            if (!$request->routeIs($allowedRoutes) && !$request->is('login') && !$request->is('logout')) {
                return redirect()->route('maintenance');
            }
        }

        return $next($request);
    }
}
